<?php
/**
 * Blog Posts Management
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
    if (dbExecute("DELETE FROM blog_posts WHERE id = ?", [$_GET['delete']])) {
        $_SESSION['flash_message'] = 'Blog post deleted.';
        $_SESSION['flash_type'] = 'success';
    }
    header('Location: /admin/blog.php');
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
            'excerpt' => sanitize($_POST['excerpt']),
            'content' => $_POST['content'],
            'featured_image' => sanitize($_POST['featured_image']),
            'meta_description' => sanitize($_POST['meta_description']),
            'focus_keyphrase' => sanitize($_POST['focus_keyphrase']),
            'category' => sanitize($_POST['category']),
            'status' => sanitize($_POST['status']),
            'published_at' => !empty($_POST['published_at']) ? $_POST['published_at'] : null,
        ];

        // If publishing and no date set, use now
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        if ($editId) {
            $sql = "UPDATE blog_posts SET title=?, slug=?, excerpt=?, content=?, featured_image=?, meta_description=?, focus_keyphrase=?, category=?, status=?, published_at=?, updated_at=NOW() WHERE id=?";
            $params = array_values($data);
            $params[] = $editId;
            $success = dbExecute($sql, $params);
        } else {
            $sql = "INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, meta_description, focus_keyphrase, category, status, published_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $success = dbExecute($sql, array_values($data));
        }

        if ($success) {
            $_SESSION['flash_message'] = 'Blog post saved!';
            $_SESSION['flash_type'] = 'success';
            header('Location: /admin/blog.php');
            exit;
        } else {
            $error = 'Failed to save blog post.';
        }
    }
}

$editPost = null;
if ($editId) {
    $editPost = dbFetchOne("SELECT * FROM blog_posts WHERE id = ?", [$editId]);
}

// Get posts with counts
$posts = dbFetchAll("SELECT * FROM blog_posts ORDER BY
    CASE WHEN status = 'draft' THEN 0
         WHEN status = 'scheduled' THEN 1
         ELSE 2 END,
    published_at DESC, created_at DESC");

$categories = ['Fire Safety', 'Health & Safety', 'Training', 'Compliance', 'Workplace Safety', 'Legislation', 'Tips & Guides'];

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($action === 'new' || $editPost): ?>
<div class="mb-4">
    <a href="/admin/blog.php" class="text-orange-500 hover:text-orange-600">&larr; Back to Blog Posts</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <?= $editPost ? 'Edit: ' . e($editPost['title']) : 'New Blog Post' ?>
    </h1>

    <?php if ($error): ?>
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <?= csrfField() ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Post Title *</label>
                    <input type="text" name="title" value="<?= e($editPost['title'] ?? '') ?>" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Excerpt</label>
                    <textarea name="excerpt" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                              placeholder="Brief summary shown in blog listings..."><?= e($editPost['excerpt'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-2">Content</label>
                    <div class="quill-editor bg-white" style="min-height: 400px;"></div>
                    <input type="hidden" name="content" value="<?= e($editPost['content'] ?? '') ?>">
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Box -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-4">Publish</h3>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="draft" <?= ($editPost['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="scheduled" <?= ($editPost['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                            <option value="published" <?= ($editPost['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Publish Date</label>
                        <input type="datetime-local" name="published_at"
                               value="<?= $editPost['published_at'] ? date('Y-m-d\TH:i', strtotime($editPost['published_at'])) : '' ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to publish immediately</p>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                        <?= $editPost ? 'Update Post' : 'Save Post' ?>
                    </button>
                </div>

                <!-- Category -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-4">Category</h3>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Select category...</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= e($cat) ?>" <?= ($editPost['category'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Featured Image -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-4">Featured Image</h3>
                    <div class="flex gap-2">
                        <input type="text" name="featured_image" id="featured_image"
                               value="<?= e($editPost['featured_image'] ?? '') ?>"
                               placeholder="/uploads/image.jpg"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm">
                        <button type="button" onclick="openGalleryPicker('featured_image')"
                                class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                            Browse
                        </button>
                    </div>
                    <div id="featured_image_preview" class="mt-3">
                        <?php if (!empty($editPost['featured_image'])): ?>
                        <img src="<?= e($editPost['featured_image']) ?>" class="w-full h-32 object-cover rounded-lg">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SEO -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-4">SEO</h3>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Focus Keyphrase</label>
                        <input type="text" name="focus_keyphrase" value="<?= e($editPost['focus_keyphrase'] ?? '') ?>"
                               placeholder="e.g., fire risk assessment guide"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Meta Description</label>
                        <textarea name="meta_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm"
                                  placeholder="Brief description for search results..."><?= e($editPost['meta_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

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
            <div id="galleryGrid" class="grid grid-cols-4 md:grid-cols-6 gap-4"></div>
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
                         onclick="highlightGalleryImage(this, '/uploads/${img.filename}')">
                        <img src="/uploads/${img.filename}" class="w-full h-24 object-cover">
                    </div>
                `).join('');
            } else {
                grid.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8">No images. Upload via Gallery page.</p>';
            }
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
        document.getElementById(galleryTargetField + '_preview').innerHTML =
            '<img src="' + selectedImage + '" class="w-full h-32 object-cover rounded-lg">';
    }
    closeGalleryPicker();
}
</script>

<?php else: ?>
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Blog Posts</h1>
        <p class="text-gray-600">Manage your blog articles and content</p>
    </div>
    <a href="/admin/blog.php?action=new" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
        + New Post
    </a>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <?php
    $totalPosts = count($posts);
    $published = count(array_filter($posts, fn($p) => $p['status'] === 'published'));
    $scheduled = count(array_filter($posts, fn($p) => $p['status'] === 'scheduled'));
    $drafts = count(array_filter($posts, fn($p) => $p['status'] === 'draft'));
    ?>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Total Posts</p>
        <p class="text-2xl font-bold text-gray-800"><?= $totalPosts ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Published</p>
        <p class="text-2xl font-bold text-green-600"><?= $published ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Scheduled</p>
        <p class="text-2xl font-bold text-blue-600"><?= $scheduled ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-gray-500 text-sm">Drafts</p>
        <p class="text-2xl font-bold text-gray-600"><?= $drafts ?></p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Post</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($posts)): ?>
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    No blog posts yet. <a href="/admin/blog.php?action=new" class="text-orange-500 hover:text-orange-600">Create your first post</a>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900"><?= e($post['title']) ?></div>
                    <div class="text-sm text-gray-500">/blog/<?= e($post['slug']) ?></div>
                </td>
                <td class="px-6 py-4 text-gray-500"><?= e($post['category'] ?: '-') ?></td>
                <td class="px-6 py-4">
                    <?php
                    $statusColors = [
                        'published' => 'bg-green-100 text-green-800',
                        'scheduled' => 'bg-blue-100 text-blue-800',
                        'draft' => 'bg-gray-100 text-gray-800',
                    ];
                    $color = $statusColors[$post['status']] ?? 'bg-gray-100 text-gray-800';
                    ?>
                    <span class="px-2 py-1 text-xs rounded-full <?= $color ?>">
                        <?= ucfirst(e($post['status'])) ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-500 text-sm">
                    <?= $post['published_at'] ? formatDate($post['published_at']) : '-' ?>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="/admin/blog.php?edit=<?= $post['id'] ?>" class="text-orange-500 hover:text-orange-600">Edit</a>
                    <?php if ($post['status'] === 'published'): ?>
                    <a href="/blog/<?= e($post['slug']) ?>" target="_blank" class="text-gray-500 hover:text-gray-700">View</a>
                    <?php endif; ?>
                    <a href="/admin/blog.php?delete=<?= $post['id'] ?>" class="text-red-500 hover:text-red-600" data-confirm="Delete this post?">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
