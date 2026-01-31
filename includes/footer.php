    </main>

    <!-- Footer -->
    <footer class="bg-navy-900 text-white pt-16 pb-8">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Company Info -->
                <div>
                    <h3 class="font-heading font-semibold text-lg mb-4"><?= e(getSetting('site_name', SITE_NAME)) ?></h3>
                    <p class="text-gray-400 mb-4">Professional health and safety consultancy services for businesses across Leicestershire and the Midlands.</p>
                    <div class="flex gap-4">
                        <?php if ($fb = getSetting('facebook_url')): ?>
                        <a href="<?= e($fb) ?>" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5h-4.33C10.24.5,9.5,3.44,9.5,5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4Z"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if ($li = getSetting('linkedin_url')): ?>
                        <a href="<?= e($li) ?>" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447,20.452H16.893V14.883c0-1.328-.027-3.037-1.852-3.037-1.853,0-2.136,1.445-2.136,2.939v5.667H9.351V9h3.414v1.561h.046a3.745,3.745,0,0,1,3.37-1.85c3.6,0,4.267,2.37,4.267,5.455v6.286ZM5.337,7.433A2.064,2.064,0,1,1,7.4,5.368,2.062,2.062,0,0,1,5.337,7.433ZM7.119,20.452H3.555V9H7.119ZM22.225,0H1.771A1.75,1.75,0,0,0,0,1.729V22.271A1.749,1.749,0,0,0,1.771,24H22.222A1.756,1.756,0,0,0,24,22.271V1.729A1.756,1.756,0,0,0,22.222,0Z"/></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Services -->
                <div>
                    <h3 class="font-heading font-semibold text-lg mb-4">Services</h3>
                    <ul class="space-y-2">
                        <?php foreach (getServices() as $service): ?>
                        <li><a href="/services/<?= e($service['slug']) ?>" class="text-gray-400 hover:text-white transition-colors"><?= e($service['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Training -->
                <div>
                    <h3 class="font-heading font-semibold text-lg mb-4">Training</h3>
                    <ul class="space-y-2">
                        <?php foreach (getTraining() as $course): ?>
                        <li><a href="/training/<?= e($course['slug']) ?>" class="text-gray-400 hover:text-white transition-colors"><?= e($course['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="font-heading font-semibold text-lg mb-4">Contact</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span><?= e(getSetting('address_line1')) ?><br><?= e(getSetting('address_line2')) ?></span>
                        </li>
                        <li>
                            <a href="tel:<?= preg_replace('/\s+/', '', getSetting('contact_phone')) ?>" class="flex items-center gap-3 hover:text-white transition-colors">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <?= e(getSetting('contact_phone')) ?>
                            </a>
                        </li>
                        <li>
                            <a href="mailto:<?= e(getSetting('contact_email')) ?>" class="flex items-center gap-3 hover:text-white transition-colors">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <?= e(getSetting('contact_email')) ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-navy-700 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-400 text-sm">&copy; <?= date('Y') ?> <?= e(getSetting('site_name', SITE_NAME)) ?>. All rights reserved.</p>
                <div class="flex flex-wrap justify-center gap-4 md:gap-6 text-sm text-gray-400">
                    <a href="/privacy" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="/terms" class="hover:text-white transition-colors">Terms & Conditions</a>
                    <button type="button" onclick="openCookieSettings()" class="hover:text-white transition-colors">Cookie Settings</button>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        });
    </script>

    <?php
    // Cookie consent banner
    require_once INCLUDES_PATH . '/cookies.php';
    outputCookieConsentBanner();
    ?>
</body>
</html>
