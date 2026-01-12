<?php
/**
 * User Management Page
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

// Only admins can manage users
if (!isAdmin()) {
    header('Location: /admin/');
    exit;
}

$currentUser = getCurrentUser();
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Add new user
    if ($action === 'add_user') {
        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'admin';

        if (empty($email) || empty($name) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } else {
            $result = createUser($email, $password, $name, $role);
            if (isset($result['error'])) {
                $error = $result['error'];
            } else {
                $message = "User '{$name}' has been created successfully. They will need to set up 2FA on first login.";
            }
        }
    }

    // Update user
    if ($action === 'update_user') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $role = $_POST['role'] ?? 'admin';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($userId && $name) {
            // Prevent disabling own account
            if ($userId === $currentUser['id'] && !$isActive) {
                $error = 'You cannot disable your own account.';
            } else {
                dbExecute(
                    "UPDATE users SET name = ?, role = ?, is_active = ? WHERE id = ?",
                    [$name, $role, $isActive, $userId]
                );
                $message = 'User updated successfully.';
            }
        }
    }

    // Reset 2FA
    if ($action === 'reset_2fa') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId) {
            dbExecute(
                "UPDATE users SET twofa_method = 'none', twofa_secret = NULL, twofa_verified = 0 WHERE id = ?",
                [$userId]
            );
            $message = '2FA has been reset. User will need to set up 2FA on next login.';
        }
    }

    // Reset password
    if ($action === 'reset_password') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? '';

        if ($userId && $newPassword) {
            $passwordErrors = validatePassword($newPassword);
            if (!empty($passwordErrors)) {
                $error = implode(' ', $passwordErrors);
            } else {
                changePassword($userId, $newPassword);
                $message = 'Password has been reset successfully.';
            }
        }
    }

    // Delete user
    if ($action === 'delete_user') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId) {
            if ($userId === $currentUser['id']) {
                $error = 'You cannot delete your own account.';
            } else {
                dbExecute("DELETE FROM users WHERE id = ?", [$userId]);
                $message = 'User deleted successfully.';
            }
        }
    }
}

// Get all users
$users = getAllUsers();

// Get recent login attempts
$loginAttempts = getRecentLoginAttempts(30);

// Include header
$pageTitle = 'User Management';
include __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-navy-800">User Management</h1>
            <p class="text-gray-600">Manage admin users and security settings</p>
        </div>
        <button onclick="document.getElementById('addUserModal').classList.remove('hidden')"
                class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add User
        </button>
    </div>

    <?php if ($message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <?= e($message) ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?= e($error) ?>
    </div>
    <?php endif; ?>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Admin Users</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">2FA</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                    <tr class="<?= $user['id'] == $currentUser['id'] ? 'bg-orange-50' : '' ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-navy-800 text-white flex items-center justify-center font-semibold">
                                    <?= strtoupper(substr($user['name'] ?? $user['username'], 0, 1)) ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= e($user['name'] ?? $user['username']) ?>
                                        <?php if ($user['id'] == $currentUser['id']): ?>
                                        <span class="text-xs text-orange-500 font-normal">(you)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-sm text-gray-500"><?= e($user['username']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= e($user['email']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($user['twofa_verified']): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <?= e(ucfirst($user['twofa_method'])) ?>
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Not Set Up
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($user['is_active']): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Disabled
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $user['last_login'] ? date('d M Y, H:i', strtotime($user['last_login'])) : 'Never' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="editUser(<?= e(json_encode($user)) ?>)"
                                    class="text-orange-500 hover:text-orange-700 mr-3">Edit</button>
                            <?php if ($user['id'] != $currentUser['id']): ?>
                            <button onclick="confirmDelete(<?= $user['id'] ?>, '<?= e($user['name'] ?? $user['username']) ?>')"
                                    class="text-red-500 hover:text-red-700">Delete</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Login Attempts -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Recent Login Attempts</h2>
            <span class="text-sm text-gray-500">Last 30 attempts</span>
        </div>
        <div class="overflow-x-auto max-h-96 overflow-y-auto">
            <table class="w-full">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($loginAttempts as $attempt): ?>
                    <tr>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d M, H:i:s', strtotime($attempt['created_at'])) ?>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm">
                            <?= e($attempt['user_name'] ?? $attempt['email']) ?>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap">
                            <?php if ($attempt['success']): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Success
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Failed
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 font-mono">
                            <?= e($attempt['ip_address']) ?>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                            <?= e($attempt['failure_reason'] ?? '-') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Add New User</h3>
            <button onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_user">
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                    <input type="email" name="email" required
                           placeholder="user@integralsafetyltd.co.uk"
                           pattern=".+@integralsafetyltd\.co\.uk$"
                           title="Email must be @integralsafetyltd.co.uk"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <p class="text-xs text-gray-500 mt-1">Must be @integralsafetyltd.co.uk</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                    <div class="relative">
                        <input type="password" name="password" id="addPassword" required
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <button type="button" onclick="togglePassword('addPassword')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Min 8 chars, uppercase, lowercase, and number</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                    </select>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Add User</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Edit User</h3>
            <button onclick="document.getElementById('editUserModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form method="POST" action="" id="editUserForm">
            <input type="hidden" name="action" value="update_user">
            <input type="hidden" name="user_id" id="editUserId">
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" id="editName" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="editEmail" disabled
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" id="editRole" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="editActive" class="rounded text-orange-500 focus:ring-orange-500">
                    <label for="editActive" class="text-sm text-gray-700">Account Active</label>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-between">
                <div class="flex gap-2">
                    <button type="button" onclick="reset2FA()"
                            class="px-3 py-1.5 text-sm text-yellow-600 hover:text-yellow-800 border border-yellow-300 rounded-lg hover:bg-yellow-50">Reset 2FA</button>
                    <button type="button" onclick="showResetPassword()"
                            class="px-3 py-1.5 text-sm text-blue-600 hover:text-blue-800 border border-blue-300 rounded-lg hover:bg-blue-50">Reset Password</button>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Reset Password</h3>
            <button onclick="document.getElementById('resetPasswordModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" name="user_id" id="resetPasswordUserId">
            <div class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-600">Enter a new password for <strong id="resetPasswordUserName"></strong></p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="newPassword" required
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <button type="button" onclick="togglePassword('newPassword')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Min 8 chars, uppercase, lowercase, and number</p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('resetPasswordModal').classList.add('hidden')"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Reset Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Delete User</h3>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="delete_user">
            <input type="hidden" name="user_id" id="deleteUserId">
            <div class="px-6 py-4">
                <p class="text-gray-600">Are you sure you want to delete <strong id="deleteUserName"></strong>? This action cannot be undone.</p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Delete User</button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden form for 2FA reset -->
<form method="POST" action="" id="reset2FAForm" class="hidden">
    <input type="hidden" name="action" value="reset_2fa">
    <input type="hidden" name="user_id" id="reset2FAUserId">
</form>

<script>
let currentEditUser = null;

function editUser(user) {
    currentEditUser = user;
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editName').value = user.name || user.username;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editRole').value = user.role;
    document.getElementById('editActive').checked = user.is_active == 1;
    document.getElementById('editUserModal').classList.remove('hidden');
}

function reset2FA() {
    if (currentEditUser && confirm('Reset 2FA for ' + (currentEditUser.name || currentEditUser.username) + '? They will need to set up 2FA again on next login.')) {
        document.getElementById('reset2FAUserId').value = currentEditUser.id;
        document.getElementById('reset2FAForm').submit();
    }
}

function showResetPassword() {
    if (currentEditUser) {
        document.getElementById('resetPasswordUserId').value = currentEditUser.id;
        document.getElementById('resetPasswordUserName').textContent = currentEditUser.name || currentEditUser.username;
        document.getElementById('editUserModal').classList.add('hidden');
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }
}

function confirmDelete(userId, userName) {
    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const container = field.parentElement;
    const eyeIcon = container.querySelector('.eye-icon');
    const eyeOffIcon = container.querySelector('.eye-off-icon');

    if (field.type === 'password') {
        field.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeOffIcon.classList.remove('hidden');
    } else {
        field.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeOffIcon.classList.add('hidden');
    }
}

// Close modals on escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
});

// Close modals on backdrop click
document.querySelectorAll('[id$="Modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
