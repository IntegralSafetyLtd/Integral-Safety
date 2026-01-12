<?php
/**
 * Section Editor - Visual page builder for all content types
 * Enhanced with color pickers, image picker, layout options
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

// Get parameters
$pageType = $_GET['type'] ?? 'page';
$pageId = (int)($_GET['id'] ?? 0);

// Validate page type
if (!in_array($pageType, ['page', 'service', 'training'])) {
    header('Location: /admin/');
    exit;
}

// Get the item being edited
$item = null;
$backUrl = '/admin/pages.php';
switch ($pageType) {
    case 'page':
        $item = dbFetchOne("SELECT * FROM pages WHERE id = ?", [$pageId]);
        $backUrl = '/admin/pages.php';
        break;
    case 'service':
        $item = dbFetchOne("SELECT * FROM services WHERE id = ?", [$pageId]);
        $backUrl = '/admin/services.php';
        break;
    case 'training':
        $item = dbFetchOne("SELECT * FROM training WHERE id = ?", [$pageId]);
        $backUrl = '/admin/training.php';
        break;
}

if (!$item) {
    header('Location: ' . $backUrl);
    exit;
}

// Section type definitions
$sectionTypes = [
    'hero' => [
        'label' => 'Hero Section',
        'icon' => 'star',
        'description' => 'Large heading with subtitle and optional image',
        'fields' => ['heading', 'subheading', 'image', 'show_cta'],
        'supportsLayout' => true
    ],
    'text' => [
        'label' => 'Text Block',
        'icon' => 'document-text',
        'description' => 'Rich text content with optional heading',
        'fields' => ['heading', 'content'],
        'supportsLayout' => false
    ],
    'text_image' => [
        'label' => 'Text + Image',
        'icon' => 'photograph',
        'description' => 'Text content with image on left or right',
        'fields' => ['heading', 'content', 'image', 'image_position'],
        'supportsLayout' => true
    ],
    'image' => [
        'label' => 'Image',
        'icon' => 'photograph',
        'description' => 'Full-width or contained image',
        'fields' => ['image', 'alt_text', 'caption'],
        'supportsLayout' => false
    ],
    'checklist' => [
        'label' => 'Checklist',
        'icon' => 'check-circle',
        'description' => 'List of items with checkmarks',
        'fields' => ['heading', 'intro', 'items'],
        'supportsLayout' => false
    ],
    'process_steps' => [
        'label' => 'Process Steps',
        'icon' => 'view-list',
        'description' => 'Numbered steps with descriptions',
        'fields' => ['heading', 'intro', 'steps'],
        'supportsLayout' => false
    ],
    'faq' => [
        'label' => 'FAQ',
        'icon' => 'question-mark-circle',
        'description' => 'Questions and answers',
        'fields' => ['heading', 'items'],
        'supportsLayout' => false
    ],
    'benefits' => [
        'label' => 'Benefits/Features',
        'icon' => 'badge-check',
        'description' => 'List of benefits or features',
        'fields' => ['heading', 'items'],
        'supportsLayout' => false
    ],
    'stats' => [
        'label' => 'Statistics',
        'icon' => 'chart-bar',
        'description' => 'Numbers with labels',
        'fields' => ['items'],
        'supportsLayout' => false
    ],
    'cta' => [
        'label' => 'Call to Action',
        'icon' => 'cursor-click',
        'description' => 'Highlighted call to action block',
        'fields' => ['heading', 'content', 'button_text', 'button_link', 'style'],
        'supportsLayout' => false
    ],
    'cards' => [
        'label' => 'Card Grid',
        'icon' => 'view-grid',
        'description' => 'Grid of cards with icons',
        'fields' => ['heading', 'cards'],
        'supportsLayout' => false
    ],
];

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');

    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'error' => 'Invalid token']);
        exit;
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'save_section':
            $sectionId = (int)($_POST['section_id'] ?? 0);
            $sectionType = sanitize($_POST['section_type'] ?? '');
            $sectionData = $_POST['section_data'] ?? '{}';
            $sortOrder = (int)($_POST['sort_order'] ?? 0);

            if ($sectionId > 0) {
                $sql = "UPDATE page_sections SET section_type = ?, section_data = ?, sort_order = ?, updated_at = NOW() WHERE id = ? AND page_type = ? AND page_id = ?";
                $result = dbExecute($sql, [$sectionType, $sectionData, $sortOrder, $sectionId, $pageType, $pageId]);
            } else {
                $sql = "INSERT INTO page_sections (page_type, page_id, section_type, section_data, sort_order) VALUES (?, ?, ?, ?, ?)";
                $result = dbExecute($sql, [$pageType, $pageId, $sectionType, $sectionData, $sortOrder]);
                $sectionId = db()->lastInsertId();
            }

            echo json_encode(['success' => (bool)$result, 'section_id' => $sectionId]);
            exit;

        case 'delete_section':
            $sectionId = (int)($_POST['section_id'] ?? 0);
            $result = dbExecute("DELETE FROM page_sections WHERE id = ? AND page_type = ? AND page_id = ?", [$sectionId, $pageType, $pageId]);
            echo json_encode(['success' => (bool)$result]);
            exit;

        case 'reorder_sections':
            $order = json_decode($_POST['order'] ?? '[]', true);
            foreach ($order as $index => $sectionId) {
                dbExecute("UPDATE page_sections SET sort_order = ? WHERE id = ? AND page_type = ? AND page_id = ?",
                    [$index, (int)$sectionId, $pageType, $pageId]);
            }
            echo json_encode(['success' => true]);
            exit;
    }

    echo json_encode(['success' => false, 'error' => 'Unknown action']);
    exit;
}

// Get existing sections
$sections = dbFetchAll(
    "SELECT * FROM page_sections WHERE page_type = ? AND page_id = ? ORDER BY sort_order ASC",
    [$pageType, $pageId]
);

// Get site colors for JavaScript
$siteColors = defined('SITE_COLORS') ? SITE_COLORS : [
    'navy-800' => '#132337',
    'navy-900' => '#0c1929',
    'orange-500' => '#e85d04',
    'white' => '#ffffff',
    'transparent' => 'transparent'
];

require_once __DIR__ . '/includes/header.php';
?>

<style>
.section-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    margin-bottom: 8px;
    transition: all 0.2s;
}
.section-card:hover {
    border-color: #f97316;
}
.section-card.dragging {
    opacity: 0.5;
    border-color: #f97316;
}
.add-section-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    color: #6b7280;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.2s;
    margin: 8px 0;
}
.add-section-btn:hover {
    border-color: #f97316;
    color: #f97316;
    background: #fff7ed;
}
.section-type-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}
.section-type-option {
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s;
}
.section-type-option:hover {
    border-color: #f97316;
    background: #fff7ed;
}
.field-group {
    margin-bottom: 16px;
}
.field-label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}
.list-item {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
    align-items: flex-start;
}
.list-item input, .list-item textarea {
    flex: 1;
}
.drag-handle {
    cursor: grab;
    padding: 8px;
    color: #9ca3af;
}
.drag-handle:hover {
    color: #6b7280;
}

/* Color Picker Styles */
.color-picker-wrapper {
    margin-top: 4px;
}
.color-presets {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 8px;
}
.color-preset {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 2px solid #d1d5db;
    cursor: pointer;
    transition: all 0.15s;
}
.color-preset:hover {
    transform: scale(1.1);
}
.color-preset.selected {
    border-color: #f97316;
    box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);
}
.color-input-row {
    display: flex;
    gap: 8px;
    align-items: center;
}
.color-input-row input[type="color"] {
    width: 40px;
    height: 36px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    cursor: pointer;
    padding: 2px;
}
.color-input-row input[type="text"] {
    flex: 1;
    font-family: monospace;
}

