<?php
/**
 * Seed Blog Articles
 * Creates 12 scheduled health & safety articles
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$articles = [
    [
        'title' => 'Fire Risk Assessments: What Every Business Owner Needs to Know',
        'slug' => 'fire-risk-assessments-what-every-business-owner-needs-to-know',
        'excerpt' => 'A fire risk assessment is a legal requirement for all businesses. Learn what it involves, who needs one, and how to ensure your premises are compliant.',
        'meta_description' => 'Understand fire risk assessment requirements for UK businesses. Learn what is assessed, legal obligations under the Fire Safety Order, and how often reviews are needed.',
        'category' => 'Fire Safety',
        'days_from_now' => 2,
        'content' => '<p>Under the Regulatory Reform (Fire Safety) Order 2005, every business and organisation in England and Wales must carry out a fire risk assessment. This is not just a bureaucratic requirement – it is a fundamental step in protecting your employees, customers, and premises from the devastating effects of fire.</p>

<h2>What is a Fire Risk Assessment?</h2>
<p>A fire risk assessment is a systematic evaluation of your premises to identify potential fire hazards, determine who might be at risk, and implement appropriate measures to reduce or eliminate those risks. The assessment must be recorded if you employ five or more people, though we recommend documenting it regardless of staff numbers.</p>

<h2>Who is Responsible?</h2>
<p>The "responsible person" for fire safety is typically:</p>
<ul>
<li>The employer, if the workplace is under their control</li>
<li>The owner or landlord, for shared or common areas</li>
<li>The occupier, if they have control of the premises</li>
<li>Any person who has some degree of control over the premises</li>
</ul>

<h2>The Five Steps of Fire Risk Assessment</h2>
<p>The HSE and fire services recommend following five key steps:</p>

<h3>1. Identify Fire Hazards</h3>
<p>Look for potential sources of ignition (heaters, electrical equipment, naked flames), fuel sources (paper, textiles, flammable liquids), and oxygen sources (air conditioning, stored oxidising materials).</p>

<h3>2. Identify People at Risk</h3>
<p>Consider everyone who might be in the building, including employees, visitors, contractors, and particularly vulnerable individuals such as those with mobility impairments, hearing or visual impairments, or anyone unfamiliar with the premises.</p>

<h3>3. Evaluate, Remove, and Reduce Risks</h3>
<p>Assess the likelihood of a fire starting and its potential consequences. Implement control measures such as removing or reducing fuel sources, controlling ignition sources, and ensuring adequate fire detection and warning systems.</p>

<h3>4. Record, Plan, and Train</h3>
<p>Document your findings, prepare an emergency plan, and ensure all staff receive appropriate fire safety training. This includes fire warden training, evacuation procedures, and use of fire extinguishers.</p>

<h3>5. Review and Update</h3>
<p>Fire risk assessments should be living documents. Review them regularly, particularly after any significant changes to your premises, processes, or staffing levels.</p>

<h2>How Often Should You Review?</h2>
<p>While there is no set legal timeframe, best practice suggests reviewing your fire risk assessment:</p>
<ul>
<li>Annually as a minimum</li>
<li>After any fire or near miss</li>
<li>Following significant changes to the building or its use</li>
<li>When new hazards are introduced</li>
<li>After changes in occupancy or working patterns</li>
</ul>

<h2>The Consequences of Non-Compliance</h2>
<p>Failure to comply with fire safety regulations can result in enforcement notices, prohibition notices (closure of premises), unlimited fines, and in serious cases, imprisonment. More importantly, inadequate fire safety can cost lives.</p>

<h2>Professional Fire Risk Assessment Services</h2>
<p>While you can conduct a fire risk assessment yourself, many businesses benefit from professional assessment services. A competent fire safety professional brings expertise, objectivity, and up-to-date knowledge of regulations and best practices.</p>

<p>At Integral Safety, our fire risk assessments are thorough, practical, and tailored to your specific premises and operations. We provide clear, actionable recommendations and ongoing support to help you maintain compliance.</p>'
    ],
    [
        'title' => 'Understanding COSHH: A Guide to Hazardous Substances in the Workplace',
        'slug' => 'understanding-coshh-hazardous-substances-workplace',
        'excerpt' => 'COSHH regulations protect workers from hazardous substances. This guide explains your legal duties and how to conduct effective COSHH assessments.',
        'meta_description' => 'Learn about COSHH regulations, hazardous substance classifications, and how to protect your employees. Essential guide for UK employers.',
        'category' => 'Health & Safety',
        'days_from_now' => 5,
        'content' => '<p>The Control of Substances Hazardous to Health Regulations 2002 (COSHH) require employers to control substances that can harm workers\' health. From cleaning chemicals to dust and fumes, hazardous substances are present in almost every workplace.</p>

<h2>What Substances Does COSHH Cover?</h2>
<p>COSHH applies to a wide range of substances including:</p>
<ul>
<li>Chemicals used directly in work activities (cleaning agents, adhesives, paints)</li>
<li>Substances generated during work (dust, fumes, vapours)</li>
<li>Naturally occurring substances (flour dust, wood dust)</li>
<li>Biological agents (bacteria, viruses)</li>
</ul>

<h2>Employer Duties Under COSHH</h2>
<p>As an employer, you must:</p>
<ul>
<li>Assess the risks to health from hazardous substances</li>
<li>Prevent or adequately control exposure</li>
<li>Ensure control measures are maintained and used</li>
<li>Monitor exposure and conduct health surveillance where required</li>
<li>Provide information, instruction, and training</li>
</ul>

<h2>Conducting a COSHH Assessment</h2>
<p>A thorough COSHH assessment involves:</p>

<h3>Identifying Hazardous Substances</h3>
<p>Review all substances used, produced, or encountered in your workplace. Check Safety Data Sheets (SDS) for hazard information and examine product labels for hazard pictograms.</p>

<h3>Assessing the Risks</h3>
<p>Consider how workers might be exposed (inhalation, skin contact, ingestion), the extent and frequency of exposure, and the severity of potential health effects.</p>

<h3>Implementing Control Measures</h3>
<p>Apply the hierarchy of control:</p>
<ol>
<li>Eliminate the substance if possible</li>
<li>Substitute with a less hazardous alternative</li>
<li>Use engineering controls (ventilation, enclosure)</li>
<li>Implement administrative controls (procedures, training)</li>
<li>Provide personal protective equipment as a last resort</li>
</ol>

<h2>Common COSHH Mistakes</h2>
<p>We frequently encounter these issues during audits:</p>
<ul>
<li>Missing or outdated Safety Data Sheets</li>
<li>Generic assessments that do not reflect actual work practices</li>
<li>Over-reliance on PPE without considering other controls</li>
<li>Lack of training on specific substances and procedures</li>
<li>Failure to review assessments after changes</li>
</ul>

<h2>Getting COSHH Right</h2>
<p>Effective COSHH management protects your workers\' health and demonstrates your commitment to safety. If you need assistance with COSHH assessments or training, our consultants can help you develop practical, compliant solutions tailored to your operations.</p>'
    ],
    [
        'title' => 'Manual Handling: Reducing Workplace Injuries Through Proper Training',
        'slug' => 'manual-handling-reducing-workplace-injuries',
        'excerpt' => 'Manual handling injuries account for over a third of workplace injuries. Discover how proper training and risk assessment can protect your team.',
        'meta_description' => 'Reduce manual handling injuries in your workplace. Learn about legal requirements, risk assessment techniques, and effective training strategies.',
        'category' => 'Training',
        'days_from_now' => 9,
        'content' => '<p>Manual handling injuries remain one of the most common causes of workplace ill health in the UK. According to HSE statistics, handling, lifting, and carrying activities account for over a third of all workplace injuries, resulting in millions of lost working days each year.</p>

<h2>What is Manual Handling?</h2>
<p>Manual handling refers to any transporting or supporting of a load by hand or bodily force. This includes lifting, putting down, pushing, pulling, carrying, or moving loads. The load can be an object, person, or animal.</p>

<h2>Legal Requirements</h2>
<p>The Manual Handling Operations Regulations 1992 require employers to:</p>
<ul>
<li>Avoid hazardous manual handling operations where reasonably practicable</li>
<li>Assess any hazardous operations that cannot be avoided</li>
<li>Reduce the risk of injury as far as reasonably practicable</li>
<li>Review assessments if circumstances change</li>
</ul>

<h2>Risk Factors in Manual Handling</h2>
<p>When assessing manual handling risks, consider the TILE acronym:</p>

<h3>Task</h3>
<p>Does the task involve twisting, stooping, reaching upward, large vertical movement, long carrying distances, strenuous pushing or pulling, or repetitive handling?</p>

<h3>Individual</h3>
<p>Does the task require unusual strength or height, pose a risk to those with health problems or pregnant workers, or require specialist knowledge or training?</p>

<h3>Load</h3>
<p>Is the load heavy, bulky, difficult to grasp, unstable, sharp, hot, or cold?</p>

<h3>Environment</h3>
<p>Are there space constraints, uneven floors, slippery surfaces, variations in levels, poor lighting, or extreme temperatures?</p>

<h2>Reducing Manual Handling Risks</h2>
<p>Apply the hierarchy of control:</p>
<ol>
<li><strong>Avoid</strong> - Can the task be eliminated or automated?</li>
<li><strong>Mechanise</strong> - Can handling aids be used (trolleys, hoists, conveyors)?</li>
<li><strong>Redesign</strong> - Can the task or load be modified to reduce risk?</li>
<li><strong>Train</strong> - Ensure workers understand safe handling techniques</li>
</ol>

<h2>The Value of Training</h2>
<p>While training alone is not sufficient to control manual handling risks, it is an essential component of any risk reduction strategy. Effective training helps workers:</p>
<ul>
<li>Understand the risks and their causes</li>
<li>Recognise hazardous handling activities</li>
<li>Use appropriate handling techniques</li>
<li>Make proper use of mechanical aids</li>
<li>Report problems and suggest improvements</li>
</ul>

<p>Our manual handling training courses combine theory with practical exercises, ensuring participants can apply what they learn directly to their work activities.</p>'
    ],
    [
        'title' => 'Working at Height: Essential Safety Measures for 2025',
        'slug' => 'working-at-height-safety-measures-2025',
        'excerpt' => 'Falls from height remain one of the biggest causes of workplace fatalities. Learn the latest regulations and best practices for keeping workers safe.',
        'meta_description' => 'Working at height safety guide for UK businesses. Covers regulations, equipment requirements, training needs, and risk assessment procedures.',
        'category' => 'Health & Safety',
        'days_from_now' => 12,
        'content' => '<p>Falls from height continue to be one of the biggest causes of workplace fatalities and major injuries in the UK. In 2023/24, falls from height accounted for a significant proportion of fatal injuries to workers. These tragic incidents are preventable with proper planning, equipment, and training.</p>

<h2>What Counts as Working at Height?</h2>
<p>Working at height means working in any place where a person could fall a distance liable to cause personal injury. This includes:</p>
<ul>
<li>Working on ladders, scaffolding, or mobile towers</li>
<li>Working on roofs or near edges</li>
<li>Working near openings or fragile surfaces</li>
<li>Working at ground level near excavations</li>
</ul>

<h2>The Work at Height Regulations 2005</h2>
<p>These regulations require duty holders to:</p>
<ul>
<li>Avoid work at height where possible</li>
<li>Use work equipment or other measures to prevent falls where working at height cannot be avoided</li>
<li>Where the risk of a fall cannot be eliminated, use work equipment to minimise the distance and consequences of a fall</li>
</ul>

<h2>Planning and Organising Work at Height</h2>
<p>Before any work at height begins, ensure:</p>
<ul>
<li>The work is properly planned and supervised</li>
<li>A risk assessment has been completed</li>
<li>Appropriate equipment has been selected</li>
<li>Workers are competent and trained</li>
<li>Weather conditions are suitable</li>
<li>Emergency and rescue procedures are in place</li>
</ul>

<h2>Choosing the Right Equipment</h2>
<p>Select equipment based on the hierarchy of control:</p>
<ol>
<li><strong>Collective protection first</strong> - guardrails, working platforms</li>
<li><strong>Personal protection</strong> - harnesses, fall arrest systems</li>
<li><strong>Ladders and stepladders</strong> - only for short-duration, low-risk work</li>
</ol>

<h2>Ladder Safety</h2>
<p>Ladders should only be used when other equipment is not reasonably practicable and when the risk assessment shows the task is low risk and short duration. When using ladders:</p>
<ul>
<li>Ensure they are in good condition</li>
<li>Position them on firm, level ground</li>
<li>Secure them at the top or bottom</li>
<li>Maintain three points of contact</li>
<li>Do not overreach</li>
</ul>

<h2>Training Requirements</h2>
<p>Anyone involved in work at height must be competent. This means they need sufficient training, experience, and knowledge to carry out the work safely. Training should cover:</p>
<ul>
<li>Risk assessment for work at height</li>
<li>Safe use of specific equipment</li>
<li>Inspection and maintenance requirements</li>
<li>Emergency and rescue procedures</li>
</ul>

<p>Integral Safety provides comprehensive work at height training and can help you assess risks and select appropriate equipment for your specific activities.</p>'
    ],
    [
        'title' => 'First Aid at Work: How Many First Aiders Does Your Business Need?',
        'slug' => 'first-aid-at-work-how-many-first-aiders',
        'excerpt' => 'The number of first aiders your business needs depends on several factors. This guide helps you determine your requirements and stay compliant.',
        'meta_description' => 'Calculate first aid requirements for your workplace. Guidance on first aider numbers, equipment needs, and training requirements for UK businesses.',
        'category' => 'Training',
        'days_from_now' => 16,
        'content' => '<p>The Health and Safety (First-Aid) Regulations 1981 require employers to provide adequate and appropriate equipment, facilities, and personnel to ensure their employees receive immediate attention if they are injured or taken ill at work.</p>

<h2>First Aid Needs Assessment</h2>
<p>There is no fixed formula for determining first aid provision. You must assess your specific circumstances, considering:</p>
<ul>
<li>The nature of your work and workplace hazards</li>
<li>The size and spread of your workforce</li>
<li>Your accident and ill health history</li>
<li>Access to emergency services</li>
<li>Presence of inexperienced workers or those with disabilities</li>
<li>Holiday and shift cover requirements</li>
</ul>

<h2>Minimum Recommended Provision</h2>
<p>As a general guide, the HSE suggests:</p>

<h3>Low Hazard Environments (offices, shops, libraries)</h3>
<ul>
<li>Fewer than 25 employees: At least one appointed person</li>
<li>25-50 employees: At least one first aider (FAW or EFAW)</li>
<li>More than 50 employees: One first aider for every 100 employees</li>
</ul>

<h3>Higher Hazard Environments (construction, manufacturing, warehouses)</h3>
<ul>
<li>Fewer than 5 employees: At least one appointed person</li>
<li>5-50 employees: At least one first aider (FAW recommended)</li>
<li>More than 50 employees: One first aider for every 50 employees</li>
</ul>

<h2>Types of First Aid Training</h2>

<h3>First Aid at Work (FAW)</h3>
<p>A comprehensive three-day course covering a wide range of first aid situations. Suitable for higher-risk environments or as main first aid provision.</p>

<h3>Emergency First Aid at Work (EFAW)</h3>
<p>A one-day course covering basic life-saving first aid. Suitable for lower-risk environments or to supplement FAW holders.</p>

<h3>Appointed Person</h3>
<p>Not a trained first aider, but someone who takes charge of first aid arrangements and calls emergency services when needed. A half-day course is available.</p>

<h2>First Aid Equipment</h2>
<p>At minimum, you need a suitably stocked first aid kit. Contents should be based on your risk assessment, but typically include:</p>
<ul>
<li>Sterile plasters of various sizes</li>
<li>Sterile eye pads</li>
<li>Triangular bandages</li>
<li>Safety pins</li>
<li>Sterile wound dressings</li>
<li>Disposable gloves</li>
<li>First aid guidance leaflet</li>
</ul>

<h2>Keeping Records</h2>
<p>Record all first aid treatment given, including the name of the patient, date, circumstances, and treatment provided. This helps identify trends and informs your risk assessments.</p>

<p>Integral Safety offers first aid training courses at various levels, delivered at your premises for convenience. Contact us to discuss your first aid needs.</p>'
    ],
    [
        'title' => 'Fire Door Safety: Maintenance and Compliance Requirements',
        'slug' => 'fire-door-safety-maintenance-compliance',
        'excerpt' => 'Fire doors are critical life-safety features that require regular inspection. Learn what to check and how often maintenance should be carried out.',
        'meta_description' => 'Fire door inspection and maintenance guide. Understand compliance requirements, common defects, and inspection frequencies for UK buildings.',
        'category' => 'Fire Safety',
        'days_from_now' => 19,
        'content' => '<p>Fire doors are critical life-safety features designed to compartmentalise buildings and protect escape routes. Yet they are frequently found to be defective during fire risk assessments. Understanding fire door requirements is essential for building managers and responsible persons.</p>

<h2>How Fire Doors Work</h2>
<p>Fire doors are engineered to:</p>
<ul>
<li>Resist the passage of fire for a specified period (typically 30 or 60 minutes)</li>
<li>Prevent the spread of smoke through intumescent strips and smoke seals</li>
<li>Self-close to ensure protection is maintained</li>
<li>Allow safe evacuation while containing fire spread</li>
</ul>

<h2>Regulatory Requirements</h2>
<p>The Regulatory Reform (Fire Safety) Order 2005 requires the responsible person to ensure fire safety measures, including fire doors, are properly maintained. The Fire Safety (England) Regulations 2022 introduced additional requirements for residential buildings, including quarterly checks of flat entrance doors and annual checks of fire doors in common areas.</p>

<h2>Common Fire Door Defects</h2>
<p>Our fire risk assessments regularly identify these issues:</p>
<ul>
<li>Damaged or missing intumescent strips and smoke seals</li>
<li>Faulty or disconnected self-closing devices</li>
<li>Excessive gaps around doors (over 3mm)</li>
<li>Doors held open without automatic release devices</li>
<li>Damaged door leaves, frames, or glazing</li>
<li>Incorrect hinges (three required for fire doors)</li>
<li>Inappropriate locks or furniture</li>
</ul>

<h2>Inspection Frequency</h2>
<p>Fire doors should be checked regularly:</p>
<ul>
<li><strong>Weekly</strong> - Visual check by building users/staff</li>
<li><strong>Monthly</strong> - More detailed check by trained staff</li>
<li><strong>Annually</strong> - Comprehensive inspection by a competent person</li>
</ul>

<h2>What to Check</h2>
<p>A basic fire door check should verify:</p>
<ul>
<li>The door closes fully into the frame</li>
<li>All seals and strips are present and undamaged</li>
<li>The self-closer operates correctly</li>
<li>Gaps around the door are consistent (2-4mm typically)</li>
<li>Hinges are secure with no missing screws</li>
<li>The door is not damaged, split, or warped</li>
<li>Vision panels are intact</li>
<li>Signage is present where required</li>
</ul>

<h2>When to Replace</h2>
<p>Fire doors should be replaced if:</p>
<ul>
<li>They cannot be repaired to meet fire resistance requirements</li>
<li>Damage is extensive or structural</li>
<li>The door no longer fits correctly in the frame</li>
<li>Third-party certification cannot be verified</li>
</ul>

<p>Our fire door surveys provide a detailed assessment of all fire doors within your building, identifying defects and recommending proportionate remedial actions.</p>'
    ]
];

$count = 0;
$skipped = 0;
$errors = [];

foreach ($articles as $article) {
    $publishDate = date('Y-m-d H:i:s', strtotime('+' . $article['days_from_now'] . ' days'));

    // Check if already exists
    $existing = dbFetchOne("SELECT id FROM blog_posts WHERE slug = ?", [$article['slug']]);
    if ($existing) {
        $skipped++;
        continue;
    }

    try {
        dbExecute(
            "INSERT INTO blog_posts (title, slug, excerpt, content, meta_description, focus_keyphrase, category, status, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'scheduled', ?)",
            [
                $article['title'],
                $article['slug'],
                $article['excerpt'],
                $article['content'],
                $article['meta_description'],
                strtolower(str_replace(' & ', ' ', $article['category'])),
                $article['category'],
                $publishDate
            ]
        );
        $count++;
    } catch (Exception $e) {
        $errors[] = $article['title'] . ': ' . $e->getMessage();
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Blog Article Seeder (Part 1 of 2)</h1>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <h3 class="font-bold">Errors:</h3>
        <ul class="list-disc ml-5">
            <?php foreach ($errors as $error): ?>
            <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <p><strong><?= $count ?></strong> articles created successfully.</p>
        <?php if ($skipped > 0): ?>
        <p><strong><?= $skipped ?></strong> articles skipped (already exist).</p>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Articles Created</h2>
        <ul class="space-y-2 text-sm">
            <?php foreach ($articles as $article): ?>
            <li class="flex justify-between items-center py-2 border-b">
                <span><?= e($article['title']) ?></span>
                <span class="text-gray-500"><?= date('j M Y', strtotime('+' . $article['days_from_now'] . ' days')) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>

        <div class="mt-6 flex gap-4">
            <a href="/admin/seed-blog-articles-2.php" class="inline-block bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                Continue to Part 2
            </a>
            <a href="/admin/blog.php" class="inline-block bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                Go to Blog Management
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
