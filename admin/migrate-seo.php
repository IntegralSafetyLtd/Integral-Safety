<?php
/**
 * SEO Database Migration
 * Adds SEO-related columns to pages, services, and training tables
 * Run once to update the database schema
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

// Only allow admins to run migrations
if (!isAdmin()) {
    header('Location: /admin/');
    exit;
}

$messages = [];
$errors = [];

/**
 * Check if a column exists in a table
 */
function columnExists($table, $column) {
    $result = dbFetchOne(
        "SELECT COUNT(*) as count FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?",
        [DB_NAME, $table, $column]
    );
    return $result && $result['count'] > 0;
}

/**
 * Add a column if it doesn't exist
 */
function addColumnIfNotExists($table, $column, $definition, &$messages, &$errors) {
    if (!columnExists($table, $column)) {
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
        if (dbExecute($sql, [])) {
            $messages[] = "Added column '$column' to '$table' table.";
        } else {
            $errors[] = "Failed to add column '$column' to '$table' table.";
        }
    } else {
        $messages[] = "Column '$column' already exists in '$table' table (skipped).";
    }
}

// Run migrations if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migration'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // SEO columns to add to content tables
        $seoColumns = [
            'seo_title' => 'VARCHAR(70) NULL AFTER meta_description',
            'focus_keyphrase' => 'VARCHAR(255) NULL',
            'canonical_url' => 'VARCHAR(500) NULL',
            'robots_directive' => "VARCHAR(50) DEFAULT 'index, follow'",
            'og_image' => 'VARCHAR(500) NULL',
        ];

        // Tables to update
        $tables = ['pages', 'services', 'training'];

        foreach ($tables as $table) {
            // Check if table exists
            $tableCheck = dbFetchOne("SHOW TABLES LIKE ?", [$table]);
            if (!$tableCheck) {
                $errors[] = "Table '$table' does not exist.";
                continue;
            }

            foreach ($seoColumns as $column => $definition) {
                addColumnIfNotExists($table, $column, $definition, $messages, $errors);
            }
        }

        if (empty($errors)) {
            $_SESSION['flash_message'] = 'SEO migration completed successfully!';
            $_SESSION['flash_type'] = 'success';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="mb-4">
    <a href="/admin/" class="text-orange-500 hover:text-orange-600">&larr; Back to Dashboard</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">SEO Database Migration</h1>
    <p class="text-gray-600 mb-6">This migration adds SEO-related columns to the pages, services, and training tables.</p>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Errors:</strong>
        <ul class="list-disc list-inside mt-2">
            <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($messages)): ?>
    <div class="bg-blue-100 text-blue-700 px-4 py-3 rounded mb-4">
        <strong>Migration Results:</strong>
        <ul class="list-disc list-inside mt-2">
            <?php foreach ($messages as $message): ?>
            <li><?= e($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-gray-800 mb-3">Columns to be added:</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-600">
                    <th class="pb-2">Column</th>
                    <th class="pb-2">Type</th>
                    <th class="pb-2">Purpose</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <tr>
                    <td class="py-1 font-mono text-xs">seo_title</td>
                    <td class="py-1">VARCHAR(70)</td>
                    <td class="py-1">Custom SEO title (max 70 chars for search results)</td>
                </tr>
                <tr>
                    <td class="py-1 font-mono text-xs">focus_keyphrase</td>
                    <td class="py-1">VARCHAR(255)</td>
                    <td class="py-1">Target keyword/phrase for the page</td>
                </tr>
                <tr>
                    <td class="py-1 font-mono text-xs">canonical_url</td>
                    <td class="py-1">VARCHAR(500)</td>
                    <td class="py-1">Canonical URL to avoid duplicate content</td>
                </tr>
                <tr>
                    <td class="py-1 font-mono text-xs">robots_directive</td>
                    <td class="py-1">VARCHAR(50)</td>
                    <td class="py-1">Robots meta tag value (index/noindex)</td>
                </tr>
                <tr>
                    <td class="py-1 font-mono text-xs">og_image</td>
                    <td class="py-1">VARCHAR(500)</td>
                    <td class="py-1">Custom Open Graph image for social sharing</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <h4 class="font-semibold text-yellow-800">Before running:</h4>
                <ul class="text-yellow-700 text-sm mt-1 list-disc list-inside">
                    <li>This migration is safe to run multiple times (it skips existing columns)</li>
                    <li>Consider backing up your database before running migrations</li>
                    <li>The tables pages, services, and training will be modified</li>
                </ul>
            </div>
        </div>
    </div>

    <form method="POST">
        <?= csrfField() ?>
        <input type="hidden" name="run_migration" value="1">
        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-medium">
            Run Migration
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
