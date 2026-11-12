@extends('layouts.app')

@section('title', 'Akses Dikunci: ' . $movie->title . ' - Sinema Tegal')

@section('content')
<div class="container" style="padding-top: 3rem;">
    <div class="paywall-card">
        <div class="paywall-icon">🔒</div>

        <h1 style="font-size: 2rem; color: #fff; margin-bottom: 0.5rem;">
            Akses Film Dikunci
        </h1>
        <div style="font-size: 1.1rem; color: var(--gold-400); font-weight: 700; margin-bottom: 1rem;">
            "{{ $movie->title }}"
        </div>

        <p style="color: #cbd5e1; font-size: 1rem; line-height: 1.6; margin-bottom: 2rem;">
            {{ $accessCheck['message'] ?? 'Film kiye butuh hak akses kusus nggo nonton.' }}
        </p>

        @if($movie->access_type === 'ticket')
            <!-- Ticket Paywall Option -->
            <div style="background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.4); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 2rem; text-align: left;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <div>
                        <div style="font-weight: 800; color: #fff; font-size: 1.1rem;">Tiket Premiere Bioskop Rakyat</div>
                        <div style="font-size: 0.8rem; color: #fda4af;">Aktif 7 dina, bisa diputer bola-bali sak karepe</div>
                    </div>
                    <div style="font-size: 1.6rem; font-weight: 900; color: #fff;">
                        {{ $movie->formatted_price }}
                    </div>
                </div>

                <div style="font-size: 0.85rem; color: #cbd5e1; margin-bottom: 1.25rem;">
                    Saldo Dompet Bahari Sampeyan: <strong style="color: var(--gold-400);">Rp {{ number_format(Auth::user()->wallet_balance ?? 0, 0, ',', '.') }}</strong>
                </div>

                <form method="POST" action="{{ route('movies.buy-ticket', $movie->id) }}">
                    @csrf
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: #fff;">Pilih Metode Pembayaran (Simulasi Dummy):</label>
                        <select name="payment_method" class="form-control" style="background: var(--bg-surface); color: var(--text-main); border-color: var(--border-color);">
                            <option value="dompet_bahari">Dompet Bahari (Potong Saldo Akun)</option>
                            <option value="qris">QRIS Instan (GoPay / OVO / DANA / ShopeePay)</option>
                            <option value="tf_bca">Transfer Bank — Virtual Account BCA (8801 0283 9941 001)</option>
                            <option value="tf_bri">Transfer Bank — BRIVA BRI (1120 0098 7761 234)</option>
                            <option value="tf_mandiri">Transfer Bank — Mandiri VA (8902 2001 4451 889)</option>
                            <option value="tf_bni">Transfer Bank — Virtual Account BNI (9881 4420 7781 002)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-crimson" style="width: 100%; padding: 0.85rem; font-size: 1rem;">
                        🎟️ Bayar & Aktifkan Tiket Sekarang
                    </button>
                </form>
            </div>
        @else
            <!-- VIP Subscription Option -->
            <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.4); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 2rem; text-align: left;">
                <div style="font-weight: 800; color: var(--gold-400); font-size: 1.1rem; margin-bottom: 0.4rem;">
                    Langganan VIP Sebayu
                </div>
                <div style="font-size: 0.88rem; color: #cbd5e1; margin-bottom: 1.25rem;">
                    Kanthi langganan VIP, sampeyan bisa nonton kabeh film arsip wayang lan tembang audio kualitas dhuwur tanpa wates.
                </div>
                <a href="{{ route('subscriptions.index') }}" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem;">
                    👑 Deleng Pilihan Paket VIP (Mulai Rp 29.000)
                </a>
            </div>
        @endif

        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="{{ route('movies.show', $movie->slug) }}" class="btn btn-secondary">
                ← Bali menyang Detail Film
            </a>
            <a href="{{ route('movies.index') }}" class="btn btn-secondary">
                Deleng Film Liyane
            </a>
        </div>
    </div>
</div>
@endsection
