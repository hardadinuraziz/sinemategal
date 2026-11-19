<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\PaymentOrder;
use App\Models\Subscription;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly MidtransService $midtrans) {}

    public static function methodLabel(string $paymentMethod): string
    {
        return [
            'dompet_bahari' => 'Dompet Bahari',
            'qris' => 'QRIS Instan',
            'tf_bca' => 'Transfer Bank BCA (VA)',
            'tf_bri' => 'Transfer Bank BRI (VA)',
            'tf_mandiri' => 'Transfer Bank Mandiri (VA)',
            'tf_bni' => 'Transfer Bank BNI (VA)',
        ][$paymentMethod] ?? strtoupper($paymentMethod);
    }

    public function createOrder(
        User $user,
        string $purpose,
        int $amount,
        string $paymentMethod,
        string $itemName,
        ?int $targetId = null,
        ?string $targetCode = null,
    ): PaymentOrder {
        $prefix = match ($purpose) {
            'ticket' => 'TKT',
            'subscription' => 'SUB',
            default => 'TOP',
        };

        $order = PaymentOrder::create([
            'order_id' => 'STG-'.$prefix.'-'.strtoupper(Str::random(10)),
            'user_id' => $user->id,
            'purpose' => $purpose,
            'target_id' => $targetId,
            'target_code' => $targetCode,
            'item_name' => $itemName,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
        ]);

        if ($this->midtrans->isConfigured()) {
            $snap = $this->midtrans->createSnapTransaction($order, $user);
            if (! empty($snap['token']) || ! empty($snap['redirect_url'])) {
                $order->update([
                    'snap_token' => $snap['token'] ?? null,
                    'snap_redirect_url' => $snap['redirect_url'] ?? null,
                ]);
            }
        }

        return $order;
    }

    public function checkout(string $orderId): View|RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $order = PaymentOrder::where('order_id', $orderId)->firstOrFail();
        if ((int) $order->user_id !== (int) $user->id) {
            abort(403);
        }

        if ($order->isPaid()) {
            return $this->redirectAfterPaid($order);
        }

        $midtransReady = $this->midtrans->isConfigured() && filled($order->snap_token);
        $canSimulateSandbox = ! $this->midtrans->isConfigured() || app()->environment('local', 'testing');
        $methodLabel = self::methodLabel($order->payment_method);
        $virtualAccountNumber = match ($order->payment_method) {
            'tf_bca' => '3901'.str_pad((string) $order->id, 10, '0', STR_PAD_LEFT),
            'tf_bri' => '8881'.str_pad((string) $order->id, 10, '0', STR_PAD_LEFT),
            'tf_mandiri' => '7001'.str_pad((string) $order->id, 10, '0', STR_PAD_LEFT),
            'tf_bni' => '8241'.str_pad((string) $order->id, 10, '0', STR_PAD_LEFT),
            default => null,
        };

        return view('payments.checkout', [
            'order' => $order,
            'methodLabel' => $methodLabel,
            'midtransReady' => $midtransReady,
            'snapJsUrl' => $this->midtrans->snapJsUrl(),
            'clientKey' => $this->midtrans->clientKey(),
            'canSimulateSandbox' => $canSimulateSandbox,
            'virtualAccountNumber' => $virtualAccountNumber,
        ]);
    }

    public function checkStatus(string $orderId): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $order = PaymentOrder::where('order_id', $orderId)->firstOrFail();
        if ((int) $order->user_id !== (int) $user->id) {
            abort(403);
        }

        if ($order->isPaid()) {
            return $this->redirectAfterPaid($order);
        }

        if ($this->midtrans->isConfigured()) {
            $statusPayload = $this->midtrans->fetchStatus($order->order_id);
            if (! empty($statusPayload)) {
                $newStatus = $this->midtrans->resolveOrderStatus($statusPayload);
                $this->applyGatewayResult($order->order_id, $newStatus, $statusPayload);
                $order->refresh();

                if ($order->isPaid()) {
                    return $this->redirectAfterPaid($order);
                }
            }
        }

        return back()->with('info', 'Status pembayaran masih menunggu penyelesaian (Pending).');
    }

    public function simulatePaid(string $orderId): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $order = PaymentOrder::where('order_id', $orderId)->firstOrFail();
        if ((int) $order->user_id !== (int) $user->id) {
            abort(403);
        }

        if ($this->midtrans->isConfigured() && ! app()->environment('local', 'testing')) {
            abort(403, 'Simulasi dinonaktifkan saat Midtrans aktif di production.');
        }

        $this->applyGatewayResult($order->order_id, 'paid', [
            'transaction_status' => 'settlement',
            'payment_type' => $order->payment_method.'_sandbox',
            'transaction_id' => 'SBX-'.strtoupper(Str::random(10)),
        ]);

        $order->refresh();

        return $this->redirectAfterPaid($order);
    }

    public function cancel(string $orderId): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $order = PaymentOrder::where('order_id', $orderId)->firstOrFail();
        if ((int) $order->user_id !== (int) $user->id) {
            abort(403);
        }

        if ($order->isPending()) {
            $order->update(['status' => 'cancelled']);
        }

        return match ($order->purpose) {
            'ticket' => redirect()->route('movies.index')->with('info', 'Pembayaran tiket dibatalkan.'),
            'subscription' => redirect()->route('subscriptions.index')->with('info', 'Pembayaran langganan VIP dibatalkan.'),
            default => redirect()->route('wallet.index')->with('info', 'Top-up saldo dibatalkan.'),
        };
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (! $this->midtrans->verifySignature($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $order = PaymentOrder::where('order_id', $orderId)->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $newStatus = $this->midtrans->resolveOrderStatus($payload);
        $this->applyGatewayResult($orderId, $newStatus, $payload);

        return response()->json(['message' => 'OK', 'status' => $newStatus]);
    }

    protected function applyGatewayResult(string $orderId, string $newStatus, array $payload): void
    {
        DB::transaction(function () use ($orderId, $newStatus, $payload): void {
            $order = PaymentOrder::where('order_id', $orderId)->lockForUpdate()->first();
            if (! $order || $order->isPaid()) {
                return;
            }

            $order->gateway_transaction_id = $payload['transaction_id'] ?? $order->gateway_transaction_id;
            $order->gateway_payment_type = $payload['payment_type'] ?? $order->gateway_payment_type;
            $order->payload = $payload;
            $order->status = $newStatus;

            if ($newStatus === 'paid') {
                $order->paid_at = Carbon::now();
                $order->save();
                $this->fulfillOrder($order);
            } else {
                $order->save();
            }
        });
    }

    protected function fulfillOrder(PaymentOrder $order): void
    {
        $user = User::lockForUpdate()->find($order->user_id);
        if (! $user) {
            return;
        }

        $methodLabel = self::methodLabel($order->payment_method);

        if ($order->purpose === 'ticket' && $order->target_id) {
            $movie = Movie::find($order->target_id);
            if ($movie && ! $user->hasTicketForMovie($movie->id)) {
                $ticketCode = 'TGL-'.strtoupper(Str::random(8));
                Ticket::create([
                    'ticket_code' => $ticketCode,
                    'user_id' => $user->id,
                    'movie_id' => $movie->id,
                    'price_paid' => $order->amount,
                    'payment_method' => $order->payment_method,
                    'status' => 'active',
                    'valid_until' => Carbon::now()->addDays(7),
                ]);

                WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'purchase_ticket',
                    'amount' => $order->amount,
                    'reference_id' => $order->order_id,
                    'description' => "Pembelian Tiket Premiere: {$movie->title} ({$methodLabel})",
                ]);
            }

            return;
        }

        if ($order->purpose === 'subscription') {
            $planDays = [
                'cilik_1m' => 30,
                'wasgitel_3m' => 90,
                'lakalaka_1y' => 365,
            ];
            $days = $planDays[$order->target_code] ?? 30;
            $startsAt = Carbon::now();
            $expiresAt = ($user->hasVipAccess() && $user->vip_expires_at)
                ? $user->vip_expires_at->copy()->addDays($days)
                : $startsAt->copy()->addDays($days);

            $user->is_vip = true;
            $user->vip_plan = $order->item_name;
            $user->vip_expires_at = $expiresAt;
            $user->save();

            Subscription::create([
                'user_id' => $user->id,
                'plan_code' => $order->target_code ?? 'cilik_1m',
                'plan_name' => $order->item_name,
                'price_paid' => $order->amount,
                'payment_method' => $order->payment_method,
                'status' => 'active',
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
            ]);

            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'purchase_subscription',
                'amount' => $order->amount,
                'reference_id' => $order->order_id,
                'description' => "Langganan VIP: {$order->item_name} ({$methodLabel})",
            ]);

            return;
        }

        if ($order->purpose === 'topup') {
            $user->wallet_balance += $order->amount;
            $user->save();

            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'topup',
                'amount' => $order->amount,
                'reference_id' => $order->order_id,
                'description' => "Top-up Saldo via {$methodLabel}",
            ]);
        }
    }

    protected function redirectAfterPaid(PaymentOrder $order): RedirectResponse
    {
        $methodLabel = self::methodLabel($order->payment_method);

        if ($order->purpose === 'ticket' && $order->target_id) {
            $movie = Movie::find($order->target_id);
            if ($movie) {
                return redirect()->route('movies.watch', $movie->slug)
                    ->with('success', "Pembayaran via {$methodLabel} berhasil! Order: {$order->order_id}. Selamat nonton, dulur!");
            }

            return redirect()->route('tickets.mine')
                ->with('success', "Pembayaran tiket berhasil! Order: {$order->order_id}.");
        }

        if ($order->purpose === 'subscription') {
            return redirect()->route('subscriptions.index')
                ->with('success', "Pembayaran via {$methodLabel} berhasil! Sampeyan saiki dadi Anggota VIP Sinema Tegal ({$order->item_name})!");
        }

        return redirect()->route('wallet.index')
            ->with('success', 'Top-up saldo Dompet Bahari berhasil! Tambah Rp '.number_format($order->amount, 0, ',', '.').'.');
    }
}
