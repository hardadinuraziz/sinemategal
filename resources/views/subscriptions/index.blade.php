@extends('layouts.app')

@section('title', 'Langganan VIP Sebayu - Sinema Tegal')

@section('content')
<div class="container" style="padding-top: 2rem;">
    <!-- Pricing Header -->
    <div style="text-align: center; max-width: 680px; margin: 0 auto 3rem;">
        <span class="badge badge-vip" style="margin-bottom: 0.75rem;">
            PROGRAM VIP SEBAYU
        </span>
        <h1 style="font-size: clamp(1.6rem, 5vw, 2.5rem); font-weight: 800; color: var(--text-main); margin-bottom: 0.75rem; letter-spacing: -0.02em;">
            Pilih Paket Langganan & Dukung Sineas Tegal
        </h1>
        <p style="color: var(--text-muted); font-size: 1rem; line-height: 1.6;">
            Akses bebas seluruh arsip wayang pesisiran Ki Enthus, film dokumenter sejarah, dan alunan tembang Tegalan fidelitas tinggi tanpa jeda iklan.
        </p>
    </div>

    <!-- Active Subscription Banner if user is active VIP -->
    @if(Auth::check() && Auth::user()->hasVipAccess())
    <div style="background: var(--bg-surface); border: 1px solid var(--accent-emerald-border); border-left: 4px solid var(--accent-emerald); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 3rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem; box-shadow: var(--shadow-sm);">
        <div>
            <div style="color: var(--accent-emerald); font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
                Status Langganan Aktif
            </div>
            <div style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                {{ Auth::user()->vip_plan ?? 'Paket VIP Aktif' }}
            </div>
            <div style="color: var(--text-muted); font-size: 0.88rem;">
                Masa berlaku hingga: <strong style="color: var(--text-main);">{{ Auth::user()->vip_expires_at ? Auth::user()->vip_expires_at->format('d F Y, H:i') : 'Permanen' }}</strong>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('movies.index', ['access' => 'vip']) }}" class="btn btn-primary btn-sm">
                Akses Sinema VIP
            </a>
            <a href="{{ route('music.index') }}" class="btn btn-secondary btn-sm">
                Putar Nada Tegalan
            </a>
        </div>
    </div>
    @endif

    <!-- Pricing Cards Grid -->
    <div class="pricing-grid">
        @foreach($plans as $plan)
            <div class="pricing-card {{ $plan['popular'] ? 'is-popular' : '' }}">
                @if($plan['popular'])
                    <div class="pricing-badge">{{ $plan['badge'] }}</div>
                @endif

                <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                    {{ $plan['name'] }}
                </h3>
                <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                    {{ $plan['tagline'] }}
                </div>

                <div class="pricing-price">
                    <span style="font-size: 1.1rem; vertical-align: super; color: var(--text-muted); font-weight: 600;">Rp</span>
                    {{ number_format($plan['price'], 0, ',', '.') }}
                    <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted);">/ {{ $plan['duration_days'] }} hari</span>
                </div>

                <ul class="pricing-features">
                    @foreach($plan['features'] as $feat)
                        <li class="pricing-feature-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent-emerald)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>{{ $feat }}</span>
                        </li>
                    @endforeach
                </ul>

                <button onclick="openSubscribeModal('{{ $plan['code'] }}', '{{ $plan['name'] }}', {{ $plan['price'] }})" 
                        class="btn {{ $plan['popular'] ? 'btn-primary' : 'btn-secondary' }}" 
                        style="width: 100%; padding: 0.75rem; margin-top: auto; font-size: 0.9rem;">
                    {{ Auth::check() && Auth::user()->hasVipAccess() ? 'Perpanjang Masa Aktif' : 'Pilih Paket' }}
                </button>
            </div>
        @endforeach
    </div>

    <!-- FAQ / Pitakonan Umum -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 2.25rem; margin: 3rem 0; box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
            Pertanyaan yang Sering Diajukan (FAQ)
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.75rem;">
            <div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">
                    Bagaimana cara pembayaran langganan?
                </h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">
                    Anda dapat membayar langsung menggunakan saldo <strong>Dompet Bahari</strong>, atau menggunakan simulasi QRIS instan dan Transfer Virtual Account Bank.
                </p>
            </div>
            <div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">
                    Perbedaan Film VIP dan Tiket Premiere?
                </h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">
                    Katalog VIP dapat diakses bebas sepuasnya selama masa aktif langganan. Tiket Premiere berlaku khusus untuk film bioskop rakyat terbaru yang dirilis secara Pay-Per-View.
                </p>
            </div>
            <div>
                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">
                    Kompatibilitas Perangkat?
                </h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">
                    Sinema Tegal dirancang responsif, ringan, dan optimal di seluruh peramban modern ponsel pintar, tablet, maupun layar desktop resolusi tinggi.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Checkout Langganan VIP -->
