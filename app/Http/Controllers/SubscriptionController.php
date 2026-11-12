<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = [
            [
                'code' => 'cilik_1m',
                'name' => 'Paket Cilik',
                'tagline' => 'Nggo Nyoba 1 Wulan',
                'price' => 29000,
                'duration_days' => 30,
                'badge' => 'Pemula',
                'features' => [
                    'Nonton kabeh Film Kategori VIP',
                    'Audio Kualitas Standard (128 kbps)',
                    'Nonton ning 1 piranti (HP/Laptop)',
                    'Bebas iklan banner',
                ],
                'popular' => false,
            ],
            [
                'code' => 'wasgitel_3m',
                'name' => 'Paket Wasgitel',
                'tagline' => 'Wangi Sepet Legi Kentel (3 Wulan)',
                'price' => 79000,
                'duration_days' => 90,
                'badge' => 'Paling Laris',
                'features' => [
                    'Kabeh keuntungan Paket Cilik',
                    'Kualitas Video Full HD 1080p',
                    'Audio Hi-Fi Tembang Tegalan (320 kbps)',
                    'Akses eksklusif rekaman langka Ki Enthus',
                    'Diskon 20% kabeh Tiket Premiere Bioskop',
                    'Bisa 2 piranti bebarengan',
                ],
                'popular' => true,
            ],
            [
                'code' => 'lakalaka_1y',
                'name' => 'Paket Laka-Laka',
                'tagline' => 'VIP Sultan Tegal (1 Taun)',
                'price' => 249000,
                'duration_days' => 365,
                'badge' => 'Paling Hemat',
                'features' => [
                    'Akses Bebas Tanpa Batas 1 Taun',
                    'GRATIS 2 Tiket Premiere Film Anyar',
                    'Kualitas Video 4K Ultra HD Cinema',
                    'Badge Profil Khusus "Sultan Bahari"',
                    'Undangan Nonton Bareng Sineas Tegal',
                    'Bisa 4 piranti sak keluarga',
                ],
                'popular' => false,
            ],
        ];

        $user = Auth::user();
        $activeSubscription = $user ? $user->subscriptions()->where('status', 'active')->where('expires_at', '>', now())->latest()->first() : null;

        return view('subscriptions.index', compact('plans', 'activeSubscription'));
    }

    public function subscribe(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('warning', 'Mangga mlebu akun dhisit nggo langganan VIP.');
        }

        $validated = $request->validate([
            'plan_code' => 'required|in:cilik_1m,wasgitel_3m,lakalaka_1y',
            'payment_method' => 'required|string',
        ]);

        $planDetails = [
            'cilik_1m' => ['name' => 'Paket Cilik (1 Bulan)', 'price' => 29000, 'days' => 30],
            'wasgitel_3m' => ['name' => 'Paket Wasgitel (3 Bulan)', 'price' => 79000, 'days' => 90],
            'lakalaka_1y' => ['name' => 'Paket Laka-Laka (1 Tahun)', 'price' => 249000, 'days' => 365],
        ];

        $plan = $planDetails[$validated['plan_code']];
        $price = $plan['price'];
        $paymentMethod = $validated['payment_method'];

        $methodLabels = [
            'dompet_bahari' => 'Dompet Bahari',
            'qris' => 'QRIS Instan',
            'tf_bca' => 'Transfer Bank BCA (VA)',
            'tf_bri' => 'Transfer Bank BRI (VA)',
            'tf_mandiri' => 'Transfer Bank Mandiri (VA)',
            'tf_bni' => 'Transfer Bank BNI (VA)',
        ];
        $methodLabel = $methodLabels[$paymentMethod] ?? strtoupper($paymentMethod);

        if ($paymentMethod === 'dompet_bahari') {
            if ($user->wallet_balance < $price) {
                return back()->with('error', 'Saldo Dompet Bahari sampeyan ora cukup (Kurang Rp '.number_format($price - $user->wallet_balance, 0, ',', '.').'). Mangga top-up saldo dhisik utawa pilih metode QRIS / Transfer Bank.');
            }

            $user->wallet_balance -= $price;
        }

        // Hitung masa aktif
        $startsAt = Carbon::now();
        $expiresAt = ($user->hasVipAccess() && $user->vip_expires_at)
            ? $user->vip_expires_at->copy()->addDays($plan['days'])
            : $startsAt->copy()->addDays($plan['days']);

        $user->is_vip = true;
        $user->vip_plan = $plan['name'];
        $user->vip_expires_at = $expiresAt;
        $user->save();

        Subscription::create([
            'user_id' => $user->id,
            'plan_code' => $validated['plan_code'],
            'plan_name' => $plan['name'],
            'price_paid' => $price,
            'payment_method' => $paymentMethod,
            'status' => 'active',
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
        ]);

        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'purchase_subscription',
            'amount' => $price,
            'reference_id' => 'SUB-'.strtoupper(uniqid()),
            'description' => "Langganan VIP: {$plan['name']} ({$methodLabel})",
        ]);

        return redirect()->route('subscriptions.index')->with('success', "Pembayaran via {$methodLabel} berhasil! Sampeyan saiki dadi Anggota VIP Sinema Tegal ({$plan['name']}) tekan tanggal ".$expiresAt->format('d M Y').'!');
    }
}
