<?php
/**
 * Analytics API - Overview Stats
 * Returns summary statistics for dashboard cards
 */
require_once __DIR__ . '/../../../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/analytics.php';

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

// Calculate previous period for comparison
$periodLength = (strtotime($endDate) - strtotime($startDate)) / 86400;
$prevEndDate = date('Y-m-d', strtotime($startDate) - 1);
$prevStartDate = date('Y-m-d', strtotime($prevEndDate) - $periodLength);

try {
    // Current period stats
    $currentPageviews = dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_pageviews WHERE date_only BETWEEN ? AND ?",
        [$startDate, $endDate]
    )['total'] ?? 0;

    $currentSessions = dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_sessions WHERE date_only BETWEEN ? AND ?",
        [$startDate, $endDate]
    )['total'] ?? 0;

    $currentBounces = dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_sessions WHERE date_only BETWEEN ? AND ? AND is_bounce = 1",
        [$startDate, $endDate]
    )['total'] ?? 0;

    $currentDuration = dbFetchOne(
        "SELECT AVG(duration_seconds) as avg FROM analytics_sessions WHERE date_only BETWEEN ? AND ?",
        [$startDate, $endDate]
    )['avg'] ?? 0;

    // Previous period stats
    $prevPageviews = dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_pageviews WHERE date_only BETWEEN ? AND ?",
        [$prevStartDate, $prevEndDate]
    )['total'] ?? 0;

    $prevSessions = dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_sessions WHERE date_only BETWEEN ? AND ?",
        [$prevStartDate, $prevEndDate]
    )['total'] ?? 0;

    $prevBounces = dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_sessions WHERE date_only BETWEEN ? AND ? AND is_bounce = 1",
        [$prevStartDate, $prevEndDate]
    )['total'] ?? 0;

    $prevDuration = dbFetchOne(
        "SELECT AVG(duration_seconds) as avg FROM analytics_sessions WHERE date_only BETWEEN ? AND ?",
        [$prevStartDate, $prevEndDate]
    )['avg'] ?? 0;

    // Calculate metrics
    $bounceRate = $currentSessions > 0 ? round(($currentBounces / $currentSessions) * 100, 1) : 0;
    $prevBounceRate = $prevSessions > 0 ? round(($prevBounces / $prevSessions) * 100, 1) : 0;

    echo json_encode([
        'success' => true,
        'data' => [
            'visitors' => [
                'value' => (int)$currentSessions,
                'change' => getPercentageChange($currentSessions, $prevSessions),
                'previous' => (int)$prevSessions
            ],
            'pageviews' => [
                'value' => (int)$currentPageviews,
                'change' => getPercentageChange($currentPageviews, $prevPageviews),
                'previous' => (int)$prevPageviews
            ],
            'bounce_rate' => [
                'value' => $bounceRate,
                'change' => round($bounceRate - $prevBounceRate, 1),
                'previous' => $prevBounceRate
            ],
            'avg_duration' => [
                'value' => (int)$currentDuration,
                'formatted' => formatDuration((int)$currentDuration),
                'change' => getPercentageChange($currentDuration, $prevDuration),
                'previous' => (int)$prevDuration
            ]
        ],
        'period' => [
            'start' => $startDate,
            'end' => $endDate,
            'days' => $days
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch analytics data']);
}
