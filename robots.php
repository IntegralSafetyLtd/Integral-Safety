<?php
/**
 * Dynamic robots.txt Handler
 * Serves robots.txt content from database settings
 */
require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/seo.php';

// Set content type to plain text
header('Content-Type: text/plain; charset=UTF-8');

// Output the robots.txt content
echo getRobotsTxt();
