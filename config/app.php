<?php
// config/app.php

// Base URL detection
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// Assumes web-ppdb is in the root of htdocs/web-ppdb
// Adjust this path if your folder name is different or nested
$path = '/web-ppdb/';

define('BASE_URL', $protocol . "://" . $host . $path);
define('APP_NAME', 'PPDB Online');

// Timezone
date_default_timezone_set('Asia/Jakarta');


// Start Session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        return BASE_URL . ltrim($path, '/');
    }
}
