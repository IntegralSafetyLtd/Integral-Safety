import { Metadata } from 'next'
import Link from 'next/link'
import { Clock, Users, Award, Monitor, Check, Building2 } from 'lucide-react'
import { CTA } from '@/components/sections'
import { BreadcrumbSchema } from '@/components/schema'

export const metadata: Metadata = {
  title: 'Health & Safety Training Courses | Leicestershire | Integral Safety',
  description: 'IOSH approved health and safety training in Leicestershire. IOSH Managing Safely, manual handling, COSHH, fire awareness, and more. In-person or online delivery across the Midlands.',
}

const courses = [
  {
    title: 'IOSH Managing Safely',
    duration: '3-4 days',
    delivery: 'In-person or Online',
    accreditation: 'IOSH',
    description: 'The world\'s most popular health and safety course for managers. Covers risk assessment, hazard identification, incident investigation, and legal responsibilities. Internationally recognised certification.',
    href: '/training/iosh-managing-safely',
    featured: true,
  },
  {
    title: 'Manual Handling Awareness',
    duration: 'Half day',
    delivery: 'In-person',
    accreditation: null,
    description: 'Practical training on safe lifting and handling techniques. Includes hands-on exercises using real workplace scenarios and the TILE risk assessment framework.',
    href: '/training/manual-handling',
    featured: false,
  },
  {
    title: 'COSHH Awareness',
    duration: 'Half day',
    delivery: 'In-person or Online',
    accreditation: null,
    description: 'Understanding the Control of Substances Hazardous to Health regulations. Learn to read safety data sheets, identify hazards, and use control measures correctly.',
    href: '/training/coshh-awareness',
    featured: false,
  },
  {
    title: 'Fire Awareness',
    duration: '2-3 hours',
    delivery: 'In-person',
    accreditation: null,
    description: 'Essential fire safety training for all employees. Covers fire prevention, emergency procedures, and includes practical fire extinguisher training.',
    href: '/training/fire-awareness',
    featured: false,
  },
  {
    title: 'Sharps & Needlestick Awareness',
    duration: 'Half day',
    delivery: 'In-person',
    accreditation: null,
    description: 'For anyone who may encounter discarded needles and sharps. Covers safe handling, correct disposal, and post-exposure procedures to minimise infection risk.',
    href: '/training/sharps-awareness',
    featured: false,
  },
  {
    title: 'Accident Investigation',
    duration: '1 day',
    delivery: 'In-person',
    accreditation: null,
    description: 'Learn how to conduct thorough accident investigations. Evidence gathering, witness interviews, root cause analysis, and writing actionable reports.',
    href: '/training/accident-investigation',
    featured: false,
  },
]

const benefits = [
  'Experienced trainers with real-world industry knowledge',
  'Practical, engaging content - not death by PowerPoint',
  'Flexible delivery: at your premises or our training rooms',
  'Courses tailored to your industry and workplace hazards',
  'Competitive group rates for multiple delegates',
  'All materials, refreshments, and certificates included',
]

