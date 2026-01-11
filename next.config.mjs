import { withPayload } from '@payloadcms/next/withPayload'

/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    remotePatterns: [],
  },
  async redirects() {
    return [
      // Core pages
      {
        source: '/contact-us',
        destination: '/contact',
        permanent: true,
      },
      {
        source: '/contact-us/',
        destination: '/contact',
        permanent: true,
      },
      // Services
      {
        source: '/fire-risk-assessments',
        destination: '/services/fire-risk-assessments',
        permanent: true,
      },
      {
        source: '/fire-risk-assessments/',
        destination: '/services/fire-risk-assessments',
        permanent: true,
      },
      {
        source: '/health-and-safety-consultancy-service',
        destination: '/services/consultancy',
        permanent: true,
      },
      {
        source: '/health-and-safety-consultancy-service/',
        destination: '/services/consultancy',
        permanent: true,
      },
      {
        source: '/face-fit-testing',
        destination: '/services/face-fit-testing',
        permanent: true,
      },
      {
        source: '/face-fit-testing/',
        destination: '/services/face-fit-testing',
        permanent: true,
      },
      {
        source: '/work-at-height-surveys',
        destination: '/services/work-at-height-surveys',
        permanent: true,
      },
      {
        source: '/work-at-height-surveys/',
        destination: '/services/work-at-height-surveys',
        permanent: true,
      },
      {
        source: '/accident-investigation-and-riddor',
        destination: '/services/accident-investigation',
        permanent: true,
      },
      {
        source: '/accident-investigation-and-riddor/',
        destination: '/services/accident-investigation',
        permanent: true,
      },
      {
        source: '/accreditation-support',
        destination: '/services/accreditation-support',
        permanent: true,
      },
      {
        source: '/accreditation-support/',
        destination: '/services/accreditation-support',
        permanent: true,
      },
      {
        source: '/auditing-and-inspections',
        destination: '/services/auditing',
        permanent: true,
      },
      {
        source: '/auditing-and-inspections/',
        destination: '/services/auditing',
        permanent: true,
      },
      {
        source: '/havs-hand-arm-vibration',
        destination: '/services/havs-testing',
        permanent: true,
      },
      {
        source: '/havs-hand-arm-vibration/',
        destination: '/services/havs-testing',
        permanent: true,
      },
      {
        source: '/health-safety-competent-person-services',
        destination: '/services/competent-person',
        permanent: true,
      },
      {
        source: '/health-safety-competent-person-services/',
        destination: '/services/competent-person',
        permanent: true,
      },
      // Training
      {
        source: '/health-and-safety-training',
        destination: '/training',
        permanent: true,
      },
      {
        source: '/health-and-safety-training/',
        destination: '/training',
        permanent: true,
      },
      {
        source: '/iosh-managing-safely',
        destination: '/training/iosh-managing-safely',
        permanent: true,
      },
      {
        source: '/iosh-managing-safely/',
        destination: '/training/iosh-managing-safely',
        permanent: true,
      },
      {
        source: '/manual-handling-awareness',
        destination: '/training/manual-handling',
        permanent: true,
      },
      {
        source: '/manual-handling-awareness/',
        destination: '/training/manual-handling',
        permanent: true,
      },
      {
        source: '/coshh-awareness',
        destination: '/training/coshh-awareness',
        permanent: true,
      },
      {
        source: '/coshh-awareness/',
        destination: '/training/coshh-awareness',
        permanent: true,
      },
      {
        source: '/sharps-and-needlestick-awareness',
        destination: '/training/sharps-awareness',
        permanent: true,
      },
      {
        source: '/sharps-and-needlestick-awareness/',
        destination: '/training/sharps-awareness',
        permanent: true,
      },
      {
        source: '/advanced-accident-investigation',
        destination: '/training/accident-investigation',
        permanent: true,
      },
      {
        source: '/advanced-accident-investigation/',
        destination: '/training/accident-investigation',
        permanent: true,
      },
      // Other pages
      {
        source: '/housing-association-and-almshouses',
        destination: '/sectors/housing-almshouses',
        permanent: true,
      },
      {
        source: '/housing-association-and-almshouses/',
        destination: '/sectors/housing-almshouses',
        permanent: true,
      },
      {
        source: '/leicestershire-health-and-safety-consultant',
        destination: '/areas/leicestershire',
        permanent: true,
      },
      {
        source: '/leicestershire-health-and-safety-consultant/',
        destination: '/areas/leicestershire',
        permanent: true,
      },
      {
        source: '/melton-mowbray-health-and-safety-consultants',
        destination: '/areas/melton-mowbray',
        permanent: true,
      },
      {
        source: '/melton-mowbray-health-and-safety-consultants/',
        destination: '/areas/melton-mowbray',
        permanent: true,
      },
      // Legal pages
      {
        source: '/wpautoterms/privacy-policy',
        destination: '/privacy',
        permanent: true,
      },
      {
        source: '/wpautoterms/privacy-policy/',
        destination: '/privacy',
        permanent: true,
      },
      {
        source: '/wpautoterms/terms-and-conditions',
        destination: '/terms',
        permanent: true,
      },
      {
        source: '/wpautoterms/terms-and-conditions/',
        destination: '/terms',
        permanent: true,
      },
    ]
  },
}

export default withPayload(nextConfig)