/* Image Picker Button */
.image-input-wrapper {
    display: flex;
    gap: 8px;
}
.image-input-wrapper input {
    flex: 1;
}
.image-preview-small {
    width: 60px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #d1d5db;
}

/* Layout Options */
.layout-options {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.layout-option {
    padding: 8px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s;
    font-size: 13px;
}
.layout-option:hover {
    border-color: #9ca3af;
}
.layout-option.selected {
    border-color: #f97316;
    background: #fff7ed;
}

/* Styling Section */
.styling-section {
    border-top: 2px solid #e5e7eb;
    margin-top: 24px;
    padding-top: 24px;
}
.styling-section-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Opacity Slider */
.opacity-slider-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}
.opacity-slider-wrapper input[type="range"] {
    flex: 1;
    height: 6px;
    -webkit-appearance: none;
    background: #e5e7eb;
    border-radius: 3px;
}
.opacity-slider-wrapper input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    background: #f97316;
    border-radius: 50%;
    cursor: pointer;
}
.opacity-value {
    min-width: 45px;
    text-align: right;
    font-weight: 500;
}
</style>

<div class="mb-4 flex items-center justify-between">
    <div>
        <a href="<?= e($backUrl) ?>" class="text-orange-500 hover:text-orange-600">&larr; Back to <?= ucfirst($pageType) ?>s</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Edit Sections: <?= e($item['title']) ?></h1>
    </div>
    <a href="/<?= $pageType === 'page' ? ($item['slug'] === 'home' ? '' : $item['slug']) : ($pageType === 'service' ? 'services/' : 'training/') . $item['slug'] ?>"
       target="_blank" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
        Preview
    </a>
</div>

<!-- Sections Container -->
<div id="sections-container">
    <button type="button" class="add-section-btn" onclick="showAddSectionModal(0)">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Add Section
    </button>

    <?php foreach ($sections as $index => $section):
        $data = json_decode($section['section_data'], true) ?: [];
        $typeInfo = $sectionTypes[$section['section_type']] ?? ['label' => $section['section_type']];
    ?>
    <div class="section-card" data-section-id="<?= $section['id'] ?>" data-section-type="<?= e($section['section_type']) ?>">
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="drag-handle" title="Drag to reorder">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                </div>
                <span class="font-semibold text-navy-900"><?= e($typeInfo['label']) ?></span>
                <?php if (!empty($data['heading'])): ?>
                <span class="text-gray-400">-</span>
                <span class="text-gray-500 truncate max-w-xs"><?= e($data['heading']) ?></span>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="editSection(<?= $section['id'] ?>)" class="text-orange-500 hover:text-orange-600 px-3 py-1">
                    Edit
                </button>
                <button type="button" onclick="deleteSection(<?= $section['id'] ?>)" class="text-red-500 hover:text-red-600 px-3 py-1">
                    Delete
                </button>
            </div>
        </div>

        <div class="p-4 text-sm text-gray-600 bg-gray-50 rounded-b-xl">
            <?php echo renderSectionPreview($section['section_type'], $data); ?>
        </div>

        <script type="application/json" class="section-data"><?= json_encode($data) ?></script>
    </div>

    <button type="button" class="add-section-btn" onclick="showAddSectionModal(<?= $index + 1 ?>)">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Add Section
    </button>
    <?php endforeach; ?>

    <?php if (empty($sections)): ?>
    <div class="text-center py-12 text-gray-500">
        <p class="mb-4">No sections yet. Click "Add Section" to start building your page.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Add Section Modal -->
