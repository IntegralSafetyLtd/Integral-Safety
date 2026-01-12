<?php
/**
 * Edit Image API
 * Handles image editing operations: get info, rename, process (resize/crop/quality)
 */
require_once __DIR__ . '/../../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/image-processor.php';

requireLogin();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
    exit;
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid token']);
    exit;
}

$imageId = (int)($_POST['image_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$imageId) {
    echo json_encode(['success' => false, 'error' => 'Image ID required']);
    exit;
}

$image = dbFetchOne("SELECT * FROM gallery WHERE id = ?", [$imageId]);
if (!$image) {
    echo json_encode(['success' => false, 'error' => 'Image not found']);
    exit;
}

$filepath = UPLOADS_PATH . '/' . $image['filename'];

switch ($action) {
    case 'get_info':
        // Get image dimensions if not stored
        $width = $image['width'];
        $height = $image['height'];
        if ((!$width || !$height) && file_exists($filepath)) {
            $size = @getimagesize($filepath);
            if ($size) {
                $width = $size[0];
                $height = $size[1];
                // Update database with dimensions
                dbExecute("UPDATE gallery SET width = ?, height = ? WHERE id = ?", [$width, $height, $imageId]);
            }
        }

        echo json_encode([
            'success' => true,
            'image' => [
                'id' => $image['id'],
                'filename' => pathinfo($image['filename'], PATHINFO_FILENAME),
                'extension' => pathinfo($image['filename'], PATHINFO_EXTENSION),
                'original_name' => $image['original_name'],
                'url' => UPLOADS_URL . '/' . $image['filename'],
                'width' => (int)$width,
                'height' => (int)$height,
                'file_size' => (int)($image['file_size'] ?? (file_exists($filepath) ? filesize($filepath) : 0)),
                'alt_text' => $image['alt_text'] ?? ''
            ]
        ]);
        break;

    case 'rename':
        $newName = $_POST['new_name'] ?? '';
        $result = renameImageFile($imageId, $newName);
        echo json_encode($result);
        break;

    case 'update_alt':
        $altText = sanitize($_POST['alt_text'] ?? '');
        dbExecute("UPDATE gallery SET alt_text = ? WHERE id = ?", [$altText, $imageId]);
        echo json_encode(['success' => true]);
        break;

    case 'process':
        $options = [];

        // Quality
        if (isset($_POST['quality'])) {
            $options['quality'] = (int)$_POST['quality'];
        }

        // Resize
        if (!empty($_POST['width']) && !empty($_POST['height'])) {
            $options['width'] = (int)$_POST['width'];
            $options['height'] = (int)$_POST['height'];
        }

        // Crop
        if (!empty($_POST['crop'])) {
            $crop = json_decode($_POST['crop'], true);
            if ($crop && isset($crop['x'], $crop['y'], $crop['width'], $crop['height'])) {
                $options['crop'] = $crop;
            }
        }

        $result = processExistingImage($imageId, $options);
        echo json_encode($result);
        break;

    case 'delete':
        // Delete the image file and database record
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        // Delete webp version if exists
        $webpPath = UPLOADS_PATH . '/' . pathinfo($image['filename'], PATHINFO_FILENAME) . '.webp';
        if (file_exists($webpPath)) {
            unlink($webpPath);
        }
        dbExecute("DELETE FROM gallery WHERE id = ?", [$imageId]);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
}
