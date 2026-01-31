<?php
/**
 * Analytics Dashboard
 * Server-side, cookie-free analytics visualisation
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/analytics.php';

requireLogin();

// Check if tables exist
try {
    $tableCheck = dbFetchOne("SHOW TABLES LIKE 'analytics_pageviews'");
    $tablesExist = !empty($tableCheck);
} catch (Exception $e) {
    $tablesExist = false;
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<?php if (!$tablesExist): ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-4 rounded-lg mb-6">
        <h2 class="font-bold text-lg mb-2">Analytics Not Configured</h2>
        <p class="mb-4">The analytics database tables have not been created yet. Run the migration to set up analytics.</p>
        <a href="/admin/migrate-analytics.php" class="inline-block bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
            Run Migration
        </a>
    </div>
</div>
<?php else: ?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Analytics</h1>
            <p class="text-gray-600">Server-side, cookie-free visitor tracking</p>
        </div>

        <!-- Date Range Picker -->
        <div class="flex items-center gap-2">
            <select id="dateRange" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500">
                <option value="7">Last 7 days</option>
                <option value="14">Last 14 days</option>
                <option value="30" selected>Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last 365 days</option>
                <option value="custom">Custom range</option>
            </select>

            <div id="customDateRange" class="hidden flex items-center gap-2">
                <input type="date" id="startDate" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500">
                <span class="text-gray-500">to</span>
                <input type="date" id="endDate" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500">
                <button id="applyCustomDate" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                    Apply
                </button>
            </div>
        </div>
    </div>

    <!-- Live Visitors Panel -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <h2 class="text-lg font-semibold text-gray-800">Live Visitors</h2>
                <span class="text-3xl font-bold text-green-600" id="liveCount">-</span>
                <span class="text-sm text-gray-500">active in last 5 minutes</span>
            </div>
            <div class="text-xs text-gray-400" id="liveUpdated">-</div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Page</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Referrer</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Device</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pages</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Time</th>
                    </tr>
                </thead>
                <tbody id="liveVisitorsTable" class="divide-y divide-gray-200">
                    <tr><td colspan="6" class="px-4 py-3 text-center text-gray-500">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-2 bg-gray-50 text-xs text-gray-500 rounded-b-lg">
            Auto-refreshes every 30 seconds
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Unique Visitors</p>
                    <p class="text-3xl font-bold text-gray-800" id="statVisitors">-</p>
                </div>
                <div class="text-right">
                    <span id="statVisitorsChange" class="text-sm font-medium"></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Page Views</p>
                    <p class="text-3xl font-bold text-gray-800" id="statPageviews">-</p>
                </div>
                <div class="text-right">
                    <span id="statPageviewsChange" class="text-sm font-medium"></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Bounce Rate</p>
                    <p class="text-3xl font-bold text-gray-800" id="statBounceRate">-</p>
                </div>
                <div class="text-right">
                    <span id="statBounceRateChange" class="text-sm font-medium"></span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Avg Session</p>
                    <p class="text-3xl font-bold text-gray-800" id="statDuration">-</p>
                </div>
                <div class="text-right">
                    <span id="statDurationChange" class="text-sm font-medium"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Visitors/Pageviews Chart -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Traffic Over Time</h2>
            <div class="h-80">
                <canvas id="trafficChart"></canvas>
            </div>
        </div>

        <!-- Traffic Sources Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Traffic Sources</h2>
            <div class="h-80 flex items-center justify-center">
                <canvas id="sourcesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Pages -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Top Pages</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Page</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Views</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">%</th>
                        </tr>
                    </thead>
                    <tbody id="topPagesTable" class="divide-y divide-gray-200">
                        <tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Traffic Sources Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Referrers</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Visitors</th>
                        </tr>
                    </thead>
                    <tbody id="referrersTable" class="divide-y divide-gray-200">
                        <tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Second Row of Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Devices -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Devices</h2>
            </div>
            <div class="p-4">
                <canvas id="devicesChart" height="200"></canvas>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Device</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">%</th>
                        </tr>
                    </thead>
                    <tbody id="devicesTable" class="divide-y divide-gray-200">
                        <tr><td colspan="2" class="px-4 py-3 text-center text-gray-500">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Browsers -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Browsers</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Browser</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Visitors</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">%</th>
                        </tr>
                    </thead>
                    <tbody id="browsersTable" class="divide-y divide-gray-200">
                        <tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Countries -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Countries</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Visitors</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">%</th>
                        </tr>
                    </thead>
                    <tbody id="countriesTable" class="divide-y divide-gray-200">
                        <tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- UTM Campaigns -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">UTM Campaigns</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medium</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campaign</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Visitors</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pageviews</th>
                    </tr>
                </thead>
                <tbody id="campaignsTable" class="divide-y divide-gray-200">
                    <tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Settings Note -->
    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
        <p class="font-medium mb-1">Analytics Settings</p>
        <p>Manage analytics settings in <a href="/admin/settings.php" class="text-orange-500 hover:text-orange-600">Site Settings</a>.</p>
        <p class="mt-2 text-xs">
            Tracking: <?= getSetting('analytics_enabled', '1') === '1' ? 'Enabled' : 'Disabled' ?> |
            Exclude admins: <?= getSetting('analytics_exclude_admins', '1') === '1' ? 'Yes' : 'No' ?> |
            Exclude bots: <?= getSetting('analytics_exclude_bots', '1') === '1' ? 'Yes' : 'No' ?> |
            Data retention: <?= getSetting('analytics_data_retention_days', '365') ?> days
        </p>
    </div>
</div>

<script>
// Analytics Dashboard JavaScript
(function() {
    // State
    let currentDays = 30;
    let customStart = null;
    let customEnd = null;

    // Chart instances
    let trafficChart = null;
    let sourcesChart = null;
    let devicesChart = null;

    // Chart colours
    const colors = {
        orange: '#e85d04',
        orangeLight: 'rgba(232, 93, 4, 0.1)',
        navy: '#1e3a5f',
        navyLight: 'rgba(30, 58, 95, 0.1)',
        green: '#22c55e',
        red: '#ef4444',
        gray: '#6b7280',
        chartColors: ['#e85d04', '#1e3a5f', '#22c55e', '#3b82f6', '#8b5cf6', '#ec4899']
    };

    // Live refresh interval
    let liveRefreshInterval = null;

    // Initialise on page load
    document.addEventListener('DOMContentLoaded', function() {
        initDatePicker();
        loadAllData();
        loadLiveVisitors();
        // Auto-refresh live visitors every 30 seconds
        liveRefreshInterval = setInterval(loadLiveVisitors, 30000);
    });

    // Date picker functionality
    function initDatePicker() {
        const dateRange = document.getElementById('dateRange');
        const customDateRange = document.getElementById('customDateRange');
        const applyBtn = document.getElementById('applyCustomDate');

        dateRange.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.classList.remove('hidden');
                // Set default dates
                const end = new Date();
                const start = new Date();
                start.setDate(start.getDate() - 30);
                document.getElementById('startDate').value = formatDateForInput(start);
                document.getElementById('endDate').value = formatDateForInput(end);
            } else {
                customDateRange.classList.add('hidden');
                currentDays = parseInt(this.value);
                customStart = null;
                customEnd = null;
                loadAllData();
            }
        });

        applyBtn.addEventListener('click', function() {
            customStart = document.getElementById('startDate').value;
            customEnd = document.getElementById('endDate').value;
            if (customStart && customEnd) {
                loadAllData();
            }
        });
    }

    // Format date for input field
    function formatDateForInput(date) {
        return date.toISOString().split('T')[0];
    }

    // Build API URL with date params
    function buildApiUrl(endpoint) {
        let url = `/admin/api/analytics/${endpoint}?`;
        if (customStart && customEnd) {
            url += `start=${customStart}&end=${customEnd}`;
        } else {
            url += `days=${currentDays}`;
        }
        return url;
    }

    // Load all data
    function loadAllData() {
        loadOverview();
        loadTraffic();
        loadPages();
        loadReferrers();
        loadDevices();
        loadLocations();
    }

    // Load live visitors
    async function loadLiveVisitors() {
        try {
            const response = await fetch('/admin/api/analytics/live.php?minutes=5');
            const data = await response.json();

            if (data.success) {
                // Update count
                document.getElementById('liveCount').textContent = data.data.active_count;

                // Update timestamp
                const now = new Date();
                document.getElementById('liveUpdated').textContent = 'Updated ' + now.toLocaleTimeString();

                // Update table
                const tbody = document.getElementById('liveVisitorsTable');

                if (data.data.visitors.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No active visitors right now</td></tr>';
                    return;
                }

                tbody.innerHTML = data.data.visitors.map(v => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <div class="text-sm font-medium text-gray-900 truncate max-w-xs" title="${escapeHtml(v.page)}">${escapeHtml(v.page)}</div>
                            ${v.page_title ? `<div class="text-xs text-gray-500 truncate max-w-xs">${escapeHtml(v.page_title)}</div>` : ''}
                        </td>
                        <td class="px-4 py-2">
                            <span class="text-sm text-gray-600">${escapeHtml(v.referrer)}</span>
                            <span class="ml-1 px-1.5 py-0.5 text-xs rounded ${getTypeColor(v.referrer_type)}">${v.referrer_type}</span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            ${getDeviceIcon(v.device)}
                            ${escapeHtml(v.device)}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            ${v.country_code ? `<span class="mr-1">${getFlagEmoji(v.country_code)}</span>` : ''}
                            ${escapeHtml(v.country)}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">${v.session_pages}</td>
                        <td class="px-4 py-2 text-right text-sm text-gray-500">${escapeHtml(v.time_ago)}</td>
                    </tr>
                `).join('');
            }
        } catch (error) {
            console.error('Failed to load live visitors:', error);
        }
    }

    // Get device icon
    function getDeviceIcon(device) {
        const icons = {
            'Desktop': '<svg class="inline w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
            'Mobile': '<svg class="inline w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
            'Tablet': '<svg class="inline w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>'
        };
        return icons[device] || '';
    }

    // Get flag emoji from country code
    function getFlagEmoji(countryCode) {
        if (!countryCode || countryCode.length !== 2) return '';
        const codePoints = countryCode
            .toUpperCase()
            .split('')
            .map(char => 127397 + char.charCodeAt());
        return String.fromCodePoint(...codePoints);
    }

    // Load overview stats
    async function loadOverview() {
        try {
            const response = await fetch(buildApiUrl('overview.php'));
            const data = await response.json();

            if (data.success) {
                const stats = data.data;

                // Update stat cards
                document.getElementById('statVisitors').textContent = formatNumber(stats.visitors.value);
                document.getElementById('statPageviews').textContent = formatNumber(stats.pageviews.value);
                document.getElementById('statBounceRate').textContent = stats.bounce_rate.value + '%';
                document.getElementById('statDuration').textContent = stats.avg_duration.formatted;

                // Update change indicators
                updateChangeIndicator('statVisitorsChange', stats.visitors.change);
                updateChangeIndicator('statPageviewsChange', stats.pageviews.change);
                updateChangeIndicator('statBounceRateChange', stats.bounce_rate.change, true); // Invert for bounce rate
                updateChangeIndicator('statDurationChange', stats.avg_duration.change);
            }
        } catch (error) {
            console.error('Failed to load overview:', error);
        }
    }

    // Update change indicator
    function updateChangeIndicator(elementId, change, invert = false) {
        const element = document.getElementById(elementId);
        const isPositive = invert ? change < 0 : change > 0;
        const arrow = isPositive ? '↑' : (change < 0 ? '↓' : '');
        const colorClass = isPositive ? 'text-green-600' : (change < 0 ? 'text-red-600' : 'text-gray-500');

        element.textContent = `${arrow} ${Math.abs(change)}%`;
        element.className = `text-sm font-medium ${colorClass}`;
    }

    // Load traffic data and render chart
    async function loadTraffic() {
        try {
            const response = await fetch(buildApiUrl('traffic.php'));
            const data = await response.json();

            if (data.success) {
                renderTrafficChart(data.data.timeseries);
                renderSourcesChart(data.data.sources);
            }
        } catch (error) {
            console.error('Failed to load traffic:', error);
        }
    }

    // Render traffic line chart
    function renderTrafficChart(timeseries) {
        const ctx = document.getElementById('trafficChart').getContext('2d');

        if (trafficChart) {
            trafficChart.destroy();
        }

        trafficChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: timeseries.labels,
                datasets: [
                    {
                        label: 'Visitors',
                        data: timeseries.visitors,
                        borderColor: colors.orange,
                        backgroundColor: colors.orangeLight,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Pageviews',
                        data: timeseries.pageviews,
                        borderColor: colors.navy,
                        backgroundColor: colors.navyLight,
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Render sources doughnut chart
    function renderSourcesChart(sources) {
        const ctx = document.getElementById('sourcesChart').getContext('2d');

        if (sourcesChart) {
            sourcesChart.destroy();
        }

        if (sources.length === 0) {
            ctx.font = '14px sans-serif';
            ctx.fillStyle = colors.gray;
            ctx.textAlign = 'center';
            ctx.fillText('No data available', ctx.canvas.width / 2, ctx.canvas.height / 2);
            return;
        }

        sourcesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: sources.map(s => s.label),
                datasets: [{
                    data: sources.map(s => s.value),
                    backgroundColor: colors.chartColors.slice(0, sources.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Load and render top pages
    async function loadPages() {
        try {
            const response = await fetch(buildApiUrl('pages.php') + '&limit=10');
            const data = await response.json();

            if (data.success) {
                const tbody = document.getElementById('topPagesTable');

                if (data.data.pages.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">No page views recorded</td></tr>';
                    return;
                }

                tbody.innerHTML = data.data.pages.map(page => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900 truncate max-w-xs" title="${escapeHtml(page.page_path)}">${escapeHtml(page.page_path)}</div>
                            ${page.page_title ? `<div class="text-xs text-gray-500 truncate max-w-xs">${escapeHtml(page.page_title)}</div>` : ''}
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600">${formatNumber(page.views)}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600">${page.percentage}%</td>
                    </tr>
                `).join('');
            }
        } catch (error) {
            console.error('Failed to load pages:', error);
        }
    }

    // Load and render referrers
    async function loadReferrers() {
        try {
            const response = await fetch(buildApiUrl('referrers.php') + '&limit=10');
            const data = await response.json();

            if (data.success) {
                const tbody = document.getElementById('referrersTable');

                if (data.data.top_referrers.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">No referrer data</td></tr>';
                    return;
                }

                tbody.innerHTML = data.data.top_referrers.map(ref => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(ref.domain)}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded ${getTypeColor(ref.type)}">${escapeHtml(ref.type)}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600">${formatNumber(ref.visitors)}</td>
                    </tr>
                `).join('');

                // Render campaigns table
                const campaignsTbody = document.getElementById('campaignsTable');

                if (data.data.campaigns.length === 0) {
                    campaignsTbody.innerHTML = '<tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">No UTM campaign data</td></tr>';
                } else {
                    campaignsTbody.innerHTML = data.data.campaigns.map(c => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(c.source || '-')}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(c.medium || '-')}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">${escapeHtml(c.campaign || '-')}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-600">${formatNumber(c.visitors)}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-600">${formatNumber(c.pageviews)}</td>
                        </tr>
                    `).join('');
                }
            }
        } catch (error) {
            console.error('Failed to load referrers:', error);
        }
    }

    // Load and render devices/browsers
    async function loadDevices() {
        try {
            const response = await fetch(buildApiUrl('devices.php'));
            const data = await response.json();

            if (data.success) {
                // Render devices chart
                renderDevicesChart(data.data.device_types);

                // Render devices table
                const devicesTbody = document.getElementById('devicesTable');
                devicesTbody.innerHTML = data.data.device_types.map(d => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-900 capitalize">${escapeHtml(d.type)}</td>
                        <td class="px-4 py-2 text-right text-sm text-gray-600">${d.percentage}%</td>
                    </tr>
                `).join('') || '<tr><td colspan="2" class="px-4 py-3 text-center text-gray-500">No data</td></tr>';

                // Render browsers table
                const browsersTbody = document.getElementById('browsersTable');
                browsersTbody.innerHTML = data.data.browsers.map(b => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(b.browser)}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600">${formatNumber(b.visitors)}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600">${b.percentage}%</td>
                    </tr>
                `).join('') || '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">No data</td></tr>';
            }
        } catch (error) {
            console.error('Failed to load devices:', error);
        }
    }

    // Render devices chart
    function renderDevicesChart(devices) {
        const ctx = document.getElementById('devicesChart').getContext('2d');

        if (devicesChart) {
            devicesChart.destroy();
        }

        if (devices.length === 0) {
            return;
        }

        devicesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: devices.map(d => d.type.charAt(0).toUpperCase() + d.type.slice(1)),
                datasets: [{
                    data: devices.map(d => d.visitors),
                    backgroundColor: colors.chartColors.slice(0, devices.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Load and render locations
    async function loadLocations() {
        try {
            const response = await fetch(buildApiUrl('locations.php') + '&limit=10');
            const data = await response.json();

            if (data.success) {
                const tbody = document.getElementById('countriesTable');

                if (data.data.countries.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">No location data</td></tr>';
                    return;
                }

                tbody.innerHTML = data.data.countries.map(c => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(c.country_name)}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600">${formatNumber(c.visitors)}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600">${c.percentage}%</td>
                    </tr>
                `).join('');
            }
        } catch (error) {
            console.error('Failed to load locations:', error);
        }
    }

    // Helper: Format number with commas
    function formatNumber(num) {
        return parseInt(num).toLocaleString();
    }

    // Helper: Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Helper: Get badge colour for referrer type
    function getTypeColor(type) {
        const colors = {
            direct: 'bg-gray-100 text-gray-700',
            search: 'bg-green-100 text-green-700',
            social: 'bg-blue-100 text-blue-700',
            referral: 'bg-purple-100 text-purple-700'
        };
        return colors[type] || 'bg-gray-100 text-gray-700';
    }
})();
</script>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
