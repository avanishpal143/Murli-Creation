<?php
/**
 * =============================================================================
 * MURLI CREATIONS - UNIFIED ROUTER & FRONT CONTROLLER
 * =============================================================================
 * Handles dynamic routing for both local development (php -S 127.0.0.1:8000)
 * and production web servers:
 * - /                     => frontend/index.php
 * - /shop.php             => frontend/shop.php
 * - /product.php          => frontend/product.php
 * - /admin.php            => backend/admin.php
 * - /api/*                => backend/api/*
 * - /assets/*             => frontend/assets/*
 * =============================================================================
 */

$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($rawUri, PHP_URL_PATH);
$path = ltrim($path, '/');

// 1. Static Files: images/ & img-prodcut/ in root
if (!empty($path)) {
    // If exact file exists in root (images/, img-prodcut/, favicon.ico, etc.)
    if (file_exists(__DIR__ . '/' . $path) && !is_dir(__DIR__ . '/' . $path)) {
        return false; // Let PHP built-in server serve static file
    }

    // Static Assets mapping: /assets/... => /frontend/assets/...
    if (strpos($path, 'assets/') === 0) {
        $assetFile = __DIR__ . '/frontend/' . $path;
        if (file_exists($assetFile) && !is_dir($assetFile)) {
            $ext = strtolower(pathinfo($assetFile, PATHINFO_EXTENSION));
            $mimes = [
                'css'   => 'text/css',
                'js'    => 'application/javascript',
                'png'   => 'image/png',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg',
                'webp'  => 'image/webp',
                'svg'   => 'image/svg+xml',
                'gif'   => 'image/gif',
                'ico'   => 'image/x-icon',
                'woff'  => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf'   => 'font/ttf'
            ];
            header('Content-Type: ' . ($mimes[$ext] ?? 'application/octet-stream'));
            readfile($assetFile);
            exit;
        }
    }
}

// 2. Admin Portal Routing
if ($path === 'admin.php' || $path === 'admin' || strpos($path, 'backend/admin') === 0) {
    require __DIR__ . '/backend/admin.php';
    exit;
}

// 3. REST API Routing (/api/cart.php => backend/api/cart.php)
if (strpos($path, 'api/') === 0) {
    $apiFile = substr($path, 4);
    $targetApi = __DIR__ . '/backend/api/' . $apiFile;
    if (file_exists($targetApi)) {
        require $targetApi;
        exit;
    }
}

// 4. Frontend Storefront Routing
if (empty($path) || $path === 'index.php') {
    require __DIR__ . '/frontend/index.php';
    exit;
}

// Check direct frontend file (e.g. /shop.php => frontend/shop.php)
if (file_exists(__DIR__ . '/frontend/' . $path)) {
    require __DIR__ . '/frontend/' . $path;
    exit;
}

// Check without .php extension (e.g. /shop => frontend/shop.php)
if (file_exists(__DIR__ . '/frontend/' . $path . '.php')) {
    require __DIR__ . '/frontend/' . $path . '.php';
    exit;
}

// 5. 404 Fallback
if (file_exists(__DIR__ . '/frontend/404.php')) {
    http_response_code(404);
    require __DIR__ . '/frontend/404.php';
    exit;
}

http_response_code(404);
echo "404 - Page Not Found";
exit;
