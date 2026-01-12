<?php
/**
 * Authentication Functions with 2FA Support
 */

require_once __DIR__ . '/database.php';

// Session timeout in seconds (1 hour)
define('SESSION_TIMEOUT', 3600);

// Login attempt limits
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900); // 15 minutes

// Allowed email domain for admins
define('ALLOWED_EMAIL_DOMAIN', 'integralsafetyltd.co.uk');

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['auth_complete']) && $_SESSION['auth_complete'] === true;
}

/**
 * Check if user is in 2FA pending state
 */
function is2FAPending() {
    return isset($_SESSION['2fa_pending']) && $_SESSION['2fa_pending'] === true;
}

/**
 * Require login - redirect to login page if not authenticated
 */
function requireLogin() {
    if (is2FAPending()) {
        header('Location: /admin/verify-2fa.php');
        exit;
    }

    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }

    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        logout();
        header('Location: /admin/login.php?timeout=1');
        exit;
    }

    // Check if user needs to set up 2FA
    if (isset($_SESSION['needs_2fa_setup']) && $_SESSION['needs_2fa_setup'] === true) {
        header('Location: /admin/setup-2fa.php');
        exit;
    }

    $_SESSION['last_activity'] = time();
}

/**
 * Require 2FA setup - called after initial 2FA setup
 */
function require2FASetup() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * Check if IP/email is rate limited
 */
function isRateLimited($email, $ip) {
    // Check user account lockout
    $user = dbFetchOne("SELECT locked_until FROM users WHERE email = ?", [$email]);
    if ($user && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
        return true;
    }

    // Check recent failed attempts from this IP
    $recentAttempts = dbFetchOne(
        "SELECT COUNT(*) as count FROM login_attempts WHERE ip_address = ? AND success = 0 AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
        [$ip]
    );

    return $recentAttempts && $recentAttempts['count'] >= MAX_LOGIN_ATTEMPTS;
}

/**
 * Get remaining lockout time
 */
function getLockoutRemaining($email) {
    $user = dbFetchOne("SELECT locked_until FROM users WHERE email = ?", [$email]);
    if ($user && $user['locked_until']) {
        $remaining = strtotime($user['locked_until']) - time();
        return $remaining > 0 ? $remaining : 0;
    }
    return 0;
}

/**
 * Log login attempt
 */
function logLoginAttempt($email, $success, $failureReason = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    dbExecute(
        "INSERT INTO login_attempts (email, ip_address, user_agent, success, failure_reason) VALUES (?, ?, ?, ?, ?)",
        [$email, $ip, $userAgent, $success ? 1 : 0, $failureReason]
    );
}

/**
 * Validate email domain
 */
function isAllowedEmailDomain($email) {
    $domain = substr(strrchr($email, "@"), 1);
    return strtolower($domain) === strtolower(ALLOWED_EMAIL_DOMAIN);
}

/**
 * Attempt first step of login (username/password)
 * Returns: 'success', '2fa_required', 'setup_2fa', or error message
 */
function loginStep1($emailOrUsername, $password) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    // Check rate limiting
    if (isRateLimited($emailOrUsername, $ip)) {
        logLoginAttempt($emailOrUsername, false, 'rate_limited');
        return ['error' => 'Too many login attempts. Please try again in 15 minutes.'];
    }

    $user = dbFetchOne(
        "SELECT id, username, email, name, password, role, twofa_method, twofa_secret, twofa_verified, is_active, failed_attempts FROM users WHERE username = ? OR email = ?",
        [$emailOrUsername, $emailOrUsername]
    );

    if (!$user) {
        logLoginAttempt($emailOrUsername, false, 'user_not_found');
        sleep(1); // Prevent timing attacks
        return ['error' => 'Invalid email or password.'];
    }

    if (!$user['is_active']) {
        logLoginAttempt($emailOrUsername, false, 'account_disabled');
        return ['error' => 'This account has been disabled.'];
    }

    if (!password_verify($password, $user['password'])) {
        // Increment failed attempts
        $newAttempts = $user['failed_attempts'] + 1;
        if ($newAttempts >= MAX_LOGIN_ATTEMPTS) {
            $lockUntil = date('Y-m-d H:i:s', time() + LOCKOUT_DURATION);
            dbExecute("UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?", [$newAttempts, $lockUntil, $user['id']]);
            logLoginAttempt($emailOrUsername, false, 'account_locked');
            return ['error' => 'Account locked due to too many failed attempts. Try again in 15 minutes.'];
        } else {
            dbExecute("UPDATE users SET failed_attempts = ? WHERE id = ?", [$newAttempts, $user['id']]);
        }

        logLoginAttempt($emailOrUsername, false, 'invalid_password');
        sleep(1);
        return ['error' => 'Invalid email or password.'];
    }

    // Password correct - reset failed attempts
    dbExecute("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?", [$user['id']]);

    // Store user info in session for 2FA step
    $_SESSION['2fa_user_id'] = $user['id'];
    $_SESSION['2fa_user_email'] = $user['email'];
    $_SESSION['2fa_user_name'] = $user['name'] ?? $user['username'];
    $_SESSION['2fa_method'] = $user['twofa_method'];

    // Check if 2FA is set up
    if ($user['twofa_method'] === 'none' || !$user['twofa_verified']) {
        // Need to set up 2FA
        $_SESSION['needs_2fa_setup'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['auth_complete'] = false;
        logLoginAttempt($emailOrUsername, true, 'needs_2fa_setup');
        return ['status' => 'setup_2fa'];
    }

    // 2FA is required
    $_SESSION['2fa_pending'] = true;
    logLoginAttempt($emailOrUsername, true, '2fa_pending');
    return ['status' => '2fa_required', 'method' => $user['twofa_method']];
}

