<?php
/**
 * Analytics API - Top Pages
 * Returns most viewed pages with metrics
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

// Optional filter by specific page path
$pagePath = isset($_GET['path']) ? $_GET['path'] : null;

try {
    if ($pagePath) {
        // Get detailed stats for a specific page
        $pageStats = dbFetchOne(
            "SELECT
                page_path,
                page_title,
                COUNT(*) as views,
                COUNT(DISTINCT session_hash) as visitors
             FROM analytics_pageviews
             WHERE date_only BETWEEN ? AND ?
             AND page_path = ?
             GROUP BY page_path, page_title",
            [$startDate, $endDate, $pagePath]
        );

        // Get daily breakdown for this page
        $dailyViews = dbFetchAll(
            "SELECT
                date_only as date,
                COUNT(*) as views
             FROM analytics_pageviews
             WHERE date_only BETWEEN ? AND ?
             AND page_path = ?
             GROUP BY date_only
             ORDER BY date_only ASC",
            [$startDate, $endDate, $pagePath]
        );

        // Get referrer breakdown for this page
        $referrers = dbFetchAll(
            "SELECT
                COALESCE(referrer_domain, 'Direct') as source,
                referrer_type,
                COUNT(*) as views
             FROM analytics_pageviews
             WHERE date_only BETWEEN ? AND ?
             AND page_path = ?
             GROUP BY referrer_domain, referrer_type
             ORDER BY views DESC
             LIMIT 10",
            [$startDate, $endDate, $pagePath]
        );

        echo json_encode([
            'success' => true,
            'data' => [
                'page' => $pageStats,
                'daily' => $dailyViews,
                'referrers' => $referrers
            ]
        ]);
    } else {
        // Get top pages
        $pages = dbFetchAll(
            "SELECT
                page_path,
                MAX(page_title) as page_title,
                COUNT(*) as views,
                COUNT(DISTINCT session_hash) as visitors
             FROM analytics_pageviews
             WHERE date_only BETWEEN ? AND ?
             GROUP BY page_path
             ORDER BY views DESC
             LIMIT ?",
            [$startDate, $endDate, $limit]
        );

        // Get total pageviews for percentage calculation
        $total = dbFetchOne(
            "SELECT COUNT(*) as total FROM analytics_pageviews WHERE date_only BETWEEN ? AND ?",
            [$startDate, $endDate]
        )['total'] ?? 1;

        // Add percentage to each page
        foreach ($pages as &$page) {
            $page['percentage'] = round(($page['views'] / $total) * 100, 1);
        }

        // Get landing pages (first page of session)
        $landingPages = dbFetchAll(
            "SELECT
                landing_page as page_path,
                COUNT(*) as sessions
             FROM analytics_sessions
             WHERE date_only BETWEEN ? AND ?
             GROUP BY landing_page
             ORDER BY sessions DESC
             LIMIT ?",
            [$startDate, $endDate, $limit]
        );

        // Get exit pages (last page of session)
        $exitPages = dbFetchAll(
            "SELECT
                exit_page as page_path,
                COUNT(*) as sessions
             FROM analytics_sessions
             WHERE date_only BETWEEN ? AND ?
             GROUP BY exit_page
             ORDER BY sessions DESC
             LIMIT ?",
            [$startDate, $endDate, $limit]
        );

        echo json_encode([
            'success' => true,
            'data' => [
                'pages' => $pages,
                'landing_pages' => $landingPages,
                'exit_pages' => $exitPages,
                'total_pageviews' => (int)$total
            ],
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch page data']);
}
