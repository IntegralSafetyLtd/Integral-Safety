import { Metadata } from 'next'
import Link from 'next/link'
import { notFound } from 'next/navigation'
import { Clock, Monitor, Award, Users, Check, HelpCircle, BookOpen, Target, Building2 } from 'lucide-react'
import { CTA } from '@/components/sections'
import { CourseSchema, FAQSchema, BreadcrumbSchema } from '@/components/schema'

const coursesData: Record<string, {
  metaTitle: string
  metaDescription: string
  title: string
  duration: string
  delivery: string
  accreditation: string | null
  heroSubheading: string
  overview: string[]
  modules: { title: string; description: string }[]
  learningOutcomes: string[]
  whoShouldAttend: string
  prerequisites: string | null
  certification: string
  faqs: { question: string; answer: string }[]
  relatedCourses: string[]
}> = {
  'iosh-managing-safely': {
    metaTitle: 'IOSH Managing Safely Course | Leicestershire | Integral Safety',
    metaDescription: 'IOSH Managing Safely training in Leicestershire. 3-4 day accredited course for managers and supervisors. In-person or online delivery. Book your place today.',
    title: 'IOSH Managing Safely',
    duration: '3-4 days',
    delivery: 'In-person or Online',
    accreditation: 'IOSH',
    heroSubheading: 'The essential health and safety qualification for managers and supervisors. Gain internationally recognised certification in just 3-4 days.',
    overview: [
      'IOSH Managing Safely is the world\'s most popular health and safety training course for managers, with over 130,000 people completing it every year. Designed by the Institution of Occupational Safety and Health (IOSH), this course gives managers the knowledge and skills they need to manage health and safety effectively within their teams.',
      'Unlike many health and safety courses, IOSH Managing Safely focuses on practical application rather than theory. Your managers will learn how to conduct risk assessments, identify common workplace hazards, investigate incidents, and understand their legal responsibilities - all with a focus on what actually works in the real world.',
      'As an IOSH Approved Training Provider, Integral Safety delivers this course through experienced trainers who understand the practical challenges of managing health and safety across different industries. We bring real-world examples and case studies that make the content relevant and engaging.',
    ],
    modules: [
      { title: 'Module 1: Introducing Managing Safely', description: 'Understanding the importance of health and safety management and the moral, legal, and financial reasons for getting it right.' },
      { title: 'Module 2: Assessing Risks', description: 'How to identify hazards, assess risks, and implement effective controls. Includes practical risk assessment exercises.' },
      { title: 'Module 3: Controlling Risks', description: 'Understanding the hierarchy of controls and how to select appropriate risk control measures.' },
      { title: 'Module 4: Understanding Responsibilities', description: 'Legal framework for health and safety, including employer and employee duties under UK law.' },
      { title: 'Module 5: Understanding Hazards', description: 'Common workplace hazards including slips and trips, manual handling, work at height, and more.' },
      { title: 'Module 6: Investigating Incidents', description: 'How to investigate accidents and near-misses to identify root causes and prevent recurrence.' },
      { title: 'Module 7: Measuring Performance', description: 'Leading and lagging indicators, and how to monitor and improve health and safety performance.' },
    ],
    learningOutcomes: [
      'Assess and control risks in your workplace',
      'Understand your legal health and safety responsibilities',
      'Identify common workplace hazards and how to manage them',
      'Investigate incidents effectively to prevent recurrence',
      'Measure and improve health and safety performance',
      'Implement practical safety management systems',
      'Communicate health and safety requirements to your team',
      'Understand the moral, legal, and financial case for safety',
    ],
    whoShouldAttend: 'Managers, supervisors, team leaders, and anyone with responsibility for others in the workplace. This course is suitable for all industries and sectors. No prior health and safety knowledge is required - the course starts from the basics and builds up.',
    prerequisites: 'No prior health and safety knowledge or qualifications required. Basic English literacy needed to complete the written assessment.',
    certification: 'On successful completion of the course assessment, delegates receive an IOSH Managing Safely certificate. This internationally recognised qualification is valid indefinitely, though IOSH recommends refresher training every three years to keep knowledge current.',
    faqs: [
      { question: 'How long is the IOSH Managing Safely course?', answer: 'The course typically runs over 3-4 days, depending on delivery format. The online version can be more flexible, allowing delegates to work at their own pace.' },
      { question: 'Is there an exam?', answer: 'Yes, the course includes a multiple-choice assessment and a practical risk assessment project. Both must be passed to achieve certification. The pass mark is achievable for anyone who engages with the course content.' },
      { question: 'Can the course be delivered at our premises?', answer: 'We can deliver IOSH Managing Safely at your workplace for groups of 6 or more delegates. This can be more cost-effective and allows us to use examples relevant to your industry.' },
      { question: 'How long is the certificate valid?', answer: 'The IOSH Managing Safely certificate does not expire. However, IOSH recommends refresher training every three years to keep knowledge up to date with changing legislation and best practices.' },
      { question: 'What is the difference between IOSH Managing Safely and NEBOSH?', answer: 'IOSH Managing Safely is a shorter, more practical course aimed at managers who need a working knowledge of health and safety. NEBOSH qualifications are more in-depth and typically aimed at those pursuing a career in health and safety. For most managers, IOSH Managing Safely provides all the knowledge they need.' },
    ],
    relatedCourses: ['manual-handling', 'fire-awareness', 'accident-investigation'],
  },
  'manual-handling': {
    metaTitle: 'Manual Handling Training Course | Leicestershire | Integral Safety',
    metaDescription: 'Manual handling training in Leicestershire. Half-day practical course teaching safe lifting techniques. Reduce workplace injuries and meet legal requirements.',
    title: 'Manual Handling Awareness',
    duration: 'Half day',
    delivery: 'In-person',
    accreditation: null,
    heroSubheading: 'Practical training that reduces manual handling injuries and keeps your workforce safe. Hands-on exercises using real workplace scenarios.',
    overview: [
      'Manual handling injuries remain one of the most common causes of workplace absence in the UK, accounting for over a third of all workplace injuries. These injuries are largely preventable with the right training and awareness.',
      'Our Manual Handling Awareness course goes beyond the basics of "bend your knees and keep your back straight". We teach delegates to understand why injuries occur, how to assess handling risks, and how to apply practical techniques that work in their specific work environment.',
      'The course is highly practical, with hands-on exercises that allow delegates to practice techniques using loads similar to those they handle at work. Our experienced trainers provide individual feedback and coaching to ensure everyone develops good habits.',
    ],
    modules: [
      { title: 'Understanding Manual Handling', description: 'What counts as manual handling, the types of injuries that can occur, and the scale of the problem in UK workplaces.' },
      { title: 'The Law and Your Responsibilities', description: 'Manual Handling Operations Regulations, employer duties, and what employees are required to do.' },
      { title: 'Anatomy and Injury Mechanisms', description: 'How the spine works, why injuries occur, and the long-term consequences of poor handling techniques.' },
      { title: 'Risk Assessment Principles', description: 'The TILE framework (Task, Individual, Load, Environment) for assessing manual handling risks.' },
      { title: 'Practical Handling Techniques', description: 'Hands-on practice of lifting, lowering, carrying, pushing, and pulling techniques with individual coaching.' },
    ],
    learningOutcomes: [
      'Understand the risks associated with poor manual handling',
      'Apply the TILE framework to assess handling risks',
      'Demonstrate safe lifting and handling techniques',
      'Recognise when to seek help or use mechanical aids',
      'Understand your legal responsibilities for manual handling',
      'Identify ways to reduce handling risks in your workplace',
    ],
    whoShouldAttend: 'Anyone whose work involves lifting, carrying, pushing, or pulling loads. Particularly suitable for warehouse and logistics staff, care workers, retail staff, manufacturing workers, construction workers, and office staff who handle deliveries or equipment.',
    prerequisites: 'None. Delegates should wear comfortable clothing and flat shoes suitable for practical exercises.',
    certification: 'All delegates receive a certificate of attendance valid for three years. We recommend refresher training every three years or when staff change roles to tasks with different handling requirements.',
    faqs: [
      { question: 'How many people can attend a manual handling course?', answer: 'For practical courses, we recommend a maximum of 12 delegates to ensure everyone gets adequate hands-on practice and individual feedback from the trainer.' },
      { question: 'Can you deliver the training at our site?', answer: 'Yes, on-site delivery is often preferable as we can use your actual equipment and loads, making the training directly relevant to your operations.' },
      { question: 'Do you provide refresher training?', answer: 'Yes, we offer shorter refresher sessions for staff who have previously completed manual handling training. These typically run for 2 hours.' },
      { question: 'Is manual handling training a legal requirement?', answer: 'The Manual Handling Operations Regulations 1992 require employers to provide training for employees who carry out manual handling tasks. The training must be appropriate to the risks and the individual tasks involved.' },
    ],
    relatedCourses: ['iosh-managing-safely', 'accident-investigation'],
  },
  'coshh-awareness': {
    metaTitle: 'COSHH Awareness Training Course | Leicestershire | Integral Safety',
    metaDescription: 'COSHH awareness training in Leicestershire. Half-day course covering hazardous substances regulations, safety data sheets, and control measures. Book now.',
    title: 'COSHH Awareness',
    duration: 'Half day',
    delivery: 'In-person or Online',
    accreditation: null,
    heroSubheading: 'Understanding the Control of Substances Hazardous to Health regulations. Learn to work safely with chemicals and protect yourself and others.',
    overview: [
      'The COSHH (Control of Substances Hazardous to Health) Regulations 2002 require employers to prevent or control exposure to hazardous substances. This course gives your employees the knowledge they need to understand these requirements and work safely with chemicals.',
      'Many workplaces use hazardous substances without fully appreciating the risks - from cleaning products and paints to industrial chemicals and biological agents. Our COSHH Awareness training helps staff recognise these hazards and use appropriate control measures.',
      'The course covers how to read and understand safety data sheets, interpret hazard symbols, use personal protective equipment correctly, and respond to spills and emergencies. Real workplace examples help delegates relate the content to their own situations.',
    ],
    modules: [
      { title: 'Introduction to COSHH', description: 'What COSHH covers, why it matters, and the legal framework for controlling hazardous substances.' },
      { title: 'Types of Hazardous Substances', description: 'Categories of hazardous substances, routes of entry into the body, and types of health effects.' },
      { title: 'Understanding Safety Data Sheets', description: 'How to find and interpret key information from safety data sheets (SDS), including hazard statements and precautionary statements.' },
      { title: 'Hazard Symbols and Classification', description: 'The GHS hazard pictograms and what they mean, plus understanding hazard and precautionary statements.' },
      { title: 'Control Measures and PPE', description: 'The hierarchy of control, engineering controls, safe working practices, and correct use of personal protective equipment.' },
      { title: 'Emergency Procedures', description: 'What to do in case of spills, exposure, or other emergencies involving hazardous substances.' },
    ],
    learningOutcomes: [
      'Understand the COSHH regulations and employer responsibilities',
      'Identify different types of hazardous substances',
      'Read and interpret safety data sheets',
      'Recognise GHS hazard symbols and understand their meaning',
      'Select and use appropriate PPE correctly',
      'Follow safe working procedures when using hazardous substances',
      'Respond appropriately to spills and emergencies',
    ],
    whoShouldAttend: 'Anyone who works with or around chemicals and hazardous substances. Particularly suitable for cleaners, laboratory staff, manufacturing workers, painters and decorators, hairdressers, healthcare workers, and maintenance staff.',
    prerequisites: 'None required.',
    certification: 'All delegates receive a certificate of attendance valid for three years.',
    faqs: [
      { question: 'What substances does COSHH cover?', answer: 'COSHH covers most hazardous substances including chemicals, fumes, dusts, vapours, mists, biological agents, and certain types of germs. It does not cover asbestos, lead, or radioactive substances which have their own specific regulations.' },
      { question: 'Is online delivery available?', answer: 'Yes, COSHH Awareness can be delivered online via video conferencing. This option is suitable for smaller groups or when gathering delegates in one location is impractical.' },
      { question: 'Do we need COSHH assessments as well as training?', answer: 'Yes, COSHH training is separate from the requirement to conduct COSHH risk assessments. We can also help you develop COSHH assessments for your workplace if needed.' },
    ],
    relatedCourses: ['iosh-managing-safely', 'fire-awareness'],
  },
  'fire-awareness': {
    metaTitle: 'Fire Awareness Training Course | Leicestershire | Integral Safety',
    metaDescription: 'Fire awareness training in Leicestershire. Essential fire safety training for all employees. Includes practical fire extinguisher use. 2-3 hour course.',
    title: 'Fire Awareness',
    duration: '2-3 hours',
    delivery: 'In-person',
    accreditation: null,
    heroSubheading: 'Essential fire safety training for all employees. Understand fire prevention, emergency procedures, and gain confidence using fire extinguishers.',
    overview: [
      'Fire can spread with terrifying speed - in just three minutes, a small fire can engulf a room. Every employee needs to understand fire safety basics: how fires start, how to prevent them, and what to do if a fire occurs.',
      'Our Fire Awareness training covers all the essential elements of fire safety, from identifying fire hazards to understanding evacuation procedures. The course includes practical fire extinguisher training so your staff gain confidence and know how to respond in an emergency.',
      'The training is tailored to your workplace, incorporating your fire safety arrangements, evacuation procedures, and specific fire risks. This ensures the content is directly relevant and immediately applicable.',
    ],
    modules: [
      { title: 'Fire Science Basics', description: 'The fire triangle, how fires start and spread, and the factors that affect fire development.' },
      { title: 'Common Fire Hazards', description: 'Identifying fire hazards in the workplace, including electrical, heating, and storage risks.' },
      { title: 'Fire Prevention', description: 'Good housekeeping, electrical safety, hot work controls, and other fire prevention measures.' },
      { title: 'What to Do If You Discover a Fire', description: 'Raising the alarm, alerting others, and when to attempt to fight a fire.' },
      { title: 'Evacuation Procedures', description: 'Understanding your workplace fire plan, evacuation routes, assembly points, and roles and responsibilities.' },
      { title: 'Fire Extinguishers', description: 'Types of fire extinguishers, which to use on different fire types, and practical hands-on training.' },
    ],
    learningOutcomes: [
      'Understand how fires start and spread',
      'Identify fire hazards in the workplace',
      'Know the actions to take on discovering a fire',
      'Understand evacuation procedures and your role',
      'Select the correct type of fire extinguisher',
      'Use fire extinguishers safely and effectively',
      'Understand fire prevention best practices',
    ],
    whoShouldAttend: 'All employees. Fire awareness training is a legal requirement and should be included in induction for new starters. The Regulatory Reform (Fire Safety) Order 2005 requires employers to provide adequate fire safety training.',
    prerequisites: 'None.',
    certification: 'All delegates receive a certificate of attendance. Fire training should be refreshed annually or when there are significant changes to fire arrangements.',
    faqs: [
      { question: 'Is fire awareness training a legal requirement?', answer: 'Yes. The Regulatory Reform (Fire Safety) Order 2005 requires the responsible person to ensure that employees receive adequate fire safety training when they start work and at regular intervals thereafter.' },
      { question: 'Does the course include practical fire extinguisher training?', answer: 'Yes, we provide hands-on training using real fire extinguishers. Delegates will practice using different types of extinguishers on simulated fires.' },
      { question: 'How often should fire training be repeated?', answer: 'Fire training should be refreshed at least annually. More frequent training may be needed for high-risk premises or when there are significant changes to fire procedures or building layout.' },
      { question: 'Can you tailor the training to our premises?', answer: 'Yes, we will incorporate your specific fire procedures, evacuation arrangements, and fire risks into the training to make it directly relevant to your workplace.' },
    ],
    relatedCourses: ['iosh-managing-safely', 'accident-investigation'],
  },
  'sharps-awareness': {
    metaTitle: 'Sharps & Needlestick Awareness Training | Leicestershire | Integral Safety',
    metaDescription: 'Sharps awareness training for housing officers, cleaners, and waste handlers. Learn safe handling, disposal procedures, and post-exposure response.',
    title: 'Sharps & Needlestick Awareness',
    duration: 'Half day',
    delivery: 'In-person',
    accreditation: null,
    heroSubheading: 'Essential training for anyone who may encounter discarded needles and sharps. Learn safe handling, correct disposal, and what to do if an injury occurs.',
    overview: [
      'Needlestick injuries can expose workers to serious blood-borne infections including Hepatitis B, Hepatitis C, and HIV. While the risk of transmission from a single needlestick is relatively low, the consequences can be devastating.',
      'This course is designed for people whose work may bring them into contact with discarded needles and sharps - housing officers, caretakers, cleaners, refuse collectors, and parks workers. We cover how to assess risks, handle sharps safely if required, and dispose of them correctly.',
      'Critically, we also cover what to do if a needlestick injury does occur. Quick, correct action in the moments following an injury can significantly reduce the risk of infection. Every delegate will leave knowing exactly what steps to take.',
    ],
    modules: [
      { title: 'Understanding the Risks', description: 'Blood-borne viruses, how infections are transmitted, and the real-world risks from needlestick injuries.' },
      { title: 'Legal Requirements', description: 'Health and Safety at Work Act, Management of Health and Safety Regulations, and specific sharps regulations.' },
      { title: 'Risk Assessment', description: 'Identifying where sharps may be found, assessing risks, and implementing controls.' },
      { title: 'Safe Handling and Disposal', description: 'If sharps must be handled, how to do so safely. Correct use of sharps containers and disposal procedures.' },
      { title: 'Post-Exposure Procedures', description: 'Immediate first aid, reporting requirements, medical assessment, and follow-up testing protocols.' },
    ],
    learningOutcomes: [
      'Understand the risks associated with needlestick injuries',
      'Know which blood-borne infections can be transmitted',
      'Assess sharps risks in your work environment',
      'Handle and dispose of sharps safely when required',
      'Take correct immediate action following a needlestick injury',
      'Understand reporting and medical follow-up requirements',
    ],
    whoShouldAttend: 'Housing officers and estate managers, caretakers and maintenance staff, cleaners and domestic staff, refuse collectors, parks and grounds workers, police officers, anyone who may encounter discarded needles in their work.',
    prerequisites: 'None.',
    certification: 'All delegates receive a certificate of attendance valid for three years.',
    faqs: [
      { question: 'Should we be handling sharps at all?', answer: 'Where possible, the preferred approach is to call in specialist contractors or local authority services to collect discarded sharps. However, this isn\'t always practical, and your staff need to know how to stay safe when sharps are encountered.' },
      { question: 'What equipment do we need for sharps disposal?', answer: 'You will need appropriate sharps containers (which meet BS 7320 standards), tongs or litter pickers for picking up sharps, and potentially protective gloves rated for sharps use.' },
      { question: 'What should we do if someone gets a needlestick injury?', answer: 'Encourage bleeding from the wound, wash with soap and water (don\'t scrub), dry and cover with a waterproof dressing, then seek immediate medical advice. Do not suck the wound. Report the incident and retain the sharp if safe to do so for testing.' },
    ],
    relatedCourses: ['manual-handling', 'coshh-awareness'],
  },
  'accident-investigation': {
    metaTitle: 'Accident Investigation Training Course | Leicestershire | Integral Safety',
    metaDescription: 'Accident investigation training in Leicestershire. One-day course covering evidence gathering, root cause analysis, and report writing. Book now.',
    title: 'Accident Investigation',
    duration: '1 day',
    delivery: 'In-person',
    accreditation: null,
    heroSubheading: 'Learn how to conduct thorough, effective accident investigations. Identify root causes and implement measures to prevent recurrence.',
    overview: [
      'When accidents occur, a thorough investigation is essential - not to assign blame, but to understand what went wrong and prevent it happening again. Poor investigations lead to superficial fixes that don\'t address underlying problems.',
      'This one-day course gives managers and supervisors the skills to conduct systematic, effective accident investigations. Delegates learn how to gather and preserve evidence, interview witnesses, identify root causes, and write clear reports with actionable recommendations.',
      'The course is highly practical, using case studies and exercises to give delegates hands-on experience of investigation techniques. By the end, participants will have the confidence to lead investigations in their own workplace.',
    ],
    modules: [
      { title: 'Why Investigate?', description: 'The purpose of investigation, legal requirements, and the difference between blame culture and learning culture.' },
      { title: 'When to Investigate', description: 'Which incidents to investigate, including the importance of near-miss investigation.' },
      { title: 'Planning Your Investigation', description: 'Assembling your team, initial response, and setting objectives for the investigation.' },
      { title: 'Gathering Evidence', description: 'Physical evidence, documentation, photographs, and preserving the scene.' },
      { title: 'Interviewing Witnesses', description: 'Interview techniques, asking effective questions, and dealing with reluctant or hostile witnesses.' },
      { title: 'Root Cause Analysis', description: 'Going beyond immediate causes to understand why the incident really occurred. The 5 Whys and other techniques.' },
      { title: 'Report Writing and Recommendations', description: 'Writing clear, objective reports and making practical, implementable recommendations.' },
    ],
    learningOutcomes: [
      'Understand why thorough accident investigation matters',
      'Know when to investigate and how to prioritise',
      'Plan and organise an effective investigation',
      'Gather and preserve relevant evidence',
      'Interview witnesses effectively and sensitively',
      'Identify root causes using structured techniques',
      'Write clear investigation reports with actionable recommendations',
      'Follow up on recommendations to ensure implementation',
    ],
    whoShouldAttend: 'Managers, supervisors, health and safety representatives, and anyone who may be called upon to investigate workplace accidents and incidents. Also valuable for HR professionals involved in incident response.',
    prerequisites: 'None required, though delegates will benefit from some supervisory or management experience.',
    certification: 'All delegates receive a certificate of attendance.',
    faqs: [
      { question: 'Should we investigate near-misses as well as accidents?', answer: 'Yes. Near-misses provide valuable learning opportunities without anyone getting hurt. The same underlying causes that lead to near-misses can result in serious injuries if circumstances are slightly different.' },
      { question: 'How soon after an incident should investigation begin?', answer: 'As soon as possible, once any injured persons have been cared for and the scene is safe. Physical evidence can be lost or disturbed, and witnesses\' memories fade quickly.' },
      { question: 'What about RIDDOR reportable incidents?', answer: 'The course covers RIDDOR requirements, including which incidents must be reported to the HSE and the timescales involved. Investigation should not be delayed while awaiting a RIDDOR report.' },
      { question: 'Can we use templates from the course in our workplace?', answer: 'Yes, we provide delegates with investigation templates and checklists that can be adapted for use in their own organisations.' },
    ],
    relatedCourses: ['iosh-managing-safely', 'fire-awareness'],
  },
}

