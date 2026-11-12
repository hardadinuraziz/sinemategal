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
                    <label class="form-label" style="font-size: 0.82rem; font-weight: 700;">Metode Pembayaran (Simulasi Dummy):</label>
                    <select name="method" id="walletMethodSelect" class="form-control" onchange="updateWalletPaymentPreview()">
                        <option value="QRIS">QRIS Instan (GoPay / OVO / DANA / ShopeePay / m-Banking)</option>
                        <option value="Virtual Account BCA">Transfer Bank — Virtual Account BCA</option>
                        <option value="Virtual Account BRI">Transfer Bank — Virtual Account BRI</option>
                        <option value="Virtual Account Mandiri">Transfer Bank — Virtual Account Mandiri</option>
                        <option value="Virtual Account BNI">Transfer Bank — Virtual Account BNI</option>
                    </select>
                </div>

                <!-- Dummy QRIS Preview Box -->
                <div id="walletQrisPreview" style="background: var(--bg-subtle); border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1.15rem; margin-bottom: 1.25rem; text-align: center;">
                    <div style="display: inline-block; background: rgba(229, 9, 20, 0.15); color: #f87171; border: 1px solid rgba(229, 9, 20, 0.35); font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 4px; letter-spacing: 0.06em; margin-bottom: 0.5rem;">
                        QRIS NASIONAL • MODE SIMULASI
                    </div>
                    <div style="font-weight: 800; color: var(--text-main); font-size: 0.9rem;">PT SINEMA BAHARI TEGAL</div>
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.75rem;">NMID: ID1026028009921 • Terminal: A01</div>
                    <div style="background: #ffffff; padding: 0.65rem; border-radius: 10px; display: inline-block; margin-bottom: 0.65rem;">
                        <svg width="128" height="128" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Simulasi Kode QRIS">
                            <rect width="29" height="29" fill="#FFFFFF"/>
                            <path fill="#0F172A" d="M2 2h7v7H2V2zm2 2v3h3V4H4zm16-2h7v7h-7V2zm2 2v3h3V4h-3zM2 20h7v7H2v-7zm2 2v3h3v-3H4zM11 2h2v2h-2V2zm4 0h2v4h-2V2zm-4 4h2v3h-2V6zm2 3h2v2h-2V9zm4-1h2v3h-2V8zM2 11h3v2H2v-2zm5 0h4v2H7v-2zm6 1h3v3h-3v-3zm5-1h3v2h-3v-2zm5 0h4v2h-4v-2zM4 15h3v3H4v-3zm5 1h2v2H9v-2zm8 0h3v3h-3v-3zm6-1h4v3h-4v-3zM11 20h3v2h-3v-2zm5 0h2v4h-2v-4zm4 1h3v2h-3v-2zm5 0h2v3h-2v-3zM11 24h2v3h-2v-3zm3 1h3v2h-3v-2zm5 0h4v2h-4v-2zm6-1h2v3h-2v-3z"/>
                            <rect x="12" y="12" width="5" height="5" rx="1" fill="#E50914"/>
                        </svg>
                    </div>
                    <div style="font-size: 0.76rem; color: var(--text-muted); line-height: 1.45;">
                        Simulasi QRIS Otomatis: Tidak perlu transfer uang asli. Klik tombol di bawah untuk langsung menambah saldo.
                    </div>
                </div>

                <!-- Dummy Bank Transfer Preview Box -->
                <div id="walletBankPreview" style="display: none; background: var(--bg-subtle); border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1.15rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.7rem; font-weight: 800; color: #fbbf24; text-transform: uppercase; letter-spacing: 0.05em;">
                            SIMULASI TRANSFER VIRTUAL ACCOUNT
                        </span>
                        <span id="walletBankNameBadge" class="badge badge-ticket" style="font-size: 0.68rem;">BANK BCA</span>
                    </div>
                    <div style="font-size: 0.76rem; color: var(--text-muted); margin-bottom: 0.25rem;">Nomor Virtual Account (Dummy):</div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; background: var(--bg-main); border: 1px solid var(--border-color); padding: 0.6rem 0.85rem; border-radius: var(--radius-sm); margin-bottom: 0.6rem;">
                        <span id="walletVaNumber" style="font-family: monospace; font-size: 1.05rem; font-weight: 800; color: var(--text-main); letter-spacing: 0.06em;">8801 0283 9941 001</span>
                        <button type="button" onclick="copyDummyVa('walletVaNumber', this)" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.72rem;">Salin</button>
                    </div>
                    <div style="font-size: 0.76rem; color: var(--text-muted); display: flex; justify-content: space-between;">
                        <span>Penerima: <strong style="color: var(--text-main);">PT Sinema Bahari Tegal</strong></span>
                        <span style="color: var(--accent-emerald); font-weight: 700;">Verifikasi Otomatis</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-size: 0.92rem;">
                    Simulasikan Bayar & Isi Ulang Saldo
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

    function updateWalletPaymentPreview() {
        const select = document.getElementById('walletMethodSelect');
        const qrisBox = document.getElementById('walletQrisPreview');
        const bankBox = document.getElementById('walletBankPreview');
        const bankBadge = document.getElementById('walletBankNameBadge');
        const vaNumber = document.getElementById('walletVaNumber');

        if (!select) return;
        const val = select.value;

        const vaMap = {
            'Virtual Account BCA': { badge: 'BANK BCA', va: '8801 0283 9941 001' },
            'Virtual Account BRI': { badge: 'BANK BRI', va: '1120 0098 7761 234' },
            'Virtual Account Mandiri': { badge: 'BANK MANDIRI', va: '8902 2001 4451 889' },
            'Virtual Account BNI': { badge: 'BANK BNI', va: '9881 4420 7781 002' }
        };

        if (val === 'QRIS') {
            qrisBox.style.display = 'block';
            bankBox.style.display = 'none';
        } else {
            qrisBox.style.display = 'none';
            bankBox.style.display = 'block';
            const info = vaMap[val] || vaMap['Virtual Account BCA'];
            bankBadge.textContent = info.badge;
            vaNumber.textContent = info.va;
        }
    }

    function copyDummyVa(elementId, btn) {
        const text = document.getElementById(elementId)?.textContent?.replace(/\s+/g, '') || '';
        if (navigator.clipboard && text) {
            navigator.clipboard.writeText(text).catch(() => {});
        }
        const original = btn.textContent;
        btn.textContent = 'Tersalin!';
        setTimeout(() => { btn.textContent = original; }, 1500);
    }
</script>
@endsection
