<?php
/**
 * Analytics Aggregation Cron Job
 * Pre-computes daily statistics for faster dashboard loading
 *
 * Run daily after midnight: 5 0 * * * php /path/to/cron/aggregate-analytics.php
 */

// Ensure running from CLI
if (php_sapi_name() !== 'cli') {
    die('This script must be run from the command line');
}

require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$startTime = microtime(true);
echo "Analytics Aggregation Started: " . date('Y-m-d H:i:s') . "\n";

// Aggregate yesterday's data by default
$date = isset($argv[1]) ? $argv[1] : date('Y-m-d', strtotime('yesterday'));

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo "ERROR: Invalid date format. Use YYYY-MM-DD\n";
    exit(1);
}

echo "Aggregating data for: {$date}\n";

try {
    // Calculate statistics for the date
    $pageviewStats = dbFetchOne(
        "SELECT
            COUNT(*) as total_pageviews,
            COUNT(DISTINCT session_hash) as unique_sessions
         FROM analytics_pageviews
         WHERE date_only = ?",
        [$date]
    );

    $sessionStats = dbFetchOne(
        "SELECT
            SUM(is_bounce) as bounced_sessions,
            SUM(duration_seconds) as total_duration
         FROM analytics_sessions
         WHERE date_only = ?",
        [$date]
    );

    $totalPageviews = (int) ($pageviewStats['total_pageviews'] ?? 0);
    $uniqueSessions = (int) ($pageviewStats['unique_sessions'] ?? 0);
    $bouncedSessions = (int) ($sessionStats['bounced_sessions'] ?? 0);
    $totalDuration = (int) ($sessionStats['total_duration'] ?? 0);

    echo "Pageviews: {$totalPageviews}\n";
    echo "Sessions: {$uniqueSessions}\n";
    echo "Bounced: {$bouncedSessions}\n";
    echo "Total duration: {$totalDuration}s\n";

    // Insert or update daily stats
    dbExecute(
        "INSERT INTO analytics_daily_stats
         (stat_date, total_pageviews, unique_sessions, bounced_sessions, total_duration_seconds)
         VALUES (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
         total_pageviews = VALUES(total_pageviews),
         unique_sessions = VALUES(unique_sessions),
         bounced_sessions = VALUES(bounced_sessions),
         total_duration_seconds = VALUES(total_duration_seconds),
         updated_at = NOW()",
        [$date, $totalPageviews, $uniqueSessions, $bouncedSessions, $totalDuration]
    );

    echo "Daily stats saved successfully\n";

    // Optionally backfill missing dates (run with --backfill flag)
    if (in_array('--backfill', $argv)) {
        echo "\nBackfilling missing dates...\n";

        // Find dates with pageviews but no daily stats
        $missingDates = dbFetchAll(
            "SELECT DISTINCT p.date_only
             FROM analytics_pageviews p
             LEFT JOIN analytics_daily_stats d ON p.date_only = d.stat_date
             WHERE d.stat_date IS NULL
             AND p.date_only < ?
             ORDER BY p.date_only ASC
             LIMIT 30",
            [$date]
        );

        foreach ($missingDates as $row) {
            $missingDate = $row['date_only'];
            echo "Backfilling: {$missingDate}\n";

            // Recursively call this script for each missing date
            $pageviewStats = dbFetchOne(
                "SELECT
                    COUNT(*) as total_pageviews,
                    COUNT(DISTINCT session_hash) as unique_sessions
                 FROM analytics_pageviews
                 WHERE date_only = ?",
                [$missingDate]
            );

            $sessionStats = dbFetchOne(
                "SELECT
                    SUM(is_bounce) as bounced_sessions,
                    SUM(duration_seconds) as total_duration
                 FROM analytics_sessions
                 WHERE date_only = ?",
                [$missingDate]
            );

            dbExecute(
                "INSERT INTO analytics_daily_stats
                 (stat_date, total_pageviews, unique_sessions, bounced_sessions, total_duration_seconds)
                 VALUES (?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                 total_pageviews = VALUES(total_pageviews),
                 unique_sessions = VALUES(unique_sessions),
                 bounced_sessions = VALUES(bounced_sessions),
                 total_duration_seconds = VALUES(total_duration_seconds)",
                [
                    $missingDate,
                    (int) ($pageviewStats['total_pageviews'] ?? 0),
                    (int) ($pageviewStats['unique_sessions'] ?? 0),
                    (int) ($sessionStats['bounced_sessions'] ?? 0),
                    (int) ($sessionStats['total_duration'] ?? 0)
                ]
            );
        }

        echo "Backfill completed for " . count($missingDates) . " dates\n";
    }

    $duration = round(microtime(true) - $startTime, 2);
    echo "Aggregation completed in {$duration} seconds\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