type Props = {
  params: Promise<{ slug: string }>
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params
  const course = coursesData[slug]

  if (!course) {
    return { title: 'Course Not Found' }
  }

  return {
    title: course.metaTitle,
    description: course.metaDescription,
  }
}

export async function generateStaticParams() {
  return Object.keys(coursesData).map((slug) => ({ slug }))
}

export default async function TrainingCoursePage({ params }: Props) {
  const { slug } = await params
  const course = coursesData[slug]

  if (!course) {
    notFound()
  }

  // Breadcrumb items
  const breadcrumbItems = [
    { name: 'Home', url: 'https://integralsafety.co.uk' },
    { name: 'Training', url: 'https://integralsafety.co.uk/training' },
    { name: course.title, url: `https://integralsafety.co.uk/training/${slug}` },
  ]

  return (
    <>
      {/* Schema Markup */}
      <CourseSchema
        name={course.title}
        description={course.heroSubheading}
        slug={slug}
        duration={course.duration}
      />
      {course.faqs.length > 0 && <FAQSchema faqs={course.faqs} />}
      <BreadcrumbSchema items={breadcrumbItems} />

      {/* Hero */}
      <section className="py-16 md:py-20 bg-white">
        <div className="container">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="max-w-xl">
              <p className="section-eyebrow">Training</p>
              <h1 className="text-hero text-navy-900 mb-6">
                {course.title}
              </h1>
              <p className="text-body-lg text-gray-600 mb-8">
                {course.heroSubheading}
              </p>

              {/* Course Info */}
              <div className="flex flex-wrap gap-6 mb-8">
                <div className="flex items-center gap-2 text-gray-600">
                  <Clock className="w-5 h-5 text-navy-700" />
                  {course.duration}
                </div>
                <div className="flex items-center gap-2 text-gray-600">
                  <Monitor className="w-5 h-5 text-navy-700" />
                  {course.delivery}
                </div>
                {course.accreditation && (
                  <div className="flex items-center gap-2 text-green-600">
                    <Award className="w-5 h-5" />
                    {course.accreditation} Accredited
                  </div>
                )}
              </div>

              <div className="flex flex-wrap gap-4">
                <Link href="/contact" className="btn-primary">
                  Book This Course
                </Link>
                <a href="tel:01530382150" className="btn-secondary">
                  Call 01530 382 150
                </a>
              </div>
            </div>

            {/* Hero Image Placeholder */}
            <div className="hidden lg:block">
              <div className="w-full h-[360px] bg-cream rounded-hero shadow-hero overflow-hidden">
                {/* Hero image placeholder - add Image component with CMS image here */}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Overview */}
      <section className="py-16 bg-cream">
        <div className="container">
          <div className="grid lg:grid-cols-3 gap-12">
            {/* Main Content */}
            <div className="lg:col-span-2 space-y-8">
              {/* About */}
              <div className="bg-white rounded-card p-8 md:p-10">
                <h2 className="font-heading text-2xl font-semibold text-navy-900 mb-6">
                  About This Course
                </h2>
                <div className="text-gray-600 space-y-4">
                  {course.overview.map((paragraph, index) => (
                    <p key={index} className="leading-relaxed">
                      {paragraph}
                    </p>
                  ))}
                </div>
              </div>

              {/* Course Modules */}
              <div className="bg-white rounded-card p-8 md:p-10">
                <div className="flex items-center gap-3 mb-6">
                  <BookOpen className="w-6 h-6 text-orange-500" />
                  <h2 className="font-heading text-2xl font-semibold text-navy-900">
                    Course Content
                  </h2>
                </div>
                <div className="space-y-4">
                  {course.modules.map((module, index) => (
                    <div key={index} className="border-l-2 border-orange-200 pl-4 py-2">
                      <h3 className="font-semibold text-navy-900 mb-1">{module.title}</h3>
                      <p className="text-gray-600 text-sm">{module.description}</p>
                    </div>
                  ))}
                </div>
              </div>

              {/* Learning Outcomes */}
              <div className="bg-white rounded-card p-8 md:p-10">
                <div className="flex items-center gap-3 mb-6">
                  <Target className="w-6 h-6 text-orange-500" />
                  <h2 className="font-heading text-2xl font-semibold text-navy-900">
                    Learning Outcomes
                  </h2>
                </div>
                <ul className="space-y-3">
                  {course.learningOutcomes.map((outcome) => (
                    <li key={outcome} className="flex items-start gap-3">
                      <Check className="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" />
                      <span className="text-gray-600">{outcome}</span>
                    </li>
                  ))}
                </ul>
              </div>

              {/* FAQs */}
              <div className="bg-white rounded-card p-8 md:p-10">
                <div className="flex items-center gap-3 mb-6">
                  <HelpCircle className="w-6 h-6 text-orange-500" />
                  <h2 className="font-heading text-2xl font-semibold text-navy-900">
                    Frequently Asked Questions
                  </h2>
                </div>
                <div className="space-y-6">
                  {course.faqs.map((faq, index) => (
                    <div key={index}>
                      <h3 className="font-semibold text-navy-900 mb-2">{faq.question}</h3>
                      <p className="text-gray-600">{faq.answer}</p>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* Sidebar */}
            <div className="space-y-6">
              {/* Who Should Attend */}
              <div className="bg-white rounded-card p-6">
                <div className="flex items-center gap-3 mb-4">
                  <Users className="w-6 h-6 text-orange-500" />
                  <h3 className="font-heading text-lg font-semibold text-navy-900">
                    Who Should Attend
                  </h3>
                </div>
                <p className="text-gray-600 text-sm">
                  {course.whoShouldAttend}
                </p>
              </div>

              {/* Prerequisites */}
              {course.prerequisites && (
                <div className="bg-white rounded-card p-6">
                  <h3 className="font-heading text-lg font-semibold text-navy-900 mb-3">
                    Prerequisites
                  </h3>
                  <p className="text-gray-600 text-sm">
                    {course.prerequisites}
                  </p>
                </div>
              )}

              {/* Certification */}
              <div className="bg-white rounded-card p-6">
                <div className="flex items-center gap-3 mb-4">
                  <Award className="w-6 h-6 text-orange-500" />
                  <h3 className="font-heading text-lg font-semibold text-navy-900">
                    Certification
                  </h3>
                </div>
                <p className="text-gray-600 text-sm">
                  {course.certification}
                </p>
              </div>

              {/* Book Now Card */}
              <div className="bg-navy-900 rounded-card p-6 text-white">
                <h3 className="font-heading text-lg font-semibold mb-3">
                  Book This Course
                </h3>
                <p className="text-white/80 text-sm mb-4">
                  Contact us to discuss dates and arrange training for your team.
                </p>
                <Link
                  href="/contact"
                  className="block w-full bg-orange-500 text-white text-center py-3 rounded-button font-semibold hover:bg-orange-600 transition-colors mb-3"
                >
                  Request Booking
                </Link>
                <a
                  href="tel:01530382150"
                  className="block w-full bg-white/10 text-white text-center py-3 rounded-button font-semibold hover:bg-white/20 transition-colors"
                >
                  01530 382 150
                </a>
              </div>

              {/* On-Site Delivery */}
              <div className="bg-white rounded-card p-6">
                <div className="flex items-center gap-3 mb-4">
                  <Building2 className="w-6 h-6 text-orange-500" />
                  <h3 className="font-heading text-lg font-semibold text-navy-900">
                    On-Site Delivery
                  </h3>
                </div>
                <p className="text-gray-600 text-sm mb-3">
                  We can deliver this course at your premises. On-site training is often more convenient and allows us to tailor content to your workplace.
                </p>
                <Link
                  href="/contact"
                  className="text-orange-500 hover:text-orange-600 font-medium text-sm transition-colors"
                >
                  Enquire About On-Site Training &rarr;
                </Link>
              </div>

              {/* Related Courses */}
              <div className="bg-white rounded-card p-6">
                <h3 className="font-heading text-lg font-semibold text-navy-900 mb-4">
                  Related Courses
                </h3>
                <ul className="space-y-2">
                  {course.relatedCourses.map((relatedSlug) => {
                    const related = coursesData[relatedSlug]
                    if (!related) return null
                    return (
                      <li key={relatedSlug}>
                        <Link
                          href={`/training/${relatedSlug}`}
                          className="text-orange-500 hover:text-orange-600 font-medium text-sm transition-colors"
                        >
                          {related.title} &rarr;
                        </Link>
                      </li>
                    )
                  })}
                </ul>
                <div className="mt-4 pt-4 border-t border-navy-100">
                  <Link
                    href="/training"
                    className="text-navy-700 hover:text-navy-900 font-medium text-sm transition-colors"
                  >
                    View All Courses &rarr;
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <CTA />
    </>
  )
}
