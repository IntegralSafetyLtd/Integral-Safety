// Run with: npx tsx src/scripts/seed-training-pages.ts
import * as dotenv from 'dotenv'
// Load env before any imports that use process.env
const result = dotenv.config({ path: '.env.local' })
if (result.error) {
  console.error('Error loading .env.local:', result.error)
}
console.log('DATABASE_URI loaded:', process.env.DATABASE_URI ? 'Yes' : 'No')

// Now import payload after env is loaded
import { getPayload } from 'payload'
import { buildConfig } from 'payload'
import { postgresAdapter } from '@payloadcms/db-postgres'
import { lexicalEditor } from '@payloadcms/richtext-lexical'
import path from 'path'
import { fileURLToPath } from 'url'
import sharp from 'sharp'

import { Users } from '../payload/collections/Users'
import { Media } from '../payload/collections/Media'
import { Pages } from '../payload/collections/Pages'
import { Services } from '../payload/collections/Services'
import { Training } from '../payload/collections/Training'
import { Posts } from '../payload/collections/Posts'
import { Testimonials } from '../payload/collections/Testimonials'

const filename = fileURLToPath(import.meta.url)
const dirname = path.dirname(filename)

// Build config inline to ensure env vars are available
const config = buildConfig({
  admin: {
    user: Users.slug,
    importMap: {
      baseDir: path.resolve(dirname, '../payload'),
    },
  },
  collections: [Users, Media, Pages, Services, Training, Posts, Testimonials],
  editor: lexicalEditor(),
  secret: process.env.PAYLOAD_SECRET || 'your-secret-key-change-in-production',
  typescript: {
    outputFile: path.resolve(dirname, '../payload/payload-types.ts'),
  },
  db: postgresAdapter({
    pool: {
      connectionString: process.env.DATABASE_URI!,
    },
  }),
  sharp,
})

