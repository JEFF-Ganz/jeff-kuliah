<?php
// PRODUCTION OPTIMIZATION CONFIG - APLIKASI SUPER RINGAN!

// ERROR HANDLING
error_reporting(APP_ENV === 'production' ? 0 : E_ALL);
ini_set('display_errors', APP_ENV === 'production' ? 0 : 1);
ini_set('log_errors', 1);

// MEMORY & PERFORMANCE
ini_set('memory_limit', '32M'); // Minimal memory usage
ini_set('max_execution_time', 30);
ini_set('default_socket_timeout', 30);

// SESSION - EFFICIENT
ini_set('session.use_strict_mode', 1);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_httponly', 1);

// GZIP COMPRESSION
if (extension_loaded('zlib')) {
    ob_start('ob_gzhandler');
    ini_set('zlib.output_compression', 1);
    ini_set('zlib.output_compression_level', 9);
}

// CACHE HEADERS
header('Cache-Control: public, max-age=3600');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

// TIMEZONE
date_default_timezone_set('Asia/Jakarta');

// SECURITY HEADERS
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cache-Control: public, max-age=3600');
    header('Vary: Accept-Encoding');
}

// Output Compression
if (function_exists('ob_start') && !ob_get_level()) {
    ob_start('ob_gzhandler');
}
?>