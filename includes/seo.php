<?php
/**
 * SEO Helper Functions
 * Provides functions for verification tags, Open Graph, analytics, structured data, and sitemap generation
 */

/**
 * Output search engine verification meta tags
 */
function outputVerificationTags() {
    $tags = [];

    // Google Search Console
    if ($code = getSetting('seo_google_verification')) {
        $tags[] = '<meta name="google-site-verification" content="' . e($code) . '">';
    }

    // Bing Webmaster Tools
    if ($code = getSetting('seo_bing_verification')) {
        $tags[] = '<meta name="msvalidate.01" content="' . e($code) . '">';
    }

    // Pinterest
    if ($code = getSetting('seo_pinterest_verification')) {
        $tags[] = '<meta name="p:domain_verify" content="' . e($code) . '">';
    }

    // Facebook Domain Verification
    if ($code = getSetting('seo_facebook_verification')) {
        $tags[] = '<meta name="facebook-domain-verification" content="' . e($code) . '">';
    }

    if (!empty($tags)) {
        echo "\n    <!-- Search Engine Verification -->\n    " . implode("\n    ", $tags) . "\n";
    }
}

/**
 * Output Open Graph and Twitter Card meta tags
 * @param array $options - title, description, url, image, type, siteName
 */
function outputOpenGraphTags($options = []) {
    $siteName = getSetting('site_name', SITE_NAME);
    $defaults = [
        'title' => $siteName,
        'description' => '',
        'url' => SITE_URL . $_SERVER['REQUEST_URI'],
        'image' => getSetting('seo_default_og_image', ''),
        'type' => 'website',
        'siteName' => $siteName
    ];

    $opts = array_merge($defaults, $options);

    // Clean the URL
    $opts['url'] = rtrim($opts['url'], '/');
    if (strpos($opts['url'], '?') !== false) {
        $opts['url'] = strtok($opts['url'], '?');
    }

    $output = "\n    <!-- Open Graph -->\n";
    $output .= '    <meta property="og:type" content="' . e($opts['type']) . '">' . "\n";
    $output .= '    <meta property="og:title" content="' . e($opts['title']) . '">' . "\n";
    $output .= '    <meta property="og:description" content="' . e($opts['description']) . '">' . "\n";
    $output .= '    <meta property="og:url" content="' . e($opts['url']) . '">' . "\n";
    $output .= '    <meta property="og:site_name" content="' . e($opts['siteName']) . '">' . "\n";

    if (!empty($opts['image'])) {
        $imageUrl = $opts['image'];
        if (strpos($imageUrl, 'http') !== 0) {
            $imageUrl = SITE_URL . $imageUrl;
        }
        $output .= '    <meta property="og:image" content="' . e($imageUrl) . '">' . "\n";
    }

    // Facebook App ID
    if ($fbAppId = getSetting('seo_facebook_app_id')) {
        $output .= '    <meta property="fb:app_id" content="' . e($fbAppId) . '">' . "\n";
    }

    // Twitter Card
    $twitterCardType = getSetting('seo_twitter_card_type', 'summary_large_image');
    $output .= "\n    <!-- Twitter Card -->\n";
    $output .= '    <meta name="twitter:card" content="' . e($twitterCardType) . '">' . "\n";
    $output .= '    <meta name="twitter:title" content="' . e($opts['title']) . '">' . "\n";
    $output .= '    <meta name="twitter:description" content="' . e($opts['description']) . '">' . "\n";

    if (!empty($opts['image'])) {
        $imageUrl = $opts['image'];
        if (strpos($imageUrl, 'http') !== 0) {
            $imageUrl = SITE_URL . $imageUrl;
        }
        $output .= '    <meta name="twitter:image" content="' . e($imageUrl) . '">' . "\n";
    }

    if ($twitterUsername = getSetting('seo_twitter_username')) {
        $twitterUsername = ltrim($twitterUsername, '@');
        $output .= '    <meta name="twitter:site" content="@' . e($twitterUsername) . '">' . "\n";
    }

    echo $output;
}

/**
 * Output robots meta tag
 * @param string $directive - e.g., 'index, follow' or 'noindex, nofollow'
 */
