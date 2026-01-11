import { Metadata } from 'next'
import Link from 'next/link'
import { Users, Award, Heart, Check, Clock, MapPin, Phone } from 'lucide-react'
import { CTA } from '@/components/sections'
import { BreadcrumbSchema } from '@/components/schema'

export const metadata: Metadata = {
  title: 'About Us | Health & Safety Consultants | Integral Safety',
  description: 'Integral Safety Ltd - Leicestershire health and safety consultants with over 20 years of experience. Practical, proportionate advice from our offices in Coalville and Melton Mowbray.',
}

const values = [
  {
    icon: Shield,
    title: 'Integrity',
    description: 'We provide honest, practical advice. If something isn\'t necessary, we\'ll tell you. Our reputation is built on trust, and we won\'t recommend services you don\'t need.',
  },
  {
    icon: Users,
    title: 'Partnership',
    description: 'We work with you, not against you. Health and safety should support your business operations, not create unnecessary barriers. We become an extension of your team.',
  },
  {
    icon: Award,
    title: 'Expertise',
    description: 'Our consultants have real-world experience across multiple industries. We understand the practical challenges you face because we\'ve faced them ourselves.',
  },
  {
    icon: Heart,
    title: 'Care',
    description: 'At its core, health and safety is about people going home safe to their families. That\'s what drives everything we do - protecting lives, not just ticking boxes.',
  },
]

const timeline = [
  {
    year: 'Founded',
    title: 'Integral Safety Established',
    description: 'Founded in Coalville with a mission to provide practical, proportionate health and safety advice to Leicestershire businesses.',
  },
  {
    year: 'Growth',
    title: 'Expanding Our Reach',
    description: 'As our reputation grew, we expanded our team and services to meet increasing demand from businesses across the Midlands.',
  },
  {
    year: 'Housing Sector',
    title: 'Specialising in Housing',
    description: 'Developed deep expertise in the housing sector, becoming trusted advisors to housing associations and almshouse charities.',
  },
  {
    year: 'Second Office',
    title: 'Melton Mowbray Office Opens',
    description: 'Opened our second office to better serve East Leicestershire and Rutland, while maintaining our commitment to local, personal service.',
  },
  {
    year: 'IOSH Approved',
    title: 'Becoming IOSH Approved',
    description: 'Achieved IOSH Approved Training Provider status, enabling us to deliver internationally recognised qualifications.',
  },
  {
    year: 'Today',
    title: '100+ Organisations Served',
    description: 'Now serving over 100 businesses from small family firms to large housing associations, with the same commitment to practical, sensible advice.',
  },
]

const sectors = [
  'Housing Associations',
  'Almshouse Charities',
  'Construction',
  'Manufacturing',
  'Hospitality',
  'Retail',
  'Care Homes',
  'Education',
  'Local Authorities',
  'Warehousing & Logistics',
]

