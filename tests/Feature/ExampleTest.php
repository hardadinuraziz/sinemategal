<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sinema Tegal');
    }

    public function test_serverless_sqlite_fallback_recovers_when_primary_db_is_unreachable(): void
    {
        @unlink('/tmp/database.sqlite');

        Config::set('database.default', 'pgsql');
        Config::set('database.connections.pgsql.host', 'invalid-unreachable-host.local');
        DB::purge('pgsql');

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame('/tmp/database.sqlite', config('database.connections.sqlite.database'));

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sintren: Tembang Asmara Muarareja');
    }
}
