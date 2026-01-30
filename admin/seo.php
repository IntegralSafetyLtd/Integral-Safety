<?php
/**
 * SEO Settings Management
 * Comprehensive SEO admin panel with tabs for verification, analytics, schema, and technical SEO
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/seo.php';

requireLogin();

$activeTab = $_GET['tab'] ?? 'verification';
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $formType = $_POST['form_type'] ?? '';

        switch ($formType) {
            case 'verification':
                $settings = [
                    'seo_google_verification',
                    'seo_bing_verification',
                    'seo_pinterest_verification',
                    'seo_facebook_verification',
                ];
                foreach ($settings as $key) {
                    updateSetting($key, sanitize($_POST[$key] ?? ''));
                }
                $success = 'Verification codes saved successfully!';
                break;

            case 'analytics':
                $settings = [
                    'seo_ga4_id',
                    'seo_gtm_id',
                    'seo_clarity_id',
                    'seo_facebook_pixel_id',
                    'seo_linkedin_partner_id',
                ];
                foreach ($settings as $key) {
                    updateSetting($key, sanitize($_POST[$key] ?? ''));
                }
                // Allow HTML for custom scripts
                updateSetting('seo_custom_head_scripts', $_POST['seo_custom_head_scripts'] ?? '');
                updateSetting('seo_custom_body_scripts', $_POST['seo_custom_body_scripts'] ?? '');
                $success = 'Analytics settings saved successfully!';
                break;

            case 'meta':
                $settings = [
                    'seo_title_suffix',
                    'seo_default_og_image',
                    'seo_twitter_card_type',
                    'seo_twitter_username',
                    'seo_facebook_app_id',
                    'seo_default_robots',
                ];
                foreach ($settings as $key) {
                    updateSetting($key, sanitize($_POST[$key] ?? ''));
                }
                $success = 'Meta defaults saved successfully!';
                break;

            case 'schema':
                $settings = [
                    'seo_schema_organization_enabled',
                    'seo_schema_legal_name',
                    'seo_schema_logo_url',
                    'seo_schema_contact_type',
                    'seo_schema_business_type',
                    'seo_schema_street_address',
                    'seo_schema_address_locality',
                    'seo_schema_address_region',
                    'seo_schema_postal_code',
                    'seo_schema_country',
                    'seo_schema_latitude',
                    'seo_schema_longitude',
                    'seo_schema_opening_hours',
                    'seo_schema_service_areas',
                    'seo_schema_price_range',
                ];
                foreach ($settings as $key) {
                    $value = $_POST[$key] ?? '';
                    if ($key === 'seo_schema_organization_enabled') {
                        $value = isset($_POST[$key]) ? '1' : '0';
                    }
                    updateSetting($key, sanitize($value));
                }
                $success = 'Schema settings saved successfully!';
                break;

            case 'technical':
                // Handle sitemap generation
                if (isset($_POST['generate_sitemap'])) {
                    try {
                        generateSitemap(true);
                        $success = 'Sitemap generated successfully!';
                    } catch (Exception $e) {
                        $error = 'Failed to generate sitemap: ' . $e->getMessage();
                    }
                }

                // Save robots.txt content and auto-regenerate setting
                updateSetting('seo_robots_txt_content', $_POST['seo_robots_txt_content'] ?? '');
                updateSetting('seo_sitemap_auto_regenerate', isset($_POST['seo_sitemap_auto_regenerate']) ? '1' : '0');

                if (!$success && !$error) {
                    $success = 'Technical SEO settings saved successfully!';
                }
                break;
        }

        if ($success) {
            $_SESSION['flash_message'] = $success;
            $_SESSION['flash_type'] = 'success';
            header('Location: /admin/seo.php?tab=' . $activeTab);
            exit;
        }
    }
}

require_once __DIR__ . '/includes/header.php';

// Tab definitions
$tabs = [
    'verification' => 'Verification Codes',
    'analytics' => 'Analytics & Tracking',
    'meta' => 'Meta Defaults',
    'schema' => 'Structured Data',
    'technical' => 'Technical SEO',
    'links' => 'Quick Links',
];
?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">SEO Settings</h1>
    <p class="text-gray-600">Manage search engine optimisation, analytics, and structured data</p>
</div>

<?php if ($error): ?>
<div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4"><?= e($error) ?></div>
<?php endif; ?>

<!-- Tabs -->
<div class="border-b border-gray-200 mb-6">
    <nav class="flex flex-wrap gap-2" aria-label="Tabs">
        <?php foreach ($tabs as $tabId => $tabName): ?>
        <a href="?tab=<?= $tabId ?>"
           class="px-4 py-2 text-sm font-medium rounded-t-lg border-b-2 <?= $activeTab === $tabId ? 'border-orange-500 text-orange-600 bg-orange-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
            <?= e($tabName) ?>
        </a>
        <?php endforeach; ?>
    </nav>
</div>

<!-- Tab Content -->
<div class="bg-white rounded-lg shadow p-6">

<?php if ($activeTab === 'verification'): ?>
<!-- Verification Codes Tab -->
<form method="POST">
    <?= csrfField() ?>
    <input type="hidden" name="form_type" value="verification">

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Search Engine Verification</h2>
        <p class="text-gray-600 text-sm">Add verification codes to prove site ownership to search engines and social platforms.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Google Search Console</label>
            <input type="text" name="seo_google_verification" value="<?= e(getSetting('seo_google_verification')) ?>"
                   placeholder="e.g., abc123xyz..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Content value from the meta tag provided by Google</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Bing Webmaster Tools</label>
            <input type="text" name="seo_bing_verification" value="<?= e(getSetting('seo_bing_verification')) ?>"
                   placeholder="e.g., ABC123..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Content value from msvalidate.01 meta tag</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Pinterest</label>
            <input type="text" name="seo_pinterest_verification" value="<?= e(getSetting('seo_pinterest_verification')) ?>"
                   placeholder="e.g., abc123..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Content value from p:domain_verify meta tag</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Facebook Domain Verification</label>
            <input type="text" name="seo_facebook_verification" value="<?= e(getSetting('seo_facebook_verification')) ?>"
                   placeholder="e.g., abc123..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Content value from facebook-domain-verification meta tag</p>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
            Save Verification Codes
        </button>
    </div>
</form>

<?php elseif ($activeTab === 'analytics'): ?>
<!-- Analytics Tab -->
<form method="POST">
    <?= csrfField() ?>
    <input type="hidden" name="form_type" value="analytics">

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Analytics & Tracking</h2>
        <p class="text-gray-600 text-sm">Configure analytics platforms and tracking codes. Scripts are automatically added to all public pages.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Google Analytics 4 (GA4) Measurement ID</label>
            <input type="text" name="seo_ga4_id" value="<?= e(getSetting('seo_ga4_id')) ?>"
                   placeholder="e.g., G-XXXXXXXXXX"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Found in GA4 &gt; Admin &gt; Data Streams</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Google Tag Manager ID</label>
            <input type="text" name="seo_gtm_id" value="<?= e(getSetting('seo_gtm_id')) ?>"
                   placeholder="e.g., GTM-XXXXXXX"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">If using GTM, you may not need GA4 ID separately</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Microsoft Clarity Project ID</label>
            <input type="text" name="seo_clarity_id" value="<?= e(getSetting('seo_clarity_id')) ?>"
                   placeholder="e.g., abc123xyz"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Free heatmaps and session recordings</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Facebook Pixel ID</label>
            <input type="text" name="seo_facebook_pixel_id" value="<?= e(getSetting('seo_facebook_pixel_id')) ?>"
                   placeholder="e.g., 1234567890123456"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">For Facebook/Meta advertising tracking</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">LinkedIn Partner ID</label>
            <input type="text" name="seo_linkedin_partner_id" value="<?= e(getSetting('seo_linkedin_partner_id')) ?>"
                   placeholder="e.g., 1234567"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">For LinkedIn Insight Tag / advertising</p>
        </div>
    </div>

    <hr class="my-6">

    <h3 class="text-md font-semibold text-gray-800 mb-4">Custom Scripts</h3>

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2">Custom &lt;head&gt; Scripts</label>
        <textarea name="seo_custom_head_scripts" rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 font-mono text-sm"
                  placeholder="<!-- Add custom scripts here -->"><?= e(getSetting('seo_custom_head_scripts')) ?></textarea>
        <p class="text-sm text-gray-500 mt-1">Added before &lt;/head&gt;. Include full &lt;script&gt; tags.</p>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2">Custom &lt;body&gt; Scripts</label>
        <textarea name="seo_custom_body_scripts" rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 font-mono text-sm"
                  placeholder="<!-- Add custom scripts here -->"><?= e(getSetting('seo_custom_body_scripts')) ?></textarea>
        <p class="text-sm text-gray-500 mt-1">Added after &lt;body&gt; opening tag. Include full &lt;script&gt; tags.</p>
    </div>

    <div class="mt-6">
        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
            Save Analytics Settings
        </button>
    </div>
</form>

<?php elseif ($activeTab === 'meta'): ?>
<!-- Meta Defaults Tab -->
<form method="POST">
    <?= csrfField() ?>
    <input type="hidden" name="form_type" value="meta">

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Default Meta Settings</h2>
        <p class="text-gray-600 text-sm">Configure default values for Open Graph, Twitter Cards, and other meta tags.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Title Suffix</label>
            <input type="text" name="seo_title_suffix" value="<?= e(getSetting('seo_title_suffix')) ?>"
                   placeholder="e.g., | Integral Safety Ltd"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Appended to all page titles (leave blank for default)</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Default Robots Directive</label>
            <select name="seo_default_robots"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <option value="index, follow" <?= getSetting('seo_default_robots', 'index, follow') === 'index, follow' ? 'selected' : '' ?>>index, follow (Recommended)</option>
                <option value="index, nofollow" <?= getSetting('seo_default_robots') === 'index, nofollow' ? 'selected' : '' ?>>index, nofollow</option>
                <option value="noindex, follow" <?= getSetting('seo_default_robots') === 'noindex, follow' ? 'selected' : '' ?>>noindex, follow</option>
                <option value="noindex, nofollow" <?= getSetting('seo_default_robots') === 'noindex, nofollow' ? 'selected' : '' ?>>noindex, nofollow</option>
            </select>
            <p class="text-sm text-gray-500 mt-1">Default for pages without specific settings</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-gray-700 font-medium mb-2">Default Open Graph Image</label>
            <div class="flex gap-2">
                <input type="text" name="seo_default_og_image" id="seo_default_og_image"
                       value="<?= e(getSetting('seo_default_og_image')) ?>"
                       placeholder="/uploads/og-default.jpg"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <button type="button" onclick="openGalleryPicker('seo_default_og_image')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 border border-gray-300">
                    Browse
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-1">Recommended: 1200x630 pixels. Used when no page-specific image is set.</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Twitter Card Type</label>
            <select name="seo_twitter_card_type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <option value="summary_large_image" <?= getSetting('seo_twitter_card_type', 'summary_large_image') === 'summary_large_image' ? 'selected' : '' ?>>Summary with Large Image (Recommended)</option>
                <option value="summary" <?= getSetting('seo_twitter_card_type') === 'summary' ? 'selected' : '' ?>>Summary</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Twitter Username</label>
            <input type="text" name="seo_twitter_username" value="<?= e(getSetting('seo_twitter_username')) ?>"
                   placeholder="e.g., @integralsafety"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">For twitter:site meta tag</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Facebook App ID</label>
            <input type="text" name="seo_facebook_app_id" value="<?= e(getSetting('seo_facebook_app_id')) ?>"
                   placeholder="e.g., 1234567890"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Optional: For Facebook Insights on shared content</p>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
            Save Meta Defaults
        </button>
    </div>
</form>

<?php elseif ($activeTab === 'schema'): ?>
<!-- Structured Data Tab -->
<form method="POST">
    <?= csrfField() ?>
    <input type="hidden" name="form_type" value="schema">

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Structured Data (Schema.org)</h2>
        <p class="text-gray-600 text-sm">Configure JSON-LD structured data for rich search results. This helps search engines understand your business.</p>
    </div>

    <div class="mb-6">
        <label class="flex items-center">
            <input type="checkbox" name="seo_schema_organization_enabled" value="1"
                   <?= getSetting('seo_schema_organization_enabled', '1') === '1' ? 'checked' : '' ?>
                   class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
            <span class="ml-2 text-gray-700 font-medium">Enable Organisation/Business Schema</span>
        </label>
        <p class="text-sm text-gray-500 mt-1 ml-6">Outputs JSON-LD structured data on all public pages</p>
    </div>

    <hr class="my-6">

    <h3 class="text-md font-semibold text-gray-800 mb-4">Business Information</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Business Type</label>
            <select name="seo_schema_business_type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <option value="LocalBusiness" <?= getSetting('seo_schema_business_type', 'LocalBusiness') === 'LocalBusiness' ? 'selected' : '' ?>>Local Business</option>
                <option value="ProfessionalService" <?= getSetting('seo_schema_business_type') === 'ProfessionalService' ? 'selected' : '' ?>>Professional Service</option>
                <option value="Organization" <?= getSetting('seo_schema_business_type') === 'Organization' ? 'selected' : '' ?>>Organisation</option>
                <option value="Corporation" <?= getSetting('seo_schema_business_type') === 'Corporation' ? 'selected' : '' ?>>Corporation</option>
            </select>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Legal Name</label>
            <input type="text" name="seo_schema_legal_name" value="<?= e(getSetting('seo_schema_legal_name')) ?>"
                   placeholder="e.g., Integral Safety Ltd"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Official registered company name</p>
        </div>

        <div class="md:col-span-2">
            <label class="block text-gray-700 font-medium mb-2">Logo URL</label>
            <div class="flex gap-2 mb-3">
                <input type="text" name="seo_schema_logo_url" id="seo_schema_logo_url"
                       value="<?= e(getSetting('seo_schema_logo_url')) ?>"
                       placeholder="/uploads/logo.png"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <button type="button" onclick="openGalleryPicker('seo_schema_logo_url')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 border border-gray-300">
                    Browse Gallery
                </button>
            </div>
            <?php
            $siteLogo = getSetting('site_logo');
            $siteLogoWhite = getSetting('site_logo_white');
            if ($siteLogo || $siteLogoWhite): ?>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-600 mb-2">Quick select from Site Settings:</p>
                <div class="flex flex-wrap gap-3">
                    <?php if ($siteLogo): ?>
                    <button type="button" onclick="document.getElementById('seo_schema_logo_url').value='<?= e($siteLogo) ?>'"
                            class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-colors">
                        <img src="<?= e($siteLogo) ?>" alt="Site Logo" class="h-8 w-auto max-w-[100px] object-contain">
                        <span class="text-sm text-gray-600">Colour Logo</span>
                    </button>
                    <?php endif; ?>
                    <?php if ($siteLogoWhite): ?>
                    <button type="button" onclick="document.getElementById('seo_schema_logo_url').value='<?= e($siteLogoWhite) ?>'"
                            class="flex items-center gap-2 px-3 py-2 bg-navy-800 border border-gray-600 rounded-lg hover:border-orange-500 transition-colors">
                        <img src="<?= e($siteLogoWhite) ?>" alt="White Logo" class="h-8 w-auto max-w-[100px] object-contain">
                        <span class="text-sm text-white">White Logo</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Contact Type</label>
            <select name="seo_schema_contact_type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <option value="customer service" <?= getSetting('seo_schema_contact_type', 'customer service') === 'customer service' ? 'selected' : '' ?>>Customer Service</option>
                <option value="sales" <?= getSetting('seo_schema_contact_type') === 'sales' ? 'selected' : '' ?>>Sales</option>
                <option value="technical support" <?= getSetting('seo_schema_contact_type') === 'technical support' ? 'selected' : '' ?>>Technical Support</option>
            </select>
        </div>
    </div>

    <hr class="my-6">

    <h3 class="text-md font-semibold text-gray-800 mb-4">Address &amp; Location</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="md:col-span-2">
            <label class="block text-gray-700 font-medium mb-2">Street Address</label>
            <input type="text" name="seo_schema_street_address"
                   value="<?= e(getSetting('seo_schema_street_address', getSetting('address_line1'))) ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">City/Town (Locality)</label>
            <input type="text" name="seo_schema_address_locality"
                   value="<?= e(getSetting('seo_schema_address_locality', getSetting('city'))) ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">County/Region</label>
            <input type="text" name="seo_schema_address_region"
                   value="<?= e(getSetting('seo_schema_address_region')) ?>"
                   placeholder="e.g., Leicestershire"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Postcode</label>
            <input type="text" name="seo_schema_postal_code"
                   value="<?= e(getSetting('seo_schema_postal_code', getSetting('postcode'))) ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Country Code</label>
            <input type="text" name="seo_schema_country"
                   value="<?= e(getSetting('seo_schema_country', 'GB')) ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">ISO country code (e.g., GB, US)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Latitude</label>
            <input type="text" name="seo_schema_latitude"
                   value="<?= e(getSetting('seo_schema_latitude')) ?>"
                   placeholder="e.g., 52.7128"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Longitude</label>
            <input type="text" name="seo_schema_longitude"
                   value="<?= e(getSetting('seo_schema_longitude')) ?>"
                   placeholder="e.g., -1.2097"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
        </div>
    </div>

    <hr class="my-6">

    <h3 class="text-md font-semibold text-gray-800 mb-4">Additional Details</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Opening Hours</label>
            <input type="text" name="seo_schema_opening_hours"
                   value="<?= e(getSetting('seo_schema_opening_hours')) ?>"
                   placeholder="e.g., Mo-Fr 09:00-17:00"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Schema format: Mo-Fr 09:00-17:00</p>
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-2">Price Range</label>
            <select name="seo_schema_price_range"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                <option value="" <?= getSetting('seo_schema_price_range') === '' ? 'selected' : '' ?>>Not specified</option>
                <option value="£" <?= getSetting('seo_schema_price_range') === '£' ? 'selected' : '' ?>>£ - Budget</option>
                <option value="££" <?= getSetting('seo_schema_price_range', '££') === '££' ? 'selected' : '' ?>>££ - Moderate</option>
                <option value="£££" <?= getSetting('seo_schema_price_range') === '£££' ? 'selected' : '' ?>>£££ - Expensive</option>
                <option value="££££" <?= getSetting('seo_schema_price_range') === '££££' ? 'selected' : '' ?>>££££ - Very Expensive</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-gray-700 font-medium mb-2">Service Areas</label>
            <input type="text" name="seo_schema_service_areas"
                   value="<?= e(getSetting('seo_schema_service_areas')) ?>"
                   placeholder="e.g., Leicestershire, Derbyshire, Nottinghamshire, Midlands, UK"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-500 mt-1">Comma-separated list of areas you serve</p>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
            Save Schema Settings
        </button>
    </div>
</form>

<?php elseif ($activeTab === 'technical'): ?>
<!-- Technical SEO Tab -->
<form method="POST">
    <?= csrfField() ?>
    <input type="hidden" name="form_type" value="technical">

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Technical SEO</h2>
        <p class="text-gray-600 text-sm">Manage your sitemap and robots.txt file.</p>
    </div>

    <!-- Sitemap Section -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-gray-800 mb-3">XML Sitemap</h3>

        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-gray-700">
                    Sitemap URL: <a href="<?= SITE_URL ?>/sitemap.xml" target="_blank" class="text-orange-500 hover:text-orange-600"><?= SITE_URL ?>/sitemap.xml</a>
                </p>
                <?php if ($lastGenerated = getSetting('seo_sitemap_last_generated')): ?>
                <p class="text-sm text-gray-500">Last generated: <?= formatDate($lastGenerated, 'j M Y, H:i') ?></p>
                <?php else: ?>
                <p class="text-sm text-gray-500">Not yet generated</p>
                <?php endif; ?>
            </div>
            <button type="submit" name="generate_sitemap" value="1"
                    class="px-4 py-2 bg-navy-700 text-white rounded-lg hover:bg-navy-800">
                Generate Now
            </button>
        </div>

        <label class="flex items-center">
            <input type="checkbox" name="seo_sitemap_auto_regenerate" value="1"
                   <?= getSetting('seo_sitemap_auto_regenerate') === '1' ? 'checked' : '' ?>
                   class="rounded border-gray-300 text-orange-500 focus:ring-orange-500">
            <span class="ml-2 text-gray-700">Auto-regenerate when content is updated</span>
        </label>
        <p class="text-sm text-gray-500 mt-1 ml-6">Automatically updates sitemap when pages, services, or training courses are modified</p>
    </div>

    <!-- Robots.txt Section -->
    <div class="mb-6">
        <h3 class="font-semibold text-gray-800 mb-3">robots.txt</h3>
        <p class="text-gray-600 text-sm mb-3">
            View your robots.txt: <a href="<?= SITE_URL ?>/robots.txt" target="_blank" class="text-orange-500 hover:text-orange-600"><?= SITE_URL ?>/robots.txt</a>
        </p>

        <textarea name="seo_robots_txt_content" rows="10"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 font-mono text-sm"
                  placeholder="<?= e(getDefaultRobotsTxt()) ?>"><?= e(getSetting('seo_robots_txt_content')) ?></textarea>
        <p class="text-sm text-gray-500 mt-1">Leave blank to use the default robots.txt content shown in the placeholder</p>

        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
            <h4 class="font-medium text-blue-800 text-sm mb-1">Default robots.txt content:</h4>
            <pre class="text-xs text-blue-700 whitespace-pre-wrap"><?= e(getDefaultRobotsTxt()) ?></pre>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
            Save Technical Settings
        </button>
    </div>
</form>

<?php elseif ($activeTab === 'links'): ?>
<!-- Quick Links Tab -->
<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-1">Quick Links</h2>
    <p class="text-gray-600 text-sm">Useful tools and dashboards for managing your site's SEO.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- Google Tools -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google Tools
        </h3>
        <ul class="space-y-2">
            <li>
                <a href="https://search.google.com/search-console" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Search Console
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://analytics.google.com/" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Google Analytics
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://tagmanager.google.com/" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Tag Manager
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://pagespeed.web.dev/analysis?url=<?= urlencode(SITE_URL) ?>" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    PageSpeed Insights
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://search.google.com/test/rich-results?url=<?= urlencode(SITE_URL) ?>" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Rich Results Test
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://www.google.com/business/" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Google Business Profile
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
        </ul>
    </div>

    <!-- Microsoft/Bing Tools -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                <path d="M5.3 2.7v15.3l3.1 1.8 8.9-3.1v-3.7l-5.8 2v-3l5.8-2v-3L5.3 2.7zm3.1 5.9v5.3l2.7-1V8.6l-2.7-.9z"/>
            </svg>
            Microsoft Tools
        </h3>
        <ul class="space-y-2">
            <li>
                <a href="https://www.bing.com/webmasters/" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Bing Webmaster Tools
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://clarity.microsoft.com/" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Microsoft Clarity
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
        </ul>
    </div>

    <!-- Social Media Tools -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-700" viewBox="0 0 24 24" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Social Media
        </h3>
        <ul class="space-y-2">
            <li>
                <a href="https://developers.facebook.com/tools/debug/?q=<?= urlencode(SITE_URL) ?>" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Facebook Sharing Debugger
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://cards-dev.twitter.com/validator" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Twitter Card Validator
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://www.linkedin.com/post-inspector/" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    LinkedIn Post Inspector
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://business.facebook.com/" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Meta Business Suite
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
        </ul>
    </div>

    <!-- Testing Tools -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Testing Tools
        </h3>
        <ul class="space-y-2">
            <li>
                <a href="https://validator.schema.org/#url=<?= urlencode(SITE_URL) ?>" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Schema.org Validator
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://www.xml-sitemaps.com/validate-xml-sitemap.html?op=validate-xml-sitemap&sitemap=<?= urlencode(SITE_URL . '/sitemap.xml') ?>" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Sitemap Validator
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://web.dev/measure/?url=<?= urlencode(SITE_URL) ?>" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    Web.dev Measure
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="https://gtmetrix.com/?url=<?= urlencode(SITE_URL) ?>" target="_blank" rel="noopener"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    GTmetrix
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
        </ul>
    </div>

    <!-- Your Site Links -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Your Site
        </h3>
        <ul class="space-y-2">
            <li>
                <a href="<?= SITE_URL ?>/sitemap.xml" target="_blank"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    View Sitemap
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/robots.txt" target="_blank"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    View robots.txt
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>" target="_blank"
                   class="text-orange-500 hover:text-orange-600 flex items-center gap-2">
                    View Live Site
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </li>
        </ul>
    </div>
</div>

<?php endif; ?>

</div>

<?php
// Enable site logos section in gallery picker for SEO page
$galleryPickerShowLogos = true;
require_once __DIR__ . '/includes/gallery-picker.php';
?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
