<?php
/**
 * Blog Listing Page
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';

$pageTitle = 'Blog | ' . getSetting('site_name', SITE_NAME);
$metaDescription = 'Health and safety articles, guides, and industry updates from Integral Safety. Expert advice on fire safety, compliance, training, and workplace safety.';

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Get published posts (including scheduled posts whose date has passed)
$posts = dbFetchAll(
    "SELECT * FROM blog_posts
     WHERE status = 'published'
        OR (status = 'scheduled' AND published_at <= NOW())
     ORDER BY published_at DESC
     LIMIT ? OFFSET ?",
    [$perPage, $offset]
);

// Get total count for pagination
$totalCount = dbFetchOne(
    "SELECT COUNT(*) as count FROM blog_posts
     WHERE status = 'published'
        OR (status = 'scheduled' AND published_at <= NOW())"
)['count'];

$totalPages = ceil($totalCount / $perPage);

// Get categories with counts
$categories = dbFetchAll(
    "SELECT category, COUNT(*) as count FROM blog_posts
     WHERE (status = 'published' OR (status = 'scheduled' AND published_at <= NOW()))
       AND category IS NOT NULL AND category != ''
     GROUP BY category
     ORDER BY count DESC"
);

require_once INCLUDES_PATH . '/header.php';
?>

<!-- Hero Section -->
<section class="py-12 bg-navy-900 text-white">
    <div class="max-w-6xl mx-auto px-6">
        <h1 class="font-heading text-4xl md:text-5xl font-bold mb-4">Health & Safety Blog</h1>
        <p class="text-xl text-gray-300">Expert advice, industry updates, and practical guides</p>
    </div>
</section>

<!-- Blog Content -->
<section class="py-16 bg-cream">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid lg:grid-cols-3 gap-12">
            <!-- Posts Grid -->
            <div class="lg:col-span-2">
                <?php if (empty($posts)): ?>
                <div class="bg-white rounded-2xl p-12 text-center shadow-sm">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Coming Soon</h2>
                    <p class="text-gray-600">We're preparing helpful articles on health and safety. Check back soon!</p>
                </div>
                <?php else: ?>
                <div class="grid sm:grid-cols-2 gap-6">
                    <?php foreach ($posts as $post): ?>
                    <article class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow">
                        <?php if ($post['featured_image']): ?>
                        <a href="/blog/<?= e($post['slug']) ?>">
                            <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>"
                                 class="w-full h-48 object-cover">
                        </a>
                        <?php else: ?>
                        <div class="w-full h-48 bg-gradient-to-br from-navy-700 to-navy-900 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <?php if ($post['category']): ?>
                            <span class="text-orange-500 text-sm font-medium"><?= e($post['category']) ?></span>
                            <?php endif; ?>
                            <h2 class="font-heading text-xl font-semibold text-navy-900 mt-1 mb-2">
                                <a href="/blog/<?= e($post['slug']) ?>" class="hover:text-orange-500 transition-colors">
                                    <?= e($post['title']) ?>
                                </a>
                            </h2>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                <?= e($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 150) . '...') ?>
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 text-sm"><?= formatDate($post['published_at']) ?></span>
                                <a href="/blog/<?= e($post['slug']) ?>" class="text-orange-500 text-sm font-medium hover:text-orange-600">
                                    Read more &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="mt-12 flex justify-center gap-2">
                    <?php if ($page > 1): ?>
                    <a href="/blog?page=<?= $page - 1 ?>" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">&larr; Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="/blog?page=<?= $i ?>"
                       class="px-4 py-2 border rounded-lg <?= $i === $page ? 'bg-orange-500 text-white border-orange-500' : 'border-gray-300 hover:bg-gray-50' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                    <a href="/blog?page=<?= $page + 1 ?>" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Next &rarr;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Categories -->
                <?php if (!empty($categories)): ?>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-heading text-lg font-semibold text-navy-900 mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="/blog?category=<?= urlencode($cat['category']) ?>"
                               class="flex items-center justify-between text-gray-600 hover:text-orange-500 transition-colors">
                                <span><?= e($cat['category']) ?></span>
                                <span class="text-gray-400 text-sm">(<?= $cat['count'] ?>)</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- CTA -->
                <div class="bg-navy-900 rounded-2xl p-6 text-white">
                    <h3 class="font-heading text-lg font-semibold mb-3">Need Expert Advice?</h3>
                    <p class="text-white/80 text-sm mb-4">Get in touch with our health and safety consultants for tailored support.</p>
                    <a href="/contact" class="block w-full bg-orange-500 text-white text-center py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                        Contact Us
                    </a>
                </div>

                <!-- Services -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-heading text-lg font-semibold text-navy-900 mb-4">Our Services</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="/services/fire-risk-assessments" class="text-gray-600 hover:text-orange-500 transition-colors">
                                Fire Risk Assessments
                            </a>
                        </li>
                        <li>
                            <a href="/services/consultancy" class="text-gray-600 hover:text-orange-500 transition-colors">
                                H&S Consultancy
                            </a>
                        </li>
                        <li>
                            <a href="/training" class="text-gray-600 hover:text-orange-500 transition-colors">
                                Training Courses
                            </a>
                        </li>
                        <li>
                            <a href="/services" class="text-orange-500 font-medium hover:text-orange-600 transition-colors">
                                View All Services &rarr;
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
