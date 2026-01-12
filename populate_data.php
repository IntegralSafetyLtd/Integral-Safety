<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/config.php";
require_once INCLUDES_PATH . "/database.php";

// Clear existing data
db()->exec("TRUNCATE TABLE services");
db()->exec("TRUNCATE TABLE training");

// Insert Services
$services = [
    [
        "title" => "Fire Risk Assessments",
        "slug" => "fire-risk-assessments",
        "short_description" => "PAS 79 compliant fire risk assessments for commercial buildings, HMOs, and housing stock. We identify fire hazards, evaluate existing controls, and provide prioritised action plans with clear recommendations.",
        "content" => "<p>PAS 79 compliant fire risk assessments for commercial buildings, HMOs, and housing stock. We identify fire hazards, evaluate existing controls, and provide prioritised action plans with clear recommendations.</p><p>Our fire risk assessments are thorough, practical, and comply with the Regulatory Reform (Fire Safety) Order 2005.</p>",
        "icon" => "flame",
        "sort_order" => 1
    ],
    [
        "title" => "Health & Safety Consultancy",
        "slug" => "consultancy",
        "short_description" => "Expert H&S support without the overhead of a full-time safety manager. Policy development, risk assessments, safe systems of work, and ongoing guidance tailored to your industry and operations.",
        "content" => "<p>Expert H&S support without the overhead of a full-time safety manager. Policy development, risk assessments, safe systems of work, and ongoing guidance tailored to your industry and operations.</p>",
        "icon" => "clipboard",
        "sort_order" => 2
    ],
    [
        "title" => "Face-Fit Testing",
        "slug" => "face-fit-testing",
        "short_description" => "Qualitative RPE testing to HSE protocols, ensuring masks seal correctly against your workers faces. On-site testing, instant certification, and guidance on selecting the right respiratory protection.",
        "content" => "<p>Qualitative RPE testing to HSE protocols, ensuring masks seal correctly against your workers faces. On-site testing, instant certification, and guidance on selecting the right respiratory protection.</p>",
        "icon" => "shield",
        "sort_order" => 3
    ],
    [
        "title" => "Drone Surveys",
        "slug" => "work-at-height-surveys",
        "short_description" => "Aerial inspections for roofs, chimneys, and structures. Get 4K video and 8MP imagery without scaffolding costs or sending workers to dangerous heights.",
        "content" => "<p>Aerial inspections for roofs, chimneys, and structures. Get 4K video and 8MP imagery without scaffolding costs or sending workers to dangerous heights.</p>",
        "icon" => "video",
        "sort_order" => 4
    ],
    [
        "title" => "Auditing & Inspections",
        "slug" => "auditing",
        "short_description" => "Independent workplace safety audits aligned with HSG65 and UK legislation. We identify compliance gaps, benchmark your performance, and provide practical recommendations for improvement.",
        "content" => "<p>Independent workplace safety audits aligned with HSG65 and UK legislation. We identify compliance gaps, benchmark your performance, and provide practical recommendations for improvement.</p>",
        "icon" => "search",
        "sort_order" => 5
    ],
    [
        "title" => "Accident Investigation",
        "slug" => "accident-investigation",
        "short_description" => "Thorough investigation of workplace incidents and near-misses. We apply root cause analysis techniques to understand why accidents happen and help you implement effective prevention measures.",
        "content" => "<p>Thorough investigation of workplace incidents and near-misses. We apply root cause analysis techniques to understand why accidents happen and help you implement effective prevention measures.</p>",
        "icon" => "hardhat",
        "sort_order" => 6
    ],
    [
        "title" => "Accreditation Support",
        "slug" => "accreditation-support",
        "short_description" => "Expert guidance through ISO 45001, SafeContractor, CHAS, Constructionline, and other accreditation schemes. We help you achieve and maintain the certifications your business needs.",
        "content" => "<p>Expert guidance through ISO 45001, SafeContractor, CHAS, Constructionline, and other accreditation schemes. We help you achieve and maintain the certifications your business needs.</p>",
        "icon" => "award",
        "sort_order" => 7
    ],
    [
        "title" => "Competent Person Services",
        "slug" => "competent-person",
        "short_description" => "Act as your appointed competent person for health and safety compliance. Regular site visits, ongoing telephone and email support, and a reliable resource when you need expert advice.",
        "content" => "<p>Act as your appointed competent person for health and safety compliance. Regular site visits, ongoing telephone and email support, and a reliable resource when you need expert advice.</p>",
        "icon" => "users",
        "sort_order" => 8
    ],
    [
        "title" => "HAVS Testing",
        "slug" => "havs-testing",
        "short_description" => "Hand-Arm Vibration Syndrome health surveillance to meet the Control of Vibration at Work Regulations. Screening questionnaires, Tier assessments, and practical guidance on reducing exposure.",
        "content" => "<p>Hand-Arm Vibration Syndrome health surveillance to meet the Control of Vibration at Work Regulations. Screening questionnaires, Tier assessments, and practical guidance on reducing exposure.</p>",
        "icon" => "vibrate",
        "sort_order" => 9
    ]
];

