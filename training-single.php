<?php
/**
 * Single Training Course Page - Section-based with fallback
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/sections.php';

$slug = $_GET['slug'] ?? '';
$course = getTrainingCourse($slug);

if (!$course) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

// Try to decode content as JSON, fall back to treating as HTML
$contentData = json_decode($course['content'], true);
$isStructured = is_array($contentData);

$pageTitle = $course['title'] . ' | Training | ' . getSetting('site_name', SITE_NAME);
$metaDescription = $course['meta_description'] ?: $course['short_description'];

// Get sections for this training course
$sections = getSections('training', $course['id']);

// Check if we have hero-related sections at the start
$hasHeroSections = false;
$heroSections = [];
$contentSections = [];

foreach ($sections as $section) {
    $type = $section['section_type'];
    if (!$hasHeroSections && in_array($type, ['page_header', 'hero'])) {
        $heroSections[] = $section;
        if ($type === 'hero') {
            $hasHeroSections = true;
        }
    } else {
        $contentSections[] = $section;
    }
}

$relatedCourses = dbFetchAll("SELECT * FROM training WHERE id != ? AND is_active = 1 ORDER BY RAND() LIMIT 3", [$course['id']]);

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
                <p class="text-orange-500 font-semibold text-sm uppercase tracking-wider mb-4">Training Courses</p>
                <h1 class="font-heading text-4xl md:text-5xl font-bold text-navy-900 mb-6">
                    <?= e($course['title']) ?>
                </h1>
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    <?= e($isStructured && !empty($contentData['overview']) ? $contentData['overview'] : $course['short_description']) ?>
                </p>

                <!-- Course Info Badges -->
                <div class="flex flex-wrap gap-3 mb-8">
                    <?php if ($course['duration']): ?>
                    <span class="bg-navy-100 text-navy-800 px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <?= e($course['duration']) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($course['certification']): ?>
                    <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                        <?= e($course['certification']) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($course['delivery_method']): ?>
                    <span class="bg-orange-100 text-orange-700 px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <?= e($course['delivery_method']) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <div class="flex flex-wrap gap-4">
                    <a href="/contact" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors shadow-md">
                        Book This Course
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
                // Default training image
                $heroImage = $course['image'] ?: '/uploads/training-session.jpg';
                ?>
                <img src="<?= e($heroImage) ?>" alt="<?= e($course['title']) ?>" class="w-full h-[360px] object-cover rounded-2xl shadow-xl">
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

                <!-- Course Overview -->
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <h2 class="font-heading text-2xl font-semibold text-navy-900 mb-4">Course Overview</h2>
                    <p class="text-gray-600 leading-relaxed"><?= e($contentData['overview'] ?? $course['short_description']) ?></p>
                </div>

                <!-- What You'll Learn -->
                <?php if (!empty($contentData['learningOutcomes'])): ?>
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        </div>
                        <h2 class="font-heading text-2xl font-semibold text-navy-900">What You'll Learn</h2>
                    </div>
                    <p class="text-gray-600 mb-4">By the end of this course, delegates will be able to:</p>
                    <ul class="space-y-3">
                        <?php foreach ($contentData['learningOutcomes'] as $outcome): ?>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600"><?= e($outcome) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Who Should Attend -->
                <?php if (!empty($contentData['whoShouldAttend'])): ?>
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h2 class="font-heading text-2xl font-semibold text-navy-900">Who Should Attend</h2>
                    </div>
                    <p class="text-gray-600 leading-relaxed"><?= e($contentData['whoShouldAttend']) ?></p>
                </div>
                <?php endif; ?>

                <!-- Book This Course -->
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <h2 class="font-heading text-2xl font-semibold text-navy-900 mb-4">Book This Course</h2>
                    <p class="text-gray-600 mb-4">We offer this training course at your premises (in-house) or as part of our scheduled open courses in Leicestershire. Contact us to discuss your requirements and receive a quote tailored to your needs.</p>
                    <p class="text-gray-600 mb-6">For group bookings of 6 or more delegates, in-house training at your premises is often the most cost-effective option. We can tailor the course content to your specific industry and workplace.</p>
                    <a href="/contact" class="inline-block bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                        Enquire About This Course
                    </a>
                </div>

                <?php else: ?>
                <!-- Fallback: Render HTML content in a card -->
                <div class="bg-white rounded-2xl p-8 md:p-10 shadow-sm">
                    <div class="prose prose-lg max-w-none">
                        <?= $course['content'] ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Course Details Card -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-heading text-lg font-semibold text-navy-900 mb-4">Course Details</h3>
                    <dl class="space-y-4">
                        <?php if ($course['duration']): ?>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <dt class="text-gray-500 text-sm">Duration</dt>
                                <dd class="font-semibold text-navy-900"><?= e($course['duration']) ?></dd>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($course['certification']): ?>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                            <div>
                                <dt class="text-gray-500 text-sm">Certification</dt>
                                <dd class="font-semibold text-navy-900"><?= e($course['certification']) ?></dd>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($course['delivery_method']): ?>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <div>
                                <dt class="text-gray-500 text-sm">Delivery</dt>
                                <dd class="font-semibold text-navy-900"><?= e($course['delivery_method']) ?></dd>
                            </div>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>

                <!-- Other Courses -->
                <?php if (!empty($relatedCourses)): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-heading text-lg font-semibold text-navy-900 mb-4">Other Courses</h3>
                    <ul class="space-y-3">
                        <?php foreach ($relatedCourses as $related): ?>
                        <li>
                            <a href="/training/<?= e($related['slug']) ?>" class="flex items-center gap-2 text-navy-800 hover:text-orange-500 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <?= e($related['title']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Contact Card -->
                <div class="bg-navy-900 rounded-2xl p-6 text-white">
                    <h3 class="font-heading text-lg font-semibold mb-3">Ready to Book?</h3>
                    <p class="text-white/80 text-sm mb-4">Contact us to discuss dates, group sizes, and pricing for this course.</p>
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
        <h2 class="font-heading text-3xl md:text-4xl font-bold text-white mb-4">Need Training for Your Team?</h2>
        <p class="text-gray-300 text-lg mb-8">We offer in-house training at your premises. Contact us to discuss your requirements and receive a tailored quote.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/contact" class="bg-orange-500 text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-600 transition-colors shadow-lg">
                Get a Training Quote
            </a>
            <a href="/training" class="bg-white/10 text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition-colors">
                View All Courses
            </a>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
