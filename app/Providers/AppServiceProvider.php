<?php

namespace App\Providers;

use App\Models\Playlist;
use Illuminate\Support\Facades\Artisan;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function (ViewInstance $view): void {
            $playlists = Schema::hasTable('playlists')
                && Schema::hasTable('playlist_tracks')
                && Schema::hasTable('tracks')
                    ? Playlist::with('tracks')->whereIn('title', ['Tembang Dolanan', 'Musik Jawa'])->get()->keyBy('title')
                    : collect();

            $view->with([
                'tembangDolananPlaylist' => $playlists->get('Tembang Dolanan'),
                'musikJawaPlaylist' => $playlists->get('Musik Jawa'),
            ]);
        });

        if ($this->app->environment('production') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['HTTP_HOST']) && (str_contains($_SERVER['HTTP_HOST'], 'railway.app') || str_contains($_SERVER['HTTP_HOST'], 'vercel.app')))) {
            URL::forceScheme('https');
        }
        try {
            if (config('database.default') === 'sqlite') {
                $dbPath = database_path('database.sqlite');
                if (! file_exists($dbPath)) {
                    @touch($dbPath);
                }
            }

            // Self-healing: auto-migrate and seed if tables are missing in fresh cloud deployment
            if (! Schema::hasTable('movies')) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Self-healing database initialization: '.$e->getMessage());
        }
    }
}
