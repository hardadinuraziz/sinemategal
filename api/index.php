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

// Forward the request to Laravel public front controller
require __DIR__.'/../public/index.php';
