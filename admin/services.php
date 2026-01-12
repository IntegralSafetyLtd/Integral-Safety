<?php
/**
 * Services Management
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$action = $_GET['action'] ?? 'list';
$editId = $_GET['edit'] ?? null;
$error = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (dbExecute("DELETE FROM services WHERE id = ?", [$_GET['delete']])) {
        $_SESSION['flash_message'] = 'Service deleted.';
        $_SESSION['flash_type'] = 'success';
    }
    header('Location: /admin/services.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $data = [
            'title' => sanitize($_POST['title']),
            'slug' => slugify($_POST['title']),
            'short_description' => sanitize($_POST['short_description']),
            'content' => $_POST['content'],
            'icon' => sanitize($_POST['icon']),
            'meta_description' => sanitize($_POST['meta_description']),
            'show_on_homepage' => isset($_POST['show_on_homepage']) ? 1 : 0,
            'sort_order' => (int)$_POST['sort_order'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($editId) {
            // Update
            $sql = "UPDATE services SET title=?, slug=?, short_description=?, content=?, icon=?, meta_description=?, show_on_homepage=?, sort_order=?, is_active=? WHERE id=?";
            $params = array_values($data);
            $params[] = $editId;
            $success = dbExecute($sql, $params);
        } else {
            // Insert
            $sql = "INSERT INTO services (title, slug, short_description, content, icon, meta_description, show_on_homepage, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $success = dbExecute($sql, array_values($data));
        }

        if ($success) {
            $_SESSION['flash_message'] = 'Service saved successfully!';
            $_SESSION['flash_type'] = 'success';
            header('Location: /admin/services.php');
            exit;
        } else {
            $error = 'Failed to save service.';
        }
    }
}

// Get service for editing
$editService = null;
if ($editId) {
    $editService = dbFetchOne("SELECT * FROM services WHERE id = ?", [$editId]);
}

// Get all services
$services = dbFetchAll("SELECT * FROM services ORDER BY sort_order ASC, title ASC");

$icons = ['fire', 'clipboard', 'book', 'shield', 'users', 'check'];

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($action === 'new' || $editService): ?>
<!-- Edit/New Service Form -->
<div class="mb-4">
    <a href="/admin/services.php" class="text-orange-500 hover:text-orange-600">&larr; Back to Services</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <?= $editService ? 'Edit Service: ' . e($editService['title']) : 'Add New Service' ?>
    </h1>

    <?php if ($error): ?>
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <?= csrfField() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Service Title *</label>
                <input type="text" name="title" value="<?= e($editService['title'] ?? '') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Icon</label>
                <select name="icon" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <?php foreach ($icons as $icon): ?>
                    <option value="<?= $icon ?>" <?= ($editService['icon'] ?? 'clipboard') === $icon ? 'selected' : '' ?>>
                        <?= ucfirst($icon) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Short Description</label>
            <textarea name="short_description" rows="2"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"><?= e($editService['short_description'] ?? '') ?></textarea>
            <p class="text-sm text-gray-500 mt-1">Shown on service cards (keep it brief)</p>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Full Content</label>
            <div class="quill-editor bg-white" style="min-height: 300px;"></div>
            <input type="hidden" name="content" value="<?= e($editService['content'] ?? '') ?>">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Meta Description (SEO)</label>
            <textarea name="meta_description" rows="2"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"><?= e($editService['meta_description'] ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Sort Order</label>
                <input type="number" name="sort_order" value="<?= e($editService['sort_order'] ?? 0) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>

            <div class="flex items-center pt-8">
                <label class="flex items-center">
                    <input type="checkbox" name="show_on_homepage" value="1"
                           <?= ($editService['show_on_homepage'] ?? 1) ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                    <span class="ml-2 text-gray-700">Show on Homepage</span>
                </label>
            </div>

            <div class="flex items-center pt-8">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           <?= ($editService['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                    <span class="ml-2 text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="/admin/services.php" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                Save Service
            </button>
        </div>
    </form>
</div>

<?php else: ?>
<!-- Services List -->
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Services</h1>
        <p class="text-gray-600">Manage your service offerings</p>
    </div>
    <a href="/admin/services.php?action=new" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
        + Add Service
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Homepage</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($services as $service): ?>
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900"><?= e($service['title']) ?></div>
                    <div class="text-sm text-gray-500">/services/<?= e($service['slug']) ?></div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full <?= $service['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                        <?= $service['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td class="px-6 py-4">
                    <?= $service['show_on_homepage'] ? 'Yes' : 'No' ?>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="/admin/services.php?edit=<?= $service['id'] ?>" class="text-orange-500 hover:text-orange-600">Edit</a>
                    <a href="/services/<?= e($service['slug']) ?>" target="_blank" class="text-gray-500 hover:text-gray-700">View</a>
                    <a href="/admin/services.php?delete=<?= $service['id'] ?>" class="text-red-500 hover:text-red-600" data-confirm="Are you sure you want to delete this service?">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
