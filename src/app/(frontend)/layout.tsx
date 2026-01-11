import type { Metadata } from "next"
import { Header, Footer } from "@/components/layout"
import { OrganizationSchema, LocalBusinessSchema } from "@/components/schema"
import "../globals.css"

export const metadata: Metadata = {
  title: {
    default: "Integral Safety Ltd | Health & Safety Consultants Leicestershire",
    template: "%s | Integral Safety Ltd",
  },
  description: "Expert health and safety consultancy for Leicestershire businesses. Fire risk assessments, IOSH training, drone surveys, and more. Offices in Coalville and Melton Mowbray.",
  keywords: ["health and safety", "consultancy", "fire risk assessment", "IOSH training", "Leicestershire", "Coalville", "Melton Mowbray"],
  authors: [{ name: "Integral Safety Ltd" }],
  openGraph: {
    type: "website",
    locale: "en_GB",
    siteName: "Integral Safety Ltd",
  },
}

export default function FrontendLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <head>
        <OrganizationSchema />
        <LocalBusinessSchema />
      </head>
      <body className="antialiased">
        <Header />
        <main>{children}</main>
        <Footer />
      </body>
    </html>
  )
}
