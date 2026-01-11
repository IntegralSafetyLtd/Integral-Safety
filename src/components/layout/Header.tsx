'use client'

import Link from 'next/link'
import Image from 'next/image'
import { useState } from 'react'
import { Menu, X, Phone, Mail } from 'lucide-react'

const navigation = [
  { name: 'Services', href: '/services' },
  { name: 'Training', href: '/training' },
  { name: 'About', href: '/about' },
  { name: 'Blog', href: '/blog' },
]

export function Header() {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  return (
    <>
      {/* Top Bar */}
      <div className="bg-navy-900 text-white py-2.5 text-sm">
        <div className="container flex justify-between items-center">
          <span className="hidden sm:block">Health & Safety Consultants — Coalville & Melton Mowbray</span>
          <div className="flex gap-6">
            <a href="tel:01530382150" className="flex items-center gap-2 opacity-90 hover:opacity-100 transition-opacity">
              <Phone className="w-4 h-4 opacity-80" />
              <span>01530 382 150</span>
            </a>
            <a href="mailto:info@integralsafetyltd.co.uk" className="hidden md:flex items-center gap-2 opacity-90 hover:opacity-100 transition-opacity">
              <Mail className="w-4 h-4 opacity-80" />
              <span>info@integralsafetyltd.co.uk</span>
            </a>
          </div>
        </div>
      </div>

      {/* Main Header */}
      <header className="bg-white py-4 border-b border-navy-100 sticky top-0 z-50">
        <div className="container flex justify-between items-center">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-3">
            <Image
              src="/images/logo.png"
              alt="Integral Safety Ltd"
              width={280}
              height={84}
              className="h-20 w-auto"
              priority
            />
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden lg:flex items-center gap-8">
            {navigation.map((item) => (
              <Link
                key={item.name}
                href={item.href}
                className="text-navy-800 text-[0.95rem] font-medium hover:text-orange-500 transition-colors"
              >
                {item.name}
              </Link>
            ))}
            <Link href="/contact" className="btn-primary !py-3 !px-6">
              Get a Quote
            </Link>
          </nav>

          {/* Mobile Menu Button */}
          <button
            type="button"
            className="lg:hidden p-2 text-navy-800"
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
          >
            <span className="sr-only">Open menu</span>
            {mobileMenuOpen ? (
              <X className="w-6 h-6" />
            ) : (
              <Menu className="w-6 h-6" />
            )}
          </button>
        </div>

        {/* Mobile Navigation */}
        {mobileMenuOpen && (
          <div className="lg:hidden bg-white border-t border-navy-100">
            <div className="container py-4 space-y-4">
              {navigation.map((item) => (
                <Link
                  key={item.name}
                  href={item.href}
                  className="block text-navy-800 text-base font-medium hover:text-orange-500 transition-colors"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  {item.name}
                </Link>
              ))}
              <Link
                href="/contact"
                className="btn-primary w-full justify-center !py-3"
                onClick={() => setMobileMenuOpen(false)}
              >
                Get a Quote
              </Link>
            </div>
          </div>
        )}
      </header>
    </>
  )
}