function outputRobotsTag($directive = null) {
    if ($directive === null) {
        $directive = getSetting('seo_default_robots', 'index, follow');
    }

    if (!empty($directive)) {
        echo '    <meta name="robots" content="' . e($directive) . '">' . "\n";
    }
}

/**
 * Output canonical URL tag
 * @param string $url - The canonical URL (optional, defaults to current page)
 */
function outputCanonicalUrl($url = null) {
    if ($url === null) {
        $url = SITE_URL . strtok($_SERVER['REQUEST_URI'], '?');
    }

    // Clean trailing slashes for consistency
    $url = rtrim($url, '/');

    // Add trailing slash for root
    if (parse_url($url, PHP_URL_PATH) === '' || parse_url($url, PHP_URL_PATH) === null) {
        $url .= '/';
    }

    echo '    <link rel="canonical" href="' . e($url) . '">' . "\n";
}

/**
 * Generate page title with configurable suffix
 * @param string $title - The page-specific title
 * @return string - Full title with suffix
 */
function generatePageTitle($title) {
    $suffix = getSetting('seo_title_suffix', '');
    $siteName = getSetting('site_name', SITE_NAME);

    if (empty($suffix)) {
        $suffix = ' | ' . $siteName;
    }

    // Don't add suffix if it already ends with it or is the site name
    if ($title === $siteName || str_ends_with($title, $suffix)) {
        return $title;
    }

    return $title . $suffix;
}

/**
 * Output JSON-LD structured data for Organization and LocalBusiness
 */
function outputStructuredData() {
    if (!getSetting('seo_schema_organization_enabled', '1')) {
        return;
    }

    $siteName = getSetting('site_name', SITE_NAME);
    $contactEmail = getSetting('contact_email', SITE_EMAIL);
    $contactPhone = getSetting('contact_phone', '');

    // Build the schema object
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => getSetting('seo_schema_business_type', 'LocalBusiness'),
        'name' => $siteName,
        'url' => SITE_URL,
    ];

    // Legal name
    if ($legalName = getSetting('seo_schema_legal_name')) {
        $schema['legalName'] = $legalName;
    }

    // Logo
    if ($logo = getSetting('seo_schema_logo_url')) {
        if (strpos($logo, 'http') !== 0) {
            $logo = SITE_URL . $logo;
        }
        $schema['logo'] = $logo;
        $schema['image'] = $logo;
    }

    // Description
    $schema['description'] = getSetting('site_tagline', 'Health and safety consultants providing fire risk assessments, IOSH training, and consultancy services.');

    // Contact
    if ($contactPhone) {
        $schema['telephone'] = $contactPhone;
    }
    if ($contactEmail) {
        $schema['email'] = $contactEmail;
    }

    // Contact point
    $contactType = getSetting('seo_schema_contact_type', 'customer service');
    if ($contactPhone || $contactEmail) {
        $schema['contactPoint'] = [
            '@type' => 'ContactPoint',
            'contactType' => $contactType,
        ];
        if ($contactPhone) {
            $schema['contactPoint']['telephone'] = $contactPhone;
        }
        if ($contactEmail) {
            $schema['contactPoint']['email'] = $contactEmail;
        }
    }

    // Address
    $streetAddress = getSetting('seo_schema_street_address', getSetting('address_line1', ''));
    $locality = getSetting('seo_schema_address_locality', getSetting('city', ''));
    $region = getSetting('seo_schema_address_region', '');
    $postalCode = getSetting('seo_schema_postal_code', getSetting('postcode', ''));
    $country = getSetting('seo_schema_country', 'GB');

    if ($streetAddress || $locality || $postalCode) {
        $schema['address'] = [
            '@type' => 'PostalAddress',
            'addressCountry' => $country,
        ];
        if ($streetAddress) {
            $schema['address']['streetAddress'] = $streetAddress;
        }
        if ($locality) {
            $schema['address']['addressLocality'] = $locality;
        }
        if ($region) {
            $schema['address']['addressRegion'] = $region;
        }
        if ($postalCode) {
            $schema['address']['postalCode'] = $postalCode;
        }
    }

    // Geo coordinates
    $latitude = getSetting('seo_schema_latitude');
    $longitude = getSetting('seo_schema_longitude');
    if ($latitude && $longitude) {
        $schema['geo'] = [
            '@type' => 'GeoCoordinates',
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }

    // Opening hours
    if ($openingHours = getSetting('seo_schema_opening_hours')) {
        $schema['openingHours'] = $openingHours;
    }

    // Service areas
    if ($serviceAreas = getSetting('seo_schema_service_areas')) {
        $areas = array_map('trim', explode(',', $serviceAreas));
        $schema['areaServed'] = $areas;
    }

    // Price range
    if ($priceRange = getSetting('seo_schema_price_range')) {
        $schema['priceRange'] = $priceRange;
    }

    // Social profiles
    $sameAs = [];
    if ($fb = getSetting('facebook_url')) {
        $sameAs[] = $fb;
    }
    if ($li = getSetting('linkedin_url')) {
        $sameAs[] = $li;
    }
    if ($tw = getSetting('twitter_url')) {
        $sameAs[] = $tw;
    }
    if (!empty($sameAs)) {
        $schema['sameAs'] = $sameAs;
    }

    // Aggregate Rating from testimonials
    $ratingStats = dbFetchOne("SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM testimonials WHERE is_active = 1 AND rating > 0");
    if ($ratingStats && $ratingStats['review_count'] > 0) {
        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => round($ratingStats['avg_rating'], 1),
            'bestRating' => 5,
            'worstRating' => 1,
            'ratingCount' => (int)$ratingStats['review_count']
        ];
    }

    echo "\n    <!-- Structured Data -->\n";
    echo '    <script type="application/ld+json">' . "\n";
    echo '    ' . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    echo "\n    </script>\n";
}

