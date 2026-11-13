<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Playlist;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Seeder;

class TembangDolananSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('type', 'music')->first()
            ?? Category::where('type', 'both')->first();
        $owner = User::where('role', 'admin')->first() ?? User::first();

        if (! $category || ! $owner) {
            return;
        }

        $songs = [
            [
                'title' => 'Gundul Pacul',
                'slug' => 'gundul-pacul',
                'artist' => 'Putri Sari Wahyuningsih & Sekar Kinanthi Kidung Wening',
                'audio_url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Gundul-Gundul_Pacul.ogg',
                'duration_seconds' => 185,
                'description' => 'Rekaman oleh Putri Sari Wahyuningsih dan Sekar Kinanthi Kidung Wening. CC BY-SA 4.0: https://commons.wikimedia.org/wiki/File:Gundul-Gundul_Pacul.ogg',
            ],
            [
                'title' => 'Lir-Ilir',
                'slug' => 'lir-ilir',
                'artist' => 'Dewinta Sanggar Seni Laras Siwi',
                'audio_url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Gending_Lir-Ilir.ogg',
                'duration_seconds' => 353,
                'description' => 'Gending Lir-Ilir oleh Dewinta Sanggar Seni Laras Siwi. CC BY-SA 4.0: https://commons.wikimedia.org/wiki/File:Gending_Lir-Ilir.ogg',
            ],
            [
                'title' => 'Cublak-Cublak Suweng',
                'slug' => 'cublak-cublak-suweng',
                'artist' => 'Kinanthi Nugroho & Fandy Aprianto Rohman',
                'audio_url' => 'https://commons.wikimedia.org/wiki/Special:Redirect/file/Cublak-Cublak_Suweng.ogg',
                'duration_seconds' => 127,
                'description' => 'Rekaman oleh Kinanthi Nugroho dan Fandy Aprianto Rohman. CC BY-SA 4.0: https://commons.wikimedia.org/wiki/File:Cublak-Cublak_Suweng.ogg',
            ],
            [
                'title' => 'Suwe Ora Jamu',
                'slug' => 'suwe-ora-jamu',
                'artist' => 'Tembang Tradisional Jawa',
                'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3',
                'duration_seconds' => 210,
                'description' => 'Audio demo sementara dari SoundHelix; ganti dengan rekaman Tembang Dolanan berlisensi sebelum publikasi.',
            ],
            [
                'title' => 'Menthok-Menthok',
                'slug' => 'menthok-menthok',
                'artist' => 'Tembang Tradisional Jawa',
                'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3',
                'duration_seconds' => 210,
                'description' => 'Audio demo sementara dari SoundHelix; ganti dengan rekaman Tembang Dolanan berlisensi sebelum publikasi.',
            ],
        ];

        $tracks = [];
        foreach ($songs as $song) {
            $track = Track::updateOrCreate(
                ['slug' => $song['slug']],
                [
                    'category_id' => $category->id,
                    'title' => $song['title'],
                    'artist' => $song['artist'],
                    'album' => 'Tembang Dolanan (Lagu Anak Tradisional)',
                    'release_year' => 2026,
                    'duration_seconds' => $song['duration_seconds'],
                    'access_type' => 'free',
                    'audio_url' => $song['audio_url'],
                    'cover_url' => '/images/album-lakalaka.jpg',
                    'genre' => 'Tembang Dolanan',
                    'description' => $song['description'],
                    'lyrics' => null,
                    'featured' => false,
                ]
            );
            $tracks[] = $track;
        }

        $playlist = Playlist::updateOrCreate(
            ['title' => 'Tembang Dolanan'],
            [
                'user_id' => $owner->id,
                'description' => 'Lagu anak tradisional Jawa: dolanan, gending, lan tembang sing diwariskan turun-temurun.',
                'cover_url' => '/images/album-lakalaka.jpg',
                'is_public' => true,
            ]
        );

        $playlist->tracks()->sync(collect($tracks)->mapWithKeys(
            fn (Track $track, int $index) => [$track->id => ['order' => $index + 1]]
        )->all());

        $jawaSongs = [
            [
                'title' => 'Bengawan Solo',
                'slug' => 'bengawan-solo-niken-salindry',
                'artist' => 'Niken Salindry',
                'audio_url' => '/audio/musik-jawa/bengawan-solo-niken-salindry.mp3',
                'description' => 'Audio Niken Salindry dari berkas MP3 yang disediakan pengguna.',
            ],
            [
                'title' => 'Yen Ing Tawang Ono Lintang',
                'slug' => 'yen-ing-tawang-ono-lintang-niken-salindry',
                'artist' => 'Niken Salindry',
                'audio_url' => '/audio/musik-jawa/yen-ing-tawang-ono-lintang-niken-salindry.mp3',
                'description' => 'Audio Niken Salindry dari berkas MP3 yang disediakan pengguna.',
            ],
            [
                'title' => 'Sing Keri Cokot Boyo',
                'slug' => 'sing-keri-cokot-boyo-niken-salindry',
                'artist' => 'Niken Salindry',
                'audio_url' => '/audio/musik-jawa/sing-keri-cokot-boyo-niken-salindry.mp3',
                'description' => 'Audio Niken Salindry dari berkas MP3 yang disediakan pengguna.',
            ],
            [
                'title' => 'Durmagati',
                'slug' => 'durmagati',
                'artist' => 'Kajawi',
                'audio_url' => '/audio/musik-jawa/durmagati-kajawi.mp3',
                'description' => 'Audio KAJAWI dari berkas MP3 yang disediakan pengguna.',
            ],
            [
                'title' => 'Angin (Bawalah Jiwaku Melayang)',
                'slug' => 'angin-bawalah-jiwaku-melayang',
                'artist' => 'Naff',
                'duration_seconds' => 236,
                'audio_url' => '/audio/musik-jawa/angin-bawalah-jiwaku-melayang.mp3',
                'description' => 'Lagu pilihan pengguna (Angin - Bawalah Jiwaku Melayang).',
            ],
            [
                'title' => 'Dear God',
                'slug' => 'dear-god-avenged-sevenfold',
                'artist' => 'Avenged Sevenfold',
                'duration_seconds' => 393,
                'audio_url' => '/audio/dear-god-avenged-sevenfold.mp3',
                'description' => 'Lagu pilihan pengguna (Avenged Sevenfold - Dear God).',
            ],
            [
                'title' => "Doin' Time",
                'slug' => 'doin-time-lana-del-rey',
                'artist' => 'Lana Del Rey',
                'duration_seconds' => 265,
                'audio_url' => '/audio/musik-jawa/doin-time-lana-del-rey.mp3',
                'description' => "Lagu pilihan pengguna (Lana Del Rey - Doin' Time).",
            ],
        ];

        $jawaTracks = [];
        foreach ($jawaSongs as $song) {
            $jawaTracks[] = Track::updateOrCreate(
                ['slug' => $song['slug']],
                [
                    'category_id' => $category->id,
                    'title' => $song['title'],
                    'artist' => $song['artist'],
                    'album' => 'Musik Jawa',
                    'release_year' => 2026,
                    'duration_seconds' => $song['duration_seconds'] ?? 210,
                    'access_type' => 'free',
                    'audio_url' => $song['audio_url'],
                    'cover_url' => '/images/album-lakalaka.jpg',
                    'genre' => 'Musik Jawa',
                    'description' => $song['description'],
                    'lyrics' => null,
                    'featured' => false,
                ]
            );
        }

        $jawaPlaylist = Playlist::updateOrCreate(
            ['title' => 'Musik Jawa'],
            [
                'user_id' => $owner->id,
                'description' => 'Pilihan lagu Jawa, termasuk tembang klasik dan lagu populer.',
                'cover_url' => '/images/album-lakalaka.jpg',
                'is_public' => true,
            ]
        );

        $jawaPlaylist->tracks()->sync(collect($jawaTracks)->mapWithKeys(
            fn (Track $track, int $index) => [$track->id => ['order' => $index + 1]]
        )->all());
    }
}
