<?php
/**
 * Logout
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/auth.php';

logout();
header('Location: /admin/login.php');
exit;