/**
 * Output analytics and tracking scripts for the <head> section
 */
function outputHeadAnalytics() {
    $output = '';

    // Google Analytics 4
    if ($ga4Id = getSetting('seo_ga4_id')) {
        $output .= "\n    <!-- Google Analytics 4 -->\n";
        $output .= '    <script async src="https://www.googletagmanager.com/gtag/js?id=' . e($ga4Id) . '"></script>' . "\n";
        $output .= "    <script>\n";
        $output .= "        window.dataLayer = window.dataLayer || [];\n";
        $output .= "        function gtag(){dataLayer.push(arguments);}\n";
        $output .= "        gtag('js', new Date());\n";
        $output .= "        gtag('config', '" . e($ga4Id) . "');\n";
        $output .= "    </script>\n";
    }

    // Google Tag Manager (head part)
    if ($gtmId = getSetting('seo_gtm_id')) {
        $output .= "\n    <!-- Google Tag Manager -->\n";
        $output .= "    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':\n";
        $output .= "    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],\n";
        $output .= "    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=\n";
        $output .= "    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);\n";
        $output .= "    })(window,document,'script','dataLayer','" . e($gtmId) . "');</script>\n";
    }

    // Microsoft Clarity
    if ($clarityId = getSetting('seo_clarity_id')) {
        $output .= "\n    <!-- Microsoft Clarity -->\n";
        $output .= "    <script type=\"text/javascript\">\n";
        $output .= "        (function(c,l,a,r,i,t,y){\n";
        $output .= "            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};\n";
        $output .= "            t=l.createElement(r);t.async=1;t.src=\"https://www.clarity.ms/tag/\"+i;\n";
        $output .= "            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);\n";
        $output .= "        })(window, document, \"clarity\", \"script\", \"" . e($clarityId) . "\");\n";
        $output .= "    </script>\n";
    }

    // Facebook Pixel
    if ($fbPixelId = getSetting('seo_facebook_pixel_id')) {
        $output .= "\n    <!-- Facebook Pixel -->\n";
        $output .= "    <script>\n";
        $output .= "        !function(f,b,e,v,n,t,s)\n";
        $output .= "        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?\n";
        $output .= "        n.callMethod.apply(n,arguments):n.queue.push(arguments)};\n";
        $output .= "        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';\n";
        $output .= "        n.queue=[];t=b.createElement(e);t.async=!0;\n";
        $output .= "        t.src=v;s=b.getElementsByTagName(e)[0];\n";
        $output .= "        s.parentNode.insertBefore(t,s)}(window, document,'script',\n";
        $output .= "        'https://connect.facebook.net/en_US/fbevents.js');\n";
        $output .= "        fbq('init', '" . e($fbPixelId) . "');\n";
        $output .= "        fbq('track', 'PageView');\n";
        $output .= "    </script>\n";
        $output .= '    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' . e($fbPixelId) . '&ev=PageView&noscript=1"/></noscript>' . "\n";
    }

    // Custom head scripts
    if ($customHead = getSetting('seo_custom_head_scripts')) {
        $output .= "\n    <!-- Custom Head Scripts -->\n";
        $output .= "    " . $customHead . "\n";
    }

    echo $output;
}

