export function LocalBusinessSchema() {
  const coalvilleOffice = {
    '@context': 'https://schema.org',
    '@type': 'LocalBusiness',
    '@id': 'https://integralsafety.co.uk/#coalville-office',
    name: 'Integral Safety Ltd - Coalville Office',
    image: 'https://integralsafety.co.uk/logo.png',
    url: 'https://integralsafety.co.uk',
    telephone: '+44-1530-382150',
    priceRange: '££',
    address: {
      '@type': 'PostalAddress',
      streetAddress: 'Coalville',
      addressLocality: 'Coalville',
      addressRegion: 'Leicestershire',
      postalCode: 'LE67',
      addressCountry: 'GB',
    },
    geo: {
      '@type': 'GeoCoordinates',
      latitude: 52.7243,
      longitude: -1.3697,
    },
    openingHoursSpecification: [
      {
        '@type': 'OpeningHoursSpecification',
        dayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        opens: '09:00',
        closes: '17:00',
      },
    ],
    areaServed: [
      { '@type': 'City', name: 'Leicester' },
      { '@type': 'City', name: 'Loughborough' },
      { '@type': 'City', name: 'Hinckley' },
      { '@type': 'City', name: 'Nuneaton' },
      { '@type': 'City', name: 'Coalville' },
    ],
    parentOrganization: {
      '@id': 'https://integralsafety.co.uk/#organization',
    },
  }

  const meltonOffice = {
    '@context': 'https://schema.org',
    '@type': 'LocalBusiness',
    '@id': 'https://integralsafety.co.uk/#melton-office',
    name: 'Integral Safety Ltd - Melton Mowbray Office',
    image: 'https://integralsafety.co.uk/logo.png',
    url: 'https://integralsafety.co.uk',
    telephone: '+44-1664-400450',
    priceRange: '££',
    address: {
      '@type': 'PostalAddress',
      streetAddress: 'Melton Mowbray',
      addressLocality: 'Melton Mowbray',
      addressRegion: 'Leicestershire',
      postalCode: 'LE13',
      addressCountry: 'GB',
    },
    geo: {
      '@type': 'GeoCoordinates',
      latitude: 52.7653,
      longitude: -0.8868,
    },
    openingHoursSpecification: [
      {
        '@type': 'OpeningHoursSpecification',
        dayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        opens: '09:00',
        closes: '17:00',
      },
    ],
    areaServed: [
      { '@type': 'City', name: 'Melton Mowbray' },
      { '@type': 'City', name: 'Oakham' },
      { '@type': 'City', name: 'Stamford' },
      { '@type': 'AdministrativeArea', name: 'Rutland' },
    ],
    parentOrganization: {
      '@id': 'https://integralsafety.co.uk/#organization',
    },
  }

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(coalvilleOffice) }}
      />
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(meltonOffice) }}
      />
    </>
  )
}
