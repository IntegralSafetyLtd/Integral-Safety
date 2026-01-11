type CourseSchemaProps = {
  name: string
  description: string
  slug: string
  duration?: string
  provider?: string
}

export function CourseSchema({
  name,
  description,
  slug,
  duration,
  provider = 'Integral Safety Ltd',
}: CourseSchemaProps) {
  const schema = {
    '@context': 'https://schema.org',
    '@type': 'Course',
    '@id': `https://integralsafety.co.uk/training/${slug}#course`,
    name,
    description,
    url: `https://integralsafety.co.uk/training/${slug}`,
    provider: {
      '@type': 'Organization',
      name: provider,
      sameAs: 'https://integralsafety.co.uk',
    },
    ...(duration && {
      hasCourseInstance: {
        '@type': 'CourseInstance',
        courseMode: ['onsite', 'online'],
        duration,
        courseWorkload: duration,
      },
    }),
    offers: {
      '@type': 'Offer',
      availability: 'https://schema.org/InStock',
      priceCurrency: 'GBP',
      category: 'Health and Safety Training',
    },
    educationalCredentialAwarded: 'Certificate of Completion',
    occupationalCredentialAwarded: {
      '@type': 'EducationalOccupationalCredential',
      credentialCategory: 'certificate',
    },
    inLanguage: 'en-GB',
    isAccessibleForFree: false,
    audience: {
      '@type': 'Audience',
      audienceType: 'Business professionals',
    },
  }

  return (
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
    />
  )
}
