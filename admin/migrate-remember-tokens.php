<?php
/**
 * Remember Tokens Migration
 * Creates table for "Trust This Browser" feature
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$messages = [];
$errors = [];

// Create remember_tokens table
try {
    $sql = "CREATE TABLE IF NOT EXISTS remember_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token_hash VARCHAR(255) NOT NULL,
        device_info VARCHAR(255),
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at DATETIME NOT NULL,
        last_used_at DATETIME,
        INDEX idx_user_id (user_id),
        INDEX idx_token_hash (token_hash),
        INDEX idx_expires (expires_at),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    dbExecute($sql);
    $messages[] = "remember_tokens table created successfully.";
} catch (Exception $e) {
    $errors[] = "Error creating table: " . $e->getMessage();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Remember Tokens Migration</h1>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc ml-5">
            <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($messages)): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc ml-5">
            <?php foreach ($messages as $message): ?>
            <li><?= e($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">"Trust This Browser" Feature Ready</h2>
        <p class="text-gray-600 mb-4">The database table for trusted devices has been created. Users can now:</p>
        <ul class="list-disc ml-5 text-gray-600 mb-4">
            <li>Check "Trust this browser for 7 days" on the 2FA verification screen</li>
            <li>Stay logged in for 7 days without re-entering 2FA</li>
            <li>View and revoke trusted devices from Settings</li>
        </ul>
        <a href="/admin/settings.php" class="inline-block bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition-colors">
            Go to Settings
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
