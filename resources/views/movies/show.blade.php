@extends('layouts.app')

@section('title', $movie->title . ' - Sinema Tegal')

@section('content')
<div class="container" style="padding-top: 1.5rem;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
        <a href="{{ route('home') }}" style="color: var(--text-muted);">Beranda</a>
        <span>/</span>
        <a href="{{ route('movies.index') }}" style="color: var(--text-muted);">Sinema</a>
        <span>/</span>
        <span style="color: var(--text-main); font-weight: 600;">{{ $movie->title }}</span>
    </div>

    <!-- Movie Detail Header Banner -->
    <div class="movie-detail-banner">
        <div class="movie-detail-bg" style="background-image: url('{{ $movie->banner_url }}');"></div>
        <div class="movie-detail-gradient"></div>

        <div class="movie-detail-content">
            <!-- Poster Card -->
            <div class="movie-detail-poster">
                <img decoding="async" src="{{ $movie->poster_url }}" alt="{{ $movie->title }}">
            </div>

            <!-- Details -->
            <div class="movie-detail-info">
                <div class="hero-meta-row" style="margin-bottom: 0.75rem;">
                    <span class="badge {{ $movie->access_badge['class'] }}">
                        {{ $movie->access_badge['label'] }}
                    </span>
                    <div class="hero-meta-list">
                        <span>{{ $movie->release_year }}</span>
                        <span class="meta-dot">•</span>
                        <span>{{ $movie->duration_string }}</span>
                        @if($movie->location_tag)
                            <span class="meta-dot">•</span>
                            <span>{{ $movie->location_tag }}</span>
                        @endif
                        <span class="meta-dot">•</span>
                        <span class="meta-rating">★ {{ $movie->rating }}</span>
                    </div>
                </div>

                <h1 class="movie-detail-title">
                    {{ $movie->title }}
                </h1>

                @if($movie->tagline)
                    <p style="font-size: 1rem; color: #cbd5e1; margin-bottom: 1.25rem;">
                        {{ $movie->tagline }}
                    </p>
                @endif

                <!-- Call to action button -->
                <div class="movie-detail-actions">
                    @if($accessCheck['allowed'])
                        <a href="{{ route('movies.watch', $movie->slug) }}" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 0.95rem;">
                            Putar Film Sekarang
                        </a>
                    @elseif($movie->access_type === 'ticket')
                        <button onclick="document.getElementById('ticketModal').classList.add('is-open')" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 0.95rem;">
                            Beli Tiket Premiere ({{ $movie->formatted_price }})
                        </button>
                    @else
                        <a href="{{ route('subscriptions.index') }}" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 0.95rem;">
                            Langganan VIP untuk Menonton
                        </a>
                    @endif

                    @if($movie->trailer_url)
                        <button type="button" class="btn btn-secondary" onclick="openTrailerModal('{{ addslashes($movie->title) }}', '{{ $movie->trailer_url }}')" style="background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.25); color: #ffffff; padding: 0.75rem 1.5rem; font-size: 0.95rem; cursor: pointer;">
                            🎬 Tonton Cuplikan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Columns -->
    <div class="detail-grid">
        <!-- Left: Synopsis & Info -->
        <div>
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 2.25rem; margin-bottom: 2rem; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                    Sinopsis & Latar Cerita
                </h3>
                <p style="font-size: 0.95rem; line-height: 1.8; color: var(--text-body); margin-bottom: 1.75rem;">
                    {{ $movie->synopsis }}
                </p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; background: var(--bg-main); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div>
                        <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Sutradara</div>
                        <div style="font-size: 0.92rem; font-weight: 700; color: var(--text-main); margin-top: 2px;">{{ $movie->director }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Pemeran Utama</div>
                        <div style="font-size: 0.92rem; font-weight: 700; color: var(--text-main); margin-top: 2px;">{{ $movie->cast ?? 'Seniman Pesisir Tegal' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Kategori</div>
                        <div style="font-size: 0.92rem; font-weight: 700; color: var(--text-main); margin-top: 2px;">{{ $movie->category->name }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Total Tayang</div>
                        <div style="font-size: 0.92rem; font-weight: 700; color: var(--accent-emerald); margin-top: 2px;">{{ number_format($movie->views_count) }} kali</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Access Status & Ticket Box -->
        <div>
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 1.75rem; box-shadow: var(--shadow-sm);">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                    Status Akses Anda
                </div>

                @if($accessCheck['allowed'])
                    <div style="background: var(--accent-emerald-subtle); border: 1px solid var(--accent-emerald-border); border-radius: var(--radius-md); padding: 1.25rem; text-align: center; margin-bottom: 1.25rem;">
                        <div style="font-weight: 800; color: var(--accent-emerald); font-size: 0.95rem; margin-bottom: 0.25rem;">
                            Akses Siap Ditonton
                        </div>
                        <div style="font-size: 0.82rem; color: var(--accent-emerald-hover);">
                            @if($movie->access_type === 'free')
                                Film ini tersedia gratis untuk seluruh pengguna.
                            @elseif($hasTicket)
                                Tiket premiere aktif terdaftar pada akun Anda.
                            @else
                                Akun Anda memiliki status langganan VIP aktif.
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('movies.watch', $movie->slug) }}" class="btn btn-primary" style="width: 100%;">
                        Putar Film Sekarang
                    </a>
                @elseif($movie->access_type === 'ticket')
                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; text-align: center; margin-bottom: 1.25rem;">
                        <span class="badge badge-ticket" style="margin-bottom: 0.5rem;">TIKET PREMIERE</span>
                        <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                            Beli tiket satu kali, akses 7 hari sepuasnya
                        </div>
                        <div style="font-size: 1.6rem; font-weight: 900; color: var(--text-main);">
                            {{ $movie->formatted_price }}
                        </div>
                    </div>

                    <button onclick="document.getElementById('ticketModal').classList.add('is-open')" class="btn btn-primary" style="width: 100%;">
                        Beli Tiket Premiere
                    </button>
                @else
                    <div style="background: var(--gold-50); border: 1px solid var(--gold-200); border-radius: var(--radius-md); padding: 1.25rem; text-align: center; margin-bottom: 1.25rem;">
                        <span class="badge badge-vip" style="margin-bottom: 0.5rem;">KHUSUS VIP</span>
                        <div style="font-size: 0.82rem; color: var(--gold-700);">
                            Aktifkan keanggotaan VIP untuk menikmati tayangan ini dan arsip langka lainnya.
                        </div>
                    </div>

                    <a href="{{ route('subscriptions.index') }}" class="btn btn-primary" style="width: 100%;">
                        Lihat Pilihan Langganan
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Movies Section -->
    @if($relatedMovies->count() > 0)
    <section class="section" style="margin-top: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
            <h3 class="section-title" style="margin: 0;">
                Film Terkait Lainnya
            </h3>
            <a href="{{ route('movies.index') }}" class="view-all-link">Lihat Semua →</a>
        </div>
        <div class="grid-movies">
            @foreach($relatedMovies as $rel)
                <div class="movie-card">
                    <div class="movie-poster-wrap">
                        <img decoding="async" src="{{ $rel->poster_url }}" alt="{{ $rel->title }}" class="movie-poster" loading="lazy">
                        <div class="movie-badge-top">
                            <span class="badge {{ $rel->access_badge['class'] }}">{{ $rel->access_badge['label'] }}</span>
                        </div>
                        <div class="movie-rating">★ {{ $rel->rating }}</div>
                        <div class="movie-card-overlay"></div>
                        <a href="{{ route('movies.show', $rel->slug) }}" class="movie-play-btn">▶</a>
                    </div>
                    <div class="movie-info">
                        <a href="{{ route('movies.show', $rel->slug) }}">
                            <h4 class="movie-title">{{ $rel->title }}</h4>
                        </a>
                        <div class="movie-meta">
                            <span>{{ $rel->duration_string }}</span>
                            <span>•</span>
                            <span>{{ $rel->release_year }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif
</div>

<!-- Modal Beli Tiket (Pay-Per-View Checkout) -->
@if($movie->access_type === 'ticket')
<div id="ticketModal" class="modal-backdrop">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('ticketModal').classList.remove('is-open')">✕</button>
        <div style="margin-bottom: 1.5rem;">
            <span class="badge badge-ticket" style="margin-bottom: 0.5rem;">PEMBELIAN TIKET</span>
            <h3 style="font-size: 1.35rem; color: var(--text-main); font-weight: 800; margin-bottom: 0.2rem;">Konfirmasi Pembelian Tiket</h3>
            <p style="color: var(--text-muted); font-size: 0.88rem;">{{ $movie->title }}</p>
        </div>

        <form method="POST" action="{{ route('movies.buy-ticket', $movie->id) }}">
            @csrf
            
            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.6rem; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Biaya Tiket</span>
                    <span style="font-weight: 800; color: var(--text-main); font-size: 1.05rem;">{{ $movie->formatted_price }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.6rem; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Masa Berlaku</span>
                    <span style="color: var(--text-main); font-weight: 600;">7 Hari (Bebas Replay)</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; border-top: 1px dashed var(--border-color); padding-top: 0.6rem;">
                    <span style="color: var(--text-muted);">Saldo Dompet Bahari</span>
                    <span style="font-weight: 700; color: var(--text-main);">
                        Rp {{ number_format(Auth::user()->wallet_balance ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-weight: 700; font-size: 0.85rem;">Metode Pembayaran (Simulasi Dummy):</label>
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    <label style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.75rem; border-radius: var(--radius-sm); cursor: pointer;">
                        <input type="radio" name="payment_method" value="dompet_bahari" checked onchange="toggleTicketPaymentDummy(this.value)">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.88rem;">Dompet Bahari (Instan 1-Klik)</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Potong langsung dari saldo dompet</div>
                        </div>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.75rem; border-radius: var(--radius-sm); cursor: pointer;">
                        <input type="radio" name="payment_method" value="qris" onchange="toggleTicketPaymentDummy(this.value)">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.88rem;">QRIS Instan (GoPay / OVO / DANA / ShopeePay)</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Tampilkan barcode QRIS simulasi</div>
                        </div>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.75rem; border-radius: var(--radius-sm); cursor: pointer;">
                        <input type="radio" name="payment_method" id="ticketBankRadio" value="tf_bca" onchange="toggleTicketPaymentDummy(this.value)">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.88rem;">Transfer Bank / Virtual Account</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">BCA, BRI, Mandiri, BNI (Simulasi Dummy)</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Dummy QRIS Box for Ticket -->
            <div id="ticketQrisDummy" style="display: none; background: var(--bg-subtle); border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.25rem; text-align: center;">
                <div style="display: inline-block; background: rgba(229, 9, 20, 0.15); color: #f87171; border: 1px solid rgba(229, 9, 20, 0.35); font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 4px; letter-spacing: 0.05em; margin-bottom: 0.4rem;">
                    SIMULASI QRIS NASIONAL
                </div>
                <div style="font-weight: 800; color: var(--text-main); font-size: 0.88rem;">PT SINEMA BAHARI TEGAL</div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.6rem;">NMID: ID1026028009921 • Total: {{ $movie->formatted_price }}</div>
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

            <!-- Dummy Bank Transfer Box for Ticket -->
            <div id="ticketBankDummy" style="display: none; background: var(--bg-subtle); border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.25rem;">
                <div style="margin-bottom: 0.65rem;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.35rem;">Pilih Bank Tujuan:</label>
                    <select id="ticketBankSelect" class="form-control" style="padding: 0.45rem 0.65rem; font-size: 0.85rem;" onchange="changeTicketBankDummy(this.value)">
                        <option value="tf_bca">Bank BCA — Virtual Account</option>
                        <option value="tf_bri">Bank BRI — BRIVA</option>
                        <option value="tf_mandiri">Bank Mandiri — Mandiri VA</option>
                        <option value="tf_bni">Bank BNI — Virtual Account</option>
                    </select>
                </div>
                <div style="font-size: 0.74rem; color: var(--text-muted); margin-bottom: 0.25rem;">Nomor Virtual Account (Dummy):</div>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; background: var(--bg-main); border: 1px solid var(--border-color); padding: 0.55rem 0.75rem; border-radius: var(--radius-sm); margin-bottom: 0.5rem;">
                    <span id="ticketVaNumber" style="font-family: monospace; font-size: 0.98rem; font-weight: 800; color: var(--text-main); letter-spacing: 0.05em;">8801 0283 9941 001</span>
                    <button type="button" onclick="copyTicketVa(this)" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.72rem;">Salin</button>
                </div>
                <div style="font-size: 0.73rem; color: var(--text-muted); display: flex; justify-content: space-between;">
                    <span>a.n. <strong style="color: var(--text-main);">PT Sinema Bahari Tegal</strong></span>
                    <span style="color: var(--accent-emerald); font-weight: 700;">Verifikasi Dummy Instan</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.92rem;">
                Konfirmasi Pembayaran & Aktifkan Tiket
            </button>
        </form>
    </div>
</div>

<script>
    function toggleTicketPaymentDummy(val) {
        const qrisBox = document.getElementById('ticketQrisDummy');
        const bankBox = document.getElementById('ticketBankDummy');
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

    function changeTicketBankDummy(bankCode) {
        const radio = document.getElementById('ticketBankRadio');
        const vaSpan = document.getElementById('ticketVaNumber');
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

    function copyTicketVa(btn) {
        const text = document.getElementById('ticketVaNumber')?.textContent?.replace(/\s+/g, '') || '';
        if (navigator.clipboard && text) {
            navigator.clipboard.writeText(text).catch(() => {});
        }
        const orig = btn.textContent;
        btn.textContent = 'Tersalin!';
        setTimeout(() => { btn.textContent = orig; }, 1500);
    }
</script>
@endif

@endsection
