<?php
/**
 * Seed Blog Articles - Part 2
 * Creates remaining 6 scheduled health & safety articles
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$articles = [
    [
        'title' => 'DSE Assessments: Protecting Office Workers in the Hybrid Age',
        'slug' => 'dse-assessments-protecting-office-workers',
        'excerpt' => 'With hybrid working now commonplace, DSE assessments need to cover both office and home setups. Learn how to protect your workforce effectively.',
        'meta_description' => 'Display Screen Equipment assessment guide for modern workplaces. Covers home working, ergonomic requirements, and employer legal duties.',
        'category' => 'Health & Safety',
        'days_from_now' => 23,
        'content' => '<p>The Health and Safety (Display Screen Equipment) Regulations 1992 require employers to protect workers who regularly use computers and other display screen equipment. With the growth of hybrid working, these requirements now extend to home working setups.</p>

<h2>Who is a DSE User?</h2>
<p>An employee is a DSE user if they habitually use display screen equipment as a significant part of their normal work. This typically means using DSE for continuous periods of an hour or more on most days.</p>

<h2>Employer Duties</h2>
<p>For DSE users, employers must:</p>
<ul>
<li>Assess workstations and reduce risks identified</li>
<li>Ensure workstations meet minimum requirements</li>
<li>Plan work to include breaks or changes of activity</li>
<li>Provide eye tests on request and glasses if needed for DSE work</li>
<li>Provide health and safety training and information</li>
</ul>

<h2>Workstation Requirements</h2>
<p>The regulations set minimum standards for:</p>

<h3>Display Screen</h3>
<ul>
<li>Clear, well-defined characters</li>
<li>Adjustable brightness and contrast</li>
<li>Easily tilting and swivelling screen</li>
<li>No reflections or glare</li>
</ul>

<h3>Keyboard</h3>
<ul>
<li>Separate from screen (for desktop use)</li>
<li>Tiltable with legible symbols</li>
<li>Space in front for hands to rest</li>
</ul>

<h3>Work Surface</h3>
<ul>
<li>Sufficiently large for flexible arrangement</li>
<li>Low reflectance surface</li>
<li>Document holder if needed</li>
</ul>

<h3>Chair</h3>
<ul>
<li>Adjustable height seat and back</li>
<li>Stable five-point base</li>
<li>Footrest if needed</li>
</ul>

<h2>Home Working Considerations</h2>
<p>The same DSE regulations apply to home workers as to office workers. Employers should:</p>
<ul>
<li>Conduct DSE assessments for home workstations</li>
<li>Provide equipment where necessary (chairs, monitors, keyboards)</li>
<li>Offer guidance on workstation setup</li>
<li>Ensure workers know how to adjust their equipment</li>
<li>Review arrangements periodically</li>
</ul>

<h2>Common DSE Issues</h2>
<p>Problems frequently identified include:</p>
<ul>
<li>Screen positioned too high or low</li>
<li>Working from laptops without external peripherals</li>
<li>Poor lighting causing glare or eye strain</li>
<li>Insufficient desk space</li>
<li>Unsuitable seating</li>
<li>Lack of movement or breaks</li>
</ul>

<h2>Health Effects</h2>
<p>Poor DSE setup can lead to:</p>
<ul>
<li>Musculoskeletal disorders (back, neck, shoulder, arm pain)</li>
<li>Eye strain and headaches</li>
<li>Fatigue and stress</li>
</ul>

<p>Integral Safety can help you develop effective DSE assessment processes for both office and home-based workers, including self-assessment tools and manager training.</p>'
    ],
    [
        'title' => 'The Benefits of IOSH Training for Your Organisation',
        'slug' => 'benefits-iosh-training-organisation',
        'excerpt' => 'IOSH qualifications are recognised worldwide. Discover how investing in IOSH training can improve safety culture and reduce incidents.',
        'meta_description' => 'Explore the benefits of IOSH Managing Safely and IOSH Working Safely courses. Learn how accredited training improves workplace safety culture.',
        'category' => 'Training',
        'days_from_now' => 26,
        'content' => '<p>IOSH (Institution of Occupational Safety and Health) is the world\'s largest health and safety membership organisation. Their training courses are recognised globally and provide practical skills that can be immediately applied in the workplace.</p>

<h2>IOSH Managing Safely</h2>
<p>This flagship course is designed for managers and supervisors in any sector. Over three to four days, delegates learn:</p>
<ul>
<li>The importance of health and safety management</li>
<li>How to assess and control risks</li>
<li>How to identify hazards and implement controls</li>
<li>How to investigate incidents effectively</li>
<li>How to measure health and safety performance</li>
</ul>

<h3>Who Should Attend?</h3>
<p>IOSH Managing Safely is ideal for managers, supervisors, and team leaders who need to understand their health and safety responsibilities. It is particularly valuable for those new to management or those who have not had formal H&S training.</p>

<h2>IOSH Working Safely</h2>
<p>A one-day course designed for employees at all levels, IOSH Working Safely covers:</p>
<ul>
<li>Why health and safety matters</li>
<li>Defining hazards and risks</li>
<li>Identifying common hazards</li>
<li>Improving safety performance</li>
</ul>

<h3>Who Should Attend?</h3>
<p>IOSH Working Safely is suitable for all employees regardless of role or industry. It provides a solid foundation of health and safety awareness.</p>

<h2>Benefits for Your Organisation</h2>

<h3>Improved Safety Culture</h3>
<p>Trained managers and employees are more likely to identify and report hazards, follow safe working practices, and contribute to a positive safety culture.</p>

<h3>Reduced Incidents</h3>
<p>Understanding risk assessment and control measures leads to fewer accidents and incidents, reducing human suffering, lost time, and costs.</p>

<h3>Legal Compliance</h3>
<p>Demonstrating that staff have received recognised health and safety training helps meet your duty to provide information, instruction, and training.</p>

<h3>Recognised Certification</h3>
<p>IOSH certificates are widely recognised by employers, clients, and regulators. They demonstrate commitment to professional development and safety standards.</p>

<h3>Practical Application</h3>
<p>IOSH courses use real-world examples and practical exercises. Delegates complete a risk assessment project, ensuring they can apply their learning immediately.</p>

<h2>Delivery Options</h2>
<p>We deliver IOSH courses:</p>
<ul>
<li>At your premises for groups (most cost-effective)</li>
<li>At scheduled open courses (for individuals or small numbers)</li>
</ul>

<p>Contact us to discuss which IOSH course is right for your team and to arrange training at a time that suits your business.</p>'
    ],
    [
        'title' => 'Fire Safety in Care Homes: Special Considerations for Vulnerable Residents',
        'slug' => 'fire-safety-care-homes-vulnerable-residents',
        'excerpt' => 'Care homes face unique fire safety challenges due to resident mobility and awareness. Learn about PEEPs, compartmentation, and compliance requirements.',
        'meta_description' => 'Fire safety guidance for care homes and residential settings. Covers PEEPs, evacuation strategies, and specific requirements for vulnerable occupants.',
        'category' => 'Fire Safety',
        'days_from_now' => 30,
        'content' => '<p>Care homes and other residential settings for vulnerable people face unique fire safety challenges. Residents may have limited mobility, cognitive impairment, or sensory loss that affects their ability to respond to fire alarms and evacuate safely. This makes comprehensive fire safety planning essential.</p>

<h2>Regulatory Framework</h2>
<p>Care homes must comply with the Regulatory Reform (Fire Safety) Order 2005, as well as CQC regulations and National Minimum Standards. Fire safety is a key area of regulatory inspection.</p>

<h2>Personal Emergency Evacuation Plans (PEEPs)</h2>
<p>Every resident should have a PEEP that details:</p>
<ul>
<li>Their specific evacuation requirements</li>
<li>Any mobility or sensory impairments</li>
<li>Equipment needed for evacuation</li>
<li>Level of assistance required</li>
<li>Preferred evacuation method and route</li>
</ul>

<h3>Keeping PEEPs Current</h3>
<p>PEEPs should be reviewed:</p>
<ul>
<li>When a resident\'s condition changes</li>
<li>After any incident or near miss</li>
<li>At least annually</li>
<li>When staffing levels change</li>
</ul>

<h2>Evacuation Strategies</h2>
<p>Care homes typically employ progressive horizontal evacuation or defend in place strategies rather than simultaneous evacuation. This involves:</p>
<ul>
<li>Moving residents away from the fire to a place of relative safety</li>
<li>Using compartmentation to protect residents who cannot move quickly</li>
<li>Having sufficient trained staff available at all times</li>
</ul>

<h2>Compartmentation</h2>
<p>Effective compartmentation is critical in care settings. Fire-resistant walls, floors, and doors divide the building into compartments that contain fire and smoke for a specified period, allowing time for evacuation.</p>

<h3>Common Compartmentation Failures</h3>
<ul>
<li>Fire doors wedged or propped open</li>
<li>Damaged or missing fire stopping</li>
<li>Service penetrations not properly sealed</li>
<li>Ceiling voids without adequate compartmentation</li>
</ul>

<h2>Detection and Alarm Systems</h2>
<p>Care homes should have:</p>
<ul>
<li>Automatic fire detection throughout</li>
<li>Alarm systems suitable for residents with hearing impairment</li>
<li>Staff alert systems for quiet evacuation if appropriate</li>
<li>Regular testing and maintenance</li>
</ul>

<h2>Staff Training</h2>
<p>All staff must receive fire safety training covering:</p>
<ul>
<li>Fire prevention measures</li>
<li>Action on discovering a fire</li>
<li>Evacuation procedures and PEEP awareness</li>
<li>Use of evacuation equipment</li>
<li>Fire extinguisher use (appropriate staff)</li>
</ul>

<h2>Night-Time Considerations</h2>
<p>Reduced staffing at night increases risk. Ensure:</p>
<ul>
<li>Sufficient staff to implement evacuation</li>
<li>All staff know the location and use of evacuation equipment</li>
<li>Regular night-time fire drills</li>
<li>Enhanced automatic detection</li>
</ul>

<p>Our care home fire risk assessments take full account of resident needs and regulatory requirements. We provide practical recommendations that balance safety with the homely environment residents deserve.</p>'
    ],
    [
        'title' => 'Accident Investigation: Turning Incidents into Learning Opportunities',
        'slug' => 'accident-investigation-learning-opportunities',
        'excerpt' => 'Effective accident investigation helps prevent recurrence. Learn the structured approach to investigating workplace incidents and near misses.',
        'meta_description' => 'Workplace accident investigation guide. Learn structured investigation techniques, root cause analysis, and how to implement effective corrective actions.',
        'category' => 'Health & Safety',
        'days_from_now' => 33,
        'content' => '<p>When accidents happen, there is a natural temptation to assign blame and move on. However, effective accident investigation focuses on understanding why an incident occurred, not just who was involved. This approach transforms accidents into opportunities for organisational learning and genuine improvement.</p>

<h2>Why Investigate?</h2>
<p>Proper investigation helps you:</p>
<ul>
<li>Identify the root causes of incidents</li>
<li>Prevent similar incidents recurring</li>
<li>Meet legal requirements under RIDDOR</li>
<li>Demonstrate due diligence to regulators</li>
<li>Support affected workers</li>
<li>Identify systemic weaknesses</li>
</ul>

<h2>What to Investigate</h2>
<p>Consider investigating:</p>
<ul>
<li>All injuries requiring first aid or medical treatment</li>
<li>Near misses with potential for serious injury</li>
<li>Dangerous occurrences</li>
<li>Cases of work-related ill health</li>
<li>Property damage incidents</li>
<li>Environmental incidents</li>
</ul>

<h2>The Investigation Process</h2>

<h3>1. Immediate Response</h3>
<ul>
<li>Ensure the area is safe</li>
<li>Provide first aid and emergency services as needed</li>
<li>Preserve the scene where possible</li>
<li>Identify witnesses</li>
</ul>

<h3>2. Gather Information</h3>
<ul>
<li>Take photographs and measurements</li>
<li>Collect physical evidence</li>
<li>Interview witnesses and those involved</li>
<li>Review relevant documents (risk assessments, training records, maintenance logs)</li>
</ul>

<h3>3. Analyse the Information</h3>
<ul>
<li>Establish the sequence of events</li>
<li>Identify immediate causes</li>
<li>Determine underlying and root causes</li>
<li>Use structured techniques (5 Whys, fault tree analysis)</li>
</ul>

<h3>4. Identify Corrective Actions</h3>
<ul>
<li>Address root causes, not just symptoms</li>
<li>Consider the hierarchy of control</li>
<li>Assign responsibility and timescales</li>
<li>Ensure actions are SMART</li>
</ul>

<h3>5. Implement and Monitor</h3>
<ul>
<li>Track completion of actions</li>
<li>Verify effectiveness</li>
<li>Share learning across the organisation</li>
<li>Update risk assessments and procedures</li>
</ul>

<h2>Root Cause Analysis</h2>
<p>Root causes are the underlying systemic failures that allowed the incident to occur. They often relate to:</p>
<ul>
<li>Management systems and leadership</li>
<li>Risk assessment processes</li>
<li>Communication and supervision</li>
<li>Training and competence</li>
<li>Procedures and safe systems of work</li>
<li>Equipment maintenance and selection</li>
</ul>

<h2>Avoiding Common Pitfalls</h2>
<ul>
<li>Do not focus on blame - focus on systems</li>
<li>Do not accept "human error" as a root cause</li>
<li>Do not rush the investigation</li>
<li>Do not ignore near misses</li>
<li>Do not let actions drift</li>
</ul>

<p>Integral Safety can support your accident investigation process, from training your team in investigation techniques to conducting independent investigations of serious incidents.</p>'
    ],
    [
        'title' => 'Legionella Risk Assessment: Protecting Your Water Systems',
        'slug' => 'legionella-risk-assessment-water-systems',
        'excerpt' => 'Legionella bacteria can thrive in poorly maintained water systems. Understand your legal duties and how to manage this serious health risk.',
        'meta_description' => 'Legionella risk assessment requirements for UK businesses. Learn about water system hazards, control measures, and compliance with HSE guidance.',
        'category' => 'Health & Safety',
        'days_from_now' => 37,
        'content' => '<p>Legionnaires\' disease is a potentially fatal form of pneumonia caused by Legionella bacteria. These bacteria thrive in water systems between 20-45 degrees Celsius and can be inhaled through water droplets. Employers and building owners have a legal duty to assess and control Legionella risks.</p>

<h2>Legal Requirements</h2>
<p>Under the Health and Safety at Work Act 1974 and Control of Substances Hazardous to Health Regulations 2002 (COSHH), duty holders must:</p>
<ul>
<li>Identify and assess sources of Legionella risk</li>
<li>Prepare a scheme to prevent or control risk</li>
<li>Implement and manage the scheme</li>
<li>Keep records</li>
<li>Appoint a competent responsible person</li>
</ul>

<h2>High-Risk Water Systems</h2>
<p>Systems that may require Legionella risk assessment include:</p>
<ul>
<li>Hot and cold water systems</li>
<li>Cooling towers and evaporative condensers</li>
<li>Spa pools and hot tubs</li>
<li>Humidifiers and air washers</li>
<li>Dental equipment</li>
<li>Vehicle wash systems</li>
<li>Indoor fountains and water features</li>
</ul>

<h2>Risk Factors</h2>
<p>Conditions that increase Legionella risk:</p>
<ul>
<li>Water temperature between 20-45 degrees Celsius</li>
<li>Stagnation (dead legs, infrequently used outlets)</li>
<li>Biofilm and scale deposits</li>
<li>Nutrients for bacterial growth (rust, sludge, organic matter)</li>
<li>Aerosol generation (showers, spray taps, cooling towers)</li>
</ul>

<h2>Who is at Risk?</h2>
<p>While anyone can contract Legionnaires\' disease, higher risk groups include:</p>
<ul>
<li>People over 45 years old</li>
<li>Smokers and heavy drinkers</li>
<li>People with chronic respiratory or kidney disease</li>
<li>Those with weakened immune systems</li>
</ul>

<h2>Control Measures</h2>
<p>Key control measures include:</p>

<h3>Temperature Control</h3>
<ul>
<li>Store hot water at 60 degrees Celsius or above</li>
<li>Distribute hot water at 50 degrees Celsius or above</li>
<li>Keep cold water below 20 degrees Celsius</li>
</ul>

<h3>System Management</h3>
<ul>
<li>Avoid stagnation - flush little-used outlets weekly</li>
<li>Remove dead legs and redundant pipework</li>
<li>Clean and disinfect systems regularly</li>
<li>Maintain accurate records</li>
</ul>

<h3>Monitoring</h3>
<ul>
<li>Monthly temperature checks at representative outlets</li>
<li>Quarterly checks of calorifier temperatures</li>
<li>Visual inspections of tanks and calorifiers</li>
<li>Water sampling where appropriate</li>
</ul>

<h2>Legionella Risk Assessment</h2>
<p>A competent assessment should:</p>
<ul>
<li>Identify all water systems and their components</li>
<li>Assess the risk of Legionella exposure</li>
<li>Recommend control measures</li>
<li>Specify monitoring and maintenance requirements</li>
<li>Be reviewed every two years or when circumstances change</li>
</ul>

<p>Integral Safety works with water hygiene specialists to provide comprehensive Legionella risk assessments and ongoing monitoring services.</p>'
    ],
    [
        'title' => 'Creating a Positive Safety Culture: Beyond Compliance',
        'slug' => 'creating-positive-safety-culture-beyond-compliance',
        'excerpt' => 'A strong safety culture goes beyond ticking boxes. Discover how to engage your workforce and make health and safety part of your organisation\'s DNA.',
        'meta_description' => 'Build a positive workplace safety culture. Practical strategies for employee engagement, leadership commitment, and continuous improvement in H&S.',
        'category' => 'Health & Safety',
        'days_from_now' => 40,
        'content' => '<p>A truly effective health and safety management system goes beyond policies, procedures, and risk assessments. It requires a positive safety culture where everyone - from senior leadership to frontline workers - is committed to working safely and looking out for each other.</p>

<h2>What is Safety Culture?</h2>
<p>Safety culture refers to the shared values, attitudes, perceptions, and behaviours that characterise how an organisation approaches health and safety. It is "the way we do things around here" when it comes to safety.</p>

<h2>Signs of a Positive Safety Culture</h2>
<ul>
<li>Safety is a visible priority at all levels</li>
<li>Workers feel comfortable reporting concerns</li>
<li>Near misses are reported and investigated</li>
<li>People intervene when they see unsafe behaviour</li>
<li>Managers lead by example</li>
<li>Training is valued and invested in</li>
<li>Good safety performance is recognised</li>
<li>Continuous improvement is the norm</li>
</ul>

<h2>Signs of a Negative Safety Culture</h2>
<ul>
<li>Safety is seen as a box-ticking exercise</li>
<li>Production pressure overrides safety concerns</li>
<li>Workers fear reporting incidents or concerns</li>
<li>Blame is the default response to accidents</li>
<li>Rules are routinely ignored or worked around</li>
<li>PPE is not worn consistently</li>
<li>"We have always done it this way" mentality</li>
</ul>

<h2>Building a Positive Safety Culture</h2>

<h3>Leadership Commitment</h3>
<p>Safety culture starts at the top. Leaders must:</p>
<ul>
<li>Make safety a genuine priority, not just words</li>
<li>Allocate adequate resources</li>
<li>Be visible on the shop floor</li>
<li>Follow the same rules as everyone else</li>
<li>Respond constructively to concerns</li>
</ul>

<h3>Worker Engagement</h3>
<p>Involve workers in health and safety through:</p>
<ul>
<li>Safety committees and representatives</li>
<li>Consultation on changes affecting safety</li>
<li>Encouraging suggestions and feedback</li>
<li>Recognising good safety behaviour</li>
<li>Involving them in risk assessments</li>
</ul>

<h3>Communication</h3>
<p>Effective communication includes:</p>
<ul>
<li>Clear safety messages from leadership</li>
<li>Regular toolbox talks and briefings</li>
<li>Sharing lessons from incidents and near misses</li>
<li>Open door policies for raising concerns</li>
<li>Two-way feedback mechanisms</li>
</ul>

<h3>Learning Organisation</h3>
<p>A positive safety culture embraces learning:</p>
<ul>
<li>Incidents are investigated without blame</li>
<li>Near misses are valued as learning opportunities</li>
<li>External incidents and best practices are shared</li>
<li>Training is ongoing, not one-off</li>
<li>Performance is measured and reviewed</li>
</ul>

<h2>Measuring Safety Culture</h2>
<p>Indicators of safety culture include:</p>
<ul>
<li>Near miss reporting rates (higher is better)</li>
<li>Safety observation completion rates</li>
<li>Employee perception surveys</li>
<li>Safety conversation frequency</li>
<li>Training completion and competence</li>
<li>Audit scores and trends</li>
</ul>

<h2>The Business Case</h2>
<p>Organisations with strong safety cultures typically see:</p>
<ul>
<li>Fewer accidents and incidents</li>
<li>Lower absence rates</li>
<li>Better staff retention</li>
<li>Improved productivity and quality</li>
<li>Enhanced reputation</li>
<li>Reduced insurance and legal costs</li>
</ul>

<p>Integral Safety can help you assess your current safety culture and develop practical strategies for improvement. From leadership workshops to employee engagement programmes, we support organisations on their journey to safety excellence.</p>'
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
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Blog Article Seeder (Part 2 of 2)</h1>

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

        <div class="mt-6">
            <a href="/admin/blog.php" class="inline-block bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                Go to Blog Management
            </a>
        </div>
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-semibold text-blue-900 mb-2">All Done!</h3>
        <p class="text-blue-800 text-sm">You now have 12 articles scheduled over the next 6 weeks. They will automatically publish on their scheduled dates. You can edit, reschedule, or add featured images from the Blog Management page.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
