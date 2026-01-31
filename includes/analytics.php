<?php
/**
 * Server-Side Analytics System
 * GDPR-compliant, cookie-free tracking
 */

/**
 * Track a page view
 * Call this at the top of each public page (after session/config)
 */
function trackPageview($pageTitle = null) {
    // Check if analytics is enabled
    if (getSetting('analytics_enabled', '1') !== '1') {
        return false;
    }

    // Exclude logged-in admins if setting enabled
    $loggedIn = function_exists('isLoggedIn') ? isLoggedIn() : !empty($_SESSION['user_id']);
    if (getSetting('analytics_exclude_admins', '1') === '1' && $loggedIn) {
        return false;
    }

    // Get device info
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $deviceInfo = detectDevice($userAgent);

    // Exclude bots if setting enabled
    if (getSetting('analytics_exclude_bots', '1') === '1' && $deviceInfo['type'] === 'bot') {
        return false;
    }

    // Get page info
    $pagePath = $_SERVER['REQUEST_URI'] ?? '/';
    $pagePath = strtok($pagePath, '?'); // Remove query string for cleaner paths

    // Exclude automated/probe paths (not real page visits)
    $excludedPaths = [
        '/autodiscover',
        '/.well-known',
        '/wp-admin',
        '/wp-login',
        '/wp-content',
        '/xmlrpc.php',
        '/wp-includes',
        '/phpmyadmin',
        '/admin/api/',  // Our own API calls
        '/cgi-bin',
        '/cpanel',
        '/webmail',
        '/.env',
        '/config',
        '/vendor',
        '/node_modules',
    ];

    $pathLower = strtolower($pagePath);
    foreach ($excludedPaths as $excluded) {
        if (strpos($pathLower, $excluded) === 0) {
            return false;
        }
    }

    // Generate session hash (cookie-free identification)

    // Get referrer info
    $referrerInfo = parseReferrer();

    // Get UTM parameters
    $utmParams = getUtmParameters();

    // Get country code (basic, from Accept-Language header)
    $countryCode = detectCountry();

    try {
        // Record the pageview
        dbExecute(
            "INSERT INTO analytics_pageviews
            (session_hash, page_path, page_title, referrer_url, referrer_domain, referrer_type,
             utm_source, utm_medium, utm_campaign, utm_term, utm_content,
             device_type, browser_name, os_name, country_code)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $sessionHash,
                substr($pagePath, 0, 500),
                $pageTitle ? substr($pageTitle, 0, 255) : null,
                $referrerInfo['url'] ? substr($referrerInfo['url'], 0, 1000) : null,
                $referrerInfo['domain'] ? substr($referrerInfo['domain'], 0, 255) : null,
                $referrerInfo['type'],
                $utmParams['source'] ? substr($utmParams['source'], 0, 100) : null,
                $utmParams['medium'] ? substr($utmParams['medium'], 0, 100) : null,
                $utmParams['campaign'] ? substr($utmParams['campaign'], 0, 100) : null,
                $utmParams['term'] ? substr($utmParams['term'], 0, 100) : null,
                $utmParams['content'] ? substr($utmParams['content'], 0, 100) : null,
                $deviceInfo['type'],
                $deviceInfo['browser'] ? substr($deviceInfo['browser'], 0, 50) : null,
                $deviceInfo['os'] ? substr($deviceInfo['os'], 0, 50) : null,
                $countryCode
            ]
        );

        // Update or create session record
        updateSession($sessionHash, $pagePath, $deviceInfo['type'], $countryCode, $referrerInfo['type'], $utmParams['source']);

        return true;
    } catch (Exception $e) {
        error_log("Analytics tracking error: " . $e->getMessage());
        return false;
    }
}

/**
 * Generate a session hash (cookie-free, GDPR compliant)
 * Same visitor on same day = same session
 */
function generateSessionHash() {
    // Get anonymised IP (last octet zeroed)
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $anonymisedIp = anonymiseIp($ip);

    // Get user agent
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Get today's date
    $date = date('Y-m-d');

    // Get secret key from config
    $secret = defined('SECURE_KEY') ? SECURE_KEY : 'default_secret_change_me';

    // Create hash
    return hash('sha256', $date . $anonymisedIp . $userAgent . $secret);
}

/**
 * Anonymise IP address (zero last octet for IPv4, last 80 bits for IPv6)
 */
function anonymiseIp($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        // IPv4: zero last octet
        $parts = explode('.', $ip);
        $parts[3] = '0';
        return implode('.', $parts);
    } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        // IPv6: zero last 80 bits (last 5 groups)
        $parts = explode(':', $ip);
        for ($i = 3; $i < 8; $i++) {
            if (isset($parts[$i])) {
                $parts[$i] = '0';
            }
        }
        return implode(':', $parts);
    }
    return '0.0.0.0';
}

