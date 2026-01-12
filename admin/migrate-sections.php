<?php
/**
 * Migration: Create page_sections table
 * Run this once to add the sections system for pages
 * DELETE THIS FILE AFTER RUNNING
 */
require_once __DIR__ . '/../config.php';

// Direct database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$messages = [];
$errors = [];

// Create page_sections table
$sql = "CREATE TABLE IF NOT EXISTS `page_sections` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `page_type` enum('page','service','training') NOT NULL DEFAULT 'page',
    `page_id` int(11) NOT NULL,
    `section_type` varchar(50) NOT NULL,
    `section_data` longtext,
    `sort_order` int(11) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_page_type_id` (`page_type`, `page_id`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

try {
    $pdo->exec($sql);
    $messages[] = "Created page_sections table";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        $messages[] = "page_sections table already exists";
    } else {
        $errors[] = "Error creating page_sections table: " . $e->getMessage();
    }
}

// Get home page ID
$homePageId = null;
try {
    $stmt = $pdo->query("SELECT id FROM pages WHERE slug = 'home' LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $homePageId = $result['id'];
        $messages[] = "Found home page with ID: " . $homePageId;
    }
} catch (PDOException $e) {
    $errors[] = "Error finding home page: " . $e->getMessage();
}

// Check if home page already has sections
$hasHomeSections = false;
if ($homePageId) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM page_sections WHERE page_type = 'page' AND page_id = ?");
        $stmt->execute([$homePageId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $hasHomeSections = ($result['cnt'] ?? 0) > 0;
    } catch (PDOException $e) {
        // Table might not exist yet
    }
}

// Create default sections for home page if none exist
if ($homePageId && !$hasHomeSections) {
    $defaultSections = [
        [
            'section_type' => 'hero',
            'section_data' => json_encode([
                'heading' => "Leicestershire's Trusted",
                'subheading' => 'From fire risk assessments to IOSH training, we help Midlands businesses create safer workplaces. Over 20 years of experience protecting your people, property, and peace of mind.',
                'image' => '/uploads/safety-consultant.jpg',
                'image_position' => 'right',
                'content_width' => 50,
                'show_cta' => true,
                'button1_text' => 'Get Your Free Quote',
                'button1_url' => '/contact',
                'button2_text' => 'Explore Our Services',
                'button2_url' => '/services'
            ]),
            'sort_order' => 0
        ],
        [
            'section_type' => 'cards',
            'section_data' => json_encode([
                'heading' => 'Comprehensive Health & Safety Solutions',
                'cards' => [] // Will be populated from services
            ]),
            'sort_order' => 1
        ],
        [
            'section_type' => 'text_image',
            'section_data' => json_encode([
                'heading' => '20+ Years Protecting Midlands Businesses',
                'content' => "We're not just consultants – we're your partners in creating safer workplaces. Our practical, no-nonsense approach means you get real solutions, not just paperwork.",
                'image' => '/uploads/consultation.jpg',
                'image_position' => 'right',
                'content_width' => 50
            ]),
            'sort_order' => 2
        ],
        [
            'section_type' => 'cta',
            'section_data' => json_encode([
                'heading' => 'Ready to Improve Your Workplace Safety?',
                'content' => 'Get in touch today for a free, no-obligation consultation. We\'ll discuss your needs and provide practical solutions tailored to your business.',
                'button_text' => 'Get Your Free Quote',
                'button_link' => '/contact',
                'style' => 'navy'
            ]),
            'sort_order' => 3
        ]
    ];

    try {
        $stmt = $pdo->prepare("INSERT INTO page_sections (page_type, page_id, section_type, section_data, sort_order) VALUES ('page', ?, ?, ?, ?)");
        foreach ($defaultSections as $section) {
            $stmt->execute([$homePageId, $section['section_type'], $section['section_data'], $section['sort_order']]);
        }
        $messages[] = "Created " . count($defaultSections) . " default sections for home page";
    } catch (PDOException $e) {
        $errors[] = "Error creating default sections: " . $e->getMessage();
    }
}

// Get about page ID and create default sections
$aboutPageId = null;
try {
    $stmt = $pdo->query("SELECT id FROM pages WHERE slug = 'about' LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $aboutPageId = $result['id'];

        // Check if about page already has sections
        $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM page_sections WHERE page_type = 'page' AND page_id = ?");
        $stmt->execute([$aboutPageId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (($result['cnt'] ?? 0) == 0) {
            $aboutSections = [
                [
                    'section_type' => 'page_header',
                    'section_data' => json_encode([
                        'breadcrumb' => 'About Us',
                        'title' => 'About Integral Safety',
                        'description' => 'Over 20 years of experience protecting your people, property, and peace of mind.',
                        'image' => '/uploads/consultation.jpg',
                        'image_position' => 'right',
                        'content_width' => 55
                    ]),
                    'sort_order' => 0
                ],
                [
                    'section_type' => 'text',
                    'section_data' => json_encode([
                        'heading' => 'Our Story',
                        'content' => "Integral Safety Ltd was founded with a simple mission: to provide practical, proportionate health and safety advice that actually works for businesses.\n\nBased in Leicestershire, we serve clients across the Midlands and beyond, bringing over 20 years of industry experience to every project.\n\nWe believe that good health and safety shouldn't be about ticking boxes – it should be about genuinely protecting people and helping businesses thrive."
                    ]),
                    'sort_order' => 1
                ],
                [
                    'section_type' => 'benefits',
                    'section_data' => json_encode([
                        'heading' => 'Why Choose Integral Safety?',
                        'items' => [
                            'IOSH Approved Training Provider',
                            'Over 20 years industry experience',
                            'Local knowledge of Leicestershire & Midlands',
                            'Practical, no-nonsense approach',
                            'Competitive pricing',
                            'Responsive and reliable service'
                        ]
                    ]),
                    'sort_order' => 2
                ]
            ];

            $stmt = $pdo->prepare("INSERT INTO page_sections (page_type, page_id, section_type, section_data, sort_order) VALUES ('page', ?, ?, ?, ?)");
            foreach ($aboutSections as $section) {
                $stmt->execute([$aboutPageId, $section['section_type'], $section['section_data'], $section['sort_order']]);
            }
            $messages[] = "Created default sections for about page";
        }
    }
} catch (PDOException $e) {
    $errors[] = "Error with about page: " . $e->getMessage();
}

// Get contact page ID and create default sections
$contactPageId = null;
try {
    $stmt = $pdo->query("SELECT id FROM pages WHERE slug = 'contact' LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $contactPageId = $result['id'];

        $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM page_sections WHERE page_type = 'page' AND page_id = ?");
        $stmt->execute([$contactPageId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (($result['cnt'] ?? 0) == 0) {
            $contactSections = [
                [
                    'section_type' => 'page_header',
                    'section_data' => json_encode([
                        'breadcrumb' => 'Contact',
                        'title' => 'Get In Touch',
                        'description' => 'Ready to improve your workplace safety? Contact us for a free, no-obligation quote.',
                        'image_position' => 'none',
                        'content_width' => 100
                    ]),
                    'sort_order' => 0
                ]
            ];

            $stmt = $pdo->prepare("INSERT INTO page_sections (page_type, page_id, section_type, section_data, sort_order) VALUES ('page', ?, ?, ?, ?)");
            foreach ($contactSections as $section) {
                $stmt->execute([$contactPageId, $section['section_type'], $section['section_data'], $section['sort_order']]);
            }
            $messages[] = "Created default sections for contact page";
        }
    }
} catch (PDOException $e) {
    $errors[] = "Error with contact page: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sections Migration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 10px 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 10px 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 10px 15px; border-radius: 5px; margin: 10px 0; }
        a { color: #e85d04; }
    </style>
</head>
<body>
    <h1>Sections Migration</h1>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="success"><?= htmlspecialchars($msg) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="warning">
        <strong>Important:</strong> Delete this file after running the migration!
        <br><br>
        <code>rm admin/migrate-sections.php</code>
    </div>

    <p><a href="/admin/pages.php">Go to Pages &rarr;</a></p>
</body>
</html>
