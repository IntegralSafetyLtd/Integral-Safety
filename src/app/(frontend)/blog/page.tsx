import { Metadata } from 'next'
import Link from 'next/link'
import Image from 'next/image'
import { Calendar, ArrowRight } from 'lucide-react'
import { BreadcrumbSchema } from '@/components/schema'
import { getPosts } from '@/lib/payload'
import type { Media } from '@/payload/payload-types'

export const metadata: Metadata = {
  title: 'Health & Safety Blog | Insights & Guidance | Integral Safety',
  description: 'Expert health and safety insights, guidance, and updates from Integral Safety. Practical advice on fire safety, compliance, training, and workplace safety.',
}

function formatDate(dateString: string): string {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

export default async function BlogPage() {
  const breadcrumbItems = [
    { name: 'Home', url: 'https://integralsafety.co.uk' },
    { name: 'Blog', url: 'https://integralsafety.co.uk/blog' },
  ]

  // Fetch posts from Payload
  const allPosts = await getPosts({ status: 'published' })

  // First post is featured, rest are regular posts
  const featuredPost = allPosts[0] || null
  const recentPosts = allPosts.slice(1)

  return (
    <>
      <BreadcrumbSchema items={breadcrumbItems} />

      {/* Hero */}
      <section className="py-16 md:py-20 bg-white">
        <div className="container">
          <div className="max-w-3xl">
            <p className="section-eyebrow">Blog</p>
            <h1 className="text-hero text-navy-900 mb-6">
              Health &amp; Safety Insights
            </h1>
            <p className="text-body-lg text-gray-600">
              Practical guidance, industry updates, and expert insights to help you manage
              health and safety effectively. Written by our experienced consultants.
            </p>
          </div>
        </div>
      </section>

      {/* Featured Post */}
      {featuredPost && (
        <section className="py-12 bg-cream">
          <div className="container">
            <h2 className="font-heading text-lg font-semibold text-navy-900 mb-6">Latest Article</h2>
            <Link
              href={`/blog/${featuredPost.slug}`}
              className="group block bg-white rounded-card overflow-hidden shadow-card hover:shadow-floating transition-all duration-300"
            >
              <div className="grid lg:grid-cols-2">
                {/* Image */}
                <div className="relative h-64 lg:h-auto min-h-[280px]">
                  {featuredPost.featuredImage && typeof featuredPost.featuredImage === 'object' ? (
                    <Image
                      src={(featuredPost.featuredImage as Media).url || ''}
                      alt={(featuredPost.featuredImage as Media).alt || featuredPost.title}
                      fill
                      className="object-cover"
                    />
                  ) : (
                    <div className="absolute inset-0 bg-gradient-to-br from-orange-600 to-red-700 flex items-center justify-center">
                      <div className="text-center text-white/90 p-6">
                        <div className="w-16 h-16 border-2 border-white/30 rounded-full flex items-center justify-center mx-auto mb-3">
                          <span className="text-2xl">📝</span>
                        </div>
                        <p className="text-sm font-medium">
                          Photo: Health & Safety article
                        </p>
                      </div>
                    </div>
                  )}
                </div>

                {/* Content */}
                <div className="p-8 lg:p-10">
                  <div className="flex items-center gap-4 mb-4">
                    <span className="inline-block bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-xs font-semibold">
                      Health & Safety
                    </span>
                  </div>

                  <h3 className="font-heading text-2xl font-semibold text-navy-900 mb-4 group-hover:text-orange-600 transition-colors">
                    {featuredPost.title}
                  </h3>

                  <p className="text-gray-600 mb-6 leading-relaxed">
                    {featuredPost.excerpt}
                  </p>

                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3 text-sm text-gray-500">
                      {featuredPost.publishedAt && (
                        <>
                          <Calendar className="w-4 h-4" />
                          <span>{formatDate(featuredPost.publishedAt)}</span>
                        </>
                      )}
                    </div>

                    <span className="text-orange-500 font-semibold flex items-center gap-2 group-hover:gap-3 transition-all">
                      Read Article
                      <ArrowRight className="w-4 h-4" />
                    </span>
                  </div>
                </div>
              </div>
            </Link>
          </div>
        </section>
      )}

      {/* All Posts */}
      {recentPosts.length > 0 && (
        <section className="py-16 bg-white">
          <div className="container">
            <h2 className="font-heading text-2xl font-semibold text-navy-900 mb-8">
              {featuredPost ? 'More Articles' : 'All Articles'}
            </h2>
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
              {recentPosts.map((post) => (
                <Link
                  key={post.id}
                  href={`/blog/${post.slug}`}
                  className="group bg-cream rounded-card overflow-hidden hover:shadow-card transition-all duration-300"
                >
                  {/* Image */}
                  <div className="relative h-48">
                    {post.featuredImage && typeof post.featuredImage === 'object' ? (
                      <Image
                        src={(post.featuredImage as Media).url || ''}
                        alt={(post.featuredImage as Media).alt || post.title}
                        fill
                        className="object-cover"
                      />
                    ) : (
                      <div className="absolute inset-0 bg-gradient-to-br from-orange-600 to-red-700 flex items-center justify-center">
                        <div className="text-center text-white/80 p-4">
                          <p className="text-xs">
                            Photo: Health & Safety article
                          </p>
                        </div>
                      </div>
                    )}
                  </div>

                  {/* Content */}
                  <div className="p-6">
                    <div className="flex items-center gap-3 mb-3">
                      <span className="text-orange-600 text-xs font-semibold">
                        Health & Safety
                      </span>
                    </div>

                    <h3 className="font-heading text-lg font-semibold text-navy-900 mb-3 group-hover:text-orange-600 transition-colors line-clamp-2">
                      {post.title}
                    </h3>

                    <p className="text-gray-600 text-sm mb-4 line-clamp-3">
                      {post.excerpt}
                    </p>

                    {post.publishedAt && (
                      <div className="flex items-center gap-2 text-xs text-gray-500">
                        <Calendar className="w-3 h-3" />
                        <span>{formatDate(post.publishedAt)}</span>
                      </div>
                    )}
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* No posts message */}
      {allPosts.length === 0 && (
        <section className="py-16 bg-cream">
          <div className="container">
            <div className="text-center py-12">
              <p className="text-gray-600 mb-4">No blog posts published yet.</p>
              <p className="text-gray-500 text-sm">Check back soon for health and safety insights and guidance.</p>
            </div>
          </div>
        </section>
      )}

      {/* Newsletter CTA */}
      <section className="py-16 bg-navy-900 text-white">
        <div className="container">
          <div className="max-w-2xl mx-auto text-center">
            <h2 className="font-heading text-2xl md:text-3xl font-semibold mb-4">
              Stay Updated
            </h2>
            <p className="text-white/80 mb-6">
              Get practical health and safety insights delivered to your inbox.
              No spam - just useful guidance and industry updates.
            </p>
            <Link href="/contact" className="btn-primary bg-orange-500 hover:bg-orange-600">
              Get in Touch
            </Link>
          </div>
        </div>
      </section>
    </>
  )
}
