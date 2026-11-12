@extends('layouts.app')

@section('title', 'Daftar Akun Anyar - Sinema Tegal')

@section('content')
<div class="container" style="padding-top: 3.5rem; max-width: 480px;">
    <div class="auth-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="/images/logo.svg" alt="Sinema Tegal" style="height: 68px; width: auto; margin: 0 auto 0.75rem; display: block;">
            <h1 style="font-size: 1.8rem; color: var(--text-main); font-weight: 800; margin-bottom: 0.35rem;">
                Daftar Warga Sinema Tegal
            </h1>
            <p style="color: var(--text-muted); font-size: 0.88rem;">
                Oleh bonus saldo Dompet Bahari <strong>Rp 100.000</strong> nggo nyoba tuku tiket premiere & musik!
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Jeneng Lengkap:</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Contoh: Mas Bowo Kalisapu" required>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Email:</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="bowo@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Kata Sandi (Minimal 6 karakter):</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label class="form-label">Baleni Kata Sandi (Konfirmasi):</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; margin-top: 0.5rem;">
                🎉 Daftar & Klaim Bonus Saldo
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-muted);">
            Wis duwe akun? 
            <a href="{{ route('login') }}" style="color: var(--gold-400); font-weight: 700;">Mlebu ning kene</a>
        </div>
    </div>
</div>
@endsection
