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
                <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}">
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
                        <img src="{{ $rel->poster_url }}" alt="{{ $rel->title }}" class="movie-poster" loading="lazy">
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
                <label class="form-label" style="font-weight: 700; font-size: 0.85rem;">Metode Pembayaran:</label>
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    <label style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.75rem; border-radius: var(--radius-sm); cursor: pointer;">
                        <input type="radio" name="payment_method" value="dompet_bahari" checked>
                        <div>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.88rem;">Dompet Bahari (Instan 1-Klik)</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Potong langsung dari saldo dompet</div>
                        </div>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; background: var(--bg-subtle); border: 1px solid var(--border-color); padding: 0.75rem; border-radius: var(--radius-sm); cursor: pointer;">
                        <input type="radio" name="payment_method" value="qris">
                        <div>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.88rem;">QRIS Instan</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Simulasi pembayaran QRIS</div>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.92rem;">
                Konfirmasi & Aktifkan Tiket
            </button>
        </form>
    </div>
</div>
@endif

@endsection
