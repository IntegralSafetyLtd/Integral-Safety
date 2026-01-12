<?php
/**
 * Image Processing Functions
 * Handles optimization, resizing, compression, cropping, and WebP generation
 */

define('IMAGE_MAX_WIDTH', 1200);
define('IMAGE_QUALITY', 80);
define('WEBP_QUALITY', 80);

/**
 * Upload and optimize an image
 */
function uploadAndOptimizeImage($file, $subfolder = '') {
    // Validate upload
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed: ' . getUploadErrorMessage($file['error'] ?? -1)];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File too large (max ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB)'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS)];
    }

    // Verify MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes)) {
        return ['success' => false, 'error' => 'Invalid MIME type'];
    }

    // Generate filename
    $filename = uniqid() . '_' . time();
    $uploadDir = UPLOADS_PATH . ($subfolder ? '/' . $subfolder : '');

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Load and process image
    $image = loadImage($file['tmp_name'], $mimeType);
    if (!$image) {
        return ['success' => false, 'error' => 'Failed to process image'];
    }

    // Get original dimensions
    $origWidth = imagesx($image);
    $origHeight = imagesy($image);

    // Resize if needed
    if ($origWidth > IMAGE_MAX_WIDTH) {
        $newWidth = IMAGE_MAX_WIDTH;
        $newHeight = (int)($origHeight * (IMAGE_MAX_WIDTH / $origWidth));
        $image = resizeImage($image, $newWidth, $newHeight);
    }

    $finalWidth = imagesx($image);
    $finalHeight = imagesy($image);

    // Determine output format (preserve original or convert)
    $outputExt = in_array($ext, ['jpg', 'jpeg']) ? 'jpg' : $ext;
    $filepath = $uploadDir . '/' . $filename . '.' . $outputExt;

    // Save optimized image
    $saved = saveImage($image, $filepath, $mimeType, IMAGE_QUALITY);
    if (!$saved) {
        imagedestroy($image);
        return ['success' => false, 'error' => 'Failed to save image'];
    }

    // Generate WebP version
    $webpPath = $uploadDir . '/' . $filename . '.webp';
    if (function_exists('imagewebp')) {
        imagewebp($image, $webpPath, WEBP_QUALITY);
    }

    imagedestroy($image);

    // Get file sizes
    $fileSize = filesize($filepath);
    $webpSize = file_exists($webpPath) ? filesize($webpPath) : 0;

    // Save to database
    $dbFilename = $filename . '.' . $outputExt;
    dbExecute(
        "INSERT INTO gallery (filename, original_name, alt_text, file_size, mime_type, width, height, webp_filename)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        [
            $dbFilename,
            $file['name'],
            pathinfo($file['name'], PATHINFO_FILENAME),
            $fileSize,
            $mimeType,
            $finalWidth,
            $finalHeight,
            $filename . '.webp'
        ]
    );

    $id = db()->lastInsertId();
    $url = UPLOADS_URL . ($subfolder ? '/' . $subfolder : '') . '/' . $dbFilename;

    return [
        'success' => true,
        'id' => $id,
        'filename' => $dbFilename,
        'url' => $url,
        'webp_url' => UPLOADS_URL . ($subfolder ? '/' . $subfolder : '') . '/' . $filename . '.webp',
        'width' => $finalWidth,
        'height' => $finalHeight,
        'file_size' => $fileSize,
        'original_size' => $file['size']
    ];
}

/**
 * Load image from file
 */
function loadImage($path, $mimeType) {
    switch ($mimeType) {
        case 'image/jpeg':
            return @imagecreatefromjpeg($path);
        case 'image/png':
            $img = @imagecreatefrompng($path);
            if ($img) {
                imagealphablending($img, false);
                imagesavealpha($img, true);
            }
            return $img;
        case 'image/gif':
            return @imagecreatefromgif($path);
        case 'image/webp':
            return @imagecreatefromwebp($path);
        default:
            return false;
    }
}

/**
 * Resize image maintaining aspect ratio
 */
function resizeImage($image, $newWidth, $newHeight) {
    $resized = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG/GIF
    imagealphablending($resized, false);
    imagesavealpha($resized, true);
    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
    imagefill($resized, 0, 0, $transparent);

    imagecopyresampled(
        $resized, $image,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        imagesx($image), imagesy($image)
    );

    imagedestroy($image);
    return $resized;
}

/**
 * Save image with compression
 */
function saveImage($image, $path, $mimeType, $quality) {
    switch ($mimeType) {
        case 'image/jpeg':
            return imagejpeg($image, $path, $quality);
        case 'image/png':
            // PNG quality is 0-9 (compression level)
            $pngQuality = (int)(9 - ($quality / 100 * 9));
            return imagepng($image, $path, $pngQuality);
        case 'image/gif':
            return imagegif($image, $path);
        case 'image/webp':
            return imagewebp($image, $path, $quality);
        default:
            return false;
    }
}

/**
 * Crop image
 */
