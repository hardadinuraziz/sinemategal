@extends('layouts.app')

@section('title', 'Pembayaran - ' . $order->order_id)

@section('content')
<div class="container" style="max-width: 680px; margin: 24px auto 48px;">
    <div class="stat-card" style="padding: 28px; display: block;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 20px;">
            <div>
                <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.75;">
                    Gateway Pembayaran Resmi
                </div>
                <h1 class="section-title" style="margin: 4px 0 0; font-size: 1.45rem;">
                    Selesaikan Pembayaran
                </h1>
            </div>
            <span style="padding: 6px 14px; border-radius: 999px; font-size: 0.78rem; font-weight: 700; background: rgba(245, 158, 11, 0.18); border: 1px solid rgba(245, 158, 11, 0.45); color: #fbbf24;">
                {{ strtoupper($order->status) }}
            </span>
        </div>

        <div style="border: 1px solid rgba(255,255,255,0.12); border-radius: 14px; padding: 18px; margin-bottom: 22px; background: rgba(0,0,0,0.14);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; gap: 12px;">
                <span style="opacity: 0.75;">Order ID</span>
                <strong style="font-family: monospace;">{{ $order->order_id }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; gap: 12px;">
                <span style="opacity: 0.75;">Item</span>
                <strong style="text-align: right;">{{ $order->item_name }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; gap: 12px;">
                <span style="opacity: 0.75;">Metode</span>
                <strong>{{ $methodLabel }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-top: 12px; border-top: 1px dashed rgba(255,255,255,0.18); font-size: 1.15rem;">
                <span>Total Bayar</span>
                <strong style="color: #34d399;">Rp {{ number_format($order->amount, 0, ',', '.') }}</strong>
            </div>
        </div>

        @if ($midtransReady)
            <div style="margin-bottom: 18px; padding: 14px 16px; border-radius: 12px; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.32); font-size: 0.9rem;">
                Terhubung ke <strong>Midtrans Snap</strong>. Klik tombol di bawah untuk membuka popup QRIS / Virtual Account / E-Wallet.
            </div>

            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <button type="button" id="payWithMidtransBtn" class="btn btn-primary" style="flex: 1; min-width: 220px;">
                    Bayar Sekarang via Midtrans
                </button>
                <form method="POST" action="{{ route('payments.check-status', $order->order_id) }}" id="checkStatusForm">
                    @csrf
                    <button type="submit" class="btn btn-outline">
                        Cek Status Pembayaran
                    </button>
                </form>
            </div>
        @else
            <div style="margin-bottom: 18px; padding: 16px; border-radius: 12px; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.35); font-size: 0.9rem;">
                <strong>Mode Sandbox Lokal (Tanpa Key Midtrans)</strong>
                <p style="margin: 6px 0 0; opacity: 0.88;">
                    Pesanan saat ini berstatus <strong>Pending</strong>. Untuk mengaktifkan popup Midtrans sungguhan (QRIS &amp; VA asli), isi
                    <code>MIDTRANS_SERVER_KEY</code> dan <code>MIDTRANS_CLIENT_KEY</code> di file <code>.env</code>.
                </p>
                @if ($virtualAccountNumber)
                    <div style="margin-top: 12px; padding: 10px 12px; border-radius: 8px; background: rgba(0,0,0,0.25); font-family: monospace;">
                        No. Virtual Account: <strong>{{ $virtualAccountNumber }}</strong>
                    </div>
                @elseif ($order->payment_method === 'qris')
                    <div style="margin-top: 12px; padding: 10px 12px; border-radius: 8px; background: rgba(0,0,0,0.25); font-family: monospace;">
                        QRIS Ref: <strong>QR-{{ $order->order_id }}</strong>
                    </div>
                @endif
            </div>

            @if ($canSimulateSandbox)
                <form method="POST" action="{{ route('payments.simulate-paid', $order->order_id) }}" style="margin-bottom: 12px;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Konfirmasi Pembayaran Berhasil (Sandbox)
                    </button>
                </form>
            @endif
        @endif

        <form method="POST" action="{{ route('payments.cancel', $order->order_id) }}" style="margin-top: 12px;">
            @csrf
            <button type="submit" class="btn btn-outline" style="width: 100%; opacity: 0.85;">
                Batalkan Pesanan
            </button>
        </form>
    </div>
</div>

@if ($midtransReady)
    <script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        (() => {
            const btn = document.getElementById('payWithMidtransBtn');
            const statusForm = document.getElementById('checkStatusForm');
            const snapToken = @json($order->snap_token);

            function openSnap() {
                if (!window.snap || !snapToken) return;
                window.snap.pay(snapToken, {
                    onSuccess: () => statusForm.submit(),
                    onPending: () => statusForm.submit(),
                    onError: () => statusForm.submit(),
                    onClose: () => {}
                });
            }

            if (btn) {
                btn.addEventListener('click', openSnap);
                setTimeout(openSnap, 350);
            }
        })();
    </script>
@endif
@endsection
