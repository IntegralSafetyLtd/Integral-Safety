<?php
/**
 * Analytics API - Referrers & Traffic Sources
 * Returns referrer breakdown and UTM campaign data
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

// Optional filter by referrer type
$type = isset($_GET['type']) ? $_GET['type'] : null;

try {
    // Get referrer type breakdown
    $byType = dbFetchAll(
        "SELECT
            referrer_type as type,
            COUNT(*) as pageviews,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND referrer_type != 'internal'
         GROUP BY referrer_type
         ORDER BY visitors DESC",
        [$startDate, $endDate]
    );

    // Get top referrer domains
    $whereType = $type ? "AND referrer_type = ?" : "";
    $params = $type
        ? [$startDate, $endDate, $type, $limit]
        : [$startDate, $endDate, $limit];

    $topReferrers = dbFetchAll(
        "SELECT
            COALESCE(referrer_domain, 'Direct') as domain,
            referrer_type as type,
            COUNT(*) as pageviews,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND referrer_type != 'internal'
         {$whereType}
         GROUP BY referrer_domain, referrer_type
         ORDER BY visitors DESC
         LIMIT ?",
        $params
    );

    // Get UTM campaigns
    $campaigns = dbFetchAll(
        "SELECT
            utm_source as source,
            utm_medium as medium,
            utm_campaign as campaign,
            COUNT(*) as pageviews,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND utm_source IS NOT NULL
         GROUP BY utm_source, utm_medium, utm_campaign
         ORDER BY visitors DESC
         LIMIT ?",
        [$startDate, $endDate, $limit]
    );

    // Get search terms (from UTM term parameter)
    $searchTerms = dbFetchAll(
        "SELECT
            utm_term as term,
            COUNT(*) as pageviews,
            COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND utm_term IS NOT NULL
         AND utm_term != ''
         GROUP BY utm_term
         ORDER BY visitors DESC
         LIMIT 20",
        [$startDate, $endDate]
    );

    echo json_encode([
        'success' => true,
        'data' => [
            'by_type' => $byType,
            'top_referrers' => $topReferrers,
            'campaigns' => $campaigns,
            'search_terms' => $searchTerms
        ],
        'period' => [
            'start' => $startDate,
            'end' => $endDate
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch referrer data']);
}
