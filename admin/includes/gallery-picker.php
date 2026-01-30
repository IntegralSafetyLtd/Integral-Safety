<?php
/**
 * Reusable Gallery Picker Modal with Upload Functionality
 * Include this file on any admin page that needs image selection
 *
 * Usage:
 * 1. Include this file: require_once __DIR__ . '/includes/gallery-picker.php';
 * 2. Add a button to open: onclick="openGalleryPicker('field_id')"
 * 3. Optionally add preview div: <div id="field_id_preview"></div>
 *
 * Optional: Set $galleryPickerShowLogos = true before including to show site logos section
 */

// Check if logos should be shown (for SEO page)
$showLogos = isset($galleryPickerShowLogos) && $galleryPickerShowLogos;
?>

<!-- Gallery Picker Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Select or Upload Image</h3>
            <button onclick="closeGalleryPicker()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Upload Zone -->
        <div class="px-6 pt-4">
            <div id="uploadZone"
                 class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-400 transition-colors cursor-pointer"
                 onclick="document.getElementById('uploadInput').click()">
                <input type="file" id="uploadInput" accept="image/*" class="hidden" onchange="handleGalleryUpload(this.files[0])">
                <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-600 font-medium">Drop an image here or click to upload</p>
                <p class="text-gray-400 text-sm mt-1">Images are automatically compressed and optimised</p>
            </div>
            <div id="uploadProgress" class="hidden mt-3">
                <div class="flex items-center gap-3">
                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div id="uploadProgressBar" class="h-full bg-orange-500 transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <span id="uploadProgressText" class="text-sm text-gray-600">Uploading...</span>
                </div>
            </div>
            <div id="uploadError" class="hidden mt-3 text-red-600 text-sm"></div>
        </div>

        <div class="px-6 py-2">
            <p class="text-gray-500 text-sm">Or select from gallery:</p>
        </div>

        <div class="px-6 pb-4 overflow-y-auto" style="max-height: calc(80vh - 280px);">
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
let selectedGalleryImage = null;
const galleryShowLogos = <?= $showLogos ? 'true' : 'false' ?>;

<?php if ($showLogos): ?>
// Site logos for SEO page
const siteLogos = [];
<?php if ($siteLogo = getSetting('site_logo')): ?>
siteLogos.push({ url: '<?= e($siteLogo) ?>', label: 'Site Logo (Colour)', bg: 'bg-gray-100' });
<?php endif; ?>
<?php if ($siteLogoWhite = getSetting('site_logo_white')): ?>
siteLogos.push({ url: '<?= e($siteLogoWhite) ?>', label: 'Site Logo (White)', bg: 'bg-navy-800' });
<?php endif; ?>
<?php endif; ?>

function openGalleryPicker(fieldId) {
    galleryTargetField = fieldId;
    selectedGalleryImage = null;
    document.getElementById('galleryModal').classList.remove('hidden');
    document.getElementById('uploadError').classList.add('hidden');
    document.getElementById('uploadProgress').classList.add('hidden');
    document.getElementById('uploadInput').value = '';
    loadGalleryImages();
}

function closeGalleryPicker() {
    document.getElementById('galleryModal').classList.add('hidden');
    galleryTargetField = null;
    selectedGalleryImage = null;
}

