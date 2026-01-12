<?php
/**
 * Pages Management
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$action = $_GET['action'] ?? 'list';
$editSlug = $_GET['edit'] ?? null;
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $slug = sanitize($_POST['slug']);
        $data = [
            'title' => sanitize($_POST['title']),
            'meta_description' => sanitize($_POST['meta_description']),
            'meta_keywords' => sanitize($_POST['meta_keywords']),
            'hero_title' => sanitize($_POST['hero_title']),
            'hero_subtitle' => sanitize($_POST['hero_subtitle']),
            'content' => $_POST['content'], // Allow HTML from editor
        ];

        $sql = "UPDATE pages SET title = ?, meta_description = ?, meta_keywords = ?, hero_title = ?, hero_subtitle = ?, content = ? WHERE slug = ?";
        $params = array_values($data);
        $params[] = $slug;

        if (dbExecute($sql, $params)) {
            $_SESSION['flash_message'] = 'Page updated successfully!';
            $_SESSION['flash_type'] = 'success';
            header('Location: /admin/pages.php');
            exit;
        } else {
            $error = 'Failed to update page.';
        }
    }
}

// Get page for editing
$editPage = null;
if ($editSlug) {
    $editPage = dbFetchOne("SELECT * FROM pages WHERE slug = ?", [$editSlug]);
}

// Get all pages
$pages = dbFetchAll("SELECT * FROM pages ORDER BY title ASC");

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($editPage): ?>
<!-- Edit Page Form -->
<div class="mb-4">
    <a href="/admin/pages.php" class="text-orange-500 hover:text-orange-600">&larr; Back to Pages</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Page: <?= e($editPage['title']) ?></h1>

    <?php if ($error): ?>
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <?= csrfField() ?>
        <input type="hidden" name="slug" value="<?= e($editPage['slug']) ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Page Title</label>
                <input type="text" name="title" value="<?= e($editPage['title']) ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">URL Slug</label>
                <input type="text" value="<?= e($editPage['slug']) ?>" disabled
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Meta Description (SEO)</label>
            <textarea name="meta_description" rows="2"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"><?= e($editPage['meta_description']) ?></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Meta Keywords (SEO)</label>
            <input type="text" name="meta_keywords" value="<?= e($editPage['meta_keywords']) ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Hero Title</label>
                <input type="text" name="hero_title" value="<?= e($editPage['hero_title']) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Hero Subtitle</label>
                <input type="text" name="hero_subtitle" value="<?= e($editPage['hero_subtitle']) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Page Content</label>
            <div class="quill-editor bg-white" style="min-height: 300px;"></div>
            <input type="hidden" name="content" value="<?= e($editPage['content']) ?>">
        </div>

        <div class="flex justify-end space-x-4">
            <a href="/admin/pages.php" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                Save Changes
            </button>
        </div>
    </form>
</div>

<?php else: ?>
<!-- Pages List -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Pages</h1>
    <p class="text-gray-600">Manage your website pages</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Page</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Updated</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($pages as $page): ?>
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900"><?= e($page['title']) ?></div>
                </td>
                <td class="px-6 py-4 text-gray-500">
                    /<?= e($page['slug'] === 'home' ? '' : $page['slug']) ?>
                </td>
                <td class="px-6 py-4 text-gray-500">
                    <?= formatDate($page['updated_at']) ?>
                </td>
                <td class="px-6 py-4 text-right">
                    <a href="/admin/pages.php?edit=<?= e($page['slug']) ?>"
                       class="text-orange-500 hover:text-orange-600">Edit</a>
                    <span class="mx-2 text-gray-300">|</span>
                    <a href="/<?= e($page['slug'] === 'home' ? '' : $page['slug']) ?>" target="_blank"
                       class="text-gray-500 hover:text-gray-700">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