<div id="subModal" class="modal-backdrop">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('subModal').classList.remove('is-open')">✕</button>
        <div style="margin-bottom: 1.5rem;">
            <span class="badge badge-vip" style="margin-bottom: 0.5rem;">KONFIRMASI LANGGANAN</span>
            <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">Langganan VIP Sebayu</h3>
            <p id="subPlanTitle" style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;"></p>
        </div>

        <form method="POST" action="{{ route('subscriptions.subscribe') }}">
            @csrf
            <input type="hidden" name="plan_code" id="subPlanCode">

            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.6rem; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Biaya Paket</span>
                    <span id="subPlanPrice" style="font-weight: 800; color: var(--text-main); font-size: 1.05rem;"></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; border-top: 1px dashed var(--border-color); padding-top: 0.6rem;">
                    <span style="color: var(--text-muted);">Saldo Dompet Bahari</span>
                    <span style="font-weight: 700; color: var(--text-main);">
                        Rp {{ number_format(Auth::user()->wallet_balance ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-size: 0.85rem; font-weight: 700;">Metode Pembayaran (Simulasi Dummy):</label>
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    <label style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.75rem; border-radius: var(--radius-sm); cursor: pointer;">
                        <input type="radio" name="payment_method" value="dompet_bahari" checked onchange="toggleSubPaymentDummy(this.value)">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.88rem;">Dompet Bahari (Instan 1-Klik)</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Potong otomatis dari saldo akun</div>
                        </div>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.75rem; border-radius: var(--radius-sm); cursor: pointer;">
                        <input type="radio" name="payment_method" value="qris" onchange="toggleSubPaymentDummy(this.value)">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.88rem;">QRIS Instan (GoPay / OVO / DANA / ShopeePay)</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Tampilkan barcode QRIS simulasi</div>
                        </div>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.75rem; border-radius: var(--radius-sm); cursor: pointer;">
                        <input type="radio" name="payment_method" id="subBankRadio" value="tf_bca" onchange="toggleSubPaymentDummy(this.value)">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.88rem;">Transfer Bank / Virtual Account</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">BCA, BRI, Mandiri, BNI (Simulasi Dummy)</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Dummy QRIS Box for Subscription -->
            <div id="subQrisDummy" style="display: none; background: var(--bg-subtle); border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.25rem; text-align: center;">
                <div style="display: inline-block; background: rgba(229, 9, 20, 0.15); color: #f87171; border: 1px solid rgba(229, 9, 20, 0.35); font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 4px; letter-spacing: 0.05em; margin-bottom: 0.4rem;">
                    SIMULASI QRIS NASIONAL
                </div>
                <div style="font-weight: 800; color: var(--text-main); font-size: 0.88rem;">PT SINEMA BAHARI TEGAL</div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.6rem;">NMID: ID1026028009921</div>
                <div style="background: #ffffff; padding: 0.55rem; border-radius: 8px; display: inline-block; margin-bottom: 0.5rem;">
                    <svg width="116" height="116" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Simulasi Kode QRIS">
                        <rect width="29" height="29" fill="#FFFFFF"/>
                        <path fill="#0F172A" d="M2 2h7v7H2V2zm2 2v3h3V4H4zm16-2h7v7h-7V2zm2 2v3h3V4h-3zM2 20h7v7H2v-7zm2 2v3h3v-3H4zM11 2h2v2h-2V2zm4 0h2v4h-2V2zm-4 4h2v3h-2V6zm2 3h2v2h-2V9zm4-1h2v3h-2V8zM2 11h3v2H2v-2zm5 0h4v2H7v-2zm6 1h3v3h-3v-3zm5-1h3v2h-3v-2zm5 0h4v2h-4v-2zM4 15h3v3H4v-3zm5 1h2v2H9v-2zm8 0h3v3h-3v-3zm6-1h4v3h-4v-3zM11 20h3v2h-3v-2zm5 0h2v4h-2v-4zm4 1h3v2h-3v-2zm5 0h2v3h-2v-3zM11 24h2v3h-2v-3zm3 1h3v2h-3v-2zm5 0h4v2h-4v-2zm6-1h2v3h-2v-3z"/>
                        <rect x="12" y="12" width="5" height="5" rx="1" fill="#E50914"/>
                    </svg>
                </div>
                <div style="font-size: 0.74rem; color: var(--text-muted);">
                    Klik tombol konfirmasi di bawah untuk mensimulasikan pembayaran QRIS berhasil.
                </div>
            </div>

            <!-- Dummy Bank Transfer Box for Subscription -->
            <div id="subBankDummy" style="display: none; background: var(--bg-subtle); border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.25rem;">
                <div style="margin-bottom: 0.65rem;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.35rem;">Pilih Bank Tujuan:</label>
                    <select id="subBankSelect" class="form-control" style="padding: 0.45rem 0.65rem; font-size: 0.85rem;" onchange="changeSubBankDummy(this.value)">
                        <option value="tf_bca">Bank BCA — Virtual Account</option>
                        <option value="tf_bri">Bank BRI — BRIVA</option>
                        <option value="tf_mandiri">Bank Mandiri — Mandiri VA</option>
                        <option value="tf_bni">Bank BNI — Virtual Account</option>
                    </select>
                </div>
                <div style="font-size: 0.74rem; color: var(--text-muted); margin-bottom: 0.25rem;">Nomor Virtual Account (Dummy):</div>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; background: var(--bg-main); border: 1px solid var(--border-color); padding: 0.55rem 0.75rem; border-radius: var(--radius-sm); margin-bottom: 0.5rem;">
                    <span id="subVaNumber" style="font-family: monospace; font-size: 0.98rem; font-weight: 800; color: var(--text-main); letter-spacing: 0.05em;">8801 0283 9941 001</span>
                    <button type="button" onclick="copySubVa(this)" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.72rem;">Salin</button>
                </div>
                <div style="font-size: 0.73rem; color: var(--text-muted); display: flex; justify-content: space-between;">
                    <span>a.n. <strong style="color: var(--text-main);">PT Sinema Bahari Tegal</strong></span>
                    <span style="color: var(--accent-emerald); font-weight: 700;">Verifikasi Dummy Instan</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.92rem;">
                Konfirmasi Pembayaran & Aktifkan VIP
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openSubscribeModal(code, name, price) {
        @if(!Auth::check())
            window.location.href = "{{ route('login') }}";
            return;
        @endif
        document.getElementById('subPlanCode').value = code;
        document.getElementById('subPlanTitle').textContent = name;
        document.getElementById('subPlanPrice').textContent = 'Rp ' + Number(price).toLocaleString('id-ID');
        document.getElementById('subModal').classList.add('is-open');
    }

    function toggleSubPaymentDummy(val) {
        const qrisBox = document.getElementById('subQrisDummy');
        const bankBox = document.getElementById('subBankDummy');
        if (!qrisBox || !bankBox) return;

        if (val === 'qris') {
            qrisBox.style.display = 'block';
            bankBox.style.display = 'none';
        } else if (val.startsWith('tf_')) {
            qrisBox.style.display = 'none';
            bankBox.style.display = 'block';
        } else {
            qrisBox.style.display = 'none';
            bankBox.style.display = 'none';
        }
    }

    function changeSubBankDummy(bankCode) {
        const radio = document.getElementById('subBankRadio');
        const vaSpan = document.getElementById('subVaNumber');
        if (radio) {
            radio.value = bankCode;
            radio.checked = true;
        }
        const vaNumbers = {
            'tf_bca': '8801 0283 9941 001',
            'tf_bri': '1120 0098 7761 234',
            'tf_mandiri': '8902 2001 4451 889',
            'tf_bni': '9881 4420 7781 002'
        };
        if (vaSpan) {
            vaSpan.textContent = vaNumbers[bankCode] || vaNumbers['tf_bca'];
        }
    }

    function copySubVa(btn) {
        const text = document.getElementById('subVaNumber')?.textContent?.replace(/\s+/g, '') || '';
        if (navigator.clipboard && text) {
            navigator.clipboard.writeText(text).catch(() => {});
        }
        const orig = btn.textContent;
        btn.textContent = 'Tersalin!';
        setTimeout(() => { btn.textContent = orig; }, 1500);
    }
</script>
@endsection
