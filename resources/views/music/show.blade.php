@extends('layouts.app')

@section('title', $track->title . ' - Lirik & Nada Tegalan')

@section('content')
<div class="container" style="padding-top: 2rem;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.85rem; color: var(--text-dim); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
        <a href="{{ route('home') }}" style="color: var(--text-muted);">Beranda</a>
        <span>/</span>
        <a href="{{ route('music.index') }}" style="color: var(--text-muted);">Nada Tegalan</a>
        <span>/</span>
        <span style="color: var(--gold-400);">{{ $track->title }}</span>
    </div>

    <!-- Track Header Card -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 2.5rem; margin-bottom: 2.5rem; display: flex; align-items: center; gap: 2.5rem; flex-wrap: wrap;">
        <div style="width: 200px; height: 200px; border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-lg), 0 0 25px var(--gold-glow); flex-shrink: 0; border: 2px solid rgba(245, 158, 11, 0.3);">
            <img loading="lazy" decoding="async" src="{{ $track->cover_url }}" alt="{{ $track->title }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>

        <div style="flex: 1; min-width: 280px;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <span class="badge {{ $track->access_badge['class'] }}">
                    {{ $track->access_badge['label'] }}
                </span>
                <span class="hero-tag">🎵 {{ $track->genre ?? 'Tembang Ngapak' }}</span>
                <span class="hero-tag">⏱️ {{ $track->duration_string }}</span>
                <span class="hero-tag">📅 {{ $track->release_year }}</span>
            </div>

            <h1 style="font-size: 2.4rem; color: #fff; font-weight: 900; line-height: 1.2; margin-bottom: 0.4rem;">
                {{ $track->title }}
            </h1>
            <div style="font-size: 1.2rem; color: var(--text-muted); margin-bottom: 1rem;">
                Dinyanyikan dening: <strong style="color: #fff;">{{ $track->artist }}</strong>
                @if($track->album) • Album: {{ $track->album }} @endif
            </div>

            <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                {{ $track->description ?? 'Tembang khas Tegalan kanthi cengkok pesisiran kang asri lan ngemu teges filosofi urip.' }}
            </p>

            <div style="display: flex; gap: 1rem; align-items: center;">
                <button onclick="window.playTrackById({{ $track->id }})" class="btn btn-primary btn-lg" style="box-shadow: 0 0 20px var(--gold-glow);">
                    <span>▶️</span> Putar Lagu Saiki
                </button>
                <a href="{{ route('music.index') }}" class="btn btn-secondary btn-lg">
                    ← Daftar Tembang Liyane
                </a>
            </div>
        </div>
    </div>

    <!-- Lyrics & Cultural Meaning Box -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; margin-bottom: 3.5rem;">
        <!-- Left: Lyrics -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 2.5rem;">
            <h3 style="font-size: 1.3rem; color: #fff; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>📜</span> Lirik Asli Dialek Tegal & Maknane
            </h3>

            <div style="font-size: 1.05rem; line-height: 2; color: #f1f5f9; white-space: pre-line; background: rgba(0, 0, 0, 0.25); padding: 2rem; border-radius: 12px; border: 1px solid var(--border-subtle);">
                {{ $track->lyrics ?? "Lirik dereng kasedia kanggone tembang kiye." }}
            </div>
        </div>

        <!-- Right: Related Tracks -->
        <div>
            <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem;">
                <h4 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">
                    🎵 Rekomendasi Tembang
                </h4>
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    @foreach($relatedTracks as $rel)
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.5rem; border-radius: 8px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'" onclick="window.playTrackById({{ $rel->id }})">
                            <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                                <img loading="lazy" decoding="async" src="{{ $rel->cover_url }}" alt="{{ $rel->title }}" style="width: 44px; height: 44px; border-radius: 6px; object-fit: cover;">
                                <div style="min-width: 0;">
                                    <div style="font-size: 0.88rem; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $rel->title }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-dim);">
                                        {{ $rel->artist }}
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-sm" style="padding: 0.25rem 0.6rem; border-radius: 50%;">
                                ▶
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