/**
 * Output analytics scripts for the <body> section (right after opening body tag)
 */
function outputBodyAnalytics() {
    $output = '';

    // Google Tag Manager (noscript part)
    if ($gtmId = getSetting('seo_gtm_id')) {
        $output .= "<!-- Google Tag Manager (noscript) -->\n";
        $output .= '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . e($gtmId) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
    }

    // LinkedIn Insight Tag
    if ($linkedInId = getSetting('seo_linkedin_partner_id')) {
        $output .= "\n<!-- LinkedIn Insight Tag -->\n";
        $output .= "<script type=\"text/javascript\">\n";
        $output .= "_linkedin_partner_id = \"" . e($linkedInId) . "\";\n";
        $output .= "window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];\n";
        $output .= "window._linkedin_data_partner_ids.push(_linkedin_partner_id);\n";
        $output .= "</script>\n";
        $output .= "<script type=\"text/javascript\">\n";
        $output .= "(function(l) {\n";
        $output .= "if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};\n";
        $output .= "window.lintrk.q=[]}\n";
        $output .= "var s = document.getElementsByTagName(\"script\")[0];\n";
        $output .= "var b = document.createElement(\"script\");\n";
        $output .= "b.type = \"text/javascript\";b.async = true;\n";
        $output .= "b.src = \"https://snap.licdn.com/li.lms-analytics/insight.min.js\";\n";
        $output .= "s.parentNode.insertBefore(b, s);})(window.lintrk);\n";
        $output .= "</script>\n";
        $output .= '<noscript><img height="1" width="1" style="display:none;" alt="" src="https://px.ads.linkedin.com/collect/?pid=' . e($linkedInId) . '&fmt=gif" /></noscript>' . "\n";
    }

    // Custom body scripts
    if ($customBody = getSetting('seo_custom_body_scripts')) {
        $output .= "\n<!-- Custom Body Scripts -->\n";
        $output .= $customBody . "\n";
    }

    echo $output;
}

/**
 * Generate XML sitemap
 * @param bool $save - Whether to save to file
 * @return string - XML content
 */
