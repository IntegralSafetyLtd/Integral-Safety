<?php
/**
 * Privacy Policy Page
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Privacy Policy | ' . getSetting('site_name', SITE_NAME);
$metaDescription = 'Privacy policy for Integral Safety Ltd - how we collect, use, and protect your personal data.';

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Hero -->
<section class="py-12 bg-navy-800 text-white">
    <div class="max-w-6xl mx-auto px-6">
        <h1 class="font-heading text-3xl md:text-4xl font-semibold">Privacy Policy</h1>
        <p class="text-gray-300 mt-2">Last updated: January 2026</p>
    </div>
</section>

<!-- Content -->
<section class="py-16 bg-cream">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="prose max-w-none">

                <h2>1. Introduction</h2>
                <p>Integral Safety Ltd ("we", "our", "us") is committed to protecting your privacy and personal data. This Privacy Policy explains how we collect, use, store, and protect your information when you use our website or engage our services.</p>
                <p>We are registered in England and Wales. For data protection purposes, we are the data controller.</p>
                <p><strong>Contact Details:</strong><br>
                Integral Safety Ltd<br>
                Email: info@integralsafetyltd.co.uk<br>
                Phone: 01530 382 150</p>

                <h2>2. Information We Collect</h2>
                <h3>Information You Provide</h3>
                <p>We collect information you provide directly to us, including:</p>
                <ul>
                    <li><strong>Contact Information:</strong> Name, email address, phone number, company name, and postal address</li>
                    <li><strong>Enquiry Details:</strong> Information you provide when completing our contact form or requesting a quote</li>
                    <li><strong>Training Records:</strong> Delegate names, job titles, and assessment results for training courses</li>
                    <li><strong>Service Records:</strong> Information related to site assessments, audits, and consultancy work</li>
                </ul>

                <h3>Information Collected Automatically</h3>
                <p>When you visit our website, we automatically collect certain information:</p>
                <ul>
                    <li>IP address and location data</li>
                    <li>Browser type and version</li>
                    <li>Device information</li>
                    <li>Pages visited and time spent on site</li>
                    <li>Referring website</li>
                </ul>

                <h2>3. How We Use Your Information</h2>
                <p>We use your personal data for the following purposes:</p>
                <ul>
                    <li><strong>Service Delivery:</strong> To provide health and safety consultancy services, training, and assessments</li>
                    <li><strong>Communication:</strong> To respond to your enquiries and keep you informed about your booking or service</li>
                    <li><strong>Legal Compliance:</strong> To comply with legal obligations and regulatory requirements</li>
                    <li><strong>Administration:</strong> To manage our business operations, including invoicing and record-keeping</li>
                    <li><strong>Improvement:</strong> To improve our website and services based on usage patterns</li>
                    <li><strong>Marketing:</strong> To send you information about our services (only with your consent)</li>
                </ul>

                <h2>4. Legal Basis for Processing</h2>
                <p>We process your personal data under the following legal bases:</p>
                <ul>
                    <li><strong>Contract:</strong> Processing necessary to fulfil our contract with you</li>
                    <li><strong>Legal Obligation:</strong> Processing required to comply with UK law</li>
                    <li><strong>Legitimate Interest:</strong> Processing necessary for our legitimate business interests, such as improving our services</li>
                    <li><strong>Consent:</strong> Processing based on your explicit consent (e.g., marketing communications)</li>
                </ul>

                <h2>5. Data Sharing</h2>
                <p>We do not sell your personal data. We may share your information with:</p>
                <ul>
                    <li><strong>Service Providers:</strong> Third-party providers who assist with our operations (e.g., email services, IT support)</li>
                    <li><strong>Certification Bodies:</strong> IOSH or other awarding bodies for training certification purposes</li>
                    <li><strong>Legal Authorities:</strong> When required by law or to protect our legal rights</li>
                    <li><strong>Professional Advisors:</strong> Accountants, lawyers, and insurers as necessary</li>
                </ul>
                <p>All third parties are required to respect the security of your personal data and treat it in accordance with the law.</p>

                <h2>6. Data Retention</h2>
                <p>We retain your personal data only for as long as necessary to fulfil the purposes we collected it for:</p>
                <ul>
                    <li><strong>Client Records:</strong> 7 years after the end of our business relationship (for legal and accounting purposes)</li>
                    <li><strong>Training Records:</strong> Indefinitely (certification records may be required for verification)</li>
                    <li><strong>Marketing Contacts:</strong> Until you unsubscribe or withdraw consent</li>
                    <li><strong>Website Analytics:</strong> 26 months</li>
                </ul>

                <h2>7. Data Security</h2>
                <p>We implement appropriate technical and organisational measures to protect your personal data, including:</p>
                <ul>
                    <li>Secure encrypted connections (HTTPS) for our website</li>
                    <li>Password protection and access controls</li>
                    <li>Regular security assessments</li>
                    <li>Staff training on data protection</li>
                    <li>Secure storage of physical documents</li>
                </ul>

                <h2>8. Your Rights</h2>
                <p>Under UK GDPR, you have the following rights:</p>
                <ul>
                    <li><strong>Access:</strong> Request a copy of your personal data</li>
                    <li><strong>Rectification:</strong> Request correction of inaccurate data</li>
                    <li><strong>Erasure:</strong> Request deletion of your data (subject to legal retention requirements)</li>
                    <li><strong>Restriction:</strong> Request limitation of processing</li>
                    <li><strong>Portability:</strong> Request transfer of your data to another organisation</li>
                    <li><strong>Objection:</strong> Object to processing based on legitimate interests</li>
                    <li><strong>Withdraw Consent:</strong> Withdraw consent at any time (where processing is based on consent)</li>
                </ul>
                <p>To exercise any of these rights, please contact us at info@integralsafetyltd.co.uk. We will respond within one month.</p>

                <h2>9. Cookies</h2>
                <p>Our website uses cookies to improve your browsing experience. Cookies are small text files stored on your device.</p>
                <h3>Types of Cookies We Use</h3>
                <ul>
                    <li><strong>Essential Cookies:</strong> Required for the website to function properly (e.g., session management)</li>
                    <li><strong>Analytics Cookies:</strong> Help us understand how visitors use our website</li>
                </ul>
                <p>You can control cookies through your browser settings. Disabling certain cookies may affect website functionality.</p>

                <h2>10. Third-Party Links</h2>
                <p>Our website may contain links to external websites. We are not responsible for the privacy practices of these sites. We encourage you to read the privacy policies of any external websites you visit.</p>

                <h2>11. Children's Privacy</h2>
                <p>Our services are not directed at individuals under 18 years of age. We do not knowingly collect personal data from children.</p>

                <h2>12. International Transfers</h2>
                <p>Your data is primarily stored and processed within the UK. If we transfer data outside the UK, we ensure appropriate safeguards are in place in compliance with UK data protection law.</p>

                <h2>13. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated revision date. We encourage you to review this policy periodically.</p>

                <h2>14. Complaints</h2>
                <p>If you have concerns about how we handle your personal data, please contact us first at info@integralsafetyltd.co.uk.</p>
                <p>You also have the right to lodge a complaint with the Information Commissioner's Office (ICO):</p>
                <p>Website: <a href="https://ico.org.uk" target="_blank" rel="noopener">ico.org.uk</a><br>
                Phone: 0303 123 1113</p>

                <h2>15. Contact Us</h2>
                <p>For any questions about this Privacy Policy or our data practices, please contact us:</p>
                <p><strong>Integral Safety Ltd</strong><br>
                Email: info@integralsafetyltd.co.uk<br>
                Phone: 01530 382 150</p>

            </div>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
