<?php

// Ensure /tmp directory structure exists for serverless runtime on Vercel
$storageDirs = [
    '/tmp/views',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Forward the request to Laravel public front controller
require __DIR__ . '/../public/index.php';
