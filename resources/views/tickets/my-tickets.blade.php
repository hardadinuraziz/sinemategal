@extends('layouts.app')

@section('title', 'Tiket Saya - Sinema Tegal')

@section('content')
<div class="container" style="padding-top: 2rem;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
        <div>
            <span class="badge badge-ticket" style="margin-bottom: 0.5rem;">TIKET BIOSKOP RAKYAT</span>
            <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem; letter-spacing: -0.02em;">
                Tiket Nonton Saya
            </h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">
                Daftar tiket film premiere yang telah Anda miliki. Tiket aktif dapat digunakan untuk menonton kapan saja.
            </p>
        </div>
        <a href="{{ route('movies.index', ['access' => 'ticket']) }}" class="btn btn-secondary btn-sm">
            + Jelajahi Film Premiere
        </a>
    </div>

    <!-- Active Tickets List -->
    <div style="margin-bottom: 3.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h2 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main);">
                Tiket Aktif ({{ $activeTickets->count() }})
            </h2>
            <span class="brand-badge" style="font-size: 0.72rem;">Siap Ditonton</span>
        </div>

        @if($activeTickets->count() > 0)
            <div class="ticket-grid">
                @foreach($activeTickets as $ticket)
                    <div class="ticket-card">
                        <!-- Left Poster -->
                        <div style="width: 120px; flex-shrink: 0; background: var(--bg-subtle);">
                            <img loading="lazy" decoding="async" src="{{ $ticket->movie->poster_url }}" alt="{{ $ticket->movie->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <!-- Right Info -->
                        <div style="padding: 1.25rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span class="badge badge-ticket" style="font-size: 0.65rem;">AKTIF</span>
                                    <span style="font-family: monospace; font-size: 0.75rem; color: var(--text-muted); background: var(--bg-main); padding: 2px 8px; border-radius: var(--radius-xs); border: 1px solid var(--border-color); font-weight: 600;">
                                        {{ $ticket->ticket_code }}
                                    </span>
                                </div>
                                <h3 style="font-size: 1.05rem; color: var(--text-main); font-weight: 800; margin-bottom: 0.35rem; line-height: 1.3;">
                                    {{ $ticket->movie->title }}
                                </h3>
                                <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                                    {{ $ticket->movie->category->name }} • {{ $ticket->movie->duration_string }}
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">
                                    Berlaku hingga: <strong style="color: var(--text-main);">{{ $ticket->valid_until->format('d M Y, H:i') }}</strong>
                                </div>
                            </div>

                            <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                                <a href="{{ route('movies.watch', $ticket->movie->slug) }}" class="btn btn-primary btn-sm" style="flex: 1;">
                                    Putar Film
                                </a>
                                <a href="{{ route('movies.show', $ticket->movie->slug) }}" class="btn btn-secondary btn-sm">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 3rem; text-align: center; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.35rem;">Belum Ada Tiket Aktif</h3>
                <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 1.5rem; max-width: 480px; margin-left: auto; margin-right: auto;">
                    Anda belum memiliki tiket film premiere aktif. Silakan pilih film bioskop rakyat yang ingin Anda tonton.
                </p>
                <a href="{{ route('movies.index', ['access' => 'ticket']) }}" class="btn btn-primary btn-sm">
                    Lihat Film Bioskop Rakyat
                </a>
            </div>
        @endif
    </div>

    <!-- Expired Tickets List -->
    @if($expiredTickets->count() > 0)
    <div style="margin-bottom: 3.5rem;">
        <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-muted); margin-bottom: 1rem;">
            Riwayat Tiket Kedaluwarsa
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            @foreach($expiredTickets as $et)
                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem; opacity: 0.8;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                        <span style="font-family: monospace;">{{ $et->ticket_code }}</span>
                        <span>Kedaluwarsa: {{ $et->valid_until->format('d/m/Y') }}</span>
                    </div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">{{ $et->movie->title }}</div>
                    <div style="margin-top: 0.5rem;">
                        <a href="{{ route('movies.show', $et->movie->slug) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">
                            Beli Tiket Baru
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
