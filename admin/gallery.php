<?php
/**
 * Gallery/Image Management
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deleteImage($_GET['delete'])) {
        $_SESSION['flash_message'] = 'Image deleted.';
        $_SESSION['flash_type'] = 'success';
    }
    header('Location: /admin/gallery.php');
    exit;
}

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request.';
    } else {
        $result = uploadImage($_FILES['image']);
        if ($result['success']) {
            $_SESSION['flash_message'] = 'Image uploaded successfully!';
            $_SESSION['flash_type'] = 'success';
            header('Location: /admin/gallery.php');
            exit;
        } else {
            $error = $result['error'];
        }
    }
}

// Handle alt text update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_alt'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $id = (int)$_POST['image_id'];
        $altText = sanitize($_POST['alt_text']);
        dbExecute("UPDATE gallery SET alt_text = ? WHERE id = ?", [$altText, $id]);
        $_SESSION['flash_message'] = 'Image updated.';
        $_SESSION['flash_type'] = 'success';
        header('Location: /admin/gallery.php');
        exit;
    }
}

// Get all images
$images = getGalleryImages();
$csrfToken = generateCSRFToken();

require_once __DIR__ . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Image Gallery</h1>
        <p class="text-gray-600">Upload and manage images for your website</p>
    </div>
</div>

<!-- Upload Form -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Upload New Image</h2>

    <?php if ($error): ?>
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="flex items-end gap-4">
        <?= csrfField() ?>
        <div class="flex-1">
            <label class="block text-gray-700 font-medium mb-2">Select Image</label>
            <input type="file" name="image" accept="image/*" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Max 5MB. Allowed: JPG, PNG, GIF, WebP</p>
        </div>
        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
            Upload
        </button>
    </form>
</div>

<!-- Image Grid -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Your Images (<?= count($images) ?>)</h2>

    <?php if (empty($images)): ?>
    <p class="text-gray-500">No images uploaded yet.</p>
    <?php else: ?>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <?php foreach ($images as $image): ?>
        <div class="relative group">
            <img src="<?= UPLOADS_URL ?>/<?= e($image['filename']) ?>"
                 alt="<?= e($image['alt_text']) ?>"
                 class="w-full h-32 object-cover rounded-lg cursor-pointer"
                 onclick="openImageEditor(<?= $image['id'] ?>)">

            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center space-x-2">
                <button onclick="openImageEditor(<?= $image['id'] ?>)"
                        class="px-2 py-1 bg-white text-gray-800 rounded text-sm hover:bg-gray-100">
                    Edit
                </button>
                <button onclick="copyUrl('<?= UPLOADS_URL ?>/<?= e($image['filename']) ?>')"
                        class="px-2 py-1 bg-white text-gray-800 rounded text-sm hover:bg-gray-100">
                    Copy URL
                </button>
                <a href="/admin/gallery.php?delete=<?= $image['id'] ?>"
                   class="px-2 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600"
                   data-confirm="Delete this image?">
                    Delete
                </a>
            </div>

            <div class="text-xs text-gray-500 mt-1 truncate"><?= e($image['original_name']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Image Editor Modal -->
<div id="imageEditorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full mx-4 my-8">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Edit Image</h3>
            <button onclick="closeImageEditor()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="flex flex-col lg:flex-row">
            <!-- Image Preview Area -->
            <div class="lg:w-2/3 p-6 border-b lg:border-b-0 lg:border-r bg-gray-100">
                <div class="relative bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center" style="min-height: 400px;">
                    <img id="editorPreview" src="" alt="Preview" class="max-w-full max-h-[500px]">
                    <!-- Crop overlay -->
                    <div id="cropOverlay" class="hidden absolute bg-black bg-opacity-50">
                        <div id="cropSelection" class="absolute border-2 border-white bg-transparent cursor-move"
                             style="top: 50px; left: 50px; width: 200px; height: 150px;">
                            <div class="absolute -top-1 -left-1 w-3 h-3 bg-white border border-gray-400 cursor-nw-resize" data-handle="nw"></div>
                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-white border border-gray-400 cursor-ne-resize" data-handle="ne"></div>
                            <div class="absolute -bottom-1 -left-1 w-3 h-3 bg-white border border-gray-400 cursor-sw-resize" data-handle="sw"></div>
                            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-white border border-gray-400 cursor-se-resize" data-handle="se"></div>
                        </div>
                    </div>
                </div>
                <p id="editorStatus" class="text-sm text-gray-500 mt-2 text-center"></p>
            </div>

            <!-- Controls Panel -->
            <div class="lg:w-1/3 p-6 space-y-6 max-h-[600px] overflow-y-auto">
                <!-- File Information -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">File Information</h4>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p>Dimensions: <span id="infoDimensions" class="font-medium">-</span></p>
                        <p>File Size: <span id="infoFileSize" class="font-medium">-</span></p>
                        <p>Format: <span id="infoFormat" class="font-medium">-</span></p>
                    </div>
                </div>

                <!-- Rename -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Rename File</h4>
                    <div class="flex items-center gap-2">
                        <input type="text" id="renameInput"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500"
                               placeholder="filename">
                        <span id="fileExtension" class="text-gray-500 text-sm">.jpg</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Letters, numbers, hyphens, underscores only</p>
                </div>

                <!-- Alt Text -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Alt Text</h4>
                    <input type="text" id="altTextInput"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500"
                           placeholder="Describe the image for accessibility">
                </div>

                <!-- Quality -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Quality</h4>
                    <div class="flex items-center gap-3">
                        <input type="range" id="qualitySlider" min="10" max="100" value="80"
                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <span id="qualityValue" class="text-sm font-medium w-12 text-right">80%</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Lower = smaller file, reduced quality</p>
                </div>

                <!-- Resize -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Resize</h4>
                    <div class="flex items-center gap-2">
                        <input type="number" id="resizeWidth"
                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500"
                               placeholder="Width">
                        <span class="text-gray-400">x</span>
                        <input type="number" id="resizeHeight"
                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500"
                               placeholder="Height">
                        <button id="lockAspectBtn" onclick="toggleAspectLock()"
                                class="p-2 border border-gray-300 rounded-lg hover:bg-gray-100" title="Lock aspect ratio">
                            <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Crop -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Crop</h4>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <button onclick="setCropRatio('free')" class="crop-ratio-btn px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100" data-ratio="free">Free</button>
                        <button onclick="setCropRatio('1:1')" class="crop-ratio-btn px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100" data-ratio="1:1">1:1</button>
                        <button onclick="setCropRatio('16:9')" class="crop-ratio-btn px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100" data-ratio="16:9">16:9</button>
                        <button onclick="setCropRatio('4:3')" class="crop-ratio-btn px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100" data-ratio="4:3">4:3</button>
                        <button onclick="setCropRatio('3:2')" class="crop-ratio-btn px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100" data-ratio="3:2">3:2</button>
                        <button onclick="setCropRatio('2:3')" class="crop-ratio-btn px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-100" data-ratio="2:3">2:3</button>
                    </div>
                    <button id="toggleCropBtn" onclick="toggleCropMode()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 text-sm">
                        Enable Crop Mode
                    </button>
                    <div id="cropInfo" class="hidden text-xs text-gray-500 mt-2">
                        <p>Crop: <span id="cropDimensions">-</span></p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t space-y-2">
                    <button onclick="saveImageChanges()"
                            class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-medium">
                        Save Changes
                    </button>
                    <button onclick="closeImageEditor()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentImageId = null;
let currentImageData = null;
let aspectLocked = true;
let aspectRatio = 1;
let cropMode = false;
let cropRatio = 'free';
let cropSelection = { x: 0, y: 0, width: 200, height: 150 };
let imageDisplayScale = 1;
const csrfToken = '<?= $csrfToken ?>';

async function openImageEditor(imageId) {
    currentImageId = imageId;
    document.getElementById('imageEditorModal').classList.remove('hidden');
    document.getElementById('imageEditorModal').classList.add('flex');
    document.getElementById('editorStatus').textContent = 'Loading image...';

    try {
        const response = await fetch('/admin/api/edit-image.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ csrf_token: csrfToken, action: 'get_info', image_id: imageId })
        });
        const data = await response.json();
        if (data.success) {
            currentImageData = data;
            populateEditorFields(data);
            document.getElementById('editorStatus').textContent = '';
        } else {
            document.getElementById('editorStatus').textContent = 'Error: ' + data.error;
        }
    } catch (error) {
        document.getElementById('editorStatus').textContent = 'Failed to load image data';
    }
}

function populateEditorFields(data) {
    document.getElementById('editorPreview').src = data.url + '?t=' + Date.now();
    document.getElementById('infoDimensions').textContent = data.width + ' x ' + data.height + ' px';
    document.getElementById('infoFileSize').textContent = data.file_size_formatted;
    document.getElementById('infoFormat').textContent = data.mime_type;
    const nameParts = data.filename.split('.');
    const ext = nameParts.pop();
    document.getElementById('renameInput').value = nameParts.join('.');
    document.getElementById('fileExtension').textContent = '.' + ext;
    document.getElementById('altTextInput').value = data.alt_text || '';
    document.getElementById('resizeWidth').value = data.width;
    document.getElementById('resizeHeight').value = data.height;
    aspectRatio = data.width / data.height;
    document.getElementById('qualitySlider').value = 80;
    document.getElementById('qualityValue').textContent = '80%';
    cropMode = false;
    document.getElementById('toggleCropBtn').textContent = 'Enable Crop Mode';
    document.getElementById('toggleCropBtn').classList.remove('bg-orange-500', 'text-white');
    document.getElementById('cropOverlay').classList.add('hidden');
    document.getElementById('cropInfo').classList.add('hidden');
    document.querySelectorAll('.crop-ratio-btn').forEach(btn => btn.classList.remove('bg-orange-500', 'text-white', 'border-orange-500'));
}

function closeImageEditor() {
    document.getElementById('imageEditorModal').classList.add('hidden');
    document.getElementById('imageEditorModal').classList.remove('flex');
    currentImageId = null;
    currentImageData = null;
    cropMode = false;
}

function toggleAspectLock() {
    aspectLocked = !aspectLocked;
    const svg = document.getElementById('lockAspectBtn').querySelector('svg');
    if (aspectLocked) {
        svg.innerHTML = '<path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>';
        svg.classList.add('text-orange-500');
        svg.classList.remove('text-gray-400');
    } else {
        svg.innerHTML = '<path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z"/>';
        svg.classList.remove('text-orange-500');
        svg.classList.add('text-gray-400');
    }
}

document.getElementById('resizeWidth').addEventListener('input', function() {
    if (aspectLocked && currentImageData) {
        document.getElementById('resizeHeight').value = Math.round((parseInt(this.value) || 0) / aspectRatio);
    }
});

document.getElementById('resizeHeight').addEventListener('input', function() {
    if (aspectLocked && currentImageData) {
        document.getElementById('resizeWidth').value = Math.round((parseInt(this.value) || 0) * aspectRatio);
    }
});

document.getElementById('qualitySlider').addEventListener('input', function() {
    document.getElementById('qualityValue').textContent = this.value + '%';
});

function setCropRatio(ratio) {
    cropRatio = ratio;
    document.querySelectorAll('.crop-ratio-btn').forEach(btn => {
        if (btn.dataset.ratio === ratio) btn.classList.add('bg-orange-500', 'text-white', 'border-orange-500');
        else btn.classList.remove('bg-orange-500', 'text-white', 'border-orange-500');
    });
    if (cropMode) adjustCropToRatio();
}

function toggleCropMode() {
    cropMode = !cropMode;
    const btn = document.getElementById('toggleCropBtn');
    if (cropMode) {
        btn.textContent = 'Disable Crop Mode';
        btn.classList.add('bg-orange-500', 'text-white');
        document.getElementById('cropOverlay').classList.remove('hidden');
        document.getElementById('cropInfo').classList.remove('hidden');
        initCropSelection();
    } else {
        btn.textContent = 'Enable Crop Mode';
        btn.classList.remove('bg-orange-500', 'text-white');
        document.getElementById('cropOverlay').classList.add('hidden');
        document.getElementById('cropInfo').classList.add('hidden');
    }
}

function initCropSelection() {
    const preview = document.getElementById('editorPreview');
    const overlay = document.getElementById('cropOverlay');
    if (!preview.complete) { preview.onload = initCropSelection; return; }
    imageDisplayScale = preview.naturalWidth / preview.clientWidth;
    overlay.style.width = preview.clientWidth + 'px';
    overlay.style.height = preview.clientHeight + 'px';
    overlay.style.left = preview.offsetLeft + 'px';
    overlay.style.top = preview.offsetTop + 'px';
    const selWidth = Math.min(200, preview.clientWidth * 0.8);
    const selHeight = Math.min(150, preview.clientHeight * 0.8);
    cropSelection = { x: (preview.clientWidth - selWidth) / 2, y: (preview.clientHeight - selHeight) / 2, width: selWidth, height: selHeight };
    adjustCropToRatio();
    updateCropSelectionUI();
    makeDraggable(document.getElementById('cropSelection'), overlay);
}

function adjustCropToRatio() {
    if (cropRatio === 'free') return;
    const [w, h] = cropRatio.split(':').map(Number);
    cropSelection.height = cropSelection.width / (w / h);
    updateCropSelectionUI();
}

function updateCropSelectionUI() {
    const sel = document.getElementById('cropSelection');
    sel.style.left = cropSelection.x + 'px';
    sel.style.top = cropSelection.y + 'px';
    sel.style.width = cropSelection.width + 'px';
    sel.style.height = cropSelection.height + 'px';
    document.getElementById('cropDimensions').textContent = Math.round(cropSelection.width * imageDisplayScale) + ' x ' + Math.round(cropSelection.height * imageDisplayScale) + ' px';
}

function makeDraggable(element, container) {
    let isDragging = false, isResizing = false, currentHandle = null;
    let startX, startY, startLeft, startTop, startWidth, startHeight;
    element.addEventListener('mousedown', function(e) {
        if (e.target.dataset.handle) { isResizing = true; currentHandle = e.target.dataset.handle; }
        else { isDragging = true; }
        startX = e.clientX; startY = e.clientY;
        startLeft = cropSelection.x; startTop = cropSelection.y;
        startWidth = cropSelection.width; startHeight = cropSelection.height;
        e.preventDefault();
    });
    document.addEventListener('mousemove', function(e) {
        if (!isDragging && !isResizing) return;
        const dx = e.clientX - startX, dy = e.clientY - startY;
        const cw = parseFloat(container.style.width), ch = parseFloat(container.style.height);
        if (isDragging) {
            cropSelection.x = Math.max(0, Math.min(startLeft + dx, cw - cropSelection.width));
            cropSelection.y = Math.max(0, Math.min(startTop + dy, ch - cropSelection.height));
        } else if (isResizing) {
            resizeCrop(currentHandle, dx, dy, cw, ch);
        }
        updateCropSelectionUI();
    });
    document.addEventListener('mouseup', function() { isDragging = false; isResizing = false; currentHandle = null; });
}

function resizeCrop(handle, dx, dy, cw, ch) {
    const minSize = 50;
    if (handle === 'se') {
        cropSelection.width = Math.max(minSize, Math.min(startWidth + dx, cw - cropSelection.x));
        if (cropRatio !== 'free') { const [w, h] = cropRatio.split(':').map(Number); cropSelection.height = cropSelection.width / (w / h); }
        else { cropSelection.height = Math.max(minSize, Math.min(startHeight + dy, ch - cropSelection.y)); }
    } else if (handle === 'sw') {
        const newW = Math.max(minSize, startWidth - dx), newX = startLeft + (startWidth - newW);
        if (newX >= 0) { cropSelection.width = newW; cropSelection.x = newX; }
        if (cropRatio !== 'free') { const [w, h] = cropRatio.split(':').map(Number); cropSelection.height = cropSelection.width / (w / h); }
        else { cropSelection.height = Math.max(minSize, Math.min(startHeight + dy, ch - cropSelection.y)); }
    } else if (handle === 'ne') {
        cropSelection.width = Math.max(minSize, Math.min(startWidth + dx, cw - cropSelection.x));
        if (cropRatio !== 'free') { const [w, h] = cropRatio.split(':').map(Number); const newH = cropSelection.width / (w / h), newY = startTop + (startHeight - newH); if (newY >= 0) { cropSelection.height = newH; cropSelection.y = newY; } }
        else { const newH = Math.max(minSize, startHeight - dy), newY = startTop + (startHeight - newH); if (newY >= 0) { cropSelection.height = newH; cropSelection.y = newY; } }
    } else if (handle === 'nw') {
        const newW = Math.max(minSize, startWidth - dx), newX = startLeft + (startWidth - newW);
        if (newX >= 0) { cropSelection.width = newW; cropSelection.x = newX; }
        if (cropRatio !== 'free') { const [w, h] = cropRatio.split(':').map(Number); const newH = cropSelection.width / (w / h), newY = startTop + (startHeight - newH); if (newY >= 0) { cropSelection.height = newH; cropSelection.y = newY; } }
        else { const newH = Math.max(minSize, startHeight - dy), newY = startTop + (startHeight - newH); if (newY >= 0) { cropSelection.height = newH; cropSelection.y = newY; } }
    }
}

async function saveImageChanges() {
    if (!currentImageId || !currentImageData) return;
    const status = document.getElementById('editorStatus');
    status.textContent = 'Saving changes...';
    try {
        const newName = document.getElementById('renameInput').value.trim();
        const ext = document.getElementById('fileExtension').textContent;
        const oldName = currentImageData.filename.split('.').slice(0, -1).join('.');
        if (newName && newName !== oldName) {
            const res = await fetch('/admin/api/edit-image.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ csrf_token: csrfToken, action: 'rename', image_id: currentImageId, new_filename: newName + ext }) });
            const data = await res.json();
            if (!data.success) { status.textContent = 'Error renaming: ' + data.error; return; }
        }
        const altText = document.getElementById('altTextInput').value.trim();
        if (altText !== (currentImageData.alt_text || '')) {
            await fetch('/admin/api/edit-image.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ csrf_token: csrfToken, action: 'update_alt', image_id: currentImageId, alt_text: altText }) });
        }
        const newW = parseInt(document.getElementById('resizeWidth').value) || currentImageData.width;
        const newH = parseInt(document.getElementById('resizeHeight').value) || currentImageData.height;
        const quality = parseInt(document.getElementById('qualitySlider').value);
        if (newW !== currentImageData.width || newH !== currentImageData.height || quality !== 80 || cropMode) {
            const processData = { csrf_token: csrfToken, action: 'process', image_id: currentImageId, quality: quality };
            if ((newW !== currentImageData.width || newH !== currentImageData.height) && !cropMode) { processData.width = newW; processData.height = newH; }
            if (cropMode) {
                processData.crop_x = Math.round(cropSelection.x * imageDisplayScale);
                processData.crop_y = Math.round(cropSelection.y * imageDisplayScale);
                processData.crop_width = Math.round(cropSelection.width * imageDisplayScale);
                processData.crop_height = Math.round(cropSelection.height * imageDisplayScale);
            }
            const res = await fetch('/admin/api/edit-image.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(processData) });
            const data = await res.json();
            if (!data.success) { status.textContent = 'Error processing: ' + data.error; return; }
        }
        status.textContent = 'Changes saved!';
        setTimeout(() => { closeImageEditor(); location.reload(); }, 1000);
    } catch (error) { status.textContent = 'Failed to save changes'; }
}

function copyUrl(url) { navigator.clipboard.writeText(url).then(() => alert('URL copied to clipboard!')); }
</script>

<style>
input[type="range"] { -webkit-appearance: none; appearance: none; background: #e5e7eb; border-radius: 4px; height: 8px; }
input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 18px; height: 18px; border-radius: 50%; background: #e85d04; cursor: pointer; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
input[type="range"]::-moz-range-thumb { width: 18px; height: 18px; border-radius: 50%; background: #e85d04; cursor: pointer; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
#cropSelection { box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5); }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
