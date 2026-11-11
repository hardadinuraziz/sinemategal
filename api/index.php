<?php

// Ensure /tmp directory structure and SQLite database exist for serverless runtime on Vercel
$storageDirs = [
    '/tmp/views',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

if (! file_exists('/tmp/database.sqlite')) {
    @touch('/tmp/database.sqlite');
}

// Force safe serverless environment variables on Vercel even if Dashboard Env Vars override vercel.json
$vercelOverrides = [
    'VERCEL' => '1',
    'APP_KEY' => $_ENV['APP_KEY'] ?? $_SERVER['APP_KEY'] ?? 'base64:9u5ClZ+qDebsfCvhsmt65K639DQWn0zxzSH0mOCPmkQ=',
    'APP_CONFIG_CACHE' => '/tmp/config.php',
    'APP_EVENTS_CACHE' => '/tmp/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_ROUTES_CACHE' => '/tmp/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'VIEW_COMPILED_PATH' => '/tmp/views',
    'SESSION_DRIVER' => 'cookie',
    'CACHE_STORE' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'LOG_CHANNEL' => 'stderr',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => '/tmp/database.sqlite',
];

foreach ($vercelOverrides as $key => $value) {
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

// Forward the request to Laravel public front controller
require __DIR__.'/../public/index.php';
