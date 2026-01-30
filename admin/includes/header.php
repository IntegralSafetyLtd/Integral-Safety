<?php
/**
 * Admin Header Template
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$siteName = getSetting('site_name', SITE_NAME);
$siteLogoWhite = getSetting('site_logo_white', '/assets/images/logo-white.png');
$siteFavicon = getSetting('site_favicon', '/assets/images/favicon.png');

// Define which pages belong to which dropdown
$contentPages = ['pages', 'services', 'training', 'blog', 'testimonials'];
$mediaPages = ['gallery'];
$toolsPages = ['seo', 'messages'];
$accountPages = ['settings', 'users'];

$isContentPage = in_array($currentPage, $contentPages);
$isMediaPage = in_array($currentPage, $mediaPages);
$isToolsPage = in_array($currentPage, $toolsPages);
$isAccountPage = in_array($currentPage, $accountPages);
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

        /* Dropdown styles */
        .nav-dropdown { position: relative; }
        .nav-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 180px;
            background: #132337;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            z-index: 50;
            padding: 0.5rem 0;
            margin-top: 0.25rem;
        }
        .nav-dropdown:hover .nav-dropdown-menu,
        .nav-dropdown-menu:hover { display: block; }
        .nav-dropdown-menu a {
            display: block;
            padding: 0.5rem 1rem;
            color: #fff;
            text-decoration: none;
            white-space: nowrap;
        }
        .nav-dropdown-menu a:hover { background: #1e3a5f; }
        .nav-dropdown-menu a.active { background: #1e3a5f; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Top Bar -->
    <nav class="bg-navy-800 text-white shadow-lg">
        <div class="w-full px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Left side: Logo and main nav -->
                <div class="flex items-center space-x-6">
                    <a href="/admin/" class="flex items-center gap-3 shrink-0">
                        <img src="<?= e($siteLogoWhite) ?>" alt="<?= e($siteName) ?>" class="h-10 w-auto">
                        <span class="font-bold text-lg hidden sm:inline">Admin</span>
                    </a>

                    <div class="hidden md:flex items-center space-x-1">
                        <!-- Dashboard -->
                        <a href="/admin/" class="px-3 py-2 rounded <?= $currentPage === 'index' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">
                            Dashboard
                        </a>

                        <!-- Content Dropdown -->
                        <div class="nav-dropdown">
                            <button class="px-3 py-2 rounded flex items-center gap-1 <?= $isContentPage ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">
                                Content
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="nav-dropdown-menu">
                                <a href="/admin/pages.php" class="<?= $currentPage === 'pages' ? 'active' : '' ?>">Pages</a>
                                <a href="/admin/services.php" class="<?= $currentPage === 'services' ? 'active' : '' ?>">Services</a>
                                <a href="/admin/training.php" class="<?= $currentPage === 'training' ? 'active' : '' ?>">Training</a>
                                <a href="/admin/blog.php" class="<?= $currentPage === 'blog' ? 'active' : '' ?>">Blog</a>
                                <a href="/admin/testimonials.php" class="<?= $currentPage === 'testimonials' ? 'active' : '' ?>">Testimonials</a>
                            </div>
                        </div>

                        <!-- Gallery -->
                        <a href="/admin/gallery.php" class="px-3 py-2 rounded <?= $currentPage === 'gallery' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">
                            Gallery
                        </a>

                        <!-- SEO -->
                        <a href="/admin/seo.php" class="px-3 py-2 rounded <?= $currentPage === 'seo' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">
                            SEO
                        </a>

                        <!-- Messages -->
                        <a href="/admin/messages.php" class="relative px-3 py-2 rounded <?= $currentPage === 'messages' ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">
                            Messages
                            <?php $unread = getUnreadContactCount(); if ($unread > 0): ?>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"><?= $unread ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>

                <!-- Right side: Account menu -->
                <div class="flex items-center space-x-2">
                    <!-- Account Dropdown -->
                    <div class="nav-dropdown">
                        <button class="px-3 py-2 rounded flex items-center gap-1 <?= $isAccountPage ? 'bg-navy-700' : 'hover:bg-navy-700' ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="hidden lg:inline"><?= e($_SESSION['name'] ?? $_SESSION['username'] ?? 'Account') ?></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="nav-dropdown-menu" style="right: 0; left: auto;">
                            <a href="/admin/settings.php" class="<?= $currentPage === 'settings' ? 'active' : '' ?>">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Settings
                                </span>
                            </a>
                            <?php if (isAdmin()): ?>
                            <a href="/admin/users.php" class="<?= $currentPage === 'users' ? 'active' : '' ?>">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Users
                                </span>
                            </a>
                            <?php endif; ?>
                            <a href="/" target="_blank">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    View Site
                                </span>
                            </a>
                            <div class="border-t border-navy-600 my-1"></div>
                            <a href="/admin/logout.php" class="text-orange-400 hover:text-orange-300">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </span>
                            </a>
                        </div>
                    </div>
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
            <a href="/admin/blog.php" class="px-2 py-1 text-sm rounded hover:bg-navy-700">Blog</a>
            <a href="/admin/gallery.php" class="px-2 py-1 text-sm rounded hover:bg-navy-700">Gallery</a>
            <a href="/admin/seo.php" class="px-2 py-1 text-sm rounded hover:bg-navy-700">SEO</a>
            <a href="/admin/settings.php" class="px-2 py-1 text-sm rounded hover:bg-navy-700">Settings</a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="w-full px-4 py-8">
        <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="mb-4 px-4 py-3 rounded <?= $_SESSION['flash_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
            <?= e($_SESSION['flash_message']) ?>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>
