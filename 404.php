<?php
/**
 * 404 Not Found Page
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Page Not Found | ' . getSetting('site_name', SITE_NAME);
$metaDescription = 'The page you are looking for could not be found.';

require_once INCLUDES_PATH . '/header.php';
?>

<section class="py-32 bg-cream">
    <div class="max-w-2xl mx-auto px-6 text-center">
        <h1 class="font-heading text-6xl font-bold text-navy-800 mb-4">404</h1>
        <h2 class="font-heading text-2xl font-semibold text-navy-900 mb-4">Page Not Found</h2>
        <p class="text-gray-600 mb-8">
            Sorry, the page you're looking for doesn't exist or has been moved.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                Go Home
            </a>
            <a href="/contact" class="bg-white text-navy-900 px-6 py-3 rounded-lg font-semibold border-2 border-navy-100 hover:border-navy-800 transition-colors">
                Contact Us
            </a>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