/**
 * Complete login after 2FA verification
 */
function completeLogin($userId) {
    $user = dbFetchOne(
        "SELECT id, username, email, name, role FROM users WHERE id = ?",
        [$userId]
    );

    if (!$user) {
        return false;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['last_activity'] = time();
    $_SESSION['auth_complete'] = true;

    // Clean up 2FA session vars
    unset($_SESSION['2fa_pending']);
    unset($_SESSION['2fa_user_id']);
    unset($_SESSION['2fa_user_email']);
    unset($_SESSION['2fa_user_name']);
    unset($_SESSION['2fa_method']);
    unset($_SESSION['needs_2fa_setup']);

    // Update last login
    dbExecute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);

    logLoginAttempt($user['email'], true, 'login_complete');
    return true;
}

/**
 * Log out user
 */
function logout() {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
}

/**
 * Get current user
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'name' => $_SESSION['name'] ?? $_SESSION['username'],
        'role' => $_SESSION['role']
    ];
}

/**
 * Get full user data from database
 */
function getUserById($userId) {
    return dbFetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
}

/**
 * Check if current user is admin
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF input field
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}

/**
 * Hash a password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }

    return $errors;
}

/**
 * Change user password
 */
function changePassword($userId, $newPassword) {
    return dbExecute(
        "UPDATE users SET password = ? WHERE id = ?",
        [hashPassword($newPassword), $userId]
    );
}

/**
 * Set up 2FA for user
 */
function setup2FA($userId, $method, $secret = null) {
    if ($method === 'totp' && !$secret) {
        require_once __DIR__ . '/totp.php';
        $secret = TOTP::generateSecret();
    }

    dbExecute(
        "UPDATE users SET twofa_method = ?, twofa_secret = ?, twofa_verified = 0 WHERE id = ?",
        [$method, $secret, $userId]
    );

    return $secret;
}

/**
 * Verify and activate 2FA
 */
function verify2FASetup($userId, $code) {
    $user = dbFetchOne("SELECT twofa_method, twofa_secret FROM users WHERE id = ?", [$userId]);

    if (!$user) {
        return false;
    }

    $valid = false;

    if ($user['twofa_method'] === 'email' || $user['twofa_method'] === 'both') {
        require_once __DIR__ . '/totp.php';
        $valid = verifyEmailCode($userId, $code);
    }

    if (!$valid && ($user['twofa_method'] === 'totp' || $user['twofa_method'] === 'both')) {
        require_once __DIR__ . '/totp.php';
        $valid = TOTP::verify($user['twofa_secret'], $code);
    }

    if ($valid) {
        dbExecute("UPDATE users SET twofa_verified = 1 WHERE id = ?", [$userId]);
    }

    return $valid;
}

/**
 * Create new user
 */
function createUser($email, $password, $name, $role = 'admin') {
    // Validate email domain
    if (!isAllowedEmailDomain($email)) {
        return ['error' => 'Only @' . ALLOWED_EMAIL_DOMAIN . ' email addresses are allowed.'];
    }

    // Check if email already exists
    $existing = dbFetchOne("SELECT id FROM users WHERE email = ?", [$email]);
    if ($existing) {
        return ['error' => 'An account with this email already exists.'];
    }

    // Validate password
    $passwordErrors = validatePassword($password);
    if (!empty($passwordErrors)) {
        return ['error' => implode('. ', $passwordErrors)];
    }

    // Create username from email
    $username = explode('@', $email)[0];

    // Ensure unique username
    $counter = 1;
    $baseUsername = $username;
    while (dbFetchOne("SELECT id FROM users WHERE username = ?", [$username])) {
        $username = $baseUsername . $counter++;
    }

    $result = dbExecute(
        "INSERT INTO users (username, email, name, password, role, twofa_method, is_active, created_at) VALUES (?, ?, ?, ?, ?, 'none', 1, NOW())",
        [$username, $email, $name, hashPassword($password), $role]
    );

    if ($result) {
        return ['success' => true, 'id' => db()->lastInsertId()];
    }

    return ['error' => 'Failed to create user.'];
}

/**
 * Get all users
 */
function getAllUsers() {
    return dbFetchAll("SELECT id, username, email, name, role, twofa_method, twofa_verified, is_active, last_login, created_at FROM users ORDER BY created_at DESC");
}

/**
 * Get recent login attempts
 */
function getRecentLoginAttempts($limit = 50) {
    return dbFetchAll(
        "SELECT la.*, u.name as user_name FROM login_attempts la LEFT JOIN users u ON la.email = u.email ORDER BY la.created_at DESC LIMIT ?",
        [$limit]
    );
}
