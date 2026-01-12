<?php
/**
 * Terms & Conditions Page
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Terms & Conditions | ' . getSetting('site_name', SITE_NAME);
$metaDescription = 'Terms and conditions for using Integral Safety Ltd services and website.';

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Hero -->
<section class="py-12 bg-navy-800 text-white">
    <div class="max-w-6xl mx-auto px-6">
        <h1 class="font-heading text-3xl md:text-4xl font-semibold">Terms & Conditions</h1>
        <p class="text-gray-300 mt-2">Last updated: January 2026</p>
    </div>
</section>

<!-- Content -->
<section class="py-16 bg-cream">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="prose max-w-none">

                <h2>1. Introduction</h2>
                <p>These Terms and Conditions govern your use of the Integral Safety Ltd website and the provision of our health and safety consultancy services. By accessing our website or engaging our services, you agree to be bound by these terms.</p>
                <p><strong>Company Details:</strong><br>
                Integral Safety Ltd<br>
                Email: info@integralsafetyltd.co.uk<br>
                Phone: 01530 382 150</p>

                <h2>2. Services</h2>
                <p>Integral Safety Ltd provides health and safety consultancy services including but not limited to:</p>
                <ul>
                    <li>Fire Risk Assessments</li>
                    <li>Health & Safety Consultancy</li>
                    <li>IOSH Training Courses</li>
                    <li>Drone Surveys</li>
                    <li>Face-Fit Testing</li>
                    <li>Auditing & Inspections</li>
                    <li>Competent Person Services</li>
                    <li>HAVS Testing</li>
                </ul>
                <p>All services are provided in accordance with current UK health and safety legislation and best practice guidelines.</p>

                <h2>3. Quotations and Pricing</h2>
                <p>All quotations provided by Integral Safety Ltd are valid for 30 days from the date of issue unless otherwise stated. Prices are exclusive of VAT unless explicitly stated otherwise.</p>
                <p>We reserve the right to amend quotations if the scope of work changes or if site conditions differ from those described at the time of quotation.</p>

                <h2>4. Payment Terms</h2>
                <p>Payment terms are 30 days from the date of invoice unless otherwise agreed in writing. We reserve the right to charge interest on overdue payments at 8% above the Bank of England base rate.</p>
                <p>For training courses, payment is required prior to attendance unless credit terms have been agreed.</p>

                <h2>5. Cancellation Policy</h2>
                <h3>Consultancy Services</h3>
                <p>Cancellations must be made in writing at least 48 hours before any scheduled site visit. Cancellations made with less notice may be subject to a cancellation fee.</p>

                <h3>Training Courses</h3>
                <p>Training course cancellations are subject to the following terms:</p>
                <ul>
                    <li>More than 14 days notice: Full refund</li>
                    <li>7-14 days notice: 50% refund</li>
                    <li>Less than 7 days notice: No refund</li>
                </ul>
                <p>Delegate substitutions are accepted at any time at no additional cost.</p>

                <h2>6. Intellectual Property</h2>
                <p>All reports, assessments, and documentation produced by Integral Safety Ltd remain our intellectual property until full payment has been received. Upon payment, copyright of deliverables transfers to the client for their internal use only.</p>
                <p>Our reports may not be reproduced, distributed, or published without our prior written consent.</p>

                <h2>7. Limitation of Liability</h2>
                <p>Our services are provided as professional advice based on information available at the time of assessment. While we exercise reasonable skill and care, we cannot guarantee that our advice will prevent all incidents or ensure complete regulatory compliance.</p>
                <p>Our liability is limited to the fee paid for the specific service giving rise to any claim. We shall not be liable for any indirect, consequential, or special damages.</p>
                <p>Nothing in these terms excludes or limits our liability for death or personal injury caused by our negligence, fraud, or any other liability that cannot be excluded by law.</p>

                <h2>8. Client Responsibilities</h2>
                <p>Clients are responsible for:</p>
                <ul>
                    <li>Providing accurate and complete information about their premises and operations</li>
                    <li>Ensuring safe access to all areas required for assessment</li>
                    <li>Implementing recommendations in a timely manner</li>
                    <li>Maintaining records and documentation as advised</li>
                    <li>Informing us of any changes that may affect our assessment or advice</li>
                </ul>

                <h2>9. Confidentiality</h2>
                <p>We treat all client information as confidential and will not disclose it to third parties without your consent, except where required by law or regulatory authorities.</p>

                <h2>10. Data Protection</h2>
                <p>We process personal data in accordance with UK GDPR and the Data Protection Act 2018. Please refer to our Privacy Policy for full details of how we handle your data.</p>

                <h2>11. Force Majeure</h2>
                <p>We shall not be liable for any failure or delay in performing our obligations where such failure or delay results from circumstances beyond our reasonable control, including but not limited to natural disasters, pandemics, government actions, or industrial disputes.</p>

                <h2>12. Complaints</h2>
                <p>If you are dissatisfied with any aspect of our service, please contact us at info@integralsafetyltd.co.uk. We will acknowledge your complaint within 5 working days and aim to resolve it within 28 days.</p>

                <h2>13. Governing Law</h2>
                <p>These Terms and Conditions are governed by and construed in accordance with the laws of England and Wales. Any disputes shall be subject to the exclusive jurisdiction of the courts of England and Wales.</p>

                <h2>14. Changes to Terms</h2>
                <p>We reserve the right to update these Terms and Conditions at any time. Changes will be posted on this page with an updated revision date. Continued use of our services after changes constitutes acceptance of the revised terms.</p>

                <h2>15. Contact Us</h2>
                <p>If you have any questions about these Terms and Conditions, please contact us:</p>
                <p><strong>Integral Safety Ltd</strong><br>
                Email: info@integralsafetyltd.co.uk<br>
                Phone: 01530 382 150</p>

            </div>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
