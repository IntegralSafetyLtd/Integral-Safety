<?php
/**
 * Analytics API - Devices
 * Returns device type, browser and OS breakdown
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

// Get date range from parameters
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d', strtotime("-{$days} days"));
$endDate = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');

try {
    // Get device type breakdown
    $deviceTypes = dbFetchAll(
        "SELECT
            device_type as type,
            COUNT(*) as pageviews,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND device_type != 'bot'
         GROUP BY device_type
         ORDER BY visitors DESC",
        [$startDate, $endDate]
    );

    // Get browser breakdown
    $browsers = dbFetchAll(
        "SELECT
            COALESCE(browser_name, 'Unknown') as browser,
            COUNT(*) as pageviews,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND device_type != 'bot'
         GROUP BY browser_name
         ORDER BY visitors DESC
         LIMIT 10",
        [$startDate, $endDate]
    );

    // Get OS breakdown
    $operatingSystems = dbFetchAll(
        "SELECT
            COALESCE(os_name, 'Unknown') as os,
            COUNT(*) as pageviews,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND device_type != 'bot'
         GROUP BY os_name
         ORDER BY visitors DESC
         LIMIT 10",
        [$startDate, $endDate]
    );

    // Get device type by browser (for drill-down)
    $browserByDevice = dbFetchAll(
        "SELECT
            device_type,
            COALESCE(browser_name, 'Unknown') as browser,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND device_type != 'bot'
         GROUP BY device_type, browser_name
         ORDER BY device_type, visitors DESC",
        [$startDate, $endDate]
    );

    // Calculate total visitors for percentages
    $totalVisitors = dbFetchOne(
        "SELECT COUNT(DISTINCT session_hash) as total FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND device_type != 'bot'",
        [$startDate, $endDate]
    )['total'] ?? 1;

    // Add percentages to device types
    foreach ($deviceTypes as &$device) {
        $device['percentage'] = round(($device['visitors'] / $totalVisitors) * 100, 1);
    }

    // Add percentages to browsers
    foreach ($browsers as &$browser) {
        $browser['percentage'] = round(($browser['visitors'] / $totalVisitors) * 100, 1);
    }

    // Add percentages to OS
    foreach ($operatingSystems as &$os) {
        $os['percentage'] = round(($os['visitors'] / $totalVisitors) * 100, 1);
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'device_types' => $deviceTypes,
            'browsers' => $browsers,
            'operating_systems' => $operatingSystems,
            'browser_by_device' => $browserByDevice,
            'total_visitors' => (int)$totalVisitors
        ],
        'period' => [
            'start' => $startDate,
            'end' => $endDate
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch device data']);
}
