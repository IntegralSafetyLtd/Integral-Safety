<?php
/**
 * Blog Migration
 * Creates the blog_posts table for the blog system
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$messages = [];
$errors = [];

// Create blog_posts table if it doesn't exist
try {
    $sql = "CREATE TABLE IF NOT EXISTS blog_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        excerpt TEXT,
        content LONGTEXT,
        featured_image VARCHAR(500),
        meta_description VARCHAR(320),
        focus_keyphrase VARCHAR(255),
        category VARCHAR(100),
        status ENUM('draft', 'published', 'scheduled') DEFAULT 'draft',
        published_at DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_published_at (published_at),
        INDEX idx_category (category)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    dbExecute($sql);
    $messages[] = "Blog posts table created successfully (or already exists).";
} catch (Exception $e) {
    $errors[] = "Error creating blog_posts table: " . $e->getMessage();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Blog Migration</h1>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <h3 class="font-bold">Errors:</h3>
        <ul class="list-disc ml-5">
            <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($messages)): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <h3 class="font-bold">Success:</h3>
        <ul class="list-disc ml-5">
            <?php foreach ($messages as $message): ?>
            <li><?= e($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Blog System Ready</h2>
        <p class="text-gray-600 mb-4">The blog system has been set up. You can now:</p>
        <ul class="list-disc ml-5 text-gray-600 mb-6">
            <li>Create and manage blog posts from the admin panel</li>
            <li>Schedule posts for future publication</li>
            <li>View posts at /blog and /blog/[slug]</li>
        </ul>
        <a href="/admin/blog.php" class="inline-block bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition-colors">
            Go to Blog Management
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
