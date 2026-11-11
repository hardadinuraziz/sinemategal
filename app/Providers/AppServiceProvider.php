<?php

namespace App\Providers;

use App\Models\Movie;
use App\Models\Playlist;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Fallback APP_KEY if missing in cloud deployment environment variables
        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:9u5ClZ+qDebsfCvhsmt65K639DQWn0zxzSH0mOCPmkQ=']);
        }

        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL')) {
            config([
                'app.debug' => true,
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => '/tmp/database.sqlite',
                'database.connections.sqlite.url' => null,
                'view.compiled' => '/tmp/views',
                'session.driver' => 'cookie',
                'cache.default' => 'array',
                'logging.default' => 'stderr',
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['HTTP_HOST']) && (str_contains($_SERVER['HTTP_HOST'], 'railway.app') || str_contains($_SERVER['HTTP_HOST'], 'vercel.app')))) {
            URL::forceScheme('https');
        }

        $this->ensureDatabaseReady();

        View::composer('layouts.app', function (ViewInstance $view): void {
            try {
                $playlists = Schema::hasTable('playlists')
                    && Schema::hasTable('playlist_tracks')
                    && Schema::hasTable('tracks')
                        ? Playlist::with('tracks')->whereIn('title', ['Tembang Dolanan', 'Musik Jawa'])->get()->keyBy('title')
                        : collect();
            } catch (\Throwable) {
                $playlists = collect();
            }

            $view->with([
                'tembangDolananPlaylist' => $playlists->get('Tembang Dolanan'),
                'musikJawaPlaylist' => $playlists->get('Musik Jawa'),
            ]);
        });
    }

    /**
     * Ensure database connection is active and self-healed (migrated & seeded),
     * falling back to /tmp/database.sqlite if the configured database is unreachable.
     */
    protected function ensureDatabaseReady(): void
    {
        try {
            if (config('database.default') === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database', database_path('database.sqlite'));
                if ($dbPath !== ':memory:' && ! file_exists($dbPath)) {
                    @touch($dbPath);
                }
            }

            if (! Schema::hasTable('movies')) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
            } elseif (Movie::count() === 0) {
                Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            Log::warning('Primary database unreachable, falling back to /tmp/database.sqlite: '.$e->getMessage());

            try {
                $fallbackSqlite = '/tmp/database.sqlite';
                if (! file_exists($fallbackSqlite)) {
                    @touch($fallbackSqlite);
                }

                config([
                    'database.default' => 'sqlite',
                    'database.connections.sqlite.database' => $fallbackSqlite,
                    'session.driver' => 'cookie',
                    'cache.default' => 'array',
                ]);

                DB::purge('sqlite');
                DB::reconnect('sqlite');

                if (! Schema::hasTable('movies')) {
                    Artisan::call('migrate', ['--force' => true]);
                    Artisan::call('db:seed', ['--force' => true]);
                } elseif (Movie::count() === 0) {
                    Artisan::call('db:seed', ['--force' => true]);
                }
            } catch (\Throwable $fallbackException) {
                Log::error('Fallback SQLite initialization failed: '.$fallbackException->getMessage());
            }
        }
    }
}
