<?php
/**
 * Contact Page - Section-based header with contact form
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/sections.php';

$page = getPage('contact');
$pageTitle = 'Contact Us | ' . getSetting('site_name', SITE_NAME);
$metaDescription = 'Get in touch with Integral Safety Ltd for a free quote on health and safety services.';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simple honeypot spam check
    if (!empty($_POST['website'])) {
        // Bot detected
        $success = true; // Pretend success
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $phone = sanitize($_POST['phone'] ?? '');
        $company = sanitize($_POST['company'] ?? '');
        $message = sanitize($_POST['message'] ?? '');

        if (empty($name) || !$email || empty($message)) {
            $error = 'Please fill in all required fields.';
        } else {
            $sent = sendContactEmail([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'company' => $company,
                'message' => $message
            ]);

            if ($sent) {
                $success = true;
            } else {
                $error = 'There was a problem sending your message. Please try again or call us directly.';
            }
        }
    }
}

// Get sections for contact page (header sections only)
$sections = getSections('page', $page['id']);
$headerSections = [];
foreach ($sections as $section) {
    if (in_array($section['section_type'], ['page_header', 'hero'])) {
        $headerSections[] = $section;
    }
}

require_once INCLUDES_PATH . '/header.php';

// Render header sections if they exist
if (!empty($headerSections)):
    foreach ($headerSections as $section):
        renderSection($section);
    endforeach;
else:
?>
<!-- Default Hero -->
<section class="py-16 bg-navy-800 text-white">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <h1 class="font-heading text-4xl md:text-5xl font-semibold mb-4"><?= e($page['hero_title'] ?: 'Contact Us') ?></h1>
        <p class="text-gray-300 text-lg max-w-2xl mx-auto">
            <?= e($page['hero_subtitle'] ?: 'Ready to improve your workplace safety? Get in touch for a free, no-obligation quote.') ?>
        </p>
    </div>
</section>
<?php endif; ?>

<!-- Contact Section -->
<section class="py-20 bg-cream">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <?php if ($success): ?>
                    <div class="bg-green-100 text-green-700 px-6 py-4 rounded-lg mb-6">
                        <h3 class="font-semibold">Thank you for your message!</h3>
                        <p>We'll get back to you within 24 hours.</p>
                    </div>
                    <?php else: ?>

                    <?php if ($error): ?>
                    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-6"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <!-- Honeypot -->
                        <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Your Name *</label>
                                <input type="text" name="name" required
                                       value="<?= e($_POST['name'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Email Address *</label>
                                <input type="email" name="email" required
                                       value="<?= e($_POST['email'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Phone Number</label>
                                <input type="tel" name="phone"
                                       value="<?= e($_POST['phone'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Company Name</label>
                                <input type="text" name="company"
                                       value="<?= e($_POST['company'] ?? '') ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">Your Message *</label>
                            <textarea name="message" rows="5" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"><?= e($_POST['message'] ?? '') ?></textarea>
                        </div>

                        <button type="submit"
                                class="w-full bg-orange-500 text-white py-4 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                            Send Message
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Info -->
            <div>
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                    <h3 class="font-heading text-xl font-semibold text-navy-900 mb-6">Contact Information</h3>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-navy-900">Phone</h4>
                                <a href="tel:<?= preg_replace('/\s+/', '', getSetting('contact_phone')) ?>" class="text-gray-600 hover:text-orange-500">
                                    <?= e(getSetting('contact_phone')) ?>
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-navy-900">Email</h4>
                                <a href="mailto:<?= e(getSetting('contact_email')) ?>" class="text-gray-600 hover:text-orange-500">
                                    <?= e(getSetting('contact_email')) ?>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="bg-navy-800 rounded-2xl p-8 text-white">
                    <h3 class="font-heading text-xl font-semibold mb-4">Quick Response</h3>
                    <p class="text-gray-300 mb-4">We aim to respond to all enquiries within 24 hours during business days.</p>
                    <p class="text-gray-300">For urgent matters, please call us directly.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Render additional sections after the contact form (if any)
$additionalSections = array_filter($sections, function($s) {
    return !in_array($s['section_type'], ['page_header', 'hero']);
});
foreach ($additionalSections as $section):
    renderSection($section);
endforeach;
?>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
