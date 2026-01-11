import { getPayload } from 'payload'
import config from '@payload-config'

export async function getPayloadClient() {
  return getPayload({ config })
}

// Get all services (for homepage listing)
export async function getServices(options?: { showOnHomepage?: boolean }) {
  const payload = await getPayloadClient()

  const where: Record<string, unknown> = {}
  if (options?.showOnHomepage) {
    where.showOnHomepage = { equals: true }
  }

  const services = await payload.find({
    collection: 'services',
    where,
    sort: 'homepageOrder',
    depth: 1,
  })

  return services.docs
}

// Get a single service by slug
export async function getServiceBySlug(slug: string) {
  const payload = await getPayloadClient()

  const services = await payload.find({
    collection: 'services',
    where: {
      slug: { equals: slug },
    },
    depth: 2,
  })

  return services.docs[0] || null
}

// Get all service slugs (for static generation)
export async function getAllServiceSlugs() {
  const payload = await getPayloadClient()

  const services = await payload.find({
    collection: 'services',
    limit: 100,
    depth: 0,
  })

  return services.docs.map((service) => service.slug)
}

// Get testimonials
export async function getTestimonials() {
  const payload = await getPayloadClient()

  const testimonials = await payload.find({
    collection: 'testimonials',
    limit: 10,
    depth: 1,
  })

  return testimonials.docs
}

// Get a page by slug
export async function getPageBySlug(slug: string) {
  const payload = await getPayloadClient()

  const pages = await payload.find({
    collection: 'pages',
    where: {
      slug: { equals: slug },
    },
    depth: 2,
  })

  return pages.docs[0] || null
}

// Get training courses
export async function getTrainingCourses() {
  const payload = await getPayloadClient()

  const training = await payload.find({
    collection: 'training',
    limit: 50,
    depth: 1,
  })

  return training.docs
}

// Get a training course by slug
export async function getTrainingBySlug(slug: string) {
  const payload = await getPayloadClient()

  const training = await payload.find({
    collection: 'training',
    where: {
      slug: { equals: slug },
    },
    depth: 2,
  })

  return training.docs[0] || null
}

// Get blog posts
export async function getPosts(options?: { limit?: number; status?: 'published' | 'draft' }) {
  const payload = await getPayloadClient()

  const where: Record<string, unknown> = {}
  if (options?.status) {
    where.status = { equals: options.status }
  }

  const posts = await payload.find({
    collection: 'posts',
    where,
    limit: options?.limit || 50,
    sort: '-publishedAt',
    depth: 2,
  })

  return posts.docs
}

// Get a single blog post by slug
export async function getPostBySlug(slug: string) {
  const payload = await getPayloadClient()

  const posts = await payload.find({
    collection: 'posts',
    where: {
      slug: { equals: slug },
      status: { equals: 'published' },
    },
    depth: 2,
  })

  return posts.docs[0] || null
}

// Get all published post slugs (for static generation)
export async function getAllPostSlugs() {
  const payload = await getPayloadClient()

  const posts = await payload.find({
    collection: 'posts',
    where: {
      status: { equals: 'published' },
    },
    limit: 100,
    depth: 0,
  })

  return posts.docs.map((post) => post.slug)
}


// Get homepage global
export async function getHomepage() {
  const payload = await getPayloadClient()

  const homepage = await payload.findGlobal({
    slug: 'homepage',
    depth: 1,
  })

  return homepage
}
