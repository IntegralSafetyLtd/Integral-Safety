import { Metadata } from 'next'
import { Hero, Services, WhyUs, Testimonials, CTA } from '@/components/sections'

export const metadata: Metadata = {
  title: 'Integral Safety | Health & Safety Consultants | Leicestershire',
  description: 'Leicestershire health and safety consultants with 20+ years experience. Fire risk assessments, IOSH training, H&S consultancy, and drone surveys. Offices in Coalville and Melton Mowbray.',
  keywords: 'health and safety consultants Leicestershire, fire risk assessments, IOSH training, health and safety Coalville, H&S consultancy Midlands',
}

export default function Home() {
  return (
    <>
      <Hero />
      <Services />
      <WhyUs />
      <Testimonials />
      <CTA />
    </>
  )
}
