<?php
/**
 * Analytics Tables Migration
 * Creates tables for server-side, cookie-free analytics
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

if (!isAdmin()) {
    header('Location: /admin/');
    exit;
}

$messages = [];
$errors = [];

try {
    $pdo = db();

    // Table 1: analytics_pageviews - Individual page views
    $sql = "CREATE TABLE IF NOT EXISTS analytics_pageviews (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        session_hash CHAR(64) NOT NULL,
        page_path VARCHAR(500) NOT NULL,
        page_title VARCHAR(255) DEFAULT NULL,
        referrer_url VARCHAR(1000) DEFAULT NULL,
        referrer_domain VARCHAR(255) DEFAULT NULL,
        referrer_type ENUM('direct','search','social','referral','internal') DEFAULT 'direct',
        utm_source VARCHAR(100) DEFAULT NULL,
        utm_medium VARCHAR(100) DEFAULT NULL,
        utm_campaign VARCHAR(100) DEFAULT NULL,
        utm_term VARCHAR(100) DEFAULT NULL,
        utm_content VARCHAR(100) DEFAULT NULL,
        device_type ENUM('desktop','tablet','mobile','bot') DEFAULT 'desktop',
        browser_name VARCHAR(50) DEFAULT NULL,
        os_name VARCHAR(50) DEFAULT NULL,
        country_code CHAR(2) DEFAULT NULL,
        viewed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        date_only DATE AS (DATE(viewed_at)) STORED,
        INDEX idx_session_hash (session_hash),
        INDEX idx_page_path (page_path(100)),
        INDEX idx_viewed_at (viewed_at),
        INDEX idx_date_only (date_only),
        INDEX idx_referrer_type (referrer_type),
        INDEX idx_device_type (device_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    $messages[] = "Created/verified analytics_pageviews table";

    // Table 2: analytics_sessions - Aggregated sessions
    $sql = "CREATE TABLE IF NOT EXISTS analytics_sessions (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        session_hash CHAR(64) NOT NULL UNIQUE,
        first_seen DATETIME NOT NULL,
        last_seen DATETIME NOT NULL,
        pageviews INT UNSIGNED DEFAULT 1,
        landing_page VARCHAR(500) DEFAULT NULL,
        exit_page VARCHAR(500) DEFAULT NULL,
        device_type ENUM('desktop','tablet','mobile','bot') DEFAULT 'desktop',
        country_code CHAR(2) DEFAULT NULL,
        referrer_type ENUM('direct','search','social','referral','internal') DEFAULT 'direct',
        utm_source VARCHAR(100) DEFAULT NULL,
        duration_seconds INT UNSIGNED AS (TIMESTAMPDIFF(SECOND, first_seen, last_seen)) STORED,
        is_bounce TINYINT(1) AS (pageviews = 1) STORED,
        date_only DATE AS (DATE(first_seen)) STORED,
        INDEX idx_first_seen (first_seen),
        INDEX idx_date_only (date_only),
        INDEX idx_is_bounce (is_bounce),
        INDEX idx_referrer_type (referrer_type),
        INDEX idx_device_type (device_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    $messages[] = "Created/verified analytics_sessions table";

    // Table 3: analytics_daily_stats - Pre-computed daily stats
    $sql = "CREATE TABLE IF NOT EXISTS analytics_daily_stats (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        stat_date DATE NOT NULL UNIQUE,
        total_pageviews INT UNSIGNED DEFAULT 0,
        unique_sessions INT UNSIGNED DEFAULT 0,
        bounced_sessions INT UNSIGNED DEFAULT 0,
        total_duration_seconds BIGINT UNSIGNED DEFAULT 0,
        bounce_rate DECIMAL(5,2) AS (
            CASE WHEN unique_sessions > 0
            THEN (bounced_sessions / unique_sessions) * 100
            ELSE 0 END
        ) STORED,
        avg_session_duration INT UNSIGNED AS (
            CASE WHEN unique_sessions > 0
            THEN total_duration_seconds / unique_sessions
            ELSE 0 END
        ) STORED,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_stat_date (stat_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    $messages[] = "Created/verified analytics_daily_stats table";

    // Insert default analytics settings if they don't exist
    $settings = [
        'analytics_enabled' => '1',
        'analytics_exclude_admins' => '1',
        'analytics_exclude_bots' => '1',
        'analytics_session_timeout' => '1800',
        'analytics_data_retention_days' => '365'
    ];

    foreach ($settings as $key => $value) {
        $existing = dbFetchOne("SELECT setting_key FROM settings WHERE setting_key = ?", [$key]);
        if (!$existing) {
            dbExecute(
                "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)",
                [$key, $value]
            );
            $messages[] = "Added setting: {$key} = {$value}";
        }
    }

} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Analytics Migration</h1>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <h3 class="font-bold mb-2">Errors</h3>
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($messages)): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        <h3 class="font-bold mb-2">Migration Complete</h3>
        <ul class="list-disc list-inside">
            <?php foreach ($messages as $message): ?>
            <li><?= e($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Tables Created</h2>

        <div class="space-y-4">
            <div class="border-l-4 border-orange-500 pl-4">
                <h3 class="font-medium">analytics_pageviews</h3>
                <p class="text-sm text-gray-600">Individual page view records with referrer, UTM, device and location data</p>
            </div>

            <div class="border-l-4 border-orange-500 pl-4">
                <h3 class="font-medium">analytics_sessions</h3>
                <p class="text-sm text-gray-600">Aggregated sessions with landing/exit pages and computed duration/bounce</p>
            </div>

            <div class="border-l-4 border-orange-500 pl-4">
                <h3 class="font-medium">analytics_daily_stats</h3>
                <p class="text-sm text-gray-600">Pre-computed daily statistics for fast dashboard loading</p>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t">
            <h3 class="font-medium mb-2">Settings Added</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li><code>analytics_enabled</code> - Enable/disable tracking (default: 1)</li>
                <li><code>analytics_exclude_admins</code> - Don't track logged-in admins (default: 1)</li>
                <li><code>analytics_exclude_bots</code> - Filter out bots (default: 1)</li>
                <li><code>analytics_session_timeout</code> - Session timeout in seconds (default: 1800)</li>
                <li><code>analytics_data_retention_days</code> - Days to keep detailed data (default: 365)</li>
            </ul>
        </div>

        <div class="mt-6">
            <a href="/admin/analytics.php" class="inline-block bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                Go to Analytics Dashboard &rarr;
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
