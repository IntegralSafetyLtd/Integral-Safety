import { config as dotenvConfig } from 'dotenv'
// Load env vars FIRST, before any other imports
dotenvConfig({ path: '.env.local' })

const competentPersonContent = {
  root: {
    type: 'root',
    children: [
      // Opening paragraph
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Every employer in the UK has a legal duty to ensure the health and safety of their employees and others who may be affected by their work activities. A key part of meeting this obligation is having access to "competent" health and safety assistance. But what exactly is a competent person, and why does your business need one?',
          },
        ],
      },
      // Key takeaways box
      {
        type: 'key-takeaways',
        title: 'Key Takeaways',
        items: [
          'UK law requires employers to appoint one or more competent persons to assist with health and safety',
          'A competent person must have sufficient training, experience, knowledge, and other qualities',
          'In-house competent persons should be appointed before external consultants where possible',
          'The level of competence required depends on the complexity and risks of your business',
          'Failing to appoint competent assistance can result in prosecution and significant fines',
        ],
      },
      // Stats box
      {
        type: 'stats-box',
        stats: [
          { value: '1974', label: 'HSWA Enacted' },
          { value: 'Reg 7', label: 'MHSWR Key Regulation' },
          { value: '£20k+', label: 'Typical Fine' },
          { value: '100%', label: 'Of Employers Need One' },
        ],
      },
      // What is section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'What is a Competent Person?' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'In health and safety terms, a "competent person" is someone who has the necessary skills, knowledge, experience, and training to carry out a specific task safely and effectively. The term appears throughout UK health and safety legislation, but importantly, it is not rigidly defined — what constitutes competence depends entirely on the task at hand.',
          },
        ],
      },
      // Image placeholder
      {
        type: 'image-placeholder',
        caption: 'Health and safety consultant reviewing workplace documentation with management',
        icon: 'shield',
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'The Health and Safety Executive (HSE) describes a competent person as someone who has "sufficient training and experience or knowledge and other qualities that allow them to assist you properly." This deliberately flexible definition recognises that the competence needed to advise a small office differs vastly from that required for a high-hazard industrial facility.',
          },
        ],
      },
      // Pull quote
      {
        type: 'blockquote',
        children: [
          {
            type: 'text',
            text: 'Competence is not just about qualifications — it\'s about having the right combination of skills, knowledge, and experience to get the job done safely.',
          },
        ],
      },
      // Legal Framework section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'The Legal Requirement' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'The requirement to appoint competent assistance comes from Regulation 7 of the Management of Health and Safety at Work Regulations 1999 (MHSWR). This regulation states that every employer shall appoint one or more competent persons to assist in undertaking the measures needed to comply with health and safety law.',
          },
        ],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'The regulation specifies that where there is a competent person in the employer\'s employment, that person shall be appointed in preference to a competent person not in their employment. In other words, you should look internally first before hiring external consultants.',
          },
        ],
      },
      // Info callout
      {
        type: 'callout',
        variant: 'info',
        title: 'Regulation 7 Requirements',
        children: [
          {
            type: 'text',
            text: 'The appointed competent person(s) must be given adequate time, facilities, and other resources to fulfil their health and safety duties. They must also have sufficient independence from management to be able to give objective advice.',
          },
        ],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'The broader duty derives from Section 2 of the Health and Safety at Work etc. Act 1974, which requires employers to ensure, so far as is reasonably practicable, the health, safety, and welfare at work of all their employees. Having access to competent advice is fundamental to meeting this duty.',
          },
        ],
      },
      // Warning callout
      {
        type: 'callout',
        variant: 'warning',
        title: 'Self-Employment Exception',
        children: [
          {
            type: 'text',
            text: 'If you are a self-employed person and you are competent to carry out the measures yourself, you do not need to appoint anyone else. However, you must be genuinely competent — overestimating your own abilities could have serious consequences.',
          },
        ],
      },
      // Why Need One section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'Why Does Your Business Need a Competent Person?' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Beyond the legal requirement, there are compelling practical reasons why every business benefits from having access to competent health and safety assistance:',
          },
        ],
      },
      // Image placeholder
      {
        type: 'image-placeholder',
        caption: 'Team meeting discussing workplace safety procedures and risk assessments',
        icon: 'users',
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: '1. Identifying and Managing Risks' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'A competent person can help you identify hazards that might not be obvious to those immersed in day-to-day operations. They bring fresh eyes and specialist knowledge to spot risks that others might overlook, and they can help you implement proportionate control measures.',
          },
        ],
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: '2. Legal Compliance' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Health and safety law is complex and constantly evolving. A competent person keeps up to date with legislative changes and can ensure your business remains compliant. They can translate legal requirements into practical actions relevant to your specific circumstances.',
          },
        ],
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: '3. Risk Assessments' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Employers must carry out suitable and sufficient risk assessments. A competent person can conduct or review these assessments to ensure they meet legal requirements and genuinely protect people from harm. Poor risk assessments are one of the most common failings identified by HSE inspectors.',
          },
        ],
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: '4. Policy and Procedure Development' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'A competent person can help develop clear, practical health and safety policies and procedures tailored to your business. Generic off-the-shelf documents rarely meet your specific needs and may not adequately address your actual risks.',
          },
        ],
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: '5. Training and Awareness' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Competent persons can identify training needs and either deliver training directly or advise on appropriate courses. They help create a positive safety culture where everyone understands their responsibilities.',
          },
        ],
      },
      // Tip callout
      {
        type: 'callout',
        variant: 'tip',
        title: 'Cost-Effective Safety',
        children: [
          {
            type: 'text',
            text: 'Investing in competent health and safety assistance typically saves money in the long run by preventing accidents, reducing sickness absence, avoiding prosecution, and improving productivity. The cost of getting it wrong far exceeds the cost of getting it right.',
          },
        ],
      },
      // Divider
      { type: 'divider' },
      // In-House vs External section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'In-House vs External Competent Person' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'As mentioned, the law prefers internal appointments where possible. However, many businesses — particularly small and medium-sized enterprises — choose to use external health and safety consultants. Both approaches have advantages:',
          },
        ],
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'In-House Competent Person' }],
      },
      {
        type: 'list',
        listType: 'bullet',
        children: [
          { type: 'listitem', children: [{ type: 'text', text: 'Detailed knowledge of your specific operations and workplace' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Immediately available when issues arise' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Can build relationships with staff and integrate safety into daily operations' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'May be more cost-effective for larger organisations' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Requires ongoing investment in training and professional development' }] },
        ],
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'External Consultant' }],
      },
      {
        type: 'list',
        listType: 'bullet',
        children: [
          { type: 'listitem', children: [{ type: 'text', text: 'Brings broad experience from working with multiple organisations' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Independent and objective perspective' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Up-to-date specialist knowledge without your training investment' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Scalable — use as much or as little support as needed' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Often more practical for small businesses' }] },
        ],
      },
      // Image placeholder
      {
        type: 'image-placeholder',
        caption: 'External consultant conducting a workplace safety inspection',
        icon: 'shield',
      },
      // Info callout
      {
        type: 'callout',
        variant: 'info',
        title: 'Combined Approach',
        children: [
          {
            type: 'text',
            text: 'Many organisations use a combination of both — an internal person handling day-to-day safety matters with external consultant support for specialist issues, complex projects, or independent auditing.',
          },
        ],
      },
      // What Makes Someone Competent section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'What Makes Someone Competent?' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Competence is typically built from four key components, often summarised as SKEP:',
          },
        ],
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'Skills' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'The practical ability to apply knowledge effectively. This includes skills in communication, investigation, risk assessment, and problem-solving. Skills are developed through practice and experience.',
          },
        ],
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'Knowledge' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Understanding of relevant health and safety principles, legal requirements, and best practices. This is often gained through formal training and qualifications such as NEBOSH, IOSH, or NVQ/SVQ qualifications.',
          },
        ],
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'Experience' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Practical experience of applying health and safety principles in real workplace situations. There is no substitute for having dealt with actual challenges and learned from them.',
          },
        ],
      },
      {
        type: 'heading',
        tag: 'h3',
        children: [{ type: 'text', text: 'Personal Qualities' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Attributes such as integrity, objectivity, assertiveness, and good communication. A competent person must be willing to give honest advice even when it\'s not what management wants to hear.',
          },
        ],
      },
      // Success callout
      {
        type: 'callout',
        variant: 'success',
        title: 'Professional Accreditation',
        children: [
          {
            type: 'text',
            text: 'While not legally required, membership of a professional body such as IOSH (Institution of Occupational Safety and Health) provides assurance of competence and commitment to continuing professional development.',
          },
        ],
      },
      // Consequences section
      {
        type: 'heading',
        tag: 'h2',
        children: [{ type: 'text', text: 'Consequences of Not Having Competent Assistance' }],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Failing to appoint competent health and safety assistance can have serious consequences for your business:',
          },
        ],
      },
      {
        type: 'list',
        listType: 'bullet',
        children: [
          { type: 'listitem', children: [{ type: 'text', text: 'Prosecution for breach of Regulation 7 of MHSWR' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Unlimited fines for organisations' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Personal liability for directors and managers' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Increased likelihood of workplace accidents and ill-health' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Civil claims for compensation from injured parties' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Reputational damage and loss of business' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Insurance implications — policies may be invalidated' }] },
        ],
      },
      // Warning callout
      {
        type: 'callout',
        variant: 'warning',
        title: 'Aggravating Factor',
        children: [
          {
            type: 'text',
            text: 'When sentencing for health and safety offences, courts consider failure to seek or follow competent advice as an aggravating factor that increases the level of fine. Conversely, having proper competent assistance in place can be a mitigating factor.',
          },
        ],
      },
      // Image placeholder
      {
        type: 'image-placeholder',
        caption: 'Court case documents relating to health and safety prosecution',
        icon: 'document',
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
            text: 'At Integral Safety, we provide competent person services to businesses across Leicestershire and the wider Midlands. Our experienced consultants have the qualifications, knowledge, and practical experience to help you meet your legal obligations and protect your people.',
          },
        ],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'We offer flexible arrangements to suit your needs:',
          },
        ],
      },
      {
        type: 'list',
        listType: 'bullet',
        children: [
          { type: 'listitem', children: [{ type: 'text', text: 'Retained competent person services — ongoing support for a fixed monthly fee' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Ad-hoc consultancy — help when you need it without ongoing commitment' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Risk assessment services — comprehensive assessments for all workplace hazards' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Policy development — practical, tailored health and safety documentation' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Training delivery — IOSH courses and bespoke training programmes' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Audit and inspection services — independent review of your arrangements' }] },
          { type: 'listitem', children: [{ type: 'text', text: 'Incident investigation — thorough investigation with practical recommendations' }] },
        ],
      },
      {
        type: 'paragraph',
        children: [
          {
            type: 'text',
            text: 'Whether you need comprehensive retained support or just occasional guidance on specific issues, we can help. Contact us today to discuss how we can support your business as your competent person for health and safety.',
          },
        ],
      },
      // FAQ section
      {
        type: 'faq',
        items: [
          {
            question: 'Do all businesses need a competent person?',
            answer: 'Yes. Every employer must appoint one or more competent persons to assist with health and safety, regardless of the size or type of business. The only exception is self-employed individuals who are genuinely competent to handle their own health and safety.',
          },
          {
            question: 'What qualifications does a competent person need?',
            answer: 'There are no specific mandatory qualifications. Competence is judged on the combination of training, experience, knowledge, and personal qualities appropriate for the tasks involved. For general health and safety advice, qualifications like NEBOSH Certificate or IOSH membership are commonly expected.',
          },
          {
            question: 'Can I be my own competent person?',
            answer: 'Potentially, yes. In a very small, low-risk business, the employer may have sufficient knowledge to act as their own competent person for basic matters. However, you must be honest about your limitations and seek external help for anything beyond your genuine competence.',
          },
          {
            question: 'How much does a competent person cost?',
            answer: 'Costs vary widely depending on the level of support needed. External consultants typically charge either a day rate or a fixed monthly retainer. For small businesses, retained services might start from a few hundred pounds per month, while larger or higher-risk businesses will need more comprehensive support.',
          },
          {
            question: 'Can I use multiple competent persons?',
            answer: 'Yes, and this is often sensible. You might have an internal person for day-to-day matters and use external specialists for specific hazards like fire safety, asbestos, or occupational health. The key is ensuring clear responsibilities and good communication between them.',
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

async function seedCompetentPerson() {
  console.log('Starting Competent Person blog seed...')
  console.log('DATABASE_URI:', process.env.DATABASE_URI ? 'Set' : 'Not set')

  // Dynamic import to ensure env vars are loaded first
  const { getPayload } = await import('payload')
  const { default: config } = await import('../payload/payload.config')

  const payload = await getPayload({ config })

  // Delete existing post if it exists
  const existingPosts = await payload.find({
    collection: 'posts',
    where: {
      slug: { equals: 'competent-person-why-your-business-needs-one' },
    },
  })

  if (existingPosts.docs.length > 0) {
    console.log('Deleting existing Competent Person post...')
    await payload.delete({
      collection: 'posts',
      id: existingPosts.docs[0].id,
    })
    console.log('Existing post deleted.')
  }

  // Create the Competent Person blog post
  const post = await payload.create({
    collection: 'posts',
    data: {
      title: 'Competent Person: Why Your Business Needs One',
      slug: 'competent-person-why-your-business-needs-one',
      status: 'published',
      publishedAt: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString(), // 7 days ago
      excerpt:
        'UK law requires every employer to appoint a competent person to assist with health and safety. Learn what competence means, the legal requirements under Regulation 7 of MHSWR, and how having the right expertise protects your business and your people.',
      content: competentPersonContent,
    },
  })

  console.log('Created blog post:', post.title)
  console.log('Blog seed complete!')

  process.exit(0)
}

seedCompetentPerson().catch((err) => {
  console.error('Error seeding blog:', err)
  process.exit(1)
})
