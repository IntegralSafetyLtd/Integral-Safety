<?php
/**
 * Homepage
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$page = getPage('home');
$pageTitle = $page['title'] . ' | ' . getSetting('site_name', SITE_NAME);
$metaDescription = $page['meta_description'];

$services = getServices(true);
$testimonials = getTestimonials();

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="py-20 md:py-24 bg-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-1/2 h-full opacity-70 pointer-events-none">
        <div class="absolute top-[30%] right-[30%] w-[400px] h-[400px] bg-orange-100 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[20%] right-[10%] w-[300px] h-[300px] bg-green-100 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-6xl mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-center relative z-10">
            <div>
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 bg-green-100 text-green-600 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    IOSH Approved Training Provider
                </div>

                <h1 class="font-heading text-4xl md:text-5xl lg:text-6xl font-semibold text-navy-900 mb-6 leading-tight">
                    <?= e($page['hero_title'] ?: "Leicestershire's Trusted") ?>
                    <span class="text-orange-500">Health & Safety</span> Experts
                </h1>

                <p class="text-lg text-gray-600 mb-8 max-w-lg leading-relaxed">
                    <?= e($page['hero_subtitle'] ?: 'From fire risk assessments to IOSH training, we help Midlands businesses create safer workplaces. Over 20 years of experience protecting your people, property, and peace of mind.') ?>
                </p>

                <div class="flex flex-wrap gap-4 mb-12">
                    <a href="/contact" class="bg-orange-500 text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-600 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                        Get Your Free Quote
                    </a>
                    <a href="/services" class="bg-white text-navy-900 px-8 py-4 rounded-lg font-semibold border-2 border-navy-100 hover:border-navy-800 transition-colors">
                        Explore Our Services
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="flex items-center gap-4">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full bg-navy-700 border-2 border-white flex items-center justify-center text-white text-xs font-semibold">CJ</div>
                        <div class="w-10 h-10 rounded-full bg-navy-600 border-2 border-white flex items-center justify-center text-white text-xs font-semibold">SK</div>
                        <div class="w-10 h-10 rounded-full bg-navy-700 border-2 border-white flex items-center justify-center text-white text-xs font-semibold">MS</div>
                        <div class="w-10 h-10 rounded-full bg-navy-600 border-2 border-white flex items-center justify-center text-white text-xs font-semibold">PC</div>
                    </div>
                    <div class="text-sm text-gray-600">
                        <strong class="text-navy-900">Trusted by 100+ organisations</strong><br>
                        Housing associations, construction, hospitality & more
                    </div>
                </div>
            </div>

            <!-- Hero Image -->
            <div class="relative hidden lg:block">
                <div class="relative w-full h-[480px] rounded-2xl shadow-2xl overflow-hidden">
                    <img src="/uploads/safety-consultant.jpg" alt="Health and safety consultant conducting a workplace inspection" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-24 bg-cream" id="services">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-14">
            <p class="text-sm font-bold text-orange-500 uppercase tracking-widest mb-3">Our Services</p>
            <h2 class="font-heading text-3xl md:text-4xl font-semibold text-navy-900 mb-4">Comprehensive Health & Safety Solutions</h2>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Practical, proportionate advice that protects your people and keeps your business compliant.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($services as $service): ?>
            <a href="/services/<?= e($service['slug']) ?>" class="group bg-white rounded-2xl p-8 border border-transparent transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-orange-100">
                <div class="w-14 h-14 bg-cream rounded-xl flex items-center justify-center mb-5 transition-colors group-hover:bg-orange-100">
                    <?= getIcon($service['icon'] ?? 'clipboard', 'w-7 h-7 text-navy-700 group-hover:text-orange-600 transition-colors') ?>
                </div>
                <h3 class="font-heading font-bold text-xl text-navy-900 mb-3"><?= e($service['title']) ?></h3>
                <p class="text-gray-600 leading-relaxed"><?= e($service['short_description']) ?></p>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="/services" class="text-orange-500 font-semibold hover:text-orange-600 transition-colors">
                View All Services &rarr;
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-24 bg-white">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <p class="text-sm font-bold text-orange-500 uppercase tracking-widest mb-3">Why Choose Us</p>
                <h2 class="font-heading text-3xl md:text-4xl font-semibold text-navy-900 mb-6">20+ Years Protecting Midlands Businesses</h2>
                <p class="text-gray-600 text-lg mb-8">
                    We're not just consultants – we're your partners in creating safer workplaces. Our practical, no-nonsense approach means you get real solutions, not just paperwork.
                </p>

                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy-900">IOSH Approved Training Provider</h4>
                            <p class="text-gray-600">Nationally recognised qualifications</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy-900">Local Knowledge</h4>
                            <p class="text-gray-600">Based in Coalville & Melton Mowbray</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy-900">Practical Approach</h4>
                            <p class="text-gray-600">Solutions that work for your business</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="w-full h-[400px] rounded-2xl shadow-xl overflow-hidden">
                    <img src="/uploads/consultation.jpg" alt="Health and safety consultation meeting" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<?php if (!empty($testimonials)): ?>
<section class="py-24 bg-cream">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-14">
            <p class="text-sm font-bold text-orange-500 uppercase tracking-widest mb-3">Testimonials</p>
            <h2 class="font-heading text-3xl md:text-4xl font-semibold text-navy-900">What Our Clients Say</h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach (array_slice($testimonials, 0, 3) as $testimonial): ?>
            <div class="bg-white rounded-2xl p-8 shadow-lg">
                <div class="flex gap-1 mb-4">
                    <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    <?php endfor; ?>
                </div>
                <p class="text-gray-600 mb-6 italic">"<?= e($testimonial['content']) ?>"</p>
                <div>
                    <p class="font-semibold text-navy-900"><?= e($testimonial['client_name']) ?></p>
                    <?php if ($testimonial['company']): ?>
                    <p class="text-gray-500 text-sm"><?= e($testimonial['company']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-20 bg-navy-800">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-heading text-3xl md:text-4xl font-semibold text-white mb-6">
            Ready to Improve Your Workplace Safety?
        </h2>
        <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
            Get in touch today for a free, no-obligation consultation. We'll discuss your needs and provide practical solutions tailored to your business.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/contact" class="bg-orange-500 text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-600 transition-all shadow-lg">
                Get Your Free Quote
            </a>
            <a href="tel:<?= preg_replace('/\s+/', '', getSetting('contact_phone')) ?>" class="bg-transparent text-white px-8 py-4 rounded-lg font-semibold border-2 border-white/40 hover:border-white hover:bg-white/10 transition-colors">
                Call <?= e(getSetting('contact_phone')) ?>
            </a>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
