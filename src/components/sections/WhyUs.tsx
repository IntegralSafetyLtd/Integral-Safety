import { Check } from 'lucide-react'
import { getHomepage } from '@/lib/payload'

const defaultStats = [
  { number: '20+', label: 'Years Experience' },
  { number: '15+', label: 'Years in Housing Sector' },
  { number: '100+', label: 'Organisations Served' },
  { number: '2', label: 'Leicestershire Offices' },
]

const defaultReasons = [
  'Practical advice that works in the real world, not just on paper',
  'No unnecessary paperwork or over-complicated systems',
  'We explain the "why" so you understand your obligations',
  'Flexible support from one day per month to daily visits',
  'Direct access to experienced consultants, not call centres',
  'Local presence with offices in Coalville and Melton Mowbray',
]

export async function WhyUs() {
  const homepage = await getHomepage()
  const whyUs = homepage?.whyUs

  // Use CMS data or defaults
  const eyebrow = whyUs?.eyebrow || 'Why Choose Integral Safety'
  const heading = whyUs?.heading || 'Health & Safety That Works For Your Business'
  const description = whyUs?.description || "We've spent over two decades helping Leicestershire businesses navigate health and safety requirements. Our approach is simple: provide sensible, proportionate advice that protects your people without drowning you in bureaucracy."

  const reasons = whyUs?.reasons && whyUs.reasons.length > 0
    ? whyUs.reasons.map((r) => r.reason).filter(Boolean) as string[]
    : defaultReasons

  const stats = whyUs?.stats && whyUs.stats.length > 0
    ? whyUs.stats.map((s) => ({ number: s.number || '', label: s.label || '' }))
    : defaultStats

  return (
    <section className="py-24 bg-navy-900 text-white">
      <div className="container">
        <div className="grid lg:grid-cols-2 gap-16 items-center">
          {/* Content */}
          <div>
            <p className="section-eyebrow !text-orange-500">{eyebrow}</p>
            <h2 className="section-title !text-white mb-6">
              {heading}
            </h2>
            <p className="text-white/70 text-lg mb-8">
              {description}
            </p>

            <ul className="space-y-4">
              {reasons.map((reason) => (
                <li key={reason} className="flex items-start gap-3">
                  <Check className="w-5 h-5 text-orange-500 mt-0.5 flex-shrink-0" />
                  <span className="text-white/80">{reason}</span>
                </li>
              ))}
            </ul>
          </div>

          {/* Stats Grid */}
          <div className="grid grid-cols-2 gap-8">
            {stats.map((stat) => (
              <div key={stat.label} className="text-center bg-white/5 rounded-card p-8">
                <div className="font-heading text-4xl md:text-5xl font-semibold text-orange-500 leading-none mb-2">
                  {stat.number}
                </div>
                <div className="text-[0.95rem] text-white/80">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  )
}
