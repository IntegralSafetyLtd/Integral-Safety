import { Metadata } from 'next'
import { ContactForm } from './ContactForm'
import { Phone, Mail, MapPin, Clock } from 'lucide-react'
import { BreadcrumbSchema } from '@/components/schema'

export const metadata: Metadata = {
  title: 'Contact Us',
  description: 'Get in touch with Integral Safety Ltd. Offices in Coalville and Melton Mowbray, Leicestershire. Call 01530 382 150 or send us a message.',
}

const contactInfo = [
  {
    icon: Phone,
    label: 'Phone',
    items: [
      { text: '01530 382 150', href: 'tel:01530382150' },
      { text: '01664 400 450', href: 'tel:01664400450' },
    ],
  },
  {
    icon: Mail,
    label: 'Email',
    items: [
      { text: 'info@integralsafetyltd.co.uk', href: 'mailto:info@integralsafetyltd.co.uk' },
    ],
  },
  {
    icon: MapPin,
    label: 'Offices',
    items: [
      { text: 'Coalville, Leicestershire', href: '#' },
      { text: 'Melton Mowbray, Leicestershire', href: '#' },
    ],
  },
  {
    icon: Clock,
    label: 'Hours',
    items: [
      { text: 'Monday - Friday: 9am - 5pm', href: '#' },
    ],
  },
]

export default function ContactPage() {
  const breadcrumbItems = [
    { name: 'Home', url: 'https://integralsafety.co.uk' },
    { name: 'Contact', url: 'https://integralsafety.co.uk/contact' },
  ]

  return (
    <>
      <BreadcrumbSchema items={breadcrumbItems} />

      {/* Hero */}
      <section className="py-16 md:py-20 bg-white">
        <div className="container">
          <div className="max-w-2xl">
            <p className="section-eyebrow">Contact Us</p>
            <h1 className="text-hero text-navy-900 mb-6">
              Get in Touch
            </h1>
            <p className="text-body-lg text-gray-600">
              Ready to discuss your health and safety needs? We offer free initial consultations
              to understand your requirements and provide tailored solutions.
            </p>
          </div>
        </div>
      </section>

      {/* Contact Section */}
      <section className="py-16 bg-cream">
        <div className="container">
          <div className="grid lg:grid-cols-3 gap-12">
            {/* Contact Info */}
            <div>
              <h2 className="font-heading text-2xl font-semibold text-navy-900 mb-8">
                Contact Information
              </h2>
              <div className="space-y-8">
                {contactInfo.map((info) => {
                  const Icon = info.icon
                  return (
                    <div key={info.label} className="flex gap-4">
                      <div className="w-12 h-12 bg-orange-100 rounded-icon flex items-center justify-center flex-shrink-0">
                        <Icon className="w-5 h-5 text-orange-600" />
                      </div>
                      <div>
                        <h3 className="font-semibold text-navy-900 mb-1">{info.label}</h3>
                        {info.items.map((item) => (
                          <a
                            key={item.text}
                            href={item.href}
                            className="block text-gray-600 hover:text-orange-500 transition-colors"
                          >
                            {item.text}
                          </a>
                        ))}
                      </div>
                    </div>
                  )
                })}
              </div>
            </div>

            {/* Contact Form */}
            <div className="lg:col-span-2">
              <div className="bg-white rounded-card p-8 md:p-10 shadow-card">
                <h2 className="font-heading text-2xl font-semibold text-navy-900 mb-6">
                  Send Us a Message
                </h2>
                <ContactForm />
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  )
}
