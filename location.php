<?php
/**
 * Location-specific Service Landing Page
 * Handles URLs like /health-and-safety-leicester, /fire-risk-assessments-nottingham
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$slug = $_GET['slug'] ?? '';

// Define location pages with their content
$locationPages = [
    // Health and Safety Consultants
    'health-and-safety-consultants-leicester' => [
        'title' => 'Health and Safety Consultants Leicester',
        'location' => 'Leicester',
        'service' => 'Health and Safety Consultants',
        'meta_description' => 'Professional health and safety consultants in Leicester. Expert H&S advice, risk assessments, and compliance support for Leicester businesses. Call 01530 382 150.',
        'hero_heading' => 'Health and Safety Consultants in Leicester',
        'hero_text' => 'Integral Safety provides expert health and safety consultancy services to businesses across Leicester and Leicestershire. With over 20 years of experience, we help organisations of all sizes meet their legal obligations and create safer workplaces.',
        'areas' => ['Leicester City Centre', 'Beaumont Leys', 'Belgrave', 'Braunstone', 'Evington', 'Highfields', 'Knighton', 'Oadby', 'Wigston', 'Glenfield', 'Birstall', 'Thurmaston'],
    ],
    'health-and-safety-consultants-nottingham' => [
        'title' => 'Health and Safety Consultants Nottingham',
        'location' => 'Nottingham',
        'service' => 'Health and Safety Consultants',
        'meta_description' => 'Expert health and safety consultants serving Nottingham businesses. Comprehensive H&S support, audits, and compliance. Local consultants, competitive rates.',
        'hero_heading' => 'Health and Safety Consultants in Nottingham',
        'hero_text' => 'Looking for reliable health and safety support in Nottingham? Integral Safety offers comprehensive consultancy services to businesses across Nottinghamshire. From risk assessments to policy development, we provide the expertise you need.',
        'areas' => ['Nottingham City Centre', 'Arnold', 'Carlton', 'West Bridgford', 'Beeston', 'Stapleford', 'Long Eaton', 'Hucknall', 'Bulwell', 'Mansfield', 'Newark'],
    ],
    'health-and-safety-consultants-derby' => [
        'title' => 'Health and Safety Consultants Derby',
        'location' => 'Derby',
        'service' => 'Health and Safety Consultants',
        'meta_description' => 'Trusted health and safety consultants in Derby. Professional H&S advice, workplace assessments, and compliance support for Derbyshire businesses.',
        'hero_heading' => 'Health and Safety Consultants in Derby',
        'hero_text' => 'Integral Safety delivers professional health and safety consultancy to businesses throughout Derby and Derbyshire. Our experienced consultants help you navigate complex regulations and implement effective safety management systems.',
        'areas' => ['Derby City Centre', 'Alvaston', 'Chaddesden', 'Chellaston', 'Littleover', 'Mickleover', 'Spondon', 'Allestree', 'Belper', 'Ripley', 'Ilkeston', 'Swadlincote'],
    ],
    'health-and-safety-consultants-loughborough' => [
        'title' => 'Health and Safety Consultants Loughborough',
        'location' => 'Loughborough',
        'service' => 'Health and Safety Consultants',
        'meta_description' => 'Local health and safety consultants in Loughborough. Expert H&S support for businesses in Loughborough and North Leicestershire. Free initial consultation.',
        'hero_heading' => 'Health and Safety Consultants in Loughborough',
        'hero_text' => 'Based nearby in Coalville, Integral Safety provides responsive health and safety consultancy services to Loughborough businesses. We understand the local business community and offer practical, cost-effective solutions.',
        'areas' => ['Loughborough Town Centre', 'Shepshed', 'Quorn', 'Mountsorrel', 'Sileby', 'Barrow upon Soar', 'Rothley', 'Syston', 'Birstall', 'Anstey'],
    ],
    'health-and-safety-east-midlands' => [
        'title' => 'Health and Safety Consultants East Midlands',
        'location' => 'East Midlands',
        'service' => 'Health and Safety Consultants',
        'meta_description' => 'Leading health and safety consultants covering the East Midlands. Expert H&S services across Leicestershire, Nottinghamshire, Derbyshire and beyond.',
        'hero_heading' => 'Health and Safety Consultants Across the East Midlands',
        'hero_text' => 'Integral Safety is a leading health and safety consultancy serving businesses throughout the East Midlands region. From our base in Coalville, we provide comprehensive H&S support to organisations across Leicestershire, Nottinghamshire, Derbyshire, and neighbouring counties.',
        'areas' => ['Leicestershire', 'Nottinghamshire', 'Derbyshire', 'Northamptonshire', 'Rutland', 'South Yorkshire', 'Lincolnshire', 'Warwickshire'],
    ],

    // Fire Risk Assessments
    'fire-risk-assessments-leicester' => [
        'title' => 'Fire Risk Assessments Leicester',
        'location' => 'Leicester',
        'service' => 'Fire Risk Assessments',
        'meta_description' => 'PAS 79 fire risk assessments in Leicester. Qualified fire safety assessors for all premises types. Competitive rates, fast turnaround. Call 01530 382 150.',
        'hero_heading' => 'Fire Risk Assessments in Leicester',
        'hero_text' => 'Integral Safety provides comprehensive PAS 79 compliant fire risk assessments throughout Leicester and Leicestershire. Our qualified fire safety consultants assess all types of premises, from offices and retail units to care homes and HMOs.',
        'areas' => ['Leicester City Centre', 'Beaumont Leys', 'Belgrave', 'Braunstone', 'Evington', 'Highfields', 'Knighton', 'Oadby', 'Wigston', 'Glenfield', 'Birstall', 'Thurmaston'],
    ],
    'fire-risk-assessments-nottingham' => [
        'title' => 'Fire Risk Assessments Nottingham',
        'location' => 'Nottingham',
        'service' => 'Fire Risk Assessments',
        'meta_description' => 'Professional fire risk assessments in Nottingham. PAS 79 compliant assessments for commercial, residential and industrial premises. Free quotes available.',
        'hero_heading' => 'Fire Risk Assessments in Nottingham',
        'hero_text' => 'Need a fire risk assessment in Nottingham? Integral Safety offers professional, PAS 79 compliant fire risk assessments for all premises types across Nottinghamshire. Our experienced assessors identify fire hazards and provide clear, actionable recommendations.',
        'areas' => ['Nottingham City Centre', 'Arnold', 'Carlton', 'West Bridgford', 'Beeston', 'Stapleford', 'Long Eaton', 'Hucknall', 'Bulwell', 'Mansfield', 'Newark'],
    ],
    'fire-risk-assessments-derby' => [
        'title' => 'Fire Risk Assessments Derby',
        'location' => 'Derby',
        'service' => 'Fire Risk Assessments',
        'meta_description' => 'Expert fire risk assessments in Derby. PAS 79 compliant assessments from experienced fire safety professionals. Serving all Derbyshire businesses.',
        'hero_heading' => 'Fire Risk Assessments in Derby',
        'hero_text' => 'Integral Safety delivers thorough fire risk assessments to businesses and landlords across Derby and Derbyshire. We help you comply with the Regulatory Reform (Fire Safety) Order 2005 and keep your premises safe.',
        'areas' => ['Derby City Centre', 'Alvaston', 'Chaddesden', 'Chellaston', 'Littleover', 'Mickleover', 'Spondon', 'Allestree', 'Belper', 'Ripley', 'Ilkeston', 'Swadlincote'],
    ],
    'fire-risk-assessments-loughborough' => [
        'title' => 'Fire Risk Assessments Loughborough',
        'location' => 'Loughborough',
        'service' => 'Fire Risk Assessments',
        'meta_description' => 'Local fire risk assessments in Loughborough. PAS 79 compliant assessments from nearby Coalville-based consultants. Quick response, competitive pricing.',
        'hero_heading' => 'Fire Risk Assessments in Loughborough',
        'hero_text' => 'As local fire safety specialists based in nearby Coalville, Integral Safety provides prompt, professional fire risk assessments to Loughborough businesses and landlords. We understand local requirements and deliver practical recommendations.',
        'areas' => ['Loughborough Town Centre', 'Shepshed', 'Quorn', 'Mountsorrel', 'Sileby', 'Barrow upon Soar', 'Rothley', 'Syston', 'Birstall', 'Anstey'],
    ],
    'fire-risk-assessments-east-midlands' => [
        'title' => 'Fire Risk Assessments East Midlands',
        'location' => 'East Midlands',
        'service' => 'Fire Risk Assessments',
        'meta_description' => 'Fire risk assessments across the East Midlands. PAS 79 compliant assessments for all premises types. Experienced assessors, competitive rates region-wide.',
        'hero_heading' => 'Fire Risk Assessments Across the East Midlands',
        'hero_text' => 'Integral Safety provides fire risk assessments throughout the East Midlands region. Whether you have a single premises or a portfolio of properties, our experienced fire safety assessors deliver thorough, compliant assessments.',
        'areas' => ['Leicestershire', 'Nottinghamshire', 'Derbyshire', 'Northamptonshire', 'Rutland', 'South Yorkshire', 'Lincolnshire', 'Warwickshire'],
    ],

    // Health and Safety Training
    'health-and-safety-training-leicester' => [
        'title' => 'Health and Safety Training Leicester',
        'location' => 'Leicester',
        'service' => 'Health and Safety Training',
        'meta_description' => 'IOSH approved health and safety training in Leicester. Managing Safely, Working Safely, and specialist courses. On-site and classroom options.',
        'hero_heading' => 'Health and Safety Training in Leicester',
        'hero_text' => 'Integral Safety delivers IOSH approved and bespoke health and safety training to businesses across Leicester. From IOSH Managing Safely to specialist courses, we provide practical training that gives your team the skills they need.',
        'areas' => ['Leicester City Centre', 'Beaumont Leys', 'Belgrave', 'Braunstone', 'Evington', 'Highfields', 'Knighton', 'Oadby', 'Wigston', 'Glenfield', 'Birstall', 'Thurmaston'],
    ],
    'health-and-safety-training-nottingham' => [
        'title' => 'Health and Safety Training Nottingham',
        'location' => 'Nottingham',
        'service' => 'Health and Safety Training',
        'meta_description' => 'Professional health and safety training in Nottingham. IOSH courses, manual handling, fire safety and more. Delivered at your premises or our venue.',
        'hero_heading' => 'Health and Safety Training in Nottingham',
        'hero_text' => 'Looking for quality health and safety training in Nottingham? Integral Safety provides a comprehensive range of IOSH approved and bespoke training courses to businesses across Nottinghamshire. We can train at your premises or arrange venue-based sessions.',
        'areas' => ['Nottingham City Centre', 'Arnold', 'Carlton', 'West Bridgford', 'Beeston', 'Stapleford', 'Long Eaton', 'Hucknall', 'Bulwell', 'Mansfield', 'Newark'],
    ],
    'health-and-safety-training-derby' => [
        'title' => 'Health and Safety Training Derby',
        'location' => 'Derby',
        'service' => 'Health and Safety Training',
        'meta_description' => 'Expert health and safety training in Derby. IOSH Managing Safely, manual handling, COSHH and more. Flexible delivery options for Derbyshire businesses.',
        'hero_heading' => 'Health and Safety Training in Derby',
        'hero_text' => 'Integral Safety offers a wide range of health and safety training courses to businesses in Derby and across Derbyshire. Our experienced trainers deliver engaging, practical sessions that meet legal requirements and genuinely improve workplace safety.',
        'areas' => ['Derby City Centre', 'Alvaston', 'Chaddesden', 'Chellaston', 'Littleover', 'Mickleover', 'Spondon', 'Allestree', 'Belper', 'Ripley', 'Ilkeston', 'Swadlincote'],
    ],
    'health-and-safety-training-loughborough' => [
        'title' => 'Health and Safety Training Loughborough',
        'location' => 'Loughborough',
        'service' => 'Health and Safety Training',
        'meta_description' => 'Local health and safety training in Loughborough. IOSH courses and bespoke training from nearby Coalville consultants. Competitive rates.',
        'hero_heading' => 'Health and Safety Training in Loughborough',
        'hero_text' => 'Based nearby in Coalville, Integral Safety provides convenient health and safety training solutions for Loughborough businesses. We deliver IOSH approved courses and bespoke training tailored to your industry and workforce.',
        'areas' => ['Loughborough Town Centre', 'Shepshed', 'Quorn', 'Mountsorrel', 'Sileby', 'Barrow upon Soar', 'Rothley', 'Syston', 'Birstall', 'Anstey'],
    ],
    'health-and-safety-training-east-midlands' => [
        'title' => 'Health and Safety Training East Midlands',
        'location' => 'East Midlands',
        'service' => 'Health and Safety Training',
        'meta_description' => 'Health and safety training across the East Midlands. IOSH approved courses delivered region-wide. On-site training available throughout the Midlands.',
        'hero_heading' => 'Health and Safety Training Across the East Midlands',
        'hero_text' => 'Integral Safety delivers professional health and safety training throughout the East Midlands. Whether you need IOSH Managing Safely for your managers or specialist training for your workforce, we provide flexible, high-quality training solutions.',
        'areas' => ['Leicestershire', 'Nottinghamshire', 'Derbyshire', 'Northamptonshire', 'Rutland', 'South Yorkshire', 'Lincolnshire', 'Warwickshire'],
    ],
];

// Check if slug exists
if (!isset($locationPages[$slug])) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

$page = $locationPages[$slug];
$pageTitle = $page['title'] . ' | ' . getSetting('site_name', SITE_NAME);
$metaDescription = $page['meta_description'];

// Service-specific content
$serviceContent = [
    'Health and Safety Consultants' => [
        'intro' => 'As your appointed health and safety partner, we provide comprehensive consultancy services tailored to your business needs. Our experienced consultants work with you to develop practical solutions that protect your people and keep you legally compliant.',
        'services' => [
            'Risk assessments and method statements',
            'Health and safety policy development',
            'Safety management system implementation',
            'Workplace inspections and audits',
            'Accident investigation and RIDDOR reporting',
            'CDM compliance and construction safety',
            'Competent person services',
            'CHAS, SafeContractor and accreditation support',
        ],
        'benefits' => [
            'Reduce workplace accidents and ill-health',
            'Meet legal obligations under the Health and Safety at Work Act',
            'Avoid costly enforcement action and prosecution',
            'Win contracts requiring H&S accreditation',
            'Lower insurance premiums',
            'Improve staff morale and retention',
        ],
        'why_local' => 'quick response times and a personal service. We understand the local business community and can be on-site promptly when you need us.',
        'link' => '/services/consultancy',
    ],
    'Fire Risk Assessments' => [
        'intro' => 'Under the Regulatory Reform (Fire Safety) Order 2005, the responsible person for any non-domestic premises must carry out a fire risk assessment. Our PAS 79 compliant assessments identify fire hazards, evaluate risks, and provide clear recommendations to keep your premises safe.',
        'services' => [
            'PAS 79 compliant fire risk assessments',
            'Fire door surveys and inspections',
            'Emergency evacuation plans',
            'Fire safety policy development',
            'Staff fire safety training',
            'Compartmentation surveys',
            'Fire risk assessment reviews and updates',
            'Specialist assessments for care homes and HMOs',
        ],
        'benefits' => [
            'Legal compliance with fire safety regulations',
            'Protect lives and property from fire',
            'Satisfy insurance requirements',
            'Meet landlord and leaseholder obligations',
            'Clear action plan with prioritised recommendations',
            'Demonstrate due diligence',
        ],
        'why_local' => 'prompt site visits and local knowledge. We understand the building types and fire safety challenges specific to your area.',
        'link' => '/services/fire-risk-assessments',
    ],
    'Health and Safety Training' => [
        'intro' => 'Effective health and safety training is essential for protecting your workforce and meeting legal requirements. We deliver IOSH approved courses alongside bespoke training programmes tailored to your industry and workplace hazards.',
        'services' => [
            'IOSH Managing Safely',
            'IOSH Working Safely',
            'Manual handling training',
            'Fire awareness and fire marshal training',
            'COSHH awareness',
            'Working at height training',
            'Accident investigation training',
            'Bespoke training programmes',
        ],
        'benefits' => [
            'Nationally recognised IOSH certification',
            'Practical skills your team can apply immediately',
            'Reduced workplace accidents and injuries',
            'Improved safety culture',
            'Meet training requirements for contracts and accreditations',
            'Flexible delivery options',
        ],
        'why_local' => 'convenient on-site training at your premises. No need to send staff away or pay for travel and accommodation.',
        'link' => '/training',
    ],
];

$content = $serviceContent[$page['service']];

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="py-16 md:py-20 bg-gradient-to-br from-navy-900 to-navy-800 text-white">
    <div class="max-w-6xl mx-auto px-6">
        <div class="max-w-3xl">
            <p class="text-orange-400 font-semibold text-sm uppercase tracking-wider mb-4"><?= e($page['service']) ?></p>
            <h1 class="font-heading text-4xl md:text-5xl font-bold mb-6"><?= e($page['hero_heading']) ?></h1>
            <p class="text-xl text-gray-300 leading-relaxed mb-8"><?= e($page['hero_text']) ?></p>
            <div class="flex flex-wrap gap-4">
                <a href="/contact" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors shadow-lg">
                    Get a Free Quote
                </a>
                <a href="tel:<?= preg_replace('/\s+/', '', getSetting('contact_phone')) ?>" class="bg-white/10 text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/20 transition-colors inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <?= e(getSetting('contact_phone')) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-16 bg-cream">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Introduction -->
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <h2 class="font-heading text-2xl font-semibold text-navy-900 mb-4">Professional <?= e($page['service']) ?> in <?= e($page['location']) ?></h2>
                    <p class="text-gray-600 leading-relaxed mb-6"><?= e($content['intro']) ?></p>
                    <p class="text-gray-600 leading-relaxed">Based in Coalville, Leicestershire, Integral Safety is ideally positioned to serve businesses in <?= e($page['location']) ?>. Choosing a local consultancy means <?= e($content['why_local']) ?></p>
                </div>

                <!-- What We Offer -->
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h2 class="font-heading text-2xl font-semibold text-navy-900">Our <?= e($page['service']) ?> Services</h2>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <?php foreach ($content['services'] as $service): ?>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600"><?= e($service) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Benefits -->
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h2 class="font-heading text-2xl font-semibold text-navy-900">Benefits for Your Business</h2>
                    </div>
                    <ul class="space-y-3">
                        <?php foreach ($content['benefits'] as $benefit): ?>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-600"><?= e($benefit) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Areas Covered -->
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h2 class="font-heading text-2xl font-semibold text-navy-900">Areas We Cover in <?= e($page['location']) ?></h2>
                    </div>
                    <p class="text-gray-600 mb-4">We provide <?= strtolower(e($page['service'])) ?> throughout <?= e($page['location']) ?> and surrounding areas, including:</p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($page['areas'] as $area): ?>
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm"><?= e($area) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Why Choose Us -->
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <h2 class="font-heading text-2xl font-semibold text-navy-900 mb-6">Why Choose Integral Safety?</h2>
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-navy-900 mb-1">20+ Years Experience</h3>
                                <p class="text-gray-600 text-sm">Decades of experience across diverse industries and sectors.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-navy-900 mb-1">Local to <?= e($page['location']) ?></h3>
                                <p class="text-gray-600 text-sm">Based in Coalville, we offer prompt service across the Midlands.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-navy-900 mb-1">Qualified Professionals</h3>
                                <p class="text-gray-600 text-sm">IOSH, NEBOSH and industry-qualified consultants.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-navy-900 mb-1">Competitive Pricing</h3>
                                <p class="text-gray-600 text-sm">Fair, transparent pricing with no hidden costs.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Contact -->
                <div class="bg-navy-900 rounded-2xl p-6 text-white">
                    <h3 class="font-heading text-xl font-semibold mb-3">Get Your Free Quote</h3>
                    <p class="text-white/80 text-sm mb-4">Contact us today for a free, no-obligation quote for <?= strtolower(e($page['service'])) ?> in <?= e($page['location']) ?>.</p>
                    <a href="tel:<?= preg_replace('/\s+/', '', getSetting('contact_phone')) ?>" class="block w-full bg-orange-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors mb-3">
                        <?= e(getSetting('contact_phone')) ?>
                    </a>
                    <a href="/contact" class="block w-full bg-white/10 text-white text-center py-3 rounded-lg font-semibold hover:bg-white/20 transition-colors">
                        Request a Callback
                    </a>
                </div>

                <!-- Other Services -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-heading text-lg font-semibold text-navy-900 mb-4">Our Services</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="/services/fire-risk-assessments" class="flex items-center gap-2 text-navy-800 hover:text-orange-500 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                Fire Risk Assessments
                            </a>
                        </li>
                        <li>
                            <a href="/services/consultancy" class="flex items-center gap-2 text-navy-800 hover:text-orange-500 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                H&S Consultancy
                            </a>
                        </li>
                        <li>
                            <a href="/training" class="flex items-center gap-2 text-navy-800 hover:text-orange-500 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                Training Courses
                            </a>
                        </li>
                        <li>
                            <a href="/services/face-fit-testing" class="flex items-center gap-2 text-navy-800 hover:text-orange-500 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                Face-Fit Testing
                            </a>
                        </li>
                        <li>
                            <a href="/services" class="flex items-center gap-2 text-orange-500 hover:text-orange-600 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                View All Services
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Accreditations -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-heading text-lg font-semibold text-navy-900 mb-4">Accreditations</h3>
                    <p class="text-gray-600 text-sm">Our consultants hold qualifications including:</p>
                    <ul class="mt-3 space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            NEBOSH Diploma
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            IOSH Membership
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Fire Risk Assessment Competency
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-orange-500">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-heading text-3xl md:text-4xl font-bold text-white mb-4">Ready to Get Started?</h2>
        <p class="text-white/90 text-lg mb-8">Contact us today for a free quote for <?= strtolower(e($page['service'])) ?> in <?= e($page['location']) ?>. We're here to help protect your business.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/contact" class="bg-white text-orange-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors shadow-lg">
                Get Your Free Quote
            </a>
            <a href="tel:<?= preg_replace('/\s+/', '', getSetting('contact_phone')) ?>" class="bg-orange-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-700 transition-colors">
                Call <?= e(getSetting('contact_phone')) ?>
            </a>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
