<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Movie;
use App\Models\PaymentOrder;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_buying_ticket_with_qris_creates_pending_order_and_redirects_to_checkout(): void
    {
        $user = User::factory()->create(['wallet_balance' => 0]);
        $category = Category::create(['name' => 'Tradisi', 'slug' => 'tradisi']);
        $movie = Movie::create([
            'title' => 'Sintren Muarareja',
            'slug' => 'sintren-muarareja',
            'synopsis' => 'Sinopsis film Sintren Muarareja.',
            'director' => 'Sutradara Tegal',
            'video_url' => 'https://example.com/video.mp4',
            'poster_url' => '/images/poster.jpg',
            'banner_url' => '/images/banner.jpg',
            'category_id' => $category->id,
            'ticket_price' => 25000,
            'access_type' => 'ticket',
        ]);

        $response = $this->actingAs($user)->post(route('movies.buy-ticket', $movie->id), [
            'payment_method' => 'qris',
        ]);

        $order = PaymentOrder::first();
        $this->assertNotNull($order);
        $this->assertSame('pending', $order->status);
        $this->assertSame('ticket', $order->purpose);
        $this->assertSame(25000, $order->amount);
        $this->assertSame(0, Ticket::count());

        $response->assertRedirect(route('payments.checkout', $order->order_id));
    }

    public function test_midtrans_webhook_with_valid_signature_fulfills_ticket_idempotently(): void
    {
        config([
            'services.midtrans.server_key' => 'SB-Mid-server-test-key',
            'services.midtrans.client_key' => 'SB-Mid-client-test-key',
            'services.midtrans.is_production' => false,
        ]);

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-123',
            ], 201),
        ]);

        $user = User::factory()->create();
        $category = Category::create(['name' => 'Budaya', 'slug' => 'budaya']);
        $movie = Movie::create([
            'title' => 'Wayang Cepak',
            'slug' => 'wayang-cepak',
            'synopsis' => 'Sinopsis film Wayang Cepak.',
            'director' => 'Sutradara Tegal',
            'video_url' => 'https://example.com/video.mp4',
            'poster_url' => '/images/poster.jpg',
            'banner_url' => '/images/banner.jpg',
            'category_id' => $category->id,
            'ticket_price' => 30000,
            'access_type' => 'ticket',
        ]);

        $this->actingAs($user)->post(route('movies.buy-ticket', $movie->id), [
            'payment_method' => 'tf_bca',
        ]);

        $order = PaymentOrder::firstOrFail();
        $this->assertSame('snap-token-123', $order->snap_token);

        $grossAmount = '30000.00';
        $statusCode = '200';
        $signature = hash('sha512', $order->order_id.$statusCode.$grossAmount.'SB-Mid-server-test-key');

        $webhookPayload = [
            'order_id' => $order->order_id,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'mid-tx-999',
            'signature_key' => $signature,
        ];

        $this->postJson(route('payments.webhook'), $webhookPayload)->assertOk();
        $this->postJson(route('payments.webhook'), $webhookPayload)->assertOk();

        $order->refresh();
        $this->assertSame('paid', $order->status);
        $this->assertSame(1, Ticket::where('user_id', $user->id)->where('movie_id', $movie->id)->count());
    }

    public function test_midtrans_webhook_rejects_invalid_signature(): void
    {
        config(['services.midtrans.server_key' => 'SB-Mid-server-test-key']);

        $this->postJson(route('payments.webhook'), [
            'order_id' => 'STG-TKT-UNKNOWN',
            'status_code' => '200',
            'gross_amount' => '25000.00',
            'signature_key' => 'invalid-signature',
        ])->assertStatus(403);
    }

    public function test_wallet_topup_creates_pending_order_and_credits_balance_on_settlement(): void
    {
        config(['services.midtrans.server_key' => 'SB-Mid-server-test-key']);

        $user = User::factory()->create(['wallet_balance' => 10000]);

        $this->actingAs($user)->post(route('wallet.topup'), [
            'amount' => 50000,
            'method' => 'QRIS',
        ])->assertRedirect();

        $order = PaymentOrder::firstOrFail();
        $this->assertSame('topup', $order->purpose);
        $this->assertSame('pending', $order->status);
        $this->assertSame(10000, (int) $user->fresh()->wallet_balance);

        $signature = hash('sha512', $order->order_id.'20050000.00SB-Mid-server-test-key');

        $this->postJson(route('payments.webhook'), [
            'order_id' => $order->order_id,
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'mid-topup-1',
            'signature_key' => $signature,
        ])->assertOk();

        $this->assertSame(60000, (int) $user->fresh()->wallet_balance);
    }
}
