import Link from 'next/link'
import Image from 'next/image'
import { Check } from 'lucide-react'
import { getHomepage } from '@/lib/payload'
import type { Media } from '@/payload/payload-types'

export async function Hero() {
  const homepage = await getHomepage()
  const hero = homepage?.hero

  // Default values if CMS data not available
  const badge = hero?.badge || 'IOSH Approved Training Provider'
  const headingLine1 = hero?.headingLine1 || "Leicestershire's Trusted"
  const headingHighlight = hero?.headingHighlight || 'Health & Safety'
  const headingLine2 = hero?.headingLine2 || 'Experts'
  const description = hero?.description || 'From fire risk assessments to IOSH training, we help Midlands businesses create safer workplaces. Over 20 years of experience protecting your people, property, and peace of mind.'
  const primaryButtonText = hero?.primaryButtonText || 'Get Your Free Quote'
  const primaryButtonLink = hero?.primaryButtonLink || '/contact'
  const secondaryButtonText = hero?.secondaryButtonText || 'Explore Our Services'
  const secondaryButtonLink = hero?.secondaryButtonLink || '/services'
  const trustText = hero?.trustText || 'Trusted by 100+ organisations'
  const trustSubtext = hero?.trustSubtext || 'Housing associations, construction, hospitality & more'
  const heroImage = hero?.heroImage as Media | null

  return (
    <section className="py-20 md:py-24 bg-white relative overflow-hidden">
      {/* Background Pattern */}
      <div className="absolute top-0 right-0 w-1/2 h-full opacity-70 pointer-events-none">
        <div className="absolute top-[30%] right-[30%] w-[400px] h-[400px] bg-orange-100 rounded-full blur-3xl" />
        <div className="absolute bottom-[20%] right-[10%] w-[300px] h-[300px] bg-green-100 rounded-full blur-3xl" />
      </div>

      <div className="container">
        <div className="grid lg:grid-cols-2 gap-16 items-center relative z-10">
          {/* Content */}
          <div className="animate-fade-in">
            {/* Badge */}
            <div className="inline-flex items-center gap-2 bg-green-100 text-green-500 px-4 py-2 rounded-full text-sm font-semibold mb-6">
              <Check className="w-4 h-4" />
              {badge}
            </div>

            <h1 className="text-hero text-navy-900 mb-6">
              {headingLine1}{' '}
              <span className="text-orange-500">{headingHighlight}</span> {headingLine2}
            </h1>

            <p className="text-body-lg text-gray-600 mb-8 max-w-[500px]">
              {description}
            </p>

            {/* Buttons */}
            <div className="flex flex-wrap gap-4 mb-12">
              <Link href={primaryButtonLink} className="btn-primary">
                {primaryButtonText}
              </Link>
              <Link href={secondaryButtonLink} className="btn-secondary">
                {secondaryButtonText}
              </Link>
            </div>

            {/* Trust Indicators */}
            <div className="flex items-center gap-4">
              <div className="flex -space-x-3">
                {['CJ', 'SK', 'MS', 'PC'].map((initials) => (
                  <div
                    key={initials}
                    className="w-10 h-10 rounded-full bg-navy-700 border-[3px] border-white flex items-center justify-center text-white text-xs font-semibold"
                  >
                    {initials}
                  </div>
                ))}
              </div>
              <div className="text-sm text-gray-600">
                <strong className="text-navy-900">{trustText}</strong>
                <br />
                {trustSubtext}
              </div>
            </div>
          </div>

          {/* Image */}
          <div className="relative hidden lg:block">
            <div className="relative w-full h-[480px] rounded-hero shadow-hero overflow-hidden">
              {heroImage?.url ? (
                <Image
                  src={heroImage.url}
                  alt={heroImage.alt || 'Health and safety consultant conducting a workplace inspection'}
                  fill
                  className="object-cover"
                  priority
                />
              ) : (
                <Image
                  src="/images/hero-safety.jpg"
                  alt="Health and safety consultant conducting a workplace inspection"
                  fill
                  className="object-cover"
                  priority
                />
              )}
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
