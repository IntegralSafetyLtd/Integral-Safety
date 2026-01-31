<?php
/**
 * Analytics API - Live Visitors
 * Returns currently active visitors (pageview in last X minutes)
 */
require_once __DIR__ . '/../../../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorised']);
    exit;
}

// Minutes to consider "live" (default 5)
$minutes = isset($_GET['minutes']) ? min((int)$_GET['minutes'], 60) : 5;
$cutoff = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));

try {
    // Get active visitors with their most recent pageview
    $liveVisitors = dbFetchAll(
        "SELECT
            p.session_hash,
            p.page_path,
            p.page_title,
            p.referrer_domain,
            p.referrer_type,
            p.device_type,
            p.browser_name,
            p.country_code,
            p.viewed_at,
            s.pageviews as session_pageviews,
            s.landing_page,
            s.first_seen
         FROM analytics_pageviews p
         INNER JOIN (
             SELECT session_hash, MAX(viewed_at) as latest
             FROM analytics_pageviews
             WHERE viewed_at >= ?
             AND device_type != 'bot'
             GROUP BY session_hash
         ) latest ON p.session_hash = latest.session_hash AND p.viewed_at = latest.latest
         LEFT JOIN analytics_sessions s ON p.session_hash = s.session_hash
         WHERE p.device_type != 'bot'
         ORDER BY p.viewed_at DESC
         LIMIT 50",
        [$cutoff]
    );

    // Count total active
    $activeCount = dbFetchOne(
        "SELECT COUNT(DISTINCT session_hash) as total
         FROM analytics_pageviews
         WHERE viewed_at >= ?
         AND device_type != 'bot'",
        [$cutoff]
    )['total'] ?? 0;

    // Get pageviews in last hour for sparkline
    $hourlyActivity = dbFetchAll(
        "SELECT
            DATE_FORMAT(viewed_at, '%H:%i') as time_slot,
            COUNT(*) as views
         FROM analytics_pageviews
         WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
         AND device_type != 'bot'
         GROUP BY DATE_FORMAT(viewed_at, '%Y-%m-%d %H:%i')
         ORDER BY viewed_at ASC"
    );

    // Country code to name mapping
    $countryNames = [
        'GB' => 'United Kingdom', 'US' => 'United States', 'DE' => 'Germany',
        'FR' => 'France', 'ES' => 'Spain', 'IT' => 'Italy', 'NL' => 'Netherlands',
        'AU' => 'Australia', 'CA' => 'Canada', 'IE' => 'Ireland', 'IN' => 'India'
    ];

    // Format visitor data
    $visitors = [];
    foreach ($liveVisitors as $v) {
        $visitors[] = [
            'session_hash' => substr($v['session_hash'], 0, 8) . '...',
            'session_hash_full' => $v['session_hash'], // Full hash for drill-down
            'page' => $v['page_path'],
            'page_title' => $v['page_title'],
            'referrer' => $v['referrer_domain'] ?: 'Direct',
            'referrer_type' => $v['referrer_type'],
            'device' => ucfirst($v['device_type']),
            'browser' => $v['browser_name'],
            'country' => $countryNames[$v['country_code']] ?? $v['country_code'] ?? 'Unknown',
            'country_code' => $v['country_code'],
            'time' => $v['viewed_at'],
            'time_ago' => timeAgo($v['viewed_at']),
            'session_pages' => (int)$v['session_pageviews'],
            'landing_page' => $v['landing_page'],
            'session_start' => $v['first_seen']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'active_count' => (int)$activeCount,
            'visitors' => $visitors,
            'activity' => $hourlyActivity,
            'cutoff_minutes' => $minutes
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch live data', 'message' => $e->getMessage()]);
}

/**
 * Format time as "X ago"
 */
function timeAgo($datetime) {
    $now = new DateTime();
    $then = new DateTime($datetime);
    $diff = $now->diff($then);

    if ($diff->i < 1) {
        return 'Just now';
    } elseif ($diff->i < 60) {
        return $diff->i . 'm ago';
    } elseif ($diff->h < 24) {
        return $diff->h . 'h ago';
    } else {
        return $diff->d . 'd ago';
    }
}
