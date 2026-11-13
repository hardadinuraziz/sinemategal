@extends('layouts.app')

@section('title', 'Beranda - Sinema Tegal Streaming & Nada Tegalan')

@section('content')
<div class="container home-dashboard">
    <!-- Hero Banner (Cinematic Visual Marquee) -->
    @if($featuredMovie)
    <section class="hero-banner" style="background-image: url('{{ $featuredMovie->banner_url }}');">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-meta-row">
                <span class="badge {{ $featuredMovie->access_badge['class'] }}">
                    {{ $featuredMovie->access_badge['label'] }}
                </span>
                <div class="hero-meta-list">
                    <span>{{ $featuredMovie->release_year }}</span>
                    <span class="meta-dot">•</span>
                    <span>{{ $featuredMovie->duration_string }}</span>
                    @if($featuredMovie->location_tag)
                        <span class="meta-dot">•</span>
                        <span>{{ $featuredMovie->location_tag }}</span>
                    @endif
                    <span class="meta-dot">•</span>
                    <span class="meta-rating">★ {{ $featuredMovie->rating }}</span>
                </div>
            </div>

            <h1 class="hero-title">{{ $featuredMovie->title }}</h1>
            
            @if($featuredMovie->tagline)
                <p class="hero-tagline">"{{ $featuredMovie->tagline }}"</p>
            @endif

            <div class="hero-actions" style="margin-top: 1.25rem;">
                @php
                    $user = Auth::user();
                    $accessCheck = $user ? $user->canAccessMovie($featuredMovie) : ['allowed' => ($featuredMovie->access_type === 'free')];
                @endphp

                @if($accessCheck['allowed'])
                    <a href="{{ route('movies.watch', $featuredMovie->slug) }}" class="btn btn-primary btn-lg pulse-button">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 6px;"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        Putar Film
                    </a>
                @elseif($featuredMovie->access_type === 'ticket')
                    <a href="{{ route('movies.show', $featuredMovie->slug) }}" class="btn btn-crimson btn-lg pulse-button">
                        🎟 Beli Tiket ({{ $featuredMovie->formatted_price }})
                    </a>
                @else
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-primary btn-lg pulse-button">
                        👑 Akses VIP Sebayu
                    </a>
                @endif

                @if($featuredMovie->trailer_url)
                    <button type="button" class="btn btn-secondary btn-lg" onclick="openTrailerModal('{{ addslashes($featuredMovie->title) }}', '{{ $featuredMovie->trailer_url }}', '{{ route('movies.show', $featuredMovie->slug) }}')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8" fill="currentColor"></polygon></svg>
                        Tonton Cuplikan
                    </button>
                @endif

                <a href="{{ route('movies.show', $featuredMovie->slug) }}" class="btn btn-secondary btn-lg">
                    Detail Sinema
                </a>
            </div>
        </div>
    </section>
    @endif

    <section class="home-category-strip" aria-label="Jelajahi kategori film">
        <div class="home-category-heading">
            <span class="eyebrow">PILIHAN BUATMU</span>
            <h2>Jelajahi Kategori</h2>
        </div>
        <div class="home-category-links">
            <a href="{{ route('movies.index') }}" class="home-category-chip is-active">Semua</a>
            @foreach($categories as $category)
                <a href="{{ route('movies.index', ['category' => $category->slug]) }}" class="home-category-chip">
                    {{ $category->name }}
                </a>
            @endforeach
            <a href="{{ route('music.index') }}" class="home-category-chip">Musik</a>
        </div>
    </section>

    <!-- Highlight Stats Bar (Visual Icons) -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="2" y1="7" x2="7" y2="7"></line><line x1="2" y1="17" x2="7" y2="17"></line><line x1="17" y1="17" x2="22" y2="17"></line><line x1="17" y1="7" x2="22" y2="7"></line></svg>
            </div>
            <div>
                <div class="stat-value">{{ $trendingMovies->count() }}+ Sinema</div>
                <div class="stat-label">Film & Dokumenter</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
            </div>
            <div>
                <div class="stat-value">{{ $featuredTracks->count() }}+ Tembang</div>
                <div class="stat-label">Nada Pantura</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
            </div>
            <div>
                <div class="stat-value">Pay-Per-View</div>
                <div class="stat-label">Tiket Mandiri</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
            </div>
            <div>
                <div class="stat-value">Wasgitel VIP</div>
                <div class="stat-label">Akses Seluruh Koleksi</div>
            </div>
        </div>
    </div>

    <!-- Section: Galeri Visual Pesona Sinema & Budaya Tegal -->
    <section class="section">
        <div class="section-header">
            <div>
                <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: var(--gold-600); letter-spacing: 0.05em; margin-bottom: 0.2rem;">
                    EKSPLORASI VISUAL
                </div>
                <h2 class="section-title">Pesona Sinema & Warisan Tegal</h2>
            </div>
            <a href="{{ route('movies.index') }}" class="view-all-link">
                Semua Koleksi →
            </a>
        </div>

        <div class="culture-gallery-grid">
            <a href="{{ route('movies.show', 'sintren-tembang-asmara-muarareja') }}" class="culture-gallery-card">
                <img src="/images/poster-sintren.jpg" alt="Sintren Muarareja" loading="lazy">
                <div class="culture-gallery-overlay">
                    <span class="culture-gallery-tag">Seni Mistis Pesisir</span>
                    <h3 class="culture-gallery-title">Sintren Muarareja</h3>
                </div>
            </a>
            <a href="{{ route('movies.show', 'ki-enthus-susmono-dalang-gila-pesisir') }}" class="culture-gallery-card">
                <img src="/images/poster-enthus.jpg" alt="Wayang Ki Enthus" loading="lazy">
                <div class="culture-gallery-overlay">
                    <span class="culture-gallery-tag">Dalang Maestro</span>
                    <h3 class="culture-gallery-title">Wayang Ki Enthus</h3>
                </div>
            </a>
            <a href="{{ route('movies.show', 'misteri-cerobong-pabrik-gula-jatibarang-1891') }}" class="culture-gallery-card">
                <img src="/images/poster-jatibarang.jpg" alt="Pabrik Gula Jatibarang" loading="lazy">
                <div class="culture-gallery-overlay">
                    <span class="culture-gallery-tag">Warisan Industri 1891</span>
                    <h3 class="culture-gallery-title">PG Jatibarang</h3>
                </div>
            </a>
            <a href="{{ route('movies.show', 'warteg-empire-invasi-rasa-pantura') }}" class="culture-gallery-card">
                <img src="/images/poster-warteg.jpg" alt="Warteg Empire" loading="lazy">
                <div class="culture-gallery-overlay">
                    <span class="culture-gallery-tag">Komedi Kuliner</span>
                    <h3 class="culture-gallery-title">Warteg Empire</h3>
                </div>
            </a>
            <a href="{{ route('movies.show', 'jodoh-segelas-teh-poci-wasgitel') }}" class="culture-gallery-card">
                <img src="/images/poster-tehpoci.jpg" alt="Teh Poci Wasgitel" loading="lazy">
                <div class="culture-gallery-overlay">
                    <span class="culture-gallery-tag">Tradisi Moci</span>
                    <h3 class="culture-gallery-title">Teh Poci Wasgitel</h3>
                </div>
            </a>
        </div>
    </section>

    <!-- Section 1: Film Bioskop Rakyat (Sistem Tiket Premiere) -->
    <section class="section">
        <div class="section-header">
            <div>
                <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: var(--rose-600); letter-spacing: 0.05em; margin-bottom: 0.2rem;">
                    PREMIERE EKSKLUSIF
                </div>
                <h2 class="section-title">
                    Bioskop Rakyat & Tiket Premiere
                </h2>
            </div>
            <a href="{{ route('movies.index', ['access' => 'ticket']) }}" class="view-all-link">
                Daftar Tiket Film →
            </a>
        </div>

        <div class="grid-movies">
            @foreach($ticketMovies as $index => $movie)
                <div class="movie-card">
                    <div class="movie-poster-wrap" data-trailer="{{ $movie->trailer_url }}">
                        <img loading="lazy" decoding="async" src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="movie-poster" loading="lazy">
                        <div class="movie-video-badge"><span class="video-pulse-dot"></span><span>Cuplikan</span></div>
                        <div style="position: absolute; top: 0.75rem; left: 0.75rem; width: 28px; height: 28px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(4px); color: #fff; font-weight: 700; font-size: 0.85rem; border-radius: 6px; display: flex; align-items: center; justify-content: center; z-index: 3;">
                            {{ $loop->iteration }}
                        </div>
                        <div class="movie-badge-top" style="left: 3rem;">
                            <span class="badge badge-ticket">{{ $movie->formatted_price }}</span>
                        </div>
                        <div class="movie-rating">★ {{ $movie->rating }}</div>
                        <div class="movie-card-overlay"></div>
                        
                        <!-- Hover Video & Play Actions -->
                        <div class="movie-card-hover-actions">
                            <a href="{{ route('movies.show', $movie->slug) }}" class="btn-card-action btn-card-play">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                <span>Putar</span>
                            </a>
                            @if($movie->trailer_url)
                                <button type="button" class="btn-card-action btn-card-trailer" onclick="openTrailerModal('{{ addslashes($movie->title) }}', '{{ $movie->trailer_url }}', '{{ route('movies.show', $movie->slug) }}')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8" fill="currentColor"></polygon></svg>
                                    <span>Cuplikan</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="movie-info">
                        <a href="{{ route('movies.show', $movie->slug) }}">
                            <h3 class="movie-title">{{ $movie->title }}</h3>
                        </a>
                        <div class="movie-meta">
                            <span>{{ $movie->release_year }}</span>
                            <span>•</span>
                            <span>{{ $movie->duration_string }}</span>
                            <span>•</span>
                            <span>{{ $movie->category->name }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Section 2: Nada Tegalan (Minimalist Audio Showcase) -->
    <section class="section neu-panel" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem; box-shadow: var(--shadow-sm);">
        <div class="section-header" style="margin-bottom: 1.25rem;">
            <div>
                <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--gold-600); font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.2rem;">
                    ARSIP AUDIO TRADISI
                </div>
                <h2 class="section-title">
                    Nada Tradisi & Tembang Pantura
                </h2>
            </div>
            <a href="{{ route('music.index') }}" class="btn btn-secondary btn-sm">
                Buka Katalog Musik →
            </a>
        </div>

        <div class="grid-tracks">
            @foreach($featuredTracks as $track)
                <div class="track-card" data-track-id="{{ $track->id }}" onclick="window.playTrackById({{ $track->id }})">
                    <div class="track-cover-wrap">
                        <img loading="lazy" decoding="async" src="{{ $track->cover_url }}" alt="{{ $track->title }}" class="track-cover">
                        <div class="track-play-hover">▶</div>
                    </div>
                    <div class="track-card-info">
                        <div class="track-card-title">{{ $track->title }}</div>
                        <div class="track-card-artist">
                            {{ $track->artist }}@if($track->genre) • <span style="color: var(--gold-600); font-weight: 600;">{{ $track->genre }}</span>@endif
                        </div>
                    </div>
                    <div class="track-card-right">
                        @if($track->access_type === 'vip')
                            <span class="badge badge-vip">VIP</span>
                        @else
                            <span class="badge badge-free">GRATIS</span>
                        @endif
                        <span class="track-card-duration">{{ $track->duration_string }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Section 3: Film Langganan VIP Sebayu & Koleksi Wayang Cepak -->
    <section class="section">
        <div class="section-header">
            <div>
                <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: var(--gold-600); letter-spacing: 0.05em; margin-bottom: 0.2rem;">
                    AKSES WASGITEL VIP
                </div>
                <h2 class="section-title">
                    Arsip Wayang & Sejarah Pantura
                </h2>
            </div>
            <a href="{{ route('movies.index', ['access' => 'vip']) }}" class="view-all-link">
                Lihat Koleksi VIP →
            </a>
        </div>

        <div class="grid-movies">
            @foreach($vipMovies as $movie)
                <div class="movie-card">
                    <div class="movie-poster-wrap" data-trailer="{{ $movie->trailer_url }}">
                        <img loading="lazy" decoding="async" src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="movie-poster" loading="lazy">
                        <div class="movie-video-badge"><span class="video-pulse-dot"></span><span>Cuplikan</span></div>
                        <div class="movie-badge-top">
                            <span class="badge badge-vip">VIP SEBAYU</span>
                        </div>
                        <div class="movie-rating">★ {{ $movie->rating }}</div>
                        <div class="movie-card-overlay"></div>
                        
                        <!-- Hover Video & Play Actions -->
                        <div class="movie-card-hover-actions">
                            <a href="{{ route('movies.show', $movie->slug) }}" class="btn-card-action btn-card-play">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                <span>Putar</span>
                            </a>
                            @if($movie->trailer_url)
                                <button type="button" class="btn-card-action btn-card-trailer" onclick="openTrailerModal('{{ addslashes($movie->title) }}', '{{ $movie->trailer_url }}', '{{ route('movies.show', $movie->slug) }}')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8" fill="currentColor"></polygon></svg>
                                    <span>Cuplikan</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="movie-info">
                        <a href="{{ route('movies.show', $movie->slug) }}">
                            <h3 class="movie-title">{{ $movie->title }}</h3>
                        </a>
                        <div class="movie-meta">
                            <span>{{ $movie->release_year }}</span>
                            <span>•</span>
                            <span>{{ $movie->duration_string }}</span>
                            <span>•</span>
                            <span>{{ $movie->category->name }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Section 4: Film Komedi & Tontonan Bebas -->
    <section class="section">
        <div class="section-header">
            <div>
                <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: var(--green-600); letter-spacing: 0.05em; margin-bottom: 0.2rem;">
                    BEBAS NONTON
                </div>
                <h2 class="section-title">
                    Koleksi Komedi & Cerita Rakyat
                </h2>
            </div>
            <a href="{{ route('movies.index', ['access' => 'free']) }}" class="view-all-link">
                Semua Film Bebas →
            </a>
        </div>

        <div class="grid-movies">
            @foreach($freeMovies as $movie)
                <div class="movie-card">
                    <div class="movie-poster-wrap" data-trailer="{{ $movie->trailer_url }}">
                        <img loading="lazy" decoding="async" src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="movie-poster" loading="lazy">
                        <div class="movie-video-badge"><span class="video-pulse-dot"></span><span>Cuplikan</span></div>
                        <div class="movie-badge-top">
                            <span class="badge badge-free">GRATIS</span>
                        </div>
                        <div class="movie-rating">★ {{ $movie->rating }}</div>
                        <div class="movie-card-overlay"></div>
                        
                        <!-- Hover Video & Play Actions -->
                        <div class="movie-card-hover-actions">
                            <a href="{{ route('movies.watch', $movie->slug) }}" class="btn-card-action btn-card-play">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                <span>Putar</span>
                            </a>
                            @if($movie->trailer_url)
                                <button type="button" class="btn-card-action btn-card-trailer" onclick="openTrailerModal('{{ addslashes($movie->title) }}', '{{ $movie->trailer_url }}', '{{ route('movies.show', $movie->slug) }}')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8" fill="currentColor"></polygon></svg>
                                    <span>Cuplikan</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="movie-info">
                        <a href="{{ route('movies.show', $movie->slug) }}">
                            <h3 class="movie-title">{{ $movie->title }}</h3>
                        </a>
                        <div class="movie-meta">
                            <span>{{ $movie->release_year }}</span>
                            <span>•</span>
                            <span>{{ $movie->duration_string }}</span>
                            <span>•</span>
                            <span>{{ $movie->category->name }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Banner Ajakan Langganan VIP (Cinematic Visual Backdrop) -->
    <section class="vip-cinematic-banner" style="background-image: url('/images/banner-vip.jpg');">
        <div class="vip-cinematic-overlay"></div>
        <div class="vip-cinematic-content">
            <span class="badge badge-vip" style="margin-bottom: 0.85rem; font-size: 0.72rem; padding: 4px 10px;">
                👑 WASGITEL VIP PASS
            </span>
            <h2 style="font-size: 2.1rem; font-weight: 800; color: #ffffff; margin-bottom: 0.65rem; letter-spacing: -0.02em; line-height: 1.2;">
                Pelestarian Sinema & Budaya Tegal
            </h2>
            <p style="color: rgba(255, 255, 255, 0.85); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1.5rem;">
                Akses tanpa batas seluruh arsip wayang cepak langka, rekaman Ki Enthus Susmono, dan tembang Pantura kualitas HD.
            </p>
            <div style="display: flex; gap: 0.85rem; flex-wrap: wrap;">
                <a href="{{ route('subscriptions.index') }}" class="btn btn-primary btn-lg pulse-button">
                    Mulai VIP (Rp 29.000/bln)
                </a>
                <a href="{{ route('movies.index') }}" class="btn btn-secondary btn-lg" style="background: rgba(255, 255, 255, 0.15); color: #fff; border-color: rgba(255, 255, 255, 0.3);">
                    Jelajahi Koleksi
                </a>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const audio = window.SinemaAudio;
        if (!audio) {
            return;
        }

        const dearGodSrc = '/audio/dear-god-avenged-sevenfold.mp3';
        const bottomPlayer = document.getElementById('bottomPlayer');
        const playerInner = bottomPlayer?.querySelector('.player-inner');
        const playIcon = document.getElementById('playerPlayIcon');
        const titleEl = document.getElementById('playerTitle');
        const artistEl = document.getElementById('playerArtist');
        const thumbImg = document.getElementById('playerThumb');

        const setDearGodUI = (isPlaying) => {
            if (bottomPlayer) {
                bottomPlayer.classList.remove('is-hidden');
                bottomPlayer.classList.toggle('is-playing', isPlaying);
            }
            if (playerInner) {
                playerInner.classList.remove('is-hidden');
            }
            if (titleEl) {
                titleEl.textContent = 'Dear God';
            }
            if (artistEl) {
                artistEl.textContent = 'Avenged Sevenfold';
            }
            if (thumbImg) {
                thumbImg.src = '/images/logo-icon.svg';
            }
            if (playIcon) {
                playIcon.innerHTML = isPlaying ? '&#10074;&#10074;' : '&#9654;';
            }
        };

        // Load Dear God if no other track is actively playing
        if (audio.paused && (!audio.currentSrc || !audio.currentSrc.includes('dear-god-avenged-sevenfold.mp3'))) {
            audio.src = dearGodSrc;
            audio.loop = true;
            audio.autoplay = true;
            audio.playsInline = true;
            audio.volume = audio.volume || 0.75;
            audio.muted = false;
            setDearGodUI(false);
        }

        const showUnlockPill = (isMutedPlaying = false) => {
            if (document.getElementById('dearGodUnlockBtn')) {
                return;
            }
            const btn = document.createElement('button');
            btn.id = 'dearGodUnlockBtn';
            btn.type = 'button';
            btn.className = 'btn btn-primary';
            btn.style.cssText = 'position:fixed;bottom:1.25rem;left:50%;transform:translateX(-50%);z-index:2100;padding:0.65rem 1.25rem;font-size:0.85rem;display:flex;align-items:center;gap:0.5rem;';
            btn.innerHTML = isMutedPlaying
                ? '<span>🔊</span><span>Aktifkan Suara: Avenged Sevenfold - Dear God</span>'
                : '<span>🎵</span><span>Putar: Avenged Sevenfold - Dear God</span>';
            btn.addEventListener('click', () => startDearGod());
            document.body.appendChild(btn);
        };

        const removeUnlockPill = () => {
            document.getElementById('dearGodUnlockBtn')?.remove();
        };

        const startDearGod = () => {
            if (!audio.currentSrc || !audio.currentSrc.includes('dear-god-avenged-sevenfold.mp3')) {
                return;
            }
            audio.muted = false;
            audio.play().then(() => {
                setDearGodUI(true);
                removeUnlockPill();
            }).catch(() => {
                showUnlockPill(false);
            });
        };

        if (audio.paused && audio.currentSrc.includes('dear-god-avenged-sevenfold.mp3')) {
            // 1. Try full unmuted autoplay immediately
            audio.play().then(() => {
                audio.muted = false;
                setDearGodUI(true);
            }).catch(() => {
                // 2. Browser blocked unmuted autoplay: start muted autoplay immediately so track runs from 0:00,
                //    then unmute seamlessly on the first user gesture
                audio.muted = true;
                audio.play().then(() => {
                    setDearGodUI(true);
                    showUnlockPill(true);
                }).catch(() => {
                    showUnlockPill(false);
                });

                const unmuteOnGesture = () => {
                    if (audio.currentSrc && audio.currentSrc.includes('dear-god-avenged-sevenfold.mp3')) {
                        startDearGod();
                    }
                };
                document.addEventListener('pointerdown', unmuteOnGesture, { once: true, passive: true });
                document.addEventListener('touchstart', unmuteOnGesture, { once: true, passive: true });
                document.addEventListener('keydown', unmuteOnGesture, { once: true, passive: true });
            });
        }
    });
</script>
@endsection