<div id="add-section-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Add Section</h2>
            <p class="text-gray-600">Choose a section type to add</p>
        </div>
        <div class="p-6">
            <div class="section-type-grid">
                <?php foreach ($sectionTypes as $type => $info): ?>
                <div class="section-type-option" onclick="addSection('<?= $type ?>')">
                    <div class="text-orange-500 mb-2">
                        <?= getIcon($info['icon'], 'w-8 h-8 mx-auto') ?>
                    </div>
                    <div class="font-semibold text-gray-800"><?= e($info['label']) ?></div>
                    <div class="text-xs text-gray-500 mt-1"><?= e($info['description']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end">
            <button type="button" onclick="closeAddSectionModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div id="edit-section-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800" id="edit-modal-title">Edit Section</h2>
            <button type="button" onclick="closeEditSectionModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="section-form" onsubmit="saveSection(event)">
            <input type="hidden" name="section_id" id="edit-section-id" value="0">
            <input type="hidden" name="section_type" id="edit-section-type" value="">
            <input type="hidden" name="sort_order" id="edit-sort-order" value="0">

            <div class="p-6" id="section-form-fields">
                <!-- Dynamic fields will be inserted here -->
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeEditSectionModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">Save Section</button>
            </div>
        </form>
    </div>
</div>

<!-- Image Picker Modal -->
<div id="image-picker-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-[60]">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Select Image</h2>
            <button type="button" onclick="closeImagePicker()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Upload Section -->
        <div class="p-4 bg-gray-50 border-b">
            <form id="picker-upload-form" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload New Image</label>
                    <input type="file" name="image" accept="image/*" id="picker-file-input"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm">
                </div>
                <button type="submit" class="px-5 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm font-medium">
                    Upload
                </button>
            </form>
            <div id="upload-progress" class="hidden mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="upload-progress-bar" class="bg-orange-500 h-2 rounded-full transition-all" style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Uploading and optimizing...</p>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="p-6 overflow-y-auto flex-1">
            <div id="picker-gallery-grid" class="grid grid-cols-4 md:grid-cols-6 gap-3">
                <!-- Images loaded via AJAX -->
            </div>
        </div>

        <!-- Selected Preview -->
        <div id="picker-preview" class="hidden p-4 border-t bg-gray-50">
            <div class="flex items-center gap-4">
                <img id="picker-preview-img" src="" class="h-16 w-24 object-cover rounded border">
                <div class="flex-1 min-w-0">
                    <div id="picker-preview-name" class="font-medium text-gray-800 truncate"></div>
                    <div id="picker-preview-dims" class="text-sm text-gray-500"></div>
                </div>
                <button type="button" onclick="confirmImageSelection()"
                        class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-medium">
                    Select Image
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = '<?= generateCSRFToken() ?>';
const pageType = '<?= $pageType ?>';
const pageId = <?= $pageId ?>;
let insertPosition = 0;

const sectionTypes = <?= json_encode($sectionTypes) ?>;
const siteColors = <?= json_encode($siteColors) ?>;

// Image Picker State
let imagePickerTargetField = null;
let selectedImage = null;

// ======================
// MODAL FUNCTIONS
// ======================

function showAddSectionModal(position) {
    insertPosition = position;
    document.getElementById('add-section-modal').classList.remove('hidden');
    document.getElementById('add-section-modal').classList.add('flex');
}

function closeAddSectionModal() {
    document.getElementById('add-section-modal').classList.add('hidden');
    document.getElementById('add-section-modal').classList.remove('flex');
}

function closeEditSectionModal() {
    document.getElementById('edit-section-modal').classList.add('hidden');
    document.getElementById('edit-section-modal').classList.remove('flex');
}

function addSection(type) {
    closeAddSectionModal();
    document.getElementById('edit-section-id').value = '0';
    document.getElementById('edit-section-type').value = type;
    document.getElementById('edit-sort-order').value = insertPosition;
    document.getElementById('edit-modal-title').textContent = 'Add ' + sectionTypes[type].label;

    renderSectionForm(type, {});

    document.getElementById('edit-section-modal').classList.remove('hidden');
    document.getElementById('edit-section-modal').classList.add('flex');
}

function editSection(sectionId) {
    const card = document.querySelector(`[data-section-id="${sectionId}"]`);
    const type = card.dataset.sectionType;
    const data = JSON.parse(card.querySelector('.section-data').textContent);

    document.getElementById('edit-section-id').value = sectionId;
    document.getElementById('edit-section-type').value = type;
    document.getElementById('edit-modal-title').textContent = 'Edit ' + sectionTypes[type].label;

    renderSectionForm(type, data);

    document.getElementById('edit-section-modal').classList.remove('hidden');
    document.getElementById('edit-section-modal').classList.add('flex');
}

// ======================
// COLOR PICKER
// ======================

function createColorPicker(name, value, label) {
    const presets = Object.entries(siteColors).map(([colorName, hex]) => {
        const isTransparent = hex === 'transparent';
        const bgStyle = isTransparent
            ? 'background: linear-gradient(45deg, #ccc 25%, transparent 25%, transparent 75%, #ccc 75%), linear-gradient(45deg, #ccc 25%, transparent 25%, transparent 75%, #ccc 75%); background-size: 8px 8px; background-position: 0 0, 4px 4px;'
            : `background: ${hex};`;
        const selected = value === hex ? 'selected' : '';
        return `<button type="button" class="color-preset ${selected}" style="${bgStyle}" data-color="${hex}" title="${colorName}" onclick="selectColorPreset(this, '${name}')"></button>`;
    }).join('');

    const colorValue = value && value !== 'transparent' && /^#[0-9A-Fa-f]{6}$/.test(value) ? value : '#ffffff';

    return `
        <div class="field-group">
            <label class="field-label">${label}</label>
            <div class="color-picker-wrapper" data-picker-name="${name}">
                <div class="color-presets">${presets}</div>
                <div class="color-input-row">
                    <input type="color" class="color-input" value="${colorValue}" onchange="syncColorFromPicker(this, '${name}')">
                    <input type="text" name="${name}" value="${escapeHtml(value || '')}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="#000000 or transparent" oninput="syncColorFromHex(this, '${name}')">
                </div>
            </div>
        </div>
    `;
}

function selectColorPreset(btn, name) {
    const wrapper = btn.closest('.color-picker-wrapper');
    wrapper.querySelectorAll('.color-preset').forEach(p => p.classList.remove('selected'));
    btn.classList.add('selected');

    const color = btn.dataset.color;
    const hexInput = wrapper.querySelector(`input[name="${name}"]`);
    const colorInput = wrapper.querySelector('.color-input');

    hexInput.value = color;
    if (color !== 'transparent' && /^#[0-9A-Fa-f]{6}$/.test(color)) {
        colorInput.value = color;
    }
}

function syncColorFromPicker(input, name) {
    const wrapper = input.closest('.color-picker-wrapper');
    const hexInput = wrapper.querySelector(`input[name="${name}"]`);
    hexInput.value = input.value;
    updatePresetSelection(wrapper, input.value);
}

function syncColorFromHex(input, name) {
    const wrapper = input.closest('.color-picker-wrapper');
    const colorInput = wrapper.querySelector('.color-input');
    const val = input.value;
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
        colorInput.value = val;
    }
    updatePresetSelection(wrapper, val);
}

