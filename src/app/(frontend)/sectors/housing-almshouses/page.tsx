import { Metadata } from 'next'
import Link from 'next/link'
import { Home, Flame, Shield, ClipboardList, Users, Check, Video, AlertTriangle, HelpCircle } from 'lucide-react'
import { CTA } from '@/components/sections'
import { BreadcrumbSchema, FAQSchema } from '@/components/schema'

export const metadata: Metadata = {
  title: 'Housing Associations & Almshouses | Health & Safety | Integral Safety',
  description: 'Specialist health and safety consultancy for housing associations and almshouse charities. Fire risk assessments, compliance support, and practical guidance for managing property portfolios safely.',
}

const services = [
  {
    icon: Flame,
    title: 'Fire Risk Assessments',
    description: 'PAS 79 compliant fire risk assessments for communal areas, individual dwellings, and HMOs. We understand the specific fire safety requirements for social housing.',
    href: '/services/fire-risk-assessments',
  },
  {
    icon: ClipboardList,
    title: 'Health & Safety Management',
    description: 'Ongoing support to manage your health and safety obligations. From policy development to contractor management, we help you stay compliant.',
    href: '/services/consultancy',
  },
  {
    icon: Video,
    title: 'Drone Roof Surveys',
    description: 'Safe, cost-effective aerial inspections of roofs, chimneys, and external structures without scaffolding or putting workers at height.',
    href: '/services/work-at-height-surveys',
  },
  {
    icon: Shield,
    title: 'Competent Person Service',
    description: 'Act as your appointed competent person for health and safety, providing the expertise your organisation needs without the cost of a full-time employee.',
    href: '/services/competent-person',
  },
]

const challenges = [
  {
    title: 'Fire Safety in Communal Areas',
    description: 'Ensuring communal hallways, stairwells, and facilities meet fire safety standards while managing resident behaviour and storage.',
  },
  {
    title: 'Contractor Management',
    description: 'Overseeing health and safety standards for maintenance contractors, repairs teams, and major works programmes.',
  },
  {
    title: 'Resident Safety',
    description: 'Balancing resident independence with safety obligations, particularly for vulnerable tenants and in supported housing.',
  },
  {
    title: 'Regulatory Compliance',
    description: 'Keeping up with changing regulations including the Building Safety Act, Fire Safety Act, and Social Housing Regulation.',
  },
  {
    title: 'Limited Resources',
    description: 'Managing comprehensive safety programmes with limited staff and budgets, particularly for smaller organisations.',
  },
  {
    title: 'Stock Condition',
    description: 'Addressing health and safety concerns in older housing stock including asbestos, electrical safety, and structural issues.',
  },
]

const benefits = [
  '15+ years experience working exclusively with housing providers',
  'Understanding of social housing regulations and standards',
  'Practical solutions that work within budget constraints',
  'Experience with housing association governance requirements',
  'Familiarity with common stock types and their challenges',
  'Flexible support from one-off projects to ongoing contracts',
]

const faqs = [
  {
    question: 'What makes housing associations different from other organisations?',
    answer: 'Housing providers face unique challenges including managing fire safety across multiple properties, safeguarding vulnerable residents, overseeing contractor safety, and meeting specific regulatory requirements. Generic health and safety advice often doesn\'t account for these complexities. We understand the sector because we\'ve worked in it for over 15 years.',
  },
  {
    question: 'How often do we need fire risk assessments?',
    answer: 'Fire risk assessments should be reviewed regularly - typically annually for higher-risk buildings with sleeping accommodation. The Regulatory Reform (Fire Safety) Order requires assessments to be kept under review and updated when there are significant changes to the building or its use. We can help you develop a proportionate assessment programme based on your stock profile.',
  },
  {
    question: 'Can you help with Building Safety Act compliance?',
    answer: 'Yes. The Building Safety Act introduces new requirements for higher-risk buildings, including the need for a Building Safety Manager and robust safety case information. We can help you understand your obligations, develop compliant processes, and implement practical safety management systems.',
  },
  {
    question: 'Do you work with almshouse charities?',
    answer: 'We have particular experience with almshouses and understand the unique governance structures and resource constraints of charitable housing providers. Many of our almshouse clients are smaller organisations without dedicated safety staff - we act as their expert resource, providing the support they need.',
  },
  {
    question: 'What level of support do you provide?',
    answer: 'We offer flexible arrangements from one-off projects to retained services. Some clients use us for specific tasks like annual fire risk assessments. Others have us on retainer for day-to-day advice and support. We can also act as your appointed competent person, providing the health and safety expertise your organisation needs.',
  },
]

