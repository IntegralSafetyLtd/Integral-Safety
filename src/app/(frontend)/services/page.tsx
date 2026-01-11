import { Metadata } from 'next'
import Link from 'next/link'
import { Flame, ClipboardList, Shield, Video, Search, HardHat, Award, Users, Vibrate, Check } from 'lucide-react'
import { CTA } from '@/components/sections'
import { BreadcrumbSchema } from '@/components/schema'

export const metadata: Metadata = {
  title: 'Health & Safety Services | Leicestershire | Integral Safety',
  description: 'Comprehensive health and safety services in Leicestershire. Fire risk assessments, H&S consultancy, drone surveys, face-fit testing, auditing, HAVS testing, and competent person services.',
}

const services = [
  {
    icon: Flame,
    title: 'Fire Risk Assessments',
    description: 'PAS 79 compliant fire risk assessments for commercial buildings, HMOs, and housing stock. We identify fire hazards, evaluate existing controls, and provide prioritised action plans with clear recommendations.',
    href: '/services/fire-risk-assessments',
  },
  {
    icon: ClipboardList,
    title: 'Health & Safety Consultancy',
    description: 'Expert H&S support without the overhead of a full-time safety manager. Policy development, risk assessments, safe systems of work, and ongoing guidance tailored to your industry and operations.',
    href: '/services/consultancy',
  },
  {
    icon: Shield,
    title: 'Face-Fit Testing',
    description: 'Qualitative RPE testing to HSE protocols, ensuring masks seal correctly against your workers\' faces. On-site testing, instant certification, and guidance on selecting the right respiratory protection.',
    href: '/services/face-fit-testing',
  },
  {
    icon: Video,
    title: 'Drone Surveys',
    description: 'Aerial inspections for roofs, chimneys, and structures. Get 4K video and 8MP imagery without scaffolding costs or sending workers to dangerous heights.',
    href: '/services/work-at-height-surveys',
  },
  {
    icon: Search,
    title: 'Auditing & Inspections',
    description: 'Independent workplace safety audits aligned with HSG65 and UK legislation. We identify compliance gaps, benchmark your performance, and provide practical recommendations for improvement.',
    href: '/services/auditing',
  },
  {
    icon: HardHat,
    title: 'Accident Investigation',
    description: 'Thorough investigation of workplace incidents and near-misses. We apply root cause analysis techniques to understand why accidents happen and help you implement effective prevention measures.',
    href: '/services/accident-investigation',
  },
  {
    icon: Award,
    title: 'Accreditation Support',
    description: 'Expert guidance through ISO 45001, SafeContractor, CHAS, Constructionline, and other accreditation schemes. We help you achieve and maintain the certifications your business needs.',
    href: '/services/accreditation-support',
  },
  {
    icon: Users,
    title: 'Competent Person Services',
    description: 'Act as your appointed competent person for health and safety compliance. Regular site visits, ongoing telephone and email support, and a reliable resource when you need expert advice.',
    href: '/services/competent-person',
  },
  {
    icon: Vibrate,
    title: 'HAVS Testing',
    description: 'Hand-Arm Vibration Syndrome health surveillance to meet the Control of Vibration at Work Regulations. Screening questionnaires, Tier assessments, and practical guidance on reducing exposure.',
    href: '/services/havs-testing',
  },
]

const benefits = [
  'Practical advice that works in the real world, not just on paper',
  'No unnecessary paperwork or over-complicated systems',
  'We explain the "why" so you understand your obligations',
  'Flexible support from one-off projects to retained contracts',
  'Direct access to experienced consultants, not call centres',
  'Local presence with offices in Coalville and Melton Mowbray',
]