export default function AboutPage() {
  const breadcrumbItems = [
    { name: 'Home', url: 'https://integralsafety.co.uk' },
    { name: 'About Us', url: 'https://integralsafety.co.uk/about' },
  ]

  return (
    <>
      <BreadcrumbSchema items={breadcrumbItems} />

      {/* Hero */}
      <section className="py-16 md:py-20 bg-white">
        <div className="container">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <p className="section-eyebrow">About Integral Safety</p>
              <h1 className="text-hero text-navy-900 mb-6">
                Leicestershire&apos;s Trusted Health &amp; Safety Partner
              </h1>
              <p className="text-body-lg text-gray-600 mb-4">
                For over 20 years, Integral Safety has been helping businesses across Leicestershire
                and the Midlands create safer workplaces. We believe health and safety should be
                practical, proportionate, and focused on what really matters - getting everyone
                home safe.
              </p>
              <p className="text-gray-600 mb-8">
                Our approach is simple: understand your business, identify the real risks, and
                provide sensible solutions that actually get implemented. No unnecessary paperwork,
                no over-complicated systems - just practical advice that works.
              </p>
              <div className="flex flex-wrap gap-4">
                <Link href="/contact" className="btn-primary">
                  Get in Touch
                </Link>
                <Link href="/services" className="btn-secondary">
                  Our Services
                </Link>
              </div>
            </div>
            <div className="relative">
              <div className="bg-cream rounded-hero h-96 overflow-hidden">
                {/* Hero image placeholder - add Image component with CMS image here */}
              </div>
              {/* Stats overlay */}
              <div className="absolute -bottom-6 left-6 right-6 bg-white rounded-card p-6 shadow-floating">
                <div className="grid grid-cols-3 gap-4 text-center">
                  <div>
                    <div className="font-heading text-2xl font-semibold text-orange-500">20+</div>
                    <div className="text-xs text-gray-600">Years Experience</div>
                  </div>
                  <div>
                    <div className="font-heading text-2xl font-semibold text-orange-500">100+</div>
                    <div className="text-xs text-gray-600">Clients Served</div>
                  </div>
                  <div>
                    <div className="font-heading text-2xl font-semibold text-orange-500">2</div>
                    <div className="text-xs text-gray-600">Local Offices</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Our Story */}
      <section className="py-20 bg-cream">
        <div className="container">
          <div className="max-w-3xl mx-auto">
            <h2 className="section-title text-center mb-6">Our Story</h2>
            <div className="text-gray-600 space-y-4 text-lg">
              <p>
                Integral Safety was founded in Coalville, Leicestershire, born from a simple belief:
                health and safety should help businesses, not burden them with unnecessary bureaucracy.
              </p>
              <p>
                Our founder had seen too many companies struggling with overly complex safety systems
                that looked impressive on paper but didn&apos;t work in practice. Thick policy documents
                gathered dust on shelves while real hazards went unaddressed. Risk assessments were
                written to satisfy auditors rather than protect workers.
              </p>
              <p>
                We set out to do things differently - providing sensible, practical advice that
                business owners and managers could actually understand and implement. Advice that
                addressed the real risks, not theoretical ones. Systems that worked in the real
                world, not just in a training manual.
              </p>
              <p>
                That approach resonated with local businesses. Word spread, and what started as a
                one-person consultancy has grown into a trusted partner for over 100 organisations
                across Leicestershire and beyond - from small family firms to large housing
                associations managing thousands of properties.
              </p>
              <p>
                Today, we have offices in Coalville and Melton Mowbray, a team of experienced
                consultants, and IOSH approved training provider status. But our approach remains
                the same: understand your business, identify what really matters, and help you get
                it right.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Values */}
      <section className="py-20 bg-white">
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="section-title">Our Values</h2>
            <p className="section-subtitle">
              The principles that guide everything we do.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {values.map((value) => {
              const Icon = value.icon
              return (
                <div key={value.title} className="text-center">
                  <div className="w-16 h-16 bg-orange-100 rounded-icon flex items-center justify-center mx-auto mb-4">
                    <Icon className="w-8 h-8 text-orange-600" />
                  </div>
                  <h3 className="font-heading text-xl font-semibold text-navy-900 mb-3">
                    {value.title}
                  </h3>
                  <p className="text-gray-600">
                    {value.description}
                  </p>
                </div>
              )
            })}
          </div>
        </div>
      </section>

      {/* Timeline */}
      <section className="py-20 bg-navy-900 text-white">
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="font-heading text-3xl md:text-4xl font-semibold mb-4">Our Journey</h2>
            <p className="text-white/70 max-w-2xl mx-auto">
              From a one-person consultancy to a trusted partner for over 100 organisations.
            </p>
          </div>

          <div className="max-w-3xl mx-auto">
            <div className="space-y-8">
              {timeline.map((item, index) => (
                <div key={index} className="flex gap-6">
                  <div className="flex-shrink-0 w-20 text-right">
                    <span className="text-orange-500 font-semibold">{item.year}</span>
                  </div>
                  <div className="flex-shrink-0 relative">
                    <div className="w-4 h-4 bg-orange-500 rounded-full mt-1" />
                    {index < timeline.length - 1 && (
                      <div className="absolute top-5 left-1.5 w-1 h-full bg-white/20" />
                    )}
                  </div>
                  <div className="pb-8">
                    <h3 className="font-semibold text-lg mb-1">{item.title}</h3>
                    <p className="text-white/70">{item.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Sectors We Serve */}
      <section className="py-20 bg-cream">
        <div className="container">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="section-title mb-6">Sectors We Serve</h2>
              <p className="text-gray-600 mb-6">
                Our consultants have hands-on experience across a wide range of industries. This
                breadth of knowledge allows us to bring fresh perspectives and proven solutions
                to every client, whatever their sector.
              </p>
              <p className="text-gray-600 mb-8">
                We have particular expertise in the housing sector, where we&apos;ve spent over 15 years
                helping housing associations and almshouse charities manage health and safety across
                their property portfolios.
              </p>
              <Link href="/sectors/housing-almshouses" className="btn-primary">
                Learn About Our Housing Expertise
              </Link>
            </div>
            <div className="bg-white rounded-card p-8">
              <h3 className="font-heading text-lg font-semibold text-navy-900 mb-6">
                Industries We Work With
              </h3>
              <div className="grid grid-cols-2 gap-3">
                {sectors.map((sector) => (
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

      {/* Locations */}
      <section className="py-20 bg-white">
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="section-title">Our Offices</h2>
            <p className="section-subtitle">
              Serving Leicestershire and the wider Midlands from two convenient locations.
            </p>
          </div>

          <div className="grid md:grid-cols-2 gap-8 max-w-3xl mx-auto">
            <div className="bg-cream rounded-card p-8">
              <div className="flex items-center gap-3 mb-4">
                <MapPin className="w-6 h-6 text-orange-500" />
                <h3 className="font-heading text-xl font-semibold text-navy-900">
                  Coalville
                </h3>
              </div>
              <p className="text-gray-600 mb-4">
                Our head office in North West Leicestershire, serving the western Midlands including
                Leicester, Loughborough, Hinckley, and Nuneaton.
              </p>
              <div className="space-y-2">
                <div className="flex items-center gap-3 text-gray-600">
                  <Phone className="w-4 h-4" />
                  <a href="tel:01530382150" className="hover:text-orange-500 transition-colors">
                    01530 382 150
                  </a>
                </div>
                <div className="flex items-center gap-3 text-gray-600">
                  <Clock className="w-4 h-4" />
                  <span>Mon-Fri: 9am - 5pm</span>
                </div>
              </div>
            </div>

            <div className="bg-cream rounded-card p-8">
              <div className="flex items-center gap-3 mb-4">
                <MapPin className="w-6 h-6 text-orange-500" />
                <h3 className="font-heading text-xl font-semibold text-navy-900">
                  Melton Mowbray
                </h3>
              </div>
              <p className="text-gray-600 mb-4">
                Our East Leicestershire office, serving Melton, Rutland, Oakham, Stamford, and the
                surrounding areas.
              </p>
              <div className="space-y-2">
                <div className="flex items-center gap-3 text-gray-600">
                  <Phone className="w-4 h-4" />
                  <a href="tel:01664400450" className="hover:text-orange-500 transition-colors">
                    01664 400 450
                  </a>
                </div>
                <div className="flex items-center gap-3 text-gray-600">
                  <Clock className="w-4 h-4" />
                  <span>Mon-Fri: 9am - 5pm</span>
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
