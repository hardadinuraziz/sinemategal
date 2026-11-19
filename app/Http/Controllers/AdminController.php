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
use Illuminate\Http\UploadedFile;
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

        $this->handleMovieMediaUploads($request, $validated);

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

        $this->handleMovieMediaUploads($request, $validated);

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

    /**
     * @param  array<string, mixed>  $validated
     */
    private function handleMovieMediaUploads(Request $request, array &$validated): void
    {
        $uploads = [
            'video_file' => ['dir' => 'videos', 'prefix' => '', 'column' => 'video_url'],
            'trailer_file' => ['dir' => 'videos', 'prefix' => 'trailer_', 'column' => 'trailer_url'],
            'poster_file' => ['dir' => 'images', 'prefix' => 'poster_', 'column' => 'poster_url'],
            'banner_file' => ['dir' => 'images', 'prefix' => 'banner_', 'column' => 'banner_url'],
        ];

        foreach ($uploads as $field => $spec) {
            if ($request->hasFile($field)) {
                $validated[$spec['column']] = $this->storeUploadedMedia(
                    $request->file($field),
                    $spec['dir'],
                    $spec['prefix'],
                );
            }
        }
    }

    private function storeUploadedMedia(UploadedFile $file, string $subDir, string $prefix = ''): string
    {
        $ext = $file->getClientOriginalExtension() ?: 'bin';
        $filename = $prefix.time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$ext;
        $publicDir = public_path($subDir);

        if ((is_dir($publicDir) || @mkdir($publicDir, 0775, true)) && is_writable($publicDir)) {
            $file->move($publicDir, $filename);

            return '/'.$subDir.'/'.$filename;
        }

        // Vercel / read-only serverless filesystem: persist images as compact data URIs in DB
        if ($subDir === 'images') {
            return $this->encodeImageAsDataUri($file);
        }

        // Videos on read-only serverless: move to /tmp/uploads/{subDir}
        $tmpDir = "/tmp/uploads/{$subDir}";
        if (! is_dir($tmpDir)) {
            @mkdir($tmpDir, 0775, true);
        }
        $file->move($tmpDir, $filename);

        return route('tmp-media.show', ['dir' => $subDir, 'filename' => $filename], false);
    }

    private function encodeImageAsDataUri(UploadedFile $file): string
    {
        $realPath = $file->getRealPath();
        $mime = $file->getMimeType() ?: 'image/jpeg';
        $raw = $realPath ? file_get_contents($realPath) : '';

        if ($realPath && function_exists('imagecreatefromstring') && $raw !== '') {
            $src = @imagecreatefromstring($raw);
            if ($src !== false) {
                $w = imagesx($src);
                $h = imagesy($src);
                $maxW = 900;
                if ($w > $maxW) {
                    $newW = $maxW;
                    $newH = (int) round($h * ($maxW / $w));
                    $dst = imagecreatetruecolor($newW, $newH);
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
                    imagedestroy($src);
                    $src = $dst;
                }
                ob_start();
                imagejpeg($src, null, 82);
                $jpegBytes = ob_get_clean();
                imagedestroy($src);
                if (is_string($jpegBytes) && $jpegBytes !== '') {
                    return 'data:image/jpeg;base64,'.base64_encode($jpegBytes);
                }
            }
        }

        return 'data:'.$mime.';base64,'.base64_encode($raw ?: '');
    }
}
