import { config as dotenvConfig } from 'dotenv'
// Load env vars FIRST, before any other imports
dotenvConfig({ path: '.env.local' })

const fireRiskAssessmentContent = {
  root: {
    type: 'root',
    children: [
      // Opening paragraph
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Fire risk assessments are a legal requirement for virtually all premises in England and Wales. Under the Regulatory Reform (Fire Safety) Order 2005, the "responsible person" for any non-domestic premises must carry out a fire risk assessment and implement appropriate fire safety measures. But what exactly does this mean for your business, and how can you ensure compliance?',
          },
        ],
      },
      // Key takeaways box
      {
        type: 'key-takeaways',
        title: 'Key Takeaways',
        items: [
          'Fire risk assessments are legally required for all non-domestic premises in England and Wales',
          'The "responsible person" (usually the employer or building owner) must conduct and maintain the assessment',
          'Assessments must be reviewed regularly and updated when circumstances change',
          'Non-compliance can result in unlimited fines and up to 2 years imprisonment',
          'Professional assessors can help ensure thorough, compliant assessments',
        ],
      },
      // Stats box
      {
        type: 'stats-box',
        stats: [
          { value: '2005', label: 'RRO Enacted' },
          { value: '5', label: 'Key Steps' },
          { value: '£∞', label: 'Max Fine' },
          { value: '2 yrs', label: 'Max Prison' },
        ],
      },
      // What is section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'What is a Fire Risk Assessment?' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'A fire risk assessment is a systematic evaluation of your premises designed to identify potential fire hazards, assess who might be at risk, and determine the measures needed to reduce or eliminate the risk of fire. Crucially, it also ensures that people can safely escape if a fire does occur.',
          },
        ],
      },
      // Image placeholder
      {
        type: 'image-placeholder',
        caption: 'Professional fire risk assessor conducting a thorough premises evaluation',
        icon: 'shield',
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'A proper fire risk assessment goes far beyond a simple checklist. It requires a detailed understanding of your premises, the activities that take place there, the materials stored or used, and the people who occupy or visit the building. The assessment should be a living document that evolves as your business changes.',
          },
        ],
      },
      // Pull quote
      {
        type: 'blockquote',
        children: [
          {
            type: 'text',
            text: 'A fire risk assessment isn\'t just a tick-box exercise — it\'s a crucial process that could save lives and protect your business from devastating losses.',
          },
        ],
      },
      // Legal Framework section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'The Legal Framework' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'The Regulatory Reform (Fire Safety) Order 2005 (commonly known as the RRO or FSO) replaced over 70 pieces of fire safety law with a single, risk-based approach. It applies to all non-domestic premises in England and Wales, including:',
          },
        ],
      },
      {
        type: 'list',
        listType: 'bullet',
        children: [
          { type: 'listitem', children: [{ type: 'text', text: 'Offices, shops, and retail premises' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Factories, warehouses, and industrial units' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Pubs, clubs, restaurants, and hotels' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Care homes, hospitals, and healthcare facilities' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Schools, colleges, and universities' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Common areas of residential buildings (flats, HMOs)' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Churches, village halls, and community buildings' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Construction sites and temporary structures' }] },
        ],
      },
      // Warning callout
      {
        type: 'callout',
        variant: 'warning',
        title: 'Important Change',
        children: [
          {
            type: 'text',
            text: 'Following the Grenfell Tower tragedy, the Fire Safety Act 2021 clarified that the RRO applies to the structure, external walls, and flat entrance doors of multi-occupied residential buildings. Building owners and managers must now include these elements in their fire risk assessments.',
          },
        ],
      },
      // Responsible Person section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'Who is the "Responsible Person"?' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'The Fire Safety Order places duties on the "responsible person" — this is typically:',
          },
        ],
      },
      {
        type: 'list',
        listType: 'bullet',
        children: [
          { type: 'listitem', children: [{ type: 'text', text: 'The employer, if the workplace is under their control' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'The person with control of the premises (as occupier or otherwise)' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'The owner, where the person in control doesn\'t have control in connection with carrying on a trade, business, or undertaking' }] },
        ],
      },
      // Image placeholder
      {
        type: 'image-placeholder',
        caption: 'Business owners discussing fire safety responsibilities with their team',
        icon: 'users',
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'In practice, this is usually the business owner, employer, landlord, or managing agent. In complex buildings with multiple occupiers, there may be several responsible persons who need to coordinate their fire safety measures. Each responsible person must ensure that the parts of the premises they control are covered by an adequate fire risk assessment.',
          },
        ],
      },
      // Info callout
      {
        type: 'callout',
        variant: 'info',
        title: 'Multiple Responsible Persons',
        children: [
          {
            type: 'text',
            text: 'In shared buildings, multiple responsible persons must cooperate and coordinate their fire safety measures. This includes sharing relevant information about risks and ensuring that shared areas like corridors, stairwells, and car parks are properly covered.',
          },
        ],
      },
      // Five Steps section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'The Five Steps of Fire Risk Assessment' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'A compliant fire risk assessment follows the government\'s recommended five-step approach. Each step is crucial for ensuring a comprehensive evaluation of fire risks:',
          },
        ],
      },
      // Step 1
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'Step 1: Identify Fire Hazards' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'The first step involves identifying anything that could cause a fire to start. This means looking for three elements: sources of ignition (heat or sparks), sources of fuel (anything that burns), and sources of oxygen (usually the air around us, but also oxidising materials).',
          },
        ],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Common ignition sources include electrical equipment, heating systems, cooking facilities, smoking materials, candles, and hot work activities. Fuel sources encompass paper, cardboard, wood, textiles, plastics, flammable liquids, and even dust accumulation. Understanding how these might combine is essential.',
          },
        ],
      },
      // Step 2
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'Step 2: Identify People at Risk' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Consider everyone who might be affected by a fire in your premises. This includes employees, visitors, contractors, customers, and members of the public. Pay particular attention to people who may be especially vulnerable:',
          },
        ],
      },
      {
        type: 'list',
        listType: 'bullet',
        children: [
          { type: 'listitem', children: [{ type: 'text', text: 'People with mobility impairments or disabilities' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Elderly people or young children' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'People unfamiliar with the premises (visitors, new staff)' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'People working alone or in isolated areas' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'People who may be sleeping on the premises' }] },
        ],
      },
      // Image placeholder
      {
        type: 'image-placeholder',
        caption: 'Clear evacuation routes and assembly points ensure everyone can escape safely',
        icon: 'users',
      },
      // Step 3
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'Step 3: Evaluate, Remove, Reduce, and Protect' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Having identified the hazards and people at risk, evaluate the likelihood of a fire occurring and its potential consequences. Then take action to remove or reduce hazards where possible, and protect people from remaining risks through appropriate fire safety measures.',
          },
        ],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Protection measures include fire detection and warning systems, firefighting equipment (extinguishers, fire blankets), emergency lighting, clear escape routes, fire doors, and proper signage. The level of protection should be proportionate to the level of risk.',
          },
        ],
      },
      // Step 4
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'Step 4: Record, Plan, Inform, Instruct, and Train' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Your findings must be recorded — this is a legal requirement if you have 5 or more employees, but good practice regardless of size. Prepare a clear emergency plan that explains what to do in case of fire, including evacuation procedures and assembly points.',
          },
        ],
      },
      // Tip callout
      {
        type: 'callout',
        variant: 'tip',
        title: 'Training Requirement',
        children: [
          {
            type: 'text',
            text: 'All employees should receive fire safety training appropriate to their role. This should include what to do on discovering a fire, how to raise the alarm, the evacuation procedure, and the location and use of firefighting equipment. Training should be repeated regularly and when circumstances change.',
          },
        ],
      },
      // Step 5
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'Step 5: Review and Update Regularly' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Fire risk assessments should be reviewed regularly to ensure they remain current and effective. You should also review and update your assessment whenever there are significant changes to your premises, activities, or the people who use them.',
          },
        ],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Changes that should trigger a review include building alterations or refurbishment, changes in use of the premises, introduction of new equipment or processes, changes in the number or characteristics of occupants, and any fire incidents or near-misses.',
          },
        ],
      },
      // Divider
      { type: 'divider' },
      // Who Can Carry Out section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'Who Can Carry Out a Fire Risk Assessment?' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'The responsible person can carry out the fire risk assessment themselves if they have sufficient training, knowledge, and experience. However, for most premises — particularly those with complex layouts, high-risk activities, or vulnerable occupants — it\'s advisable to engage a competent fire risk assessor.',
          },
        ],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'A competent fire risk assessor should have:',
          },
        ],
      },
      {
        type: 'list',
        listType: 'bullet',
        children: [
          { type: 'listitem', children: [{ type: 'text', text: 'Adequate fire safety training and relevant qualifications' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Practical experience of fire risk assessment' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Thorough knowledge of fire safety legislation' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Understanding of fire science and fire protection measures' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'The ability to identify hazards and assess risks objectively' }] },
        ],
      },
      // Image placeholder
      {
        type: 'image-placeholder',
        caption: 'Our consultants hold industry-recognised qualifications including NEBOSH Fire Certificate',
        icon: 'document',
      },
      // Success callout
      {
        type: 'callout',
        variant: 'success',
        title: 'PAS 79-2 Compliance',
        children: [
          {
            type: 'text',
            text: 'At Integral Safety, all our fire risk assessments comply with PAS 79-2:2020 — the British Standard for fire risk assessment methodology. This ensures a consistent, thorough approach that meets the expectations of enforcing authorities.',
          },
        ],
      },
      // Penalties section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'Penalties for Non-Compliance' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Failure to comply with fire safety legislation can result in serious consequences. The Fire & Rescue Service has powers to issue:',
          },
        ],
      },
      {
        type: 'list',
        listType: 'bullet',
        children: [
          { type: 'listitem', children: [{ type: 'text', text: 'Informal notifications requiring improvements' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Enforcement notices requiring you to make changes by a specific date' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Prohibition notices preventing use of all or part of the premises immediately' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Prosecution leading to unlimited fines' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Imprisonment for up to 2 years for serious breaches' }] },
        ],
      },
      // Warning callout
      {
        type: 'callout',
        variant: 'warning',
        title: 'Increased Scrutiny',
        children: [
          {
            type: 'text',
            text: 'Following the Grenfell Tower tragedy, there has been significantly increased scrutiny of fire safety compliance. The Fire Safety Act 2021 and Building Safety Act 2022 have introduced additional requirements, and enforcement activity has intensified. Responsible persons should ensure their fire risk assessments are up to date and comprehensive.',
          },
        ],
      },
      // How We Can Help section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'How Integral Safety Can Help' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'At Integral Safety, our experienced fire safety consultants carry out comprehensive fire risk assessments for businesses across Leicestershire and the wider Midlands region. We bring decades of combined experience in fire safety, helping organisations of all sizes meet their legal obligations.',
          },
        ],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Our fire risk assessment service includes:',
          },
        ],
      },
      {
        type: 'list',
        listType: 'bullet',
        children: [
          { type: 'listitem', children: [{ type: 'text', text: 'Thorough, PAS 79-2 compliant assessments' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Clear, practical recommendations prioritised by risk' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Detailed written reports suitable for audit purposes' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Support with implementing recommended improvements' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Ongoing review and update services' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Fire safety training for your staff' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Emergency plan development and testing' }] },
        ],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Don\'t wait for an incident or enforcement action — contact us today to arrange your fire risk assessment and ensure your premises and people are properly protected.',
          },
        ],
      },
      // FAQ section
      {
        type: 'faq',
        items: [
          {
            question: 'How often should a fire risk assessment be reviewed?',
            answer: 'There is no fixed legal timeframe, but best practice suggests reviewing your fire risk assessment at least annually. However, you should also review it immediately after any significant changes to your premises, activities, or occupants, or following any fire incidents or near-misses.',
          },
          {
            question: 'Do I need a fire risk assessment for a small business?',
            answer: 'Yes. The Fire Safety Order applies to all non-domestic premises regardless of size. Even small businesses with just one or two employees need a fire risk assessment. While you don\'t need to record it if you have fewer than 5 employees, doing so is still good practice.',
          },
          {
            question: 'Can I do my own fire risk assessment?',
            answer: 'Yes, the responsible person can carry out their own assessment if they have sufficient competence. However, if your premises have complex layouts, high-risk activities, or vulnerable occupants, engaging a professional fire risk assessor is strongly recommended to ensure nothing is missed.',
          },
          {
            question: 'What happens if the Fire Service inspects my premises?',
            answer: 'Fire Safety Officers have powers to inspect any premises covered by the Fire Safety Order. They will want to see your fire risk assessment, evidence that recommendations have been actioned, maintenance records for fire safety equipment, and evidence of staff training. If deficiencies are found, they may issue informal advice, formal notices, or in serious cases, prosecute.',
          },
          {
            question: 'How much does a professional fire risk assessment cost?',
            answer: 'The cost varies depending on the size and complexity of your premises. Simple, single-storey premises might start from a few hundred pounds, while large or complex buildings will cost more. Contact us for a free, no-obligation quote tailored to your specific premises.',
          },
        ],
      },
    ],
    direction: 'ltr',
    format: '',
    indent: 0,
    version: 1,
  },
}

