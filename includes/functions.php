<?php
/**
 * Helper Functions
 */

/**
 * Sanitize output for HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return strip_tags(trim($input));
}

/**
 * Generate URL-friendly slug
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

/**
 * Get page by slug
 */
function getPage($slug) {
    return dbFetchOne("SELECT * FROM pages WHERE slug = ? AND is_active = 1", [$slug]);
}

/**
 * Get all active services
 */
function getServices($homepageOnly = false) {
    $sql = "SELECT * FROM services WHERE is_active = 1";
    if ($homepageOnly) {
        $sql .= " AND show_on_homepage = 1";
    }
    $sql .= " ORDER BY sort_order ASC, title ASC";
    return dbFetchAll($sql);
}

/**
 * Get service by slug
 */
function getService($slug) {
    return dbFetchOne("SELECT * FROM services WHERE slug = ? AND is_active = 1", [$slug]);
}

/**
 * Get all active training courses
 */
function getTraining($homepageOnly = false) {
    $sql = "SELECT * FROM training WHERE is_active = 1";
    if ($homepageOnly) {
        $sql .= " AND show_on_homepage = 1";
    }
    $sql .= " ORDER BY sort_order ASC, title ASC";
    return dbFetchAll($sql);
}

/**
 * Get training by slug
 */
function getTrainingCourse($slug) {
    return dbFetchOne("SELECT * FROM training WHERE slug = ? AND is_active = 1", [$slug]);
}

/**
 * Get active testimonials
 */
function getTestimonials() {
    return dbFetchAll("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC");
}

/**
 * Get gallery images
 */
function getGalleryImages($limit = null) {
    $sql = "SELECT * FROM gallery ORDER BY uploaded_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    return dbFetchAll($sql);
}

/**
 * Get setting value
 */
function getSetting($key, $default = '') {
    $setting = dbFetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
    return $setting ? $setting['setting_value'] : $default;
}

/**
 * Update setting
 */
function updateSetting($key, $value) {
    return dbExecute(
        "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = ?",
        [$key, $value, $value]
    );
}

/**
 * Handle file upload
 */
function uploadImage($file, $subfolder = '') {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed'];
    }

    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File too large (max 5MB)'];
    }

    // Check extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $ext;

    $uploadDir = UPLOADS_PATH;
    if ($subfolder) {
        $uploadDir .= '/' . $subfolder;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
    }

    $filepath = $uploadDir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Save to database
        dbExecute(
            "INSERT INTO gallery (filename, original_name, alt_text, file_size, mime_type) VALUES (?, ?, ?, ?, ?)",
            [$filename, $file['name'], pathinfo($file['name'], PATHINFO_FILENAME), $file['size'], $mimeType]
        );

        return [
            'success' => true,
            'filename' => $filename,
            'url' => UPLOADS_URL . '/' . ($subfolder ? $subfolder . '/' : '') . $filename,
            'id' => dbLastId()
        ];
    }

    return ['success' => false, 'error' => 'Failed to save file'];
}

/**
 * Delete image
 */
function deleteImage($id) {
    $image = dbFetchOne("SELECT * FROM gallery WHERE id = ?", [$id]);
    if ($image) {
        $filepath = UPLOADS_PATH . '/' . $image['filename'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        dbExecute("DELETE FROM gallery WHERE id = ?", [$id]);
        return true;
    }
    return false;
}

/**
 * Format date
 */
function formatDate($date, $format = 'j F Y') {
    return date($format, strtotime($date));
}

/**
 * Truncate text
 */
function truncate($text, $length = 150, $append = '...') {
    $text = strip_tags($text);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $append;
}

/**
 * Get icon SVG
 */
function getIcon($name, $class = 'w-6 h-6') {
    $icons = [
        'fire' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path></svg>',
        'clipboard' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>',
        'book' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>',
        'shield' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
        'users' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
        'check' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
        'phone' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>',
        'mail' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
        'location' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
    ];

    return $icons[$name] ?? $icons['clipboard'];
}

/**
 * Send contact form email (using Resend API)
 */
function sendContactEmail($data) {
    $to = getSetting('contact_email', SITE_EMAIL);

    // Save to database first
    dbExecute(
        "INSERT INTO contact_submissions (name, email, phone, company, message) VALUES (?, ?, ?, ?, ?)",
        [$data['name'], $data['email'], $data['phone'] ?? '', $data['company'] ?? '', $data['message']]
    );

    // Send via Resend
    require_once __DIR__ . '/email.php';
    $result = sendContactFormEmail($data, $to);
    return $result['success'];
}

/**
 * Get unread contact submissions count
 */
function getUnreadContactCount() {
    $result = dbFetchOne("SELECT COUNT(*) as count FROM contact_submissions WHERE is_read = 0");
    return $result['count'] ?? 0;
}
