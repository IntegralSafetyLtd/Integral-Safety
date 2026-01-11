import { Metadata } from 'next'
import Link from 'next/link'
import Image from 'next/image'
import { notFound } from 'next/navigation'
import { Calendar, User, ArrowLeft, Share2, Flame } from 'lucide-react'
import { CTA } from '@/components/sections'
import { BreadcrumbSchema } from '@/components/schema'
import { getPostBySlug, getAllPostSlugs, getPosts } from '@/lib/payload'
import type { Media } from '@/payload/payload-types'
import { SimpleRichText } from '@/components/ui/SimpleRichText'

type Props = {
  params: Promise<{ slug: string }>
}

function formatDate(dateString: string): string {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params
  const post = await getPostBySlug(slug)

  if (!post) {
    return { title: 'Article Not Found' }
  }

  return {
    title: `${post.title} | Integral Safety Blog`,
    description: post.excerpt,
    openGraph: {
      title: post.title,
      description: post.excerpt,
      type: 'article',
      publishedTime: post.publishedAt || undefined,
      authors: ['Integral Safety'],
    },
  }
}

export async function generateStaticParams() {
  const slugs = await getAllPostSlugs()
  return slugs.map((slug) => ({ slug }))
}

export default async function BlogPostPage({ params }: Props) {
  const { slug } = await params
  const post = await getPostBySlug(slug)

  if (!post) {
    notFound()
  }

  const breadcrumbItems = [
    { name: 'Home', url: 'https://integralsafety.co.uk' },
    { name: 'Blog', url: 'https://integralsafety.co.uk/blog' },
    { name: post.title, url: `https://integralsafety.co.uk/blog/${slug}` },
  ]

  // Get other posts for "More Articles" section
  const allPosts = await getPosts({ status: 'published', limit: 4 })
  const otherPosts = allPosts.filter((p) => p.slug !== slug).slice(0, 3)

  return (
    <>
      <BreadcrumbSchema items={breadcrumbItems} />

      {/* Article Header */}
      <article>
        <header className="py-12 md:py-16 bg-white">
          <div className="container">
            <div className="max-w-3xl mx-auto">
              <Link
                href="/blog"
                className="inline-flex items-center gap-2 text-gray-500 hover:text-orange-500 transition-colors mb-6"
              >
                <ArrowLeft className="w-4 h-4" />
                Back to Blog
              </Link>

              <div className="flex items-center gap-4 mb-6">
                <span className="inline-block bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm font-semibold">
                  Health & Safety
                </span>
              </div>

              <h1 className="font-heading text-3xl md:text-4xl lg:text-5xl font-semibold text-navy-900 mb-6 leading-tight">
                {post.title}
              </h1>

              <p className="text-xl text-gray-600 mb-8 leading-relaxed">
                {post.excerpt}
              </p>

              <div className="flex items-center gap-6 text-sm text-gray-500 pb-8 border-b border-gray-200">
                <div className="flex items-center gap-2">
                  <User className="w-4 h-4" />
                  <span>Integral Safety</span>
                </div>
                {post.publishedAt && (
                  <div className="flex items-center gap-2">
                    <Calendar className="w-4 h-4" />
                    <span>{formatDate(post.publishedAt)}</span>
                  </div>
                )}
                <button className="flex items-center gap-2 hover:text-orange-500 transition-colors ml-auto">
                  <Share2 className="w-4 h-4" />
                  Share
                </button>
              </div>
            </div>
          </div>
        </header>

        {/* Featured Image */}
        <div className="container mb-12">
          <div className="max-w-4xl mx-auto">
            <div className={`relative h-64 md:h-96 rounded-card overflow-hidden ${!post.featuredImage ? 'bg-gradient-to-br from-orange-600 to-red-700' : ''}`}>
              {post.featuredImage && typeof post.featuredImage === 'object' ? (
                <Image
                  src={(post.featuredImage as Media).url || ''}
                  alt={(post.featuredImage as Media).alt || post.title}
                  fill
                  className="object-cover"
                />
              ) : (
                <div className="absolute inset-0 bg-gradient-to-br from-orange-600 to-red-700 flex items-center justify-center">
                  <div className="text-center text-white p-6">
                    <div className="w-20 h-20 border-2 border-white/30 rounded-full flex items-center justify-center mx-auto mb-4">
                      <Flame className="w-10 h-10 text-white/80" />
                    </div>
                    <p className="text-sm font-medium text-white/90">
                      Photo: Health & Safety article
                    </p>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Article Content */}
        <div className="container pb-16">
          <div className="max-w-3xl mx-auto">
            <div className="prose prose-lg max-w-none prose-headings:font-heading prose-headings:text-navy-900 prose-p:text-gray-600 prose-a:text-orange-500 prose-a:no-underline hover:prose-a:underline prose-strong:text-navy-900 prose-ul:text-gray-600 prose-ol:text-gray-600">
              {post.content && (
                <SimpleRichText data={post.content} />
              )}
            </div>

            {/* Author Bio */}
            <section className="mt-12 p-8 bg-cream rounded-card">
              <div className="flex items-start gap-4">
                <div className="w-16 h-16 bg-navy-200 rounded-full flex items-center justify-center flex-shrink-0">
                  <User className="w-8 h-8 text-navy-600" />
                </div>
                <div>
                  <h3 className="font-heading text-lg font-semibold text-navy-900 mb-2">
                    About Integral Safety
                  </h3>
                  <p className="text-gray-600 text-sm">
                    Integral Safety provides expert health and safety consultancy across Leicestershire
                    and the Midlands. With over 20 years of experience, we help businesses of all sizes
                    manage their health and safety obligations practically and effectively.
                  </p>
                </div>
              </div>
            </section>
          </div>
        </div>
      </article>

      {/* More Articles */}
      {otherPosts.length > 0 && (
        <section className="py-16 bg-cream">
          <div className="container">
            <h2 className="font-heading text-2xl font-semibold text-navy-900 mb-8">More Articles</h2>
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
              {otherPosts.map((otherPost) => (
                <Link
                  key={otherPost.id}
                  href={`/blog/${otherPost.slug}`}
                  className="group bg-white rounded-card overflow-hidden hover:shadow-card transition-all duration-300"
                >
                  <div className="relative h-48">
                    {otherPost.featuredImage && typeof otherPost.featuredImage === 'object' ? (
                      <Image
                        src={(otherPost.featuredImage as Media).url || ''}
                        alt={(otherPost.featuredImage as Media).alt || otherPost.title}
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
                  <div className="p-6">
                    <span className="text-orange-600 text-xs font-semibold">
                      Health & Safety
                    </span>
                    <h3 className="font-heading text-lg font-semibold text-navy-900 mt-2 mb-3 group-hover:text-orange-600 transition-colors line-clamp-2">
                      {otherPost.title}
                    </h3>
                    <p className="text-gray-600 text-sm line-clamp-2">{otherPost.excerpt}</p>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}

      <CTA />
    </>
  )
}
