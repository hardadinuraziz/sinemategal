@extends('layouts.app')

@section('title', 'Dompet Bahari - Saldo & Transaksi Sinema Tegal')

@section('content')
<div class="container" style="padding-top: 2rem;">
    <!-- Wallet Header & Balance Card -->
    <div class="wallet-grid">
        <!-- Left: Balance Card (Halobun Minimalist Style) -->
        <div class="wallet-card">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div>
                        <span class="badge badge-ticket" style="font-size: 0.72rem; letter-spacing: 0.05em;">DOMPET DIGITAL</span>
                        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-top: 0.35rem;">
                            Dompet Bahari
                        </h2>
                    </div>
                    <span class="brand-badge" style="font-size: 0.75rem;">Simulasi Terverifikasi</span>
                </div>

                <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.35rem; font-weight: 500;">
                    Total Saldo Aktif:
                </div>
                <div class="wallet-balance-amount">
                    <span style="font-size: 1.4rem; color: var(--text-muted); vertical-align: super; font-weight: 600;">Rp</span>
                    {{ number_format($user->wallet_balance, 0, ',', '.') }}
                </div>
                <div style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                    Dapat digunakan untuk pembelian tiket bioskop rakyat premiere dan aktivasi langganan VIP Sebayu secara instan.
                </div>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 1.25rem; margin-top: 1.5rem;">
                <div style="font-size: 0.85rem; color: var(--text-muted);">
                    Pengguna: <strong style="color: var(--text-main);">{{ $user->name }}</strong>
                </div>
                <div style="font-size: 0.82rem; font-weight: 700; color: var(--text-main);">
                    {{ $user->hasVipAccess() ? 'Status: Anggota VIP' : 'Status: Warga Reguler' }}
                </div>
            </div>
        </div>

        <!-- Right: Top-up Form -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 2.25rem; box-shadow: var(--shadow-sm);">
            <div style="margin-bottom: 1.25rem;">
                <span class="badge badge-free" style="font-size: 0.72rem;">INSTANT TOP-UP</span>
                <h3 style="font-size: 1.25rem; color: var(--text-main); font-weight: 800; margin-top: 0.35rem;">
                    Isi Ulang Saldo
                </h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.2rem;">
                    Pilih nominal cepat atau masukkan jumlah sesuai kebutuhan untuk simulasi pengisian.
                </p>
            </div>

            <form method="POST" action="{{ route('wallet.topup') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.82rem; font-weight: 700;">Nominal Cepat:</label>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; margin-bottom: 1rem;">
                        <button type="button" onclick="setAmount(25000)" class="btn btn-secondary btn-sm" style="font-weight: 600;">Rp 25.000</button>
                        <button type="button" onclick="setAmount(50000)" class="btn btn-secondary btn-sm" style="font-weight: 600;">Rp 50.000</button>
                        <button type="button" onclick="setAmount(100000)" class="btn btn-secondary btn-sm" style="font-weight: 600;">Rp 100.000</button>
                        <button type="button" onclick="setAmount(250000)" class="btn btn-secondary btn-sm" style="font-weight: 600;">Rp 250.000</button>
                    </div>

                    <label class="form-label" style="font-size: 0.82rem; font-weight: 700;">Nominal Top-up (Rp):</label>
                    <input type="number" id="topupAmount" name="amount" value="50000" min="10000" max="2000000" step="5000" class="form-control" required style="font-weight: 700; font-size: 1.05rem;">
                </div>

                <div class="form-group">
                    <label class="form-label" style="font-size: 0.82rem; font-weight: 700;">Metode Pembayaran:</label>
                    <select name="method" class="form-control">
                        <option value="QRIS">QRIS (Gopay / OVO / Dana / ShopeePay)</option>
                        <option value="Virtual Account BCA">Virtual Account Bank BCA</option>
                        <option value="Virtual Account BRI">Virtual Account Bank BRI</option>
                        <option value="Virtual Account Mandiri">Virtual Account Bank Mandiri</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-size: 0.92rem;">
                    Proses Isi Ulang Saldo
                </button>
            </form>
        </div>
    </div>

    <!-- Transaction History Table -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 2.25rem; margin-bottom: 3.5rem; box-shadow: var(--shadow-sm);">
        <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <div>
                <h3 style="font-size: 1.25rem; color: var(--text-main); font-weight: 800;">
                    Riwayat Transaksi Dompet Bahari
                </h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.2rem;">
                    Daftar seluruh aktivitas transaksi masuk dan keluar
                </p>
            </div>
            <span class="brand-badge" style="font-size: 0.75rem;">{{ $transactions->count() }} Data</span>
        </div>

        @if($transactions->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 0.75rem 1rem;">Waktu</th>
                            <th style="padding: 0.75rem 1rem;">Jenis</th>
                            <th style="padding: 0.75rem 1rem;">Keterangan</th>
                            <th style="padding: 0.75rem 1rem;">Referensi</th>
                            <th style="padding: 0.75rem 1rem; text-align: right;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $trx)
                            <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s;" onmouseover="this.style.background='var(--bg-main)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 0.85rem 1rem; color: var(--text-muted); font-size: 0.85rem;">
                                    {{ $trx->created_at->format('d M Y, H:i') }}
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    @if($trx->type === 'topup')
                                        <span class="badge badge-free">Top-Up</span>
                                    @elseif($trx->type === 'purchase_ticket')
                                        <span class="badge badge-ticket">Tiket</span>
                                    @else
                                        <span class="badge badge-vip">VIP</span>
                                    @endif
                                </td>
                                <td style="padding: 0.85rem 1rem; color: var(--text-main); font-weight: 600;">
                                    {{ $trx->description }}
                                </td>
                                <td style="padding: 0.85rem 1rem; font-family: monospace; font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $trx->reference_id ?? '-' }}
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: right; font-weight: 700; color: {{ $trx->type === 'topup' ? 'var(--accent-emerald)' : 'var(--rose-600)' }};">
                                    {{ $trx->formatted_amount }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
                {{ $transactions->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted); font-size: 0.9rem;">
                Belum ada catatan riwayat transaksi pada akun ini.
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setAmount(val) {
        document.getElementById('topupAmount').value = val;
    }
</script>
@endsection
