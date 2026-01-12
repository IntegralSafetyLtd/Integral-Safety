<?php
/**
 * Public Header Template
 */
$siteName = getSetting('site_name', SITE_NAME);
$siteTagline = getSetting('site_tagline', 'Health & Safety Consultants');
$contactPhone = getSetting('contact_phone', '01530 382 150');
$contactPhone2 = getSetting('contact_phone_2', '');
$contactEmail = getSetting('contact_email', SITE_EMAIL);
$siteLogo = getSetting('site_logo', '/assets/images/logo.png');
$siteFavicon = getSetting('site_favicon', '/assets/images/favicon.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? $siteName) ?></title>
    <meta name="description" content="<?= e($metaDescription ?? 'Health & Safety Consultants in Leicestershire. Fire risk assessments, IOSH training, and consultancy services.') ?>">
    <?php if (!empty($metaKeywords)): ?>
    <meta name="keywords" content="<?= e($metaKeywords) ?>">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($pageTitle ?? $siteName) ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? '') ?>">
    <meta property="og:url" content="<?= SITE_URL . $_SERVER['REQUEST_URI'] ?>">
    <meta property="og:site_name" content="<?= e($siteName) ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?= e($siteFavicon) ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            100: '#e8eef4',
                            600: '#2a4a6e',
                            700: '#1e3a5f',
                            800: '#132337',
                            900: '#0c1929'
                        },
                        orange: {
                            100: '#fff0e6',
                            500: '#e85d04',
                            600: '#dc5503'
                        },
                        cream: {
                            DEFAULT: '#faf9f7',
                            dark: '#f5f3ef'
                        },
                        green: {
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a'
                        }
                    },
                    fontFamily: {
                        heading: ['Poppins', 'sans-serif'],
                        body: ['Plus Jakarta Sans', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }

        /* Prose-like content styling */
        .prose { color: #374151; line-height: 1.75; }
        .prose h2 { font-family: 'Poppins', sans-serif; font-size: 1.5rem; font-weight: 600; color: #0c1929; margin-top: 2.5rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e85d04; }
        .prose h2:first-child { margin-top: 0; }
        .prose h3 { font-family: 'Poppins', sans-serif; font-size: 1.125rem; font-weight: 600; color: #132337; margin-top: 1.5rem; margin-bottom: 0.5rem; }
        .prose p { margin-bottom: 1.25rem; }
        .prose ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .prose ul li { margin-bottom: 0.5rem; position: relative; }
        .prose ul li::marker { color: #e85d04; }
        .prose ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .prose ol li { margin-bottom: 0.5rem; }
        .prose strong { font-weight: 600; color: #0c1929; }
        .prose a { color: #e85d04; text-decoration: underline; }
        .prose a:hover { color: #dc5503; }

        /* Blob animations */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
    </style>

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "<?= e($siteName) ?>",
        "description": "Health and safety consultants providing fire risk assessments, IOSH training, and consultancy services in Leicestershire.",
        "telephone": "<?= e($contactPhone) ?>",
        "email": "<?= e($contactEmail) ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Coalville",
            "addressRegion": "Leicestershire",
            "addressCountry": "GB"
        },
        "areaServed": ["Leicestershire", "Midlands", "UK"],
        "priceRange": "££"
    }
    </script>
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
                <img src="<?= e($siteLogo) ?>" alt="<?= e($siteName) ?>" class="h-16 w-auto">
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="/" class="text-navy-800 font-medium hover:text-orange-500 transition-colors">Home</a>
                <a href="/services" class="text-navy-800 font-medium hover:text-orange-500 transition-colors">Services</a>
                <a href="/training" class="text-navy-800 font-medium hover:text-orange-500 transition-colors">Training</a>
                <a href="/about" class="text-navy-800 font-medium hover:text-orange-500 transition-colors">About</a>
                <a href="/contact" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors shadow-md hover:shadow-lg">
                    Get a Quote
                </a>
            </nav>

            <!-- Mobile Menu Button -->
            <button id="mobileMenuBtn" class="lg:hidden p-2 text-navy-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <a href="/contact" class="block bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-orange-600">
                    Get a Quote
                </a>
            </div>
        </div>
    </header>

    <main>