function updatePresetSelection(wrapper, value) {
    wrapper.querySelectorAll('.color-preset').forEach(p => {
        p.classList.toggle('selected', p.dataset.color === value);
    });
}

// ======================
// IMAGE PICKER
// ======================

function openImagePicker(targetFieldId) {
    imagePickerTargetField = targetFieldId;
    selectedImage = null;
    document.getElementById('picker-preview').classList.add('hidden');
    loadGalleryImages();
    document.getElementById('image-picker-modal').classList.remove('hidden');
    document.getElementById('image-picker-modal').classList.add('flex');
}

function closeImagePicker() {
    document.getElementById('image-picker-modal').classList.add('hidden');
    document.getElementById('image-picker-modal').classList.remove('flex');
}

async function loadGalleryImages() {
    const grid = document.getElementById('picker-gallery-grid');
    grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8">Loading...</div>';

    try {
        const response = await fetch('/admin/api/gallery-images.php?csrf_token=' + encodeURIComponent(csrfToken));
        const data = await response.json();

        if (data.success && data.images.length > 0) {
            grid.innerHTML = data.images.map(img => `
                <div class="picker-image-item cursor-pointer rounded-lg overflow-hidden border-2 border-transparent hover:border-orange-300 transition-colors"
                     data-url="${escapeHtml(img.url)}"
                     data-name="${escapeHtml(img.original_name || img.filename)}"
                     data-width="${img.width || ''}"
                     data-height="${img.height || ''}"
                     onclick="selectPickerImage(this)">
                    <img src="${escapeHtml(img.url)}" class="w-full h-20 object-cover" alt="" loading="lazy">
                </div>
            `).join('');
        } else {
            grid.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8">No images yet. Upload one above.</div>';
        }
    } catch (error) {
        grid.innerHTML = '<div class="col-span-full text-center text-red-500 py-8">Failed to load images</div>';
    }
}

function selectPickerImage(element) {
    document.querySelectorAll('.picker-image-item').forEach(el => {
        el.classList.remove('border-orange-500', 'ring-2', 'ring-orange-200');
        el.classList.add('border-transparent');
    });

    element.classList.remove('border-transparent');
    element.classList.add('border-orange-500', 'ring-2', 'ring-orange-200');

    selectedImage = {
        url: element.dataset.url,
        name: element.dataset.name,
        width: element.dataset.width,
        height: element.dataset.height
    };

    document.getElementById('picker-preview-img').src = selectedImage.url;
    document.getElementById('picker-preview-name').textContent = selectedImage.name;
    document.getElementById('picker-preview-dims').textContent =
        selectedImage.width ? `${selectedImage.width} x ${selectedImage.height}` : '';
    document.getElementById('picker-preview').classList.remove('hidden');
}

function confirmImageSelection() {
    if (selectedImage && imagePickerTargetField) {
        const field = document.getElementById(imagePickerTargetField);
        if (field) {
            field.value = selectedImage.url;
            // Update preview if exists
            const previewId = imagePickerTargetField + '_preview';
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = selectedImage.url;
                preview.classList.remove('hidden');
            }
        }
    }
    closeImagePicker();
}

// Upload handler
document.getElementById('picker-upload-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const fileInput = document.getElementById('picker-file-input');
    if (!fileInput.files.length) {
        alert('Please select a file');
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    formData.append('csrf_token', csrfToken);

    const progressBar = document.getElementById('upload-progress');
    const progressFill = document.getElementById('upload-progress-bar');
    progressBar.classList.remove('hidden');

    try {
        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percent = (e.loaded / e.total) * 100;
                progressFill.style.width = percent + '%';
            }
        });

        xhr.onload = function() {
            progressBar.classList.add('hidden');
            progressFill.style.width = '0%';

            try {
                const result = JSON.parse(xhr.responseText);
                if (result.success) {
                    loadGalleryImages().then(() => {
                        setTimeout(() => {
                            const newImage = document.querySelector(`[data-url="${result.url}"]`);
                            if (newImage) selectPickerImage(newImage);
                        }, 100);
                    });
                    fileInput.value = '';
                } else {
                    alert('Upload failed: ' + (result.error || 'Unknown error'));
                }
            } catch (err) {
                alert('Upload failed: Invalid response');
            }
        };

        xhr.onerror = function() {
            progressBar.classList.add('hidden');
            alert('Upload failed: Network error');
        };

        xhr.open('POST', '/admin/api/upload-image.php');
        xhr.send(formData);

    } catch (error) {
        progressBar.classList.add('hidden');
        alert('Upload failed: ' + error.message);
    }
});

