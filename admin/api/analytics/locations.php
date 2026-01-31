<?php
/**
 * Analytics API - Locations
 * Returns country breakdown data
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
$limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 100) : 20;

// Country code to name mapping
$countryNames = [
    'GB' => 'United Kingdom',
    'US' => 'United States',
    'DE' => 'Germany',
    'FR' => 'France',
    'ES' => 'Spain',
    'IT' => 'Italy',
    'NL' => 'Netherlands',
    'PT' => 'Portugal',
    'PL' => 'Poland',
    'RU' => 'Russia',
    'JP' => 'Japan',
    'CN' => 'China',
    'KR' => 'South Korea',
    'SA' => 'Saudi Arabia',
    'IN' => 'India',
    'TR' => 'Turkey',
    'SE' => 'Sweden',
    'DK' => 'Denmark',
    'NO' => 'Norway',
    'FI' => 'Finland',
    'CZ' => 'Czech Republic',
    'HU' => 'Hungary',
    'AU' => 'Australia',
    'CA' => 'Canada',
    'IE' => 'Ireland',
    'BE' => 'Belgium',
    'AT' => 'Austria',
    'CH' => 'Switzerland'
];

try {
    // Get country breakdown
    $countries = dbFetchAll(
        "SELECT
            COALESCE(country_code, 'XX') as country_code,
            COUNT(*) as pageviews,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND device_type != 'bot'
         GROUP BY country_code
         ORDER BY visitors DESC
         LIMIT ?",
        [$startDate, $endDate, $limit]
    );

    // Calculate total visitors for percentages
    $totalVisitors = dbFetchOne(
        "SELECT COUNT(DISTINCT session_hash) as total FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND device_type != 'bot'",
        [$startDate, $endDate]
    )['total'] ?? 1;

    // Add country names and percentages
    foreach ($countries as &$country) {
        $code = $country['country_code'];
        $country['country_name'] = $countryNames[$code] ?? ($code === 'XX' ? 'Unknown' : $code);
        $country['percentage'] = round(($country['visitors'] / $totalVisitors) * 100, 1);
    }

    // Get UK breakdown by referrer type (for drill-down)
    $ukBreakdown = dbFetchAll(
        "SELECT
            referrer_type as type,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND country_code = 'GB'
         AND device_type != 'bot'
         GROUP BY referrer_type
         ORDER BY visitors DESC",
        [$startDate, $endDate]
    );

    echo json_encode([
        'success' => true,
        'data' => [
            'countries' => $countries,
            'uk_breakdown' => $ukBreakdown,
            'total_visitors' => (int)$totalVisitors
        ],
        'period' => [
            'start' => $startDate,
            'end' => $endDate
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch location data']);
}