export default function ServicesPage() {
  const breadcrumbItems = [
    { name: 'Home', url: 'https://integralsafety.co.uk' },
    { name: 'Services', url: 'https://integralsafety.co.uk/services' },
  ]

  return (
    <>
      <BreadcrumbSchema items={breadcrumbItems} />

      {/* Hero */}
      <section className="py-16 md:py-20 bg-white">
        <div className="container">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <p className="section-eyebrow">Our Services</p>
              <h1 className="text-hero text-navy-900 mb-6">
                Health &amp; Safety Services That Actually Work
              </h1>
              <p className="text-body-lg text-gray-600 mb-4">
                From fire risk assessments to ongoing consultancy support, we provide comprehensive
                health and safety services to businesses across Leicestershire and the Midlands.
              </p>
              <p className="text-gray-600">
                Our approach is simple: understand your business, identify the real risks, and
                provide practical solutions you can actually implement. No jargon, no unnecessary
                bureaucracy - just sensible advice that keeps your people safe.
              </p>
            </div>
            <div className="bg-cream rounded-card p-8">
              <h3 className="font-heading text-xl font-semibold text-navy-900 mb-6">
                Why Businesses Choose Integral Safety
              </h3>
              <ul className="space-y-4">
                {benefits.map((benefit) => (
                  <li key={benefit} className="flex items-start gap-3">
                    <Check className="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" />
                    <span className="text-gray-600">{benefit}</span>
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </div>
      </section>

      {/* Services Grid */}
      <section className="py-16 bg-cream">
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="section-title">Our Services</h2>
            <p className="section-subtitle">
              Click on any service to learn more about how we can help your business.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {services.map((service) => {
              const Icon = service.icon
              return (
                <Link
                  key={service.title}
                  href={service.href}
                  className="group bg-white rounded-card p-6 border border-transparent transition-all duration-300 hover:shadow-card hover:-translate-y-1 hover:border-orange-100 flex flex-col"
                >
                  <div className="w-12 h-12 bg-cream rounded-icon flex items-center justify-center mb-4 transition-colors group-hover:bg-orange-100">
                    <Icon className="w-6 h-6 text-navy-700 group-hover:text-orange-600 transition-colors" />
                  </div>
                  <h2 className="text-card-title text-navy-900 mb-2 group-hover:text-orange-600 transition-colors">
                    {service.title}
                  </h2>
                  <p className="text-gray-600 text-sm leading-relaxed flex-grow">
                    {service.description}
                  </p>
                  <div className="mt-4 pt-4 border-t border-navy-100">
                    <span className="text-orange-500 text-sm font-semibold group-hover:text-orange-600 transition-colors">
                      Learn more &rarr;
                    </span>
                  </div>
                </Link>
              )
            })}
          </div>
        </div>
      </section>

      {/* Training CTA */}
      <section className="py-16 bg-white">
        <div className="container">
          <div className="bg-navy-900 rounded-card p-8 md:p-12 text-white text-center">
            <h2 className="font-heading text-2xl md:text-3xl font-semibold mb-4">
              Looking for Health &amp; Safety Training?
            </h2>
            <p className="text-white/80 mb-6 max-w-2xl mx-auto">
              As an IOSH Approved Training Provider, we deliver accredited courses including
              IOSH Managing Safely, alongside essential workplace training in fire awareness,
              manual handling, COSHH, and more.
            </p>
            <Link href="/training" className="btn-primary bg-orange-500 hover:bg-orange-600">
              View Training Courses
            </Link>
          </div>
        </div>
      </section>

      {/* Sector Expertise */}
      <section className="py-16 bg-cream">
        <div className="container">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="section-title mb-6">Specialist Housing Sector Expertise</h2>
              <p className="text-gray-600 mb-4">
                We&apos;ve spent over 15 years working with housing associations and almshouse charities.
                We understand the unique challenges of managing health and safety across property
                portfolios, from fire safety in communal areas to contractor management.
              </p>
              <p className="text-gray-600 mb-6">
                If you&apos;re a housing provider, we can offer tailored support that addresses your
                specific regulatory requirements and operational challenges.
              </p>
              <Link href="/sectors/housing-almshouses" className="btn-primary">
                Learn About Our Housing Expertise
              </Link>
            </div>
            <div className="bg-white rounded-card p-8">
              <h3 className="font-heading text-lg font-semibold text-navy-900 mb-4">
                Industries We Serve
              </h3>
              <div className="grid grid-cols-2 gap-3">
                {[
                  'Housing Associations',
                  'Almshouse Charities',
                  'Construction',
                  'Manufacturing',
                  'Hospitality',
                  'Retail',
                  'Care Homes',
                  'Education',
                ].map((sector) => (
                  <div key={sector} className="flex items-center gap-2">
                    <Check className="w-4 h-4 text-green-500 flex-shrink-0" />
                    <span className="text-gray-600 text-sm">{sector}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      <CTA />
    </>
  )
}
