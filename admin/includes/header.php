<?php
/**
 * Admin Header Template
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$siteName = getSetting('site_name', SITE_NAME);
$siteLogoWhite = getSetting('site_logo_white', '/assets/images/logo-white.png');
$siteFavicon = getSetting('site_favicon', '/assets/images/favicon.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= e($siteName) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="<?= e($siteFavicon) ?>">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 100: '#e8eef4', 600: '#2a4a6e', 700: '#1e3a5f', 800: '#132337', 900: '#0c1929' },
                        orange: { 100: '#fff0e6', 500: '#e85d04', 600: '#dc5503' }
                    }
                }
            }
        }
    </script>
    <style>
        .quill-editor { border: 1px solid #d1d5db; border-radius: 0.5rem; }
        .quill-editor .ql-toolbar { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; border-bottom: 1px solid #d1d5db; background: #f9fafb; }
        .quill-editor .ql-container { border: none; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; font-size: 14px; }
        .quill-editor .ql-editor { min-height: 280px; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Top Bar -->
    <nav class="bg-navy-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <a href="/admin/" class="flex items-center gap-3">
                        <img src="<?= e($siteLogoWhite) ?>" alt="<?= e($siteName) ?>" class="h-10 w-auto">
                        <span class="font-bold text-lg hidden sm:inline">Admin</span>
                    </a>
                    <div class="hidden md:flex space-x-4">
                        <a href="/admin/" class="px-3 py-2 rounded <?= $currentPage === 'index' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">Dashboard</a>
                        <a href="/admin/pages.php" class="px-3 py-2 rounded <?= $currentPage === 'pages' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">Pages</a>
                        <a href="/admin/services.php" class="px-3 py-2 rounded <?= $currentPage === 'services' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">Services</a>
                        <a href="/admin/training.php" class="px-3 py-2 rounded <?= $currentPage === 'training' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">Training</a>
                        <a href="/admin/testimonials.php" class="px-3 py-2 rounded <?= $currentPage === 'testimonials' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">Testimonials</a>
                        <a href="/admin/blog.php" class="px-3 py-2 rounded <?= $currentPage === 'blog' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">Blog</a>
                        <a href="/admin/gallery.php" class="px-3 py-2 rounded <?= $currentPage === 'gallery' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">Gallery</a>
                        <a href="/admin/seo.php" class="px-3 py-2 rounded <?= $currentPage === 'seo' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">SEO</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/admin/messages.php" class="relative px-3 py-2 rounded hover:bg-navy-700">
                        Messages
                        <?php $unread = getUnreadContactCount(); if ($unread > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"><?= $unread ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="/admin/settings.php" class="px-3 py-2 rounded <?= $currentPage === 'settings' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">Settings</a>
                    <?php if (isAdmin()): ?>
                    <a href="/admin/users.php" class="px-3 py-2 rounded <?= $currentPage === 'users' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">Users</a>
                    <?php endif; ?>
                    <a href="/" target="_blank" class="px-3 py-2 rounded hover:bg-navy-700" title="View Site">View Site</a>
                    <a href="/admin/logout.php" class="px-3 py-2 rounded bg-orange-500 hover:bg-orange-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="md:hidden bg-navy-900 text-white px-4 py-2">
        <div class="flex flex-wrap gap-2">
            <a href="/admin/pages.php" class="px-2 py-1 text-sm rounded hover:bg-navy-700">Pages</a>
            <a href="/admin/services.php" class="px-2 py-1 text-sm rounded hover:bg-navy-700">Services</a>
            <a href="/admin/training.php" class="px-2 py-1 text-sm rounded hover:bg-navy-700">Training</a>
            <a href="/admin/gallery.php" class="px-2 py-1 text-sm rounded hover:bg-navy-700">Gallery</a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="mb-4 px-4 py-3 rounded <?= $_SESSION['flash_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
            <?= e($_SESSION['flash_message']) ?>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>
