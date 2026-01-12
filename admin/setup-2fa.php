<?php
/**
 * Two-Factor Authentication Setup Page
 * For users who need to configure 2FA for the first time
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/totp.php';

// Check if user is in setup mode
if (isLoggedIn()) {
    // User is fully logged in - they can access this to reconfigure 2FA
    $userId = $_SESSION['user_id'];
    $userEmail = $_SESSION['email'];
    $userName = $_SESSION['name'] ?? $_SESSION['username'];
    $isReconfigure = true;
} elseif (isset($_SESSION['user_id']) && isset($_SESSION['needs_2fa_setup']) && $_SESSION['needs_2fa_setup'] === true) {
    // User just logged in and needs to set up 2FA
    $userId = $_SESSION['user_id'];
    $userEmail = $_SESSION['email'];
    $userName = $_SESSION['name'] ?? $_SESSION['username'];
    $isReconfigure = false;
} else {
    header('Location: /admin/login.php');
    exit;
}

$error = '';
$success = '';
$step = $_GET['step'] ?? '1';

// Get or generate TOTP secret
$user = getUserById($userId);
$totpSecret = $user['twofa_secret'];

if (!$totpSecret || $user['twofa_method'] === 'none' || isset($_GET['regenerate'])) {
    $totpSecret = TOTP::generateSecret();
    setup2FA($userId, 'totp', $totpSecret);
}

$qrCodeUrl = TOTP::getQRCodeUrl($totpSecret, $userEmail);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'choose_method') {
        $method = $_POST['method'] ?? 'totp';
        if (!in_array($method, ['email', 'totp', 'both'])) {
            $method = 'totp';
        }

        // Update method
        dbExecute("UPDATE users SET twofa_method = ? WHERE id = ?", [$method, $userId]);

        // If email method, send verification code
        if ($method === 'email' || $method === 'both') {
            $code = generateEmailCode($userId);
            sendTwoFactorEmail($userEmail, $code, $userName);
        }

        header('Location: /admin/setup-2fa.php?step=2&method=' . $method);
        exit;
    }

    if ($action === 'verify') {
        $code = trim($_POST['code'] ?? '');
        $method = $_POST['method'] ?? 'totp';

        if (empty($code)) {
            $error = 'Please enter the verification code.';
        } elseif (strlen($code) !== 6 || !ctype_digit($code)) {
            $error = 'Please enter a valid 6-digit code.';
        } else {
            $valid = false;

            // Verify based on method
            if ($method === 'email') {
                $valid = verifyEmailCode($userId, $code);
            } elseif ($method === 'totp') {
                $valid = TOTP::verify($totpSecret, $code);
            } elseif ($method === 'both') {
                // For 'both', accept either code type
                $valid = verifyEmailCode($userId, $code) || TOTP::verify($totpSecret, $code);
            }

            if ($valid) {
                // Mark 2FA as verified
                dbExecute("UPDATE users SET twofa_verified = 1 WHERE id = ?", [$userId]);

                // Complete login
                $_SESSION['needs_2fa_setup'] = false;

                if ($isReconfigure) {
                    $success = '2FA has been configured successfully!';
                    header('Location: /admin/settings.php?2fa=success');
                    exit;
                } else {
                    if (completeLogin($userId)) {
                        header('Location: /admin/');
                        exit;
                    }
                }
            } else {
                $error = 'Invalid verification code. Please try again.';
            }
        }
        $step = '2';
    }

    if ($action === 'resend_email') {
        $code = generateEmailCode($userId);
        if (sendTwoFactorEmail($userEmail, $code, $userName)) {
            $success = 'A new verification code has been sent to your email.';
        } else {
            $error = 'Failed to send email. Please try again.';
        }
        $step = '2';
    }
}

$selectedMethod = $_GET['method'] ?? 'totp';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Two-Factor Authentication - <?= e(SITE_NAME) ?></title>
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
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-8">
    <div class="max-w-lg w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-navy-800">Set Up Two-Factor Authentication</h1>
                <p class="text-gray-600 mt-2">
                    <?= $isReconfigure ? 'Reconfigure your 2FA settings' : 'Secure your account with 2FA' ?>
                </p>
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

            <!-- Progress Steps -->
            <div class="flex items-center justify-center mb-8">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full <?= $step === '1' ? 'bg-orange-500 text-white' : 'bg-green-500 text-white' ?> flex items-center justify-center font-bold text-sm">
                        <?= $step === '1' ? '1' : '&#10003;' ?>
                    </div>
                    <span class="ml-2 text-sm <?= $step === '1' ? 'text-orange-500 font-medium' : 'text-gray-500' ?>">Choose Method</span>
                </div>
                <div class="w-8 h-px bg-gray-300 mx-4"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full <?= $step === '2' ? 'bg-orange-500 text-white' : 'bg-gray-300 text-gray-500' ?> flex items-center justify-center font-bold text-sm">2</div>
                    <span class="ml-2 text-sm <?= $step === '2' ? 'text-orange-500 font-medium' : 'text-gray-500' ?>">Verify</span>
                </div>
            </div>

            <?php if ($step === '1'): ?>
            <!-- Step 1: Choose Method -->
            <form method="POST" action="">
                <input type="hidden" name="action" value="choose_method">

                <div class="space-y-4 mb-6">
                    <label class="block border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-orange-500 transition-colors has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="method" value="totp" checked class="mt-1">
                            <div>
                                <div class="font-medium text-gray-800">Authenticator App (Recommended)</div>
                                <p class="text-gray-500 text-sm mt-1">Use Google Authenticator, Authy, or similar app to generate codes.</p>
                            </div>
                        </div>
                    </label>

                    <label class="block border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-orange-500 transition-colors has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="method" value="email" class="mt-1">
                            <div>
                                <div class="font-medium text-gray-800">Email Codes</div>
                                <p class="text-gray-500 text-sm mt-1">Receive a code via email each time you log in.</p>
                            </div>
                        </div>
                    </label>

                    <label class="block border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-orange-500 transition-colors has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                        <div class="flex items-start gap-3">
                            <input type="radio" name="method" value="both" class="mt-1">
                            <div>
                                <div class="font-medium text-gray-800">Both Methods</div>
                                <p class="text-gray-500 text-sm mt-1">Use either authenticator app or email codes - whichever is more convenient.</p>
                            </div>
                        </div>
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-orange-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                    Continue
                </button>
            </form>

            <?php else: ?>
            <!-- Step 2: Setup & Verify -->
            <?php if ($selectedMethod === 'totp' || $selectedMethod === 'both'): ?>
            <div class="mb-6">
                <h3 class="font-medium text-gray-800 mb-3">1. Scan QR Code with Your Authenticator App</h3>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <img src="<?= e($qrCodeUrl) ?>" alt="QR Code" class="mx-auto mb-3" style="width: 200px; height: 200px;">
                    <p class="text-gray-500 text-xs">Can't scan? Enter this code manually:</p>
                    <code class="text-sm font-mono bg-gray-200 px-2 py-1 rounded mt-1 inline-block select-all"><?= e($totpSecret) ?></code>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($selectedMethod === 'email' || $selectedMethod === 'both'): ?>
            <div class="mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-blue-800 text-sm">
                        <strong>Email Code Sent:</strong> We've sent a verification code to <strong><?= e($userEmail) ?></strong>
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-6">
                <h3 class="font-medium text-gray-800 mb-3">
                    <?= ($selectedMethod === 'totp' || $selectedMethod === 'both') ? '2. ' : '' ?>Enter Verification Code
                </h3>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="verify">
                    <input type="hidden" name="method" value="<?= e($selectedMethod) ?>">

                    <input type="text" id="code" name="code" required autocomplete="one-time-code"
                           inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                           class="w-full px-4 py-3 text-center text-2xl tracking-widest border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 mb-4"
                           placeholder="000000"
                           autofocus>

                    <button type="submit"
                            class="w-full bg-orange-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                        Verify & Complete Setup
                    </button>
                </form>
            </div>

            <?php if ($selectedMethod === 'email' || $selectedMethod === 'both'): ?>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="resend_email">
                    <input type="hidden" name="method" value="<?= e($selectedMethod) ?>">
                    <button type="submit"
                            class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        Resend Email Code
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <div class="mt-4">
                <a href="/admin/setup-2fa.php?step=1" class="text-gray-500 hover:text-orange-500 text-sm">
                    &larr; Change Method
                </a>
            </div>
            <?php endif; ?>

            <?php if (!$isReconfigure): ?>
            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <a href="/admin/logout.php" class="text-gray-500 hover:text-orange-500 text-sm">
                    Cancel &amp; Log Out
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Auto-format code input
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        codeInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pastedText.replace(/[^0-9]/g, '').substring(0, 6);
            this.value = digits;
        });
    }
    </script>
</body>
</html>
