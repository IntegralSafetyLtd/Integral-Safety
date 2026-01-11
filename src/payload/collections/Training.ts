import type { CollectionConfig } from 'payload'

export const Training: CollectionConfig = {
  slug: 'training',
  admin: {
    useAsTitle: 'title',
    defaultColumns: ['title', 'slug', 'deliveryMethod', 'updatedAt'],
  },
  access: {
    read: () => true,
  },
  fields: [
    {
      name: 'title',
      type: 'text',
      required: true,
    },
    {
      name: 'slug',
      type: 'text',
      required: true,
      unique: true,
      admin: {
        position: 'sidebar',
      },
    },
    {
      name: 'duration',
      type: 'text',
      label: 'Duration',
      admin: {
        description: 'e.g., "3 days", "Half day", "4 hours"',
      },
    },
    {
      name: 'deliveryMethod',
      type: 'select',
      label: 'Delivery Method',
      options: [
        { label: 'In-person', value: 'in-person' },
        { label: 'Online', value: 'online' },
        { label: 'Both', value: 'both' },
      ],
    },
    {
      name: 'accreditation',
      type: 'text',
      label: 'Accreditation',
      admin: {
        description: 'e.g., "IOSH", "RoSPA"',
      },
    },
    {
      name: 'overview',
      type: 'textarea',
      label: 'Overview',
    },
    {
      name: 'heroImage',
      type: 'upload',
      relationTo: 'media',
      label: 'Hero Image',
    },
    {
      name: 'content',
      type: 'richText',
      label: 'Main Content',
    },
    {
      name: 'learningOutcomes',
      type: 'array',
      label: 'Learning Outcomes',
      fields: [
        {
          name: 'outcome',
          type: 'text',
        },
      ],
    },
    {
      name: 'whoShouldAttend',
      type: 'textarea',
      label: 'Who Should Attend',
    },
    {
      name: 'isComingSoon',
      type: 'checkbox',
      label: 'Coming Soon',
      defaultValue: false,
      admin: {
        position: 'sidebar',
        description: 'Mark as coming soon placeholder',
      },
    },
    {
      name: 'showOnHomepage',
      type: 'checkbox',
      label: 'Show on Homepage',
      defaultValue: false,
      admin: {
        position: 'sidebar',
      },
    },
    {
      name: 'seo',
      type: 'group',
      fields: [
        {
          name: 'metaTitle',
          type: 'text',
          label: 'Meta Title',
        },
        {
          name: 'metaDescription',
          type: 'textarea',
          label: 'Meta Description',
        },
        {
          name: 'ogImage',
          type: 'upload',
          relationTo: 'media',
          label: 'OG Image',
        },
      ],
    },
  ],
}
