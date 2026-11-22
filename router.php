<?php
// Router untuk PHP built-in server: php -S localhost:8080 -t public router.php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . '/public' . $path;
if ($path !== '/' && is_file($file) && !preg_match('#/\.#', $path)) {
    return false; // sajikan file statis (css, js, gambar, upload)
}
require __DIR__ . '/public/index.php';