// ======================
// IMAGE FIELD HELPER
// ======================

function createImageField(name, value, label) {
    const previewClass = value ? '' : 'hidden';
    return `
        <div class="field-group">
            <label class="field-label">${label}</label>
            <div class="image-input-wrapper">
                <input type="text" name="${name}" id="${name}_field" value="${escapeHtml(value || '')}"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="/uploads/image.jpg">
                <button type="button" onclick="openImagePicker('${name}_field')"
                        class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 text-sm">
                    Browse
                </button>
                ${value ? `<img id="${name}_field_preview" src="${escapeHtml(value)}" class="image-preview-small ${previewClass}">` : `<img id="${name}_field_preview" src="" class="image-preview-small hidden">`}
            </div>
        </div>
    `;
}

// ======================
// STYLING SECTION
// ======================

function getStylingFieldsHtml(data) {
    return `
        <div class="styling-section">
            <div class="styling-section-title">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                Section Styling
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                ${createColorPicker('bg_color', data.bg_color || '', 'Background Color')}
                ${createColorPicker('text_color', data.text_color || '', 'Text Color')}
                ${createColorPicker('heading_color', data.heading_color || '', 'Heading Color')}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                ${createImageField('bg_image', data.bg_image || '', 'Background Image')}

                <div class="field-group">
                    <label class="field-label">Background Image Opacity: <span id="bg_opacity_value">${data.bg_opacity || 100}%</span></label>
                    <div class="opacity-slider-wrapper">
                        <input type="range" name="bg_opacity" min="0" max="100" value="${data.bg_opacity || 100}"
                               oninput="document.getElementById('bg_opacity_value').textContent = this.value + '%'">
                        <span class="opacity-value">${data.bg_opacity || 100}%</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Lower = more overlay color visible over image</p>
                </div>
            </div>
        </div>
    `;
}

// ======================
// LAYOUT OPTIONS
// ======================

function getLayoutFieldHtml(data, sectionType) {
    if (!sectionTypes[sectionType]?.supportsLayout) return '';

    const layouts = {
        'text-only': 'Text Only',
        'image-left': 'Image Left',
        'image-right': 'Image Right',
        'image-top': 'Image Top',
        'full-width': 'Full Width'
    };

    const currentLayout = data.layout_type || 'image-right';

    return `
        <div class="field-group">
            <label class="field-label">Layout</label>
            <div class="layout-options">
                ${Object.entries(layouts).map(([value, label]) => `
                    <label class="layout-option ${currentLayout === value ? 'selected' : ''}">
                        <input type="radio" name="layout_type" value="${value}" ${currentLayout === value ? 'checked' : ''} class="sr-only" onchange="updateLayoutSelection(this)">
                        ${label}
                    </label>
                `).join('')}
            </div>
        </div>
    `;
}

function updateLayoutSelection(input) {
    const wrapper = input.closest('.layout-options');
    wrapper.querySelectorAll('.layout-option').forEach(opt => opt.classList.remove('selected'));
    input.closest('.layout-option').classList.add('selected');
}

// ======================
// RENDER SECTION FORM
// ======================

