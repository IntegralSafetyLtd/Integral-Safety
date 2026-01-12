<?php
/**
 * Admin Dashboard
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

$user = getCurrentUser();

// Get stats
$stats = [
    'services' => dbFetchOne("SELECT COUNT(*) as count FROM services WHERE is_active = 1")['count'],
    'training' => dbFetchOne("SELECT COUNT(*) as count FROM training WHERE is_active = 1")['count'],
    'testimonials' => dbFetchOne("SELECT COUNT(*) as count FROM testimonials WHERE is_active = 1")['count'],
    'images' => dbFetchOne("SELECT COUNT(*) as count FROM gallery")['count'],
    'messages' => getUnreadContactCount()
];

// Get recent contact submissions
$recentMessages = dbFetchAll("SELECT * FROM contact_submissions ORDER BY submitted_at DESC LIMIT 5");

require_once __DIR__ . '/includes/header.php';
?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-600">Welcome back, <?= e($user['username']) ?></p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <a href="/admin/services.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
        <div class="text-3xl font-bold text-orange-500"><?= $stats['services'] ?></div>
        <div class="text-gray-600">Services</div>
    </a>

    <a href="/admin/training.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
        <div class="text-3xl font-bold text-orange-500"><?= $stats['training'] ?></div>
        <div class="text-gray-600">Training Courses</div>
    </a>

    <a href="/admin/testimonials.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
        <div class="text-3xl font-bold text-orange-500"><?= $stats['testimonials'] ?></div>
        <div class="text-gray-600">Testimonials</div>
    </a>

    <a href="/admin/gallery.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
        <div class="text-3xl font-bold text-orange-500"><?= $stats['images'] ?></div>
        <div class="text-gray-600">Images</div>
    </a>

    <a href="/admin/messages.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
        <div class="text-3xl font-bold <?= $stats['messages'] > 0 ? 'text-red-500' : 'text-orange-500' ?>">
            <?= $stats['messages'] ?>
        </div>
        <div class="text-gray-600">Unread Messages</div>
    </a>
</div>

<!-- Quick Links -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="space-y-2">
            <a href="/admin/pages.php?edit=home" class="block px-4 py-2 bg-gray-50 rounded hover:bg-gray-100">
                Edit Homepage
            </a>
            <a href="/admin/services.php?action=new" class="block px-4 py-2 bg-gray-50 rounded hover:bg-gray-100">
                Add New Service
            </a>
            <a href="/admin/training.php?action=new" class="block px-4 py-2 bg-gray-50 rounded hover:bg-gray-100">
                Add New Training Course
            </a>
            <a href="/admin/gallery.php" class="block px-4 py-2 bg-gray-50 rounded hover:bg-gray-100">
                Upload Images
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Messages</h2>
        <?php if (empty($recentMessages)): ?>
            <p class="text-gray-500">No messages yet.</p>
        <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($recentMessages as $msg): ?>
                <a href="/admin/messages.php?view=<?= $msg['id'] ?>"
                   class="block px-4 py-2 <?= $msg['is_read'] ? 'bg-gray-50' : 'bg-orange-50' ?> rounded hover:bg-gray-100">
                    <div class="font-medium"><?= e($msg['name']) ?></div>
                    <div class="text-sm text-gray-500"><?= e(truncate($msg['message'], 50)) ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- View Site Link -->
<div class="text-center">
    <a href="/" target="_blank" class="text-orange-500 hover:text-orange-600">
        View Live Site &rarr;
    </a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
