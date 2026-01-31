<?php
/**
 * Analytics Cleanup Cron Job
 * Removes old analytics data based on retention settings
 *
 * Run daily: 0 2 * * * php /path/to/cron/cleanup-analytics.php
 */

// Ensure running from CLI
if (php_sapi_name() !== 'cli') {
    die('This script must be run from the command line');
}

require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$startTime = microtime(true);
echo "Analytics Cleanup Started: " . date('Y-m-d H:i:s') . "\n";

// Get retention period from settings
$retentionDays = (int) getSetting('analytics_data_retention_days', 365);
$cutoffDate = date('Y-m-d', strtotime("-{$retentionDays} days"));

echo "Retention period: {$retentionDays} days\n";
echo "Cutoff date: {$cutoffDate}\n";

try {
    // Delete old pageviews
    $result = dbExecute(
        "DELETE FROM analytics_pageviews WHERE date_only < ?",
        [$cutoffDate]
    );
    $deletedPageviews = db()->rowCount();
    echo "Deleted pageviews: {$deletedPageviews}\n";

    // Delete old sessions
    $result = dbExecute(
        "DELETE FROM analytics_sessions WHERE date_only < ?",
        [$cutoffDate]
    );
    $deletedSessions = db()->rowCount();
    echo "Deleted sessions: {$deletedSessions}\n";

    // Keep daily stats longer (useful for year-over-year comparisons)
    // Only delete stats older than 2 years
    $statsRetentionDays = max($retentionDays * 2, 730);
    $statsCutoffDate = date('Y-m-d', strtotime("-{$statsRetentionDays} days"));

    $result = dbExecute(
        "DELETE FROM analytics_daily_stats WHERE stat_date < ?",
        [$statsCutoffDate]
    );
    $deletedStats = db()->rowCount();
    echo "Deleted daily stats: {$deletedStats}\n";

    // Optimise tables after deletion (only if significant rows deleted)
    if ($deletedPageviews > 1000) {
        echo "Optimising analytics_pageviews table...\n";
        db()->exec("OPTIMIZE TABLE analytics_pageviews");
    }

    if ($deletedSessions > 1000) {
        echo "Optimising analytics_sessions table...\n";
        db()->exec("OPTIMIZE TABLE analytics_sessions");
    }

    $duration = round(microtime(true) - $startTime, 2);
    echo "Cleanup completed in {$duration} seconds\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
