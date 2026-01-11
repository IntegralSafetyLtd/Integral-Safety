import Link from 'next/link'
import Image from 'next/image'

const services = [
  { name: 'Fire Risk Assessments', href: '/services/fire-risk-assessments' },
  { name: 'H&S Consultancy', href: '/services/consultancy' },
  { name: 'Drone Surveys', href: '/services/work-at-height-surveys' },
  { name: 'Face-Fit Testing', href: '/services/face-fit-testing' },
  { name: 'Auditing', href: '/services/auditing' },
]

const training = [
  { name: 'IOSH Managing Safely', href: '/training/iosh-managing-safely' },
  { name: 'Manual Handling', href: '/training/manual-handling' },
  { name: 'COSHH Awareness', href: '/training/coshh-awareness' },
  { name: 'Fire Awareness', href: '/training/fire-awareness' },
]

const contact = [
  { name: '01530 382 150', href: 'tel:01530382150' },
  { name: '01664 400 450', href: 'tel:01664400450' },
  { name: 'info@integralsafetyltd.co.uk', href: 'mailto:info@integralsafetyltd.co.uk' },
  { name: 'Coalville, Leicestershire', href: '/contact' },
]

export function Footer() {
  return (
    <footer className="bg-navy-900 text-white pt-16 pb-8">
      <div className="container">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
          {/* Brand */}
          <div className="lg:col-span-1">
            <Link href="/" className="inline-block mb-4">
              <Image
                src="/images/logo.png"
                alt="Integral Safety Ltd"
                width={280}
                height={84}
                className="h-20 w-auto brightness-0 invert"
              />
            </Link>
            <p className="text-white/70 text-[0.95rem] leading-relaxed">
              Leicestershire-based health and safety consultancy with offices in Coalville and Melton Mowbray. Integrity and customer satisfaction in everything we do.
            </p>
          </div>

          {/* Services */}
          <div>
            <h4 className="font-bold text-[0.95rem] mb-5">Services</h4>
            <ul className="space-y-3">
              {services.map((item) => (
                <li key={item.name}>
                  <Link
                    href={item.href}
                    className="text-white/70 text-sm hover:text-orange-500 transition-colors"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Training */}
          <div>
            <h4 className="font-bold text-[0.95rem] mb-5">Training</h4>
            <ul className="space-y-3">
              {training.map((item) => (
                <li key={item.name}>
                  <Link
                    href={item.href}
                    className="text-white/70 text-sm hover:text-orange-500 transition-colors"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h4 className="font-bold text-[0.95rem] mb-5">Contact</h4>
            <ul className="space-y-3">
              {contact.map((item) => (
                <li key={item.name}>
                  <Link
                    href={item.href}
                    className="text-white/70 text-sm hover:text-orange-500 transition-colors"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </div>

        {/* Bottom */}
        <div className="border-t border-white/10 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-white/50 text-sm">
          <span>&copy; {new Date().getFullYear()} Integral Safety Ltd. All rights reserved.</span>
          <div className="flex gap-4">
            <Link href="/terms" className="hover:text-white transition-colors">Terms</Link>
            <span>&middot;</span>
            <Link href="/privacy" className="hover:text-white transition-colors">Privacy</Link>
          </div>
        </div>
      </div>
    </footer>
  )
}
