type ServiceSchemaProps = {
  name: string
  description: string
  slug: string
  areaServed?: string[]
}

export function ServiceSchema({ name, description, slug, areaServed }: ServiceSchemaProps) {
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'Service',
    '@id': `https://integralsafety.co.uk/services/${slug}#service`,
    name,
    description,
    url: `https://integralsafety.co.uk/services/${slug}`,
    provider: {
      '@id': 'https://integralsafety.co.uk/#organization',
    },
    areaServed: areaServed || [
      { '@type': 'AdministrativeArea', name: 'Leicestershire' },
      { '@type': 'AdministrativeArea', name: 'East Midlands' },
      { '@type': 'City', name: 'Leicester' },
      { '@type': 'City', name: 'Loughborough' },
      { '@type': 'City', name: 'Coalville' },
      { '@type': 'City', name: 'Melton Mowbray' },
    ],
    serviceType: 'Health and Safety Consultancy',
    offers: {
      '@type': 'Offer',
      availability: 'https://schema.org/InStock',
      priceSpecification: {
        '@type': 'PriceSpecification',
        priceCurrency: 'GBP',
      },
    },
  }

  return (
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
    />
  )
}
