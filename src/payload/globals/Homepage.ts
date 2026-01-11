import type { GlobalConfig } from 'payload'

export const Homepage: GlobalConfig = {
  slug: 'homepage',
  label: 'Homepage',
  access: {
    read: () => true,
  },
  fields: [
    // Hero Section
    {
      name: 'hero',
      type: 'group',
      label: 'Hero Section',
      fields: [
        {
          name: 'badge',
          type: 'text',
          label: 'Badge Text',
          defaultValue: 'IOSH Approved Training Provider',
          admin: {
            description: 'Small badge shown above the headline',
          },
        },
        {
          name: 'headingLine1',
          type: 'text',
          label: 'Heading Line 1',
          defaultValue: "Leicestershire's Trusted",
        },
        {
          name: 'headingHighlight',
          type: 'text',
          label: 'Heading Highlight (orange text)',
          defaultValue: 'Health & Safety',
        },
        {
          name: 'headingLine2',
          type: 'text',
          label: 'Heading Line 2',
          defaultValue: 'Experts',
        },
        {
          name: 'description',
          type: 'textarea',
          label: 'Description',
          defaultValue: 'From fire risk assessments to IOSH training, we help Midlands businesses create safer workplaces. Over 20 years of experience protecting your people, property, and peace of mind.',
        },
        {
          name: 'primaryButtonText',
          type: 'text',
          label: 'Primary Button Text',
          defaultValue: 'Get Your Free Quote',
        },
        {
          name: 'primaryButtonLink',
          type: 'text',
          label: 'Primary Button Link',
          defaultValue: '/contact',
        },
        {
          name: 'secondaryButtonText',
          type: 'text',
          label: 'Secondary Button Text',
          defaultValue: 'Explore Our Services',
        },
        {
          name: 'secondaryButtonLink',
          type: 'text',
          label: 'Secondary Button Link',
          defaultValue: '/services',
        },
        {
          name: 'trustText',
          type: 'text',
          label: 'Trust Indicator Text',
          defaultValue: 'Trusted by 100+ organisations',
        },
        {
          name: 'trustSubtext',
          type: 'text',
          label: 'Trust Indicator Subtext',
          defaultValue: 'Housing associations, construction, hospitality & more',
        },
        {
          name: 'floatingCardTitle',
          type: 'text',
          label: 'Floating Card Title',
          defaultValue: 'PAS 79 Compliant',
        },
        {
          name: 'floatingCardSubtitle',
          type: 'text',
          label: 'Floating Card Subtitle',
          defaultValue: 'Fire Risk Assessments',
        },
        {
          name: 'heroImage',
          type: 'upload',
          relationTo: 'media',
          label: 'Hero Image',
        },
      ],
    },
    // Why Us Section
    {
      name: 'whyUs',
      type: 'group',
      label: 'Why Choose Us Section',
      fields: [
        {
          name: 'eyebrow',
          type: 'text',
          label: 'Eyebrow Text',
          defaultValue: 'Why Choose Integral Safety',
        },
        {
          name: 'heading',
          type: 'text',
          label: 'Heading',
          defaultValue: 'Health & Safety That Works For Your Business',
        },
        {
          name: 'description',
          type: 'textarea',
          label: 'Description',
          defaultValue: "We've spent over two decades helping Leicestershire businesses navigate health and safety requirements. Our approach is simple: provide sensible, proportionate advice that protects your people without drowning you in bureaucracy.",
        },
        {
          name: 'reasons',
          type: 'array',
          label: 'Reasons to Choose Us',
          fields: [
            {
              name: 'reason',
              type: 'text',
            },
          ],
        },
        {
          name: 'stats',
          type: 'array',
          label: 'Statistics',
          maxRows: 4,
          fields: [
            {
              name: 'number',
              type: 'text',
              label: 'Number/Value',
              admin: {
                description: 'e.g., "20+", "100+", "2"',
              },
            },
            {
              name: 'label',
              type: 'text',
              label: 'Label',
              admin: {
                description: 'e.g., "Years Experience"',
              },
            },
          ],
        },
      ],
    },
    // CTA Section
    {
      name: 'cta',
      type: 'group',
      label: 'Call to Action Section',
      fields: [
        {
          name: 'heading',
          type: 'text',
          label: 'Heading',
          defaultValue: 'Ready to Improve Your Safety Culture?',
        },
        {
          name: 'subheading',
          type: 'text',
          label: 'Subheading',
          defaultValue: "Book a free consultation and let's discuss how we can help.",
        },
        {
          name: 'phoneNumber',
          type: 'text',
          label: 'Phone Number',
          defaultValue: '01530 382 150',
        },
        {
          name: 'primaryButtonText',
          type: 'text',
          label: 'Primary Button Text',
          defaultValue: 'Call 01530 382 150',
        },
        {
          name: 'secondaryButtonText',
          type: 'text',
          label: 'Secondary Button Text',
          defaultValue: 'Send Enquiry',
        },
        {
          name: 'secondaryButtonLink',
          type: 'text',
          label: 'Secondary Button Link',
          defaultValue: '/contact',
        },
      ],
    },
    // Services Section (just the header - services come from collection)
    {
      name: 'services',
      type: 'group',
      label: 'Services Section',
      fields: [
        {
          name: 'eyebrow',
          type: 'text',
          label: 'Eyebrow Text',
          defaultValue: 'Our Services',
        },
        {
          name: 'heading',
          type: 'text',
          label: 'Heading',
          defaultValue: 'Comprehensive Health & Safety Solutions',
        },
        {
          name: 'description',
          type: 'textarea',
          label: 'Description',
          defaultValue: 'Practical, proportionate advice that protects your people and keeps your business compliant. No jargon, no unnecessary paperwork - just results.',
        },
      ],
    },
    // SEO
    {
      name: 'seo',
      type: 'group',
      label: 'SEO Settings',
      fields: [
        {
          name: 'metaTitle',
          type: 'text',
          label: 'Meta Title',
          defaultValue: 'Integral Safety | Health & Safety Consultants | Leicestershire',
        },
        {
          name: 'metaDescription',
          type: 'textarea',
          label: 'Meta Description',
          defaultValue: 'Leicestershire health and safety consultants with 20+ years experience. Fire risk assessments, IOSH training, H&S consultancy, and drone surveys. Offices in Coalville and Melton Mowbray.',
        },
        {
          name: 'keywords',
          type: 'text',
          label: 'Keywords',
          defaultValue: 'health and safety consultants Leicestershire, fire risk assessments, IOSH training, health and safety Coalville, H&S consultancy Midlands',
        },
      ],
    },
  ],
}
