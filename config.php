<?php
/**
 * Integral Safety - Configuration
 * Update these settings for your server
 */

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'integralsafetylt_integralsafety_cms');
define('DB_USER', 'integralsafetylt_integralsafety_admin');
define('DB_PASS', 'P4r4d0x!integral');

// Site Configuration
define('SITE_NAME', 'Integral Safety Ltd');
define('SITE_URL', 'https://integralsafetyltd.co.uk');
define('SITE_EMAIL', 'info@integralsafetyltd.co.uk');
define('SITE_PHONE', '01onal530 382 150'); // TODO: Update with correct phone number

// Security
define('SECURE_KEY', 'CHANGE_THIS_TO_RANDOM_STRING_32_CHARS_MIN');
define('SESSION_LIFETIME', 3600); // 1 hour

// Email (Resend API)
define('RESEND_API_KEY', 're_ivaUnqQ6_Nuh3RT7aRxgjqZ9USZ5Xa2Jb');
define('EMAIL_FROM', 'Integral Safety <noreply@integralsafetyltd.co.uk>');

// Paths
define('ROOT_PATH', __DIR__);
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('UPLOADS_URL', SITE_URL . '/uploads');

// Upload settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Timezone
date_default_timezone_set('Europe/London');

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}
