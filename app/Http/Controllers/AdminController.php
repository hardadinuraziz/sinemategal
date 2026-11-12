<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Movie;
use App\Models\Subscription;
use App\Models\Ticket;
use App\Models\Track;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $isAdmin = $user && in_array($user->role, ['admin', 'creator']);

        $stats = [
            'total_movies' => Movie::count(),
            'total_tracks' => Track::count(),
            'total_users' => User::count(),
            'active_vips' => User::where('is_vip', true)->count(),
            'tickets_sold' => Ticket::count(),
            'ticket_revenue' => Ticket::sum('price_paid') ?? 0,
            'sub_revenue' => Subscription::sum('price_paid') ?? 0,
        ];

        $movies = Movie::with('category')->latest()->get();
        $tracks = Track::with('category')->latest()->get();
        $recentTickets = Ticket::with(['user', 'movie'])->latest()->take(15)->get();
        $recentSubscriptions = Subscription::with('user')->latest()->take(15)->get();
        $categories = Category::all();
        $users = User::latest()->get();

        return view('admin.dashboard', compact(
            'isAdmin',
            'stats',
            'movies',
            'tracks',
            'recentTickets',
            'recentSubscriptions',
            'categories',
            'users'
        ));
    }

    public function storeMovie(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tagline' => 'nullable|string|max:255',
            'synopsis' => 'required|string',
            'director' => 'required|string|max:255',
            'cast' => 'nullable|string|max:255',
            'release_year' => 'required|integer',
            'duration_minutes' => 'required|integer',
            'access_type' => 'required|in:free,vip,ticket',
            'ticket_price' => 'nullable|numeric|min:0',
            'video_url' => 'nullable|string',
            'trailer_url' => 'nullable|string',
            'poster_url' => 'nullable|string',
            'banner_url' => 'nullable|string',
            'location_tag' => 'nullable|string',
            'featured' => 'nullable',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg,mkv,mov|max:512000',
            'trailer_file' => 'nullable|file|mimes:mp4,webm,ogg,mkv,mov|max:512000',
            'poster_file' => 'nullable|image|max:10240',
            'banner_file' => 'nullable|image|max:10240',
        ]);

        if (! file_exists(public_path('videos'))) {
            @mkdir(public_path('videos'), 0777, true);
        }
        if (! file_exists(public_path('images'))) {
            @mkdir(public_path('images'), 0777, true);
        }

        // Handle uploaded video file
        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $filename = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('videos'), $filename);
            $validated['video_url'] = '/videos/'.$filename;
        }

        // Handle uploaded trailer file
        if ($request->hasFile('trailer_file')) {
            $file = $request->file('trailer_file');
            $filename = 'trailer_'.time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('videos'), $filename);
            $validated['trailer_url'] = '/videos/'.$filename;
        }

        // Handle uploaded poster image
        if ($request->hasFile('poster_file')) {
            $file = $request->file('poster_file');
            $filename = 'poster_'.time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $validated['poster_url'] = '/images/'.$filename;
        }

        // Handle uploaded banner image
        if ($request->hasFile('banner_file')) {
            $file = $request->file('banner_file');
            $filename = 'banner_'.time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $validated['banner_url'] = '/images/'.$filename;
        }

        // Fallbacks
        $validated['poster_url'] = $validated['poster_url'] ?? '/images/poster-sintren.jpg';
        $validated['banner_url'] = $validated['banner_url'] ?? '/images/banner-vip.jpg';
        $validated['video_url'] = $validated['video_url'] ?? '/videos/trailer-sintren.mp4';
        $validated['trailer_url'] = $validated['trailer_url'] ?? $validated['video_url'];

        $validated['slug'] = Str::slug($validated['title']).'-'.rand(100, 999);
        $validated['ticket_price'] = $validated['access_type'] === 'ticket' ? ($validated['ticket_price'] ?? 15000) : 0;
        $validated['featured'] = $request->has('featured');

        unset($validated['video_file'], $validated['trailer_file'], $validated['poster_file'], $validated['banner_file']);

        Movie::create($validated);

        return back()->with('success', "Film '{$validated['title']}' kasil ditambahna ning katalog Sinema Tegal!");
    }

    public function updateMovie(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tagline' => 'nullable|string|max:255',
            'synopsis' => 'required|string',
            'director' => 'required|string|max:255',
            'cast' => 'nullable|string|max:255',
            'release_year' => 'required|integer',
            'duration_minutes' => 'required|integer',
            'access_type' => 'required|in:free,vip,ticket',
            'ticket_price' => 'nullable|numeric|min:0',
            'video_url' => 'nullable|string',
            'trailer_url' => 'nullable|string',
            'poster_url' => 'nullable|string',
            'banner_url' => 'nullable|string',
            'location_tag' => 'nullable|string',
            'featured' => 'nullable',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg,mkv,mov|max:512000',
            'trailer_file' => 'nullable|file|mimes:mp4,webm,ogg,mkv,mov|max:512000',
            'poster_file' => 'nullable|image|max:10240',
            'banner_file' => 'nullable|image|max:10240',
        ]);

        if (! file_exists(public_path('videos'))) {
            @mkdir(public_path('videos'), 0777, true);
        }
        if (! file_exists(public_path('images'))) {
            @mkdir(public_path('images'), 0777, true);
        }

        // Handle uploaded video file
        if ($request->hasFile('video_file')) {
            $file = $request->file('video_file');
            $filename = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('videos'), $filename);
            $validated['video_url'] = '/videos/'.$filename;
        }

        // Handle uploaded trailer file
        if ($request->hasFile('trailer_file')) {
            $file = $request->file('trailer_file');
            $filename = 'trailer_'.time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('videos'), $filename);
            $validated['trailer_url'] = '/videos/'.$filename;
        }

        // Handle uploaded poster image
        if ($request->hasFile('poster_file')) {
            $file = $request->file('poster_file');
            $filename = 'poster_'.time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $validated['poster_url'] = '/images/'.$filename;
        }

        // Handle uploaded banner image
        if ($request->hasFile('banner_file')) {
            $file = $request->file('banner_file');
            $filename = 'banner_'.time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $validated['banner_url'] = '/images/'.$filename;
        }

        $validated['ticket_price'] = $validated['access_type'] === 'ticket' ? ($validated['ticket_price'] ?? 15000) : 0;
        $validated['featured'] = $request->has('featured');

        // Retain existing values if not provided
        $validated['poster_url'] = $validated['poster_url'] ?? $movie->poster_url;
        $validated['banner_url'] = $validated['banner_url'] ?? $movie->banner_url;
        $validated['video_url'] = $validated['video_url'] ?? $movie->video_url;
        $validated['trailer_url'] = $validated['trailer_url'] ?? ($movie->trailer_url ?? $validated['video_url']);

        unset($validated['video_file'], $validated['trailer_file'], $validated['poster_file'], $validated['banner_file']);

        $movie->update($validated);

        return back()->with('success', "Data film '{$movie->title}' kasil dianyari!");
    }

    public function storeTrack(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'artist' => 'required|string|max:255',
            'album' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'release_year' => 'required|integer',
            'duration_seconds' => 'required|integer',
            'access_type' => 'required|in:free,vip',
            'audio_url' => 'required|string',
            'cover_url' => 'required|string',
            'lyrics' => 'nullable|string',
            'description' => 'nullable|string',
            'featured' => 'nullable',
        ]);

        $validated['slug'] = Str::slug($validated['title']).'-'.rand(100, 999);
        $validated['featured'] = $request->has('featured');

        Track::create($validated);

        return back()->with('success', "Tembang '{$validated['title']}' kasil ditambahna ning Nada Tegalan!");
    }

    public function updateTrack(Request $request, $id)
    {
        $track = Track::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'artist' => 'required|string|max:255',
            'album' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'release_year' => 'required|integer',
            'duration_seconds' => 'required|integer',
            'access_type' => 'required|in:free,vip',
            'audio_url' => 'required|string',
            'cover_url' => 'required|string',
            'lyrics' => 'nullable|string',
            'description' => 'nullable|string',
            'featured' => 'nullable',
        ]);

        $validated['featured'] = $request->has('featured');
        $track->update($validated);

        return back()->with('success', "Data tembang '{$track->title}' kasil dianyari!");
    }

    public function destroyMovie($id)
    {
        $movie = Movie::findOrFail($id);
        $title = $movie->title;
        $movie->delete();

        return back()->with('success', "Film '{$title}' kasil dibusak.");
    }

    public function destroyTrack($id)
    {
        $track = Track::findOrFail($id);
        $title = $track->title;
        $track->delete();

        return back()->with('success', "Tembang '{$title}' kasil dibusak.");
    }

    public function toggleUserVip($id)
    {
        $targetUser = User::findOrFail($id);
        $targetUser->is_vip = ! $targetUser->is_vip;
        if ($targetUser->is_vip) {
            $targetUser->vip_expires_at = now()->addDays(30);
        }
        $targetUser->save();

        $statusText = $targetUser->is_vip ? 'VIP Aktif (30 dina)' : 'VIP Dinonaktifkan';

        return back()->with('success', "Status pengguna {$targetUser->name} saiki: {$statusText}.");
    }

    public function addUserBalance(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        $amount = (int) $request->input('amount', 50000);

        $targetUser->wallet_balance += $amount;
        $targetUser->save();

        WalletTransaction::create([
            'user_id' => $targetUser->id,
            'amount' => $amount,
            'type' => 'topup',
            'description' => 'Top-up manual dening Kurator Admin Sinema Tegal',
            'reference_id' => 'ADM-'.strtoupper(Str::random(8)),
            'status' => 'completed',
        ]);

        return back()->with('success', 'Kasil nambahi saldo Rp '.number_format($amount, 0, ',', '.')." maring {$targetUser->name}.");
    }

    public function updateUserRole(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        $role = $request->input('role', 'user');

        if (! in_array($role, ['user', 'creator', 'admin'])) {
            $role = 'user';
        }

        $targetUser->role = $role;
        $targetUser->save();

        return back()->with('success', "Peran {$targetUser->name} kasil diubah dadi {$role}.");
    }
}
