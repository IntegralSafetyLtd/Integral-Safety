<?php
/**
 * Analytics - Acquisition Drill-Down
 * Detailed referrer and traffic source analysis
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

// Date range
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
$startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d', strtotime("-{$days} days"));
$endDate = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');

// Filter parameters
$filterType = $_GET['type'] ?? null; // direct, search, social, referral
$filterDomain = $_GET['domain'] ?? null;

// Build date params for links
$dateParams = "days={$days}&start={$startDate}&end={$endDate}";

// Fetch data based on filters
$data = [];

if ($filterDomain) {
    // Drill into specific domain
    $data['domain'] = $filterDomain;
    $data['domain_type'] = $_GET['dtype'] ?? 'referral';

    // Sessions from this domain
    $data['sessions'] = dbFetchOne(
        "SELECT COUNT(DISTINCT session_hash) as total FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND referrer_domain = ?",
        [$startDate, $endDate, $filterDomain]
    )['total'] ?? 0;

    // Pageviews from this domain
    $data['pageviews'] = dbFetchOne(
        "SELECT COUNT(*) as total FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND referrer_domain = ?",
        [$startDate, $endDate, $filterDomain]
    )['total'] ?? 0;

    // Landing pages from this domain
    $data['landing_pages'] = dbFetchAll(
        "SELECT s.landing_page, COUNT(*) as sessions,
                SUM(s.pageviews) as total_pageviews,
                AVG(s.duration_seconds) as avg_duration,
                SUM(s.is_bounce) as bounces
         FROM analytics_sessions s
         INNER JOIN analytics_pageviews p ON s.session_hash = p.session_hash
         WHERE p.date_only BETWEEN ? AND ?
         AND p.referrer_domain = ?
         AND p.id = (SELECT MIN(id) FROM analytics_pageviews WHERE session_hash = s.session_hash)
         GROUP BY s.landing_page
         ORDER BY sessions DESC
         LIMIT 20",
        [$startDate, $endDate, $filterDomain]
    );

    // Daily trend from this domain
    $data['daily'] = dbFetchAll(
        "SELECT date_only as date, COUNT(DISTINCT session_hash) as sessions
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND referrer_domain = ?
         GROUP BY date_only ORDER BY date_only",
        [$startDate, $endDate, $filterDomain]
    );

    // Pages visited by visitors from this domain
    $data['pages_visited'] = dbFetchAll(
        "SELECT page_path, COUNT(*) as views, COUNT(DISTINCT session_hash) as visitors
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ?
         AND session_hash IN (
             SELECT DISTINCT session_hash FROM analytics_pageviews
             WHERE referrer_domain = ? AND date_only BETWEEN ? AND ?
         )
         GROUP BY page_path ORDER BY views DESC LIMIT 20",
        [$startDate, $endDate, $filterDomain, $startDate, $endDate]
    );

} elseif ($filterType) {
    // Drill into source type
    $data['type'] = $filterType;
    $typeLabel = ucfirst($filterType);

    // Total sessions for this type
    $data['sessions'] = dbFetchOne(
        "SELECT COUNT(DISTINCT session_hash) as total FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND referrer_type = ?",
        [$startDate, $endDate, $filterType]
    )['total'] ?? 0;

    // All domains for this type
    $data['domains'] = dbFetchAll(
        "SELECT COALESCE(referrer_domain, 'Direct') as domain,
                COUNT(DISTINCT session_hash) as sessions,
                COUNT(*) as pageviews
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND referrer_type = ?
         GROUP BY referrer_domain
         ORDER BY sessions DESC
         LIMIT 50",
        [$startDate, $endDate, $filterType]
    );

    // Daily trend for this type
    $data['daily'] = dbFetchAll(
        "SELECT date_only as date, COUNT(DISTINCT session_hash) as sessions
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND referrer_type = ?
         GROUP BY date_only ORDER BY date_only",
        [$startDate, $endDate, $filterType]
    );

    // Top landing pages for this type
    $data['landing_pages'] = dbFetchAll(
        "SELECT landing_page, COUNT(*) as sessions,
                SUM(is_bounce) as bounces,
                AVG(duration_seconds) as avg_duration
         FROM analytics_sessions
         WHERE date_only BETWEEN ? AND ? AND referrer_type = ?
         GROUP BY landing_page
         ORDER BY sessions DESC LIMIT 15",
        [$startDate, $endDate, $filterType]
    );

} else {
    // Overview - all source types
    $data['overview'] = true;

    // Source type breakdown
    $data['by_type'] = dbFetchAll(
        "SELECT referrer_type,
                COUNT(DISTINCT session_hash) as sessions,
                COUNT(*) as pageviews
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND referrer_type != 'internal'
         GROUP BY referrer_type
         ORDER BY sessions DESC",
        [$startDate, $endDate]
    );

    // Top referrer domains
    $data['top_domains'] = dbFetchAll(
        "SELECT COALESCE(referrer_domain, 'Direct') as domain,
                referrer_type,
                COUNT(DISTINCT session_hash) as sessions,
                COUNT(*) as pageviews
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND referrer_type != 'internal'
         GROUP BY referrer_domain, referrer_type
         ORDER BY sessions DESC
         LIMIT 25",
        [$startDate, $endDate]
    );

    // UTM Campaigns
    $data['campaigns'] = dbFetchAll(
        "SELECT utm_source, utm_medium, utm_campaign,
                COUNT(DISTINCT session_hash) as sessions,
                COUNT(*) as pageviews
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND utm_source IS NOT NULL
         GROUP BY utm_source, utm_medium, utm_campaign
         ORDER BY sessions DESC
         LIMIT 20",
        [$startDate, $endDate]
    );

    // Search terms (from utm_term)
    $data['search_terms'] = dbFetchAll(
        "SELECT utm_term as term, COUNT(DISTINCT session_hash) as sessions
         FROM analytics_pageviews
         WHERE date_only BETWEEN ? AND ? AND utm_term IS NOT NULL AND utm_term != ''
         GROUP BY utm_term
         ORDER BY sessions DESC
         LIMIT 15",
        [$startDate, $endDate]
    );

    // New vs returning (approximation based on pageviews in session)
    $data['engagement'] = dbFetchOne(
        "SELECT
            SUM(CASE WHEN pageviews = 1 THEN 1 ELSE 0 END) as single_page,
            SUM(CASE WHEN pageviews > 1 THEN 1 ELSE 0 END) as multi_page,
            SUM(CASE WHEN pageviews >= 3 THEN 1 ELSE 0 END) as engaged
         FROM analytics_sessions
         WHERE date_only BETWEEN ? AND ?",
        [$startDate, $endDate]
    );
}

$siteName = getSetting('site_name', SITE_NAME);
require_once __DIR__ . '/includes/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <div class="mb-4 flex items-center gap-2 text-sm">
        <a href="/admin/analytics.php" class="text-orange-500 hover:text-orange-600">Analytics</a>
        <span class="text-gray-400">/</span>
        <a href="/admin/analytics-acquisition.php?<?= $dateParams ?>" class="<?= !$filterType && !$filterDomain ? 'text-gray-600' : 'text-orange-500 hover:text-orange-600' ?>">Acquisition</a>
        <?php if ($filterType && !$filterDomain): ?>
        <span class="text-gray-400">/</span>
        <span class="text-gray-600"><?= ucfirst(e($filterType)) ?></span>
        <?php endif; ?>
        <?php if ($filterDomain): ?>
        <span class="text-gray-400">/</span>
        <a href="/admin/analytics-acquisition.php?<?= $dateParams ?>&type=<?= e($data['domain_type']) ?>" class="text-orange-500 hover:text-orange-600"><?= ucfirst(e($data['domain_type'])) ?></a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-600"><?= e($filterDomain) ?></span>
        <?php endif; ?>
    </div>

    <!-- Header -->
    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <?php if ($filterDomain): ?>
                    <?= e($filterDomain) ?>
                <?php elseif ($filterType): ?>
                    <?= ucfirst(e($filterType)) ?> Traffic
                <?php else: ?>
                    Acquisition Overview
                <?php endif; ?>
            </h1>
            <p class="text-gray-600"><?= date('j M Y', strtotime($startDate)) ?> - <?= date('j M Y', strtotime($endDate)) ?></p>
        </div>

        <div class="flex items-center gap-2">
            <select onchange="updateDateRange(this.value)" class="border border-gray-300 rounded-lg px-3 py-2">
                <option value="7" <?= $days == 7 ? 'selected' : '' ?>>Last 7 days</option>
                <option value="14" <?= $days == 14 ? 'selected' : '' ?>>Last 14 days</option>
                <option value="30" <?= $days == 30 ? 'selected' : '' ?>>Last 30 days</option>
                <option value="90" <?= $days == 90 ? 'selected' : '' ?>>Last 90 days</option>
            </select>
        </div>
    </div>

    <?php if ($filterDomain): ?>
    <!-- Domain Detail View -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Sessions</p>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($data['sessions']) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Pageviews</p>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($data['pageviews']) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Pages/Session</p>
            <p class="text-2xl font-bold text-gray-800"><?= $data['sessions'] > 0 ? round($data['pageviews'] / $data['sessions'], 1) : 0 ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Source Type</p>
            <p class="text-2xl font-bold text-gray-800 capitalize"><?= e($data['domain_type']) ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Daily Trend -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Sessions Over Time</h3>
            <canvas id="trendChart" height="200"></canvas>
        </div>

        <!-- Landing Pages from this source -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b"><h3 class="font-semibold text-gray-800">Landing Pages</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Page</th>
                            <th class="px-4 py-2 text-right">Sessions</th>
                            <th class="px-4 py-2 text-right">Bounce</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($data['landing_pages'] as $page):
                            $bounceRate = $page['sessions'] > 0 ? round(($page['bounces'] / $page['sessions']) * 100) : 0;
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 truncate max-w-xs"><?= e($page['landing_page']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($page['sessions']) ?></td>
                            <td class="px-4 py-2 text-right"><?= $bounceRate ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pages Visited -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b"><h3 class="font-semibold text-gray-800">Pages Visited by Visitors from <?= e($filterDomain) ?></h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Page</th>
                        <th class="px-4 py-2 text-right">Views</th>
                        <th class="px-4 py-2 text-right">Visitors</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($data['pages_visited'] as $page): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2"><?= e($page['page_path']) ?></td>
                        <td class="px-4 py-2 text-right"><?= number_format($page['views']) ?></td>
                        <td class="px-4 py-2 text-right"><?= number_format($page['visitors']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const dailyData = <?= json_encode($data['daily']) ?>;
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: dailyData.map(d => d.date),
                datasets: [{
                    label: 'Sessions',
                    data: dailyData.map(d => d.sessions),
                    borderColor: '#e85d04',
                    backgroundColor: 'rgba(232, 93, 4, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    </script>

    <?php elseif ($filterType): ?>
    <!-- Source Type Detail View -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="text-3xl font-bold text-orange-500"><?= number_format($data['sessions']) ?></div>
            <div class="text-gray-600">sessions from <?= ucfirst(e($filterType)) ?> traffic</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Domains List -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b"><h3 class="font-semibold text-gray-800"><?= ucfirst(e($filterType)) ?> Sources</h3></div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left">Domain</th>
                            <th class="px-4 py-2 text-right">Sessions</th>
                            <th class="px-4 py-2 text-right">Pageviews</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($data['domains'] as $domain): ?>
                        <tr class="hover:bg-orange-50 cursor-pointer" onclick="window.location='?<?= $dateParams ?>&domain=<?= urlencode($domain['domain']) ?>&dtype=<?= e($filterType) ?>'">
                            <td class="px-4 py-2 text-orange-600"><?= e($domain['domain']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($domain['sessions']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($domain['pageviews']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Landing Pages -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b"><h3 class="font-semibold text-gray-800">Landing Pages</h3></div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left">Page</th>
                            <th class="px-4 py-2 text-right">Sessions</th>
                            <th class="px-4 py-2 text-right">Bounce</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($data['landing_pages'] as $page):
                            $bounceRate = $page['sessions'] > 0 ? round(($page['bounces'] / $page['sessions']) * 100) : 0;
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 truncate max-w-xs"><?= e($page['landing_page']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($page['sessions']) ?></td>
                            <td class="px-4 py-2 text-right"><?= $bounceRate ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Daily Trend -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4"><?= ucfirst(e($filterType)) ?> Traffic Over Time</h3>
        <canvas id="trendChart" height="100"></canvas>
    </div>

    <script>
        const dailyData = <?= json_encode($data['daily']) ?>;
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: dailyData.map(d => d.date),
                datasets: [{
                    label: 'Sessions',
                    data: dailyData.map(d => d.sessions),
                    borderColor: '#e85d04',
                    backgroundColor: 'rgba(232, 93, 4, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    </script>

    <?php else: ?>
    <!-- Overview -->

    <!-- Source Type Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?php
        $typeColors = ['direct' => 'gray', 'search' => 'green', 'social' => 'blue', 'referral' => 'purple'];
        $typeIcons = [
            'direct' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
            'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
            'social' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
            'referral' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>'
        ];
        foreach ($data['by_type'] as $type):
            $color = $typeColors[$type['referrer_type']] ?? 'gray';
            $icon = $typeIcons[$type['referrer_type']] ?? $typeIcons['direct'];
        ?>
        <a href="?<?= $dateParams ?>&type=<?= e($type['referrer_type']) ?>" class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow border-l-4 border-<?= $color ?>-500">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5 text-<?= $color ?>-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $icon ?></svg>
                <span class="font-medium text-gray-700 capitalize"><?= e($type['referrer_type']) ?></span>
            </div>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($type['sessions']) ?></p>
            <p class="text-sm text-gray-500"><?= number_format($type['pageviews']) ?> pageviews</p>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Engagement Overview -->
    <?php if ($data['engagement']): ?>
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="font-semibold text-gray-800 mb-3">Session Engagement</h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-2xl font-bold text-red-500"><?= number_format($data['engagement']['single_page']) ?></p>
                <p class="text-sm text-gray-500">Single Page (Bounce)</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-yellow-500"><?= number_format($data['engagement']['multi_page']) ?></p>
                <p class="text-sm text-gray-500">Multi-Page</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-500"><?= number_format($data['engagement']['engaged']) ?></p>
                <p class="text-sm text-gray-500">Engaged (3+ pages)</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Referrer Domains -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b"><h3 class="font-semibold text-gray-800">Top Referrer Domains</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Domain</th>
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2 text-right">Sessions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach (array_slice($data['top_domains'], 0, 15) as $domain): ?>
                        <tr class="hover:bg-orange-50 cursor-pointer" onclick="window.location='?<?= $dateParams ?>&domain=<?= urlencode($domain['domain']) ?>&dtype=<?= e($domain['referrer_type']) ?>'">
                            <td class="px-4 py-2 text-orange-600"><?= e($domain['domain']) ?></td>
                            <td class="px-4 py-2 capitalize text-gray-500"><?= e($domain['referrer_type']) ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($domain['sessions']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- UTM Campaigns -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b"><h3 class="font-semibold text-gray-800">UTM Campaigns</h3></div>
            <?php if (empty($data['campaigns'])): ?>
            <div class="p-4 text-gray-500 text-center">No campaign data</div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Source / Medium</th>
                            <th class="px-4 py-2 text-left">Campaign</th>
                            <th class="px-4 py-2 text-right">Sessions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($data['campaigns'] as $c): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2"><?= e($c['utm_source']) ?> / <?= e($c['utm_medium'] ?? '-') ?></td>
                            <td class="px-4 py-2"><?= e($c['utm_campaign'] ?? '-') ?></td>
                            <td class="px-4 py-2 text-right"><?= number_format($c['sessions']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search Terms -->
    <?php if (!empty($data['search_terms'])): ?>
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b"><h3 class="font-semibold text-gray-800">Search Terms (from UTM)</h3></div>
        <div class="p-4 flex flex-wrap gap-2">
            <?php foreach ($data['search_terms'] as $term): ?>
            <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">
                <?= e($term['term']) ?> <span class="text-gray-500">(<?= $term['sessions'] ?>)</span>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

<script>
function updateDateRange(days) {
    const params = new URLSearchParams(window.location.search);
    params.set('days', days);
    params.delete('start');
    params.delete('end');
    window.location.search = params.toString();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
