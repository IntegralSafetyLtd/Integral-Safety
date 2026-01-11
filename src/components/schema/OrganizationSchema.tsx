export function OrganizationSchema() {
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    '@id': 'https://integralsafety.co.uk/#organization',
    name: 'Integral Safety Ltd',
    url: 'https://integralsafety.co.uk',
    logo: {
      '@type': 'ImageObject',
      url: 'https://integralsafety.co.uk/logo.png',
      width: 280,
      height: 84,
    },
    description:
      'Expert health and safety consultancy for Leicestershire businesses. Fire risk assessments, IOSH training, drone surveys, and more.',
    foundingDate: '2003',
    numberOfEmployees: {
      '@type': 'QuantitativeValue',
      minValue: 2,
      maxValue: 10,
    },
    areaServed: [
      {
        '@type': 'GeoCircle',
        geoMidpoint: {
          '@type': 'GeoCoordinates',
          latitude: 52.7116,
          longitude: -1.3628,
        },
        geoRadius: '50000',
      },
      {
        '@type': 'AdministrativeArea',
        name: 'Leicestershire',
      },
      {
        '@type': 'AdministrativeArea',
        name: 'East Midlands',
      },
    ],
    contactPoint: [
      {
        '@type': 'ContactPoint',
        telephone: '+44-1530-382150',
        contactType: 'customer service',
        areaServed: 'GB',
        availableLanguage: 'English',
      },
      {
        '@type': 'ContactPoint',
        telephone: '+44-1664-400450',
        contactType: 'customer service',
        areaServed: 'GB',
        availableLanguage: 'English',
      },
    ],
    sameAs: [
      // Add social media URLs when available
    ],
  }

  return (
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
    />
  )
}
