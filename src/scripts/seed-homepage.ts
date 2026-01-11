// Run with: npx tsx src/scripts/seed-homepage.ts
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
import { Homepage } from '../payload/globals/Homepage'

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
  globals: [Homepage],
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

const homepageData = {
  hero: {
    badge: 'IOSH Approved Training Provider',
    headingLine1: "Leicestershire's Trusted",
    headingHighlight: 'Health & Safety',
    headingLine2: 'Experts',
    description: 'From fire risk assessments to IOSH training, we help Midlands businesses create safer workplaces. Over 20 years of experience protecting your people, property, and peace of mind.',
    primaryButtonText: 'Get Your Free Quote',
    primaryButtonLink: '/contact',
    secondaryButtonText: 'Explore Our Services',
    secondaryButtonLink: '/services',
    trustText: 'Trusted by 100+ organisations',
    trustSubtext: 'Housing associations, construction, hospitality & more',
    floatingCardTitle: 'PAS 79 Compliant',
    floatingCardSubtitle: 'Fire Risk Assessments',
  },
  whyUs: {
    eyebrow: 'Why Choose Integral Safety',
    heading: 'Health & Safety That Works For Your Business',
    description: "We've spent over two decades helping Leicestershire businesses navigate health and safety requirements. Our approach is simple: provide sensible, proportionate advice that protects your people without drowning you in bureaucracy.",
    reasons: [
      { reason: 'Practical advice that works in the real world, not just on paper' },
      { reason: 'No unnecessary paperwork or over-complicated systems' },
      { reason: 'We explain the "why" so you understand your obligations' },
      { reason: 'Flexible support from one day per month to daily visits' },
      { reason: 'Direct access to experienced consultants, not call centres' },
      { reason: 'Local presence with offices in Coalville and Melton Mowbray' },
    ],
    stats: [
      { number: '20+', label: 'Years Experience' },
      { number: '15+', label: 'Years in Housing Sector' },
      { number: '100+', label: 'Organisations Served' },
      { number: '2', label: 'Leicestershire Offices' },
    ],
  },
  cta: {
    heading: 'Ready to Improve Your Safety Culture?',
    subheading: "Book a free consultation and let's discuss how we can help.",
    phoneNumber: '01530 382 150',
    primaryButtonText: 'Call 01530 382 150',
    secondaryButtonText: 'Send Enquiry',
    secondaryButtonLink: '/contact',
  },
  services: {
    eyebrow: 'Our Services',
    heading: 'Comprehensive Health & Safety Solutions',
    description: 'Practical, proportionate advice that protects your people and keeps your business compliant. No jargon, no unnecessary paperwork - just results.',
  },
  seo: {
    metaTitle: 'Integral Safety | Health & Safety Consultants | Leicestershire',
    metaDescription: 'Leicestershire health and safety consultants with 20+ years experience. Fire risk assessments, IOSH training, H&S consultancy, and drone surveys. Offices in Coalville and Melton Mowbray.',
    keywords: 'health and safety consultants Leicestershire, fire risk assessments, IOSH training, health and safety Coalville, H&S consultancy Midlands',
  },
}

async function seed() {
  console.log('Starting homepage seed...')

  const payload = await getPayload({ config })

  console.log('Updating homepage global...')
  try {
    await payload.updateGlobal({
      slug: 'homepage',
      data: homepageData,
    })
    console.log('Homepage global updated successfully!')
  } catch (error) {
    console.error('Error updating homepage:', error)
  }

  console.log('Seed completed!')
  process.exit(0)
}

seed().catch((error) => {
  console.error('Seed error:', error)
  process.exit(1)
})
