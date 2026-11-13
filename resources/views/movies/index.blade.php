@extends('layouts.app')

@section('title', 'Katalog Sinema & Film Tegalan')

@section('content')
<div class="container" style="padding-top: 2rem;">
    <!-- Catalog Header -->
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem; letter-spacing: -0.02em;">
            Katalog Sinema & Film
        </h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            Koleksi film cerita, dokumenter tradisi pesisir, seni Sintren, teater wayang cepak, dan drama komedi khas Tegal.
        </p>
    </div>

    <!-- Search & Filter Bar -->
    <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 2rem; box-shadow: var(--shadow-xs);">
        <form method="GET" action="{{ route('movies.index') }}" style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" style="flex: 1 1 200px; min-width: 0;" placeholder="Cari judul film, sutradara, paraga (pemeran), atau latar lokasi...">
                
                <select name="access" class="form-control" style="flex: 0 1 auto; min-width: 140px;">
                    <option value="">Semua Tipe Akses</option>
                    <option value="free" {{ request('access') === 'free' ? 'selected' : '' }}>Gratis</option>
                    <option value="vip" {{ request('access') === 'vip' ? 'selected' : '' }}>VIP Sebayu</option>
                    <option value="ticket" {{ request('access') === 'ticket' ? 'selected' : '' }}>Tiket Premiere</option>
                </select>

                <button type="submit" class="btn btn-primary">
                    Cari Film
                </button>
                @if(request()->anyFilled(['q', 'category', 'access']))
                    <a href="{{ route('movies.index') }}" class="btn btn-secondary">
                        Reset
                    </a>
                @endif
            </div>

            <!-- Category Pills -->
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; padding-top: 0.5rem; border-top: 1px solid var(--border-subtle);">
                <span style="font-size: 0.8rem; color: var(--text-dim); font-weight: 600;">Kategori:</span>
                <a href="{{ route('movies.index', request()->except('category')) }}" 
                   class="demo-pill {{ !request('category') ? 'is-active' : '' }}">
                   Kabeh (Semua)
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('movies.index', array_merge(request()->query(), ['category' => $cat->slug])) }}" 
                       class="demo-pill {{ request('category') === $cat->slug ? 'is-active' : '' }}">
                       {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Movies Grid -->
    @if($movies->count() > 0)
        <div class="grid-movies">
            @foreach($movies as $movie)
                <div class="movie-card">
                    <div class="movie-poster-wrap" data-trailer="{{ $movie->trailer_url }}">
                        <img loading="lazy" decoding="async" src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="movie-poster" loading="lazy">
                        <div class="movie-video-badge"><span class="video-pulse-dot"></span><span>Cuplikan</span></div>
                        <div class="movie-badge-top">
                            <span class="badge {{ $movie->access_badge['class'] }}">
                                {{ $movie->access_badge['label'] }}
                            </span>
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
                            <span>{{ $movie->category->name }}</span>
                            <span>•</span>
                            <span>{{ $movie->duration_string }}</span>
                            <span>•</span>
                            <span>{{ $movie->release_year }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 2.5rem; display: flex; justify-content: center;">
            {{ $movies->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 4rem 1.5rem; background: var(--bg-surface); border-radius: var(--radius-xl); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">Tidak Ada Film yang Ditemukan</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
                Silakan ubah kata kunci pencarian atau sesuaikan kembali filter kategori akses.
            </p>
            <a href="{{ route('movies.index') }}" class="btn btn-secondary btn-sm">
                Lihat Semua Katalog
            </a>
        </div>
    @endif
</div>
@endsection
