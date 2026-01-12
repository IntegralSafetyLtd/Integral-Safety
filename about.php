<?php
/**
 * About Page - Section-based with fallback
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/sections.php';

$page = getPage('about');
$pageTitle = 'About Us | ' . getSetting('site_name', SITE_NAME);
$metaDescription = $page['meta_description'] ?: 'Learn about Integral Safety Ltd - experienced health and safety consultants serving Leicestershire and the Midlands.';

// Get sections for about page
$sections = getSections('page', $page['id']);
$useSections = !empty($sections);

require_once INCLUDES_PATH . '/header.php';

if ($useSections):
    // Render all sections from the database
    foreach ($sections as $section):
        renderSection($section);
    endforeach;
else:
// Fallback: Original hardcoded content when no sections exist
?>

<!-- Hero -->
<section class="py-16 bg-navy-800 text-white">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <h1 class="font-heading text-4xl md:text-5xl font-semibold mb-4"><?= e($page['hero_title'] ?: 'About Us') ?></h1>
        <p class="text-gray-300 text-lg max-w-2xl mx-auto">
            <?= e($page['hero_subtitle'] ?: 'Over 20 years of experience protecting businesses across Leicestershire and the Midlands.') ?>
        </p>
    </div>
</section>

<!-- Content -->
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-center mb-20">
            <div>
                <h2 class="font-heading text-3xl font-semibold text-navy-900 mb-6">Your Local Health & Safety Experts</h2>
                <div class="prose prose-lg text-gray-600">
                    <?php if ($page['content']): ?>
                        <?= $page['content'] ?>
                    <?php else: ?>
                        <p>Integral Safety Ltd is a leading health and safety consultancy based in Leicestershire, serving businesses across the Midlands and beyond.</p>
                        <p>With over 20 years of experience, we provide practical, proportionate advice that protects your people while keeping your business compliant with health and safety regulations.</p>
                        <p>Our team of qualified consultants works with organisations of all sizes, from small businesses to large corporations, delivering tailored solutions that fit your specific needs and budget.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="relative">
                <div class="w-full h-[400px] rounded-2xl shadow-xl overflow-hidden">
                    <img src="/uploads/workplace-safety.jpg" alt="Workplace safety inspection" class="w-full h-full object-cover">
                </div>
                <!-- Stats overlay -->
                <div class="absolute -bottom-6 left-6 right-6 bg-white rounded-xl p-6 shadow-lg">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="font-heading text-2xl font-semibold text-orange-500">20+</div>
                            <div class="text-xs text-gray-600">Years Experience</div>
                        </div>
                        <div>
                            <div class="font-heading text-2xl font-semibold text-orange-500">100+</div>
                            <div class="text-xs text-gray-600">Clients Served</div>
                        </div>
                        <div>
                            <div class="font-heading text-2xl font-semibold text-orange-500">2</div>
                            <div class="text-xs text-gray-600">Local Offices</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values -->
        <div class="text-center mb-12">
            <h2 class="font-heading text-3xl font-semibold text-navy-900 mb-4">Our Values</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-20">
            <div class="bg-cream rounded-2xl p-8 text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-6">
                    <?= getIcon('shield', 'w-8 h-8 text-orange-500') ?>
                </div>
                <h3 class="font-heading font-semibold text-xl text-navy-900 mb-3">Integrity</h3>
                <p class="text-gray-600">Honest, straightforward advice you can trust.</p>
            </div>

            <div class="bg-cream rounded-2xl p-8 text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-6">
                    <?= getIcon('users', 'w-8 h-8 text-orange-500') ?>
                </div>
                <h3 class="font-heading font-semibold text-xl text-navy-900 mb-3">Partnership</h3>
                <p class="text-gray-600">We work alongside you, not just for you.</p>
            </div>

            <div class="bg-cream rounded-2xl p-8 text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-6">
                    <?= getIcon('check', 'w-8 h-8 text-orange-500') ?>
                </div>
                <h3 class="font-heading font-semibold text-xl text-navy-900 mb-3">Practical</h3>
                <p class="text-gray-600">Real-world solutions, not just paperwork.</p>
            </div>
        </div>

        <!-- Accreditations -->
        <div class="bg-navy-800 rounded-2xl p-12 text-center text-white">
            <h2 class="font-heading text-2xl font-semibold mb-6">Accreditations & Memberships</h2>
            <div class="flex flex-wrap justify-center gap-8 items-center">
                <div class="bg-white/10 px-6 py-3 rounded-lg">IOSH Approved</div>
                <div class="bg-white/10 px-6 py-3 rounded-lg">CHAS Accredited</div>
                <div class="bg-white/10 px-6 py-3 rounded-lg">Constructionline</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-orange-500 text-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-heading text-3xl font-semibold mb-4">Ready to Work Together?</h2>
        <p class="text-white/90 mb-8">Get in touch today for a free consultation about your health and safety needs.</p>
        <a href="/contact" class="inline-block bg-white text-orange-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
            Contact Us
        </a>
    </div>
</section>

<?php endif; ?>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
