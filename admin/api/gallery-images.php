<?php
/**
 * Gallery Images API
 * Returns gallery images as JSON for the image picker modal
 */
require_once __DIR__ . '/../../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();
header('Content-Type: application/json');

// Verify CSRF token for GET requests too (passed as query param)
if (!verifyCSRFToken($_GET['csrf_token'] ?? $_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid token']);
    exit;
}

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$images = dbFetchAll(
    "SELECT id, filename, original_name, alt_text, file_size, mime_type, width, height, webp_filename, uploaded_at
     FROM gallery
     ORDER BY uploaded_at DESC
     LIMIT ? OFFSET ?",
    [$limit, $offset]
);

$total = dbFetchOne("SELECT COUNT(*) as cnt FROM gallery", [])['cnt'] ?? 0;

// Add URL to each image
foreach ($images as &$img) {
    $img['url'] = UPLOADS_URL . '/' . $img['filename'];
    if (!empty($img['webp_filename'])) {
        $img['webp_url'] = UPLOADS_URL . '/' . $img['webp_filename'];
    }
    // Format file size
    $img['file_size_formatted'] = formatFileSizeDisplay($img['file_size']);
}

echo json_encode([
    'success' => true,
    'images' => $images,
    'total' => (int)$total,
    'limit' => $limit,
    'offset' => $offset
]);

function formatFileSizeDisplay($bytes) {
    if (!$bytes) return '0 B';
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 2) . ' MB';
}