function loadGalleryImages() {
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;
    fetch('/admin/api/gallery-images.php?csrf_token=' + encodeURIComponent(csrfToken))
        .then(res => res.json())
        .then(data => {
            const grid = document.getElementById('galleryGrid');
            let html = '';

            // Add site logos first if enabled
            <?php if ($showLogos): ?>
            if (siteLogos.length > 0) {
                html += '<div class="col-span-full text-sm font-medium text-gray-600 mb-1">Site Logos</div>';
                html += siteLogos.map(logo => `
                    <div class="cursor-pointer border-2 border-transparent rounded-lg overflow-hidden hover:border-orange-300 transition-colors gallery-item ${logo.bg}"
                         data-url="${logo.url}"
                         onclick="highlightGalleryImage(this, '${logo.url}')">
                        <img src="${logo.url}" alt="${logo.label}" class="w-full h-24 object-contain p-2">
                    </div>
                `).join('');
                html += '<div class="col-span-full text-sm font-medium text-gray-600 mt-4 mb-1">Gallery Images</div>';
            }
            <?php endif; ?>

            if (data.success && data.images.length > 0) {
                html += data.images.map(img => `
                    <div class="cursor-pointer border-2 border-transparent rounded-lg overflow-hidden hover:border-orange-300 transition-colors gallery-item"
                         data-url="/uploads/${img.filename}"
                         onclick="highlightGalleryImage(this, '/uploads/${img.filename}')">
                        <img src="/uploads/${img.filename}" alt="${img.alt_text || ''}" class="w-full h-24 object-cover">
                    </div>
                `).join('');
            } else if (!galleryShowLogos || typeof siteLogos === 'undefined' || siteLogos.length === 0) {
                html += '<p class="col-span-full text-center text-gray-500 py-8">No images yet. Upload one above!</p>';
            }

            grid.innerHTML = html;
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
    selectedGalleryImage = url;
}

function selectGalleryImage() {
    if (selectedGalleryImage && galleryTargetField) {
        document.getElementById(galleryTargetField).value = selectedGalleryImage;
        const preview = document.getElementById(galleryTargetField + '_preview');
        if (preview) {
            preview.innerHTML = '<img src="' + selectedGalleryImage + '" class="w-full h-32 object-cover rounded-lg">';
        }
    }
    closeGalleryPicker();
}

// Upload handling
async function handleGalleryUpload(file) {
    if (!file) return;

    // Validate file type
    if (!file.type.startsWith('image/')) {
        showGalleryUploadError('Please select an image file');
        return;
    }

    // Validate size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showGalleryUploadError('Image must be less than 5MB');
        return;
    }

    const csrfToken = document.querySelector('input[name="csrf_token"]').value;
    const formData = new FormData();
    formData.append('image', file);
    formData.append('csrf_token', csrfToken);

    // Show progress
    document.getElementById('uploadError').classList.add('hidden');
    document.getElementById('uploadProgress').classList.remove('hidden');
    document.getElementById('uploadProgressBar').style.width = '0%';
    document.getElementById('uploadProgressText').textContent = 'Uploading...';

    // Simulate progress while uploading
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress = Math.min(progress + 10, 90);
        document.getElementById('uploadProgressBar').style.width = progress + '%';
    }, 200);

    try {
        const response = await fetch('/admin/api/upload-image.php', {
            method: 'POST',
            body: formData
        });

        clearInterval(progressInterval);
        const data = await response.json();

        if (data.success) {
            // Complete progress bar
            document.getElementById('uploadProgressBar').style.width = '100%';
            document.getElementById('uploadProgressText').textContent = 'Uploaded! Compressed by ' + (data.savings || 0) + '%';

            // Set as selected image and auto-select
            selectedGalleryImage = data.url;

            // Update the field and preview
            if (galleryTargetField) {
                document.getElementById(galleryTargetField).value = data.url;
                const preview = document.getElementById(galleryTargetField + '_preview');
                if (preview) {
                    preview.innerHTML = '<img src="' + data.url + '" class="w-full h-32 object-cover rounded-lg">';
                }
            }

            // Reload gallery to show new image
            loadGalleryImages();

            // Close modal after short delay
            setTimeout(() => {
                closeGalleryPicker();
            }, 1000);
        } else {
            showGalleryUploadError(data.error || 'Upload failed');
        }
    } catch (error) {
        clearInterval(progressInterval);
        showGalleryUploadError('Upload failed. Please try again.');
    }

    // Reset input
    document.getElementById('uploadInput').value = '';
}

function showGalleryUploadError(message) {
    document.getElementById('uploadProgress').classList.add('hidden');
    document.getElementById('uploadError').classList.remove('hidden');
    document.getElementById('uploadError').textContent = message;
}

// Drag and drop handling
document.addEventListener('DOMContentLoaded', function() {
    const uploadZone = document.getElementById('uploadZone');

    if (uploadZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadZone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadZone.addEventListener(eventName, () => {
                uploadZone.classList.add('border-orange-500', 'bg-orange-50');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadZone.addEventListener(eventName, () => {
                uploadZone.classList.remove('border-orange-500', 'bg-orange-50');
            }, false);
        });

        uploadZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleGalleryUpload(files[0]);
            }
        }, false);
    }
});
</script>
