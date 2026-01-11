// Run with: npx tsx src/scripts/seed-services.ts
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

const servicesData = [
  {
    title: 'Fire Risk Assessments',
    slug: 'fire-risk-assessments',
    icon: 'fire',
    shortDescription: 'PAS 79 compliant fire risk assessments for all premises types. Protect your people and property.',
    heroHeading: 'Fire Risk Assessments Across Leicestershire & the Midlands',
    heroSubheading: 'PAS 79 compliant fire risk assessments from local fire safety professionals based in Coalville and Melton Mowbray. Serving Leicester, Loughborough, Hinckley, Nuneaton, and the wider East Midlands.',
    whatWeAssess: [
      { item: 'Sources of ignition and fuel' },
      { item: 'Means of escape and emergency routes' },
      { item: 'Emergency lighting and signage' },
      { item: 'Fire detection and warning systems' },
      { item: 'Fire-fighting equipment provision' },
      { item: 'Compartmentation and fire doors' },
      { item: 'Electrical safety and installations' },
      { item: 'Storage of flammable materials' },
      { item: 'Housekeeping and waste management' },
      { item: 'Arson prevention measures' },
      { item: 'Staff training and fire drills' },
      { item: 'Emergency plans and procedures' },
    ],
    processSteps: [
      {
        title: 'Initial Consultation',
        description: 'We discuss your premises, occupancy, and any specific concerns. You tell us about your building and how it is used, and we explain what the assessment involves.',
      },
      {
        title: 'Site Visit',
        description: 'Our assessor visits your premises to carry out a thorough inspection. We examine all areas, take photographs, and review your existing fire safety arrangements and documentation.',
      },
      {
        title: 'Risk Evaluation',
        description: 'We identify fire hazards, evaluate who might be at risk, and assess the adequacy of existing control measures. We consider both the likelihood of fire and its potential consequences.',
      },
      {
        title: 'Report Preparation',
        description: 'We prepare a detailed, PAS 79:2020 compliant report with clear findings, risk ratings, and a prioritised action plan with recommended timescales.',
      },
      {
        title: 'Delivery & Discussion',
        description: 'We deliver your report and walk you through the findings. We explain each recommendation clearly and answer any questions you may have.',
      },
    ],
    whatYouReceive: [
      { item: 'Comprehensive PAS 79:2020 compliant fire risk assessment report' },
      { item: 'Detailed photographic evidence of findings' },
      { item: 'Clear risk ratings for identified hazards' },
      { item: 'Prioritised action plan with recommended timescales' },
      { item: 'Floor plans showing escape routes and equipment (where applicable)' },
      { item: 'Summary of significant findings for quick reference' },
      { item: 'Recommendations for staff training requirements' },
      { item: 'Digital PDF format for easy sharing and storage' },
    ],
    premisesTypes: [
      { type: 'Commercial offices and business premises' },
      { type: 'Retail shops and shopping centres' },
      { type: 'Houses in Multiple Occupation (HMOs)' },
      { type: 'Housing association properties' },
      { type: 'Communal areas of flats and apartments' },
      { type: 'Care homes and residential care facilities' },
      { type: 'Schools, colleges, and educational buildings' },
      { type: 'Warehouses and industrial units' },
      { type: 'Hotels, B&Bs, and hospitality venues' },
      { type: 'Pubs, restaurants, and licensed premises' },
      { type: 'Churches and community halls' },
      { type: 'Construction sites and temporary structures' },
    ],
    benefits: [
      { benefit: 'Local Leicestershire-based consultants' },
      { benefit: 'Fully PAS 79:2020 compliant assessments' },
      { benefit: 'Serving Leicester, Loughborough, Hinckley & beyond' },
      { benefit: 'Detailed photographic reports' },
      { benefit: 'Prioritised action plans with timescales' },
      { benefit: 'Suitable for all property types including HMOs' },
      { benefit: 'Housing association specialists' },
      { benefit: 'Competitive fixed-fee pricing' },
    ],
    faqs: [
      {
        question: 'How often should a fire risk assessment be reviewed?',
        answer: 'Fire risk assessments should be reviewed regularly and updated whenever there are significant changes to your premises, such as alterations to the building, changes in use, or after a fire or near miss. For most premises, an annual review is recommended as good practice.',
      },
      {
        question: 'Who is responsible for the fire risk assessment?',
        answer: 'The "responsible person" is legally required to ensure a fire risk assessment is carried out. This is typically the employer, building owner, landlord, or managing agent. While you can carry out the assessment yourself if you are competent, many organisations choose to use a qualified professional.',
      },
      {
        question: 'What happens if I do not have a fire risk assessment?',
        answer: 'Failure to carry out a suitable and sufficient fire risk assessment is a criminal offence. Enforcement authorities can issue enforcement notices, prohibition notices (closing your premises), or prosecute. Fines can be unlimited and responsible persons can face imprisonment for serious breaches.',
      },
      {
        question: 'How much does a fire risk assessment cost?',
        answer: 'The cost varies depending on the size and complexity of your premises. Please contact us for a free, no-obligation quote tailored to your specific requirements.',
      },
      {
        question: 'What is PAS 79 and why does it matter?',
        answer: 'PAS 79 is a Publicly Available Specification published by the British Standards Institution. It provides a framework for carrying out fire risk assessments in buildings, ensuring a consistent and thorough approach. A PAS 79 compliant assessment demonstrates that your fire risk assessment meets recognised industry standards.',
      },
      {
        question: 'How long does a fire risk assessment take?',
        answer: 'The site visit typically takes between 1-4 hours depending on the size and complexity of your premises. A small office might take an hour, while a large warehouse or multi-storey building will take longer. We will give you an estimate when you book.',
      },
      {
        question: 'Do I need to be present during the assessment?',
        answer: 'It is helpful to have someone available who knows the building and can provide access to all areas. They should also be able to answer questions about how the premises are used, staffing levels, and existing fire safety procedures.',
      },
      {
        question: 'What is the Fire Safety Act 2021 and how does it affect me?',
        answer: 'The Fire Safety Act 2021 clarified that the Regulatory Reform (Fire Safety) Order 2005 applies to the structure, external walls, and flat entrance doors of multi-occupied residential buildings. It also introduced requirements to share fire risk assessment information with residents. If you manage a block of flats, these changes may affect your obligations.',
      },
    ],
    showOnHomepage: true,
    homepageOrder: 1,
    seo: {
      metaTitle: 'Fire Risk Assessments Leicestershire | Leicester, Coalville, Melton | Integral Safety',
      metaDescription: 'PAS 79 fire risk assessments across Leicestershire, Leicester, Loughborough & the East Midlands. Local fire safety consultants with 20+ years experience. Free quotes.',
    },
  },
  {
    title: 'Health & Safety Consultancy',
    slug: 'consultancy',
    icon: 'clipboard',
    shortDescription: 'Expert health and safety advice tailored to your business needs. Flexible support packages available.',
    heroHeading: 'Health & Safety Consultants in Leicestershire',
    heroSubheading: 'Local H&S consultants based in Coalville and Melton Mowbray, providing practical health and safety support to businesses across Leicester, Loughborough, and the East Midlands.',
    whatWeAssess: [
      { item: 'Workplace hazards and risk controls' },
      { item: 'Health and safety management systems' },
      { item: 'Legal compliance and regulatory requirements' },
      { item: 'Training needs and competency gaps' },
      { item: 'Accident and incident records' },
      { item: 'Emergency procedures and arrangements' },
      { item: 'Contractor management processes' },
      { item: 'Manual handling operations' },
      { item: 'COSHH and hazardous substances' },
      { item: 'Work at height activities' },
      { item: 'Display screen equipment use' },
      { item: 'Fire safety arrangements' },
    ],
    processSteps: [
      {
        title: 'Initial Discussion',
        description: 'We start with a free, no-obligation conversation to understand your business, your challenges, and what you need from a health and safety consultant. No sales pitch - just an honest discussion.',
      },
      {
        title: 'Site Visit & Review',
        description: 'We visit your premises to see your operations first-hand. We review your existing documentation, observe work practices, and identify any gaps or areas for improvement.',
      },
      {
        title: 'Tailored Proposal',
        description: 'Based on what we find, we put together a proposal that addresses your specific needs. Whether you need a one-off project or ongoing support, we will recommend the right approach for your situation.',
      },
      {
        title: 'Practical Implementation',
        description: 'We get to work delivering practical solutions. Risk assessments, policies, training, audits - whatever you need, we focus on getting it right and making sure it works for your business.',
      },
      {
        title: 'Ongoing Support',
        description: 'Health and safety is not a one-time fix. We provide ongoing telephone and email support, regular reviews, and are always available when you need advice or assistance.',
      },
    ],
    whatYouReceive: [
      { item: 'Health and safety policy tailored to your business' },
      { item: 'Comprehensive risk assessments for your activities' },
      { item: 'Safe systems of work and method statements' },
      { item: 'COSHH assessments for hazardous substances' },
      { item: 'Manual handling assessments' },
      { item: 'Display screen equipment assessments' },
      { item: 'Fire risk assessments (if required)' },
      { item: 'Staff training and toolbox talks' },
      { item: 'Accident investigation and reporting support' },
      { item: 'Ongoing telephone and email advice' },
      { item: 'Regular site visits and inspections' },
      { item: 'Contractor and supplier vetting guidance' },
    ],
    premisesTypes: [
      { type: 'Offices and commercial premises' },
      { type: 'Manufacturing and production facilities' },
      { type: 'Warehouses and distribution centres' },
      { type: 'Construction sites and projects' },
      { type: 'Retail shops and stores' },
      { type: 'Hospitality venues and restaurants' },
      { type: 'Care homes and healthcare settings' },
      { type: 'Educational establishments' },
      { type: 'Housing associations and property management' },
      { type: 'Transport and logistics operations' },
      { type: 'Agricultural and rural businesses' },
      { type: 'Leisure and fitness facilities' },
    ],
    benefits: [
      { benefit: 'Local consultants based in Leicestershire' },
      { benefit: 'Serving Leicester, Loughborough, Hinckley & beyond' },
      { benefit: 'Offices in Coalville and Melton Mowbray' },
      { benefit: 'Flexible packages to suit your budget' },
      { benefit: 'Policy and procedure development' },
      { benefit: 'Comprehensive risk assessments' },
      { benefit: 'No long-term contracts required' },
      { benefit: 'Experience across multiple industries' },
    ],
    faqs: [
      {
        question: 'Do I legally need a health and safety consultant?',
        answer: 'Not necessarily. The law requires you to appoint a "competent person" to help with health and safety. If you have someone in-house with the right knowledge and experience, they can fulfil this role. However, if you lack in-house expertise, appointing an external consultant is often the most practical and cost-effective solution.',
      },
      {
        question: 'How much does health and safety consultancy cost?',
        answer: 'The cost depends on the level of support you need and the complexity of your operations. Please contact us for a free, no-obligation discussion about your needs and we will provide a clear quotation tailored to your requirements.',
      },
      {
        question: 'Can you help with health and safety documentation?',
        answer: 'Absolutely. Documentation development is a core part of our consultancy service. We can create or review policies, risk assessments, method statements, safe systems of work, and any other documentation you need for compliance or tendering purposes.',
      },
      {
        question: 'What is the difference between consultancy and competent person services?',
        answer: 'Our competent person service is a retained package where we act as your appointed competent person with regular site visits and ongoing support. Consultancy can be more flexible - from one-off projects to ad-hoc advice. Many clients start with consultancy and move to a retained package once they see the value.',
      },
      {
        question: 'How quickly can you help us?',
        answer: 'We understand that sometimes you need help urgently - perhaps for a tender deadline or an HSE visit. We always try to respond quickly and can often arrange an initial visit within a few days. For urgent matters, call us and we will do our best to help.',
      },
      {
        question: 'Do you work with small businesses?',
        answer: 'Yes, we work with businesses of all sizes. In fact, small businesses often benefit most from external consultancy because they typically lack the resources for a dedicated in-house safety professional. We tailor our support to be proportionate to your size and risks.',
      },
      {
        question: 'Can you help us win contracts that require health and safety accreditation?',
        answer: 'Yes. Many of our clients come to us because they need CHAS, SafeContractor, Constructionline or similar accreditation to win work. We can help you develop the documentation and systems you need, and guide you through the application process.',
      },
      {
        question: 'What industries do you have experience in?',
        answer: 'We have worked across a wide range of industries including construction, manufacturing, warehousing, hospitality, retail, healthcare, education, and housing. This breadth of experience means we can bring proven solutions and fresh perspectives to your business.',
      },
    ],
    showOnHomepage: true,
    homepageOrder: 2,
    seo: {
      metaTitle: 'Health & Safety Consultants Leicestershire | Leicester, Loughborough | Integral Safety',
      metaDescription: 'Local health and safety consultants in Leicestershire. Serving Leicester, Loughborough, Coalville & the East Midlands. Practical H&S advice. Call 01530 382150.',
    },
  },
  {
    title: 'IOSH Training',
    slug: 'iosh-training',
    icon: 'book',
    shortDescription: 'IOSH Managing Safely and Working Safely courses. Accredited training delivered locally.',
    heroHeading: 'IOSH Training Courses in Leicestershire',
    heroSubheading: 'IOSH Managing Safely and Working Safely courses delivered locally in Leicester, Loughborough, and across the East Midlands. In-house and open courses available.',
    benefits: [
      { benefit: 'IOSH approved training provider' },
      { benefit: 'Courses in Leicester, Loughborough & Coalville' },
      { benefit: 'In-house training at your premises' },
      { benefit: 'Managing Safely & Working Safely' },
      { benefit: 'Experienced local trainers' },
      { benefit: 'Competitive group rates' },
      { benefit: 'IOSH certificates on completion' },
      { benefit: 'Refresher courses available' },
    ],
    faqs: [
      {
        question: 'What is the difference between IOSH Managing Safely and Working Safely?',
        answer: 'Managing Safely is designed for managers and supervisors who need to understand their health and safety responsibilities. Working Safely is for employees at any level who need a basic understanding of workplace health and safety.',
      },
      {
        question: 'How long do the courses take?',
        answer: 'IOSH Managing Safely is typically 3-4 days, while IOSH Working Safely is a 1-day course. Both can be delivered as classroom or online training.',
      },
      {
        question: 'Do IOSH certificates expire?',
        answer: 'IOSH certificates do not officially expire, but best practice is to refresh training every 3-5 years to stay current with legislation and best practices.',
      },
    ],
    showOnHomepage: true,
    homepageOrder: 3,
    seo: {
      metaTitle: 'IOSH Training Leicestershire | Managing Safely, Working Safely | Integral Safety',
      metaDescription: 'IOSH training courses in Leicester, Loughborough & Leicestershire. Managing Safely & Working Safely from local IOSH approved trainers. In-house or open courses.',
    },
  },
  {
    title: 'Drone Surveys',
    slug: 'work-at-height-surveys',
    icon: 'video',
    shortDescription: 'Aerial surveys eliminating work at height risks. 4K imagery and comprehensive reports.',
    heroHeading: 'Drone Surveys & Roof Inspections in Leicestershire',
    heroSubheading: 'Drone pilots based in Leicestershire, providing aerial inspections across Leicester, Loughborough, and the East Midlands. 4K video and high-resolution imagery without scaffolding.',
    whatWeAssess: [
      { item: 'Roof condition and defects' },
      { item: 'Chimney stacks and flashings' },
      { item: 'Gutters, downpipes, and drainage' },
      { item: 'Flat roof membranes and coverings' },
      { item: 'Skylights and roof windows' },
      { item: 'Solar panel installations' },
      { item: 'Building facades and cladding' },
      { item: 'High-level masonry and pointing' },
      { item: 'Lightning protection systems' },
      { item: 'Ventilation and extraction systems' },
      { item: 'Hard-to-access building features' },
      { item: 'Storm or weather damage' },
    ],
    processSteps: [
      {
        title: 'Survey Planning',
        description: 'We discuss your requirements and assess the site. We check airspace restrictions, identify any permissions needed, and plan the flight path to capture all areas of interest.',
      },
      {
        title: 'Site Preparation',
        description: 'On the day, we arrive and conduct a site safety assessment. We set up exclusion zones if needed, brief any personnel, and check weather conditions are suitable for safe flight.',
      },
      {
        title: 'Aerial Survey',
        description: 'Our qualified pilot flies the drone to capture comprehensive 4K video footage and high-resolution still images. We cover all specified areas and can focus on any particular concerns you have identified.',
      },
      {
        title: 'Image Review',
        description: 'We review all captured footage and images, identifying any defects, damage, or areas of concern. We select the most relevant images to include in your report.',
      },
      {
        title: 'Report Delivery',
        description: 'We provide a written report with annotated images highlighting findings. You also receive all original footage and images for your records.',
      },
    ],
    whatYouReceive: [
      { item: '4K video footage of all surveyed areas' },
      { item: 'High-resolution still photographs' },
      { item: 'Written survey report with findings' },
      { item: 'Annotated images highlighting defects' },
      { item: 'Recommendations for remedial work' },
      { item: 'Digital files via secure download or USB' },
      { item: 'Before and after comparisons (for repeat surveys)' },
      { item: 'Priority ratings for any issues found' },
    ],
    premisesTypes: [
      { type: 'Residential properties and housing stock' },
      { type: 'Commercial buildings and offices' },
      { type: 'Industrial units and warehouses' },
      { type: 'Churches and historic buildings' },
      { type: 'Schools and educational buildings' },
      { type: 'Care homes and healthcare facilities' },
      { type: 'Retail premises and shopping centres' },
      { type: 'Multi-storey buildings and tower blocks' },
      { type: 'Agricultural buildings and barns' },
      { type: 'Listed buildings and heritage structures' },
      { type: 'Solar farm installations' },
      { type: 'Any structure requiring high-level inspection' },
    ],
    benefits: [
      { benefit: 'Local pilots in Leicestershire' },
      { benefit: 'Covering Leicester, Loughborough & the Midlands' },
      { benefit: '4K video and high-resolution imagery' },
      { benefit: 'No scaffolding or access equipment needed' },
      { benefit: 'Eliminates work at height risks' },
      { benefit: 'Cost-effective compared to traditional methods' },
      { benefit: 'Rapid turnaround on reports' },
      { benefit: 'Suitable for listed buildings and sensitive structures' },
    ],
    faqs: [
      {
        question: 'Do you need permission to fly drones?',
        answer: 'Yes, commercial drone operations require appropriate permissions. Our pilots are fully certified and we carry public liability insurance. We also obtain any necessary permissions for flights in controlled airspace or over private land.',
      },
      {
        question: 'What weather conditions are needed?',
        answer: 'Drones cannot fly safely in strong winds (typically above 20mph), heavy rain, or poor visibility. We monitor weather conditions and will reschedule if conditions are unsuitable. Light rain or overcast conditions are usually fine.',
      },
      {
        question: 'How do I receive the survey results?',
        answer: 'We provide digital files including video footage and still images, along with a written report highlighting any areas of concern. Files can be delivered via secure download link or USB drive depending on file size and your preference.',
      },
      {
        question: 'How much does a drone survey cost?',
        answer: 'The cost depends on the size and complexity of the structure being surveyed. Please contact us to discuss your requirements and we will provide a clear quotation.',
      },
      {
        question: 'Is a drone survey as good as a physical inspection?',
        answer: 'Drone surveys provide excellent visual assessment and can often identify issues that would be missed from ground level. For most purposes, they provide sufficient detail for maintenance planning. However, for detailed structural assessments, a physical inspection may still be needed for specific areas identified by the drone survey.',
      },
      {
        question: 'Can you fly near airports or in built-up areas?',
        answer: 'We can often operate in these areas but may need additional permissions. We handle all the necessary authorisations as part of our service. Let us know your location and we will advise on any restrictions.',
      },
      {
        question: 'How long does a survey take?',
        answer: 'The flight itself typically takes 15-45 minutes depending on the size of the structure. We allow additional time for setup, safety checks, and multiple passes if needed. Most surveys are completed within a half-day visit.',
      },
      {
        question: 'Can you survey multiple buildings in one visit?',
        answer: 'Yes, surveying multiple buildings in one visit is often more cost-effective. This is ideal for housing associations, property portfolios, or estates with multiple structures to inspect.',
      },
    ],
    showOnHomepage: true,
    homepageOrder: 4,
    seo: {
      metaTitle: 'Drone Roof Surveys Leicestershire | Leicester, Loughborough | Integral Safety',
      metaDescription: 'Drone surveys across Leicestershire & the East Midlands. Roof inspections, chimney surveys & building assessments. Local pilots, 4K imagery, no scaffolding.',
    },
  },
  {
    title: 'Competent Person Services',
    slug: 'competent-person',
    icon: 'shield',
    shortDescription: 'Meet your legal obligations with our appointed competent person packages.',
    heroHeading: 'Competent Person Services in Leicestershire',
    heroSubheading: 'Local appointed competent person services for businesses across Leicester, Loughborough, and the East Midlands. Meet your legal obligations with flexible support from Leicestershire-based consultants.',
    whatWeAssess: [
      { item: 'Your current health and safety arrangements' },
      { item: 'Existing policies and procedures' },
      { item: 'Risk assessments and their adequacy' },
      { item: 'Training needs and competency gaps' },
      { item: 'Workplace conditions and hazards' },
      { item: 'Accident and incident trends' },
      { item: 'Legal compliance status' },
      { item: 'Emergency preparedness' },
      { item: 'Contractor and visitor management' },
      { item: 'Employee consultation arrangements' },
    ],
    processSteps: [
      {
        title: 'Initial Meeting',
        description: 'We meet with you to understand your business, your operations, and your current health and safety arrangements. We discuss what level of support you need and how we can best help.',
      },
      {
        title: 'Baseline Review',
        description: 'We conduct an initial review of your premises and documentation to establish where you are now. This helps us identify priorities and plan the support you need.',
      },
      {
        title: 'Ongoing Support',
        description: 'We provide regular site visits at an agreed frequency, plus unlimited telephone and email support. You have a named consultant who knows your business and is always available when you need advice.',
      },
      {
        title: 'Proactive Guidance',
        description: 'We keep you informed of relevant legislative changes, help you prepare for audits or inspections, and provide timely reminders for reviews and renewals.',
      },
      {
        title: 'Continuous Improvement',
        description: 'We work with you to continuously improve your health and safety performance, addressing issues as they arise and helping you develop a positive safety culture.',
      },
    ],
    whatYouReceive: [
      { item: 'Named competent person for your organisation' },
      { item: 'Competent person appointment certificate' },
      { item: 'Regular scheduled site visits' },
      { item: 'Unlimited telephone and email support' },
      { item: 'Health and safety policy review and updates' },
      { item: 'Risk assessment reviews and development' },
      { item: 'Accident investigation support' },
      { item: 'Legislative updates relevant to your business' },
      { item: 'Preparation support for external audits' },
      { item: 'Annual health and safety report' },
      { item: 'Staff toolbox talks and briefings' },
      { item: 'Priority response for urgent matters' },
    ],
    premisesTypes: [
      { type: 'Small and medium-sized businesses' },
      { type: 'Manufacturing and engineering companies' },
      { type: 'Construction contractors' },
      { type: 'Warehouses and distribution centres' },
      { type: 'Retail businesses' },
      { type: 'Hospitality and leisure venues' },
      { type: 'Care providers and charities' },
      { type: 'Property management companies' },
      { type: 'Transport and logistics operators' },
      { type: 'Professional services firms' },
      { type: 'Educational establishments' },
      { type: 'Any business requiring competent person support' },
    ],
    benefits: [
      { benefit: 'Local consultants in Coalville & Melton Mowbray' },
      { benefit: 'Serving Leicester, Loughborough & the Midlands' },
      { benefit: 'Flexible packages to suit your needs' },
      { benefit: 'Regular site visits included' },
      { benefit: 'Telephone and email support' },
      { benefit: 'Policy review and development' },
      { benefit: 'Risk assessment assistance' },
      { benefit: 'Competent person certificate provided' },
    ],
    faqs: [
      {
        question: 'What is a competent person?',
        answer: 'Under the Management of Health and Safety at Work Regulations 1999, employers must appoint one or more competent persons to help them comply with health and safety law. A competent person has sufficient training, experience, and knowledge to provide sensible advice on health and safety matters.',
      },
      {
        question: 'Can I be my own competent person?',
        answer: 'Yes, if you have the necessary training, experience, and knowledge. However, many business owners find they lack the time or expertise to stay current with health and safety requirements while running their business. Appointing an external competent person can be more practical and cost-effective.',
      },
      {
        question: 'How many competent persons do I need?',
        answer: 'The regulations require "one or more" competent persons, with the number depending on your organisation size and complexity. Most small to medium businesses need only one competent person. Larger organisations or those with complex operations may benefit from multiple appointments.',
      },
      {
        question: 'How much does competent person services cost?',
        answer: 'The cost depends on the size of your organisation, the complexity of your operations, and how frequently you need site visits. Please contact us to discuss your requirements and we will provide a clear quotation.',
      },
      {
        question: 'Is there a contract or minimum term?',
        answer: 'We offer flexible arrangements. While many clients prefer the certainty of a monthly package, we do not require long-term commitments. You can adjust your support level as your business needs change.',
      },
      {
        question: 'How often will you visit our site?',
        answer: 'Visit frequency depends on your needs and package. Some clients need monthly visits, others quarterly. We agree a schedule that works for your business and can adjust it as your needs change. You can also request additional visits when needed.',
      },
      {
        question: 'What happens if we have an accident or incident?',
        answer: 'Call us immediately. We provide priority support for accidents and incidents, including guidance on immediate actions, RIDDOR reporting requirements, investigation support, and help implementing corrective measures.',
      },
      {
        question: 'Will you help us with accreditation schemes?',
        answer: 'Yes. Many accreditation schemes like CHAS, SafeContractor, and Constructionline require evidence of competent person arrangements. We can provide the documentation and support you need, and help you through the application process.',
      },
    ],
    showOnHomepage: true,
    homepageOrder: 5,
    seo: {
      metaTitle: 'Competent Person Services Leicestershire | Leicester, Coalville | Integral Safety',
      metaDescription: 'Appointed competent person services across Leicestershire & the East Midlands. Local H&S experts, flexible packages. Meet your legal obligations.',
    },
  },
  {
    title: 'Audits & Inspections',
    slug: 'auditing',
    icon: 'search',
    shortDescription: 'Independent health and safety audits and workplace inspections with prioritised recommendations.',
    heroHeading: 'Health & Safety Audits & Inspections in Leicestershire',
    heroSubheading: 'Independent health and safety audits from local consultants based in Coalville and Melton Mowbray. Serving businesses across Leicester, Loughborough, and the East Midlands.',
    whatWeAssess: [
      { item: 'Health and safety policy and organisation' },
      { item: 'Risk assessments and safe systems of work' },
      { item: 'Training records and competency' },
      { item: 'Accident and incident reporting' },
      { item: 'Emergency procedures and arrangements' },
      { item: 'Contractor management and control' },
      { item: 'Workplace conditions and housekeeping' },
      { item: 'Equipment maintenance and inspection records' },
      { item: 'Personal protective equipment provision' },
      { item: 'Fire safety arrangements' },
      { item: 'First aid provision and records' },
      { item: 'Consultation and communication with employees' },
    ],
    processSteps: [
      {
        title: 'Scope Agreement',
        description: 'We discuss your objectives for the audit - whether you need a comprehensive review or want to focus on specific areas. We agree the scope, timing, and what access we will need.',
      },
      {
        title: 'Document Review',
        description: 'We review your health and safety documentation including policies, risk assessments, procedures, and records. This can often be done before the site visit to make best use of on-site time.',
      },
      {
        title: 'Site Inspection',
        description: 'We conduct a thorough inspection of your workplace, observing work practices, checking conditions, and verifying that documented procedures are being followed in practice.',
      },
      {
        title: 'Staff Interviews',
        description: 'We speak with managers and employees at all levels to understand how health and safety works in practice. This helps identify gaps between policy and reality.',
      },
      {
        title: 'Report & Recommendations',
        description: 'We prepare a detailed report with our findings, highlighting areas of good practice as well as gaps. Recommendations are prioritised by risk so you know what to tackle first.',
      },
    ],
    whatYouReceive: [
      { item: 'Comprehensive written audit report' },
      { item: 'Executive summary for senior management' },
      { item: 'Detailed findings with photographic evidence' },
      { item: 'Prioritised recommendations by risk level' },
      { item: 'Compliance checklist against key legislation' },
      { item: 'Comparison against HSG65 best practice' },
      { item: 'Action plan template for tracking improvements' },
      { item: 'Verbal debrief with key personnel' },
      { item: 'Follow-up support to address findings' },
    ],
    premisesTypes: [
      { type: 'Manufacturing and production facilities' },
      { type: 'Warehouses and distribution centres' },
      { type: 'Construction sites and projects' },
      { type: 'Offices and commercial premises' },
      { type: 'Retail and hospitality venues' },
      { type: 'Care homes and healthcare settings' },
      { type: 'Educational establishments' },
      { type: 'Housing associations and property portfolios' },
      { type: 'Transport and logistics operations' },
      { type: 'Engineering and workshop environments' },
      { type: 'Food processing and manufacturing' },
      { type: 'Multi-site organisations' },
    ],
    benefits: [
      { benefit: 'Local auditors based in Leicestershire' },
      { benefit: 'Serving Leicester, Loughborough & the Midlands' },
      { benefit: 'Independent, objective assessment' },
      { benefit: 'Aligned with HSG65 and UK legislation' },
      { benefit: 'Identify gaps and areas for improvement' },
      { benefit: 'Detailed written reports provided' },
      { benefit: 'Prioritised recommendations' },
      { benefit: 'Follow-up support available' },
    ],
    faqs: [
      {
        question: 'How often should we have a health and safety audit?',
        answer: 'This depends on your organisation size, risk profile, and sector requirements. Annual audits are common, but high-risk industries or those with accreditation requirements may need more frequent assessment. We can advise on an appropriate frequency for your circumstances.',
      },
      {
        question: 'What is the difference between an audit and an inspection?',
        answer: 'An audit is a systematic examination of your health and safety management system - policies, procedures, and their implementation. An inspection is a physical examination of the workplace to identify hazards and verify that controls are in place. Both are valuable and complementary.',
      },
      {
        question: 'Will an audit be disruptive to our operations?',
        answer: 'We work around your operational requirements and aim to minimise disruption. Document reviews can often be done off-site, and workplace observations are conducted without interrupting work unless safety concerns require immediate action.',
      },
      {
        question: 'How much does a health and safety audit cost?',
        answer: 'The cost depends on the scope of the audit, the size of your organisation, and the number of sites involved. Please contact us to discuss your requirements and we will provide a clear quotation.',
      },
      {
        question: 'What is HSG65?',
        answer: 'HSG65 is the HSE\'s guidance document "Managing for Health and Safety". It provides a framework for effective health and safety management based on Plan-Do-Check-Act. We use this as a benchmark for assessing your management system.',
      },
      {
        question: 'Can you audit against specific standards like ISO 45001?',
        answer: 'Yes, we can audit against ISO 45001, OHSAS 18001 (legacy), or other specific standards if required. We can also conduct gap analyses to help you prepare for certification audits by accredited bodies.',
      },
      {
        question: 'What happens after the audit?',
        answer: 'We provide a detailed report with prioritised recommendations. We can then support you to address the findings - whether that means developing new procedures, providing training, or carrying out follow-up inspections to verify improvements.',
      },
      {
        question: 'Do you offer regular inspection programmes?',
        answer: 'Yes, many clients find value in regular scheduled inspections - monthly, quarterly, or at whatever frequency suits their needs. This provides ongoing assurance and helps identify issues before they become problems.',
      },
    ],
    showOnHomepage: true,
    homepageOrder: 6,
    seo: {
      metaTitle: 'Health & Safety Audits Leicestershire | Leicester, Loughborough | Integral Safety',
      metaDescription: 'Independent H&S audits and workplace inspections across Leicestershire & the East Midlands. Local auditors, HSG65 aligned, practical recommendations. Book your audit.',
    },
  },
  {
    title: 'Face-Fit Testing',
    slug: 'face-fit-testing',
    icon: 'users',
    shortDescription: 'On-site face-fit testing with instant certification. Qualitative testing to HSE standards.',
    heroHeading: 'Face-Fit Testing Across Leicestershire & the Midlands',
    heroSubheading: 'On-site face-fit testing from local professionals based in Coalville. We travel to your premises anywhere in Leicester, Loughborough, Hinckley, and the wider East Midlands.',
    whatWeAssess: [
      { item: 'Mask seal and facial fit' },
      { item: 'Correct mask size selection' },
      { item: 'Facial hair that may affect seal' },
      { item: 'Spectacles or eyewear compatibility' },
      { item: 'Correct donning and doffing technique' },
      { item: 'Strap adjustment and positioning' },
      { item: 'Mask condition and serviceability' },
      { item: 'User comfort and tolerance' },
      { item: 'Breathing resistance acceptability' },
      { item: 'Communication ability while wearing RPE' },
    ],
    processSteps: [
      {
        title: 'Pre-Test Preparation',
        description: 'We confirm the mask types to be tested and check that participants have not eaten, drunk (except water), or smoked for at least 15 minutes before testing. We also check for facial hair that might affect the seal.',
      },
      {
        title: 'Sensitivity Test',
        description: 'Before the main fit test, we conduct a sensitivity test to ensure the wearer can detect the test solution. This confirms they will be able to identify any leakage during the fit test.',
      },
      {
        title: 'Mask Fitting',
        description: 'We help the wearer select the correct size mask and ensure it is fitted correctly according to the manufacturer\'s instructions. We check strap positioning and seal quality.',
      },
      {
        title: 'Fit Test Exercises',
        description: 'The wearer performs a series of exercises while wearing the mask inside a test hood - including normal breathing, deep breathing, head movements, talking, and bending over. We check for any taste of the test solution.',
      },
      {
        title: 'Certification',
        description: 'If the test is passed, we issue an instant certificate recording the mask make, model, and size that fits. If failed, we can try alternative masks or sizes until we find one that fits correctly.',
      },
    ],
    whatYouReceive: [
      { item: 'Qualitative face-fit testing to HSE INDG479 protocol' },
      { item: 'Individual fit test certificate for each person tested' },
      { item: 'Record of mask make, model, and size that fits' },
      { item: 'Advice on correct mask donning and doffing' },
      { item: 'Guidance on mask care and maintenance' },
      { item: 'Information on when re-testing is required' },
      { item: 'Summary report for your records' },
      { item: 'Advice on alternative masks if initial test fails' },
    ],
    premisesTypes: [
      { type: 'Construction sites and contractors' },
      { type: 'Manufacturing and engineering' },
      { type: 'Pharmaceutical and chemical industries' },
      { type: 'Healthcare and dental practices' },
      { type: 'Laboratories and research facilities' },
      { type: 'Spray painters and bodyshops' },
      { type: 'Asbestos removal contractors' },
      { type: 'Woodworking and joinery workshops' },
      { type: 'Foundries and metal processing' },
      { type: 'Food manufacturing' },
      { type: 'Agricultural and farming operations' },
      { type: 'Any workplace requiring tight-fitting RPE' },
    ],
    benefits: [
      { benefit: 'On-site testing anywhere in Leicestershire' },
      { benefit: 'Local testers based in Coalville' },
      { benefit: 'Covering Leicester, Loughborough, Hinckley & beyond' },
      { benefit: 'Instant pass/fail certification provided' },
      { benefit: 'Qualitative testing to HSE standards' },
      { benefit: 'Test multiple mask types in one session' },
      { benefit: 'Competitive rates for group testing' },
      { benefit: 'Flexible scheduling to suit your operations' },
    ],
    faqs: [
      {
        question: 'How long does face-fit testing take?',
        answer: 'Each individual test takes approximately 15-20 minutes. We can typically test 3-4 people per hour, depending on how many mask types need to be tested. For larger groups, we recommend scheduling a dedicated testing day.',
      },
      {
        question: 'Can you test disposable masks?',
        answer: 'Yes, we can test disposable FFP masks as well as reusable half-masks and full-face masks. Each mask type requires a separate test, so if workers use both disposable FFP3s and reusable half-masks, both must be tested.',
      },
      {
        question: 'How often should face-fit testing be repeated?',
        answer: 'There is no fixed legal requirement for re-testing frequency. However, testing should be repeated if the wearer has significant weight changes, dental work affecting facial shape, facial scarring, or if a different mask is selected. Annual re-testing is considered good practice.',
      },
      {
        question: 'How much does face-fit testing cost?',
        answer: 'The cost depends on the number of people to be tested and the number of mask types. We offer competitive rates for group testing. Please contact us for a quote tailored to your requirements.',
      },
      {
        question: 'What if someone fails the fit test?',
        answer: 'If someone fails with one mask, we can try alternative masks, sizes, or styles until we find one that fits correctly. Different face shapes suit different masks, so failure with one type does not mean all masks will fail.',
      },
      {
        question: 'Can people with beards be fit tested?',
        answer: 'Facial hair in the seal area of a tight-fitting mask will prevent an adequate seal and the person will fail the fit test. Workers requiring RPE must be clean-shaven in the seal area. Alternatives like powered air respirators may be suitable for those who cannot shave.',
      },
      {
        question: 'What is the difference between qualitative and quantitative testing?',
        answer: 'Qualitative testing uses a taste solution to detect leakage - if you taste the solution, the mask has failed. Quantitative testing uses electronic equipment to measure the actual amount of leakage. We provide qualitative testing which is accepted by the HSE and suitable for most disposable and half-mask respirators.',
      },
      {
        question: 'Do you provide the masks for testing?',
        answer: 'We test the masks your workers will actually be using, so you should provide these. If you are unsure which masks to purchase, we can advise on suitable options for your application before the testing session.',
      },
    ],
    showOnHomepage: true,
    homepageOrder: 7,
    seo: {
      metaTitle: 'Face-Fit Testing Leicestershire | Leicester, Coalville, Loughborough | Integral Safety',
      metaDescription: 'On-site face-fit testing across Leicestershire, Leicester & the East Midlands. Qualitative RPE testing with instant certification. Local testers, competitive group rates.',
    },
  },
  {
    title: 'Accreditation Support',
    slug: 'accreditation-support',
    icon: 'award',
    shortDescription: 'Help achieving CHAS, SafeContractor, Constructionline and other H&S accreditations.',
    heroHeading: 'Health & Safety Accreditation Support in Leicestershire',
    heroSubheading: 'Local consultants helping Leicestershire and East Midlands businesses achieve CHAS, SafeContractor, Constructionline, and other accreditations. From gap analysis to successful assessment.',
    whatWeAssess: [
      { item: 'Current health and safety documentation' },
      { item: 'Policy statements and commitments' },
      { item: 'Risk assessments and method statements' },
      { item: 'Training records and competency evidence' },
      { item: 'Insurance certificates and coverage' },
      { item: 'Accident and incident records' },
      { item: 'Subcontractor management procedures' },
      { item: 'Consultation and communication arrangements' },
      { item: 'Monitoring and review processes' },
      { item: 'Previous assessment feedback' },
    ],
    processSteps: [
      {
        title: 'Initial Review',
        description: 'We assess your current health and safety arrangements against scheme requirements. We identify gaps and develop a clear action plan to achieve accreditation.',
      },
      {
        title: 'Documentation Development',
        description: 'We help you create or update the documentation you need - policies, risk assessments, method statements, and procedures. Everything is tailored to your business, not generic templates.',
      },
      {
        title: 'Evidence Gathering',
        description: 'We help you compile the evidence portfolio required for assessment - training records, insurance certificates, inspection records, and other supporting documentation.',
      },
      {
        title: 'Pre-Assessment Review',
        description: 'Before you submit, we conduct a thorough pre-assessment review to identify and resolve any remaining issues. This maximises your chances of first-time success.',
      },
      {
        title: 'Submission & Support',
        description: 'We guide you through the submission process and remain available to respond to any assessor queries. We support you through to successful accreditation.',
      },
    ],
    whatYouReceive: [
      { item: 'Gap analysis against scheme requirements' },
      { item: 'Clear action plan with priorities' },
      { item: 'Health and safety policy development' },
      { item: 'Risk assessments tailored to your activities' },
      { item: 'Method statements and safe systems of work' },
      { item: 'Documentation review and refinement' },
      { item: 'Evidence portfolio compilation' },
      { item: 'Pre-assessment review and feedback' },
      { item: 'Submission guidance and support' },
      { item: 'Response to assessor queries' },
      { item: 'Ongoing support for annual renewals' },
    ],
    premisesTypes: [
      { type: 'Construction contractors and subcontractors' },
      { type: 'Maintenance and facilities management' },
      { type: 'Electrical and mechanical contractors' },
      { type: 'Roofing and building envelope specialists' },
      { type: 'Groundworks and civil engineering' },
      { type: 'Cleaning and specialist services' },
      { type: 'Transport and logistics providers' },
      { type: 'Manufacturing suppliers' },
      { type: 'Professional services firms' },
      { type: 'Any business requiring contractor accreditation' },
    ],
    benefits: [
      { benefit: 'Local support from Leicestershire consultants' },
      { benefit: 'Helping Midlands contractors win more work' },
      { benefit: 'CHAS, SafeContractor, Constructionline' },
      { benefit: 'SMAS, Achilles, SSIP schemes' },
      { benefit: 'Gap analysis and action planning' },
      { benefit: 'Documentation development' },
      { benefit: 'Pre-assessment reviews' },
      { benefit: 'High first-time pass rate' },
    ],
    faqs: [
      {
        question: 'How long does it take to achieve accreditation?',
        answer: 'This depends on your starting point. Organisations with good existing systems may only need minor refinements. Those starting from scratch will need more time to develop documentation and embed new practices. Typically, we can help clients achieve accreditation within 4-12 weeks.',
      },
      {
        question: 'Which accreditation scheme should I choose?',
        answer: 'This depends on your clients\' requirements and the sectors you work in. SSIP mutual recognition means that achieving one member scheme may satisfy clients requiring another. We can advise on the most appropriate scheme for your circumstances.',
      },
      {
        question: 'What happens if we fail the assessment?',
        answer: 'Most schemes allow resubmission after addressing the issues identified. Our pre-assessment review process is designed to identify and resolve potential problems before you submit, giving you the best chance of first-time success.',
      },
      {
        question: 'How much does accreditation support cost?',
        answer: 'The cost depends on your starting point and which scheme you are applying for. Please contact us to discuss your requirements and we will provide a clear quotation.',
      },
      {
        question: 'What is SSIP and mutual recognition?',
        answer: 'SSIP (Safety Schemes in Procurement) is a forum of health and safety assessment schemes that have agreed mutual recognition. This means if you achieve one SSIP member scheme (like CHAS), other clients requiring a different SSIP scheme should accept it. This saves you paying for multiple similar assessments.',
      },
      {
        question: 'Do you help with renewals and annual reviews?',
        answer: 'Yes. Most schemes require annual renewal and periodic reassessment. We can support you through renewals, ensuring your documentation stays current and addressing any new requirements.',
      },
      {
        question: 'Can you help with ISO 45001 certification?',
        answer: 'Yes. While ISO 45001 is more comprehensive than contractor schemes, we can help you develop the management system documentation and prepare for certification audits. We can also conduct internal audits and gap analyses.',
      },
      {
        question: 'What if we need accreditation urgently?',
        answer: 'We understand that sometimes you need accreditation quickly to win a contract. We can often fast-track the process for urgent cases. Call us to discuss your deadline and we will advise on what is achievable.',
      },
    ],
    showOnHomepage: true,
    homepageOrder: 8,
    seo: {
      metaTitle: 'H&S Accreditation Support Leicestershire | CHAS, SafeContractor | Integral Safety',
      metaDescription: 'Accreditation support for Leicestershire businesses. CHAS, SafeContractor, Constructionline, SMAS help from local consultants. Gap analysis to successful assessment.',
    },
  },
  {
    title: 'Accident Investigation',
    slug: 'accident-investigation',
    icon: 'search',
    shortDescription: 'Professional accident investigation with root cause analysis and RIDDOR reporting support.',
    heroHeading: 'Accident Investigation Services in Leicestershire',
    heroSubheading: 'Local accident investigation experts serving Leicester, Loughborough, and the East Midlands. Thorough investigations, root cause analysis, and RIDDOR reporting support.',
    whatWeAssess: [
      { item: 'The sequence of events leading to the incident' },
      { item: 'Immediate and underlying causes' },
      { item: 'Equipment, machinery, and work environment' },
      { item: 'Safe systems of work and procedures' },
      { item: 'Training and competency of those involved' },
      { item: 'Supervision and management oversight' },
      { item: 'Risk assessments and their adequacy' },
      { item: 'Previous similar incidents or near-misses' },
      { item: 'Human factors and organisational culture' },
      { item: 'Communication and information flow' },
    ],
    processSteps: [
      {
        title: 'Immediate Response',
        description: 'When you call us, we provide immediate guidance on preserving evidence, securing the scene, and meeting any reporting obligations. For serious incidents, we can attend site the same day.',
      },
      {
        title: 'Evidence Gathering',
        description: 'We systematically collect evidence including photographs, measurements, documents, and physical items. We ensure evidence is properly preserved and documented for any subsequent proceedings.',
      },
      {
        title: 'Witness Interviews',
        description: 'We conduct structured interviews with witnesses, the injured person (if appropriate), supervisors, and managers. Our approach is thorough but sensitive, focusing on understanding what happened rather than apportioning blame.',
      },
      {
        title: 'Root Cause Analysis',
        description: 'We apply recognised techniques to identify not just what happened, but why it happened. We look beyond immediate causes to find the underlying system failures that allowed the incident to occur.',
      },
      {
        title: 'Report & Recommendations',
        description: 'We provide a comprehensive investigation report with clear findings and practical recommendations. Actions are prioritised by risk and designed to prevent recurrence.',
      },
    ],
    whatYouReceive: [
      { item: 'Immediate telephone guidance when incidents occur' },
      { item: 'On-site investigation by experienced professionals' },
      { item: 'Comprehensive written investigation report' },
      { item: 'Root cause analysis using recognised techniques' },
      { item: 'Witness interview records and statements' },
      { item: 'Photographic and documentary evidence pack' },
      { item: 'RIDDOR reporting support and guidance' },
      { item: 'Prioritised corrective action recommendations' },
      { item: 'Support with HSE liaison if required' },
      { item: 'Lessons learned briefing for management' },
      { item: 'Follow-up to verify actions completed' },
    ],
    premisesTypes: [
      { type: 'Construction sites and projects' },
      { type: 'Manufacturing and engineering facilities' },
      { type: 'Warehouses and distribution centres' },
      { type: 'Transport and logistics operations' },
      { type: 'Retail and hospitality venues' },
      { type: 'Care homes and healthcare settings' },
      { type: 'Educational establishments' },
      { type: 'Offices and commercial premises' },
      { type: 'Agricultural and rural businesses' },
      { type: 'Any workplace where incidents occur' },
    ],
    benefits: [
      { benefit: 'Local investigators in Leicestershire' },
      { benefit: 'Serving Leicester, Loughborough & the Midlands' },
      { benefit: 'Systematic root cause analysis' },
      { benefit: 'Experienced investigation professionals' },
      { benefit: 'RIDDOR reporting assistance' },
      { benefit: 'Clear corrective action plans' },
      { benefit: 'Support with HSE liaison if required' },
      { benefit: 'Rapid response when incidents occur' },
    ],
    faqs: [
      {
        question: 'When should an accident be investigated?',
        answer: 'All accidents and near-misses should be investigated, though the depth of investigation should be proportionate to the actual or potential severity. Serious incidents require thorough investigation; minor incidents may need only brief review. The key is to identify and address causes before a more serious incident occurs.',
      },
      {
        question: 'What is a RIDDOR reportable injury?',
        answer: 'Specified injuries that must be reported include fractures (except to fingers, thumbs and toes), amputations, serious burns, loss of consciousness, and injuries requiring hospital treatment for more than 24 hours. Any injury causing more than 7 consecutive days off work must also be reported.',
      },
      {
        question: 'Can investigation findings be used against us?',
        answer: 'Investigation reports can potentially be disclosed in legal proceedings. We can advise on legal privilege considerations and help you balance transparency with appropriate protection. The benefits of thorough investigation and genuine improvement far outweigh the risks of not investigating.',
      },
      {
        question: 'How much does accident investigation cost?',
        answer: 'The cost depends on the complexity of the incident and the depth of investigation required. Please contact us to discuss your situation and we will provide a clear quotation.',
      },
      {
        question: 'How quickly can you respond?',
        answer: 'We understand that time is critical after an incident. We can provide immediate telephone guidance and, for serious incidents, can usually attend site the same day or next day. Call us as soon as possible after an incident occurs.',
      },
      {
        question: 'What is root cause analysis?',
        answer: 'Root cause analysis goes beyond the immediate cause of an incident to identify the underlying failures in systems, processes, or culture that allowed it to happen. By addressing root causes, you prevent not just this incident recurring, but other incidents with similar underlying causes.',
      },
      {
        question: 'Should we wait for the HSE before investigating?',
        answer: 'No. You should begin your own investigation promptly while evidence is fresh. If the HSE is investigating, we can advise on how to conduct your internal investigation appropriately and how to work constructively with the HSE inspector.',
      },
      {
        question: 'Do you investigate near-misses?',
        answer: 'Yes, and we strongly encourage it. Near-misses are free lessons - they reveal hazards and control failures without anyone being hurt. Investigating near-misses helps prevent future incidents that could result in serious injury.',
      },
    ],
    showOnHomepage: false,
    homepageOrder: 9,
    seo: {
      metaTitle: 'Accident Investigation Leicestershire | RIDDOR Support | Integral Safety',
      metaDescription: 'Professional accident investigation across Leicestershire & the East Midlands. Local experts for root cause analysis, witness interviews, RIDDOR reporting. Call 01530 382150.',
    },
  },
  {
    title: 'HAVS Testing',
    slug: 'havs-testing',
    icon: 'shield',
    shortDescription: 'Hand-arm vibration syndrome testing and vibration risk assessments to protect your workers.',
    heroHeading: 'HAVS Testing & Vibration Assessment in Leicestershire',
    heroSubheading: 'Local HAVS testing and vibration risk assessment services for businesses across Leicester, Loughborough, Coalville, and the East Midlands. Protect your workforce from hand-arm vibration syndrome.',
    whatWeAssess: [
      { item: 'Vibration magnitude of tools and equipment' },
      { item: 'Daily exposure durations for each worker' },
      { item: 'Trigger time calculations' },
      { item: 'Exposure against action and limit values' },
      { item: 'Current control measures in place' },
      { item: 'Tool selection and maintenance practices' },
      { item: 'Work scheduling and job rotation' },
      { item: 'Worker symptoms and health surveillance records' },
      { item: 'Training and awareness levels' },
      { item: 'PPE provision (anti-vibration gloves)' },
    ],
    processSteps: [
      {
        title: 'Initial Discussion',
        description: 'We discuss your operations to understand which workers use vibrating tools, what equipment they use, and for how long. This helps us plan the assessment approach.',
      },
      {
        title: 'Equipment Assessment',
        description: 'We assess the vibration levels of your tools and equipment. Where manufacturer data is available, we use this; otherwise, we can arrange vibration magnitude testing.',
      },
      {
        title: 'Exposure Calculations',
        description: 'We calculate daily vibration exposure for each worker or job role, comparing against the exposure action value (EAV) and exposure limit value (ELV) set in the regulations.',
      },
      {
        title: 'Risk Assessment',
        description: 'We prepare a comprehensive vibration risk assessment identifying who is at risk, current exposure levels, and what control measures are needed to reduce exposure.',
      },
      {
        title: 'Health Surveillance Guidance',
        description: 'We advise on setting up appropriate health surveillance for workers exposed above the action value, including questionnaires, Tier assessments, and occupational health referrals.',
      },
    ],
    whatYouReceive: [
      { item: 'Vibration risk assessment to regulatory standards' },
      { item: 'Tool and equipment vibration data compilation' },
      { item: 'Individual worker exposure calculations' },
      { item: 'Trigger time guidance for each tool' },
      { item: 'Control measures recommendations' },
      { item: 'Health surveillance programme guidance' },
      { item: 'HAVS screening questionnaire templates' },
      { item: 'Worker information and awareness materials' },
      { item: 'Tool selection and purchasing advice' },
      { item: 'Ongoing support and annual reviews' },
    ],
    premisesTypes: [
      { type: 'Construction sites and contractors' },
      { type: 'Engineering and fabrication workshops' },
      { type: 'Automotive repair and bodyshops' },
      { type: 'Foundries and metal working' },
      { type: 'Quarries and mineral extraction' },
      { type: 'Forestry and arboriculture' },
      { type: 'Garden maintenance and landscaping' },
      { type: 'Utilities and infrastructure maintenance' },
      { type: 'Manufacturing and production' },
      { type: 'Any workplace using vibrating tools' },
    ],
    benefits: [
      { benefit: 'Local testing across Leicestershire' },
      { benefit: 'Serving Leicester, Loughborough & the Midlands' },
      { benefit: 'Vibration exposure assessments' },
      { benefit: 'Equipment vibration testing' },
      { benefit: 'Risk assessment development' },
      { benefit: 'Health surveillance guidance' },
      { benefit: 'HAVS awareness training' },
      { benefit: 'Compliance documentation' },
    ],
    faqs: [
      {
        question: 'What is the exposure limit for vibration?',
        answer: 'The daily exposure limit value (ELV) is 5 m/s² A(8), which must not be exceeded. The exposure action value (EAV) is 2.5 m/s² A(8), above which you must take action to reduce exposure. A(8) means the exposure standardised to an 8-hour day.',
      },
      {
        question: 'How do I know if my workers are at risk?',
        answer: 'If your workers regularly use powered hand tools, particularly hammer-action or rotating tools, they may be at risk. A risk assessment considering the vibration magnitude of your tools and the duration of use will determine whether exposure is likely to exceed action or limit values.',
      },
      {
        question: 'What is health surveillance for HAVS?',
        answer: 'Health surveillance is a programme of health checks to detect early signs of HAVS before permanent damage occurs. It includes initial questionnaires, annual reviews, and referral to an occupational health professional if symptoms are reported. We can advise on setting up an appropriate programme.',
      },
      {
        question: 'How much does HAVS assessment cost?',
        answer: 'The cost depends on the number of tools to be assessed and the complexity of your operations. Please contact us to discuss your requirements and we will provide a clear quotation.',
      },
      {
        question: 'What is HAVS and why is it serious?',
        answer: 'Hand-Arm Vibration Syndrome is a painful and disabling condition affecting the blood vessels, nerves, muscles, and joints of the hand, wrist, and arm. It is caused by regular exposure to vibration from powered hand tools. Once damage occurs, it is permanent and can significantly affect quality of life.',
      },
      {
        question: 'Which tools cause the most vibration?',
        answer: 'Generally, hammer-action tools like breakers, chipping hammers, and needle scalers produce the highest vibration. However, grinders, sanders, polishers, chainsaws, and many other tools can also cause harmful exposure, especially with prolonged use.',
      },
      {
        question: 'Do anti-vibration gloves work?',
        answer: 'Anti-vibration gloves can provide some protection but should not be relied upon as the primary control measure. The protection they offer varies and is often less than claimed. Reducing exposure time and using lower-vibration tools are more effective controls.',
      },
      {
        question: 'What are trigger times?',
        answer: 'Trigger time is the maximum time a worker can use a particular tool before reaching the exposure action value or limit value. We calculate trigger times for your tools to help you manage daily exposure and plan work schedules.',
      },
    ],
    showOnHomepage: false,
    homepageOrder: 10,
    seo: {
      metaTitle: 'HAVS Testing Leicestershire | Leicester, Loughborough | Integral Safety',
      metaDescription: 'HAVS testing and vibration risk assessments across Leicestershire & the East Midlands. Local consultants, equipment testing, health surveillance guidance. Protect your workers.',
    },
  },
]

async function seed() {
  console.log('Starting seed...')

  const payload = await getPayload({ config })

  // Clear existing services
  console.log('Clearing existing services...')
  const existing = await payload.find({
    collection: 'services',
    limit: 100,
  })

  for (const service of existing.docs) {
    await payload.delete({
      collection: 'services',
      id: service.id,
    })
  }

  // Create services
  console.log('Creating services...')
  for (const service of servicesData) {
    try {
      await payload.create({
        collection: 'services',
        data: service,
      })
      console.log(`Created: ${service.title}`)
    } catch (error) {
      console.error(`Error creating ${service.title}:`, error)
    }
  }

  console.log('Seed completed!')
  process.exit(0)
}

seed().catch((error) => {
  console.error('Seed error:', error)
  process.exit(1)
})
