<?php
/**
 * Training Courses Listing
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Training Courses | ' . getSetting('site_name', SITE_NAME);
$metaDescription = 'IOSH approved health and safety training courses. From Managing Safely to specialist courses, we deliver practical training that works.';

$courses = getTraining();

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Hero -->
<section class="py-16 bg-navy-800 text-white">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <div class="inline-flex items-center gap-2 bg-green-500/20 text-green-400 px-4 py-2 rounded-full text-sm font-semibold mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            IOSH Approved Training Provider
        </div>
        <h1 class="font-heading text-4xl md:text-5xl font-semibold mb-4">Training Courses</h1>
        <p class="text-gray-300 text-lg max-w-2xl mx-auto">
            Practical, engaging training that gives your team the skills they need to stay safe.
        </p>
    </div>
</section>

<!-- Courses Grid -->
<section class="py-20 bg-cream">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($courses as $course): ?>
            <a href="/training/<?= e($course['slug']) ?>" class="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                <div class="p-8">
                    <h2 class="font-heading font-bold text-xl text-navy-900 mb-3 group-hover:text-orange-500 transition-colors"><?= e($course['title']) ?></h2>
                    <p class="text-gray-600 mb-4"><?= e($course['short_description']) ?></p>

                    <?php if ($course['duration'] || $course['certification']): ?>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php if ($course['duration']): ?>
                        <span class="text-xs bg-navy-100 text-navy-700 px-3 py-1 rounded-full"><?= e($course['duration']) ?></span>
                        <?php endif; ?>
                        <?php if ($course['certification']): ?>
                        <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full"><?= e($course['certification']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <span class="text-orange-500 font-semibold group-hover:text-orange-600">View Details &rarr;</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-navy-800 text-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-heading text-3xl font-semibold mb-4">Need a Bespoke Training Solution?</h2>
        <p class="text-gray-300 mb-8">We can tailor our training to meet your specific industry requirements and deliver on-site at your premises.</p>
        <a href="/contact" class="inline-block bg-orange-500 text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
            Discuss Your Requirements
        </a>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
