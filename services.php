<?php
/**
 * Services Listing Page
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Our Services | ' . getSetting('site_name', SITE_NAME);
$metaDescription = 'Professional health and safety services including fire risk assessments, IOSH training, consultancy, and more. Serving Leicestershire and the Midlands.';

$services = getServices();

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Hero -->
<section class="py-16 bg-navy-800 text-white">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <h1 class="font-heading text-4xl md:text-5xl font-semibold mb-4">Our Services</h1>
        <p class="text-gray-300 text-lg max-w-2xl mx-auto">
            Comprehensive health and safety solutions tailored to your business needs.
        </p>
    </div>
</section>

<!-- Services Grid -->
<section class="py-20 bg-cream">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($services as $service): ?>
            <a href="/services/<?= e($service['slug']) ?>" class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="w-14 h-14 bg-cream rounded-xl flex items-center justify-center mb-5 group-hover:bg-orange-100 transition-colors">
                    <?= getIcon($service['icon'] ?? 'clipboard', 'w-7 h-7 text-navy-700 group-hover:text-orange-600 transition-colors') ?>
                </div>
                <h2 class="font-heading font-bold text-xl text-navy-900 mb-3"><?= e($service['title']) ?></h2>
                <p class="text-gray-600 mb-4"><?= e($service['short_description']) ?></p>
                <span class="text-orange-500 font-semibold group-hover:text-orange-600">Learn more &rarr;</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-navy-800 text-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-heading text-3xl font-semibold mb-4">Not sure what you need?</h2>
        <p class="text-gray-300 mb-8">Contact us for a free consultation and we'll help identify the right services for your business.</p>
        <a href="/contact" class="inline-block bg-orange-500 text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
            Get Free Advice
        </a>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
