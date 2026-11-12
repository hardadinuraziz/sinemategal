@extends('layouts.app')

@section('title', 'Mlebu Akun - Sinema Tegal')

@section('content')
<div class="container" style="padding-top: 3.5rem; max-width: 480px;">
    <div class="auth-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="/images/logo.svg" alt="Sinema Tegal" style="height: 68px; width: auto; margin: 0 auto 0.75rem; display: block;">
            <h1 style="font-size: 1.8rem; color: var(--text-main); font-weight: 800; margin-bottom: 0.35rem;">
                Mlebu Akun Sinema Tegal
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem;">
                Nonton film lokal sarta ngrungokna nada Tegalan laka-laka.
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Alamat Email:</label>
                <input type="email" name="email" value="{{ old('email', 'vip@sinemategal.id') }}" class="form-control" placeholder="jeneng@domain.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Kata Sandi (Password):</label>
                <input type="password" name="password" value="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; margin-top: 0.5rem;">
                Mlebu Saiki
            </button>
        </form>

        <!-- Quick 1-Click Demo Profiles -->
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-subtle);">
            <div style="font-size: 0.8rem; color: var(--gold-400); font-weight: 700; margin-bottom: 0.6rem; text-align: center;">
                ☕ Uji Akun Demo (1-Klik Tanpa Ngetik Password):
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @php
                    $demoUsers = \App\Models\User::take(4)->get();
                @endphp
                @foreach($demoUsers as $du)
                    <a href="{{ route('demo.switch', $du->id) }}" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left; padding: 0.5rem 0.75rem;">
                        <span>👤</span>
                        <div style="flex-grow: 1;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 0.82rem;">{{ $du->name }}</div>
                            <div style="font-size: 0.72rem; color: var(--text-dim);">
                                {{ $du->email }} • {{ $du->role }} @if($du->hasVipAccess()) (VIP Aktif) @endif
                            </div>
                        </div>
                        <span style="color: var(--gold-400); font-size: 0.75rem;">Mlebu →</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-muted);">
            Durung duwe akun? 
            <a href="{{ route('register') }}" style="color: var(--gold-400); font-weight: 700;">Daftar Akun Anyar</a>
        </div>
    </div>
</div>
@endsection
