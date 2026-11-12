@extends('layouts.app')

@section('title', 'Pusat Kelola Admin & Kurator - Sinema Tegal')

@section('content')
<div class="container" style="padding-top: 1.5rem; padding-bottom: 3rem;">

    @if(!$isAdmin)
        <!-- Access Gate for Non-Admin Testing -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-xl); padding: 3.5rem 2rem; text-align: center; max-width: 680px; margin: 3rem auto; box-shadow: var(--shadow-lg);">
            <div style="font-size: 3.5rem; margin-bottom: 1rem;">🔐</div>
            <h2 style="font-size: 1.8rem; font-weight: 900; color: var(--text-main); margin-bottom: 0.75rem;">
                Gerbang Kurator & Admin Sinema Tegal
            </h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem;">
                Halaman iki kusus nggo Kurator Budaya lan Administrator Sinema Tegal nggo ngatur film, lagu, tiket bioskop rakyat, sarta anggota VIP.
            </p>
            <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                <a href="{{ route('demo.switch', 1) }}" class="btn btn-primary btn-lg">
                    👑 Masuk sebagai Kurator Admin (Ki Patih)
                </a>
                <a href="{{ route('home') }}" class="btn btn-secondary btn-lg">
                    Balik maring Beranda
                </a>
            </div>
        </div>
    @else
        <!-- Admin Header (Clean Minimalist SaaS Style) -->
        <div class="admin-header">
            <div class="admin-title-wrap">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <span class="badge badge-vip">ADMINISTRATOR & KURATOR</span>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Sinema & Tembang Pantura</span>
                </div>
                <h1>Pusat Kendali Sinema Tegal</h1>
                <p>Kelola katalog film budaya, tembang tradisi, transaksi tiket bioskop rakyat, dan status keanggotaan VIP.</p>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <button type="button" onclick="switchAdminTab('formTab')" class="btn btn-primary btn-sm">
                    + Tambah Konten
                </button>
                <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">
                    Lihat Web
                </a>
            </div>
        </div>

        <!-- KPI Stat Cards Grid (Minimalist Cards) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="stat-card">
                <div class="stat-label">Film & Dokumenter</div>
                <div class="stat-num">{{ $stats['total_movies'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-dim);">Katalog Aktif</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tembang Tradisi</div>
                <div class="stat-num">{{ $stats['total_tracks'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-dim);">Tersedia di Audio Player</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Anggota VIP</div>
                <div class="stat-num">{{ $stats['active_vips'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-dim);">Pelanggan Wasgitel</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tiket Terjual</div>
                <div class="stat-num">{{ $stats['tickets_sold'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-dim);">Sistem Pay-Per-View</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-num" style="font-size: 1.45rem;">
                    Rp {{ number_format($stats['ticket_revenue'] + $stats['sub_revenue'], 0, ',', '.') }}
                </div>
                <div style="font-size: 0.75rem; color: var(--text-dim);">Tiket & Langganan</div>
            </div>
        </div>

        <!-- Segmented Tab Navigation -->
        <div class="admin-tabs">
            <button class="admin-tab-btn active" id="btn-moviesTab" onclick="switchAdminTab('moviesTab')">
                Katalog Film ({{ $movies->count() }})
            </button>
            <button class="admin-tab-btn" id="btn-tracksTab" onclick="switchAdminTab('tracksTab')">
                Tembang Musik ({{ $tracks->count() }})
            </button>
            <button class="admin-tab-btn" id="btn-ticketsTab" onclick="switchAdminTab('ticketsTab')">
                Transaksi Tiket ({{ $recentTickets->count() }})
            </button>
            <button class="admin-tab-btn" id="btn-usersTab" onclick="switchAdminTab('usersTab')">
                Pengguna & VIP ({{ $users->count() }})
            </button>
            <button class="admin-tab-btn" id="btn-formTab" onclick="switchAdminTab('formTab')">
                Tambah Konten
            </button>
        </div>

        <!-- ================= TAB 1: KELOLA FILM ================= -->
        <div id="moviesTab" class="admin-tab-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main);">Daftar Film & Dokumenter Budaya</h3>
                <button onclick="switchAdminTab('formTab')" class="btn btn-primary btn-sm">+ Tambah Film Baru</button>
            </div>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Poster & Judul</th>
                            <th>Kategori Budaya</th>
                            <th>Tipe Akses</th>
                            <th>Sutradara & Durasi</th>
                            <th>Penayangan</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movies as $m)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <img src="{{ $m->poster_url }}" alt="{{ $m->title }}" style="width: 44px; height: 56px; border-radius: 8px; object-fit: cover; box-shadow: var(--shadow-xs);">
                                    <div>
                                        <div style="font-weight: 800; color: var(--text-main); font-size: 0.95rem;">{{ $m->title }}</div>
                                        <div style="font-size: 0.78rem; color: var(--text-dim); margin-bottom: 0.25rem;">{{ $m->release_year }} • {{ $m->location_tag ?? 'Tegal' }}</div>
                                        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                                            <span style="font-size: 0.7rem; background: rgba(217, 119, 6, 0.1); color: var(--amber-500); font-weight: 600; padding: 0.15rem 0.45rem; border-radius: 4px; border: 1px solid rgba(217, 119, 6, 0.2);">
                                                🎬 Trailer Hover: {{ $m->trailer_url ? 'Aktif' : 'Belum Ada' }}
                                            </span>
                                            <span style="font-size: 0.7rem; background: var(--surface-subtle); color: var(--text-muted); padding: 0.15rem 0.45rem; border-radius: 4px; border: 1px solid var(--border-subtle);">
                                                🎥 Full Video: {{ $m->video_url ? 'Tersedia' : 'Kosong' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $m->category->name ?? 'Budaya' }}</span>
                            </td>
                            <td>
                                @if($m->access_type === 'ticket')
                                    <span class="badge badge-ticket">🎟️ Tiket ({{ $m->formatted_price }})</span>
                                @elseif($m->access_type === 'vip')
                                    <span class="badge badge-vip">👑 VIP Wasgitel</span>
                                @else
                                    <span class="badge badge-free">✨ Gratis</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ $m->director }}</div>
                                <div style="font-size: 0.78rem; color: var(--text-dim);">{{ $m->duration_string }}</div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--text-main);">{{ number_format($m->views_count, 0, ',', '.') }}</span>
                                <span style="font-size: 0.78rem; color: var(--text-dim);">kali tonton</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.35rem;">
                                    <a href="{{ route('movies.show', $m->slug) }}" class="btn btn-secondary btn-sm" title="Lihat film">Lihat</a>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick='openEditMovieModal(@json($m))' title="Edit data & ganti video" style="font-weight: 600;">Edit / Ganti Video</button>
                                    <form action="{{ route('admin.movies.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Hapus film {{ $m->title }}?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--rose-600);" title="Hapus">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 2: KELOLA TEMBANG ================= -->
        <div id="tracksTab" class="admin-tab-content" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main);">Daftar Nada & Tembang Pantura</h3>
                <button onclick="switchAdminTab('formTab')" class="btn btn-primary btn-sm">+ Tambah Tembang</button>
            </div>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Cover & Judul</th>
                            <th>Artis / Seniman</th>
                            <th>Genre & Durasi</th>
                            <th>Akses</th>
                            <th>Pemutaran</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tracks as $t)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <img src="{{ $t->cover_url }}" alt="{{ $t->title }}" style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover;">
                                    <div>
                                        <div style="font-weight: 700; color: var(--text-main); font-size: 0.9rem;">{{ $t->title }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $t->album ?? 'Single Pantura' }} ({{ $t->release_year }})</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ $t->artist }}</div>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $t->genre ?? 'Pop Ngapak' }}</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.35rem;">{{ $t->duration_string }}</span>
                            </td>
                            <td>
                                @if($t->access_type === 'vip')
                                    <span class="badge badge-vip">VIP</span>
                                @else
                                    <span class="badge badge-free">Gratis</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--text-main);">{{ number_format($t->plays_count, 0, ',', '.') }}</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">kali putar</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.35rem;">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick='openEditTrackModal(@json($t))'>Edit</button>
                                    <form action="{{ route('admin.tracks.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus tembang {{ $t->title }}?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--rose-600);">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 3: RIWAYAT TIKET & TRANSAKSI ================= -->
        <div id="ticketsTab" class="admin-tab-content" style="display: none;">
            <div style="margin-bottom: 1rem;">
                <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main);">Riwayat Pembelian Tiket Bioskop Rakyat</h3>
                <p style="font-size: 0.88rem; color: var(--text-dim);">Daftar tiket pay-per-view sing dituku dening para penonton.</p>
            </div>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Kode Tiket</th>
                            <th>Film Premiere</th>
                            <th>Nama Penonton</th>
                            <th>Biaya Tiket</th>
                            <th>Waktu Tuku</th>
                            <th>Status Tiket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTickets as $ticket)
                        <tr>
                            <td>
                                <span style="font-family: monospace; font-weight: 700; background: var(--bg-subtle); color: var(--text-main); border: 1px solid var(--border-subtle); padding: 2px 8px; border-radius: 6px; font-size: 0.85rem;">
                                    {{ $ticket->ticket_code }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-main);">{{ $ticket->movie->title ?? 'Film' }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">{{ $ticket->user->name ?? 'Pengguna' }}</div>
                                <div style="font-size: 0.78rem; color: var(--text-dim);">{{ $ticket->user->email ?? '' }}</div>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--green-700);">
                                    Rp {{ number_format($ticket->price_paid, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem; color: var(--text-dim);">
                                    {{ $ticket->created_at->format('d M Y, H:i') }}
                                </span>
                            </td>
                            <td>
                                @if($ticket->isValid())
                                    <span class="badge badge-free">Aktif Ditonton</span>
                                @else
                                    <span class="badge badge-neutral">Kadaluarsa</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-dim);">
                                Durung ana riwayat tiket sing dituku.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 4: KELOLA PENGGUNA & VIP ================= -->
        <div id="usersTab" class="admin-tab-content" style="display: none;">
            <div style="margin-bottom: 1rem;">
                <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-main);">Kelola Pengguna, Hak Akses & Status VIP</h3>
                <p style="font-size: 0.88rem; color: var(--text-dim);">Atur peran pengguna, aktivasi langganan VIP Wasgitel kanthi 1-klik, utawa tambah saldo dompet.</p>
            </div>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Peran Akun</th>
                            <th>Status VIP</th>
                            <th>Saldo Dompet Bahari</th>
                            <th style="text-align: right;">Aksi Manajemen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <img src="{{ $u->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100' }}" alt="Avatar" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;">
                                    <div>
                                        <div style="font-weight: 800; color: var(--text-main);">{{ $u->name }}</div>
                                        <div style="font-size: 0.78rem; color: var(--text-dim);">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <form action="{{ route('admin.users.change-role', $u->id) }}" method="POST" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                    @csrf
                                    <select name="role" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; width: auto;" onchange="this.form.submit()">
                                        <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>Penonton</option>
                                        <option value="creator" {{ $u->role === 'creator' ? 'selected' : '' }}>Kreator Sineas</option>
                                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Kurator Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                @if($u->is_vip)
                                    <span class="badge badge-vip">VIP Aktif</span>
                                    <div style="font-size: 0.72rem; color: var(--text-dim); margin-top: 2px;">
                                        s/d {{ $u->vip_expires_at ? \Carbon\Carbon::parse($u->vip_expires_at)->format('d M Y') : 'Aktif' }}
                                    </div>
                                @else
                                    <span class="badge badge-neutral">Reguler</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-main);">
                                    Rp {{ number_format($u->wallet_balance, 0, ',', '.') }}
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                    <!-- Toggle VIP -->
                                    <form action="{{ route('admin.users.toggle-vip', $u->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                            {{ $u->is_vip ? 'Cabut VIP' : 'Set VIP' }}
                                        </button>
                                    </form>

                                    <!-- Quick Top-up Saldo -->
                                    <form action="{{ route('admin.users.add-balance', $u->id) }}" method="POST" style="display: inline-flex; align-items: center; gap: 0.3rem;">
                                        @csrf
                                        <input type="hidden" name="amount" value="50000">
                                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; color: var(--accent-emerald);" title="Tambah saldo Rp 50.000">
                                            + Saldo 50rb
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 5: UNGGAH KONTEN ANYAR ================= -->
        <div id="formTab" class="admin-tab-content" style="display: none;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
                
                <!-- Form Tambah Film -->
                <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-xl); padding: 2rem; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🎬</span> Tambah Film / Dokumenter Anyar
                    </h3>

                    <form method="POST" action="{{ route('admin.movies.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Judul Film:</label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Sang Sintren saka Muarareja" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label">Kategori Budaya:</label>
                                <select name="category_id" class="form-control" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tipe Akses:</label>
                                <select name="access_type" class="form-control" required>
                                    <option value="free">✨ Gratis nggo Kabeh</option>
                                    <option value="vip">👑 Kusus Anggota VIP</option>
                                    <option value="ticket">🎟️ Tiket Pay-Per-View</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label">Harga Tiket (Rp):</label>
                                <input type="number" name="ticket_price" class="form-control" value="15000">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tahun Rilis:</label>
                                <input type="number" name="release_year" class="form-control" value="2026" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label">Sutradara / Sutradoro:</label>
                                <input type="text" name="director" class="form-control" placeholder="Nama sutradara" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Durasi (Menit):</label>
                                <input type="number" name="duration_minutes" class="form-control" value="95" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pemain / Seniman Utama:</label>
                            <input type="text" name="cast" class="form-control" placeholder="Contoh: Seniman Teater Tegal">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Lokasi Cerita / Budaya:</label>
                            <input type="text" name="location_tag" class="form-control" placeholder="Contoh: Pesisir Muarareja, Kota Tegal">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tagline Film:</label>
                            <input type="text" name="tagline" class="form-control" placeholder="Kutipan utawa ukara khas film">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sinopsis Cerita:</label>
                            <textarea name="synopsis" class="form-control" rows="3" placeholder="Tulis sinopsis film..." required></textarea>
                        </div>

                        <!-- PENGATURAN VIDEO UTAMA & TRAILER -->
                        <div style="background: var(--surface-subtle); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--border-subtle); margin-bottom: 1.25rem;">
                            <div style="font-weight: 700; color: var(--text-main); margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                                <span>🎥</span> Kelola Video Film & Cuplikan Trailer
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">URL / Path Video Streaming Penuh (Full Movie):</label>
                                <input type="text" name="video_url" value="/videos/trailer-sintren.mp4" class="form-control" placeholder="/videos/film.mp4 atau https://...">
                                <div style="margin-top: 0.35rem; font-size: 0.78rem; color: var(--text-muted);">Atau upload file video baru (.mp4 / .webm):</div>
                                <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime" class="form-control" style="margin-top: 0.25rem; padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                            </div>

                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">URL / Path Video Trailer (Autoplay saat Hover Poster):</label>
                                <input type="text" name="trailer_url" value="/videos/trailer-sintren.mp4" class="form-control" placeholder="/videos/trailer.mp4 atau https://...">
                                <div style="margin-top: 0.35rem; font-size: 0.78rem; color: var(--text-muted);">Atau upload file cuplikan trailer (.mp4):</div>
                                <input type="file" name="trailer_file" accept="video/mp4,video/webm,video/ogg,video/quicktime" class="form-control" style="margin-top: 0.25rem; padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                            </div>
                        </div>

                        <!-- PENGATURAN POSTER & BANNER -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label">Poster Vertikal (URL / Path):</label>
                                <input type="text" name="poster_url" value="/images/poster-sintren.jpg" class="form-control" required>
                                <div style="margin-top: 0.35rem; font-size: 0.78rem; color: var(--text-muted);">Upload foto poster (opsional):</div>
                                <input type="file" name="poster_file" accept="image/*" class="form-control" style="margin-top: 0.25rem; padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Banner Horizontal Marquee (URL / Path):</label>
                                <input type="text" name="banner_url" value="/images/banner-vip.jpg" class="form-control" required>
                                <div style="margin-top: 0.35rem; font-size: 0.78rem; color: var(--text-muted);">Upload foto banner (opsional):</div>
                                <input type="file" name="banner_file" accept="image/*" class="form-control" style="margin-top: 0.25rem; padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                            </div>
                        </div>

                        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="featured" id="chkFeatured" value="1" style="width: 18px; height: 18px; accent-color: var(--amber-500);">
                            <label for="chkFeatured" style="font-size: 0.9rem; font-weight: 600; color: var(--text-main); cursor: pointer;">
                                Tampilna dadi Film Utama (Hero Banner)
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            Simpan & Terbitna Film
                        </button>
                    </form>
                </div>

                <!-- Form Tambah Lagu -->
                <div style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-xl); padding: 2rem; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🎵</span> Tambah Tembang / Musik Tegalan
                    </h3>

                    <form method="POST" action="{{ route('admin.tracks.store') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Judul Tembang:</label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Tegal Laka-Laka" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label">Artis / Penyanyi:</label>
                                <input type="text" name="artist" class="form-control" placeholder="Contoh: Alm. Ki Enthus Susmono" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tipe Akses:</label>
                                <select name="access_type" class="form-control" required>
                                    <option value="free">✨ Gratis Diputer</option>
                                    <option value="vip">👑 Kusus Anggota VIP</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label">Genre:</label>
                                <input type="text" name="genre" class="form-control" placeholder="Pop Ngapak / Tarling">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Durasi (Detik):</label>
                                <input type="number" name="duration_seconds" class="form-control" value="225" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Album:</label>
                            <input type="text" name="album" class="form-control" placeholder="Koleksi Nada Pantura">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tahun Rilis:</label>
                            <input type="number" name="release_year" class="form-control" value="2026" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">URL File Audio (mp3):</label>
                            <input type="url" name="audio_url" value="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">URL Cover Album (Persegi):</label>
                            <input type="url" name="cover_url" value="/images/album-lakalaka.jpg" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Lirik Tembang (Ngapak):</label>
                            <textarea name="lyrics" class="form-control" rows="4" placeholder="Tulis lirik tembang ning kene..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            Simpan & Terbitna Tembang
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= EDIT MODAL FILM ================= -->
        <div id="editMovieModal" class="modal-overlay">
            <div class="modal-dialog">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main);">Edit Data Film</h3>
                    <button type="button" onclick="closeEditMovieModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-dim); cursor: pointer;">✕</button>
                </div>

                <form id="editMovieForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Judul Film:</label>
                        <input type="text" id="editMovieTitle" name="title" class="form-control" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Kategori Budaya:</label>
                            <select id="editMovieCategory" name="category_id" class="form-control" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tipe Akses:</label>
                            <select id="editMovieAccess" name="access_type" class="form-control" required>
                                <option value="free">Gratis</option>
                                <option value="vip">VIP</option>
                                <option value="ticket">Tiket Premiere (Pay-Per-View)</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Harga Tiket (Rp):</label>
                            <input type="number" id="editMoviePrice" name="ticket_price" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Durasi (Menit):</label>
                            <input type="number" id="editMovieDuration" name="duration_minutes" class="form-control" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Sutradara:</label>
                            <input type="text" id="editMovieDirector" name="director" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tahun Rilis:</label>
                            <input type="number" id="editMovieYear" name="release_year" class="form-control" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Lokasi Cerita:</label>
                            <input type="text" id="editMovieLocation" name="location_tag" class="form-control" placeholder="Contoh: Muarareja, Kota Tegal">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tagline Film:</label>
                            <input type="text" id="editMovieTagline" name="tagline" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Sinopsis:</label>
                        <textarea id="editMovieSynopsis" name="synopsis" class="form-control" rows="3" required></textarea>
                    </div>

                    <!-- KELOLA & GANTI VIDEO -->
                    <div style="background: var(--surface-subtle); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--border-subtle); margin-bottom: 1.25rem;">
                        <div style="font-weight: 700; color: var(--text-main); margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                            <span>🎥</span> Kelola & Ganti Video Film
                        </div>

                        <div class="form-group">
                            <label class="form-label">URL / Path Video Streaming Utama (Full Movie):</label>
                            <input type="text" id="editMovieVideo" name="video_url" class="form-control" placeholder="/videos/film.mp4 atau link URL">
                            <div style="margin-top: 0.35rem; font-size: 0.78rem; color: var(--text-muted);">Ganti dengan upload video baru (opsional):</div>
                            <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime" class="form-control" style="margin-top: 0.25rem; padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">URL / Path Video Trailer (Autoplay saat Hover Poster):</label>
                            <input type="text" id="editMovieTrailer" name="trailer_url" class="form-control" placeholder="/videos/trailer.mp4 atau link URL">
                            <div style="margin-top: 0.35rem; font-size: 0.78rem; color: var(--text-muted);">Ganti dengan upload trailer baru (opsional):</div>
                            <input type="file" name="trailer_file" accept="video/mp4,video/webm,video/ogg,video/quicktime" class="form-control" style="margin-top: 0.25rem; padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                        </div>
                    </div>

                    <!-- KELOLA & GANTI POSTER / BANNER -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">URL / Path Poster Vertikal:</label>
                            <input type="text" id="editMoviePoster" name="poster_url" class="form-control">
                            <div style="margin-top: 0.35rem; font-size: 0.78rem; color: var(--text-muted);">Upload ganti poster:</div>
                            <input type="file" name="poster_file" accept="image/*" class="form-control" style="margin-top: 0.25rem; padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">URL / Path Banner Horizontal:</label>
                            <input type="text" id="editMovieBanner" name="banner_url" class="form-control">
                            <div style="margin-top: 0.35rem; font-size: 0.78rem; color: var(--text-muted);">Upload ganti banner:</div>
                            <input type="file" name="banner_file" accept="image/*" class="form-control" style="margin-top: 0.25rem; padding: 0.35rem 0.5rem; font-size: 0.85rem;">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                        <button type="button" onclick="closeEditMovieModal()" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan & Perbarui Video Film</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= EDIT MODAL TRACK ================= -->
        <div id="editTrackModal" class="modal-overlay">
            <div class="modal-dialog">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-main);">Edit Data Tembang</h3>
                    <button type="button" onclick="closeEditTrackModal()" style="background: none; border: none; font-size: 1.25rem; color: var(--text-dim); cursor: pointer;">✕</button>
                </div>

                <form id="editTrackForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Judul Tembang:</label>
                        <input type="text" id="editTrackTitle" name="title" class="form-control" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Artis:</label>
                            <input type="text" id="editTrackArtist" name="artist" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Akses:</label>
                            <select id="editTrackAccess" name="access_type" class="form-control" required>
                                <option value="free">Gratis</option>
                                <option value="vip">VIP</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Lirik Tembang:</label>
                        <textarea id="editTrackLyrics" name="lyrics" class="form-control" rows="4"></textarea>
                    </div>

                    <input type="hidden" id="editTrackDuration" name="duration_seconds">
                    <input type="hidden" id="editTrackAudio" name="audio_url">
                    <input type="hidden" id="editTrackCover" name="cover_url">
                    <input type="hidden" id="editTrackYear" name="release_year">

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                        <button type="button" onclick="closeEditTrackModal()" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    @endif
</div>
@endsection