const trainingData = [
  {
    title: 'IOSH Managing Safely',
    slug: 'iosh-managing-safely',
    duration: '3-4 days',
    deliveryMethod: 'both' as const,
    accreditation: 'IOSH',
    overview: 'The IOSH Managing Safely course is designed for managers and supervisors in any sector who need to understand the essentials of health and safety. This internationally recognised qualification gives your managers the knowledge and skills to manage health and safety effectively within their teams.',
    learningOutcomes: [
      { outcome: 'Understand their legal responsibilities as managers' },
      { outcome: 'Conduct risk assessments and implement controls' },
      { outcome: 'Investigate incidents and near misses effectively' },
      { outcome: 'Measure and review health and safety performance' },
      { outcome: 'Promote a positive safety culture in their teams' },
      { outcome: 'Identify common workplace hazards' },
    ],
    whoShouldAttend: 'This course is ideal for managers, supervisors, team leaders, and anyone with responsibility for people or processes. It is suitable for all sectors and no prior health and safety knowledge is required.',
    showOnHomepage: true,
    seo: {
      metaTitle: 'IOSH Managing Safely Course Leicestershire | Leicester Training | Integral Safety',
      metaDescription: 'IOSH Managing Safely courses in Leicester, Loughborough & Leicestershire. 3-4 day management H&S training from local IOSH approved trainers. In-house or open courses.',
    },
  },
  {
    title: 'IOSH Working Safely',
    slug: 'iosh-working-safely',
    duration: '1 day',
    deliveryMethod: 'both' as const,
    accreditation: 'IOSH',
    overview: 'IOSH Working Safely is a one-day course for employees at any level who need to understand why health and safety matters and their role in keeping themselves and others safe. This entry-level qualification provides a solid foundation in workplace safety awareness.',
    learningOutcomes: [
      { outcome: 'Understand why health and safety is important' },
      { outcome: 'Define key terms including hazard and risk' },
      { outcome: 'Identify common workplace hazards' },
      { outcome: 'Understand how to improve safety performance' },
      { outcome: 'Know their responsibilities and how to report concerns' },
    ],
    whoShouldAttend: 'Suitable for employees at any level, across all industries. Perfect for new starters, as part of induction programmes, or to give existing staff a refresher on safety fundamentals. No prior knowledge required.',
    showOnHomepage: true,
    seo: {
      metaTitle: 'IOSH Working Safely Course Leicestershire | 1-Day Training | Integral Safety',
      metaDescription: 'IOSH Working Safely 1-day courses in Leicester & Leicestershire. Employee safety awareness training from local IOSH trainers. Classroom or online options.',
    },
  },
  {
    title: 'Manual Handling Training',
    slug: 'manual-handling',
    duration: 'Half day',
    deliveryMethod: 'both' as const,
    accreditation: 'RoSPA',
    overview: 'Our manual handling course teaches practical techniques for lifting, carrying, pushing, and pulling loads safely. Delegates learn to assess manual handling risks and apply correct techniques to protect themselves from musculoskeletal injuries.',
    learningOutcomes: [
      { outcome: 'Understand manual handling regulations and employer duties' },
      { outcome: 'Assess manual handling tasks for risk' },
      { outcome: 'Apply correct lifting and handling techniques' },
      { outcome: 'Recognise the causes of manual handling injuries' },
      { outcome: 'Know when to seek help or use mechanical aids' },
    ],
    whoShouldAttend: 'Anyone whose job involves lifting, carrying, pushing, pulling, or moving loads. This includes warehouse staff, care workers, construction workers, retail staff, and office workers who handle deliveries or equipment.',
    showOnHomepage: true,
    seo: {
      metaTitle: 'Manual Handling Training Leicestershire | Leicester Courses | Integral Safety',
      metaDescription: 'Manual handling training courses in Leicester, Loughborough & Leicestershire. Half-day practical training for safe lifting techniques. In-house or open courses.',
    },
  },
  {
    title: 'Fire Safety Awareness',
    slug: 'fire-safety-awareness',
    duration: '2-3 hours',
    deliveryMethod: 'both' as const,
    overview: 'This fire safety awareness course gives all employees the knowledge to prevent fires, understand fire risks, and respond correctly in an emergency. Suitable for fire wardens and general staff across all premises types.',
    learningOutcomes: [
      { outcome: 'Understand the fire triangle and how fires spread' },
      { outcome: 'Know the common causes of workplace fires' },
      { outcome: 'Respond correctly when discovering a fire' },
      { outcome: 'Understand evacuation procedures' },
      { outcome: 'Know how to use fire extinguishers safely' },
      { outcome: 'Recognise fire safety signs and equipment' },
    ],
    whoShouldAttend: 'All employees should receive fire safety awareness training. This course is also suitable for nominated fire wardens, though additional fire warden training may be beneficial for those with specific responsibilities.',
    showOnHomepage: true,
    seo: {
      metaTitle: 'Fire Safety Training Leicestershire | Leicester Fire Courses | Integral Safety',
      metaDescription: 'Fire safety awareness training in Leicester & Leicestershire. 2-3 hour courses for all staff. Fire warden training available. Local trainers, competitive rates.',
    },
  },
  {
    title: 'COSHH Awareness',
    slug: 'coshh-awareness',
    duration: 'Half day',
    deliveryMethod: 'both' as const,
    overview: 'Our COSHH awareness course covers the Control of Substances Hazardous to Health Regulations and how to work safely with chemicals, dusts, and other hazardous substances. Delegates learn to read safety data sheets, use PPE correctly, and handle substances safely.',
    learningOutcomes: [
      { outcome: 'Understand COSHH regulations and legal requirements' },
      { outcome: 'Identify hazardous substances in the workplace' },
      { outcome: 'Read and interpret safety data sheets' },
      { outcome: 'Select and use appropriate PPE' },
      { outcome: 'Store and handle hazardous substances safely' },
      { outcome: 'Know the signs and symptoms of exposure' },
    ],
    whoShouldAttend: 'Anyone who works with or near hazardous substances, including chemicals, dusts, fumes, and biological agents. Suitable for manufacturing, cleaning, construction, healthcare, and laboratory staff.',
    showOnHomepage: false,
    seo: {
      metaTitle: 'COSHH Training Leicestershire | Hazardous Substances Course | Integral Safety',
      metaDescription: 'COSHH awareness training in Leicester & Leicestershire. Half-day courses on hazardous substances and chemical safety. In-house training at your premises.',
    },
  },
  {
    title: 'Working at Height',
    slug: 'working-at-height',
    duration: 'Half day',
    deliveryMethod: 'in-person' as const,
    overview: 'This practical course covers the Work at Height Regulations and safe use of access equipment including ladders, stepladders, and mobile scaffolds. Delegates gain hands-on experience with equipment inspection and correct usage.',
    learningOutcomes: [
      { outcome: 'Understand Work at Height Regulations' },
      { outcome: 'Apply the hierarchy of controls for working at height' },
      { outcome: 'Inspect ladders and stepladders before use' },
      { outcome: 'Set up and use ladders safely' },
      { outcome: 'Understand when to use alternative access equipment' },
      { outcome: 'Know rescue and emergency procedures' },
    ],
    whoShouldAttend: 'Anyone who works at height or supervises work at height activities. This includes construction workers, maintenance staff, facilities management, and anyone using ladders or access equipment as part of their job.',
    showOnHomepage: false,
    seo: {
      metaTitle: 'Working at Height Training Leicestershire | Ladder Safety Course | Integral Safety',
      metaDescription: 'Working at height and ladder safety training in Leicester & Leicestershire. Practical half-day courses covering regulations and safe access equipment use.',
    },
  },
  {
    title: 'Asbestos Awareness',
    slug: 'asbestos-awareness',
    duration: '2-3 hours',
    deliveryMethod: 'both' as const,
    overview: 'Our asbestos awareness training is designed for anyone who may encounter asbestos-containing materials during their work. The course covers the health risks of asbestos, where it may be found, and what to do if you suspect you have found it.',
    learningOutcomes: [
      { outcome: 'Understand the health risks of asbestos exposure' },
      { outcome: 'Identify where asbestos may be found in buildings' },
      { outcome: 'Recognise types of asbestos-containing materials' },
      { outcome: 'Know what to do if asbestos is suspected or disturbed' },
      { outcome: 'Understand the duty to manage asbestos' },
      { outcome: 'Know when licensed contractors are required' },
    ],
    whoShouldAttend: 'Anyone whose work could disturb asbestos, including maintenance workers, electricians, plumbers, builders, and facilities staff. Also suitable for managers responsible for buildings containing asbestos.',
    showOnHomepage: false,
    seo: {
      metaTitle: 'Asbestos Awareness Training Leicestershire | Leicester Courses | Integral Safety',
      metaDescription: 'Asbestos awareness training in Leicester & Leicestershire. 2-3 hour courses covering identification, risks, and safe working. Meets HSE requirements.',
    },
  },
  {
    title: 'First Aid at Work',
    slug: 'first-aid-at-work',
    duration: '3 days',
    deliveryMethod: 'in-person' as const,
    accreditation: 'HSE Approved',
    overview: 'Our HSE-approved First Aid at Work course provides comprehensive training for appointed first aiders. Delegates learn to manage a range of injuries and medical emergencies, from minor wounds to life-threatening conditions.',
    learningOutcomes: [
      { outcome: 'Assess an incident and manage priorities' },
      { outcome: 'Perform CPR and use an AED' },
      { outcome: 'Treat wounds, burns, and fractures' },
      { outcome: 'Recognise and treat shock' },
      { outcome: 'Manage choking, seizures, and other medical emergencies' },
      { outcome: 'Record incidents and maintain first aid equipment' },
    ],
    whoShouldAttend: 'Those nominated as first aiders in their workplace, particularly in higher-risk environments or where employees work far from emergency services. This 3-day course meets the HSE standard for workplace first aiders.',
    showOnHomepage: false,
    seo: {
      metaTitle: 'First Aid at Work Course Leicestershire | 3-Day HSE Training | Integral Safety',
      metaDescription: 'HSE-approved First Aid at Work 3-day courses in Leicester & Leicestershire. Comprehensive first aider training from qualified local trainers. Certificates valid 3 years.',
    },
  },
  {
    title: 'Emergency First Aid at Work',
    slug: 'emergency-first-aid',
    duration: '1 day',
    deliveryMethod: 'in-person' as const,
    accreditation: 'HSE Approved',
    overview: 'The Emergency First Aid at Work (EFAW) course is a one-day qualification for appointed first aiders in lower-risk workplaces. It covers essential life-saving skills and basic first aid procedures.',
    learningOutcomes: [
      { outcome: 'Assess an incident and call for help' },
      { outcome: 'Manage an unconscious casualty' },
      { outcome: 'Perform CPR' },
      { outcome: 'Treat wounds and bleeding' },
      { outcome: 'Recognise and respond to shock' },
      { outcome: 'Treat minor injuries' },
    ],
    whoShouldAttend: 'Those nominated as first aiders in lower-risk workplaces such as offices, shops, and light industrial premises. Also suitable as a foundation before progressing to the full First Aid at Work course.',
    showOnHomepage: false,
    seo: {
      metaTitle: 'Emergency First Aid Course Leicestershire | 1-Day EFAW Training | Integral Safety',
      metaDescription: 'Emergency First Aid at Work 1-day courses in Leicester & Leicestershire. HSE-approved EFAW training for lower-risk workplaces. Local trainers, competitive rates.',
    },
  },
  {
    title: 'Abrasive Wheels',
    slug: 'abrasive-wheels',
    duration: 'Half day',
    deliveryMethod: 'in-person' as const,
    overview: 'This practical course covers the safe mounting, use, and maintenance of abrasive wheels. Delegates learn to inspect wheels for damage, select the correct wheel for the application, and mount wheels in compliance with PUWER regulations.',
    learningOutcomes: [
      { outcome: 'Understand abrasive wheel hazards and legal requirements' },
      { outcome: 'Inspect wheels for damage before mounting' },
      { outcome: 'Select appropriate wheels for specific applications' },
      { outcome: 'Mount and dress wheels correctly' },
      { outcome: 'Use abrasive wheel machines safely' },
      { outcome: 'Maintain wheels and machines' },
    ],
    whoShouldAttend: 'Anyone who uses angle grinders, bench grinders, or other abrasive wheel equipment. Also suitable for those who mount abrasive wheels as part of their duties. This training meets PUWER requirements.',
    showOnHomepage: false,
    seo: {
      metaTitle: 'Abrasive Wheels Training Leicestershire | Grinder Safety Course | Integral Safety',
      metaDescription: 'Abrasive wheels training in Leicester & Leicestershire. Practical half-day courses covering safe mounting and use. Meets PUWER requirements.',
    },
  },
]

