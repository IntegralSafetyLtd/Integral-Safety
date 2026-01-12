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
            'hero_image' => sanitize($_POST['hero_image']),
            'content' => $_POST['content'], // Allow HTML from editor
        ];

        $sql = "UPDATE pages SET title = ?, meta_description = ?, meta_keywords = ?, hero_title = ?, hero_subtitle = ?, hero_image = ?, content = ? WHERE slug = ?";
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
                <p class="text-sm text-gray-500 mt-1">For home page: Text before "Health & Safety Experts"</p>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Hero Subtitle</label>
                <input type="text" name="hero_subtitle" value="<?= e($editPage['hero_subtitle']) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Hero Image</label>
            <div class="flex gap-2">
                <input type="text" name="hero_image" id="hero_image" value="<?= e($editPage['hero_image']) ?>"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                       placeholder="/uploads/image.jpg">
                <button type="button" onclick="openGalleryPicker('hero_image')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 border border-gray-300">
                    Browse
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-1">Image displayed on the right side of the hero section</p>
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

<!-- Gallery Picker Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Select Image</h3>
            <button onclick="closeGalleryPicker()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto" style="max-height: calc(80vh - 140px);">
            <div id="galleryGrid" class="grid grid-cols-4 md:grid-cols-6 gap-4">
                <!-- Images loaded via JavaScript -->
            </div>
        </div>
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50">
            <button onclick="closeGalleryPicker()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
            <button onclick="selectGalleryImage()" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Select</button>
        </div>
    </div>
</div>

<script>
let galleryTargetField = null;
let selectedImage = null;

function openGalleryPicker(fieldId) {
    galleryTargetField = fieldId;
    selectedImage = null;
    document.getElementById('galleryModal').classList.remove('hidden');
    loadGalleryImages();
}

function closeGalleryPicker() {
    document.getElementById('galleryModal').classList.add('hidden');
    galleryTargetField = null;
    selectedImage = null;
}

function loadGalleryImages() {
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;
    fetch('/admin/api/gallery-images.php?csrf_token=' + encodeURIComponent(csrfToken))
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('galleryGrid');
            if (data.success && data.images.length > 0) {
                grid.innerHTML = data.images.map(img => `
                    <div class="cursor-pointer border-2 border-transparent rounded-lg overflow-hidden hover:border-orange-300 transition-colors gallery-item"
                         data-url="/uploads/${img.filename}"
                         onclick="highlightGalleryImage(this, '/uploads/${img.filename}')">
                        <img src="/uploads/${img.filename}" alt="${img.alt_text || ''}" class="w-full h-24 object-cover">
                    </div>
                `).join('');
            } else {
                grid.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8">No images in gallery. Upload images via the Gallery page.</p>';
            }
        })
        .catch(err => {
            console.error('Error loading gallery:', err);
            document.getElementById('galleryGrid').innerHTML = '<p class="col-span-full text-center text-red-500 py-8">Error loading images</p>';
        });
}

function highlightGalleryImage(el, url) {
    document.querySelectorAll('.gallery-item').forEach(item => {
        item.classList.remove('border-orange-500');
        item.classList.add('border-transparent');
    });
    el.classList.remove('border-transparent');
    el.classList.add('border-orange-500');
    selectedImage = url;
}

function selectGalleryImage() {
    if (selectedImage && galleryTargetField) {
        document.getElementById(galleryTargetField).value = selectedImage;
    }
    closeGalleryPicker();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