@section('scripts')
<script>
    function switchAdminTab(tabId) {
        document.querySelectorAll('.admin-tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.admin-tab-btn').forEach(btn => btn.classList.remove('active'));

        const targetContent = document.getElementById(tabId);
        const targetBtn = document.getElementById('btn-' + tabId);

        if (targetContent) targetContent.style.display = 'block';
        if (targetBtn) targetBtn.classList.add('active');
    }

    function openEditMovieModal(movie) {
        document.getElementById('editMovieForm').action = '/admin/movies/' + movie.id;
        document.getElementById('editMovieTitle').value = movie.title || '';
        document.getElementById('editMovieCategory').value = movie.category_id || '';
        document.getElementById('editMovieAccess').value = movie.access_type || 'free';
        document.getElementById('editMoviePrice').value = movie.ticket_price || 0;
        document.getElementById('editMovieDuration').value = movie.duration_minutes || 90;
        document.getElementById('editMovieDirector').value = movie.director || '';
        document.getElementById('editMovieYear').value = movie.release_year || 2026;
        document.getElementById('editMovieLocation').value = movie.location_tag || '';
        document.getElementById('editMovieTagline').value = movie.tagline || '';
        document.getElementById('editMovieSynopsis').value = movie.synopsis || '';
        document.getElementById('editMoviePoster').value = movie.poster_url || '';
        document.getElementById('editMovieBanner').value = movie.banner_url || '';
        document.getElementById('editMovieVideo').value = movie.video_url || '';
        document.getElementById('editMovieTrailer').value = movie.trailer_url || movie.video_url || '';

        document.getElementById('editMovieModal').classList.add('is-active');
    }

    function closeEditMovieModal() {
        document.getElementById('editMovieModal').classList.remove('is-active');
    }

    function openEditTrackModal(track) {
        document.getElementById('editTrackForm').action = '/admin/tracks/' + track.id;
        document.getElementById('editTrackTitle').value = track.title;
        document.getElementById('editTrackArtist').value = track.artist;
        document.getElementById('editTrackAccess').value = track.access_type;
        document.getElementById('editTrackLyrics').value = track.lyrics || '';
        document.getElementById('editTrackDuration').value = track.duration_seconds;
        document.getElementById('editTrackAudio').value = track.audio_url;
        document.getElementById('editTrackCover').value = track.cover_url;
        document.getElementById('editTrackYear').value = track.release_year;

        document.getElementById('editTrackModal').classList.add('is-active');
    }

    function closeEditTrackModal() {
        document.getElementById('editTrackModal').classList.remove('is-active');
    }
</script>
@endsection
