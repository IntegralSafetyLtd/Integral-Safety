<?php
/**
 * Single Blog Post Page
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$slug = $_GET['slug'] ?? '';

// Get the post
$post = dbFetchOne(
    "SELECT * FROM blog_posts
     WHERE slug = ?
       AND (status = 'published' OR (status = 'scheduled' AND published_at <= NOW()))",
    [$slug]
);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    include __DIR__ . '/404.php';
    exit;
}

$pageTitle = $post['title'] . ' | ' . getSetting('site_name', SITE_NAME);
$metaDescription = $post['meta_description'] ?: ($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 160));
$ogImage = $post['featured_image'] ?: getSetting('seo_default_og_image');

// Get related posts
$relatedPosts = dbFetchAll(
    "SELECT * FROM blog_posts
     WHERE id != ?
       AND (status = 'published' OR (status = 'scheduled' AND published_at <= NOW()))
       AND category = ?
     ORDER BY published_at DESC
     LIMIT 3",
    [$post['id'], $post['category']]
);

// If not enough related by category, get recent posts
if (count($relatedPosts) < 3) {
    $excludeIds = array_merge([$post['id']], array_column($relatedPosts, 'id'));
    $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
    $moreRelated = dbFetchAll(
        "SELECT * FROM blog_posts
         WHERE id NOT IN ($placeholders)
           AND (status = 'published' OR (status = 'scheduled' AND published_at <= NOW()))
         ORDER BY published_at DESC
         LIMIT ?",
        array_merge($excludeIds, [3 - count($relatedPosts)])
    );
    $relatedPosts = array_merge($relatedPosts, $moreRelated);
}

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Article Header -->
<article>
    <header class="py-12 md:py-16 bg-navy-900 text-white">
        <div class="max-w-4xl mx-auto px-6">
            <?php if ($post['category']): ?>
            <span class="inline-block bg-orange-500 text-white text-sm font-medium px-3 py-1 rounded-full mb-4">
                <?= e($post['category']) ?>
            </span>
            <?php endif; ?>

            <h1 class="font-heading text-3xl md:text-4xl lg:text-5xl font-bold mb-4"><?= e($post['title']) ?></h1>

            <div class="flex items-center gap-4 text-gray-300">
                <time datetime="<?= date('Y-m-d', strtotime($post['published_at'])) ?>">
                    <?= formatDate($post['published_at'], 'j F Y') ?>
                </time>
                <?php
                $wordCount = str_word_count(strip_tags($post['content']));
                $readTime = max(1, ceil($wordCount / 200));
                ?>
                <span>&middot;</span>
                <span><?= $readTime ?> min read</span>
            </div>
        </div>
    </header>

    <!-- Featured Image -->
    <?php if ($post['featured_image']): ?>
    <div class="bg-cream">
        <div class="max-w-4xl mx-auto px-6 -mt-8">
            <div class="aspect-[3/2] md:aspect-[2/1] overflow-hidden rounded-2xl shadow-xl">
                <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>"
                     class="w-full h-full object-cover">
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Article Content -->
    <div class="py-12 bg-cream">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white rounded-2xl p-8 md:p-12 shadow-sm">
                <div class="prose prose-lg max-w-none">
                    <?= $post['content'] ?>
                </div>
            </div>

            <!-- Share & Tags -->
            <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-gray-600">Share:</span>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(SITE_URL . '/blog/' . $post['slug']) ?>"
                       target="_blank" rel="noopener"
                       class="w-10 h-10 bg-[#0077b5] text-white rounded-full flex items-center justify-center hover:opacity-80 transition-opacity">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode(SITE_URL . '/blog/' . $post['slug']) ?>&text=<?= urlencode($post['title']) ?>"
                       target="_blank" rel="noopener"
                       class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:opacity-80 transition-opacity">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL . '/blog/' . $post['slug']) ?>"
                       target="_blank" rel="noopener"
                       class="w-10 h-10 bg-[#1877f2] text-white rounded-full flex items-center justify-center hover:opacity-80 transition-opacity">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </div>

                <a href="/blog" class="text-orange-500 hover:text-orange-600 font-medium">
                    &larr; Back to Blog
                </a>
            </div>
        </div>
    </div>
</article>

<!-- Related Posts -->
<?php if (!empty($relatedPosts)): ?>
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="font-heading text-2xl font-semibold text-navy-900 mb-8">Related Articles</h2>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($relatedPosts as $related): ?>
            <article class="bg-cream rounded-2xl overflow-hidden hover:shadow-lg transition-shadow">
                <?php if ($related['featured_image']): ?>
                <a href="/blog/<?= e($related['slug']) ?>" class="block aspect-[4/3] overflow-hidden">
                    <img src="<?= e($related['featured_image']) ?>" alt="<?= e($related['title']) ?>"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                </a>
                <?php else: ?>
                <div class="aspect-[4/3] bg-gradient-to-br from-navy-700 to-navy-900"></div>
                <?php endif; ?>
                <div class="p-6">
                    <h3 class="font-heading text-lg font-semibold text-navy-900 mb-2">
                        <a href="/blog/<?= e($related['slug']) ?>" class="hover:text-orange-500 transition-colors">
                            <?= e($related['title']) ?>
                        </a>
                    </h3>
                    <span class="text-gray-400 text-sm"><?= formatDate($related['published_at']) ?></span>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-16 bg-orange-500">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-heading text-3xl font-bold text-white mb-4">Need Professional Health & Safety Support?</h2>
        <p class="text-white/90 text-lg mb-8">Our experienced consultants are here to help your business stay safe and compliant.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/contact" class="bg-white text-orange-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors shadow-lg">
                Get in Touch
            </a>
            <a href="/services" class="bg-orange-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-orange-700 transition-colors">
                View Our Services
            </a>
        </div>
    </div>
</section>

<!-- Article Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": <?= json_encode($post['title']) ?>,
    "description": <?= json_encode($metaDescription) ?>,
    "image": <?= json_encode($ogImage ? SITE_URL . $ogImage : '') ?>,
    "datePublished": <?= json_encode(date('c', strtotime($post['published_at']))) ?>,
    "dateModified": <?= json_encode(date('c', strtotime($post['updated_at']))) ?>,
    "author": {
        "@type": "Organization",
        "name": "Integral Safety Ltd"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Integral Safety Ltd",
        "logo": {
            "@type": "ImageObject",
            "url": "<?= SITE_URL ?><?= e(getSetting('site_logo', '/assets/images/logo.png')) ?>"
        }
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?= SITE_URL ?>/blog/<?= e($post['slug']) ?>"
    }
}
</script>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
