<?php
/**
 * Section Editor - Visual page builder for all content types
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

// Get parameters
$pageType = $_GET['type'] ?? 'page'; // page, service, training
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
    'page_header' => [
        'label' => 'Page Header',
        'icon' => 'bookmark',
        'description' => 'Hero section with breadcrumb, title, description, image and CTA buttons',
        'fields' => ['breadcrumb', 'title', 'description', 'image', 'image_position', 'content_width', 'show_cta', 'cta_buttons']
    ],
    'hero' => [
        'label' => 'Hero Section',
        'icon' => 'star',
        'description' => 'Large heading with subtitle, image, and CTA buttons',
        'fields' => ['heading', 'subheading', 'image', 'show_cta', 'cta_buttons', 'content_width']
    ],
    'text' => [
        'label' => 'Text Block',
        'icon' => 'document-text',
        'description' => 'Rich text content with optional heading',
        'fields' => ['heading', 'content']
    ],
    'text_image' => [
        'label' => 'Text + Image',
        'icon' => 'photograph',
        'description' => 'Text content with image on left or right',
        'fields' => ['heading', 'content', 'image', 'image_position']
    ],
    'image' => [
        'label' => 'Image',
        'icon' => 'photograph',
        'description' => 'Full-width or contained image',
        'fields' => ['image', 'alt_text', 'caption']
    ],
    'checklist' => [
        'label' => 'Checklist',
        'icon' => 'check-circle',
        'description' => 'List of items with checkmarks',
        'fields' => ['heading', 'intro', 'items']
    ],
    'process_steps' => [
        'label' => 'Process Steps',
        'icon' => 'view-list',
        'description' => 'Numbered steps with descriptions',
        'fields' => ['heading', 'intro', 'steps']
    ],
    'faq' => [
        'label' => 'FAQ',
        'icon' => 'question-mark-circle',
        'description' => 'Questions and answers',
        'fields' => ['heading', 'items']
    ],
    'benefits' => [
        'label' => 'Benefits/Features',
        'icon' => 'badge-check',
        'description' => 'List of benefits or features',
        'fields' => ['heading', 'items']
    ],
    'stats' => [
        'label' => 'Statistics',
        'icon' => 'chart-bar',
        'description' => 'Numbers with labels',
        'fields' => ['items']
    ],
    'cta' => [
        'label' => 'Call to Action',
        'icon' => 'cursor-click',
        'description' => 'Highlighted call to action block',
        'fields' => ['heading', 'content', 'button_text', 'button_link', 'style']
    ],
    'cards' => [
        'label' => 'Card Grid',
        'icon' => 'view-grid',
        'description' => 'Grid of cards with icons',
        'fields' => ['heading', 'cards']
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
                // Update existing
                $sql = "UPDATE page_sections SET section_type = ?, section_data = ?, sort_order = ?, updated_at = NOW() WHERE id = ? AND page_type = ? AND page_id = ?";
                $result = dbExecute($sql, [$sectionType, $sectionData, $sortOrder, $sectionId, $pageType, $pageId]);
            } else {
                // Insert new
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
    <!-- Add Section Button (Top) -->
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
                <span class="text-gray-400">—</span>
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

        <!-- Section Preview -->
        <div class="p-4 text-sm text-gray-600 bg-gray-50 rounded-b-xl">
            <?php echo renderSectionPreview($section['section_type'], $data); ?>
        </div>

        <!-- Hidden data for editing -->
        <script type="application/json" class="section-data"><?= json_encode($data) ?></script>
    </div>

    <!-- Add Section Button (After each section) -->
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
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800" id="edit-modal-title">Edit Section</h2>
        </div>
        <form id="section-form" onsubmit="saveSection(event)">
            <input type="hidden" name="section_id" id="edit-section-id" value="0">
            <input type="hidden" name="section_type" id="edit-section-type" value="">
            <input type="hidden" name="sort_order" id="edit-sort-order" value="0">

            <!-- Section Type Selector -->
            <div class="px-6 pt-4 pb-2 border-b border-gray-100 bg-gray-50">
                <label class="block text-sm font-medium text-gray-700 mb-2">Section Type</label>
                <div class="flex items-center gap-3">
                    <select id="change-section-type" onchange="handleSectionTypeChange(this.value)" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-white">
                        <?php foreach ($sectionTypes as $typeKey => $typeInfo): ?>
                        <option value="<?= e($typeKey) ?>"><?= e($typeInfo['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span id="type-change-warning" class="text-amber-600 text-sm hidden">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span id="type-warning-text">Some data may be lost</span>
                    </span>
                </div>
            </div>

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

<script>
const csrfToken = '<?= generateCSRFToken() ?>';
const pageType = '<?= $pageType ?>';
const pageId = <?= $pageId ?>;
let insertPosition = 0;

const sectionTypes = <?= json_encode($sectionTypes) ?>;

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

// Store current editing state
let currentEditData = {};
let originalSectionType = '';

function editSection(sectionId) {
    const card = document.querySelector(`[data-section-id="${sectionId}"]`);
    const type = card.dataset.sectionType;
    const data = JSON.parse(card.querySelector('.section-data').textContent);

    // Store for type change handling
    currentEditData = {...data};
    originalSectionType = type;

    document.getElementById('edit-section-id').value = sectionId;
    document.getElementById('edit-section-type').value = type;
    document.getElementById('edit-modal-title').textContent = 'Edit ' + sectionTypes[type].label;
    document.getElementById('change-section-type').value = type;
    document.getElementById('type-change-warning').classList.add('hidden');

    renderSectionForm(type, data);

    document.getElementById('edit-section-modal').classList.remove('hidden');
    document.getElementById('edit-section-modal').classList.add('flex');
}

// Define compatible type groups for data conversion
const typeCompatibility = {
    'checklist': ['benefits'], // items array
    'benefits': ['checklist'], // items array
    'text': ['text_image'], // content field
    'text_image': ['text'], // content field
    'page_header': ['hero'], // similar structure
    'hero': ['page_header'], // similar structure
};

// Data fields that can be mapped between types
const fieldMappings = {
    'checklist_to_benefits': (data) => data, // Same structure
    'benefits_to_checklist': (data) => data, // Same structure
    'text_to_text_image': (data) => ({...data, image: '', image_position: 'right'}),
    'text_image_to_text': (data) => ({heading: data.heading, content: data.content}),
    'page_header_to_hero': (data) => ({
        heading: data.title || '',
        subheading: data.description || '',
        image: data.image || '',
        image_position: data.image_position || 'right',
        content_width: data.content_width || 50,
        show_cta: data.show_cta || false,
        button1_text: data.button1_text || '',
        button1_url: data.button1_url || '',
        button1_newtab: data.button1_newtab || false,
        button2_text: data.button2_text || '',
        button2_url: data.button2_url || '',
        button2_newtab: data.button2_newtab || false,
    }),
    'hero_to_page_header': (data) => ({
        breadcrumb: '',
        title: data.heading || '',
        description: data.subheading || '',
        image: data.image || '',
        image_position: data.image_position || 'right',
        content_width: data.content_width || 50,
        show_cta: data.show_cta || false,
        button1_text: data.button1_text || '',
        button1_url: data.button1_url || '',
        button1_newtab: data.button1_newtab || false,
        button2_text: data.button2_text || '',
        button2_url: data.button2_url || '',
        button2_newtab: data.button2_newtab || false,
    }),
};

function handleSectionTypeChange(newType) {
    const currentType = document.getElementById('edit-section-type').value;
    const warningEl = document.getElementById('type-change-warning');
    const warningText = document.getElementById('type-warning-text');

    if (newType === currentType) {
        warningEl.classList.add('hidden');
        return;
    }

    // Check compatibility
    const compatible = typeCompatibility[currentType]?.includes(newType);
    let convertedData = {};

    if (compatible) {
        // Try to convert data
        const mappingKey = `${currentType}_to_${newType}`;
        if (fieldMappings[mappingKey]) {
            convertedData = fieldMappings[mappingKey](currentEditData);
            warningEl.classList.add('hidden');
        }
    } else {
        // Show warning for incompatible types
        warningEl.classList.remove('hidden');
        warningText.textContent = 'Data will be reset for this type';
        convertedData = {}; // Start fresh
    }

    // Update the hidden type field
    document.getElementById('edit-section-type').value = newType;
    document.getElementById('edit-modal-title').textContent = 'Edit ' + sectionTypes[newType].label;

    // Re-render form with converted/empty data
    renderSectionForm(newType, convertedData);
}

function renderSectionForm(type, data) {
    const container = document.getElementById('section-form-fields');
    let html = '';

    switch (type) {
        case 'page_header':
            html = `
                <div class="field-group">
                    <label class="field-label">Breadcrumb Text</label>
                    <input type="text" name="breadcrumb" value="${escapeHtml(data.breadcrumb || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g., Our Services">
                </div>
                <div class="field-group">
                    <label class="field-label">Page Title</label>
                    <input type="text" name="title" value="${escapeHtml(data.title || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="field-group">
                    <label class="field-label">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Brief description shown below the title">${escapeHtml(data.description || '')}</textarea>
                </div>
                <div class="field-group">
                    <label class="field-label">Image</label>
                    <div class="flex gap-2">
                        <input type="text" name="image" id="page_header_image" value="${escapeHtml(data.image || '')}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="/uploads/image.jpg">
                        <button type="button" onclick="openImagePicker('page_header_image')" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">Browse</button>
                        <button type="button" onclick="document.getElementById('page_header_image').value=''" class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100">Clear</button>
                    </div>
                    ${data.image ? `<img src="${escapeHtml(data.image)}" class="mt-2 h-24 rounded-lg object-cover">` : ''}
                </div>
                <div class="field-group">
                    <label class="field-label">Layout</label>
                    <select name="image_position" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="right" ${data.image_position !== 'left' ? 'selected' : ''}>Image Right</option>
                        <option value="left" ${data.image_position === 'left' ? 'selected' : ''}>Image Left</option>
                        <option value="none" ${data.image_position === 'none' ? 'selected' : ''}>No Image (Text Only)</option>
                    </select>
                </div>
                <div class="field-group">
                    <label class="field-label">Content / Image Width Split</label>
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">Content %</label>
                            <input type="number" name="content_width" id="ph_content_width" value="${data.content_width || 50}" min="20" max="80"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg" onchange="updatePageHeaderWidthSplit()">
                        </div>
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">Image %</label>
                            <input type="number" id="ph_image_width" value="${100 - (data.content_width || 50)}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                        </div>
                    </div>
                </div>
                <div class="field-group border-t pt-4 mt-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="show_cta" id="ph_show_cta" value="1" ${data.show_cta ? 'checked' : ''} onchange="togglePageHeaderCta()">
                        <span class="font-medium">Show CTA Buttons</span>
                    </label>
                </div>
                <div id="ph_cta_fields" class="${data.show_cta ? '' : 'hidden'}">
                    <div class="bg-gray-50 rounded-lg p-4 mt-4">
                        <h4 class="font-medium text-gray-700 mb-3">Primary Button</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="field-label">Button Text</label>
                                <input type="text" name="button1_text" value="${escapeHtml(data.button1_text || 'Get a Free Quote')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="field-label">Link To</label>
                                <select name="button1_url_type" onchange="toggleUrlField('button1')" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="/contact" ${(data.button1_url || '/contact') === '/contact' ? 'selected' : ''}>Contact Page</option>
                                    <option value="/" ${data.button1_url === '/' ? 'selected' : ''}>Home Page</option>
                                    <option value="/services" ${data.button1_url === '/services' ? 'selected' : ''}>Services</option>
                                    <option value="/training" ${data.button1_url === '/training' ? 'selected' : ''}>Training</option>
                                    <option value="/about" ${data.button1_url === '/about' ? 'selected' : ''}>About Us</option>
                                    <option value="tel:" ${(data.button1_url || '').startsWith('tel:') ? 'selected' : ''}>Phone Number</option>
                                    <option value="mailto:" ${(data.button1_url || '').startsWith('mailto:') ? 'selected' : ''}>Email Address</option>
                                    <option value="custom" ${isCustomUrl(data.button1_url) ? 'selected' : ''}>Custom URL</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-3">
                            <div id="button1_custom_wrap" class="${isCustomUrl(data.button1_url) || (data.button1_url || '').startsWith('tel:') || (data.button1_url || '').startsWith('mailto:') ? '' : 'hidden'}">
                                <label class="field-label" id="button1_custom_label">${(data.button1_url || '').startsWith('tel:') ? 'Phone Number' : (data.button1_url || '').startsWith('mailto:') ? 'Email Address' : 'Custom URL'}</label>
                                <input type="text" name="button1_url_custom" id="button1_url_custom" value="${escapeHtml(getCustomUrlValue(data.button1_url))}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="${(data.button1_url || '').startsWith('tel:') ? '01onal 000000' : (data.button1_url || '').startsWith('mailto:') ? 'email@example.com' : 'https://...'}">
                            </div>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 pb-2">
                                    <input type="checkbox" name="button1_newtab" value="1" ${data.button1_newtab ? 'checked' : ''}>
                                    <span class="text-sm text-gray-600">Open in new window</span>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="button1_url" id="button1_url" value="${escapeHtml(data.button1_url || '/contact')}">
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mt-4">
                        <h4 class="font-medium text-gray-700 mb-3">Secondary Button</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="field-label">Button Text</label>
                                <input type="text" name="button2_text" value="${escapeHtml(data.button2_text || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g., Call Us">
                            </div>
                            <div>
                                <label class="field-label">Link To</label>
                                <select name="button2_url_type" onchange="toggleUrlField('button2')" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="" ${!data.button2_url ? 'selected' : ''}>-- Select --</option>
                                    <option value="/contact" ${data.button2_url === '/contact' ? 'selected' : ''}>Contact Page</option>
                                    <option value="/" ${data.button2_url === '/' ? 'selected' : ''}>Home Page</option>
                                    <option value="/services" ${data.button2_url === '/services' ? 'selected' : ''}>Services</option>
                                    <option value="/training" ${data.button2_url === '/training' ? 'selected' : ''}>Training</option>
                                    <option value="/about" ${data.button2_url === '/about' ? 'selected' : ''}>About Us</option>
                                    <option value="tel:" ${(data.button2_url || '').startsWith('tel:') ? 'selected' : ''}>Phone Number</option>
                                    <option value="mailto:" ${(data.button2_url || '').startsWith('mailto:') ? 'selected' : ''}>Email Address</option>
                                    <option value="custom" ${isCustomUrl(data.button2_url) ? 'selected' : ''}>Custom URL</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-3">
                            <div id="button2_custom_wrap" class="${isCustomUrl(data.button2_url) || (data.button2_url || '').startsWith('tel:') || (data.button2_url || '').startsWith('mailto:') ? '' : 'hidden'}">
                                <label class="field-label" id="button2_custom_label">${(data.button2_url || '').startsWith('tel:') ? 'Phone Number' : (data.button2_url || '').startsWith('mailto:') ? 'Email Address' : 'Custom URL'}</label>
                                <input type="text" name="button2_url_custom" id="button2_url_custom" value="${escapeHtml(getCustomUrlValue(data.button2_url))}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="${(data.button2_url || '').startsWith('tel:') ? '01xxx xxxxxx' : (data.button2_url || '').startsWith('mailto:') ? 'email@example.com' : 'https://...'}">
                            </div>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 pb-2">
                                    <input type="checkbox" name="button2_newtab" value="1" ${data.button2_newtab ? 'checked' : ''}>
                                    <span class="text-sm text-gray-600">Open in new window</span>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="button2_url" id="button2_url" value="${escapeHtml(data.button2_url || '')}">
                    </div>
                </div>`;
            break;

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
                <div class="field-group">
                    <label class="field-label">Image URL (optional)</label>
                    <div class="flex gap-2">
                        <input type="text" name="image" value="${escapeHtml(data.image || '')}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="/uploads/image.jpg">
                        <button type="button" onclick="openImagePicker('image')" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">Browse</button>
                    </div>
                </div>
                <div class="field-group">
                    <label class="field-label">Image Position</label>
                    <select name="image_position" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="right" ${data.image_position !== 'left' ? 'selected' : ''}>Image Right</option>
                        <option value="left" ${data.image_position === 'left' ? 'selected' : ''}>Image Left</option>
                    </select>
                </div>
                <div class="field-group">
                    <label class="field-label">Content / Image Width Split</label>
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">Content %</label>
                            <input type="number" name="content_width" id="content_width" value="${data.content_width || 50}" min="20" max="80"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg" onchange="updateWidthSplit()">
                        </div>
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">Image %</label>
                            <input type="number" id="image_width" value="${100 - (data.content_width || 50)}" min="20" max="80"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>
                    </div>
                </div>
                <div class="field-group">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="show_cta" id="show_cta_checkbox" value="1" ${data.show_cta ? 'checked' : ''} onchange="toggleCtaFields()">
                        <span>Show call-to-action buttons</span>
                    </label>
                </div>
                <div id="cta_fields" class="${data.show_cta ? '' : 'hidden'}">
                    <div class="bg-gray-50 rounded-lg p-4 mt-2">
                        <h4 class="font-medium text-gray-700 mb-3">Primary Button</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="field-label">Button Text</label>
                                <input type="text" name="button1_text" value="${escapeHtml(data.button1_text || 'Get a Quote')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="field-label">Link To</label>
                                <select name="hero_button1_url_type" onchange="toggleUrlField('hero_button1')" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="/contact" ${(data.button1_url || '/contact') === '/contact' ? 'selected' : ''}>Contact Page</option>
                                    <option value="/" ${data.button1_url === '/' ? 'selected' : ''}>Home Page</option>
                                    <option value="/services" ${data.button1_url === '/services' ? 'selected' : ''}>Services</option>
                                    <option value="/training" ${data.button1_url === '/training' ? 'selected' : ''}>Training</option>
                                    <option value="/about" ${data.button1_url === '/about' ? 'selected' : ''}>About Us</option>
                                    <option value="tel:" ${(data.button1_url || '').startsWith('tel:') ? 'selected' : ''}>Phone Number</option>
                                    <option value="mailto:" ${(data.button1_url || '').startsWith('mailto:') ? 'selected' : ''}>Email Address</option>
                                    <option value="custom" ${isCustomUrl(data.button1_url) ? 'selected' : ''}>Custom URL</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-3">
                            <div id="hero_button1_custom_wrap" class="${isCustomUrl(data.button1_url) || (data.button1_url || '').startsWith('tel:') || (data.button1_url || '').startsWith('mailto:') ? '' : 'hidden'}">
                                <label class="field-label" id="hero_button1_custom_label">${(data.button1_url || '').startsWith('tel:') ? 'Phone Number' : (data.button1_url || '').startsWith('mailto:') ? 'Email Address' : 'Custom URL'}</label>
                                <input type="text" name="hero_button1_url_custom" id="hero_button1_url_custom" value="${escapeHtml(getCustomUrlValue(data.button1_url))}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 pb-2">
                                    <input type="checkbox" name="button1_newtab" value="1" ${data.button1_newtab ? 'checked' : ''}>
                                    <span class="text-sm text-gray-600">Open in new window</span>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="button1_url" id="hero_button1_url" value="${escapeHtml(data.button1_url || '/contact')}">
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 mt-4">
                        <h4 class="font-medium text-gray-700 mb-3">Secondary Button</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="field-label">Button Text</label>
                                <input type="text" name="button2_text" value="${escapeHtml(data.button2_text || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Leave empty to hide">
                            </div>
                            <div>
                                <label class="field-label">Link To</label>
                                <select name="hero_button2_url_type" onchange="toggleUrlField('hero_button2')" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="" ${!data.button2_url ? 'selected' : ''}>-- Select --</option>
                                    <option value="/contact" ${data.button2_url === '/contact' ? 'selected' : ''}>Contact Page</option>
                                    <option value="/" ${data.button2_url === '/' ? 'selected' : ''}>Home Page</option>
                                    <option value="/services" ${data.button2_url === '/services' ? 'selected' : ''}>Services</option>
                                    <option value="/training" ${data.button2_url === '/training' ? 'selected' : ''}>Training</option>
                                    <option value="/about" ${data.button2_url === '/about' ? 'selected' : ''}>About Us</option>
                                    <option value="tel:" ${(data.button2_url || '').startsWith('tel:') ? 'selected' : ''}>Phone Number</option>
                                    <option value="mailto:" ${(data.button2_url || '').startsWith('mailto:') ? 'selected' : ''}>Email Address</option>
                                    <option value="custom" ${isCustomUrl(data.button2_url) ? 'selected' : ''}>Custom URL</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-3">
                            <div id="hero_button2_custom_wrap" class="${isCustomUrl(data.button2_url) || (data.button2_url || '').startsWith('tel:') || (data.button2_url || '').startsWith('mailto:') ? '' : 'hidden'}">
                                <label class="field-label" id="hero_button2_custom_label">${(data.button2_url || '').startsWith('tel:') ? 'Phone Number' : (data.button2_url || '').startsWith('mailto:') ? 'Email Address' : 'Custom URL'}</label>
                                <input type="text" name="hero_button2_url_custom" id="hero_button2_url_custom" value="${escapeHtml(getCustomUrlValue(data.button2_url))}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 pb-2">
                                    <input type="checkbox" name="button2_newtab" value="1" ${data.button2_newtab ? 'checked' : ''}>
                                    <span class="text-sm text-gray-600">Open in new window</span>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="button2_url" id="hero_button2_url" value="${escapeHtml(data.button2_url || '')}">
                    </div>
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
                <div class="field-group">
                    <label class="field-label">Image</label>
                    <div class="flex gap-2">
                        <input type="text" name="image" value="${escapeHtml(data.image || '')}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg" placeholder="/uploads/image.jpg">
                        <button type="button" onclick="openImagePicker('image')" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">Browse</button>
                    </div>
                </div>
                <div class="field-group">
                    <label class="field-label">Image Position</label>
                    <select name="image_position" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="right" ${data.image_position !== 'left' ? 'selected' : ''}>Right</option>
                        <option value="left" ${data.image_position === 'left' ? 'selected' : ''}>Left</option>
                    </select>
                </div>
                <div class="field-group">
                    <label class="field-label">Content / Image Width Split</label>
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">Content %</label>
                            <input type="number" name="content_width" id="content_width" value="${data.content_width || 50}" min="20" max="80"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg" onchange="updateWidthSplit()">
                        </div>
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">Image %</label>
                            <input type="number" id="image_width" value="${100 - (data.content_width || 50)}" min="20" max="80"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>
                    </div>
                </div>`;
            break;

        case 'image':
            html = `
                <div class="field-group">
                    <label class="field-label">Image URL</label>
                    <input type="text" name="image" value="${escapeHtml(data.image || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="/uploads/image.jpg">
                </div>
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
                                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2">&times;</button>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addListItem('checklist-items')" class="text-orange-500 text-sm mt-2">+ Add Item</button>
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
                                    <button type="button" onclick="this.closest('.step-item').remove()" class="text-red-500 text-sm">Remove</button>
                                </div>
                                <input type="text" name="step_titles[]" value="${escapeHtml(step.title || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2" placeholder="Step title">
                                <textarea name="step_descriptions[]" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Step description">${escapeHtml(step.description || '')}</textarea>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addProcessStep()" class="text-orange-500 text-sm mt-2">+ Add Step</button>
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
                                    <button type="button" onclick="this.closest('.faq-item').remove()" class="text-red-500 text-sm">Remove</button>
                                </div>
                                <input type="text" name="faq_questions[]" value="${escapeHtml(faq.question || '')}" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2" placeholder="Question">
                                <textarea name="faq_answers[]" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Answer">${escapeHtml(faq.answer || '')}</textarea>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addFaqItem()" class="text-orange-500 text-sm mt-2">+ Add FAQ</button>
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
                                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2">&times;</button>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addListItem('benefits-items')" class="text-orange-500 text-sm mt-2">+ Add Benefit</button>
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
                                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2">&times;</button>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addStatItem()" class="text-orange-500 text-sm mt-2">+ Add Statistic</button>
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
                                    <button type="button" onclick="this.closest('.card-item').remove()" class="text-red-500 text-sm">Remove</button>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mb-2">
                                    <input type="text" name="card_icons[]" value="${escapeHtml(card.icon || '')}" class="px-4 py-2 border border-gray-300 rounded-lg" placeholder="Icon (shield, check, etc)">
                                    <input type="text" name="card_titles[]" value="${escapeHtml(card.title || '')}" class="px-4 py-2 border border-gray-300 rounded-lg" placeholder="Title">
                                </div>
                                <textarea name="card_descriptions[]" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Description">${escapeHtml(card.description || '')}</textarea>
                            </div>
                        `).join('')}
                    </div>
                    <button type="button" onclick="addCardItem()" class="text-orange-500 text-sm mt-2">+ Add Card</button>
                </div>`;
            break;
    }

    container.innerHTML = html;
}

function addListItem(containerId) {
    const container = document.getElementById(containerId);
    const count = container.querySelectorAll('.list-item').length;
    const div = document.createElement('div');
    div.className = 'list-item';
    div.innerHTML = `
        <input type="text" name="items[]" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Item ${count+1}">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2">&times;</button>
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
            <button type="button" onclick="this.closest('.step-item').remove()" class="text-red-500 text-sm">Remove</button>
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
            <button type="button" onclick="this.closest('.faq-item').remove()" class="text-red-500 text-sm">Remove</button>
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
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2">&times;</button>
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
            <button type="button" onclick="this.closest('.card-item').remove()" class="text-red-500 text-sm">Remove</button>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-2">
            <input type="text" name="card_icons[]" class="px-4 py-2 border border-gray-300 rounded-lg" placeholder="Icon (shield, check, etc)">
            <input type="text" name="card_titles[]" class="px-4 py-2 border border-gray-300 rounded-lg" placeholder="Title">
        </div>
        <textarea name="card_descriptions[]" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Description"></textarea>
    `;
    container.appendChild(div);
}

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

function toggleCtaFields() {
    const checked = document.getElementById('show_cta_checkbox').checked;
    document.getElementById('cta_fields').classList.toggle('hidden', !checked);
}

function togglePageHeaderCta() {
    const checked = document.getElementById('ph_show_cta').checked;
    document.getElementById('ph_cta_fields').classList.toggle('hidden', !checked);
}

function updateWidthSplit() {
    const contentWidth = parseInt(document.getElementById('content_width').value) || 50;
    document.getElementById('image_width').value = 100 - contentWidth;
}

function updatePageHeaderWidthSplit() {
    const contentWidth = parseInt(document.getElementById('ph_content_width').value) || 50;
    document.getElementById('ph_image_width').value = 100 - contentWidth;
}

// URL selector helper functions
const standardUrls = ['/', '/contact', '/services', '/training', '/about'];

function isCustomUrl(url) {
    if (!url) return false;
    if (url.startsWith('tel:') || url.startsWith('mailto:')) return false;
    return !standardUrls.includes(url);
}

function getCustomUrlValue(url) {
    if (!url) return '';
    if (url.startsWith('tel:')) return url.substring(4);
    if (url.startsWith('mailto:')) return url.substring(7);
    if (isCustomUrl(url)) return url;
    return '';
}

function toggleUrlField(buttonId) {
    const select = document.querySelector(`select[name="${buttonId}_url_type"]`);
    const customWrap = document.getElementById(`${buttonId}_custom_wrap`);
    const customInput = document.getElementById(`${buttonId}_url_custom`);
    const customLabel = document.getElementById(`${buttonId}_custom_label`);
    const hiddenUrl = document.getElementById(`${buttonId}_url`);
    const value = select.value;

    if (value === 'tel:') {
        customWrap.classList.remove('hidden');
        customLabel.textContent = 'Phone Number';
        customInput.placeholder = '01onal 000000';
        updateButtonUrl(buttonId);
    } else if (value === 'mailto:') {
        customWrap.classList.remove('hidden');
        customLabel.textContent = 'Email Address';
        customInput.placeholder = 'email@example.com';
        updateButtonUrl(buttonId);
    } else if (value === 'custom') {
        customWrap.classList.remove('hidden');
        customLabel.textContent = 'Custom URL';
        customInput.placeholder = 'https://...';
        updateButtonUrl(buttonId);
    } else {
        customWrap.classList.add('hidden');
        hiddenUrl.value = value;
    }
}

function updateButtonUrl(buttonId) {
    const select = document.querySelector(`select[name="${buttonId}_url_type"]`);
    const customInput = document.getElementById(`${buttonId}_url_custom`);
    const hiddenUrl = document.getElementById(`${buttonId}_url`);
    const type = select.value;
    const customValue = customInput.value.trim();

    if (type === 'tel:') {
        hiddenUrl.value = 'tel:' + customValue.replace(/\s+/g, '');
    } else if (type === 'mailto:') {
        hiddenUrl.value = 'mailto:' + customValue;
    } else if (type === 'custom') {
        hiddenUrl.value = customValue;
    } else {
        hiddenUrl.value = type;
    }
}

// Add input listeners for custom URL fields
document.addEventListener('input', function(e) {
    if (e.target.name === 'button1_url_custom') {
        updateButtonUrl('button1');
    } else if (e.target.name === 'button2_url_custom') {
        updateButtonUrl('button2');
    } else if (e.target.name === 'hero_button1_url_custom') {
        updateButtonUrl('hero_button1');
    } else if (e.target.name === 'hero_button2_url_custom') {
        updateButtonUrl('hero_button2');
    }
});

function collectSectionData(type, form) {
    const data = {};
    const formData = new FormData(form);

    switch (type) {
        case 'page_header':
            data.breadcrumb = formData.get('breadcrumb');
            data.title = formData.get('title');
            data.description = formData.get('description');
            data.image = formData.get('image');
            data.image_position = formData.get('image_position');
            data.content_width = parseInt(formData.get('content_width')) || 50;
            data.show_cta = formData.get('show_cta') === '1';
            data.button1_text = formData.get('button1_text');
            data.button1_url = formData.get('button1_url');
            data.button1_newtab = formData.get('button1_newtab') === '1';
            data.button2_text = formData.get('button2_text');
            data.button2_url = formData.get('button2_url');
            data.button2_newtab = formData.get('button2_newtab') === '1';
            break;
        case 'hero':
            data.heading = formData.get('heading');
            data.subheading = formData.get('subheading');
            data.image = formData.get('image');
            data.image_position = formData.get('image_position');
            data.content_width = parseInt(formData.get('content_width')) || 50;
            data.show_cta = formData.get('show_cta') === '1';
            data.button1_text = formData.get('button1_text');
            data.button1_url = formData.get('button1_url');
            data.button1_newtab = formData.get('button1_newtab') === '1';
            data.button2_text = formData.get('button2_text');
            data.button2_url = formData.get('button2_url');
            data.button2_newtab = formData.get('button2_newtab') === '1';
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
            data.content_width = parseInt(formData.get('content_width')) || 50;
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

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Drag and drop reordering with visual indicator
let draggedItem = null;

// Add CSS for drop indicator
const dragStyles = document.createElement('style');
dragStyles.textContent = `
    .section-card.dragging {
        opacity: 0.4;
        background: #fef3c7;
    }
    .drop-indicator {
        height: 6px;
        background: linear-gradient(90deg, #e85d04, #f97316);
        border-radius: 3px;
        margin: 4px 0;
        box-shadow: 0 0 10px rgba(232, 93, 4, 0.6);
        position: relative;
    }
    .drop-indicator::before,
    .drop-indicator::after {
        content: '';
        position: absolute;
        width: 14px;
        height: 14px;
        background: #e85d04;
        border: 3px solid white;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .drop-indicator::before { left: -7px; }
    .drop-indicator::after { right: -7px; }
`;
document.head.appendChild(dragStyles);

// Create drop indicator element
const dropIndicator = document.createElement('div');
dropIndicator.className = 'drop-indicator';

document.querySelectorAll('.section-card').forEach(card => {
    const handle = card.querySelector('.drag-handle');

    handle.addEventListener('mousedown', () => {
        card.draggable = true;
    });

    card.addEventListener('dragstart', (e) => {
        draggedItem = card;
        setTimeout(() => card.classList.add('dragging'), 0);
        // Hide add-section buttons while dragging
        document.querySelectorAll('.add-section-btn').forEach(btn => btn.style.visibility = 'hidden');
    });

    card.addEventListener('dragend', () => {
        card.classList.remove('dragging');
        card.draggable = false;
        // Remove drop indicator
        if (dropIndicator.parentNode) {
            dropIndicator.remove();
        }
        // Show add-section buttons
        document.querySelectorAll('.add-section-btn').forEach(btn => btn.style.visibility = '');
        saveOrder();
    });

    card.addEventListener('dragover', (e) => {
        e.preventDefault();
        if (draggedItem && draggedItem !== card) {
            const rect = card.getBoundingClientRect();
            const midY = rect.top + rect.height / 2;

            // Remove indicator first
            if (dropIndicator.parentNode) {
                dropIndicator.remove();
            }

            // Show indicator and move item
            if (e.clientY < midY) {
                card.parentNode.insertBefore(dropIndicator, card);
                card.parentNode.insertBefore(draggedItem, card);
            } else {
                card.parentNode.insertBefore(dropIndicator, card.nextSibling);
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

    // Reload to fix add-section button positions
    window.location.reload();
}
</script>

<?php
// Helper function to render section preview
function renderSectionPreview($type, $data) {
    switch ($type) {
        case 'page_header':
            $breadcrumb = !empty($data['breadcrumb']) ? '<span class="text-orange-500">' . e($data['breadcrumb']) . '</span> &raquo; ' : '';
            $img = !empty($data['image']) ? ' <span class="text-gray-400">[Image]</span>' : '';
            return $breadcrumb . '<strong>' . e($data['title'] ?? 'Page Header') . '</strong>' . $img . '<br><span class="text-gray-500 text-sm">' . sectionTruncate($data['description'] ?? '', 80) . '</span>';
        case 'hero':
            return '<strong>' . e($data['heading'] ?? 'Hero Section') . '</strong><br>' .
                   sectionTruncate($data['subheading'] ?? '', 100);
        case 'text':
            return sectionTruncate(strip_tags($data['content'] ?? ''), 150);
        case 'text_image':
            return '<strong>' . e($data['heading'] ?? '') . '</strong> — Image on ' . ($data['image_position'] ?? 'right');
        case 'image':
            return 'Image: ' . e($data['image'] ?? 'Not set');
        case 'checklist':
            $count = count($data['items'] ?? []);
            return '<strong>' . e($data['heading'] ?? 'Checklist') . '</strong> — ' . $count . ' items';
        case 'process_steps':
            $count = count($data['steps'] ?? []);
            return '<strong>' . e($data['heading'] ?? 'Process') . '</strong> — ' . $count . ' steps';
        case 'faq':
            $count = count($data['items'] ?? []);
            return '<strong>' . e($data['heading'] ?? 'FAQ') . '</strong> — ' . $count . ' questions';
        case 'benefits':
            $count = count($data['items'] ?? []);
            return '<strong>' . e($data['heading'] ?? 'Benefits') . '</strong> — ' . $count . ' items';
        case 'stats':
            $items = $data['items'] ?? [];
            return implode(' | ', array_map(fn($s) => $s['number'] . ' ' . $s['label'], array_slice($items, 0, 3)));
        case 'cta':
            return '<strong>' . e($data['heading'] ?? 'Call to Action') . '</strong>';
        case 'cards':
            $count = count($data['cards'] ?? []);
            return '<strong>' . e($data['heading'] ?? 'Cards') . '</strong> — ' . $count . ' cards';
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
