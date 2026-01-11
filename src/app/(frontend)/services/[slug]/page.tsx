import { Metadata } from 'next'
import Link from 'next/link'
import { notFound } from 'next/navigation'
import { Check, ArrowRight, Phone, ClipboardCheck, FileText, Building2 } from 'lucide-react'
import { CTA } from '@/components/sections'
import { ServiceSchema, FAQSchema, BreadcrumbSchema } from '@/components/schema'
import { getServiceBySlug, getAllServiceSlugs, getServices } from '@/lib/payload'

type Props = {
  params: Promise<{ slug: string }>
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params
  const service = await getServiceBySlug(slug)

  if (!service) {
    return { title: 'Service Not Found' }
  }

  return {
    title: service.seo?.metaTitle || service.title,
    description: service.seo?.metaDescription || service.shortDescription,
  }
}

export async function generateStaticParams() {
  const slugs = await getAllServiceSlugs()
  return slugs.map((slug) => ({ slug }))
}

export default async function ServicePage({ params }: Props) {
  const { slug } = await params
  const service = await getServiceBySlug(slug)

  if (!service) {
    notFound()
  }

  // Get related services
  const allServices = await getServices()
  const relatedServices = allServices
    .filter((s) => s.slug !== slug)
    .slice(0, 3)

  // Prepare FAQs for schema
  const faqsForSchema = service.faqs?.map((faq) => ({
    question: faq.question,
    answer: faq.answer,
  })) || []

  // Breadcrumb items
  const breadcrumbItems = [
    { name: 'Home', url: 'https://integralsafety.co.uk' },
    { name: 'Services', url: 'https://integralsafety.co.uk/services' },
    { name: service.title, url: `https://integralsafety.co.uk/services/${slug}` },
  ]

  return (
    <>
      {/* Schema Markup */}
      <ServiceSchema
        name={service.title}
        description={service.shortDescription || ''}
        slug={slug}
      />
      {faqsForSchema.length > 0 && <FAQSchema faqs={faqsForSchema} />}
      <BreadcrumbSchema items={breadcrumbItems} />

      {/* Hero */}
      <section className="py-16 md:py-20 bg-white">
        <div className="container">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="max-w-xl">
              <p className="section-eyebrow">Our Services</p>
              <h1 className="text-hero text-navy-900 mb-6">
                {service.heroHeading || service.title}
              </h1>
              <p className="text-body-lg text-gray-600 mb-8">
                {service.heroSubheading || service.shortDescription}
              </p>
              <div className="flex flex-wrap gap-4">
                <Link href="/contact" className="btn-primary">
                  Get a Free Quote
                </Link>
                <a href="tel:01530382150" className="btn-secondary inline-flex items-center gap-2">
                  <Phone className="w-4 h-4" />
                  01530 382 150
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

      {/* Content */}
      <section className="py-16 bg-cream">
        <div className="container">
          <div className="grid lg:grid-cols-3 gap-12">
            {/* Main Content */}
            <div className="lg:col-span-2 space-y-8">
              {/* Rich text content would go here - for now show description */}
              <div className="bg-white rounded-card p-8 md:p-10">
                <h2 className="font-heading text-2xl font-semibold text-navy-900 mb-4">
                  About This Service
                </h2>
                <p className="text-gray-600 leading-relaxed">
                  {service.heroSubheading || service.shortDescription}
                </p>
              </div>

              {/* What We Assess */}
              {service.whatWeAssess && service.whatWeAssess.length > 0 && (
                <div className="bg-white rounded-card p-8 md:p-10">
                  <div className="flex items-center gap-3 mb-6">
                    <div className="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                      <ClipboardCheck className="w-5 h-5 text-orange-600" />
                    </div>
                    <h2 className="font-heading text-2xl font-semibold text-navy-900">
                      What We Assess
                    </h2>
                  </div>
                  <div className="grid sm:grid-cols-2 gap-3">
                    {service.whatWeAssess.map((item, index) => (
                      <div key={index} className="flex items-start gap-3">
                        <Check className="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span className="text-gray-600">{item.item}</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Our Process */}
              {service.processSteps && service.processSteps.length > 0 && (
                <div className="bg-white rounded-card p-8 md:p-10">
                  <h2 className="font-heading text-2xl font-semibold text-navy-900 mb-6">
                    Our Process
                  </h2>
                  <div className="space-y-6">
                    {service.processSteps.map((step, index) => (
                      <div key={index} className="flex gap-4">
                        <div className="flex-shrink-0 w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                          {index + 1}
                        </div>
                        <div>
                          <h3 className="font-semibold text-navy-900 mb-1">
                            {step.title}
                          </h3>
                          <p className="text-gray-600 text-[0.95rem]">
                            {step.description}
                          </p>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* What You Receive */}
              {service.whatYouReceive && service.whatYouReceive.length > 0 && (
                <div className="bg-white rounded-card p-8 md:p-10">
                  <div className="flex items-center gap-3 mb-6">
                    <div className="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                      <FileText className="w-5 h-5 text-orange-600" />
                    </div>
                    <h2 className="font-heading text-2xl font-semibold text-navy-900">
                      What You&apos;ll Receive
                    </h2>
                  </div>
                  <ul className="space-y-3">
                    {service.whatYouReceive.map((item, index) => (
                      <li key={index} className="flex items-start gap-3">
                        <Check className="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span className="text-gray-600">{item.item}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              )}

              {/* Types of Premises */}
              {service.premisesTypes && service.premisesTypes.length > 0 && (
                <div className="bg-white rounded-card p-8 md:p-10">
                  <div className="flex items-center gap-3 mb-6">
                    <div className="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                      <Building2 className="w-5 h-5 text-orange-600" />
                    </div>
                    <h2 className="font-heading text-2xl font-semibold text-navy-900">
                      Types of Premises We Cover
                    </h2>
                  </div>
                  <div className="grid sm:grid-cols-2 gap-3">
                    {service.premisesTypes.map((item, index) => (
                      <div key={index} className="flex items-start gap-3">
                        <Check className="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span className="text-gray-600">{item.type}</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* FAQs */}
              {service.faqs && service.faqs.length > 0 && (
                <div className="bg-white rounded-card p-8 md:p-10">
                  <h2 className="font-heading text-2xl font-semibold text-navy-900 mb-6">
                    Frequently Asked Questions
                  </h2>
                  <div className="space-y-6">
                    {service.faqs.map((faq, index) => (
                      <div key={index}>
                        <h3 className="font-semibold text-navy-900 mb-2">
                          {faq.question}
                        </h3>
                        <p className="text-gray-600 text-[0.95rem]">
                          {faq.answer}
                        </p>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>

            {/* Sidebar */}
            <div className="space-y-6">
              {/* Benefits */}
              {service.benefits && service.benefits.length > 0 && (
                <div className="bg-white rounded-card p-6">
                  <h3 className="font-heading text-lg font-semibold text-navy-900 mb-4">
                    Key Benefits
                  </h3>
                  <ul className="space-y-3">
                    {service.benefits.map((item, index) => (
                      <li key={index} className="flex items-start gap-3">
                        <Check className="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" />
                        <span className="text-gray-600 text-sm">{item.benefit}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              )}

              {/* Related Services */}
              {relatedServices.length > 0 && (
                <div className="bg-white rounded-card p-6">
                  <h3 className="font-heading text-lg font-semibold text-navy-900 mb-4">
                    Related Services
                  </h3>
                  <ul className="space-y-3">
                    {relatedServices.map((related) => (
                      <li key={related.id}>
                        <Link
                          href={`/services/${related.slug}`}
                          className="flex items-center gap-2 text-navy-800 hover:text-orange-500 transition-colors text-sm"
                        >
                          <ArrowRight className="w-4 h-4" />
                          {related.title}
                        </Link>
                      </li>
                    ))}
                  </ul>
                </div>
              )}

              {/* Contact Card */}
              <div className="bg-navy-900 rounded-card p-6 text-white">
                <h3 className="font-heading text-lg font-semibold mb-3">
                  Ready to Get Started?
                </h3>
                <p className="text-white/80 text-sm mb-4">
                  Contact us for a free, no-obligation quote or to discuss your requirements.
                </p>
                <a
                  href="tel:01530382150"
                  className="block w-full bg-orange-500 text-white text-center py-3 rounded-button font-semibold hover:bg-orange-600 transition-colors mb-3"
                >
                  01530 382 150
                </a>
                <Link
                  href="/contact"
                  className="block w-full bg-white/10 text-white text-center py-3 rounded-button font-semibold hover:bg-white/20 transition-colors"
                >
                  Send Enquiry
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>

      <CTA />
    </>
  )
}
