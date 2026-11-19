<?php

namespace App\Services;

use App\Models\PaymentOrder;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    public function isProduction(): bool
    {
        return (bool) config('services.midtrans.is_production', false);
    }

    public function serverKey(): ?string
    {
        return config('services.midtrans.server_key');
    }

    public function clientKey(): ?string
    {
        return config('services.midtrans.client_key');
    }

    public function isConfigured(): bool
    {
        return filled($this->serverKey()) && filled($this->clientKey());
    }

    public function snapJsUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    protected function snapApiBaseUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    protected function coreApiBaseUrl(): string
    {
        return $this->isProduction()
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * @return array{token?: string, redirect_url?: string, error?: string}
     */
    public function createSnapTransaction(PaymentOrder $order, User $user): array
    {
        if (! $this->isConfigured()) {
            return ['error' => 'Midtrans keys belum dikonfigurasi.'];
        }

        $channelMap = [
            'qris' => ['gopay', 'other_qris', 'shopeepay'],
            'tf_bca' => ['bca_va'],
            'tf_bri' => ['bri_va'],
            'tf_mandiri' => ['echannel'],
            'tf_bni' => ['bni_va'],
        ];

        $body = [
            'transaction_details' => [
                'order_id' => $order->order_id,
                'gross_amount' => (int) $order->amount,
            ],
            'item_details' => [
                [
                    'id' => $order->target_code ?: (string) ($order->target_id ?? $order->purpose),
                    'price' => (int) $order->amount,
                    'quantity' => 1,
                    'name' => mb_substr($order->item_name, 0, 50),
                ],
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'callbacks' => [
                'finish' => route('payments.checkout', $order->order_id),
            ],
        ];

        if (isset($channelMap[$order->payment_method])) {
            $body['enabled_payments'] = $channelMap[$order->payment_method];
        }

        $response = Http::withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->post($this->snapApiBaseUrl().'/transactions', $body);

        if (! $response->successful()) {
            return ['error' => $response->json('error_messages.0') ?? $response->body()];
        }

        return [
            'token' => $response->json('token'),
            'redirect_url' => $response->json('redirect_url'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function fetchStatus(string $orderId): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $response = Http::withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->get($this->coreApiBaseUrl()."/{$orderId}/status");

        return $response->successful() ? ($response->json() ?? []) : [];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function verifySignature(array $payload): bool
    {
        $serverKey = $this->serverKey();
        if (! $serverKey) {
            return false;
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') {
            return false;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expected, $signatureKey);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function resolveOrderStatus(array $payload): string
    {
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? 'accept');

        return match ($transactionStatus) {
            'capture' => $fraudStatus === 'accept' ? 'paid' : 'failed',
            'settlement' => 'paid',
            'pending' => 'pending',
            'expire' => 'expired',
            'cancel', 'deny' => 'failed',
            default => 'pending',
        };
    }
}
