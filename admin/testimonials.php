<?php
/**
 * Testimonials Management
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
    dbExecute("DELETE FROM testimonials WHERE id = ?", [$_GET['delete']]);
    $_SESSION['flash_message'] = 'Testimonial deleted.';
    $_SESSION['flash_type'] = 'success';
    header('Location: /admin/testimonials.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $data = [
            'client_name' => sanitize($_POST['client_name']),
            'company' => sanitize($_POST['company']),
            'content' => sanitize($_POST['content']),
            'rating' => (int)$_POST['rating'],
            'sort_order' => (int)$_POST['sort_order'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($editId) {
            $sql = "UPDATE testimonials SET client_name=?, company=?, content=?, rating=?, sort_order=?, is_active=? WHERE id=?";
            $params = array_values($data);
            $params[] = $editId;
            dbExecute($sql, $params);
        } else {
            $sql = "INSERT INTO testimonials (client_name, company, content, rating, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)";
            dbExecute($sql, array_values($data));
        }

        $_SESSION['flash_message'] = 'Testimonial saved!';
        $_SESSION['flash_type'] = 'success';
        header('Location: /admin/testimonials.php');
        exit;
    }
}

$editItem = $editId ? dbFetchOne("SELECT * FROM testimonials WHERE id = ?", [$editId]) : null;
$testimonials = dbFetchAll("SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC");

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($action === 'new' || $editItem): ?>
<div class="mb-4">
    <a href="/admin/testimonials.php" class="text-orange-500">&larr; Back</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6"><?= $editItem ? 'Edit Testimonial' : 'Add Testimonial' ?></h1>

    <form method="POST">
        <?= csrfField() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Client Name *</label>
                <input type="text" name="client_name" value="<?= e($editItem['client_name'] ?? '') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Company</label>
                <input type="text" name="company" value="<?= e($editItem['company'] ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Testimonial Content *</label>
            <textarea name="content" rows="4" required
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"><?= e($editItem['content'] ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Rating (1-5)</label>
                <select name="rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= ($editItem['rating'] ?? 5) == $i ? 'selected' : '' ?>><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Sort Order</label>
                <input type="number" name="sort_order" value="<?= e($editItem['sort_order'] ?? 0) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="flex items-center pt-8">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-orange-500">
                    <span class="ml-2 text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="/admin/testimonials.php" class="px-6 py-2 border border-gray-300 rounded-lg">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Save</button>
        </div>
    </form>
</div>

<?php else: ?>
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Testimonials</h1>
    <a href="/admin/testimonials.php?action=new" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">+ Add</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Content</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($testimonials as $t): ?>
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium"><?= e($t['client_name']) ?></div>
                    <div class="text-sm text-gray-500"><?= e($t['company']) ?></div>
                </td>
                <td class="px-6 py-4 text-gray-600"><?= e(truncate($t['content'], 80)) ?></td>
                <td class="px-6 py-4"><?= str_repeat('★', $t['rating']) ?></td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="/admin/testimonials.php?edit=<?= $t['id'] ?>" class="text-orange-500">Edit</a>
                    <a href="/admin/testimonials.php?delete=<?= $t['id'] ?>" class="text-red-500" data-confirm="Delete?">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