function renderSectionForm(type, data) {
    const container = document.getElementById('section-form-fields');
    let html = '';

    // Content fields based on type
    switch (type) {
        case 'hero':
            html = `
                <div class="field-group">
                    <label class="field-label">Heading</label>
                    <input type="text" name="heading" value="${escapeHtml(data.heading || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">Subheading</label>
                    <textarea name="subheading" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">${escapeHtml(data.subheading || '')}</textarea>
                </div>
                ${getLayoutFieldHtml(data, type)}
                ${createImageField('image', data.image || '', 'Hero Image')}
                <div class="field-group">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="show_cta" value="1" ${data.show_cta ? 'checked' : ''} class="rounded border-gray-300">
                        <span>Show call-to-action buttons</span>
                    </label>
                </div>`;
            break;

        case 'text':
            html = `
                <div class="field-group">
                    <label class="field-label">Heading (optional)</label>
                    <input type="text" name="heading" value="${escapeHtml(data.heading || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">Content</label>
                    <textarea name="content" rows="8" class="w-full px-4 py-2 border border-gray-300 rounded-lg">${escapeHtml(data.content || '')}</textarea>
                </div>`;
            break;

        case 'text_image':
            html = `
                <div class="field-group">
                    <label class="field-label">Heading</label>
                    <input type="text" name="heading" value="${escapeHtml(data.heading || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">Content</label>
                    <textarea name="content" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg">${escapeHtml(data.content || '')}</textarea>
                </div>
                ${getLayoutFieldHtml(data, type)}
                ${createImageField('image', data.image || '', 'Image')}`;
            break;

        case 'image':
            html = `
                ${createImageField('image', data.image || '', 'Image')}
                <div class="field-group">
                    <label class="field-label">Alt Text</label>
                    <input type="text" name="alt_text" value="${escapeHtml(data.alt_text || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">Caption (optional)</label>
                    <input type="text" name="caption" value="${escapeHtml(data.caption || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>`;
            break;

        case 'checklist':
            const checklistItems = data.items || [''];
            html = `
                <div class="field-group">
                    <label class="field-label">Heading</label>
                    <input type="text" name="heading" value="${escapeHtml(data.heading || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">Introduction (optional)</label>
                    <textarea name="intro" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg">${escapeHtml(data.intro || '')}</textarea>
                </div>
                <div class="field-group">
                    <label class="field-label">Items</label>
                    <div id="checklist-items">
                        ${checklistItems.map((item, i) => `
                            <div class="list-item">
                                <input type="text" name="items[]" value="${escapeHtml(item)}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Item ${i+1}">
                                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2 hover:text-red-700">&times;</button>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addListItem('checklist-items')" class="text-orange-500 text-sm mt-2 hover:text-orange-600">+ Add Item</button>
                </div>`;
            break;

        case 'process_steps':
            const steps = data.steps || [{title: '', description: ''}];
            html = `
                <div class="field-group">
                    <label class="field-label">Heading</label>
                    <input type="text" name="heading" value="${escapeHtml(data.heading || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">Introduction (optional)</label>
                    <textarea name="intro" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg">${escapeHtml(data.intro || '')}</textarea>
                </div>
                <div class="field-group">
                    <label class="field-label">Steps</label>
                    <div id="process-steps">
                        ${steps.map((step, i) => `
                            <div class="step-item bg-gray-50 p-4 rounded-lg mb-3">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold text-gray-700">Step ${i+1}</span>
                                    <button type="button" onclick="this.closest('.step-item').remove()" class="text-red-500 text-sm hover:text-red-700">Remove</button>
                                </div>
                                <input type="text" name="step_titles[]" value="${escapeHtml(step.title || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2" placeholder="Step title">
                                <textarea name="step_descriptions[]" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Step description">${escapeHtml(step.description || '')}</textarea>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addProcessStep()" class="text-orange-500 text-sm mt-2 hover:text-orange-600">+ Add Step</button>
                </div>`;
            break;

        case 'faq':
            const faqs = data.items || [{question: '', answer: ''}];
            html = `
                <div class="field-group">
                    <label class="field-label">Heading</label>
                    <input type="text" name="heading" value="${escapeHtml(data.heading || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">FAQ Items</label>
                    <div id="faq-items">
                        ${faqs.map((faq, i) => `
                            <div class="faq-item bg-gray-50 p-4 rounded-lg mb-3">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold text-gray-700">FAQ ${i+1}</span>
                                    <button type="button" onclick="this.closest('.faq-item').remove()" class="text-red-500 text-sm hover:text-red-700">Remove</button>
                                </div>
                                <input type="text" name="faq_questions[]" value="${escapeHtml(faq.question || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2" placeholder="Question">
                                <textarea name="faq_answers[]" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Answer">${escapeHtml(faq.answer || '')}</textarea>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addFaqItem()" class="text-orange-500 text-sm mt-2 hover:text-orange-600">+ Add FAQ</button>
                </div>`;
            break;

        case 'benefits':
            const benefits = data.items || [''];
            html = `
                <div class="field-group">
                    <label class="field-label">Heading</label>
                    <input type="text" name="heading" value="${escapeHtml(data.heading || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">Benefits/Features</label>
                    <div id="benefits-items">
                        ${benefits.map((item, i) => `
                            <div class="list-item">
                                <input type="text" name="items[]" value="${escapeHtml(item)}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Benefit ${i+1}">
                                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2 hover:text-red-700">&times;</button>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addListItem('benefits-items')" class="text-orange-500 text-sm mt-2 hover:text-orange-600">+ Add Benefit</button>
                </div>`;
            break;

        case 'stats':
            const stats = data.items || [{number: '', label: ''}];
            html = `
                <div class="field-group">
                    <label class="field-label">Statistics</label>
                    <div id="stats-items">
                        ${stats.map((stat, i) => `
                            <div class="stat-item flex gap-3 mb-3">
                                <input type="text" name="stat_numbers[]" value="${escapeHtml(stat.number || '')}" class="w-24 px-4 py-2 border border-gray-300 rounded-lg" placeholder="20+">
                                <input type="text" name="stat_labels[]" value="${escapeHtml(stat.label || '')}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="Years Experience">
                                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2 hover:text-red-700">&times;</button>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addStatItem()" class="text-orange-500 text-sm mt-2 hover:text-orange-600">+ Add Statistic</button>
                </div>`;
            break;

        case 'cta':
            html = `
                <div class="field-group">
                    <label class="field-label">Heading</label>
                    <input type="text" name="heading" value="${escapeHtml(data.heading || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">Content</label>
                    <textarea name="content" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">${escapeHtml(data.content || '')}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="field-group">
                        <label class="field-label">Button Text</label>
                        <input type="text" name="button_text" value="${escapeHtml(data.button_text || 'Get in Touch')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Button Link</label>
                        <input type="text" name="button_link" value="${escapeHtml(data.button_link || '/contact')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div class="field-group">
                    <label class="field-label">Style</label>
                    <select name="style" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="orange" ${data.style !== 'navy' ? 'selected' : ''}>Orange Background</option>
                        <option value="navy" ${data.style === 'navy' ? 'selected' : ''}>Navy Background</option>
                    </select>
                </div>`;
            break;

        case 'cards':
            const cards = data.cards || [{icon: '', title: '', description: ''}];
            html = `
                <div class="field-group">
                    <label class="field-label">Heading</label>
                    <input type="text" name="heading" value="${escapeHtml(data.heading || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">Cards</label>
                    <div id="cards-items">
                        ${cards.map((card, i) => `
                            <div class="card-item bg-gray-50 p-4 rounded-lg mb-3">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold text-gray-700">Card ${i+1}</span>
                                    <button type="button" onclick="this.closest('.card-item').remove()" class="text-red-500 text-sm hover:text-red-700">Remove</button>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mb-2">
                                    <input type="text" name="card_icons[]" value="${escapeHtml(card.icon || '')}" class="px-4 py-2 border border-gray-300 rounded-lg" placeholder="Icon (shield, check, etc)">
                                    <input type="text" name="card_titles[]" value="${escapeHtml(card.title || '')}" class="px-4 py-2 border border-gray-300 rounded-lg" placeholder="Title">
                                </div>
                                <textarea name="card_descriptions[]" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Description">${escapeHtml(card.description || '')}</textarea>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addCardItem()" class="text-orange-500 text-sm mt-2 hover:text-orange-600">+ Add Card</button>
                </div>`;
            break;
    }

    // Add styling section to all types
    html += getStylingFieldsHtml(data);

    container.innerHTML = html;
}

// ======================
// DYNAMIC LIST HELPERS
// ======================

function addListItem(containerId) {
    const container = document.getElementById(containerId);
    const count = container.querySelectorAll('.list-item').length;
    const div = document.createElement('div');
    div.className = 'list-item';
    div.innerHTML = `
        <input type="text" name="items[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Item ${count+1}">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2 hover:text-red-700">&times;</button>
    `;
    container.appendChild(div);
}

function addProcessStep() {
    const container = document.getElementById('process-steps');
    const count = container.querySelectorAll('.step-item').length;
    const div = document.createElement('div');
    div.className = 'step-item bg-gray-50 p-4 rounded-lg mb-3';
    div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <span class="font-semibold text-gray-700">Step ${count+1}</span>
            <button type="button" onclick="this.closest('.step-item').remove()" class="text-red-500 text-sm hover:text-red-700">Remove</button>
        </div>
        <input type="text" name="step_titles[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2" placeholder="Step title">
        <textarea name="step_descriptions[]" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Step description"></textarea>
    `;
    container.appendChild(div);
}

function addFaqItem() {
    const container = document.getElementById('faq-items');
    const count = container.querySelectorAll('.faq-item').length;
    const div = document.createElement('div');
    div.className = 'faq-item bg-gray-50 p-4 rounded-lg mb-3';
    div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <span class="font-semibold text-gray-700">FAQ ${count+1}</span>
            <button type="button" onclick="this.closest('.faq-item').remove()" class="text-red-500 text-sm hover:text-red-700">Remove</button>
        </div>
        <input type="text" name="faq_questions[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2" placeholder="Question">
        <textarea name="faq_answers[]" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Answer"></textarea>
    `;
    container.appendChild(div);
}

function addStatItem() {
    const container = document.getElementById('stats-items');
    const div = document.createElement('div');
    div.className = 'stat-item flex gap-3 mb-3';
    div.innerHTML = `
        <input type="text" name="stat_numbers[]" class="w-24 px-4 py-2 border border-gray-300 rounded-lg" placeholder="20+">
        <input type="text" name="stat_labels[]" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="Years Experience">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2 hover:text-red-700">&times;</button>
    `;
    container.appendChild(div);
}

function addCardItem() {
    const container = document.getElementById('cards-items');
    const count = container.querySelectorAll('.card-item').length;
    const div = document.createElement('div');
    div.className = 'card-item bg-gray-50 p-4 rounded-lg mb-3';
    div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <span class="font-semibold text-gray-700">Card ${count+1}</span>
            <button type="button" onclick="this.closest('.card-item').remove()" class="text-red-500 text-sm hover:text-red-700">Remove</button>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-2">
            <input type="text" name="card_icons[]" class="px-4 py-2 border border-gray-300 rounded-lg" placeholder="Icon (shield, check, etc)">
            <input type="text" name="card_titles[]" class="px-4 py-2 border border-gray-300 rounded-lg" placeholder="Title">
        </div>
        <textarea name="card_descriptions[]" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Description"></textarea>
    `;
    container.appendChild(div);
}

// ======================
// SAVE SECTION
// ======================

async function saveSection(e) {
    e.preventDefault();
    const form = document.getElementById('section-form');
    const formData = new FormData(form);

    const sectionType = formData.get('section_type');
    const sectionData = collectSectionData(sectionType, form);

    const response = await fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            ajax: '1',
            csrf_token: csrfToken,
            action: 'save_section',
            section_id: formData.get('section_id'),
            section_type: sectionType,
            section_data: JSON.stringify(sectionData),
            sort_order: formData.get('sort_order')
        })
    });

    const result = await response.json();
    if (result.success) {
        location.reload();
    } else {
        alert('Error saving section: ' + (result.error || 'Unknown error'));
    }
}

function collectSectionData(type, form) {
    const data = {};
    const formData = new FormData(form);

    // Collect styling fields (common to all sections)
    data.bg_color = formData.get('bg_color') || '';
    data.text_color = formData.get('text_color') || '';
    data.heading_color = formData.get('heading_color') || '';
    data.bg_image = formData.get('bg_image') || '';
    data.bg_opacity = parseInt(formData.get('bg_opacity')) || 100;
    data.layout_type = formData.get('layout_type') || '';

    // Collect type-specific fields
    switch (type) {
        case 'hero':
            data.heading = formData.get('heading');
            data.subheading = formData.get('subheading');
            data.image = formData.get('image');
            data.show_cta = formData.get('show_cta') === '1';
            break;
        case 'text':
            data.heading = formData.get('heading');
            data.content = formData.get('content');
            break;
        case 'text_image':
            data.heading = formData.get('heading');
            data.content = formData.get('content');
            data.image = formData.get('image');
            data.image_position = formData.get('image_position');
            break;
        case 'image':
            data.image = formData.get('image');
            data.alt_text = formData.get('alt_text');
            data.caption = formData.get('caption');
            break;
        case 'checklist':
        case 'benefits':
            data.heading = formData.get('heading');
            data.intro = formData.get('intro');
            data.items = formData.getAll('items[]').filter(i => i.trim());
            break;
        case 'process_steps':
            data.heading = formData.get('heading');
            data.intro = formData.get('intro');
            const titles = formData.getAll('step_titles[]');
            const descriptions = formData.getAll('step_descriptions[]');
            data.steps = titles.map((t, i) => ({title: t, description: descriptions[i] || ''})).filter(s => s.title.trim());
            break;
        case 'faq':
            data.heading = formData.get('heading');
            const questions = formData.getAll('faq_questions[]');
            const answers = formData.getAll('faq_answers[]');
            data.items = questions.map((q, i) => ({question: q, answer: answers[i] || ''})).filter(f => f.question.trim());
            break;
        case 'stats':
            const numbers = formData.getAll('stat_numbers[]');
            const labels = formData.getAll('stat_labels[]');
            data.items = numbers.map((n, i) => ({number: n, label: labels[i] || ''})).filter(s => s.number.trim());
            break;
        case 'cta':
            data.heading = formData.get('heading');
            data.content = formData.get('content');
            data.button_text = formData.get('button_text');
            data.button_link = formData.get('button_link');
            data.style = formData.get('style');
            break;
        case 'cards':
            data.heading = formData.get('heading');
            const icons = formData.getAll('card_icons[]');
            const cardTitles = formData.getAll('card_titles[]');
            const cardDescs = formData.getAll('card_descriptions[]');
            data.cards = icons.map((ic, i) => ({icon: ic, title: cardTitles[i] || '', description: cardDescs[i] || ''})).filter(c => c.title.trim());
            break;
    }

    return data;
}

// ======================
// DELETE SECTION
// ======================

async function deleteSection(sectionId) {
    if (!confirm('Delete this section?')) return;

    const response = await fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            ajax: '1',
            csrf_token: csrfToken,
            action: 'delete_section',
            section_id: sectionId
        })
    });

    const result = await response.json();
    if (result.success) {
        location.reload();
    }
}

// ======================
// UTILITIES
// ======================

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ======================
// DRAG AND DROP
// ======================

let draggedItem = null;

document.querySelectorAll('.section-card').forEach(card => {
    const handle = card.querySelector('.drag-handle');

    handle.addEventListener('mousedown', () => {
        card.draggable = true;
    });

    card.addEventListener('dragstart', (e) => {
        draggedItem = card;
        card.classList.add('dragging');
    });

    card.addEventListener('dragend', () => {
        card.classList.remove('dragging');
        card.draggable = false;
        saveOrder();
    });

    card.addEventListener('dragover', (e) => {
        e.preventDefault();
        if (draggedItem && draggedItem !== card) {
            const rect = card.getBoundingClientRect();
            const midY = rect.top + rect.height / 2;
            if (e.clientY < midY) {
                card.parentNode.insertBefore(draggedItem, card);
            } else {
                card.parentNode.insertBefore(draggedItem, card.nextSibling);
            }
        }
    });
});

async function saveOrder() {
    const cards = document.querySelectorAll('.section-card');
    const order = Array.from(cards).map(c => c.dataset.sectionId);

    await fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            ajax: '1',
            csrf_token: csrfToken,
            action: 'reorder_sections',
            order: JSON.stringify(order)
        })
    });
}
</script>

<?php
// Helper function to render section preview
function renderSectionPreview($type, $data) {
    switch ($type) {
        case 'hero':
            return '<strong>' . e($data['heading'] ?? 'Hero Section') . '</strong><br>' .
                   sectionTruncate($data['subheading'] ?? '', 100);
        case 'text':
            return sectionTruncate(strip_tags($data['content'] ?? ''), 150);
        case 'text_image':
            $layout = $data['layout_type'] ?? ($data['image_position'] ?? 'right');
            return '<strong>' . e($data['heading'] ?? '') . '</strong> - Layout: ' . $layout;
        case 'image':
            return 'Image: ' . e($data['image'] ?? 'Not set');
        case 'checklist':
            $count = count($data['items'] ?? []);
            return '<strong>' . e($data['heading'] ?? 'Checklist') . '</strong> - ' . $count . ' items';
        case 'process_steps':
            $count = count($data['steps'] ?? []);
            return '<strong>' . e($data['heading'] ?? 'Process') . '</strong> - ' . $count . ' steps';
        case 'faq':
            $count = count($data['items'] ?? []);
            return '<strong>' . e($data['heading'] ?? 'FAQ') . '</strong> - ' . $count . ' questions';
        case 'benefits':
            $count = count($data['items'] ?? []);
            return '<strong>' . e($data['heading'] ?? 'Benefits') . '</strong> - ' . $count . ' items';
        case 'stats':
            $items = $data['items'] ?? [];
            return implode(' | ', array_map(fn($s) => $s['number'] . ' ' . $s['label'], array_slice($items, 0, 3)));
        case 'cta':
            return '<strong>' . e($data['heading'] ?? 'Call to Action') . '</strong>';
        case 'cards':
            $count = count($data['cards'] ?? []);
            return '<strong>' . e($data['heading'] ?? 'Cards') . '</strong> - ' . $count . ' cards';
        default:
            return 'Unknown section type';
    }
}

function sectionTruncate($text, $length) {
    if (strlen($text) <= $length) return e($text);
    return e(substr($text, 0, $length)) . '...';
}
?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
