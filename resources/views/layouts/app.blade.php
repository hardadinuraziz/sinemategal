<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sinema Tegal') - Platform Streaming Film & Nada Khas Tegalan</title>
    <meta name="description" content="Platform streaming film lokal khas Tegal, dokumenter tradisi Sintren, komedi ngapak, wayang cepak Ki Enthus, dan tembang pantura legendaris.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    <!-- Vanilla CSS Stylesheet (Root Relative to prevent HTTPS Mixed Content) -->
    <link rel="stylesheet" href="/css/app.css">
    @yield('styles')
</head>
<body>


    <!-- Navigation Header (Minimalist Modern) -->
    <header class="site-navbar">
        <div class="container nav-container">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="nav-brand">
                <div class="brand-icon brand-icon-logo">
                    <img src="/images/logo.png" alt="Sinema Tegal" style="height: 44px; width: auto; object-fit: contain;">
                </div>
                <span class="brand-badge">Platform Budaya</span>
            </a>

            <!-- Navigation Links -->
            <nav>
                <ul class="nav-links">
                    <li>
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" aria-label="Home" title="Home">
                            <svg class="sidebar-nav-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1z"/></svg><span class="sidebar-nav-label">Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('movies.index') }}" class="nav-link {{ request()->routeIs('movies.*') ? 'active' : '' }}" aria-label="Movies" title="Movies">
                            <svg class="sidebar-nav-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 4v16M17 4v16M3 9h4m-4 6h4m10-6h4m-4 6h4"/></svg><span class="sidebar-nav-label">Movies</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('music.index') }}" class="nav-link {{ request()->routeIs('music.*') ? 'active' : '' }}" aria-label="Music" title="Music">
                            <svg class="sidebar-nav-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg><span class="sidebar-nav-label">Music</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('subscriptions.index') }}" class="nav-link {{ request()->routeIs('subscriptions.*') ? 'active' : '' }}" aria-label="Discover" title="Discover">
                            <svg class="sidebar-nav-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4M11 7l2 4-4 2 2-6z"/></svg><span class="sidebar-nav-label">Discover</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tickets.mine') }}" class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}" aria-label="Library" title="Library">
                            <svg class="sidebar-nav-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21z"/><path d="M4 5.5v13A2.5 2.5 0 0 1 6.5 16H20M8 7h8"/></svg><span class="sidebar-nav-label">Library</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Actions / Auth -->
            <div class="nav-actions">
                @auth
                    <!-- Dompet Bahari Pill -->
                    <a href="{{ route('wallet.index') }}" class="user-wallet-badge" title="Saldo Dompet Bahari" aria-label="Saldo Dompet Bahari">
                        <svg class="sidebar-nav-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 9h18M16 15h2"/><path d="M6 5V3h12v2"/></svg>
                        <span style="color: var(--text-muted); font-size: 0.75rem;">Saldo:</span>
                        <span>Rp {{ number_format(Auth::user()->wallet_balance, 0, ',', '.') }}</span>
                    </a>

                    <!-- User Profile Dropdown Pill -->
                    <div class="user-menu-pill">
                        <img src="{{ Auth::user()->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100' }}" alt="Avatar" class="user-avatar">
                        <span style="font-weight: 600; color: var(--text-main);">{{ Str::limit(Auth::user()->name, 14) }}</span>
                        @if(Auth::user()->hasVipAccess())
                            <span class="badge badge-vip" style="padding: 1px 5px; font-size: 0.6rem;">VIP</span>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" style="margin-left: 0.25rem;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem;" title="Keluar" aria-label="Keluar">
                                <svg class="sidebar-nav-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/></svg><span class="sidebar-nav-label">Keluar</span>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-sm" aria-label="Masuk" title="Masuk"><svg class="sidebar-nav-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5m5 5H3"/></svg><span class="sidebar-nav-label">Masuk</span></a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm" aria-label="Daftar" title="Daftar"><svg class="sidebar-nav-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"/><path d="M3 21v-2a6 6 0 0 1 12 0v2M19 8v6m-3-3h6"/></svg><span class="sidebar-nav-label">Daftar</span></a>
                @endauth

                <!-- Mobile Hamburger Toggle Button -->
                <button type="button" class="mobile-menu-toggle" id="mobileMenuBtn" aria-label="Buka Menu Navigasi" aria-expanded="false">
                    <svg class="hamburger-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                    <svg class="close-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer -->
    <div id="mobileNavDrawer" class="mobile-nav-drawer">
        <div class="mobile-nav-inner">
            <!-- Drawer Brand Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle); margin-bottom: 0.25rem;">
                <img src="/images/logo.png" alt="Sinema Tegal" style="height: 42px; width: auto; object-fit: contain;">
                <span class="brand-badge">Platform Budaya</span>
            </div>
            <nav class="mobile-nav-links">
                <a href="{{ route('home') }}" class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            <span>Home</span>
                </a>
                <a href="{{ route('movies.index') }}" class="mobile-nav-link {{ request()->routeIs('movies.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="2" y1="7" x2="7" y2="7"></line><line x1="2" y1="17" x2="7" y2="17"></line><line x1="17" y1="17" x2="22" y2="17"></line><line x1="17" y1="7" x2="22" y2="7"></line></svg>
                    <span>Movies</span>
                </a>
                <a href="{{ route('music.index') }}" class="mobile-nav-link {{ request()->routeIs('music.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                    <span>Music</span>
                </a>
                <a href="{{ route('subscriptions.index') }}" class="mobile-nav-link {{ request()->routeIs('subscriptions.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                    <span>Discover</span>
                </a>
                <a href="{{ route('tickets.mine') }}" class="mobile-nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"></path><path d="M8 8h8M8 12h8M8 16h5"></path></svg>
                    <span>Library</span>
                </a>
                @auth
                <a href="{{ route('wallet.index') }}" class="mobile-nav-link {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <span>Dompet Bahari</span>
                        <span style="font-weight: 700; color: var(--green-700);">Rp {{ number_format(Auth::user()->wallet_balance, 0, ',', '.') }}</span>
                    </div>
                </a>
                @endauth
            </nav>

            <div class="mobile-nav-footer">
                @auth
                    <div class="mobile-user-profile">
                        <img src="{{ Auth::user()->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100' }}" alt="Avatar" class="user-avatar" style="width: 38px; height: 38px;">
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->email }}</div>
                        </div>
                        @if(Auth::user()->hasVipAccess())
                            <span class="badge badge-vip" style="font-size: 0.65rem;">VIP</span>
                        @endif
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="margin-top: 0.75rem;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                            Keluar dari Akun
                        </button>
                    </form>
                @else
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <a href="{{ route('login') }}" class="btn btn-secondary" style="justify-content: center;">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary" style="justify-content: center;">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="container">
        @if(session('success'))
            <div class="flash-message flash-success">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="flash-message flash-error">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="flash-message flash-warning">
                <span>💡</span>
                <span>{{ session('warning') }}</span>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main style="flex-grow: 1;">
        @yield('content')
    </main>

    <!-- Minimalist Cultural Footer (Halobun Style) -->
    <footer style="background: #ffffff; border-top: 1px solid var(--border-subtle); padding: 3.5rem 0 2rem; margin-top: 4rem;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 2.5rem; margin-bottom: 2.5rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.6rem; font-size: 1.25rem; font-weight: 900; margin-bottom: 0.8rem; color: var(--text-main);">
                        <div class="brand-icon" style="width: 30px; height: 30px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="5 3 19 12 5 21 5 3"></polygon>
                            </svg>
                        </div>
                        <span style="font-weight: 800; color: var(--text-main);">SINEMA</span><span style="font-weight: 800; color: var(--gold-600);">TEGAL</span>
                    </div>
                    <p style="color: var(--text-dim); font-size: 0.88rem; line-height: 1.6;">
                        Platform digital nguri-uri kabudayan Tegal: Tari Mistik Sintren Muarareja, Dokumenter Ki Enthus Susmono, Drama Komedi Ngapak, sarta tembang Pantura legendaris.
                    </p>
                </div>
                <div>
                    <h4 style="font-size: 0.92rem; font-weight: 800; color: var(--text-main); margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">Kategori Budaya</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.88rem; color: var(--text-muted);">
                        <li><a href="{{ route('movies.index', ['category' => 'tradisi-mistik-sintren']) }}" style="color: var(--text-muted);">Kesenian Tari Sintren</a></li>
                        <li><a href="{{ route('movies.index', ['category' => 'wayang-cepak-ki-enthus']) }}" style="color: var(--text-muted);">Wayang Cepak Ki Enthus</a></li>
                        <li><a href="{{ route('movies.index', ['category' => 'komedi-drama-ngapak']) }}" style="color: var(--text-muted);">Sinema Komedi Ngapak</a></li>
                        <li><a href="{{ route('movies.index', ['category' => 'sejarah-budaya-bahari']) }}" style="color: var(--text-muted);">Babad Ki Gede Sebayu</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="font-size: 0.92rem; font-weight: 800; color: var(--text-main); margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">Layanan & Fitur</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.88rem; color: var(--text-muted);">
                        <li><a href="{{ route('subscriptions.index') }}" style="color: var(--text-muted);">Paket Langganan VIP Wasgitel</a></li>
                        <li><a href="{{ route('movies.index', ['access' => 'ticket']) }}" style="color: var(--text-muted);">Bioskop Tiket Premiere</a></li>
                        <li><a href="{{ route('music.index') }}" style="color: var(--text-muted);">Arsip Nada Tegalan Pantura</a></li>
                        <li><a href="{{ route('wallet.index') }}" style="color: var(--text-muted);">Dompet Bahari Digital</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="font-size: 0.92rem; font-weight: 800; color: var(--text-main); margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">Filosofi Tegal</h4>
                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                        <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">
                            "Wasgitel: Wangi, Sepet, Legi, tur Kentel."
                        </div>
                        <div style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.6;">
                            Karakter khas wong Tegal: ceplas-ceplos nanging tulus, guyub rukun sinambi ngeteh poci tanah liat.
                        </div>
                    </div>
                </div>
            </div>
            <div style="text-align: center; border-top: 1px solid var(--border-color); padding-top: 1.5rem; font-size: 0.82rem; color: var(--text-muted);">
                © 2026 Sinema Tegal • Platform Streaming & Pelestarian Budaya Bahari
            </div>
        </div>
    </footer>

    <!-- Persistent Bottom Audio Player (Spotify / YouTube Music) -->
    <div id="bottomPlayer" class="bottom-player is-hidden" role="region" aria-label="Pemutar musik">
        <div class="player-inner is-hidden">
            <!-- Left: Current Playing Info -->
            <div class="player-track-info">
                <img id="playerThumb" src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=100" alt="Track Cover" class="player-thumb">
                <div class="player-details">
                    <div id="playerTitle" class="player-track-title">Tegal Laka-Laka</div>
                    <div id="playerArtist" class="player-track-artist">Alm. Ki Enthus Susmono</div>
                </div>
            </div>

            <!-- Center: Controls & Timeline -->
            <div class="player-controls-wrap">
                <div class="player-buttons">
                    <button type="button" id="playerPrevBtn" class="player-ctrl-btn" title="Lagu Sebelumnya" aria-label="Lagu sebelumnya">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="19 20 9 12 19 4 19 20"></polygon><line x1="5" y1="19" x2="5" y2="5" stroke="currentColor" stroke-width="2"></line></svg>
                    </button>
                    <button type="button" id="playerPlayBtn" class="player-play-btn" title="Putar/Jeda" aria-label="Putar atau jeda">
                        <span id="playerPlayIcon">&#9654;</span>
                    </button>
                    <button type="button" id="playerNextBtn" class="player-ctrl-btn" title="Lagu Berikutnya" aria-label="Lagu berikutnya">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 4 15 12 5 20 5 4"></polygon><line x1="19" y1="5" x2="19" y2="19" stroke="currentColor" stroke-width="2"></line></svg>
                    </button>
                </div>
                <div class="player-timeline">
                    <span id="playerCurrentTime">00:00</span>
                    <div id="playerSeekBar" class="seek-bar-container">
                        <div id="playerSeekFill" class="seek-bar-fill"></div>
                    </div>
                    <span id="playerTotalTime">03:45</span>
                </div>
            </div>

            <!-- Right: Extra Actions & Volume -->
            <div class="player-extras">
                <button type="button" id="playlistToggleBtn" class="btn btn-secondary btn-sm playlist-toggle-btn" aria-expanded="false" aria-controls="rightPlaylistPanel">
                    Koleksi Musik
                </button>
                <button id="lyricsToggleBtn" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;">
                    Lirik
                </button>
                <div class="volume-container">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted);"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                    <input type="range" id="playerVolume" class="volume-slider" min="0" max="100" value="80">
                </div>
            </div>
        </div>
        <div id="youtubePlayerContainer" class="youtube-player-container" hidden>
            <iframe id="youtubePlayerFrame" title="Pemutar musik eksternal" allow="autoplay; encrypted-media; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
        <section id="rightPlaylistPanel" class="right-playlist-panel" aria-label="Koleksi musik">
          <section class="right-playlist-collection" aria-labelledby="tembangDolananTitle">
            <div class="right-playlist-heading">
                <div>
                    <span class="right-playlist-eyebrow">KOLEKSI LAGU ANAK</span>
                    <h2 id="tembangDolananTitle">Tembang Dolanan</h2>
                    <p>Lagu Anak Tradisional</p>
                </div>
                @if($tembangDolananPlaylist)
                    <button type="button" class="playlist-play-all" onclick="window.playPlaylist({{ $tembangDolananPlaylist->id }})" aria-label="Putar semua Tembang Dolanan">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    </button>
                @endif
            </div>

            @if($tembangDolananPlaylist && $tembangDolananPlaylist->tracks->isNotEmpty())
                <ol class="right-playlist-tracks">
                    @foreach($tembangDolananPlaylist->tracks as $index => $track)
                        <li>
                            <button type="button" class="tembang-dolanan-track" data-track-id="{{ $track->id }}" onclick="window.playTrackById({{ $track->id }})">
                                <span class="playlist-track-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="playlist-track-copy">
                                    <span class="playlist-track-title">{{ $track->title }}</span>
                                    <span class="playlist-track-artist">{{ $track->artist }}</span>
                                </span>
                                <span class="playlist-track-duration">{{ $track->duration_string }}</span>
                            </button>
                        </li>
                    @endforeach
                </ol>
                <p class="playlist-attribution">
                    Audio: <a href="https://commons.wikimedia.org/wiki/File:Gundul-Gundul_Pacul.ogg" target="_blank" rel="noopener">Putri Sari Wahyuningsih &amp; Sekar Kinanthi</a>,
                    <a href="https://commons.wikimedia.org/wiki/File:Gending_Lir-Ilir.ogg" target="_blank" rel="noopener">Dewinta Sanggar Seni Laras Siwi</a>, dan
                    <a href="https://commons.wikimedia.org/wiki/File:Cublak-Cublak_Suweng.ogg" target="_blank" rel="noopener">Kinanthi Nugroho &amp; Fandy Aprianto Rohman</a> (CC BY-SA 4.0). Suwe Ora Jamu dan Menthok-Menthok memakai audio demo sementara.
                </p>
            @else
                <p class="playlist-empty">Koleksi lagu akan tampil setelah data playlist dimuat.</p>
            @endif
          </section>

          <section class="right-playlist-collection" aria-labelledby="musikJawaTitle">
            <div class="right-playlist-heading">
                <div>
                    <span class="right-playlist-eyebrow">PILIHAN LAGU JAWA</span>
                    <h2 id="musikJawaTitle">Musik Jawa</h2>
                    <p>Tembang klasik &amp; populer</p>
                </div>
                @if($musikJawaPlaylist)
                    <button type="button" class="playlist-play-all" onclick="window.playPlaylist({{ $musikJawaPlaylist->id }})" aria-label="Putar semua Musik Jawa">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    </button>
                @endif
            </div>

            @if($musikJawaPlaylist && $musikJawaPlaylist->tracks->isNotEmpty())
                <ol class="right-playlist-tracks">
                    @foreach($musikJawaPlaylist->tracks as $index => $track)
                        <li>
                            <button type="button" class="musik-jawa-track" data-track-id="{{ $track->id }}" onclick="window.playTrackById({{ $track->id }})">
                                <span class="playlist-track-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="playlist-track-copy">
                                    <span class="playlist-track-title">{{ $track->title }}</span>
                                    <span class="playlist-track-artist">{{ $track->artist }}</span>
                                </span>
                                <span class="playlist-track-duration">{{ $track->duration_string }}</span>
                            </button>
                        </li>
                    @endforeach
                </ol>
                <p class="playlist-attribution">
                    Semua lagu memakai berkas MP3 lokal yang disediakan pengguna.
                </p>
            @else
                <p class="playlist-empty">Koleksi Musik Jawa akan tampil setelah data playlist dimuat.</p>
            @endif
          </section>
        </section>
    </div>

    <!-- Lyrics Drawer (Ngapak & Terjemahan) -->
    <div id="lyricsDrawer" class="lyrics-drawer">
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
            <div>
                <h4 id="lyricsTitle" style="font-size: 1rem; color: #fff;">Lirik Tembang</h4>
                <div style="font-size: 0.75rem; color: var(--gold-400);">Dialek Asli Tegal & Terjemahan</div>
            </div>
            <button id="closeLyricsBtn" style="background: transparent; border: none; color: var(--text-muted); font-size: 1.25rem; cursor: pointer;">✕</button>
        </div>
        <div id="lyricsContent" class="lyrics-content">
            Pilih lagu dhisit nggo ndeleng lirik lan maknane.
        </div>
    </div>

    <!-- Global Paywall & VIP Modal (For Audio/Movies) -->
    <div id="paywallModal" class="modal-backdrop">
        <div class="modal-box">
            <button class="modal-close">✕</button>
            <div style="text-align: center;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(245, 158, 11, 0.15); border: 2px solid var(--gold-500); color: var(--gold-400); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1rem;">
                    🔒
                </div>
                <h3 style="font-size: 1.4rem; color: #fff; margin-bottom: 0.5rem;">Konten Eksklusif VIP Sebayu</h3>
                <p id="paywallMessage" style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: 1.5rem;">
                    Konten kiye kusus kanggone Anggota Langganan VIP Dulur Wasgitel.
                </p>
                <div style="background: rgba(255, 255, 255, 0.04); border-radius: 12px; padding: 1rem; margin-bottom: 1.5rem; text-align: left; font-size: 0.85rem; color: #cbd5e1;">
                    <div style="font-weight: 700; color: var(--gold-400); margin-bottom: 0.35rem;">Keuntungan VIP:</div>
                    <div>✓ Bebas nonton kabeh film & arsip wayang cepak langka</div>
                    <div>✓ Audio kualitas dhuwur (Hi-Fi 320kbps) tanpa wates</div>
                    <div>✓ Diskon tuku tiket film premiere bioskop rakyat</div>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-primary" style="flex: 1;">
                        👑 Langganan Saiki (Mulai Rp 29.000)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Cinematic Video Preview Modal -->
    <div id="videoPreviewModal" class="video-preview-modal" style="display: none;">
        <div class="video-preview-backdrop" onclick="closeTrailerModal()"></div>
        <div class="video-preview-dialog">
            <div class="video-preview-header">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <span class="badge badge-crimson" style="font-size: 0.65rem; padding: 2px 8px; border-radius: 9999px;">CUPLIKAN RESMI</span>
                    <h3 id="videoModalTitle" class="video-preview-title">Cuplikan Film</h3>
                </div>
                <button type="button" class="video-preview-close" onclick="closeTrailerModal()" aria-label="Tutup Cuplikan">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="video-preview-player-wrap">
                <video id="videoModalPlayer" playsinline controls preload="metadata"></video>
            </div>
            <div class="video-preview-footer">
                <span style="font-size: 0.78rem; color: #94a3b8; display: flex; align-items: center; gap: 0.4rem;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e; display: inline-block;"></span>
                    Sinema Tegal • Pratinjau Kualitas HD
                </span>
                <a id="videoModalActionBtn" href="#" class="btn btn-primary btn-sm">
                    Buka Halaman Film →
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts (Root Relative) -->
    <script src="/js/audio-player.js"></script>
    <script>
        // Global Video Trailer Modal Controller (Supports YouTube & Local MP4)
        window.openTrailerModal = function(title, trailerUrl, movieUrl) {
            const modal = document.getElementById('videoPreviewModal');
            const wrap = modal.querySelector('.video-preview-player-wrap');
            const titleEl = document.getElementById('videoModalTitle');
            const actionBtn = document.getElementById('videoModalActionBtn');
            if (!modal || !wrap) return;
            
            titleEl.textContent = title || 'Cuplikan Film';

            // Detect YouTube URL
            const ytMatch = (trailerUrl || '').match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
            if (ytMatch && ytMatch[1]) {
                wrap.innerHTML = `<iframe src="https://www.youtube-nocookie.com/embed/${ytMatch[1]}?autoplay=1&rel=0" style="width:100%;height:100%;border:none;border-radius:12px;aspect-ratio:16/9;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            } else {
                wrap.innerHTML = `<video id="videoModalPlayer" playsinline controls preload="metadata" style="width:100%;height:100%;border-radius:12px;" src="${trailerUrl}"></video>`;
                const player = wrap.querySelector('video');
                if (player) player.play().catch(() => {});
            }

            if (actionBtn && movieUrl) {
                actionBtn.href = movieUrl;
                actionBtn.style.display = 'inline-flex';
            } else if (actionBtn) {
                actionBtn.style.display = 'none';
            }
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };

        window.closeTrailerModal = function() {
            const modal = document.getElementById('videoPreviewModal');
            if (!modal) return;
            const wrap = modal.querySelector('.video-preview-player-wrap');
            if (wrap) wrap.innerHTML = '';
            modal.style.display = 'none';
            document.body.style.overflow = '';
        };

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') window.closeTrailerModal();
        });
        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileNavDrawer = document.getElementById('mobileNavDrawer');
            if (mobileMenuBtn && mobileNavDrawer) {
                const hamburgerIcon = mobileMenuBtn.querySelector('.hamburger-icon');
                const closeIcon = mobileMenuBtn.querySelector('.close-icon');

                mobileMenuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = mobileNavDrawer.classList.toggle('is-open');
                    mobileMenuBtn.setAttribute('aria-expanded', isOpen);
                    if (hamburgerIcon) hamburgerIcon.style.display = isOpen ? 'none' : 'block';
                    if (closeIcon) closeIcon.style.display = isOpen ? 'block' : 'none';
                });

                // Close when clicking outside
                document.addEventListener('click', (e) => {
                    if (mobileNavDrawer.classList.contains('is-open') && 
                        !mobileNavDrawer.contains(e.target) && 
                        !mobileMenuBtn.contains(e.target)) {
                        mobileNavDrawer.classList.remove('is-open');
                        mobileMenuBtn.setAttribute('aria-expanded', 'false');
                        if (hamburgerIcon) hamburgerIcon.style.display = 'block';
                        if (closeIcon) closeIcon.style.display = 'none';
                    }
                });

                // Close on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && mobileNavDrawer.classList.contains('is-open')) {
                        mobileNavDrawer.classList.remove('is-open');
                        mobileMenuBtn.setAttribute('aria-expanded', 'false');
                        if (hamburgerIcon) hamburgerIcon.style.display = 'block';
                        if (closeIcon) closeIcon.style.display = 'none';
                    }
                });
            }

            // ==========================================================================
            // Hover-to-Play Video Trailer on Movie Poster Cards (Instant & Smooth)
            // ==========================================================================
            let activePlayingWrap = null;

            document.querySelectorAll('.movie-card').forEach(card => {
                const wrap = card.querySelector('.movie-poster-wrap');
                if (!wrap) return;
                const trailerUrl = wrap.dataset.trailer;
                if (!trailerUrl) return;

                // Pre-create video element with muted autoplay attributes
                let video = wrap.querySelector('.movie-card-video');
                if (!video) {
                    video = document.createElement('video');
                    video.className = 'movie-card-video';
                    video.muted = true;
                    video.defaultMuted = true;
                    video.playsInline = true;
                    video.loop = true;
                    video.setAttribute('muted', '');
                    video.setAttribute('playsinline', '');
                    video.setAttribute('webkit-playsinline', '');
                    video.preload = 'metadata';
                    video.src = trailerUrl;
                    wrap.insertBefore(video, wrap.firstChild);
                }

                card.addEventListener('mouseenter', () => {
                    if (activePlayingWrap && activePlayingWrap !== wrap) {
                        stopPosterVideo(activePlayingWrap);
                    }
                    wrap.classList.add('is-playing-video');
                    activePlayingWrap = wrap;

                    video.play().catch(() => {});
                });

                card.addEventListener('mouseleave', () => {
                    stopPosterVideo(wrap);
                });
            });

            function stopPosterVideo(wrap) {
                if (!wrap) return;
                wrap.classList.remove('is-playing-video');
                const video = wrap.querySelector('.movie-card-video');
                if (video) {
                    video.pause();
                    video.currentTime = 0;
                }
                if (activePlayingWrap === wrap) {
                    activePlayingWrap = null;
                }
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
