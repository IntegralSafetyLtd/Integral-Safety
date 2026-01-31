<?php
/**
 * Analytics API - Session Details
 * Returns full page journey for a specific session
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

$sessionHash = $_GET['hash'] ?? null;

if (!$sessionHash) {
    http_response_code(400);
    echo json_encode(['error' => 'Session hash required']);
    exit;
}

try {
    // Get session summary
    $session = dbFetchOne(
        "SELECT * FROM analytics_sessions WHERE session_hash = ?",
        [$sessionHash]
    );

    if (!$session) {
        http_response_code(404);
        echo json_encode(['error' => 'Session not found']);
        exit;
    }

    // Get all pageviews for this session, ordered by time
    $pageviews = dbFetchAll(
        "SELECT
            id,
            page_path,
            page_title,
            referrer_url,
            referrer_domain,
            referrer_type,
            utm_source,
            utm_medium,
            utm_campaign,
            device_type,
            browser_name,
            os_name,
            country_code,
            viewed_at
         FROM analytics_pageviews
         WHERE session_hash = ?
         ORDER BY viewed_at ASC",
        [$sessionHash]
    );

    // Calculate time on each page
    $journey = [];
    $prevTime = null;

    foreach ($pageviews as $i => $pv) {
        $currentTime = strtotime($pv['viewed_at']);

        // Calculate time spent on previous page
        if ($prevTime !== null && isset($journey[count($journey) - 1])) {
            $timeOnPage = $currentTime - $prevTime;
            $journey[count($journey) - 1]['time_on_page'] = $timeOnPage;
            $journey[count($journey) - 1]['time_on_page_formatted'] = formatTimeOnPage($timeOnPage);
        }

        $journey[] = [
            'step' => $i + 1,
            'page_path' => $pv['page_path'],
            'page_title' => $pv['page_title'],
            'viewed_at' => $pv['viewed_at'],
            'viewed_at_formatted' => date('H:i:s', $currentTime),
            'referrer' => $pv['referrer_domain'],
            'referrer_type' => $pv['referrer_type'],
            'is_entry' => $i === 0,
            'is_exit' => $i === count($pageviews) - 1,
            'time_on_page' => null,
            'time_on_page_formatted' => $i === count($pageviews) - 1 ? 'Current/Exit' : null
        ];

        $prevTime = $currentTime;
    }

    // Country name
    $countryNames = [
        'GB' => 'United Kingdom', 'US' => 'United States', 'DE' => 'Germany',
        'FR' => 'France', 'ES' => 'Spain', 'IT' => 'Italy', 'NL' => 'Netherlands',
        'AU' => 'Australia', 'CA' => 'Canada', 'IE' => 'Ireland', 'IN' => 'India'
    ];

    $firstPageview = $pageviews[0] ?? null;

    echo json_encode([
        'success' => true,
        'data' => [
            'session' => [
                'hash' => substr($sessionHash, 0, 8) . '...',
                'full_hash' => $sessionHash,
                'first_seen' => $session['first_seen'],
                'last_seen' => $session['last_seen'],
                'duration_seconds' => (int)$session['duration_seconds'],
                'duration_formatted' => formatDuration((int)$session['duration_seconds']),
                'pageviews' => (int)$session['pageviews'],
                'is_bounce' => (bool)$session['is_bounce'],
                'landing_page' => $session['landing_page'],
                'exit_page' => $session['exit_page'],
                'device_type' => $session['device_type'],
                'country_code' => $session['country_code'],
                'country_name' => $countryNames[$session['country_code']] ?? $session['country_code'] ?? 'Unknown',
                'referrer_type' => $session['referrer_type'],
                'utm_source' => $session['utm_source']
            ],
            'visitor' => [
                'device' => ucfirst($firstPageview['device_type'] ?? 'Unknown'),
                'browser' => $firstPageview['browser_name'] ?? 'Unknown',
                'os' => $firstPageview['os_name'] ?? 'Unknown',
                'initial_referrer' => $firstPageview['referrer_domain'] ?? 'Direct',
                'initial_referrer_type' => $firstPageview['referrer_type'] ?? 'direct'
            ],
            'journey' => $journey,
            'utm' => [
                'source' => $firstPageview['utm_source'] ?? null,
                'medium' => $firstPageview['utm_medium'] ?? null,
                'campaign' => $firstPageview['utm_campaign'] ?? null
            ]
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch session data', 'message' => $e->getMessage()]);
}

/**
 * Format time on page
 */
function formatTimeOnPage($seconds) {
    if ($seconds < 5) {
        return '< 5s';
    } elseif ($seconds < 60) {
        return $seconds . 's';
    } elseif ($seconds < 3600) {
        $mins = floor($seconds / 60);
        $secs = $seconds % 60;
        return $mins . 'm ' . $secs . 's';
    } else {
        return '> 1h';
    }
}

/**
 * Format duration
 */
function formatDuration($seconds) {
    if ($seconds < 60) {
        return $seconds . ' seconds';
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
