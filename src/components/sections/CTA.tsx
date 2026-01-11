import Link from 'next/link'
import { getHomepage } from '@/lib/payload'

export async function CTA() {
  const homepage = await getHomepage()
  const cta = homepage?.cta

  // Use CMS data or defaults
  const heading = cta?.heading || 'Ready to Improve Your Safety Culture?'
  const subheading = cta?.subheading || "Book a free consultation and let's discuss how we can help."
  const phoneNumber = cta?.phoneNumber || '01530 382 150'
  const primaryButtonText = cta?.primaryButtonText || 'Call 01530 382 150'
  const secondaryButtonText = cta?.secondaryButtonText || 'Send Enquiry'
  const secondaryButtonLink = cta?.secondaryButtonLink || '/contact'

  // Format phone for tel: link (remove spaces)
  const telNumber = phoneNumber.replace(/\s/g, '')

  return (
    <section className="py-24 bg-cream-dark">
      <div className="container">
        <div className="bg-gradient-to-br from-orange-500 to-orange-600 rounded-cta py-16 px-10 md:px-20">
          <div className="flex flex-col lg:flex-row items-center justify-between gap-8">
            {/* Content */}
            <div className="text-white text-center lg:text-left">
              <h2 className="font-heading text-3xl md:text-4xl font-semibold mb-3">
                {heading}
              </h2>
              <p className="text-lg opacity-90">
                {subheading}
              </p>
            </div>

            {/* Buttons */}
            <div className="flex flex-wrap gap-4 justify-center">
              <a href={`tel:${telNumber}`} className="btn-white">
                {primaryButtonText}
              </a>
              <Link href={secondaryButtonLink} className="btn-outline-white">
                {secondaryButtonText}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
