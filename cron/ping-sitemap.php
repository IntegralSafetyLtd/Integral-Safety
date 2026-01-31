<?php
/**
 * Sitemap Ping Cron Job
 * Notifies search engines when the sitemap has been updated
 *
 * Run daily: 0 6 * * * php /path/to/cron/ping-sitemap.php
 */

// Prevent web access
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';

$sitemapUrl = SITE_URL . '/sitemap.xml';

// Check when sitemap was last generated
$lastGenerated = getSetting('seo_sitemap_last_generated');
$lastPinged = getSetting('seo_sitemap_last_pinged');

// Only ping if sitemap was generated after last ping (or never pinged)
if ($lastGenerated && (!$lastPinged || strtotime($lastGenerated) > strtotime($lastPinged))) {

    $results = [];

    // Ping Bing (still supports sitemap ping)
    $bingUrl = 'https://www.bing.com/ping?sitemap=' . urlencode($sitemapUrl);
    $bingResponse = @file_get_contents($bingUrl);
    $bingSuccess = $bingResponse !== false;
    $results['bing'] = $bingSuccess ? 'OK' : 'Failed';

    // Ping IndexNow (Bing's newer protocol) if API key is set
    $indexNowKey = getSetting('seo_indexnow_key');
    if ($indexNowKey) {
        $indexNowUrl = 'https://www.bing.com/indexnow?url=' . urlencode(SITE_URL) . '&key=' . urlencode($indexNowKey);
        $indexNowResponse = @file_get_contents($indexNowUrl);
        $results['indexnow'] = $indexNowResponse !== false ? 'OK' : 'Failed';
    }

    // Update last pinged timestamp
    updateSetting('seo_sitemap_last_pinged', date('Y-m-d H:i:s'));

    // Log results
    $logMessage = date('Y-m-d H:i:s') . " - Sitemap ping results: " . json_encode($results);
    echo $logMessage . "\n";

    // Also log to file
    $logFile = __DIR__ . '/logs/sitemap-ping.log';
    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0755, true);
    }
    file_put_contents($logFile, $logMessage . "\n", FILE_APPEND);

} else {
    echo date('Y-m-d H:i:s') . " - No sitemap update since last ping, skipping.\n";
}
