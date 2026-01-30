<?php
/**
 * Site Settings
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$success = '';
$error = '';

// Handle logo upload (color version)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_logo'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
            $filename = $_FILES['logo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $newFilename = 'logo.' . $ext;
                $destination = UPLOADS_PATH . '/' . $newFilename;

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) {
                    updateSetting('site_logo', '/uploads/' . $newFilename);
                    $_SESSION['flash_message'] = 'Logo uploaded successfully!';
                    $_SESSION['flash_type'] = 'success';
                } else {
                    $error = 'Failed to upload logo.';
                }
            } else {
                $error = 'Invalid file type. Allowed: jpg, jpeg, png, gif, svg, webp';
            }
        } else {
            $error = 'Please select a logo file to upload.';
        }
        if (!$error) {
            header('Location: /admin/settings.php');
            exit;
        }
    }
}

// Handle white logo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_logo_white'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        if (isset($_FILES['logo_white']) && $_FILES['logo_white']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
            $filename = $_FILES['logo_white']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $newFilename = 'logo-white.' . $ext;
                $destination = UPLOADS_PATH . '/' . $newFilename;

                if (move_uploaded_file($_FILES['logo_white']['tmp_name'], $destination)) {
                    updateSetting('site_logo_white', '/uploads/' . $newFilename);
                    $_SESSION['flash_message'] = 'White logo uploaded successfully!';
                    $_SESSION['flash_type'] = 'success';
                } else {
                    $error = 'Failed to upload white logo.';
                }
            } else {
                $error = 'Invalid file type. Allowed: jpg, jpeg, png, gif, svg, webp';
            }
        } else {
            $error = 'Please select a logo file to upload.';
        }
        if (!$error) {
            header('Location: /admin/settings.php');
            exit;
        }
    }
}

// Handle favicon upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_favicon'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['ico', 'png', 'svg'];
            $filename = $_FILES['favicon']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $newFilename = 'favicon.' . $ext;
                $destination = UPLOADS_PATH . '/' . $newFilename;

                if (move_uploaded_file($_FILES['favicon']['tmp_name'], $destination)) {
                    updateSetting('site_favicon', '/uploads/' . $newFilename);
                    $_SESSION['flash_message'] = 'Favicon uploaded successfully!';
                    $_SESSION['flash_type'] = 'success';
                } else {
                    $error = 'Failed to upload favicon.';
                }
            } else {
                $error = 'Invalid file type. Allowed: ico, png, svg';
            }
        } else {
            $error = 'Please select a favicon file to upload.';
        }
        if (!$error) {
            header('Location: /admin/settings.php');
            exit;
        }
    }
}

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $settings = [
            'site_name', 'site_tagline', 'contact_email', 'contact_phone', 'contact_phone_2',
            'address_line1', 'city', 'postcode', 'facebook_url', 'linkedin_url', 'twitter_url'
        ];

        foreach ($settings as $key) {
            if (isset($_POST[$key])) {
                updateSetting($key, sanitize($_POST[$key]));
            }
        }

        $_SESSION['flash_message'] = 'Settings saved!';
        $_SESSION['flash_type'] = 'success';
        header('Location: /admin/settings.php');
        exit;
    }
}

// Handle revoke trusted device
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['revoke_device'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $tokenId = intval($_POST['token_id'] ?? 0);
        if ($tokenId > 0) {
            revokeRememberToken($tokenId, $_SESSION['user_id']);
            $_SESSION['flash_message'] = 'Device removed from trusted list.';
            $_SESSION['flash_type'] = 'success';
        }
        header('Location: /admin/settings.php#trusted-devices');
        exit;
    }
}

// Handle revoke all trusted devices
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['revoke_all_devices'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        revokeAllRememberTokens($_SESSION['user_id']);
        $_SESSION['flash_message'] = 'All trusted devices have been removed. You will need to verify 2FA on your next login.';
        $_SESSION['flash_type'] = 'success';
        header('Location: /admin/settings.php#trusted-devices');
        exit;
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        $user = dbFetchOne("SELECT password FROM users WHERE id = ?", [$_SESSION['user_id']]);

        if (!password_verify($currentPassword, $user['password'])) {
            $error = 'Current password is incorrect.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'New password must be at least 8 characters.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match.';
        } else {
            changePassword($_SESSION['user_id'], $newPassword);
            $_SESSION['flash_message'] = 'Password changed successfully!';
            $_SESSION['flash_type'] = 'success';
            header('Location: /admin/settings.php');
            exit;
        }
    }
}

$currentLogo = getSetting('site_logo', '/assets/images/logo.png');
$currentLogoWhite = getSetting('site_logo_white', '/assets/images/logo-white.png');
$currentFavicon = getSetting('site_favicon', '/assets/images/favicon.png');

require_once __DIR__ . '/includes/header.php';
?>

<h1 class="text-2xl font-bold text-gray-800 mb-8">Settings</h1>

<?php if ($error): ?>
<div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= e($error) ?></div>
<?php endif; ?>

<!-- Site Branding -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Site Branding</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Color Logo Upload -->
        <div>
            <h3 class="font-medium text-gray-700 mb-3">Logo (Colour)</h3>
            <p class="text-xs text-gray-500 mb-3">Used on the public site header</p>
            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center mb-4">
                <img src="<?= e($currentLogo) ?>?v=<?= time() ?>" alt="Current Logo" class="max-h-16 mx-auto mb-2">
            </div>
            <form method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>
                <input type="hidden" name="upload_logo" value="1">
                <input type="file" name="logo" accept=".jpg,.jpeg,.png,.gif,.svg,.webp"
                       class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 mb-2">
                <button type="submit" class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm">
                    Upload
                </button>
            </form>
        </div>

        <!-- White Logo Upload -->
        <div>
            <h3 class="font-medium text-gray-700 mb-3">Logo (White)</h3>
            <p class="text-xs text-gray-500 mb-3">Used on the admin header</p>
            <div class="bg-navy-800 border-2 border-dashed border-gray-600 rounded-lg p-4 text-center mb-4">
                <img src="<?= e($currentLogoWhite) ?>?v=<?= time() ?>" alt="Current White Logo" class="max-h-16 mx-auto mb-2">
            </div>
            <form method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>
                <input type="hidden" name="upload_logo_white" value="1">
                <input type="file" name="logo_white" accept=".jpg,.jpeg,.png,.gif,.svg,.webp"
                       class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 mb-2">
                <button type="submit" class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm">
                    Upload
                </button>
            </form>
        </div>

        <!-- Favicon Upload -->
        <div>
            <h3 class="font-medium text-gray-700 mb-3">Favicon</h3>
            <p class="text-xs text-gray-500 mb-3">Browser tab icon (32x32px)</p>
            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center mb-4">
                <img src="<?= e($currentFavicon) ?>?v=<?= time() ?>" alt="Current Favicon" class="w-8 h-8 mx-auto mb-2 mt-4">
            </div>
            <form method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>
                <input type="hidden" name="upload_favicon" value="1">
                <input type="file" name="favicon" accept=".ico,.png,.svg"
                       class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 mb-2">
                <button type="submit" class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm">
                    Upload
                </button>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Site Settings -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Site Information</h2>

        <form method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="update_settings" value="1">

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Site Name</label>
                    <input type="text" name="site_name" value="<?= e(getSetting('site_name')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Tagline</label>
                    <input type="text" name="site_tagline" value="<?= e(getSetting('site_tagline')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Contact Email</label>
                    <input type="email" name="contact_email" value="<?= e(getSetting('contact_email')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Phone Number 1</label>
                    <input type="text" name="contact_phone" value="<?= e(getSetting('contact_phone')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Phone Number 2</label>
                    <input type="text" name="contact_phone_2" value="<?= e(getSetting('contact_phone_2')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                           placeholder="Optional">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Address Line 1</label>
                    <input type="text" name="address_line1" value="<?= e(getSetting('address_line1')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">City / Town</label>
                    <input type="text" name="city" value="<?= e(getSetting('city')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Postcode</label>
                    <input type="text" name="postcode" value="<?= e(getSetting('postcode')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <h3 class="text-md font-semibold text-gray-800 mt-6 mb-4">Social Media</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Facebook URL</label>
                    <input type="url" name="facebook_url" value="<?= e(getSetting('facebook_url')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" value="<?= e(getSetting('linkedin_url')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Twitter URL</label>
                    <input type="url" name="twitter_url" value="<?= e(getSetting('twitter_url')) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Password Change -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Change Password</h2>

        <form method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="change_password" value="1">

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Current Password</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">New Password</label>
                    <input type="password" name="new_password" required minlength="8"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <p class="text-sm text-gray-500 mt-1">Minimum 8 characters</p>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Confirm New Password</label>
                    <input type="password" name="confirm_password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-6 py-2 bg-navy-700 text-white rounded-lg hover:bg-navy-800">
                    Change Password
                </button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t">
            <h3 class="text-md font-semibold text-gray-800 mb-2">Account Info</h3>
            <p class="text-gray-600">Logged in as: <strong><?= e($_SESSION['username']) ?></strong></p>
            <p class="text-gray-600">Email: <?= e($_SESSION['email']) ?></p>
        </div>
    </div>
</div>

<!-- Trusted Devices Section -->
<div class="bg-white rounded-lg shadow p-6 mt-8" id="trusted-devices">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Trusted Devices</h2>
            <p class="text-sm text-gray-500">Devices where you've checked "Trust this browser" won't require 2FA for 7 days.</p>
        </div>
        <?php $trustedDevices = getTrustedDevices($_SESSION['user_id']); ?>
        <?php if (count($trustedDevices) > 0): ?>
        <form method="POST" onsubmit="return confirm('This will log you out of all trusted devices. Continue?');">
            <?= csrfField() ?>
            <input type="hidden" name="revoke_all_devices" value="1">
            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                Remove All
            </button>
        </form>
        <?php endif; ?>
    </div>

    <?php if (empty($trustedDevices)): ?>
    <div class="text-center py-8 text-gray-500">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <p>No trusted devices</p>
        <p class="text-sm">When you log in and check "Trust this browser", it will appear here.</p>
    </div>
    <?php else: ?>
    <div class="space-y-3">
        <?php foreach ($trustedDevices as $device): ?>
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-navy-100 rounded-full flex items-center justify-center">
                    <?php if (strpos($device['device_info'], 'Windows') !== false): ?>
                    <svg class="w-5 h-5 text-navy-600" fill="currentColor" viewBox="0 0 24 24"><path d="M0 3.449L9.75 2.1v9.451H0m10.949-9.602L24 0v11.4H10.949M0 12.6h9.75v9.451L0 20.699M10.949 12.6H24V24l-12.9-1.801"/></svg>
                    <?php elseif (strpos($device['device_info'], 'Mac') !== false || strpos($device['device_info'], 'iOS') !== false): ?>
                    <svg class="w-5 h-5 text-navy-600" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                    <?php elseif (strpos($device['device_info'], 'Android') !== false): ?>
                    <svg class="w-5 h-5 text-navy-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.523 2.295l1.068-1.924a.4.4 0 00-.7-.387l-1.084 1.95a8.016 8.016 0 00-3.307-.7c-1.208 0-2.34.262-3.307.7L8.109-.016a.4.4 0 00-.7.387l1.068 1.924C6.015 3.578 4.5 5.818 4.5 8.408h15c0-2.59-1.515-4.83-3.977-6.113zM8.5 6.408a.75.75 0 110-1.5.75.75 0 010 1.5zm7 0a.75.75 0 110-1.5.75.75 0 010 1.5zM5 9.908v7.5a1.5 1.5 0 001.5 1.5H8v3a1.5 1.5 0 003 0v-3h2v3a1.5 1.5 0 003 0v-3h1.5a1.5 1.5 0 001.5-1.5v-7.5H5zm-3 0a1.5 1.5 0 00-1.5 1.5v5a1.5 1.5 0 003 0v-5a1.5 1.5 0 00-1.5-1.5zm20 0a1.5 1.5 0 00-1.5 1.5v5a1.5 1.5 0 003 0v-5a1.5 1.5 0 00-1.5-1.5z"/></svg>
                    <?php else: ?>
                    <svg class="w-5 h-5 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="font-medium text-gray-800"><?= e($device['device_info']) ?></p>
                    <p class="text-sm text-gray-500">
                        IP: <?= e($device['ip_address']) ?>
                        &middot; Last used: <?= $device['last_used_at'] ? date('j M Y, g:ia', strtotime($device['last_used_at'])) : 'Never' ?>
                    </p>
                    <p class="text-xs text-gray-400">
                        Expires: <?= date('j M Y', strtotime($device['expires_at'])) ?>
                    </p>
                </div>
            </div>
            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="revoke_device" value="1">
                <input type="hidden" name="token_id" value="<?= $device['id'] ?>">
                <button type="submit" class="text-red-600 hover:text-red-800 p-2" title="Remove this device">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
