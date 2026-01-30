<?php
/**
 * Two-Factor Authentication Verification Page
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/totp.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /admin/');
    exit;
}

// Must have pending 2FA
if (!is2FAPending()) {
    header('Location: /admin/login.php');
    exit;
}

$userId = $_SESSION['2fa_user_id'] ?? null;
$userEmail = $_SESSION['2fa_user_email'] ?? '';
$userName = $_SESSION['2fa_user_name'] ?? '';
$method = $_SESSION['2fa_method'] ?? 'totp';

if (!$userId) {
    header('Location: /admin/login.php');
    exit;
}

$error = '';
$success = '';
$emailSent = false;

// Handle resend email code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_email'])) {
    $code = generateEmailCode($userId);
    if (sendTwoFactorEmail($userEmail, $code, $userName)) {
        $success = 'A new verification code has been sent to your email.';
        $emailSent = true;
    } else {
        $error = 'Failed to send email. Please try again or use authenticator app.';
    }
}

// Handle code verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $code = trim($_POST['code'] ?? '');

    if (empty($code)) {
        $error = 'Please enter the verification code.';
    } elseif (strlen($code) !== 6 || !ctype_digit($code)) {
        $error = 'Please enter a valid 6-digit code.';
    } else {
        $user = getUserById($userId);
        $valid = false;

        // Try email code first
        if ($method === 'email' || $method === 'both') {
            $valid = verifyEmailCode($userId, $code);
        }

        // Try TOTP if email didn't work
        if (!$valid && ($method === 'totp' || $method === 'both')) {
            $valid = TOTP::verify($user['twofa_secret'], $code);
        }

        if ($valid) {
            // Complete login
            if (completeLogin($userId)) {
                // Create remember token if "Trust this browser" was checked
                if (isset($_POST['trust_browser']) && $_POST['trust_browser'] === '1') {
                    createRememberToken($userId);
                }
                header('Location: /admin/');
                exit;
            } else {
                $error = 'Login failed. Please try again.';
            }
        } else {
            $error = 'Invalid verification code. Please try again.';
            logLoginAttempt($userEmail, false, '2fa_failed');
        }
    }
}

// Send initial email code if method includes email
if (!$emailSent && ($method === 'email' || $method === 'both') && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $code = generateEmailCode($userId);
    sendTwoFactorEmail($userEmail, $code, $userName);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - <?= e(SITE_NAME) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 700: '#1e3a5f', 800: '#132337', 900: '#0c1929' },
                        orange: { 500: '#e85d04', 600: '#dc5503' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-navy-800">Two-Factor Authentication</h1>
                <p class="text-gray-600 mt-2">Hello, <?= e($userName) ?></p>
            </div>

            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= e($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= e($success) ?>
            </div>
            <?php endif; ?>

            <div class="mb-6">
                <?php if ($method === 'email'): ?>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-blue-800 text-sm">
                        <strong>Email Code:</strong> We've sent a 6-digit verification code to <strong><?= e($userEmail) ?></strong>
                    </p>
                </div>
                <?php elseif ($method === 'totp'): ?>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-blue-800 text-sm">
                        <strong>Authenticator App:</strong> Enter the 6-digit code from your Google Authenticator or similar app.
                    </p>
                </div>
                <?php else: ?>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-blue-800 text-sm">
                        <strong>Multiple Options:</strong> Enter a code from your authenticator app, or use the code sent to your email.
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <form method="POST" action="">
                <div class="mb-6">
                    <label for="code" class="block text-gray-700 font-medium mb-2">Verification Code</label>
                    <input type="text" id="code" name="code" required autocomplete="one-time-code"
                           inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                           class="w-full px-4 py-3 text-center text-2xl tracking-widest border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                           placeholder="000000"
                           autofocus>
                    <p class="text-gray-500 text-sm mt-2">Enter the 6-digit code</p>
                </div>

                <div class="mb-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="trust_browser" value="1"
                               class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                        <span class="text-gray-700">Trust this browser for 7 days</span>
                    </label>
                    <p class="text-gray-500 text-xs mt-1 ml-8">You won't need to enter a code on this device for a week</p>
                </div>

                <button type="submit"
                        class="w-full bg-orange-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                    Verify & Log In
                </button>
            </form>

            <?php if ($method === 'email' || $method === 'both'): ?>
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-gray-600 text-sm text-center mb-3">Didn't receive the code?</p>
                <form method="POST" action="">
                    <input type="hidden" name="resend_email" value="1">
                    <button type="submit"
                            class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        Resend Email Code
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <div class="mt-6 text-center">
                <a href="/admin/login.php" class="text-gray-500 hover:text-orange-500 text-sm">
                    &larr; Back to Login
                </a>
            </div>
        </div>

        <p class="text-center mt-4 text-gray-500 text-sm">
            <a href="/" class="hover:text-orange-500">&larr; Back to website</a>
        </p>
    </div>

    <script>
    // Auto-focus and format code input
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function(e) {
        // Remove non-digits
        this.value = this.value.replace(/[^0-9]/g, '');

        // Auto-submit when 6 digits entered
        if (this.value.length === 6) {
            // Small delay to allow user to see the complete code
            setTimeout(() => {
                this.form.submit();
            }, 300);
        }
    });

    // Handle paste
    codeInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        const digits = pastedText.replace(/[^0-9]/g, '').substring(0, 6);
        this.value = digits;

        if (digits.length === 6) {
            setTimeout(() => {
                this.form.submit();
            }, 300);
        }
    });
    </script>
</body>
</html>
