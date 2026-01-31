<?php
/**
 * Analytics Report Generator
 * Generate printable/downloadable reports
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/analytics.php';

requireLogin();

// Get date range
$preset = $_GET['preset'] ?? '30';
$startDate = $_GET['start'] ?? null;
$endDate = $_GET['end'] ?? null;

if (!$startDate || !$endDate) {
    $days = (int)$preset;
    $endDate = date('Y-m-d');
    $startDate = date('Y-m-d', strtotime("-{$days} days"));
}

// Validate dates
$startDate = date('Y-m-d', strtotime($startDate));
$endDate = date('Y-m-d', strtotime($endDate));

// Calculate previous period for comparison
$periodLength = (strtotime($endDate) - strtotime($startDate)) / 86400;
$prevEndDate = date('Y-m-d', strtotime($startDate) - 1);
$prevStartDate = date('Y-m-d', strtotime($prevEndDate) - $periodLength);

// Fetch all report data
$report = [];

// Summary stats - current period
$report['current'] = [
    'pageviews' => dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_pageviews WHERE date_only BETWEEN ? AND ?",
        [$startDate, $endDate]
    )['total'] ?? 0,
    'sessions' => dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_sessions WHERE date_only BETWEEN ? AND ?",
        [$startDate, $endDate]
    )['total'] ?? 0,
    'bounces' => dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_sessions WHERE date_only BETWEEN ? AND ? AND is_bounce = 1",
        [$startDate, $endDate]
    )['total'] ?? 0,
    'avg_duration' => dbFetchOne(
        "SELECT AVG(duration_seconds) as avg FROM analytics_sessions WHERE date_only BETWEEN ? AND ?",
        [$startDate, $endDate]
    )['avg'] ?? 0
];

// Summary stats - previous period
$report['previous'] = [
    'pageviews' => dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_pageviews WHERE date_only BETWEEN ? AND ?",
        [$prevStartDate, $prevEndDate]
    )['total'] ?? 0,
    'sessions' => dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_sessions WHERE date_only BETWEEN ? AND ?",
        [$prevStartDate, $prevEndDate]
    )['total'] ?? 0
];

$report['current']['bounce_rate'] = $report['current']['sessions'] > 0
    ? round(($report['current']['bounces'] / $report['current']['sessions']) * 100, 1)
    : 0;

// Top pages
$report['pages'] = dbFetchAll(
    "SELECT page_path, MAX(page_title) as page_title, COUNT(*) as views, COUNT(DISTINCT session_hash) as visitors
     FROM analytics_pageviews WHERE date_only BETWEEN ? AND ?
     GROUP BY page_path ORDER BY views DESC LIMIT 20",
    [$startDate, $endDate]
);

// Traffic sources
$report['sources'] = dbFetchAll(
    "SELECT referrer_type, COUNT(*) as pageviews, COUNT(DISTINCT session_hash) as visitors
     FROM analytics_pageviews WHERE date_only BETWEEN ? AND ? AND referrer_type != 'internal'
     GROUP BY referrer_type ORDER BY visitors DESC",
    [$startDate, $endDate]
);

// Top referrers
$report['referrers'] = dbFetchAll(
    "SELECT COALESCE(referrer_domain, 'Direct') as domain, referrer_type, COUNT(DISTINCT session_hash) as visitors
     FROM analytics_pageviews WHERE date_only BETWEEN ? AND ? AND referrer_type != 'internal'
     GROUP BY referrer_domain, referrer_type ORDER BY visitors DESC LIMIT 15",
    [$startDate, $endDate]
);

// Devices
$report['devices'] = dbFetchAll(
    "SELECT device_type, COUNT(DISTINCT session_hash) as visitors
     FROM analytics_pageviews WHERE date_only BETWEEN ? AND ? AND device_type != 'bot'
     GROUP BY device_type ORDER BY visitors DESC",
    [$startDate, $endDate]
);

// Browsers
$report['browsers'] = dbFetchAll(
    "SELECT COALESCE(browser_name, 'Unknown') as browser, COUNT(DISTINCT session_hash) as visitors
     FROM analytics_pageviews WHERE date_only BETWEEN ? AND ? AND device_type != 'bot'
     GROUP BY browser_name ORDER BY visitors DESC LIMIT 10",
    [$startDate, $endDate]
);

// Countries
$report['countries'] = dbFetchAll(
    "SELECT COALESCE(country_code, 'XX') as country_code, COUNT(DISTINCT session_hash) as visitors
     FROM analytics_pageviews WHERE date_only BETWEEN ? AND ? AND device_type != 'bot'
     GROUP BY country_code ORDER BY visitors DESC LIMIT 15",
    [$startDate, $endDate]
);

// UTM Campaigns
$report['campaigns'] = dbFetchAll(
    "SELECT utm_source, utm_medium, utm_campaign, COUNT(*) as pageviews, COUNT(DISTINCT session_hash) as visitors
     FROM analytics_pageviews WHERE date_only BETWEEN ? AND ? AND utm_source IS NOT NULL
     GROUP BY utm_source, utm_medium, utm_campaign ORDER BY visitors DESC LIMIT 15",
    [$startDate, $endDate]
);

// Daily breakdown
$report['daily'] = dbFetchAll(
    "SELECT date_only as date, COUNT(*) as pageviews, COUNT(DISTINCT session_hash) as visitors
     FROM analytics_pageviews WHERE date_only BETWEEN ? AND ?
     GROUP BY date_only ORDER BY date_only ASC",
    [$startDate, $endDate]
);

// Landing pages
$report['landing_pages'] = dbFetchAll(
    "SELECT landing_page, COUNT(*) as sessions
     FROM analytics_sessions WHERE date_only BETWEEN ? AND ?
     GROUP BY landing_page ORDER BY sessions DESC LIMIT 10",
    [$startDate, $endDate]
);

// Exit pages
$report['exit_pages'] = dbFetchAll(
    "SELECT exit_page, COUNT(*) as sessions
     FROM analytics_sessions WHERE date_only BETWEEN ? AND ?
     GROUP BY exit_page ORDER BY sessions DESC LIMIT 10",
    [$startDate, $endDate]
);

// Country names
$countryNames = [
    'GB' => 'United Kingdom', 'US' => 'United States', 'DE' => 'Germany',
    'FR' => 'France', 'ES' => 'Spain', 'IT' => 'Italy', 'NL' => 'Netherlands',
    'AU' => 'Australia', 'CA' => 'Canada', 'IE' => 'Ireland', 'IN' => 'India',
    'PT' => 'Portugal', 'PL' => 'Poland', 'SE' => 'Sweden', 'BE' => 'Belgium',
    'XX' => 'Unknown'
];

// Check if CSV export requested
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="analytics-report-' . $startDate . '-to-' . $endDate . '.csv"');

    $output = fopen('php://output', 'w');

    // Summary
    fputcsv($output, ['Analytics Report: ' . $startDate . ' to ' . $endDate]);
    fputcsv($output, []);
    fputcsv($output, ['Summary']);
    fputcsv($output, ['Metric', 'Value']);
    fputcsv($output, ['Unique Visitors', $report['current']['sessions']]);
    fputcsv($output, ['Page Views', $report['current']['pageviews']]);
    fputcsv($output, ['Bounce Rate', $report['current']['bounce_rate'] . '%']);
    fputcsv($output, ['Avg Session Duration', round($report['current']['avg_duration']) . ' seconds']);
    fputcsv($output, []);

    // Daily breakdown
    fputcsv($output, ['Daily Breakdown']);
    fputcsv($output, ['Date', 'Visitors', 'Pageviews']);
    foreach ($report['daily'] as $day) {
        fputcsv($output, [$day['date'], $day['visitors'], $day['pageviews']]);
    }
    fputcsv($output, []);

    // Top pages
    fputcsv($output, ['Top Pages']);
    fputcsv($output, ['Page', 'Views', 'Visitors']);
    foreach ($report['pages'] as $page) {
        fputcsv($output, [$page['page_path'], $page['views'], $page['visitors']]);
    }
    fputcsv($output, []);

    // Traffic sources
    fputcsv($output, ['Traffic Sources']);
    fputcsv($output, ['Source', 'Visitors']);
    foreach ($report['sources'] as $source) {
        fputcsv($output, [ucfirst($source['referrer_type']), $source['visitors']]);
    }
    fputcsv($output, []);

    // Referrers
    fputcsv($output, ['Top Referrers']);
    fputcsv($output, ['Domain', 'Type', 'Visitors']);
    foreach ($report['referrers'] as $ref) {
        fputcsv($output, [$ref['domain'], $ref['referrer_type'], $ref['visitors']]);
    }
    fputcsv($output, []);

    // Devices
    fputcsv($output, ['Devices']);
    fputcsv($output, ['Device', 'Visitors']);
    foreach ($report['devices'] as $device) {
        fputcsv($output, [ucfirst($device['device_type']), $device['visitors']]);
    }
    fputcsv($output, []);

    // Browsers
    fputcsv($output, ['Browsers']);
    fputcsv($output, ['Browser', 'Visitors']);
    foreach ($report['browsers'] as $browser) {
        fputcsv($output, [$browser['browser'], $browser['visitors']]);
    }
    fputcsv($output, []);

    // Countries
    fputcsv($output, ['Countries']);
    fputcsv($output, ['Country', 'Visitors']);
    foreach ($report['countries'] as $country) {
        $name = $countryNames[$country['country_code']] ?? $country['country_code'];
        fputcsv($output, [$name, $country['visitors']]);
    }
    fputcsv($output, []);

    // Campaigns
    if (!empty($report['campaigns'])) {
        fputcsv($output, ['UTM Campaigns']);
        fputcsv($output, ['Source', 'Medium', 'Campaign', 'Visitors', 'Pageviews']);
        foreach ($report['campaigns'] as $campaign) {
            fputcsv($output, [
                $campaign['utm_source'] ?? '-',
                $campaign['utm_medium'] ?? '-',
                $campaign['utm_campaign'] ?? '-',
                $campaign['visitors'],
                $campaign['pageviews']
            ]);
        }
    }

    fclose($output);
    exit;
}

$siteName = getSetting('site_name', SITE_NAME);
$isPrintView = isset($_GET['print']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Report - <?= e($siteName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 700: '#1e3a5f', 800: '#132337', 900: '#0c1929' },
                        orange: { 500: '#e85d04', 600: '#dc5503' }
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-break { page-break-before: always; }
            body { font-size: 12px; }
            .shadow { box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header (no print) -->
    <div class="no-print bg-navy-800 text-white py-4 px-6 mb-6">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold">Analytics Report</h1>
                <p class="text-sm opacity-75"><?= date('j M Y', strtotime($startDate)) ?> - <?= date('j M Y', strtotime($endDate)) ?></p>
            </div>
            <div class="flex gap-3">
                <a href="/admin/analytics.php" class="px-4 py-2 bg-navy-700 rounded hover:bg-navy-600">
                    &larr; Back to Dashboard
                </a>
                <a href="?start=<?= $startDate ?>&end=<?= $endDate ?>&export=csv" class="px-4 py-2 bg-green-600 rounded hover:bg-green-700">
                    Download CSV
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-orange-500 rounded hover:bg-orange-600">
                    Print / Save PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="max-w-6xl mx-auto px-6 pb-12">
        <!-- Report Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800"><?= e($siteName) ?></h1>
                    <h2 class="text-lg text-gray-600">Analytics Report</h2>
                    <p class="text-gray-500 mt-1"><?= date('j F Y', strtotime($startDate)) ?> - <?= date('j F Y', strtotime($endDate)) ?></p>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <p>Generated: <?= date('j M Y, H:i') ?></p>
                    <p class="no-print mt-4">
                        <label class="font-medium">Change period:</label>
                        <select onchange="updateDateRange(this.value)" class="ml-2 border rounded px-2 py-1">
                            <option value="7" <?= $preset === '7' ? 'selected' : '' ?>>Last 7 days</option>
                            <option value="14" <?= $preset === '14' ? 'selected' : '' ?>>Last 14 days</option>
                            <option value="30" <?= $preset === '30' ? 'selected' : '' ?>>Last 30 days</option>
                            <option value="90" <?= $preset === '90' ? 'selected' : '' ?>>Last 90 days</option>
                            <option value="365" <?= $preset === '365' ? 'selected' : '' ?>>Last 365 days</option>
                        </select>
                    </p>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500 uppercase">Unique Visitors</p>
                <p class="text-3xl font-bold text-gray-800"><?= number_format($report['current']['sessions']) ?></p>
                <?php $change = $report['previous']['sessions'] > 0 ? round((($report['current']['sessions'] - $report['previous']['sessions']) / $report['previous']['sessions']) * 100, 1) : 0; ?>
                <p class="text-sm <?= $change >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $change >= 0 ? '↑' : '↓' ?> <?= abs($change) ?>% vs previous period
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500 uppercase">Page Views</p>
                <p class="text-3xl font-bold text-gray-800"><?= number_format($report['current']['pageviews']) ?></p>
                <?php $change = $report['previous']['pageviews'] > 0 ? round((($report['current']['pageviews'] - $report['previous']['pageviews']) / $report['previous']['pageviews']) * 100, 1) : 0; ?>
                <p class="text-sm <?= $change >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $change >= 0 ? '↑' : '↓' ?> <?= abs($change) ?>% vs previous period
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500 uppercase">Bounce Rate</p>
                <p class="text-3xl font-bold text-gray-800"><?= $report['current']['bounce_rate'] ?>%</p>
                <p class="text-sm text-gray-500"><?= number_format($report['current']['bounces']) ?> single-page sessions</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500 uppercase">Avg Session Duration</p>
                <p class="text-3xl font-bold text-gray-800"><?= gmdate('i:s', (int)$report['current']['avg_duration']) ?></p>
                <p class="text-sm text-gray-500"><?= round($report['current']['avg_duration']) ?> seconds</p>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Top Pages -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-gray-800">Top Pages</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Page</th>
                            <th class="px-4 py-2 text-right">Views</th>
                            <th class="px-4 py-2 text-right">Visitors</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach (array_slice($report['pages'], 0, 10) as $page): ?>
                        <tr>
                            <td class="px-4 py-2 truncate max-w-xs" title="<?= e($page['page_path']) ?>"><?= e($page['page_path']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($page['views']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($page['visitors']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Traffic Sources -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-gray-800">Traffic Sources</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Source</th>
                            <th class="px-4 py-2 text-right">Visitors</th>
                            <th class="px-4 py-2 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php
                        $totalSourceVisitors = array_sum(array_column($report['sources'], 'visitors'));
                        foreach ($report['sources'] as $source):
                            $pct = $totalSourceVisitors > 0 ? round(($source['visitors'] / $totalSourceVisitors) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td class="px-4 py-2 capitalize"><?= e($source['referrer_type']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($source['visitors']) ?></td>
                            <td class="px-4 py-2 text-right"><?= $pct ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Referrers and Devices -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Top Referrers -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-gray-800">Top Referrers</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Domain</th>
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2 text-right">Visitors</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach (array_slice($report['referrers'], 0, 10) as $ref): ?>
                        <tr>
                            <td class="px-4 py-2"><?= e($ref['domain']) ?></td>
                            <td class="px-4 py-2 capitalize"><?= e($ref['referrer_type']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($ref['visitors']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Devices & Browsers -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-gray-800">Devices & Browsers</h3>
                </div>
                <div class="p-4">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Devices</h4>
                    <div class="space-y-2 mb-4">
                        <?php
                        $totalDevices = array_sum(array_column($report['devices'], 'visitors'));
                        foreach ($report['devices'] as $device):
                            $pct = $totalDevices > 0 ? round(($device['visitors'] / $totalDevices) * 100, 1) : 0;
                        ?>
                        <div class="flex items-center justify-between text-sm">
                            <span class="capitalize"><?= e($device['device_type']) ?></span>
                            <span><?= $pct ?>% (<?= number_format($device['visitors']) ?>)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full" style="width: <?= $pct ?>%"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h4 class="text-sm font-medium text-gray-600 mb-2 mt-4">Top Browsers</h4>
                    <div class="space-y-1">
                        <?php foreach (array_slice($report['browsers'], 0, 5) as $browser): ?>
                        <div class="flex items-center justify-between text-sm">
                            <span><?= e($browser['browser']) ?></span>
                            <span><?= number_format($browser['visitors']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Countries and Campaigns -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 print-break">
            <!-- Countries -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-gray-800">Countries</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Country</th>
                            <th class="px-4 py-2 text-right">Visitors</th>
                            <th class="px-4 py-2 text-right">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php
                        $totalCountryVisitors = array_sum(array_column($report['countries'], 'visitors'));
                        foreach (array_slice($report['countries'], 0, 10) as $country):
                            $name = $countryNames[$country['country_code']] ?? $country['country_code'];
                            $pct = $totalCountryVisitors > 0 ? round(($country['visitors'] / $totalCountryVisitors) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td class="px-4 py-2"><?= e($name) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($country['visitors']) ?></td>
                            <td class="px-4 py-2 text-right"><?= $pct ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- UTM Campaigns -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-gray-800">UTM Campaigns</h3>
                </div>
                <?php if (empty($report['campaigns'])): ?>
                <div class="p-4 text-gray-500 text-center">No campaign data for this period</div>
                <?php else: ?>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Source / Medium</th>
                            <th class="px-4 py-2 text-left">Campaign</th>
                            <th class="px-4 py-2 text-right">Visitors</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach (array_slice($report['campaigns'], 0, 10) as $campaign): ?>
                        <tr>
                            <td class="px-4 py-2"><?= e($campaign['utm_source'] ?? '-') ?> / <?= e($campaign['utm_medium'] ?? '-') ?></td>
                            <td class="px-4 py-2"><?= e($campaign['utm_campaign'] ?? '-') ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($campaign['visitors']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Landing & Exit Pages -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Landing Pages -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-gray-800">Top Landing Pages</h3>
                    <p class="text-xs text-gray-500">First page visitors see</p>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Page</th>
                            <th class="px-4 py-2 text-right">Sessions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($report['landing_pages'] as $page): ?>
                        <tr>
                            <td class="px-4 py-2 truncate max-w-xs"><?= e($page['landing_page']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($page['sessions']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Exit Pages -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h3 class="font-semibold text-gray-800">Top Exit Pages</h3>
                    <p class="text-xs text-gray-500">Last page before leaving</p>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Page</th>
                            <th class="px-4 py-2 text-right">Sessions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($report['exit_pages'] as $page): ?>
                        <tr>
                            <td class="px-4 py-2 truncate max-w-xs"><?= e($page['exit_page']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($page['sessions']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daily Breakdown -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b">
                <h3 class="font-semibold text-gray-800">Daily Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-right">Visitors</th>
                            <th class="px-4 py-2 text-right">Pageviews</th>
                            <th class="px-4 py-2 text-right">Pages/Visit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($report['daily'] as $day):
                            $pagesPerVisit = $day['visitors'] > 0 ? round($day['pageviews'] / $day['visitors'], 1) : 0;
                        ?>
                        <tr>
                            <td class="px-4 py-2"><?= date('D, j M Y', strtotime($day['date'])) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($day['visitors']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($day['pageviews']) ?></td>
                            <td class="px-4 py-2 text-right"><?= $pagesPerVisit ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td class="px-4 py-2">Total</td>
                            <td class="px-4 py-2 text-right"><?= number_format($report['current']['sessions']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($report['current']['pageviews']) ?></td>
                            <td class="px-4 py-2 text-right"><?= $report['current']['sessions'] > 0 ? round($report['current']['pageviews'] / $report['current']['sessions'], 1) : 0 ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500">
            <p>Report generated by <?= e($siteName) ?> Analytics</p>
            <p><?= date('j F Y, H:i:s') ?></p>
        </div>
    </div>

    <script>
        function updateDateRange(days) {
            window.location.href = '?preset=' + days;
        }
    </script>
</body>
</html>
