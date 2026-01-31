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
            'seo_title' => sanitize($_POST['seo_title'] ?? ''),
            'focus_keyphrase' => sanitize($_POST['focus_keyphrase'] ?? ''),
            'canonical_url' => sanitize($_POST['canonical_url'] ?? ''),
            'robots_directive' => sanitize($_POST['robots_directive'] ?? 'index, follow'),
            'og_image' => sanitize($_POST['og_image'] ?? ''),
            'related_blog_id' => !empty($_POST['related_blog_id']) ? (int)$_POST['related_blog_id'] : null,
        ];

        if ($editId) {
            // Update
            $sql = "UPDATE services SET title=?, slug=?, short_description=?, content=?, icon=?, meta_description=?, show_on_homepage=?, sort_order=?, is_active=?, seo_title=?, focus_keyphrase=?, canonical_url=?, robots_directive=?, og_image=?, related_blog_id=? WHERE id=?";
            $params = array_values($data);
            $params[] = $editId;
            $success = dbExecute($sql, $params);
        } else {
            // Insert
            $sql = "INSERT INTO services (title, slug, short_description, content, icon, meta_description, show_on_homepage, sort_order, is_active, seo_title, focus_keyphrase, canonical_url, robots_directive, og_image, related_blog_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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

// Get blog posts for related blog dropdown
$blogPosts = dbFetchAll("SELECT id, title FROM blog_posts ORDER BY title ASC");

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
            <textarea name="meta_description" id="meta_description" rows="2" maxlength="160"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"><?= e($editService['meta_description'] ?? '') ?></textarea>
            <div class="flex justify-between mt-1">
                <p class="text-sm text-gray-500">Shown in search results (recommended: 150-160 characters)</p>
                <span class="text-sm text-gray-500"><span id="meta_description_count">0</span>/160</span>
            </div>
        </div>

        <!-- SEO Settings Section -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                SEO Settings
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">SEO Title</label>
                    <input type="text" name="seo_title" id="seo_title" value="<?= e($editService['seo_title'] ?? '') ?>"
                           maxlength="70"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                           placeholder="Custom title for search results (max 70 chars)">
                    <div class="flex justify-between mt-1">
                        <p class="text-sm text-gray-500">Overrides service title in search results</p>
                        <span id="seo_title_counter" class="text-sm text-gray-500"><span id="seo_title_count">0</span>/70</span>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Focus Keyphrase</label>
                    <input type="text" name="focus_keyphrase" value="<?= e($editService['focus_keyphrase'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                           placeholder="e.g., fire risk assessment services">
                    <p class="text-sm text-gray-500 mt-1">Target keyword for this service</p>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Canonical URL</label>
                    <input type="text" name="canonical_url" value="<?= e($editService['canonical_url'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                           placeholder="Leave blank for default">
                    <p class="text-sm text-gray-500 mt-1">Only set if this content exists elsewhere</p>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Robots Directive</label>
                    <select name="robots_directive"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="index, follow" <?= ($editService['robots_directive'] ?? 'index, follow') === 'index, follow' ? 'selected' : '' ?>>index, follow (Default)</option>
                        <option value="index, nofollow" <?= ($editService['robots_directive'] ?? '') === 'index, nofollow' ? 'selected' : '' ?>>index, nofollow</option>
                        <option value="noindex, follow" <?= ($editService['robots_directive'] ?? '') === 'noindex, follow' ? 'selected' : '' ?>>noindex, follow</option>
                        <option value="noindex, nofollow" <?= ($editService['robots_directive'] ?? '') === 'noindex, nofollow' ? 'selected' : '' ?>>noindex, nofollow</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Control search engine indexing</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Open Graph Image</label>
                    <div class="flex gap-2">
                        <input type="text" name="og_image" id="og_image" value="<?= e($editService['og_image'] ?? '') ?>"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                               placeholder="Leave blank to use default">
                        <button type="button" onclick="openGalleryPicker('og_image')"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 border border-gray-300">
                            Browse
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Image shown when shared on social media (1200x630 recommended)</p>
                </div>
            </div>
        </div>

        <!-- Related Blog Post -->
        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Related Blog Post</label>
            <select name="related_blog_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <option value="">None</option>
                <?php foreach ($blogPosts as $blog): ?>
                <option value="<?= $blog['id'] ?>" <?= ($editService['related_blog_id'] ?? '') == $blog['id'] ? 'selected' : '' ?>>
                    <?= e($blog['title']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <p class="text-sm text-gray-500 mt-1">When a related blog is live, a "Read More" button will appear on this service page</p>
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

<?php require_once __DIR__ . '/includes/gallery-picker.php'; ?>

<script>
// SEO Title character counter
document.addEventListener('DOMContentLoaded', function() {
    const seoTitleInput = document.getElementById('seo_title');
    const seoTitleCount = document.getElementById('seo_title_count');

    if (seoTitleInput && seoTitleCount) {
        function updateCount() {
            const count = seoTitleInput.value.length;
            seoTitleCount.textContent = count;
            if (count > 60) {
                seoTitleCount.parentElement.classList.add('text-orange-500');
                seoTitleCount.parentElement.classList.remove('text-gray-500');
            } else {
                seoTitleCount.parentElement.classList.remove('text-orange-500');
                seoTitleCount.parentElement.classList.add('text-gray-500');
            }
        }

        seoTitleInput.addEventListener('input', updateCount);
        updateCount(); // Initial count
    }
});
</script>

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