/**
 * Detect device type, browser and OS from user agent
 */
function detectDevice($userAgent) {
    $ua = strtolower($userAgent);

    // Detect bots and automated clients
    $botPatterns = [
        // Search engine bots
        'bot', 'crawler', 'spider', 'slurp', 'googlebot', 'bingbot', 'yandex',
        'baidu', 'duckduck', 'applebot',
        // Social media bots
        'facebookexternalhit', 'linkedinbot', 'twitterbot', 'whatsapp', 'telegrambot',
        // SEO tools
        'semrush', 'ahrefs', 'mj12bot', 'dotbot', 'petalbot', 'screaming frog',
        // AI bots
        'bytespider', 'gptbot', 'claudebot', 'anthropic', 'chatgpt',
        // Automated clients (not real browsers)
        'microsoft office', 'ms office', 'outlook', 'thunderbird',
        'wget', 'curl', 'python', 'java/', 'apache-httpclient', 'okhttp',
        'postman', 'insomnia', 'axios', 'node-fetch', 'go-http-client',
        // Monitoring & security scanners
        'pingdom', 'uptimerobot', 'statuscake', 'site24x7', 'newrelic',
        'nessus', 'nikto', 'nmap', 'masscan', 'zgrab',
        // Other automated
        'headless', 'phantom', 'selenium', 'puppeteer', 'playwright'
    ];

    foreach ($botPatterns as $pattern) {
        if (strpos($ua, $pattern) !== false) {
            return ['type' => 'bot', 'browser' => 'Bot', 'os' => null];
        }
    }

    // Detect device type
    $type = 'desktop';
    if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|iemobile/i', $ua)) {
        $type = 'mobile';
    } elseif (preg_match('/tablet|ipad|playbook|silk/i', $ua)) {
        $type = 'tablet';
    }

    // Detect browser
    $browser = 'Unknown';
    if (preg_match('/edg/i', $ua)) {
        $browser = 'Edge';
    } elseif (preg_match('/opr|opera/i', $ua)) {
        $browser = 'Opera';
    } elseif (preg_match('/chrome|crios/i', $ua)) {
        $browser = 'Chrome';
    } elseif (preg_match('/firefox|fxios/i', $ua)) {
        $browser = 'Firefox';
    } elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) {
        $browser = 'Safari';
    } elseif (preg_match('/msie|trident/i', $ua)) {
        $browser = 'Internet Explorer';
    }

    // Detect OS
    $os = 'Unknown';
    if (preg_match('/windows nt 10/i', $ua)) {
        $os = 'Windows 10/11';
    } elseif (preg_match('/windows/i', $ua)) {
        $os = 'Windows';
    } elseif (preg_match('/macintosh|mac os x/i', $ua)) {
        $os = 'macOS';
    } elseif (preg_match('/iphone|ipad|ipod/i', $ua)) {
        $os = 'iOS';
    } elseif (preg_match('/android/i', $ua)) {
        $os = 'Android';
    } elseif (preg_match('/linux/i', $ua)) {
        $os = 'Linux';
    }

    return ['type' => $type, 'browser' => $browser, 'os' => $os];
}

/**
 * Parse referrer URL and determine type
 */
function parseReferrer() {
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';

    if (empty($referrer)) {
        return ['url' => null, 'domain' => null, 'type' => 'direct'];
    }

    $parsed = parse_url($referrer);
    $domain = $parsed['host'] ?? '';

    // Check if internal
    $siteHost = parse_url(SITE_URL, PHP_URL_HOST);
    if ($domain === $siteHost || $domain === 'www.' . $siteHost) {
        return ['url' => $referrer, 'domain' => $domain, 'type' => 'internal'];
    }

    // Detect referrer type
    $type = 'referral';

    // Search engines
    $searchEngines = ['google', 'bing', 'yahoo', 'duckduckgo', 'baidu', 'yandex', 'ecosia'];
    foreach ($searchEngines as $engine) {
        if (strpos($domain, $engine) !== false) {
            $type = 'search';
            break;
        }
    }

    // Social networks
    if ($type === 'referral') {
        $socialNetworks = ['facebook', 'twitter', 'linkedin', 'instagram', 'pinterest', 'youtube', 'tiktok', 'reddit', 't.co', 'x.com'];
        foreach ($socialNetworks as $network) {
            if (strpos($domain, $network) !== false) {
                $type = 'social';
                break;
            }
        }
    }

    return ['url' => $referrer, 'domain' => $domain, 'type' => $type];
}

/**
 * Get UTM parameters from URL
 */
function getUtmParameters() {
    return [
        'source' => $_GET['utm_source'] ?? null,
        'medium' => $_GET['utm_medium'] ?? null,
        'campaign' => $_GET['utm_campaign'] ?? null,
        'term' => $_GET['utm_term'] ?? null,
        'content' => $_GET['utm_content'] ?? null
    ];
}