function cropImage($image, $x, $y, $width, $height) {
    $cropped = imagecrop($image, [
        'x' => (int)$x,
        'y' => (int)$y,
        'width' => (int)$width,
        'height' => (int)$height
    ]);

    if ($cropped !== false) {
        imagedestroy($image);
        return $cropped;
    }
    return $image;
}

/**
 * Process an existing image (resize, crop, quality)
 */
function processExistingImage($imageId, $options = []) {
    $image = dbFetchOne("SELECT * FROM gallery WHERE id = ?", [$imageId]);
    if (!$image) {
        return ['success' => false, 'error' => 'Image not found'];
    }

    $filepath = UPLOADS_PATH . '/' . $image['filename'];
    if (!file_exists($filepath)) {
        return ['success' => false, 'error' => 'File not found'];
    }

    $mimeType = $image['mime_type'] ?? mime_content_type($filepath);
    $img = loadImage($filepath, $mimeType);

    if (!$img) {
        return ['success' => false, 'error' => 'Failed to load image'];
    }

    // Apply crop first if specified
    if (!empty($options['crop'])) {
        $crop = $options['crop'];
        if (isset($crop['x'], $crop['y'], $crop['width'], $crop['height'])) {
            $img = cropImage($img, $crop['x'], $crop['y'], $crop['width'], $crop['height']);
        }
    }

    // Apply resize if specified
    if (!empty($options['width']) && !empty($options['height'])) {
        $img = resizeImage($img, (int)$options['width'], (int)$options['height']);
    }

    $quality = isset($options['quality']) ? max(10, min(100, (int)$options['quality'])) : IMAGE_QUALITY;

    $finalWidth = imagesx($img);
    $finalHeight = imagesy($img);

    // Generate new filename
    $baseName = pathinfo($image['filename'], PATHINFO_FILENAME);
    $ext = pathinfo($image['filename'], PATHINFO_EXTENSION);
    $newFilename = $baseName . '_edited_' . time() . '.' . $ext;
    $newPath = UPLOADS_PATH . '/' . $newFilename;

    // Save
    saveImage($img, $newPath, $mimeType, $quality);

    // Generate new webp
    $webpFilename = pathinfo($newFilename, PATHINFO_FILENAME) . '.webp';
    if (function_exists('imagewebp')) {
        imagewebp($img, UPLOADS_PATH . '/' . $webpFilename, $quality);
    }

    $newSize = filesize($newPath);

    // Update database
    dbExecute(
        "UPDATE gallery SET filename = ?, width = ?, height = ?, file_size = ?, webp_filename = ? WHERE id = ?",
        [$newFilename, $finalWidth, $finalHeight, $newSize, $webpFilename, $imageId]
    );

    imagedestroy($img);

    return [
        'success' => true,
        'filename' => $newFilename,
        'url' => UPLOADS_URL . '/' . $newFilename,
        'width' => $finalWidth,
        'height' => $finalHeight,
        'file_size' => $newSize
    ];
}

/**
 * Rename an image file
 */
function renameImageFile($imageId, $newName) {
    $newName = preg_replace('/[^a-zA-Z0-9_-]/', '', $newName);
    if (empty($newName)) {
        return ['success' => false, 'error' => 'Invalid filename'];
    }

    $image = dbFetchOne("SELECT * FROM gallery WHERE id = ?", [$imageId]);
    if (!$image) {
        return ['success' => false, 'error' => 'Image not found'];
    }

    $ext = pathinfo($image['filename'], PATHINFO_EXTENSION);
    $oldPath = UPLOADS_PATH . '/' . $image['filename'];
    $newFilename = $newName . '.' . $ext;
    $newPath = UPLOADS_PATH . '/' . $newFilename;

    if (file_exists($newPath) && $newPath !== $oldPath) {
        return ['success' => false, 'error' => 'Filename already exists'];
    }

    if (!file_exists($oldPath)) {
        return ['success' => false, 'error' => 'Original file not found'];
    }

    if (rename($oldPath, $newPath)) {
        // Also rename webp if exists
        $oldWebp = UPLOADS_PATH . '/' . pathinfo($image['filename'], PATHINFO_FILENAME) . '.webp';
        $newWebp = UPLOADS_PATH . '/' . $newName . '.webp';
        if (file_exists($oldWebp)) {
            rename($oldWebp, $newWebp);
        }

        dbExecute("UPDATE gallery SET filename = ?, webp_filename = ? WHERE id = ?",
            [$newFilename, $newName . '.webp', $imageId]);

        return ['success' => true, 'filename' => $newFilename, 'url' => UPLOADS_URL . '/' . $newFilename];
    }

    return ['success' => false, 'error' => 'Failed to rename file'];
}

/**
 * Get upload error message
 */
function getUploadErrorMessage($code) {
    $messages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temp folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
        UPLOAD_ERR_EXTENSION => 'Upload stopped by extension',
    ];
    return $messages[$code] ?? 'Unknown upload error';
}

/**
 * Format file size for display
 */
function formatFileSize($bytes) {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 2) . ' MB';
}
