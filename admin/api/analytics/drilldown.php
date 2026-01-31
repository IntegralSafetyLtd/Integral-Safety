<?php
/**
 * Analytics API - Stat Card Drill-Down
 * Returns detailed data for each dashboard metric
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

$metric = $_GET['metric'] ?? null;
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d', strtotime("-{$days} days"));
$endDate = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');
$limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : 50;

if (!$metric) {
    http_response_code(400);
    echo json_encode(['error' => 'Metric parameter required']);
    exit;
}

$countryNames = [
    'GB' => 'United Kingdom', 'US' => 'United States', 'DE' => 'Germany',
    'FR' => 'France', 'ES' => 'Spain', 'IT' => 'Italy', 'NL' => 'Netherlands',
    'AU' => 'Australia', 'CA' => 'Canada', 'IE' => 'Ireland', 'IN' => 'India'
];

try {
    $data = [];

    switch ($metric) {
        case 'visitors':
            // List all sessions/visitors
            $sessions = dbFetchAll(
                "SELECT
                    s.session_hash,
                    s.first_seen,
                    s.last_seen,
                    s.pageviews,
                    s.landing_page,
                    s.exit_page,
                    s.device_type,
                    s.country_code,
                    s.referrer_type,
                    s.duration_seconds,
                    s.is_bounce
                 FROM analytics_sessions s
                 WHERE s.date_only BETWEEN ? AND ?
                 ORDER BY s.first_seen DESC
                 LIMIT ?",
                [$startDate, $endDate, $limit]
            );

            $data['title'] = 'All Visitors';
            $data['total'] = dbFetchOne(
                "SELECT COUNT(*) as c FROM analytics_sessions WHERE date_only BETWEEN ? AND ?",
                [$startDate, $endDate]
            )['c'] ?? 0;

            $data['items'] = array_map(function($s) use ($countryNames) {
                return [
                    'session_hash' => $s['session_hash'],
                    'session_hash_short' => substr($s['session_hash'], 0, 8) . '...',
                    'first_seen' => $s['first_seen'],
                    'time' => date('j M, H:i', strtotime($s['first_seen'])),
                    'pageviews' => (int)$s['pageviews'],
                    'landing_page' => $s['landing_page'],
                    'exit_page' => $s['exit_page'],
                    'device' => ucfirst($s['device_type']),
                    'country' => $countryNames[$s['country_code']] ?? $s['country_code'] ?? 'Unknown',
                    'country_code' => $s['country_code'],
                    'referrer_type' => $s['referrer_type'],
                    'duration' => formatDuration((int)$s['duration_seconds']),
                    'duration_seconds' => (int)$s['duration_seconds'],
                    'is_bounce' => (bool)$s['is_bounce']
                ];
            }, $sessions);
            break;

        case 'pageviews':
            // List all pageviews
            $pageviews = dbFetchAll(
                "SELECT
                    p.session_hash,
                    p.page_path,
                    p.page_title,
                    p.referrer_domain,
                    p.referrer_type,
                    p.device_type,
                    p.browser_name,
                    p.country_code,
                    p.viewed_at
                 FROM analytics_pageviews p
                 WHERE p.date_only BETWEEN ? AND ?
                 ORDER BY p.viewed_at DESC
                 LIMIT ?",
                [$startDate, $endDate, $limit]
            );

            $data['title'] = 'All Page Views';
            $data['total'] = dbFetchOne(
                "SELECT COUNT(*) as c FROM analytics_pageviews WHERE date_only BETWEEN ? AND ?",
                [$startDate, $endDate]
            )['c'] ?? 0;

            $data['items'] = array_map(function($p) use ($countryNames) {
                return [
                    'session_hash' => $p['session_hash'],
                    'page_path' => $p['page_path'],
                    'page_title' => $p['page_title'],
                    'time' => date('j M, H:i:s', strtotime($p['viewed_at'])),
                    'viewed_at' => $p['viewed_at'],
                    'referrer' => $p['referrer_domain'] ?: 'Direct',
                    'referrer_type' => $p['referrer_type'],
                    'device' => ucfirst($p['device_type']),
                    'browser' => $p['browser_name'],
                    'country' => $countryNames[$p['country_code']] ?? $p['country_code'] ?? 'Unknown',
                    'country_code' => $p['country_code']
                ];
            }, $pageviews);
            break;

        case 'bounce':
            // Bounced vs engaged sessions
            $bounced = dbFetchAll(
                "SELECT
                    s.session_hash,
                    s.first_seen,
                    s.landing_page,
                    s.device_type,
                    s.country_code,
                    s.referrer_type
                 FROM analytics_sessions s
                 WHERE s.date_only BETWEEN ? AND ?
                 AND s.is_bounce = 1
                 ORDER BY s.first_seen DESC
                 LIMIT ?",
                [$startDate, $endDate, $limit]
            );

            $engaged = dbFetchAll(
                "SELECT
                    s.session_hash,
                    s.first_seen,
                    s.pageviews,
                    s.landing_page,
                    s.device_type,
                    s.country_code,
                    s.referrer_type,
                    s.duration_seconds
                 FROM analytics_sessions s
                 WHERE s.date_only BETWEEN ? AND ?
                 AND s.is_bounce = 0
                 ORDER BY s.first_seen DESC
                 LIMIT ?",
                [$startDate, $endDate, $limit]
            );

            $stats = dbFetchOne(
                "SELECT
                    COUNT(*) as total,
                    SUM(is_bounce) as bounced,
                    SUM(CASE WHEN is_bounce = 0 THEN 1 ELSE 0 END) as engaged
                 FROM analytics_sessions
                 WHERE date_only BETWEEN ? AND ?",
                [$startDate, $endDate]
            );

            $data['title'] = 'Bounce Rate Breakdown';
            $data['stats'] = [
                'total' => (int)$stats['total'],
                'bounced' => (int)$stats['bounced'],
                'engaged' => (int)$stats['engaged'],
                'bounce_rate' => $stats['total'] > 0 ? round(($stats['bounced'] / $stats['total']) * 100, 1) : 0
            ];

            // Bounce rate by landing page
            $data['by_landing_page'] = dbFetchAll(
                "SELECT
                    landing_page,
                    COUNT(*) as sessions,
                    SUM(is_bounce) as bounces,
                    ROUND((SUM(is_bounce) / COUNT(*)) * 100, 1) as bounce_rate
                 FROM analytics_sessions
                 WHERE date_only BETWEEN ? AND ?
                 GROUP BY landing_page
                 HAVING sessions >= 2
                 ORDER BY sessions DESC
                 LIMIT 15",
                [$startDate, $endDate]
            );

            // Bounce rate by source
            $data['by_source'] = dbFetchAll(
                "SELECT
                    referrer_type,
                    COUNT(*) as sessions,
                    SUM(is_bounce) as bounces,
                    ROUND((SUM(is_bounce) / COUNT(*)) * 100, 1) as bounce_rate
                 FROM analytics_sessions
                 WHERE date_only BETWEEN ? AND ?
                 GROUP BY referrer_type
                 ORDER BY sessions DESC",
                [$startDate, $endDate]
            );

            $data['bounced_sessions'] = array_map(function($s) use ($countryNames) {
                return [
                    'session_hash' => $s['session_hash'],
                    'time' => date('j M, H:i', strtotime($s['first_seen'])),
                    'landing_page' => $s['landing_page'],
                    'device' => ucfirst($s['device_type']),
                    'country' => $countryNames[$s['country_code']] ?? $s['country_code'] ?? 'Unknown',
                    'referrer_type' => $s['referrer_type']
                ];
            }, $bounced);

            $data['engaged_sessions'] = array_map(function($s) use ($countryNames) {
                return [
                    'session_hash' => $s['session_hash'],
                    'time' => date('j M, H:i', strtotime($s['first_seen'])),
                    'pageviews' => (int)$s['pageviews'],
                    'landing_page' => $s['landing_page'],
                    'device' => ucfirst($s['device_type']),
                    'country' => $countryNames[$s['country_code']] ?? $s['country_code'] ?? 'Unknown',
                    'duration' => formatDuration((int)$s['duration_seconds'])
                ];
            }, $engaged);
            break;

        case 'duration':
            // Session duration breakdown
            $stats = dbFetchOne(
                "SELECT
                    AVG(duration_seconds) as avg_duration,
                    MIN(duration_seconds) as min_duration,
                    MAX(duration_seconds) as max_duration,
                    COUNT(*) as total_sessions
                 FROM analytics_sessions
                 WHERE date_only BETWEEN ? AND ?
                 AND is_bounce = 0",
                [$startDate, $endDate]
            );

            // Duration buckets
            $buckets = dbFetchAll(
                "SELECT
                    CASE
                        WHEN duration_seconds = 0 THEN '0s (bounce)'
                        WHEN duration_seconds < 10 THEN '1-10s'
                        WHEN duration_seconds < 30 THEN '10-30s'
                        WHEN duration_seconds < 60 THEN '30-60s'
                        WHEN duration_seconds < 180 THEN '1-3m'
                        WHEN duration_seconds < 300 THEN '3-5m'
                        WHEN duration_seconds < 600 THEN '5-10m'
                        ELSE '10m+'
                    END as bucket,
                    COUNT(*) as sessions
                 FROM analytics_sessions
                 WHERE date_only BETWEEN ? AND ?
                 GROUP BY bucket
                 ORDER BY MIN(duration_seconds)",
                [$startDate, $endDate]
            );

            // Longest sessions
            $longest = dbFetchAll(
                "SELECT
                    s.session_hash,
                    s.first_seen,
                    s.pageviews,
                    s.landing_page,
                    s.duration_seconds,
                    s.device_type,
                    s.country_code
                 FROM analytics_sessions s
                 WHERE s.date_only BETWEEN ? AND ?
                 AND s.duration_seconds > 0
                 ORDER BY s.duration_seconds DESC
                 LIMIT 20",
                [$startDate, $endDate]
            );

            $data['title'] = 'Session Duration Analysis';
            $data['stats'] = [
                'avg_duration' => (int)$stats['avg_duration'],
                'avg_formatted' => formatDuration((int)$stats['avg_duration']),
                'min_duration' => (int)$stats['min_duration'],
                'max_duration' => (int)$stats['max_duration'],
                'max_formatted' => formatDuration((int)$stats['max_duration']),
                'total_sessions' => (int)$stats['total_sessions']
            ];

            $data['buckets'] = $buckets;

            $data['longest_sessions'] = array_map(function($s) use ($countryNames) {
                return [
                    'session_hash' => $s['session_hash'],
                    'time' => date('j M, H:i', strtotime($s['first_seen'])),
                    'pageviews' => (int)$s['pageviews'],
                    'landing_page' => $s['landing_page'],
                    'duration' => formatDuration((int)$s['duration_seconds']),
                    'duration_seconds' => (int)$s['duration_seconds'],
                    'device' => ucfirst($s['device_type']),
                    'country' => $countryNames[$s['country_code']] ?? $s['country_code'] ?? 'Unknown',
                    'country_code' => $s['country_code']
                ];
            }, $longest);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid metric']);
            exit;
    }

    echo json_encode([
        'success' => true,
        'metric' => $metric,
        'data' => $data,
        'period' => [
            'start' => $startDate,
            'end' => $endDate
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch data', 'message' => $e->getMessage()]);
}

function formatDuration($seconds) {
    if ($seconds < 60) {
        return $seconds . 's';
    } elseif ($seconds < 3600) {
        $mins = floor($seconds / 60);
        $secs = $seconds % 60;
        return $mins . 'm ' . $secs . 's';
    } else {
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds % 3600) / 60);
        return $hours . 'h ' . $mins . 'm';
    }
}