/**
 * Basic country detection from Accept-Language header
 */
function detectCountry() {
    $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if (empty($acceptLang)) {
        return null;
    }

    // Map language codes to country codes (simplified)
    $langCountryMap = [
        'en-gb' => 'GB', 'en-us' => 'US', 'en-au' => 'AU', 'en-ca' => 'CA',
        'de' => 'DE', 'fr' => 'FR', 'es' => 'ES', 'it' => 'IT', 'nl' => 'NL',
        'pt' => 'PT', 'pl' => 'PL', 'ru' => 'RU', 'ja' => 'JP', 'zh' => 'CN',
        'ko' => 'KR', 'ar' => 'SA', 'hi' => 'IN', 'tr' => 'TR', 'sv' => 'SE',
        'da' => 'DK', 'no' => 'NO', 'fi' => 'FI', 'cs' => 'CZ', 'hu' => 'HU'
    ];

    // Parse first language preference
    $langs = explode(',', $acceptLang);
    $firstLang = strtolower(trim(explode(';', $langs[0])[0]));

    // Try exact match first
    if (isset($langCountryMap[$firstLang])) {
        return $langCountryMap[$firstLang];
    }

    // Try just the language code (first 2 chars)
    $shortLang = substr($firstLang, 0, 2);
    if (isset($langCountryMap[$shortLang])) {
        return $langCountryMap[$shortLang];
    }

    // Default to GB for English
    if ($shortLang === 'en') {
        return 'GB';
    }

    return null;
}

/**
 * Update or create session record
 */
function updateSession($sessionHash, $pagePath, $deviceType, $countryCode, $referrerType, $utmSource) {
    // Check if session exists
    $existing = dbFetchOne("SELECT id, pageviews FROM analytics_sessions WHERE session_hash = ?", [$sessionHash]);

    if ($existing) {
        // Update existing session
        dbExecute(
            "UPDATE analytics_sessions
             SET last_seen = NOW(), pageviews = pageviews + 1, exit_page = ?
             WHERE session_hash = ?",
            [substr($pagePath, 0, 500), $sessionHash]
        );
    } else {
        // Create new session
        dbExecute(
            "INSERT INTO analytics_sessions
             (session_hash, first_seen, last_seen, pageviews, landing_page, exit_page,
              device_type, country_code, referrer_type, utm_source)
             VALUES (?, NOW(), NOW(), 1, ?, ?, ?, ?, ?, ?)",
            [
                $sessionHash,
                substr($pagePath, 0, 500),
                substr($pagePath, 0, 500),
                $deviceType,
                $countryCode,
                $referrerType,
                $utmSource ? substr($utmSource, 0, 100) : null
            ]
        );
    }
}

/**
 * Get analytics summary for a date range
 */
function getAnalyticsSummary($startDate, $endDate) {
    // Try to use pre-computed stats first
    $stats = dbFetchOne(
        "SELECT
            SUM(total_pageviews) as pageviews,
            SUM(unique_sessions) as sessions,
            SUM(bounced_sessions) as bounced,
            AVG(bounce_rate) as avg_bounce_rate,
            AVG(avg_session_duration) as avg_duration
         FROM analytics_daily_stats
         WHERE stat_date BETWEEN ? AND ?",
        [$startDate, $endDate]
    );

    // If no pre-computed data, calculate from raw data
    if (empty($stats['pageviews'])) {
        $stats = dbFetchOne(
            "SELECT
                COUNT(*) as pageviews,
                COUNT(DISTINCT session_hash) as sessions
             FROM analytics_pageviews
             WHERE date_only BETWEEN ? AND ?",
            [$startDate, $endDate]
        );

        $sessionStats = dbFetchOne(
            "SELECT
                COUNT(*) as total,
                SUM(is_bounce) as bounced,
                AVG(duration_seconds) as avg_duration
             FROM analytics_sessions
             WHERE date_only BETWEEN ? AND ?",
            [$startDate, $endDate]
        );

        $stats['bounced'] = $sessionStats['bounced'] ?? 0;
        $stats['avg_bounce_rate'] = $stats['sessions'] > 0
            ? round(($sessionStats['bounced'] / $stats['sessions']) * 100, 1)
            : 0;
        $stats['avg_duration'] = $sessionStats['avg_duration'] ?? 0;
    }

    return $stats;
}

/**
 * Format duration in seconds to human readable string
 */
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

/**
 * Get percentage change between two values
 */
function getPercentageChange($current, $previous) {
    if ($previous == 0) {
        return $current > 0 ? 100 : 0;
    }
    return round((($current - $previous) / $previous) * 100, 1);
}
