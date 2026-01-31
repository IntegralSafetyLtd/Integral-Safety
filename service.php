<?php
/**
 * Single Service Page - Section-based with fallback
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/sections.php';

$slug = $_GET['slug'] ?? '';
$service = getService($slug);

if (!$service) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

// Try to decode content as JSON, fall back to treating as HTML
$contentData = json_decode($service['content'], true);
$isStructured = is_array($contentData);

$pageTitle = $service['title'] . ' | ' . getSetting('site_name', SITE_NAME);
$metaDescription = $service['meta_description'] ?: $service['short_description'];

// Get sections for this service
$sections = getSections('service', $service['id']);

// Check if we have hero-related sections at the start
$hasHeroSections = false;
$heroSections = [];
$contentSections = [];

foreach ($sections as $section) {
    $type = $section['section_type'];
    if (!$hasHeroSections && in_array($type, ['page_header', 'hero'])) {
        $heroSections[] = $section;
        if ($type === 'hero') {
            $hasHeroSections = true; // Found a hero, stop collecting hero sections
        }
    } else {
        $contentSections[] = $section;
    }
}

// Get related services
$relatedServices = dbFetchAll("SELECT * FROM services WHERE id != ? AND is_active = 1 ORDER BY RAND() LIMIT 3", [$service['id']]);

// Get related blog post if one is linked and is live
$relatedBlog = null;
if (!empty($service['related_blog_id'])) {
    $relatedBlog = dbFetchOne(
        "SELECT id, title, slug, excerpt, featured_image FROM blog_posts
         WHERE id = ? AND (status = 'published' OR (status = 'scheduled' AND published_at <= NOW()))",
        [$service['related_blog_id']]
    );
}

require_once INCLUDES_PATH . '/header.php';
?>

<?php if (!empty($heroSections)): ?>
<!-- Section-based Hero -->
<?php foreach ($heroSections as $section): ?>
    <?php renderSection($section); ?>
<?php endforeach; ?>
<?php else: ?>
<!-- Default Hero Section -->
<section class="py-16 md:py-20 bg-white">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="max-w-xl">
                <p class="text-orange-500 font-semibold text-sm uppercase tracking-wider mb-4">Our Services</p>
                <h1 class="font-heading text-4xl md:text-5xl font-bold text-navy-900 mb-6">
                    <?= e($isStructured && !empty($contentData['heroHeading']) ? $contentData['heroHeading'] : $service['title']) ?>
                </h1>
                <p class="text-gray-600 text-lg leading-relaxed mb-8">
                    <?= e($isStructured && !empty($contentData['heroSubheading']) ? $contentData['heroSubheading'] : $service['short_description']) ?>
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="/contact" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors shadow-md">
                        Get a Free Quote
                    </a>
                    <a href="tel:<?= preg_replace('/\s+/', '', getSetting('contact_phone')) ?>" class="border-2 border-navy-800 text-navy-800 px-6 py-3 rounded-lg font-semibold hover:bg-navy-800 hover:text-white transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <?= e(getSetting('contact_phone')) ?>
                    </a>
                </div>
            </div>

            <!-- Hero Image -->
            <div class="hidden lg:block">
                <?php
                // Service-specific images based on slug
                $serviceImages = [
                    'fire-risk-assessments' => '/uploads/fire-safety.jpg',
                    'health-and-safety-consultancy' => '/uploads/consultation.jpg',
                    'iosh-training' => '/uploads/training-session.jpg',
                    'health-and-safety-audits' => '/uploads/workplace-safety.jpg',
                    'drone-surveys' => '/uploads/workplace-safety.jpg',
                    'cdr-services' => '/uploads/safety-consultant.jpg',
                ];
                $heroImage = $service['image'] ?: ($serviceImages[$service['slug']] ?? '/uploads/safety-consultant.jpg');
                ?>
                <img src="<?= e($heroImage) ?>" alt="<?= e($service['title']) ?>" class="w-full h-[360px] object-cover rounded-2xl shadow-xl">
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Content Section -->
<section class="py-16 bg-cream">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">

                <?php if ($isStructured): ?>

                <!-- About This Service -->
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <h2 class="font-heading text-2xl font-semibold text-navy-900 mb-4">About This Service</h2>
                    <p class="text-gray-600 leading-relaxed"><?= e($contentData['heroSubheading'] ?? $service['short_description']) ?></p>
                </div>

                <!-- What We Assess -->
                <?php if (!empty($contentData['whatWeAssess'])): ?>
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h2 class="font-heading text-2xl font-semibold text-navy-900">What We Assess</h2>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <?php foreach ($contentData['whatWeAssess'] as $item): ?>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600"><?= e($item) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Our Process -->
                <?php if (!empty($contentData['processSteps'])): ?>
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <h2 class="font-heading text-2xl font-semibold text-navy-900 mb-6">Our Process</h2>
                    <div class="space-y-6">
                        <?php foreach ($contentData['processSteps'] as $index => $step): ?>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                <?= $index + 1 ?>
                            </div>
                            <div>
                                <h3 class="font-semibold text-navy-900 mb-1"><?= e($step['title']) ?></h3>
                                <p class="text-gray-600"><?= e($step['description']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- What You'll Receive -->
                <?php if (!empty($contentData['whatYouReceive'])): ?>
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h2 class="font-heading text-2xl font-semibold text-navy-900">What You'll Receive</h2>
                    </div>
                    <ul class="space-y-3">
                        <?php foreach ($contentData['whatYouReceive'] as $item): ?>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600"><?= e($item) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Types of Premises -->
                <?php if (!empty($contentData['premisesTypes'])): ?>
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h2 class="font-heading text-2xl font-semibold text-navy-900">Types of Premises We Cover</h2>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <?php foreach ($contentData['premisesTypes'] as $type): ?>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600"><?= e($type) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- FAQs -->
                <?php if (!empty($contentData['faqs'])): ?>
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <h2 class="font-heading text-2xl font-semibold text-navy-900 mb-6">Frequently Asked Questions</h2>
                    <div class="space-y-6">
                        <?php foreach ($contentData['faqs'] as $faq): ?>
                        <div class="border-b border-gray-100 pb-6 last:border-0 last:pb-0">
                            <h3 class="font-semibold text-navy-900 mb-2"><?= e($faq['question']) ?></h3>
                            <p class="text-gray-600"><?= e($faq['answer']) ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <!-- Fallback: Render HTML content in a card -->
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <div class="prose prose-lg max-w-none">
                        <?= $service['content'] ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Key Benefits -->
                <?php if ($isStructured && !empty($contentData['benefits'])): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-heading text-lg font-semibold text-navy-900 mb-4">Key Benefits</h3>
                    <ul class="space-y-3">
                        <?php foreach ($contentData['benefits'] as $benefit): ?>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600 text-sm"><?= e($benefit) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Related Services -->
                <?php if (!empty($relatedServices)): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-heading text-lg font-semibold text-navy-900 mb-4">Related Services</h3>
                    <ul class="space-y-3">
                        <?php foreach ($relatedServices as $related): ?>
                        <li>
                            <a href="/services/<?= e($related['slug']) ?>" class="flex items-center gap-2 text-navy-800 hover:text-orange-500 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <?= e($related['title']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Related Blog Post -->
                <?php if ($relatedBlog): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-heading text-lg font-semibold text-navy-900 mb-4">Learn More</h3>
                    <?php if (!empty($relatedBlog['featured_image'])): ?>
                    <a href="/blog/<?= e($relatedBlog['slug']) ?>" class="block mb-4">
                        <img src="<?= e($relatedBlog['featured_image']) ?>" alt="<?= e($relatedBlog['title']) ?>" class="w-full h-32 object-cover rounded-lg">
                    </a>
                    <?php endif; ?>
                    <p class="text-gray-600 text-sm mb-4"><?= e($relatedBlog['excerpt'] ?: 'Read our detailed guide on this topic.') ?></p>
                    <a href="/blog/<?= e($relatedBlog['slug']) ?>" class="inline-flex items-center gap-2 text-orange-500 font-semibold hover:text-orange-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Read Our Blog
                    </a>
                </div>
                <?php endif; ?>

                <!-- Contact Card -->
                <div class="bg-navy-900 rounded-2xl p-6 text-white">
                    <h3 class="font-heading text-lg font-semibold mb-3">Ready to Get Started?</h3>
                    <p class="text-white/80 text-sm mb-4">Contact us for a free, no-obligation quote or to discuss your requirements.</p>
                    <a href="tel:<?= preg_replace('/\s+/', '', getSetting('contact_phone')) ?>" class="block w-full bg-orange-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors mb-3">
                        <?= e(getSetting('contact_phone')) ?>
                    </a>
                    <a href="/contact" class="block w-full bg-white/10 text-white text-center py-3 rounded-lg font-semibold hover:bg-white/20 transition-colors">
                        Send Enquiry
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($contentSections)): ?>
<!-- Additional Sections from Section Editor -->
<?php foreach ($contentSections as $section): ?>
    <?php renderSection($section); ?>
<?php endforeach; ?>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-16 bg-navy-800">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-heading text-3xl md:text-4xl font-bold text-white mb-4">Need <?= e($service['title']) ?>?</h2>
        <p class="text-gray-300 text-lg mb-8">Get in touch today for a free, no-obligation quote. We're here to help protect your business.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/contact" class="bg-orange-500 text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-600 transition-colors shadow-lg">
                Get Your Free Quote
            </a>
            <a href="tel:<?= preg_replace('/\s+/', '', getSetting('contact_phone')) ?>" class="bg-white/10 text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition-colors">
                Call <?= e(getSetting('contact_phone')) ?>
            </a>
        </div>
    </div>
</section>

<!-- Breadcrumb Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "<?= SITE_URL ?>"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "Services",
            "item": "<?= SITE_URL ?>/services"
        },
        {
            "@type": "ListItem",
            "position": 3,
            "name": <?= json_encode($service['title']) ?>,
            "item": "<?= SITE_URL ?>/services/<?= e($service['slug']) ?>"
        }
    ]
}
</script>

<!-- Service Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": <?= json_encode($service['title']) ?>,
    "description": <?= json_encode($service['meta_description'] ?: $service['short_description']) ?>,
    "provider": {
        "@type": "LocalBusiness",
        "name": "<?= e(getSetting('site_name', SITE_NAME)) ?>",
        "url": "<?= SITE_URL ?>"
    },
    "areaServed": {
        "@type": "Place",
        "name": "Leicestershire and the East Midlands"
    },
    "url": "<?= SITE_URL ?>/services/<?= e($service['slug']) ?>"
}
</script>

<?php if ($isStructured && !empty($contentData['faqs'])): ?>
<!-- FAQ Schema for Rich Results -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        <?php foreach ($contentData['faqs'] as $index => $faq): ?>
        {
            "@type": "Question",
            "name": <?= json_encode($faq['question']) ?>,
            "acceptedAnswer": {
                "@type": "Answer",
                "text": <?= json_encode($faq['answer']) ?>
            }
        }<?= $index < count($contentData['faqs']) - 1 ? ',' : '' ?>
        <?php endforeach; ?>
    ]
}
</script>
<?php endif; ?>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
