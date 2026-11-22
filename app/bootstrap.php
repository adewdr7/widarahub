<?php
define('BASE_PATH', dirname(__DIR__));
$GLOBALS['config'] = require BASE_PATH . '/app/config.php';

require BASE_PATH . '/app/helpers.php';
require BASE_PATH . '/app/db.php';

spl_autoload_register(function (string $class): void {
    foreach (['/app/', '/app/Controllers/'] as $dir) {
        $file = BASE_PATH . $dir . $class . '.php';
        if (is_file($file)) { require $file; return; }
    }
});

date_default_timezone_set(config('timezone'));

if (config('debug')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

set_exception_handler(function (Throwable $e): void {
    error_log((string)$e);
    if (!headers_sent()) http_response_code(500);
    if (config('debug')) {
        echo '<pre style="white-space:pre-wrap;padding:1rem">' . e((string)$e) . '</pre>';
    } else {
        echo 'Terjadi kesalahan pada server.';
    }
});

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

session_name('widarahub');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();