const pagesData = [
  {
    title: 'About Us',
    slug: 'about',
    heroHeading: 'About Integral Safety',
    heroSubheading: 'Local health and safety consultants based in Leicestershire, helping businesses across the East Midlands protect their people and stay compliant since 2005.',
    seo: {
      metaTitle: 'About Us | Health & Safety Consultants Leicestershire | Integral Safety',
      metaDescription: 'Learn about Integral Safety, your local health and safety consultants in Leicestershire. Based in Coalville and Melton Mowbray, serving Leicester, Loughborough and the Midlands.',
    },
  },
  {
    title: 'Contact Us',
    slug: 'contact',
    heroHeading: 'Contact Integral Safety',
    heroSubheading: 'Get in touch with our Leicestershire team for a free, no-obligation discussion about your health and safety requirements.',
    seo: {
      metaTitle: 'Contact Us | Health & Safety Consultants Leicestershire | Integral Safety',
      metaDescription: 'Contact Integral Safety for health and safety support in Leicestershire. Call 01530 382150 or email us. Offices in Coalville and Melton Mowbray.',
    },
  },
  {
    title: 'Privacy Policy',
    slug: 'privacy-policy',
    heroHeading: 'Privacy Policy',
    heroSubheading: 'How we collect, use, and protect your personal information.',
    seo: {
      metaTitle: 'Privacy Policy | Integral Safety Ltd',
      metaDescription: 'Read our privacy policy to understand how Integral Safety Ltd collects, uses, and protects your personal information in accordance with UK GDPR.',
    },
  },
  {
    title: 'Terms & Conditions',
    slug: 'terms-conditions',
    heroHeading: 'Terms & Conditions',
    heroSubheading: 'The terms governing our services and your use of this website.',
    seo: {
      metaTitle: 'Terms & Conditions | Integral Safety Ltd',
      metaDescription: 'Read our terms and conditions governing the use of Integral Safety Ltd services and this website.',
    },
  },
]

