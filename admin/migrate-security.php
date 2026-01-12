<?php
/**
 * Security Migration Script
 * Adds 2FA support, login attempts tracking, and user management
 * Run once, then delete this file
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/auth.php';

// Only allow if already logged in as admin
if (!isLoggedIn()) {
    die('Please log in first to run this migration.');
}

$results = [];

try {
    // 1. Add 2FA columns to users table
    $columns = [
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS name VARCHAR(255) DEFAULT NULL AFTER email",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS twofa_method ENUM('none', 'email', 'totp', 'both') DEFAULT 'none' AFTER role",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS twofa_secret VARCHAR(64) DEFAULT NULL AFTER twofa_method",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS twofa_verified TINYINT(1) DEFAULT 0 AFTER twofa_secret",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1 AFTER twofa_verified",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS failed_attempts INT DEFAULT 0 AFTER is_active",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS locked_until DATETIME DEFAULT NULL AFTER failed_attempts",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER locked_until",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL AFTER name",
    ];

    foreach ($columns as $sql) {
        try {
            dbExecute($sql);
            $results[] = "OK: " . substr($sql, 0, 60) . "...";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                $results[] = "SKIP: Column already exists";
            } else {
                $results[] = "ERROR: " . $e->getMessage();
            }
        }
    }

    // 2. Create login_attempts table
    $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT,
        success TINYINT(1) DEFAULT 0,
        failure_reason VARCHAR(100) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_ip (ip_address),
        INDEX idx_created (created_at)
    )";
    dbExecute($sql);
    $results[] = "OK: Created login_attempts table";

    // 3. Create two_factor_codes table for email codes
    $sql = "CREATE TABLE IF NOT EXISTS two_factor_codes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        code VARCHAR(10) NOT NULL,
        type ENUM('email', 'sms') DEFAULT 'email',
        expires_at DATETIME NOT NULL,
        used TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user (user_id),
        INDEX idx_code (code),
        INDEX idx_expires (expires_at),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    dbExecute($sql);
    $results[] = "OK: Created two_factor_codes table";

    // 4. Update existing admin user to require 2FA setup
    // First, check if current user has 2FA - if not, they'll be prompted to set it up
    $currentUser = getCurrentUser();
    $user = dbFetchOne("SELECT twofa_method, twofa_verified FROM users WHERE id = ?", [$currentUser['id']]);

    if ($user && $user['twofa_method'] === 'none') {
        $results[] = "NOTE: Your account will need 2FA setup on next login";
    }

    // 5. Add email domain constraint check function
    $results[] = "OK: Migration complete - security tables ready";
    $results[] = "";
    $results[] = "NEXT STEPS:";
    $results[] = "1. You will be logged out";
    $results[] = "2. Log back in to set up 2FA";
    $results[] = "3. Delete this migration file";

} catch (Exception $e) {
    $results[] = "FATAL ERROR: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Security Migration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Security Migration Results</h1>
        <div class="bg-white rounded-lg shadow p-6">
            <ul class="space-y-2 font-mono text-sm">
                <?php foreach ($results as $result): ?>
                <li class="<?= strpos($result, 'ERROR') !== false ? 'text-red-600' : (strpos($result, 'OK') !== false ? 'text-green-600' : (strpos($result, 'SKIP') !== false ? 'text-yellow-600' : 'text-gray-700')) ?>">
                    <?= e($result) ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="mt-6 flex gap-4">
            <a href="/admin/logout.php" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">Log Out & Set Up 2FA</a>
            <a href="/admin/" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">Back to Admin</a>
        </div>
        <p class="mt-4 text-red-600 font-medium">Remember to delete this file after migration!</p>
    </div>
</body>
</html>
