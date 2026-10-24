@extends('layouts.app')

@section('title', 'Nonton: ' . $movie->title . ' - Sinema Tegal')

@section('styles')
<style>
    body.lights-off {
        background-color: #000 !important;
        background-image: none !important;
    }
    body.lights-off .site-navbar,
    body.lights-off .demo-bar,
    body.lights-off footer,
    body.lights-off .cinema-side-panel {
        opacity: 0.1;
        transition: opacity 0.4s ease;
    }
    body.lights-off .site-navbar:hover,
    body.lights-off .cinema-side-panel:hover {
        opacity: 1;
    }
</style>
@endsection

@section('content')
<div class="container" style="padding-top: 1.5rem;">
    <!-- Watch Top Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('movies.show', $movie->slug) }}" class="btn btn-secondary btn-sm">
                ← Profil Film
            </a>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin: 0;">
                {{ $movie->title }}
            </h1>
            <span class="badge {{ $movie->access_badge['class'] }}">
                {{ $movie->access_badge['label'] }}
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <button id="toggleLightsBtn" class="btn btn-secondary btn-sm" title="Matikan / Uripna Lampu Bioskop">
                💡 Mode Bioskop (Peteng)
            </button>
            <a href="{{ route('movies.index') }}" class="btn btn-secondary btn-sm">
                Daftar Film
            </a>
        </div>
    </div>

    <!-- Cinema Layout -->
    <div class="cinema-layout">
        <!-- Main Theater Column -->
        <div class="cinema-main">
            <!-- Video Player (Supports YouTube & Local MP4) -->
            <div class="video-player-container">
                @php
                    $watchYtId = null;
                    if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $movie->video_url ?? '', $ytMatches)) {
                        $watchYtId = $ytMatches[1];
                    }
                @endphp
                @if($watchYtId)
                    <iframe src="https://www.youtube-nocookie.com/embed/{{ $watchYtId }}?autoplay=1&rel=0" 
                            style="width: 100%; height: 500px; border: none; border-radius: var(--radius-lg); aspect-ratio: 16/9;" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen></iframe>
                @else
                    <video id="cinemaVideo" class="video-element" controls autoplay poster="{{ $movie->banner_url }}" preload="metadata">
                        <source src="{{ $movie->video_url }}" type="video/mp4">
                        Browser sampeyan ora ndhukung tag video HTML5.
                    </video>
                @endif
            </div>

            <!-- Video Info & Culture Notes Card -->
            <div class="cinema-info-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">
                            {{ $movie->title }}
                        </h2>
                        <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.88rem; color: var(--text-muted);">
                            <span>📅 {{ $movie->release_year }}</span>
                            <span>•</span>
                            <span>⏱️ {{ $movie->duration_string }}</span>
                            <span>•</span>
                            <span style="color: var(--accent); font-weight: 600;">★ {{ $movie->rating }} ({{ number_format($movie->views_count) }} ditonton)</span>
                            <span>•</span>
                            <span style="color: var(--primary); font-weight: 600;">📍 {{ $movie->location_tag ?? 'Tegal Laka-Laka' }}</span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="alert('Film disimpen ning Dhaptar Favorit!')" class="btn btn-secondary btn-sm">
                            ❤️ Simpen Favorit
                        </button>
                        <button onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan film kasil disalin!')" class="btn btn-secondary btn-sm">
                            🔗 Bagikna
                        </button>
                    </div>
                </div>

                @if($movie->tagline)
                    <div style="font-style: italic; color: var(--accent); margin-bottom: 1rem; font-size: 0.95rem;">
                        "{{ $movie->tagline }}"
                    </div>
                @endif

                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.5rem;">
                    {{ $movie->synopsis }}
                </p>

                <!-- Cultural Commentary / Catatan Budaya Tegal -->
                <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 12px; padding: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #92400E; font-weight: 800; margin-bottom: 0.4rem; font-size: 0.92rem;">
                        <span>🏮</span> Catatan Budaya & Nilai Pesisiran:
                    </div>
                    <div style="font-size: 0.88rem; color: #78350F; line-height: 1.6;">
                        Karya film kiye ngangkat kearifan lokal Tegal kang nyawiji antarane tradisi maritim lan budi pekerti wong cilik. Nggambarake semangat guyub, kejujuran basa ngapak kang blak-blakan nanging kebak tresna asih.
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Side Panel (Up Next & Rekomendasi) -->
        <div class="cinema-side-panel">
            <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🍿</span> Film Banjure (Up Next)
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($relatedMovies as $rel)
                        <a href="{{ route('movies.show', $rel->slug) }}" style="display: flex; gap: 0.75rem; align-items: center; text-decoration: none; padding: 0.5rem; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='transparent'">
                            <div style="width: 80px; height: 50px; border-radius: 6px; overflow: hidden; flex-shrink: 0; background: #000;">
                                <img src="{{ $rel->banner_url }}" alt="{{ $rel->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="flex-grow: 1; min-width: 0;">
                                <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $rel->title }}
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                    <span>{{ $rel->duration_string }}</span>
                                    <span>•</span>
                                    <span style="color: var(--accent); font-weight: 700;">★ {{ $rel->rating }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle);">
                    <a href="{{ route('movies.index') }}" class="btn btn-secondary btn-sm" style="width: 100%;">
                        Jelajahi Kabeh Film
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleLightsBtn = document.getElementById('toggleLightsBtn');
        let lightsOff = false;

        if (toggleLightsBtn) {
            toggleLightsBtn.addEventListener('click', () => {
                lightsOff = !lightsOff;
                document.body.classList.toggle('lights-off', lightsOff);
                toggleLightsBtn.textContent = lightsOff ? '☀️ Uripna Lampu' : '💡 Mode Bioskop (Peteng)';
            });
        }
    });
</script>
@endsection
