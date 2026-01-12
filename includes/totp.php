<?php
/**
 * TOTP (Time-based One-Time Password) Library
 * Compatible with Google Authenticator, Authy, etc.
 */

class TOTP {
    private static $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random secret key
     */
    public static function generateSecret($length = 16) {
        $secret = '';
        $chars = self::$base32chars;
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Generate TOTP code for a given secret and time
     */
    public static function getCode($secret, $time = null, $digits = 6, $period = 30) {
        if ($time === null) {
            $time = time();
        }

        $timeSlice = floor($time / $period);
        $secretKey = self::base32Decode($secret);

        // Pack time into binary string
        $time = chr(0) . chr(0) . chr(0) . chr(0) . pack('N*', $timeSlice);

        // Generate HMAC-SHA1 hash
        $hash = hash_hmac('sha1', $time, $secretKey, true);

        // Get offset from last nibble
        $offset = ord(substr($hash, -1)) & 0x0F;

        // Get 4 bytes from hash starting at offset
        $hashPart = substr($hash, $offset, 4);
        $value = unpack('N', $hashPart)[1];

        // Remove most significant bit and get modulo
        $value = $value & 0x7FFFFFFF;
        $modulo = pow(10, $digits);

        return str_pad($value % $modulo, $digits, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a TOTP code
     * Allows for time drift of +/- 1 period (30 seconds)
     */
    public static function verify($secret, $code, $discrepancy = 1) {
        $time = time();

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $checkTime = $time + ($i * 30);
            $expectedCode = self::getCode($secret, $checkTime);
            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate QR code URL for Google Authenticator
     */
    public static function getQRCodeUrl($secret, $email, $issuer = 'Integral Safety') {
        $issuer = rawurlencode($issuer);
        $email = rawurlencode($email);
        $otpauth = "otpauth://totp/{$issuer}:{$email}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";

        // Use QR Server API (free, no API key required)
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauth);
    }

    /**
     * Decode base32 string
     */
    private static function base32Decode($base32) {
        $base32 = strtoupper($base32);
        $base32 = str_replace(' ', '', $base32);
        $base32chars = self::$base32chars;

        $buffer = 0;
        $bufferSize = 0;
        $result = '';

        for ($i = 0; $i < strlen($base32); $i++) {
            $char = $base32[$i];
            $position = strpos($base32chars, $char);

            if ($position === false) {
                continue; // Skip invalid characters
            }

            $buffer = ($buffer << 5) | $position;
            $bufferSize += 5;

            if ($bufferSize >= 8) {
                $bufferSize -= 8;
                $result .= chr(($buffer >> $bufferSize) & 0xFF);
            }
        }

        return $result;
    }
}

/**
 * Generate and send email 2FA code
 */
function generateEmailCode($userId) {
    // Generate 6-digit code
    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // Delete any existing unused codes for this user
    dbExecute("DELETE FROM two_factor_codes WHERE user_id = ? AND used = 0", [$userId]);

    // Store code with 10-minute expiry
    $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    dbExecute(
        "INSERT INTO two_factor_codes (user_id, code, type, expires_at) VALUES (?, ?, 'email', ?)",
        [$userId, $code, $expiresAt]
    );

    return $code;
}

/**
 * Verify email 2FA code
 */
function verifyEmailCode($userId, $code) {
    $record = dbFetchOne(
        "SELECT id FROM two_factor_codes WHERE user_id = ? AND code = ? AND type = 'email' AND used = 0 AND expires_at > NOW()",
        [$userId, $code]
    );

    if ($record) {
        // Mark code as used
        dbExecute("UPDATE two_factor_codes SET used = 1 WHERE id = ?", [$record['id']]);
        return true;
    }

    return false;
}

/**
 * Send 2FA code via email (using Resend API)
 */
function sendTwoFactorEmail($email, $code, $name = '') {
    require_once __DIR__ . '/email.php';
    $result = send2FAEmail($email, $code, $name);
    return $result['success'];
}

/**
 * Clean up expired 2FA codes
 */
function cleanupExpiredCodes() {
    dbExecute("DELETE FROM two_factor_codes WHERE expires_at < NOW() OR used = 1");
}
