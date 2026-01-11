import type { CollectionConfig } from 'payload'

export const Services: CollectionConfig = {
  slug: 'services',
  admin: {
    useAsTitle: 'title',
    defaultColumns: ['title', 'slug', 'updatedAt'],
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
      name: 'shortDescription',
      type: 'textarea',
      label: 'Short Description',
      admin: {
        description: 'Brief description for service cards on homepage',
      },
    },
    {
      name: 'icon',
      type: 'select',
      label: 'Service Icon',
      options: [
        { label: 'Fire', value: 'fire' },
        { label: 'Clipboard', value: 'clipboard' },
        { label: 'Book', value: 'book' },
        { label: 'Video/Drone', value: 'video' },
        { label: 'Shield', value: 'shield' },
        { label: 'Search', value: 'search' },
        { label: 'Hard Hat', value: 'hardhat' },
        { label: 'Users', value: 'users' },
        { label: 'Award', value: 'award' },
      ],
    },
    {
      name: 'heroHeading',
      type: 'text',
      label: 'Hero Heading',
    },
    {
      name: 'heroSubheading',
      type: 'textarea',
      label: 'Hero Subheading',
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
      name: 'whatWeAssess',
      type: 'array',
      label: 'What We Assess',
      admin: {
        description: 'List of items/areas that are assessed during this service',
      },
      fields: [
        {
          name: 'item',
          type: 'text',
          required: true,
        },
      ],
    },
    {
      name: 'processSteps',
      type: 'array',
      label: 'Our Process',
      admin: {
        description: 'Step-by-step process for delivering this service',
      },
      fields: [
        {
          name: 'title',
          type: 'text',
          required: true,
        },
        {
          name: 'description',
          type: 'textarea',
          required: true,
        },
      ],
    },
    {
      name: 'whatYouReceive',
      type: 'array',
      label: 'What You Receive',
      admin: {
        description: 'Deliverables the client receives from this service',
      },
      fields: [
        {
          name: 'item',
          type: 'text',
          required: true,
        },
      ],
    },
    {
      name: 'premisesTypes',
      type: 'array',
      label: 'Types of Premises',
      admin: {
        description: 'Types of buildings/premises this service covers',
      },
      fields: [
        {
          name: 'type',
          type: 'text',
          required: true,
        },
      ],
    },
    {
      name: 'benefits',
      type: 'array',
      label: 'Key Benefits',
      fields: [
        {
          name: 'benefit',
          type: 'text',
        },
      ],
    },
    {
      name: 'faqs',
      type: 'array',
      label: 'FAQs',
      fields: [
        {
          name: 'question',
          type: 'text',
          required: true,
        },
        {
          name: 'answer',
          type: 'textarea',
          required: true,
        },
      ],
    },
    {
      name: 'relatedServices',
      type: 'relationship',
      relationTo: 'services',
      hasMany: true,
      label: 'Related Services',
    },
    {
      name: 'showOnHomepage',
      type: 'checkbox',
      label: 'Show on Homepage',
      defaultValue: true,
      admin: {
        position: 'sidebar',
      },
    },
    {
      name: 'homepageOrder',
      type: 'number',
      label: 'Homepage Order',
      admin: {
        position: 'sidebar',
        description: 'Lower numbers appear first',
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
