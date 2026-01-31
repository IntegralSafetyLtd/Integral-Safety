<?php
/**
 * Cookie Consent Management
 * Handles GDPR-compliant cookie consent with three options:
 * - Accept All: All cookies including analytics
 * - Essential Only: Only essential cookies
 * - Decline All: No non-essential cookies
 */

/**
 * Output the cookie consent banner HTML
 * Should be called just before </body>
 */
function outputCookieConsentBanner() {
    ?>
    <!-- Cookie Consent Banner -->
    <div id="cookieConsent" class="fixed inset-0 z-[9999] hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="cookieBackdrop"></div>

        <!-- Banner -->
        <div class="absolute bottom-0 left-0 right-0 bg-white shadow-2xl border-t border-gray-200 transform transition-transform duration-300" id="cookieBanner">
            <div class="max-w-6xl mx-auto px-6 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                    <!-- Text -->
                    <div class="flex-1">
                        <h3 class="font-heading font-semibold text-navy-900 text-lg mb-2">We value your privacy</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            We use cookies to enhance your browsing experience, analyse site traffic, and understand where our visitors come from.
                            You can choose to accept all cookies, essential cookies only, or decline non-essential cookies.
                            <a href="/privacy" class="text-orange-500 hover:text-orange-600 underline">Learn more in our Privacy Policy</a>.
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 lg:flex-shrink-0">
                        <button type="button" onclick="setCookieConsent('declined')"
                                class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors order-3 sm:order-1">
                            Decline All
                        </button>
                        <button type="button" onclick="setCookieConsent('essential')"
                                class="px-5 py-2.5 text-sm font-medium text-navy-800 bg-white border-2 border-navy-200 rounded-lg hover:border-navy-400 transition-colors order-2">
                            Essential Only
                        </button>
                        <button type="button" onclick="setCookieConsent('accepted')"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition-colors shadow-md order-1 sm:order-3">
                            Accept All
                        </button>
                    </div>
                </div>

                <!-- Cookie Details (collapsible) -->
                <div id="cookieDetails" class="hidden mt-6 pt-6 border-t border-gray-200">
                    <h4 class="font-semibold text-navy-900 mb-4">Cookie Categories</h4>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                <h5 class="font-semibold text-navy-900">Essential Cookies</h5>
                            </div>
                            <p class="text-gray-600">Required for the website to function. Cannot be disabled.</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                <h5 class="font-semibold text-navy-900">Analytics Cookies</h5>
                            </div>
                            <p class="text-gray-600">Help us understand how visitors use our site (Google Analytics, Microsoft Clarity).</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                <h5 class="font-semibold text-navy-900">Marketing Cookies</h5>
                            </div>
                            <p class="text-gray-600">Used to track visitors across websites for advertising purposes.</p>
                        </div>
                    </div>
                </div>

                <!-- Show Details Link -->
                <button type="button" onclick="toggleCookieDetails()" class="mt-4 text-sm text-orange-500 hover:text-orange-600 font-medium flex items-center gap-1">
                    <span id="cookieDetailsText">Show details</span>
                    <svg id="cookieDetailsIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Cookie Consent JavaScript -->
    <script>
        (function() {
            const CONSENT_KEY = 'cookie_consent';
            const CONSENT_EXPIRY_DAYS = 365;

            // Check if consent has been given
            function getConsent() {
                const consent = localStorage.getItem(CONSENT_KEY);
                if (consent) {
                    try {
                        return JSON.parse(consent);
                    } catch (e) {
                        return null;
                    }
                }
                return null;
            }

            // Save consent choice
            window.setCookieConsent = function(choice) {
                const consent = {
                    choice: choice,
                    timestamp: new Date().toISOString(),
                    analytics: choice === 'accepted',
                    marketing: choice === 'accepted'
                };
                localStorage.setItem(CONSENT_KEY, JSON.stringify(consent));
                hideCookieBanner();

                // If user accepted, load analytics
                if (choice === 'accepted') {
                    loadAnalytics();
                }

                // If user changed from accepted to something else, we should ideally clear cookies
                // But since we can't easily remove third-party cookies, we just stop loading them
            };

            // Show the cookie banner
            function showCookieBanner() {
                const banner = document.getElementById('cookieConsent');
                if (banner) {
                    banner.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }

            // Hide the cookie banner
            function hideCookieBanner() {
                const banner = document.getElementById('cookieConsent');
                if (banner) {
                    banner.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }

            // Toggle cookie details
            window.toggleCookieDetails = function() {
                const details = document.getElementById('cookieDetails');
                const text = document.getElementById('cookieDetailsText');
                const icon = document.getElementById('cookieDetailsIcon');
                if (details) {
                    details.classList.toggle('hidden');
                    if (details.classList.contains('hidden')) {
                        text.textContent = 'Show details';
                        icon.style.transform = '';
                    } else {
                        text.textContent = 'Hide details';
                        icon.style.transform = 'rotate(180deg)';
                    }
                }
            };

            // Open cookie settings (for footer link)
            window.openCookieSettings = function() {
                showCookieBanner();
            };

            // Load analytics scripts dynamically
            function loadAnalytics() {
                // Only load if not already loaded
                if (window.analyticsLoaded) return;
                window.analyticsLoaded = true;

                // Google Analytics 4
                <?php if ($ga4Id = getSetting('seo_ga4_id')): ?>
                var gaScript = document.createElement('script');
                gaScript.src = 'https://www.googletagmanager.com/gtag/js?id=<?= e($ga4Id) ?>';
                gaScript.async = true;
                document.head.appendChild(gaScript);

                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '<?= e($ga4Id) ?>');
                <?php endif; ?>

                // Google Tag Manager
                <?php if ($gtmId = getSetting('seo_gtm_id')): ?>
                (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','<?= e($gtmId) ?>');
                <?php endif; ?>

                // Microsoft Clarity
                <?php if ($clarityId = getSetting('seo_clarity_id')): ?>
                (function(c,l,a,r,i,t,y){
                    c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
                })(window, document, "clarity", "script", "<?= e($clarityId) ?>");
                <?php endif; ?>

                // Facebook Pixel
                <?php if ($fbPixelId = getSetting('seo_facebook_pixel_id')): ?>
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', '<?= e($fbPixelId) ?>');
                fbq('track', 'PageView');
                <?php endif; ?>

                // LinkedIn Insight Tag
                <?php if ($linkedInId = getSetting('seo_linkedin_partner_id')): ?>
                _linkedin_partner_id = "<?= e($linkedInId) ?>";
                window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
                window._linkedin_data_partner_ids.push(_linkedin_partner_id);
                (function(l) {
                if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
                window.lintrk.q=[]}
                var s = document.getElementsByTagName("script")[0];
                var b = document.createElement("script");
                b.type = "text/javascript";b.async = true;
                b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
                s.parentNode.insertBefore(b, s);})(window.lintrk);
                <?php endif; ?>
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                const consent = getConsent();

                if (!consent) {
                    // No consent yet, show banner
                    showCookieBanner();
                } else if (consent.analytics) {
                    // User previously accepted, load analytics
                    loadAnalytics();
                }
            });
        })();
    </script>
    <?php
}

/**
 * Check if analytics cookies are consented
 * @return bool
 */
function hasAnalyticsConsent() {
    // This is checked client-side, but we can use this for server-side checks if needed
    return true; // Default to true, actual check is done in JavaScript
}
