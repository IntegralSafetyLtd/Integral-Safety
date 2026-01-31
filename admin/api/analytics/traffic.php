<?php
/**
 * Analytics API - Traffic Time Series
 * Returns daily visitors and pageviews for charts
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
    // Get daily pageviews and sessions
    $dailyStats = dbFetchAll(
        "SELECT
            date_only as date,
            COUNT(*) as pageviews,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         GROUP BY date_only
         ORDER BY date_only ASC",
        [$startDate, $endDate]
    );

    // Create a complete date range with zeros for missing days
    $dates = [];
    $visitors = [];
    $pageviews = [];

    $current = strtotime($startDate);
    $end = strtotime($endDate);

    // Index existing data by date
    $dataByDate = [];
    foreach ($dailyStats as $row) {
        $dataByDate[$row['date']] = $row;
    }

    while ($current <= $end) {
        $dateStr = date('Y-m-d', $current);
        $dates[] = date('M j', $current);

        if (isset($dataByDate[$dateStr])) {
            $visitors[] = (int)$dataByDate[$dateStr]['visitors'];
            $pageviews[] = (int)$dataByDate[$dateStr]['pageviews'];
        } else {
            $visitors[] = 0;
            $pageviews[] = 0;
        }

        $current = strtotime('+1 day', $current);
    }

    // Get traffic sources breakdown
    $sources = dbFetchAll(
        "SELECT
            referrer_type,
            COUNT(DISTINCT session_hash) as sessions
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         GROUP BY referrer_type
         ORDER BY sessions DESC",
        [$startDate, $endDate]
    );

    // Format source names
    $sourceLabels = [
        'direct' => 'Direct',
        'search' => 'Search',
        'social' => 'Social',
        'referral' => 'Referral',
        'internal' => 'Internal'
    ];

    $sourceData = [];
    foreach ($sources as $source) {
        if ($source['referrer_type'] !== 'internal') { // Exclude internal navigation
            $sourceData[] = [
                'label' => $sourceLabels[$source['referrer_type']] ?? ucfirst($source['referrer_type']),
                'value' => (int)$source['sessions']
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'timeseries' => [
                'labels' => $dates,
                'visitors' => $visitors,
                'pageviews' => $pageviews
            ],
            'sources' => $sourceData
        ],
        'period' => [
            'start' => $startDate,
            'end' => $endDate
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch traffic data']);
}
