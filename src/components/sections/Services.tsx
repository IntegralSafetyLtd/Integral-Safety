import Link from 'next/link'
import { Flame, ClipboardList, BookOpen, Video, Shield, Search, HardHat, Users, Award, LucideIcon } from 'lucide-react'
import { getServices, getHomepage } from '@/lib/payload'

// Map icon names to Lucide icons
const iconMap: Record<string, LucideIcon> = {
  fire: Flame,
  clipboard: ClipboardList,
  book: BookOpen,
  video: Video,
  shield: Shield,
  search: Search,
  hardhat: HardHat,
  users: Users,
  award: Award,
}

export async function Services() {
  // Fetch services and homepage from CMS
  const [cmsServices, homepage] = await Promise.all([
    getServices({ showOnHomepage: true }),
    getHomepage(),
  ])

  const servicesSection = homepage?.services

  // Use CMS data or defaults for section header
  const eyebrow = servicesSection?.eyebrow || 'Our Services'
  const heading = servicesSection?.heading || 'Comprehensive Health & Safety Solutions'
  const description = servicesSection?.description || 'Practical, proportionate advice that protects your people and keeps your business compliant. No jargon, no unnecessary paperwork - just results.'

  // Map CMS services to component format
  const services = cmsServices.slice(0, 6).map((service) => ({
    icon: iconMap[service.icon || 'clipboard'] || ClipboardList,
    title: service.title,
    description: service.shortDescription || '',
    href: `/services/${service.slug}`,
  }))

  return (
    <section className="py-24 bg-cream" id="services">
      <div className="container">
        {/* Header */}
        <div className="text-center mb-14">
          <p className="section-eyebrow">{eyebrow}</p>
          <h2 className="section-title">{heading}</h2>
          <p className="section-subtitle">
            {description}
          </p>
        </div>

        {/* Grid */}
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {services.map((service) => {
            const Icon = service.icon
            return (
              <Link
                key={service.title}
                href={service.href}
                className="group bg-white rounded-card p-8 border border-transparent transition-all duration-300 hover:shadow-card hover:-translate-y-1 hover:border-orange-100"
              >
                <div className="w-14 h-14 bg-cream rounded-icon flex items-center justify-center mb-5 transition-colors group-hover:bg-orange-100">
                  <Icon className="w-7 h-7 text-navy-700 group-hover:text-orange-600 transition-colors" />
                </div>
                <h3 className="text-card-title text-navy-900 mb-3">{service.title}</h3>
                <p className="text-gray-600 text-[0.95rem] leading-relaxed">{service.description}</p>
              </Link>
            )
          })}
        </div>

        {/* View All Link */}
        <div className="text-center mt-12">
          <Link href="/services" className="text-orange-500 font-semibold hover:text-orange-600 transition-colors">
            View All Services &rarr;
          </Link>
        </div>
      </div>
    </section>
  )
}