async function seed() {
  console.log('Starting seed...')

  const payload = await getPayload({ config })

  // Seed Training
  console.log('\n--- Seeding Training Courses ---')
  console.log('Clearing existing training courses...')
  const existingTraining = await payload.find({
    collection: 'training',
    limit: 100,
  })

  for (const course of existingTraining.docs) {
    await payload.delete({
      collection: 'training',
      id: course.id,
    })
  }

  console.log('Creating training courses...')
  for (const course of trainingData) {
    try {
      await payload.create({
        collection: 'training',
        data: course,
      })
      console.log(`Created: ${course.title}`)
    } catch (error) {
      console.error(`Error creating ${course.title}:`, error)
    }
  }

  // Seed Pages
  console.log('\n--- Seeding Pages ---')
  console.log('Clearing existing pages...')
  const existingPages = await payload.find({
    collection: 'pages',
    limit: 100,
  })

  for (const page of existingPages.docs) {
    await payload.delete({
      collection: 'pages',
      id: page.id,
    })
  }

  console.log('Creating pages...')
  for (const page of pagesData) {
    try {
      await payload.create({
        collection: 'pages',
        data: page,
      })
      console.log(`Created: ${page.title}`)
    } catch (error) {
      console.error(`Error creating ${page.title}:`, error)
    }
  }

  console.log('\nSeed completed!')
  process.exit(0)
}

seed().catch((error) => {
  console.error('Seed error:', error)
  process.exit(1)
})
