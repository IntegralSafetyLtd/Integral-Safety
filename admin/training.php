<?php
/**
 * Training Courses Management
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
    if (dbExecute("DELETE FROM training WHERE id = ?", [$_GET['delete']])) {
        $_SESSION['flash_message'] = 'Training course deleted.';
        $_SESSION['flash_type'] = 'success';
    }
    header('Location: /admin/training.php');
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
            'duration' => sanitize($_POST['duration']),
            'certification' => sanitize($_POST['certification']),
            'delivery_method' => sanitize($_POST['delivery_method']),
            'meta_description' => sanitize($_POST['meta_description']),
            'show_on_homepage' => isset($_POST['show_on_homepage']) ? 1 : 0,
            'sort_order' => (int)$_POST['sort_order'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($editId) {
            $sql = "UPDATE training SET title=?, slug=?, short_description=?, content=?, duration=?, certification=?, delivery_method=?, meta_description=?, show_on_homepage=?, sort_order=?, is_active=? WHERE id=?";
            $params = array_values($data);
            $params[] = $editId;
            $success = dbExecute($sql, $params);
        } else {
            $sql = "INSERT INTO training (title, slug, short_description, content, duration, certification, delivery_method, meta_description, show_on_homepage, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $success = dbExecute($sql, array_values($data));
        }

        if ($success) {
            $_SESSION['flash_message'] = 'Training course saved!';
            $_SESSION['flash_type'] = 'success';
            header('Location: /admin/training.php');
            exit;
        } else {
            $error = 'Failed to save training course.';
        }
    }
}

$editCourse = null;
if ($editId) {
    $editCourse = dbFetchOne("SELECT * FROM training WHERE id = ?", [$editId]);
}

$courses = dbFetchAll("SELECT * FROM training ORDER BY sort_order ASC, title ASC");

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($action === 'new' || $editCourse): ?>
<div class="mb-4">
    <a href="/admin/training.php" class="text-orange-500 hover:text-orange-600">&larr; Back to Training</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <?= $editCourse ? 'Edit: ' . e($editCourse['title']) : 'Add New Training Course' ?>
    </h1>

    <?php if ($error): ?>
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <?= csrfField() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Course Title *</label>
                <input type="text" name="title" value="<?= e($editCourse['title'] ?? '') ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Duration</label>
                <input type="text" name="duration" value="<?= e($editCourse['duration'] ?? '') ?>"
                       placeholder="e.g., 4 days, Half day"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Certification</label>
                <input type="text" name="certification" value="<?= e($editCourse['certification'] ?? '') ?>"
                       placeholder="e.g., IOSH Certificate"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Delivery Method</label>
                <input type="text" name="delivery_method" value="<?= e($editCourse['delivery_method'] ?? '') ?>"
                       placeholder="e.g., Classroom, Online, Blended"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Short Description</label>
            <textarea name="short_description" rows="2"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"><?= e($editCourse['short_description'] ?? '') ?></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Full Content</label>
            <div class="quill-editor bg-white" style="min-height: 300px;"></div>
            <input type="hidden" name="content" value="<?= e($editCourse['content'] ?? '') ?>">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Meta Description (SEO)</label>
            <textarea name="meta_description" rows="2"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"><?= e($editCourse['meta_description'] ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Sort Order</label>
                <input type="number" name="sort_order" value="<?= e($editCourse['sort_order'] ?? 0) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="flex items-center pt-8">
                <label class="flex items-center">
                    <input type="checkbox" name="show_on_homepage" value="1"
                           <?= ($editCourse['show_on_homepage'] ?? 1) ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                    <span class="ml-2 text-gray-700">Show on Homepage</span>
                </label>
            </div>
            <div class="flex items-center pt-8">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           <?= ($editCourse['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                    <span class="ml-2 text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="/admin/training.php" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Save</button>
        </div>
    </form>
</div>

<?php else: ?>
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Training Courses</h1>
        <p class="text-gray-600">Manage your training offerings</p>
    </div>
    <a href="/admin/training.php?action=new" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
        + Add Course
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($courses as $course): ?>
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900"><?= e($course['title']) ?></div>
                    <div class="text-sm text-gray-500">/training/<?= e($course['slug']) ?></div>
                </td>
                <td class="px-6 py-4 text-gray-500"><?= e($course['duration'] ?: '-') ?></td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full <?= $course['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                        <?= $course['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="/admin/training.php?edit=<?= $course['id'] ?>" class="text-orange-500 hover:text-orange-600">Edit</a>
                    <a href="/training/<?= e($course['slug']) ?>" target="_blank" class="text-gray-500 hover:text-gray-700">View</a>
                    <a href="/admin/training.php?delete=<?= $course['id'] ?>" class="text-red-500 hover:text-red-600" data-confirm="Delete this course?">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
