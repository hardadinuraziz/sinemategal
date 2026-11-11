<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

if (env('DB_CONNECTION') === 'sqlite') {
    $dbFile = env('DB_DATABASE') ?: dirname(__DIR__).'/database/database.sqlite';
    if (! str_starts_with($dbFile, '/') && $dbFile !== ':memory:') {
        $dbFile = dirname(__DIR__).'/'.$dbFile;
    }
    if ($dbFile !== ':memory:' && ! file_exists($dbFile)) {
        @touch($dbFile);
    }
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
