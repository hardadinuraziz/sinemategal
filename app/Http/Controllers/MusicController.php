<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Track;
use App\Models\Playlist;
use App\Models\Category;

class MusicController extends Controller
{
    public function index(Request $request)
    {
        $query = Track::with('category');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('artist', 'like', "%{$q}%")
                    ->orWhere('album', 'like', "%{$q}%")
                    ->orWhere('genre', 'like', "%{$q}%");
            });
        }

        if ($request->filled('access')) {
            $query->where('access_type', $request->access);
        }

        $tracks = $query->orderBy('plays_count', 'desc')->get();
        $playlists = Playlist::with('tracks')->get();
        $categories = Category::whereIn('type', ['music', 'both'])->get();

        return view('music.index', compact('tracks', 'playlists', 'categories'));
    }

    public function show($slug)
    {
        $track = Track::with('category')->where('slug', $slug)->firstOrFail();
        $relatedTracks = Track::where('id', '!=', $track->id)->take(5)->get();
        $accessCheck = ['allowed' => true, 'reason' => 'free'];

        return view('music.show', compact('track', 'relatedTracks', 'accessCheck'));
    }

    public function playTrack($id)
    {
        $track = Track::findOrFail($id);

        $track->increment('plays_count');

        return response()->json([
            'allowed' => true,
            'track' => [
                'id' => $track->id,
                'title' => $track->title,
                'artist' => $track->artist,
                'album' => $track->album,
                'cover_url' => $track->cover_url,
                'audio_url' => $track->audio_url,
                'duration_seconds' => $track->duration_seconds,
                'duration_string' => $track->duration_string,
                'access_type' => 'free',
                'lyrics' => $track->lyrics,
            ]
        ]);
    }

    public function getPlaylistData($id)
    {
        $playlist = Playlist::with('tracks')->findOrFail($id);
        return response()->json([
            'playlist' => $playlist,
            'tracks' => $playlist->tracks->map(function ($track) {
                return [
                    'id' => $track->id,
                    'title' => $track->title,
                    'artist' => $track->artist,
                    'album' => $track->album,
                    'cover_url' => $track->cover_url,
                    'audio_url' => $track->audio_url,
                    'duration_seconds' => $track->duration_seconds,
                    'duration_string' => $track->duration_string,
                    'access_type' => $track->access_type,
                    'lyrics' => $track->lyrics,
                ];
            })
        ]);
    }
}