export default function TrainingPage() {
  const breadcrumbItems = [
    { name: 'Home', url: 'https://integralsafety.co.uk' },
    { name: 'Training', url: 'https://integralsafety.co.uk/training' },
  ]

  return (
    <>
      <BreadcrumbSchema items={breadcrumbItems} />

      {/* Hero */}
      <section className="py-16 md:py-20 bg-white">
        <div className="container">
          <div className="max-w-3xl">
            <p className="section-eyebrow">Training</p>
            <h1 className="text-hero text-navy-900 mb-6">
              Health &amp; Safety Training Courses in Leicestershire
            </h1>
            <p className="text-body-lg text-gray-600 mb-4">
              Practical, engaging health and safety training delivered by experienced professionals
              who understand real workplace challenges. As an IOSH Approved Training Provider,
              we deliver accredited courses alongside a range of essential workplace safety training.
            </p>
            <p className="text-gray-600">
              All courses are available at your premises across Leicestershire and the wider Midlands,
              or at our training facilities in Coalville and Melton Mowbray.
            </p>
          </div>
        </div>
      </section>

      {/* IOSH Badge */}
      <section className="py-8 bg-green-100">
        <div className="container">
          <div className="flex items-center justify-center gap-3 text-green-700">
            <Award className="w-6 h-6" />
            <span className="font-semibold">IOSH Approved Training Provider</span>
          </div>
        </div>
      </section>

      {/* Courses Grid */}
      <section className="py-16 bg-cream">
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="section-title">Our Training Courses</h2>
            <p className="section-subtitle">
              From internationally recognised IOSH qualifications to essential workplace safety awareness.
              All courses include certificates and can be delivered at your premises.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {courses.map((course) => (
              <Link
                key={course.title}
                href={course.href}
                className="group bg-white rounded-card p-6 border border-transparent transition-all duration-300 hover:shadow-card hover:-translate-y-1 hover:border-orange-100 flex flex-col"
              >
                {course.featured && (
                  <span className="inline-flex self-start items-center gap-1 bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-xs font-semibold mb-4">
                    <Award className="w-3 h-3" />
                    Most Popular
                  </span>
                )}

                <h3 className="text-card-title text-navy-900 mb-3 group-hover:text-orange-600 transition-colors">
                  {course.title}
                </h3>

                <p className="text-gray-600 text-sm mb-4 flex-grow">
                  {course.description}
                </p>

                <div className="space-y-2 pt-4 border-t border-navy-100">
                  <div className="flex items-center gap-2 text-sm text-gray-600">
                    <Clock className="w-4 h-4 text-navy-700" />
                    {course.duration}
                  </div>
                  <div className="flex items-center gap-2 text-sm text-gray-600">
                    <Monitor className="w-4 h-4 text-navy-700" />
                    {course.delivery}
                  </div>
                  {course.accreditation && (
                    <div className="flex items-center gap-2 text-sm text-green-600">
                      <Award className="w-4 h-4" />
                      {course.accreditation} Accredited
                    </div>
                  )}
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Why Train With Us */}
      <section className="py-16 bg-white">
        <div className="container">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="section-title mb-6">Why Train With Integral Safety?</h2>
              <p className="text-gray-600 mb-8">
                Health and safety training should be engaging, relevant, and practical. Our trainers
                bring years of real-world experience to every course, using examples and scenarios
                that delegates can relate to their own work.
              </p>
              <ul className="space-y-4">
                {benefits.map((benefit) => (
                  <li key={benefit} className="flex items-start gap-3">
                    <Check className="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" />
                    <span className="text-gray-600">{benefit}</span>
                  </li>
                ))}
              </ul>
            </div>
            <div className="bg-cream rounded-card p-8">
              <h3 className="font-heading text-xl font-semibold text-navy-900 mb-4">
                Training Delivery Options
              </h3>
              <div className="space-y-6">
                <div className="flex items-start gap-4">
                  <div className="w-10 h-10 bg-orange-100 rounded-icon flex items-center justify-center flex-shrink-0">
                    <Building2 className="w-5 h-5 text-orange-600" />
                  </div>
                  <div>
                    <h4 className="font-semibold text-navy-900 mb-1">At Your Premises</h4>
                    <p className="text-gray-600 text-sm">
                      We come to you. Training at your site allows us to use your equipment,
                      discuss your specific hazards, and minimise disruption to operations.
                    </p>
                  </div>
                </div>
                <div className="flex items-start gap-4">
                  <div className="w-10 h-10 bg-orange-100 rounded-icon flex items-center justify-center flex-shrink-0">
                    <Users className="w-5 h-5 text-orange-600" />
                  </div>
                  <div>
                    <h4 className="font-semibold text-navy-900 mb-1">Open Courses</h4>
                    <p className="text-gray-600 text-sm">
                      Join scheduled courses at our training facilities. Ideal for individuals
                      or small numbers where on-site training isn&apos;t cost-effective.
                    </p>
                  </div>
                </div>
                <div className="flex items-start gap-4">
                  <div className="w-10 h-10 bg-orange-100 rounded-icon flex items-center justify-center flex-shrink-0">
                    <Monitor className="w-5 h-5 text-orange-600" />
                  </div>
                  <div>
                    <h4 className="font-semibold text-navy-900 mb-1">Online Delivery</h4>
                    <p className="text-gray-600 text-sm">
                      Selected courses available via live video conferencing. Interactive
                      sessions with the same quality content as in-person training.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Bespoke Training */}
      <section className="py-16 bg-navy-900 text-white">
        <div className="container">
          <div className="max-w-3xl mx-auto text-center">
            <h2 className="font-heading text-2xl md:text-3xl font-semibold mb-4">
              Bespoke Training for Your Workplace
            </h2>
            <p className="text-white/80 mb-6">
              Can&apos;t find what you need? We develop tailored training courses to address your
              specific workplace hazards, procedures, and requirements. From toolbox talks to
              full-day courses, we can create content that&apos;s directly relevant to your operations.
            </p>
            <p className="text-white/80 mb-8">
              Popular bespoke courses include working at height, confined spaces, asbestos awareness,
              Legionella awareness, and site-specific induction programmes.
            </p>
            <Link href="/contact" className="btn-primary bg-orange-500 hover:bg-orange-600">
              Discuss Your Training Needs
            </Link>
          </div>
        </div>
      </section>

      {/* FAQ Section */}
      <section className="py-16 bg-cream">
        <div className="container">
          <div className="max-w-3xl mx-auto">
            <h2 className="section-title text-center mb-12">Training FAQs</h2>
            <div className="space-y-6">
              <div className="bg-white rounded-card p-6">
                <h3 className="font-semibold text-navy-900 mb-2">How do I book a training course?</h3>
                <p className="text-gray-600">
                  Contact us by phone or through our enquiry form. We&apos;ll discuss your requirements,
                  recommend suitable courses, and arrange dates that work for your team. For on-site
                  training, we typically need at least 2 weeks notice.
                </p>
              </div>
              <div className="bg-white rounded-card p-6">
                <h3 className="font-semibold text-navy-900 mb-2">What is the minimum group size for on-site training?</h3>
                <p className="text-gray-600">
                  We can deliver most courses for groups of 4 or more delegates. For smaller numbers,
                  open courses at our training facilities may be more cost-effective. Maximum group
                  sizes vary by course - typically 12-16 for practical courses.
                </p>
              </div>
              <div className="bg-white rounded-card p-6">
                <h3 className="font-semibold text-navy-900 mb-2">Do you provide certificates?</h3>
                <p className="text-gray-600">
                  Yes, all delegates receive a certificate of attendance or achievement (depending on
                  the course). IOSH courses include official IOSH certificates. We maintain records
                  and can provide replacement certificates if needed.
                </p>
              </div>
              <div className="bg-white rounded-card p-6">
                <h3 className="font-semibold text-navy-900 mb-2">Are refresher courses available?</h3>
                <p className="text-gray-600">
                  Yes, we offer refresher training for most courses. Refresher courses are typically
                  shorter than initial training and focus on updating knowledge and reinforcing key
                  points. We recommend refresher training every 1-3 years depending on the subject.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <CTA />
    </>
  )
}
