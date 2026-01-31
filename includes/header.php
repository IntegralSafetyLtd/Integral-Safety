<?php
/**
 * Public Header Template
 */
require_once __DIR__ . '/seo.php';
require_once __DIR__ . '/analytics.php';

// Track page view (server-side, cookie-free)
trackPageview($pageTitle ?? null);

$siteName = getSetting('site_name', SITE_NAME);
$siteTagline = getSetting('site_tagline', 'Health & Safety Consultants');
$contactPhone = getSetting('contact_phone', '01530 382 150');
$contactPhone2 = getSetting('contact_phone_2', '');
$contactEmail = getSetting('contact_email', SITE_EMAIL);
$siteLogo = getSetting('site_logo', '/assets/images/logo.png');
$siteFavicon = getSetting('site_favicon', '/assets/images/favicon.png');

// SEO variables (can be set by individual pages before including header)
$seoTitle = $seoTitle ?? ($pageTitle ?? $siteName);
$seoDescription = $metaDescription ?? 'Health & Safety Consultants in Leicestershire. Fire risk assessments, IOSH training, and consultancy services.';
$seoImage = $ogImage ?? getSetting('seo_default_og_image', '');
$seoRobots = $robotsDirective ?? null;
$seoCanonical = $canonicalUrl ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(generatePageTitle($seoTitle)) ?></title>
    <meta name="description" content="<?= e($seoDescription) ?>">
    <?php if (!empty($metaKeywords)): ?>
    <meta name="keywords" content="<?= e($metaKeywords) ?>">
    <?php endif; ?>

    <?php outputRobotsTag($seoRobots); ?>
    <?php outputCanonicalUrl($seoCanonical); ?>
    <?php outputVerificationTags(); ?>
    <?php outputOpenGraphTags([
        'title' => $seoTitle,
        'description' => $seoDescription,
        'image' => $seoImage,
        'type' => $ogType ?? 'website',
    ]); ?>

    <!-- Favicon -->
    <link rel="icon" href="<?= e($siteFavicon) ?>">

    <!-- Fonts (self-hosted) -->
    <link rel="preload" href="/assets/fonts/poppins-600-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/plus-jakarta-sans-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="/assets/css/fonts.css">

    <!-- Tailwind CSS (production build) -->
    <link rel="stylesheet" href="/assets/css/styles.css">

    <?php outputStructuredData(); ?>
    <?php // Analytics are now loaded via cookie consent system in cookies.php ?>
</head>
<body class="bg-cream text-navy-900 antialiased">

    <!-- Top Bar -->
    <div class="bg-navy-900 text-white py-2.5 text-sm">
        <div class="max-w-6xl mx-auto px-6 flex justify-between items-center">
            <span class="hidden sm:block"><?= e($siteTagline) ?></span>
            <div class="flex gap-6">
                <a href="tel:<?= preg_replace('/\s+/', '', $contactPhone) ?>" class="flex items-center gap-2 opacity-90 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span><?= e($contactPhone) ?></span>
                </a>
                <?php if ($contactPhone2): ?>
                <a href="tel:<?= preg_replace('/\s+/', '', $contactPhone2) ?>" class="flex items-center gap-2 opacity-90 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span><?= e($contactPhone2) ?></span>
                </a>
                <?php endif; ?>
                <a href="mailto:<?= e($contactEmail) ?>" class="hidden md:flex items-center gap-2 opacity-90 hover:opacity-100 transition-opacity">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span><?= e($contactEmail) ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white py-4 border-b border-navy-100 lg:sticky lg:top-0 z-50 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 flex justify-between items-center">
            <!-- Logo -->
            <a href="/" class="flex items-center">
                <img src="/assets/images/logo-1x.png"
                     srcset="/assets/images/logo-1x.png 1x, /assets/images/logo.png 2x"
                     alt="<?= e($siteName) ?>"
                     class="h-16 w-auto"
                     width="224"
                     height="112">
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="/" class="text-navy-800 font-medium hover:text-orange-500 transition-colors">Home</a>
                <a href="/services" class="text-navy-800 font-medium hover:text-orange-500 transition-colors">Services</a>
                <a href="/training" class="text-navy-800 font-medium hover:text-orange-500 transition-colors">Training</a>
                <a href="/about" class="text-navy-800 font-medium hover:text-orange-500 transition-colors">About</a>
                <a href="/blog" class="text-navy-800 font-medium hover:text-orange-500 transition-colors">Blog</a>
                <a href="/contact" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors shadow-md hover:shadow-lg">
                    Contact Us
                </a>
            </nav>

            <!-- Mobile Menu Button -->
            <button id="mobileMenuBtn" class="lg:hidden p-2 text-navy-800" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobileMenu" class="hidden lg:hidden bg-white border-t border-navy-100">
            <div class="max-w-6xl mx-auto px-6 py-4 space-y-4">
                <a href="/" class="block text-navy-800 font-medium hover:text-orange-500">Home</a>
                <a href="/services" class="block text-navy-800 font-medium hover:text-orange-500">Services</a>
                <a href="/training" class="block text-navy-800 font-medium hover:text-orange-500">Training</a>
                <a href="/about" class="block text-navy-800 font-medium hover:text-orange-500">About</a>
                <a href="/blog" class="block text-navy-800 font-medium hover:text-orange-500">Blog</a>
                <a href="/contact" class="block bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-orange-600">
                    Contact Us
                </a>
            </div>
        </div>
    </header>

    <main>
