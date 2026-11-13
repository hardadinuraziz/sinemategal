@extends('layouts.app')

@section('title', 'Nada Tegalan - Streaming Musik & Lagu Daerah Khas Tegal')

@section('content')
<div class="container" style="padding-top: 2rem;">
    <!-- Music Header Banner (Clean Minimalist Showcase) -->
    <div class="music-header-card">
        <div style="max-width: 650px;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <span class="badge badge-vip">NADA TRADISI</span>
                <span style="font-size: 0.75rem; color: var(--text-muted);">Arsip Audio Digital Pantura</span>
            </div>
            <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--text-main); line-height: 1.2; margin-bottom: 0.75rem; letter-spacing: -0.02em;">
                Tembang & Gending Tradisi Pantura
            </h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                Dengarkan rekaman tembang legendaris alm. Ki Enthus Susmono, campursari ngapak, dan gending mistis tari Sintren. Pemutar audio mengambang di bawah akan terus memutar musik meskipun Anda berpindah halaman.
            </p>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                @if($tracks->first())
                    <button onclick="window.playTrackById({{ $tracks->first()->id }})" class="btn btn-primary btn-lg">
                        Putar Lagu Populer
                    </button>
                @endif
                <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary btn-lg">
                    Upgrade Audio VIP
                </a>
            </div>
        </div>

        <div style="width: 180px; height: 180px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border-subtle); flex-shrink: 0; box-shadow: var(--shadow-xs);">
            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=500" alt="Music Cover" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </div>

    <!-- Playlists Section -->
    <div style="margin-bottom: 3rem;">
        <div class="section-header">
            <div>
                <h2 class="section-title">
                    Daftar Putar Pilihan
                </h2>
                <p class="section-subtitle">Daftar putar kurasi untuk menemani waktu santai Anda.</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            @foreach($playlists as $pl)
                <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; box-shadow: var(--shadow-xs); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="position: relative; width: 100%; aspect-ratio: 16/9; border-radius: 10px; overflow: hidden;">
                        <img loading="lazy" decoding="async" src="{{ $pl->cover_url }}" alt="{{ $pl->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        <button onclick="window.playPlaylist({{ $pl->id }})" class="movie-play-btn" style="opacity: 1;" title="Putar Playlist">
                            ▶
                        </button>
                    </div>
                    <div>
                        <h3 style="font-size: 1.1rem; color: var(--text-main); margin-bottom: 0.35rem;">{{ $pl->title }}</h3>
                        <p style="font-size: 0.82rem; color: var(--text-dim); line-height: 1.5;">{{ $pl->description }}</p>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; border-top: 1px solid var(--border-subtle); padding-top: 0.75rem; font-size: 0.8rem; color: var(--text-muted);">
                        <span>{{ $pl->tracks->count() }} Tembang</span>
                        <button onclick="window.playPlaylist({{ $pl->id }})" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                            ▶ Putar Kabeh
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- All Tracks List (Spotify Tracklist Table View) -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 2rem; margin-bottom: 3.5rem; box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem;">
            <div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.2rem;">
                    Daftar Tembang Tegalan
                </h2>
                <p style="font-size: 0.85rem; color: var(--text-muted);">
                    Klik judul lagu atau tombol putar untuk memutar audio langsung.
                </p>
            </div>

            <form method="GET" action="{{ route('music.index') }}" style="display: flex; gap: 0.5rem;">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari tembang atau artis..." style="width: 240px; padding: 0.45rem 0.85rem; font-size: 0.85rem;">
                <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                @if(request('q'))
                    <a href="{{ route('music.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                @endif
            </form>
        </div>

        <!-- Track Table -->
        <div style="display: flex; flex-direction: column; gap: 0.6rem;">
            @foreach($tracks as $index => $track)
                <div class="track-card" data-track-id="{{ $track->id }}">
                    <div style="font-family: monospace; font-size: 0.85rem; color: var(--text-muted); width: 24px; text-align: center; font-weight: 600;">
                        {{ $index + 1 }}
                    </div>

                    <div class="track-cover-wrap" onclick="window.playTrackById({{ $track->id }})">
                        <img loading="lazy" decoding="async" src="{{ $track->cover_url }}" alt="{{ $track->title }}" class="track-cover">
                        <div class="track-play-hover">▶</div>
                    </div>

                    <div class="track-card-info" onclick="window.playTrackById({{ $track->id }})">
                        <div class="track-card-title">{{ $track->title }}</div>
                        <div class="track-card-artist">
                            {{ $track->artist }} • <span style="color: var(--text-muted);">{{ $track->album ?? 'Single Tegalan' }}</span>
                        </div>
                    </div>

                    <div style="font-size: 0.82rem; color: var(--gold-700); font-weight: 600; width: 140px; display: none; @media(min-width: 768px){display:block;}">
                        {{ $track->genre ?? 'Pop Ngapak' }}
                    </div>

                    <div class="track-card-right">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            @if($track->access_type === 'vip')
                                <span class="badge badge-vip">VIP</span>
                            @else
                                <span class="badge badge-free">GRATIS</span>
                            @endif

                            <a href="{{ route('music.show', $track->slug) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;" title="Lirik & Terjemahan">
                                Lirik
                            </a>

                            <button onclick="window.playTrackById({{ $track->id }})" class="track-play-btn" title="Putar">
                                ▶
                            </button>
                        </div>
                        <span class="track-card-duration">{{ $track->duration_string }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