async function seedBlog() {
  console.log('Starting blog seed...')
  console.log('DATABASE_URI:', process.env.DATABASE_URI ? 'Set' : 'Not set')

  // Dynamic import to ensure env vars are loaded first
  const { getPayload } = await import('payload')
  const { default: config } = await import('../payload/payload.config')

  const payload = await getPayload({ config })

  // Delete existing post if it exists
  const existingPosts = await payload.find({
    collection: 'posts',
    where: {
      slug: { equals: 'fire-risk-assessments-legal-requirements' },
    },
  })

  if (existingPosts.docs.length > 0) {
    console.log('Deleting existing Fire Risk Assessment post...')
    await payload.delete({
      collection: 'posts',
      id: existingPosts.docs[0].id,
    })
    console.log('Existing post deleted.')
  }

  // Create the Fire Risk Assessment blog post
  const post = await payload.create({
    collection: 'posts',
    data: {
      title: 'Fire Risk Assessments: Understanding Your Legal Obligations',
      slug: 'fire-risk-assessments-legal-requirements',
      status: 'published',
      publishedAt: new Date().toISOString(),
      excerpt:
        'Fire risk assessments are a legal requirement for virtually all non-domestic premises in England and Wales. Learn about the Regulatory Reform (Fire Safety) Order 2005, who is responsible, the five-step assessment process, and what happens if you don\'t comply.',
      content: fireRiskAssessmentContent,
    },
  })

  console.log('Created blog post:', post.title)
  console.log('Blog seed complete!')

  process.exit(0)
}

seedBlog().catch((err) => {
  console.error('Error seeding blog:', err)
  process.exit(1)
})