$stmt = db()->prepare("INSERT INTO services (title, slug, short_description, content, icon, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
foreach ($services as $s) {
    $stmt->execute([$s["title"], $s["slug"], $s["short_description"], $s["content"], $s["icon"], $s["sort_order"]]);
}
echo "Inserted " . count($services) . " services.<br>";

// Insert Training Courses
$training = [
    [
        "title" => "IOSH Managing Safely",
        "slug" => "iosh-managing-safely",
        "short_description" => "The world's most popular health and safety course for managers. Covers risk assessment, hazard identification, incident investigation, and legal responsibilities. Internationally recognised certification.",
        "content" => "<p>The world's most popular health and safety course for managers. Covers risk assessment, hazard identification, incident investigation, and legal responsibilities. Internationally recognised certification.</p>",
        "duration" => "3-4 days",
        "certification" => "IOSH",
        "delivery_method" => "In-person or Online",
        "sort_order" => 1
    ],
    [
        "title" => "Manual Handling Awareness",
        "slug" => "manual-handling",
        "short_description" => "Practical training on safe lifting and handling techniques. Includes hands-on exercises using real workplace scenarios and the TILE risk assessment framework.",
        "content" => "<p>Practical training on safe lifting and handling techniques. Includes hands-on exercises using real workplace scenarios and the TILE risk assessment framework.</p>",
        "duration" => "Half day",
        "certification" => "",
        "delivery_method" => "In-person",
        "sort_order" => 2
    ],
    [
        "title" => "COSHH Awareness",
        "slug" => "coshh-awareness",
        "short_description" => "Understanding the Control of Substances Hazardous to Health regulations. Learn to read safety data sheets, identify hazards, and use control measures correctly.",
        "content" => "<p>Understanding the Control of Substances Hazardous to Health regulations. Learn to read safety data sheets, identify hazards, and use control measures correctly.</p>",
        "duration" => "Half day",
        "certification" => "",
        "delivery_method" => "In-person or Online",
        "sort_order" => 3
    ],
    [
        "title" => "Fire Awareness",
        "slug" => "fire-awareness",
        "short_description" => "Essential fire safety training for all employees. Covers fire prevention, emergency procedures, and includes practical fire extinguisher training.",
        "content" => "<p>Essential fire safety training for all employees. Covers fire prevention, emergency procedures, and includes practical fire extinguisher training.</p>",
        "duration" => "2-3 hours",
        "certification" => "",
        "delivery_method" => "In-person",
        "sort_order" => 4
    ],
    [
        "title" => "Sharps & Needlestick Awareness",
        "slug" => "sharps-awareness",
        "short_description" => "For anyone who may encounter discarded needles and sharps. Covers safe handling, correct disposal, and post-exposure procedures to minimise infection risk.",
        "content" => "<p>For anyone who may encounter discarded needles and sharps. Covers safe handling, correct disposal, and post-exposure procedures to minimise infection risk.</p>",
        "duration" => "Half day",
        "certification" => "",
        "delivery_method" => "In-person",
        "sort_order" => 5
    ],
    [
        "title" => "Accident Investigation",
        "slug" => "accident-investigation",
        "short_description" => "Learn how to conduct thorough accident investigations. Evidence gathering, witness interviews, root cause analysis, and writing actionable reports.",
        "content" => "<p>Learn how to conduct thorough accident investigations. Evidence gathering, witness interviews, root cause analysis, and writing actionable reports.</p>",
        "duration" => "1 day",
        "certification" => "",
        "delivery_method" => "In-person",
        "sort_order" => 6
    ]
];

$stmt = db()->prepare("INSERT INTO training (title, slug, short_description, content, duration, certification, delivery_method, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
foreach ($training as $t) {
    $stmt->execute([$t["title"], $t["slug"], $t["short_description"], $t["content"], $t["duration"], $t["certification"], $t["delivery_method"], $t["sort_order"]]);
}
echo "Inserted " . count($training) . " training courses.<br>";

echo "Data population complete!";