function generateSitemap($save = true) {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Homepage
    $xml .= "    <url>\n";
    $xml .= "        <loc>" . SITE_URL . "/</loc>\n";
    $xml .= "        <changefreq>weekly</changefreq>\n";
    $xml .= "        <priority>1.0</priority>\n";
    $xml .= "    </url>\n";

    // Static pages (excluding home, which is already added above)
    $pages = dbFetchAll("SELECT slug, updated_at FROM pages WHERE is_active = 1 AND slug != 'home'");
    foreach ($pages as $page) {
        $xml .= "    <url>\n";
        $xml .= "        <loc>" . SITE_URL . "/" . $page['slug'] . "</loc>\n";
        $xml .= "        <lastmod>" . date('Y-m-d', strtotime($page['updated_at'])) . "</lastmod>\n";
        $xml .= "        <changefreq>weekly</changefreq>\n";
        $xml .= "        <priority>0.8</priority>\n";
        $xml .= "    </url>\n";
    }

    // Services listing page
    $xml .= "    <url>\n";
    $xml .= "        <loc>" . SITE_URL . "/services</loc>\n";
    $xml .= "        <changefreq>weekly</changefreq>\n";
    $xml .= "        <priority>0.9</priority>\n";
    $xml .= "    </url>\n";

    // Individual services
    $services = dbFetchAll("SELECT slug, updated_at FROM services WHERE is_active = 1");
    foreach ($services as $service) {
        $xml .= "    <url>\n";
        $xml .= "        <loc>" . SITE_URL . "/services/" . $service['slug'] . "</loc>\n";
        $xml .= "        <lastmod>" . date('Y-m-d', strtotime($service['updated_at'])) . "</lastmod>\n";
        $xml .= "        <changefreq>monthly</changefreq>\n";
        $xml .= "        <priority>0.7</priority>\n";
        $xml .= "    </url>\n";
    }

    // Training listing page
    $xml .= "    <url>\n";
    $xml .= "        <loc>" . SITE_URL . "/training</loc>\n";
    $xml .= "        <changefreq>weekly</changefreq>\n";
    $xml .= "        <priority>0.9</priority>\n";
    $xml .= "    </url>\n";

    // Individual training courses
    $training = dbFetchAll("SELECT slug, updated_at FROM training WHERE is_active = 1");
    foreach ($training as $course) {
        $xml .= "    <url>\n";
        $xml .= "        <loc>" . SITE_URL . "/training/" . $course['slug'] . "</loc>\n";
        $xml .= "        <lastmod>" . date('Y-m-d', strtotime($course['updated_at'])) . "</lastmod>\n";
        $xml .= "        <changefreq>monthly</changefreq>\n";
        $xml .= "        <priority>0.7</priority>\n";
        $xml .= "    </url>\n";
    }

    // Blog listing page
    $xml .= "    <url>\n";
    $xml .= "        <loc>" . SITE_URL . "/blog</loc>\n";
    $xml .= "        <changefreq>daily</changefreq>\n";
    $xml .= "        <priority>0.8</priority>\n";
    $xml .= "    </url>\n";

    // Blog posts
    $blogPosts = dbFetchAll("SELECT slug, updated_at FROM blog_posts WHERE status = 'published' OR (status = 'scheduled' AND published_at <= NOW())");
    foreach ($blogPosts as $post) {
        $xml .= "    <url>\n";
        $xml .= "        <loc>" . SITE_URL . "/blog/" . $post['slug'] . "</loc>\n";
        $xml .= "        <lastmod>" . date('Y-m-d', strtotime($post['updated_at'])) . "</lastmod>\n";
        $xml .= "        <changefreq>weekly</changefreq>\n";
        $xml .= "        <priority>0.6</priority>\n";
        $xml .= "    </url>\n";
    }

    // Location-specific landing pages
    $locationPages = [
        'health-and-safety-consultants-leicester',
        'health-and-safety-consultants-nottingham',
        'health-and-safety-consultants-derby',
        'health-and-safety-consultants-loughborough',
        'health-and-safety-east-midlands',
        'fire-risk-assessments-leicester',
        'fire-risk-assessments-nottingham',
        'fire-risk-assessments-derby',
        'fire-risk-assessments-loughborough',
        'fire-risk-assessments-east-midlands',
        'health-and-safety-training-leicester',
        'health-and-safety-training-nottingham',
        'health-and-safety-training-derby',
        'health-and-safety-training-loughborough',
        'health-and-safety-training-east-midlands',
    ];
    foreach ($locationPages as $slug) {
        $xml .= "    <url>\n";
        $xml .= "        <loc>" . SITE_URL . "/" . $slug . "</loc>\n";
        $xml .= "        <changefreq>monthly</changefreq>\n";
        $xml .= "        <priority>0.8</priority>\n";
        $xml .= "    </url>\n";
    }

    $xml .= '</urlset>';

    if ($save) {
        $filepath = ROOT_PATH . '/sitemap.xml';
        file_put_contents($filepath, $xml);
        updateSetting('seo_sitemap_last_generated', date('Y-m-d H:i:s'));
    }

    return $xml;
}

/**
 * Get default robots.txt content
 * @return string
 */
function getDefaultRobotsTxt() {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n\n";
    $content .= "# Disallow admin and private areas\n";
    $content .= "Disallow: /admin/\n";
    $content .= "Disallow: /includes/\n";
    $content .= "Disallow: /uploads/*.php\n\n";
    $content .= "# Sitemap\n";
    $content .= "Sitemap: " . SITE_URL . "/sitemap.xml\n";

    return $content;
}

/**
 * Get robots.txt content from settings or return default
 * @return string
 */
function getRobotsTxt() {
    $content = getSetting('seo_robots_txt_content');

    if (empty($content)) {
        return getDefaultRobotsTxt();
    }

    return $content;
}