export default function HousingSectorPage() {
  const breadcrumbItems = [
    { name: 'Home', url: 'https://integralsafety.co.uk' },
    { name: 'Sectors', url: 'https://integralsafety.co.uk/sectors' },
    { name: 'Housing & Almshouses', url: 'https://integralsafety.co.uk/sectors/housing-almshouses' },
  ]

  return (
    <>
      <BreadcrumbSchema items={breadcrumbItems} />
      <FAQSchema faqs={faqs} />

      {/* Hero */}
      <section className="py-16 md:py-20 bg-white">
        <div className="container">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <div className="inline-flex items-center gap-2 bg-orange-100 text-orange-600 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                <Home className="w-4 h-4" />
                Sector Expertise
              </div>
              <h1 className="text-hero text-navy-900 mb-6">
                Health &amp; Safety for Housing Associations &amp; Almshouses
              </h1>
              <p className="text-body-lg text-gray-600 mb-4">
                Specialist consultancy for social housing providers. We&apos;ve spent over 15 years
                helping housing associations and almshouse charities protect their residents,
                staff, and properties.
              </p>
              <p className="text-gray-600 mb-8">
                From fire risk assessments to ongoing competent person support, we understand
                the unique challenges of managing health and safety across diverse property
                portfolios with limited resources.
              </p>
              <div className="flex flex-wrap gap-4">
                <Link href="/contact" className="btn-primary">
                  Discuss Your Needs
                </Link>
                <a href="tel:01530382150" className="btn-secondary">
                  Call 01530 382 150
                </a>
              </div>
            </div>
            <div className="relative">
              <div className="bg-cream rounded-hero h-96 overflow-hidden">
                {/* Hero image placeholder - add Image component with CMS image here */}
              </div>
              <div className="absolute -bottom-6 left-6 right-6 bg-white rounded-card p-6 shadow-floating">
                <div className="flex items-center gap-4">
                  <div className="w-12 h-12 bg-green-100 rounded-icon flex items-center justify-center">
                    <Check className="w-6 h-6 text-green-600" />
                  </div>
                  <div>
                    <div className="font-semibold text-navy-900">15+ Years in Housing</div>
                    <div className="text-sm text-gray-600">Trusted by housing providers across the Midlands</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Understanding Your Challenges */}
      <section className="py-20 bg-cream">
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="section-title">We Understand Your Challenges</h2>
            <p className="section-subtitle">
              Social housing brings unique health and safety challenges that generic consultancies
              often don&apos;t appreciate. We&apos;ve worked in the sector long enough to understand them.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {challenges.map((challenge) => (
              <div key={challenge.title} className="bg-white rounded-card p-6">
                <div className="flex items-start gap-3 mb-3">
                  <AlertTriangle className="w-5 h-5 text-orange-500 mt-0.5 flex-shrink-0" />
                  <h3 className="font-semibold text-navy-900">{challenge.title}</h3>
                </div>
                <p className="text-gray-600 text-sm">{challenge.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Services for Housing */}
      <section className="py-20 bg-white">
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="section-title">Services for Housing Providers</h2>
            <p className="section-subtitle">
              Tailored health and safety support designed for the specific needs of social housing.
            </p>
          </div>

          <div className="grid md:grid-cols-2 gap-6">
            {services.map((service) => {
              const Icon = service.icon
              return (
                <Link
                  key={service.title}
                  href={service.href}
                  className="group bg-cream rounded-card p-8 transition-all duration-300 hover:shadow-card hover:-translate-y-1"
                >
                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-orange-100 rounded-icon flex items-center justify-center flex-shrink-0 group-hover:bg-orange-200 transition-colors">
                      <Icon className="w-6 h-6 text-orange-600" />
                    </div>
                    <div>
                      <h3 className="font-heading text-xl font-semibold text-navy-900 mb-2 group-hover:text-orange-600 transition-colors">
                        {service.title}
                      </h3>
                      <p className="text-gray-600">{service.description}</p>
                    </div>
                  </div>
                </Link>
              )
            })}
          </div>

          <div className="text-center mt-10">
            <Link href="/services" className="text-orange-500 font-semibold hover:text-orange-600 transition-colors">
              View All Our Services &rarr;
            </Link>
          </div>
        </div>
      </section>

      {/* Why Choose Us */}
      <section className="py-20 bg-navy-900 text-white">
        <div className="container">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="font-heading text-3xl md:text-4xl font-semibold mb-6">
                Why Housing Providers Choose Integral Safety
              </h2>
              <p className="text-white/80 mb-8">
                Generic health and safety consultancies often provide generic advice. We specialise
                in social housing and understand the practical realities of managing safety across
                diverse property portfolios.
              </p>
              <ul className="space-y-4">
                {benefits.map((benefit) => (
                  <li key={benefit} className="flex items-start gap-3">
                    <Check className="w-5 h-5 text-orange-500 mt-0.5 flex-shrink-0" />
                    <span className="text-white/90">{benefit}</span>
                  </li>
                ))}
              </ul>
            </div>
            <div className="bg-white/10 rounded-card p-8">
              <div className="flex items-center gap-3 mb-6">
                <Users className="w-8 h-8 text-orange-500" />
                <h3 className="font-heading text-xl font-semibold">Our Housing Clients Include</h3>
              </div>
              <div className="space-y-4 text-white/80">
                <p>
                  We work with housing associations of all sizes, from large registered providers
                  managing thousands of homes to small almshouse charities with just a handful
                  of properties.
                </p>
                <p>
                  Our clients include supported housing providers, sheltered accommodation schemes,
                  and organisations managing mixed portfolios of general needs, supported, and
                  shared ownership properties.
                </p>
                <p>
                  Whatever your size or structure, we can provide the level of support that matches
                  your needs and resources.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Training for Housing */}
      <section className="py-20 bg-cream">
        <div className="container">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="order-2 lg:order-1">
              <div className="bg-white rounded-card p-8">
                <h3 className="font-heading text-xl font-semibold text-navy-900 mb-6">
                  Popular Training for Housing Staff
                </h3>
                <ul className="space-y-4">
                  <li>
                    <Link href="/training/fire-awareness" className="flex items-center justify-between group">
                      <span className="text-gray-700 group-hover:text-orange-600 transition-colors">Fire Awareness Training</span>
                      <span className="text-orange-500">&rarr;</span>
                    </Link>
                    <p className="text-sm text-gray-500 mt-1">Essential for all housing staff</p>
                  </li>
                  <li>
                    <Link href="/training/sharps-awareness" className="flex items-center justify-between group">
                      <span className="text-gray-700 group-hover:text-orange-600 transition-colors">Sharps &amp; Needlestick Awareness</span>
                      <span className="text-orange-500">&rarr;</span>
                    </Link>
                    <p className="text-sm text-gray-500 mt-1">For housing officers and caretakers</p>
                  </li>
                  <li>
                    <Link href="/training/manual-handling" className="flex items-center justify-between group">
                      <span className="text-gray-700 group-hover:text-orange-600 transition-colors">Manual Handling</span>
                      <span className="text-orange-500">&rarr;</span>
                    </Link>
                    <p className="text-sm text-gray-500 mt-1">For maintenance teams and support workers</p>
                  </li>
                  <li>
                    <Link href="/training/iosh-managing-safely" className="flex items-center justify-between group">
                      <span className="text-gray-700 group-hover:text-orange-600 transition-colors">IOSH Managing Safely</span>
                      <span className="text-orange-500">&rarr;</span>
                    </Link>
                    <p className="text-sm text-gray-500 mt-1">For managers and team leaders</p>
                  </li>
                </ul>
                <div className="mt-6 pt-6 border-t border-navy-100">
                  <Link href="/training" className="text-orange-500 font-semibold hover:text-orange-600 transition-colors">
                    View All Training Courses &rarr;
                  </Link>
                </div>
              </div>
            </div>
            <div className="order-1 lg:order-2">
              <h2 className="section-title mb-6">Training for Housing Staff</h2>
              <p className="text-gray-600 mb-4">
                We deliver practical, engaging training designed specifically for housing sector
                staff. From fire awareness for all employees to specialist courses for housing
                officers and maintenance teams.
              </p>
              <p className="text-gray-600 mb-4">
                All training can be delivered at your premises or our training facilities. We tailor
                content to reflect your specific properties, policies, and procedures - making it
                immediately relevant to your staff.
              </p>
              <p className="text-gray-600">
                As an IOSH Approved Training Provider, we also deliver internationally recognised
                qualifications for managers and supervisors who need formal health and safety
                credentials.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* FAQs */}
      <section className="py-20 bg-white">
        <div className="container">
          <div className="max-w-3xl mx-auto">
            <div className="text-center mb-12">
              <div className="flex items-center justify-center gap-3 mb-4">
                <HelpCircle className="w-8 h-8 text-orange-500" />
              </div>
              <h2 className="section-title">Frequently Asked Questions</h2>
              <p className="section-subtitle">
                Common questions from housing associations and almshouse charities.
              </p>
            </div>

            <div className="space-y-6">
              {faqs.map((faq, index) => (
                <div key={index} className="bg-cream rounded-card p-6">
                  <h3 className="font-semibold text-navy-900 mb-3">{faq.question}</h3>
                  <p className="text-gray-600">{faq.answer}</p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-20 bg-navy-900 text-white">
        <div className="container">
          <div className="max-w-3xl mx-auto text-center">
            <h2 className="font-heading text-3xl md:text-4xl font-semibold mb-4">
              Let&apos;s Discuss Your Requirements
            </h2>
            <p className="text-white/80 text-lg mb-8">
              Every housing provider is different. Contact us to discuss your specific challenges
              and how we can help - whether that&apos;s a one-off project or ongoing support.
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <Link href="/contact" className="btn-primary bg-orange-500 hover:bg-orange-600">
                Get in Touch
              </Link>
              <a href="tel:01530382150" className="btn-secondary bg-white/10 border-white/20 text-white hover:bg-white/20">
                Call 01530 382 150
              </a>
            </div>
          </div>
        </div>
      </section>

      <CTA />
    </>
  )
}
