<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
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

    public function test_user_can_order_via_dummy_qris_and_bank_transfer(): void
    {
        $user = User::where('role', 'user')->firstOrFail();
        $ticketMovie = Movie::where('access_type', 'ticket')->whereDoesntHave('tickets', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->first() ?? Movie::where('access_type', 'ticket')->firstOrFail();

        // 1. Order ticket via Bank Transfer BCA (tf_bca)
        $ticketResponse = $this->actingAs($user)->post(route('movies.buy-ticket', $ticketMovie->id), [
            'payment_method' => 'tf_bca',
        ]);
        $ticketResponse->assertRedirect(route('movies.watch', $ticketMovie->slug));
        $this->assertDatabaseHas('tickets', [
            'user_id' => $user->id,
            'movie_id' => $ticketMovie->id,
            'payment_method' => 'tf_bca',
        ]);

        // 2. Order VIP subscription via QRIS (qris)
        $subResponse = $this->actingAs($user)->post(route('subscriptions.subscribe'), [
            'plan_code' => 'cilik_1m',
            'payment_method' => 'qris',
        ]);
        $subResponse->assertRedirect(route('subscriptions.index'));
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_code' => 'cilik_1m',
            'payment_method' => 'qris',
        ]);

        // 3. Top-up wallet via Bank Transfer Mandiri
        $topupResponse = $this->actingAs($user)->post(route('wallet.topup'), [
            'amount' => 50000,
            'method' => 'Virtual Account Mandiri',
        ]);
        $topupResponse->assertRedirect(route('wallet.index'));
    }
}
