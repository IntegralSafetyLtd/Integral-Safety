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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
