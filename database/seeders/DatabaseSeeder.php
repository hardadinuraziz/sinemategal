<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Movie;
use App\Models\Playlist;
use App\Models\Subscription;
use App\Models\Ticket;
use App\Models\Track;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Ki Patih Budaya (Admin)',
            'email' => 'admin@sinemategal.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_vip' => true,
            'vip_plan' => 'Paket Laka-Laka (VIP Sultan)',
            'vip_expires_at' => Carbon::now()->addYears(2),
            'wallet_balance' => 750000.00,
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
        ]);

        $vipUser = User::create([
            'name' => 'Kang Slamet Slawi',
            'email' => 'vip@sinemategal.id',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_vip' => true,
            'vip_plan' => 'Paket Wasgitel (3 Bulan)',
            'vip_expires_at' => Carbon::now()->addMonths(3),
            'wallet_balance' => 250000.00,
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
        ]);

        $regUser = User::create([
            'name' => 'Yu Minah Pesisir',
            'email' => 'user@sinemategal.id',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_vip' => false,
            'vip_plan' => null,
            'vip_expires_at' => null,
            'wallet_balance' => 100000.00,
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&auto=format&fit=crop&q=80',
        ]);

        $creatorUser = User::create([
            'name' => 'Mas Bowo Sineas Tegal',
            'email' => 'creator@sinemategal.id',
            'password' => Hash::make('password'),
            'role' => 'creator',
            'is_vip' => true,
            'vip_plan' => 'Akses Kreator Sinema',
            'vip_expires_at' => Carbon::now()->addYear(),
            'wallet_balance' => 450000.00,
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
        ]);

        // 2. Categories
        $catSintren = Category::create([
            'name' => 'Tradisi & Mistik Sintren',
            'slug' => 'tradisi-mistik-sintren',
            'type' => 'both',
            'description' => 'Eksplorasi kesenian tari mistis pesisir Tegal dan warisan leluhur Pantura.',
            'icon' => 'sparkles',
        ]);

        $catWayang = Category::create([
            'name' => 'Wayang Cepak & Ki Enthus',
            'slug' => 'wayang-cepak-ki-enthus',
            'type' => 'both',
            'description' => 'Koleksi lakon wayang golek khas Tegal karya dalang legendaris Alm. Ki Enthus Susmono.',
            'icon' => 'theater-masks',
        ]);

        $catKomedi = Category::create([
            'name' => 'Komedi & Drama Ngapak',
            'slug' => 'komedi-drama-ngapak',
            'type' => 'movie',
            'description' => 'Film cerita dan tontonan segar berbahasa Tegal medok yang laka-laka.',
            'icon' => 'smile',
        ]);

        $catSejarah = Category::create([
            'name' => 'Sejarah & Budaya Bahari',
            'slug' => 'sejarah-budaya-bahari',
            'type' => 'movie',
            'description' => 'Babad Ki Gede Sebayu, kejayaan maritim pelabuhan, dan peninggalan pabrik gula kolonial.',
            'icon' => 'landmark',
        ]);

        $catMusik = Category::create([
            'name' => 'Tembang & Pop Nada Tegalan',
            'slug' => 'tembang-pop-nada-tegalan',
            'type' => 'music',
            'description' => 'Lagu-lagu pop Ngapak, campursari Tegalan, blues warteg, dan gending tradisional.',
            'icon' => 'music',
        ]);

        // 3. Movies
        $movie1 = Movie::create([
            'category_id' => $catSintren->id,
            'title' => 'Sintren: Tembang Asmara Muarareja',
            'slug' => 'sintren-tembang-asmara-muarareja',
            'tagline' => 'Nalika kukusing menyan nembus sukma, katresnan sejati ora bakal sirna.',
            'synopsis' => 'Sekar (21 tahun), gadis yatim di tepian pesisir Muarareja Tegal, terpilih meneruskan titah sebagai penari Sintren. Di dalam kurungan kurung ayam yang ditutupi mori putih, Sekar dirasuki roh bidadari Dewi Rantamsari. Namun ketika Raden Joko Purnomo, pemuda ningrat dari Slawi datang menyaksikan, misteri dendam masa lalu antar keluarga mulai terkuak di bawah sinar rembulan malam purnama.',
            'director' => 'Agus Riyadi S.Sn & Bambang Eko',
            'cast' => 'Retno Maruti (Sekar), Dimas Bagus (Joko), Yu Darmi (Pawang Sintren)',
            'release_year' => 2026,
            'duration_minutes' => 112,
            'access_type' => 'ticket',
            'ticket_price' => 15000.00,
            'video_url' => '/videos/trailer-sintren.mp4',
            'trailer_url' => '/videos/trailer-sintren.mp4',
            'poster_url' => '/images/poster-sintren.jpg',
            'banner_url' => '/images/banner-arthouse.jpg',
            'location_tag' => 'Pesisir Muarareja, Kota Tegal',
            'views_count' => 14320,
            'rating' => 4.9,
            'featured' => true,
        ]);

        $movie2 = Movie::create([
            'category_id' => $catWayang->id,
            'title' => 'Ki Enthus Susmono: Dalang Gila Pesisir',
            'slug' => 'ki-enthus-susmono-dalang-gila-pesisir',
            'tagline' => 'Satu-satunya dalang yang berani mendobrak pakem demi suara rakyat jelata.',
            'synopsis' => 'Dokumenter eksklusif yang merangkum arsip langka dan wawancara mendalam tentang perjalanan hidup H. Ki Enthus Susmono. Dari anak dalang sederhana di Bengle Talang hingga menjadi bupati Tegal yang fenomenal. Menampilkan cuplikan rekaman lakon legendaris "Gatotkaca Kembar" dan "Lupit & Slentheng" yang sarat komedi kritis.',
            'director' => 'Dwi Sasongko',
            'cast' => 'Alm. Ki Enthus Susmono, Ki Haryo Enthus, Budayawan Tegal',
            'release_year' => 2025,
            'duration_minutes' => 88,
            'access_type' => 'vip',
            'ticket_price' => 0.00,
            'video_url' => '/videos/trailer-enthus.mp4',
            'trailer_url' => '/videos/trailer-enthus.mp4',
            'poster_url' => '/images/poster-enthus.jpg',
            'banner_url' => '/images/poster-enthus.jpg',
            'location_tag' => 'Padepokan Seni Tegal & Slawi',
            'views_count' => 28400,
            'rating' => 5.0,
            'featured' => true,
        ]);

        $movie3 = Movie::create([
            'category_id' => $catKomedi->id,
            'title' => 'Jodoh Segelas Teh Poci Wasgitel',
            'slug' => 'jodoh-segelas-teh-poci-wasgitel',
            'tagline' => 'Tresno kuwi kaya teh poci: Wangi, Sepet, Legi, tur Kentel!',
            'synopsis' => 'Tarjo (pemuda pedagang sate kambing muda di Lebaksiu) jatuh hati pada Lilis, mahasiswi cantik yang baru pulang dari Semarang. Tapi ayah Lilis, juragan teh poci paling disegani di Slawi, menuntut calon mantu yang paham filosofi gula batu dan poci tanah liat. Dibantu sahabatnya yang kocak, Tarjo merancang taktik komedi ngapak yang mengocok perut seantero Tegal.',
            'director' => 'Kukuh Keminclong',
            'cast' => 'Slamet Gundul, Sri Lestari, Mbah Darmo',
            'release_year' => 2026,
            'duration_minutes' => 95,
            'access_type' => 'free',
            'ticket_price' => 0.00,
            'video_url' => '/videos/trailer-tehpoci.mp4',
            'trailer_url' => '/videos/trailer-tehpoci.mp4',
            'poster_url' => '/images/poster-tehpoci.jpg',
            'banner_url' => '/images/poster-tehpoci.jpg',
            'location_tag' => 'Alun-alun Hanggawana Slawi',
            'views_count' => 51200,
            'rating' => 4.7,
            'featured' => true,
        ]);

        $movie4 = Movie::create([
            'category_id' => $catSejarah->id,
            'title' => 'Misteri Cerobong Pabrik Gula Jatibarang 1891',
            'slug' => 'misteri-cerobong-pabrik-gula-jatibarang-1891',
            'tagline' => 'Suara peluit lokomotif uap kuno yang berbunyi tengah malam...',
            'synopsis' => 'Pabrik Gula Jatibarang dibangun oleh Belanda pada abad ke-19 dan menyimpan jaringan rel lori tebu bersejarah. Ketika tim arkeolog muda menemukan ruang bawah tanah tersembunyi di balik dinding benteng pabrik, mereka terseret ke dalam teka-teki misteri hilangnya arsip perlawanan buruh pribumi tahun 1926.',
            'director' => 'Raden Hardian',
            'cast' => 'Bintang Pratama, Cindy Claudia, Pak Tua Mulyono',
            'release_year' => 2025,
            'duration_minutes' => 104,
            'access_type' => 'ticket',
            'ticket_price' => 20000.00,
            'video_url' => '/videos/trailer-jatibarang.mp4',
            'trailer_url' => '/videos/trailer-jatibarang.mp4',
            'poster_url' => '/images/poster-jatibarang.jpg',
            'banner_url' => '/images/poster-jatibarang.jpg',
            'location_tag' => 'Pabrik Gula Jatibarang, Brebes-Tegal',
            'views_count' => 19780,
            'rating' => 4.8,
            'featured' => false,
        ]);

        $movie5 = Movie::create([
            'category_id' => $catSejarah->id,
            'title' => 'Babad Ki Gede Sebayu: Titah Air Kali Gung',
            'slug' => 'babad-ki-gede-sebayu-titah-air-kali-gung',
            'tagline' => 'Kisah agung sang perintis peradaban tanah Tegal Laka-Laka.',
            'synopsis' => 'Drama sejarah megah yang menelusuri pengembaraan Ki Gede Sebayu, keturunan bangsawan Pajang yang memilih hijrah ke pesisir utara Jawa. Dengan kebijaksanaan ilmu kanuragan dan teknik irigasi kuno, beliau membendung Sungai Gung di Danawarih, menyuburkan tanah gersang, dan menyatukan rakyat menjadi Kadipaten Tegal yang makmur gemah ripah loh jinawi.',
            'director' => 'H. Suwardi M.Pd & Sanggar Sebayu',
            'cast' => 'Ahmad Rofiq (Ki Gede Sebayu), Nyi Ageng Sebayu, Patih Danawarih',
            'release_year' => 2026,
            'duration_minutes' => 125,
            'access_type' => 'vip',
            'ticket_price' => 0.00,
            'video_url' => '/videos/trailer-sebayu.mp4',
            'trailer_url' => '/videos/trailer-sebayu.mp4',
            'poster_url' => '/images/poster-sebayu.jpg',
            'banner_url' => '/images/poster-sebayu.jpg',
            'location_tag' => 'Danawarih & Kaligung, Balapulang',
            'views_count' => 32100,
            'rating' => 4.9,
            'featured' => false,
        ]);

        $movie6 = Movie::create([
            'category_id' => $catKomedi->id,
            'title' => 'Warteg Empire: Invasi Rasa Pantura',
            'slug' => 'warteg-empire-invasi-rasa-pantura',
            'tagline' => 'Dari etalase kaca dua tingkat hingga menggoyang lidah jutaan pekerja ibu kota.',
            'synopsis' => 'Dokumenter komikal dan penuh inspirasi tentang keuletan diaspora Wong Tegal yang membangun puluhan ribu jaringan Warteg di Jakarta. Diceritakan dengan gaya jenaka, mengungkap rahasia tempe orek basah, oseng kerang, sayur asem gurih, dan filosofi gotong-royong "Kharisma Bahari" yang legendaris.',
            'director' => 'Bowo Pantura Media',
            'cast' => 'Paguyuban Warteg Bahari, Chef Ragil, Pengusaha Muda Tegal',
            'release_year' => 2025,
            'duration_minutes' => 74,
            'access_type' => 'free',
            'ticket_price' => 0.00,
            'video_url' => '/videos/trailer-warteg.mp4',
            'trailer_url' => '/videos/trailer-warteg.mp4',
            'poster_url' => '/images/poster-warteg.jpg',
            'banner_url' => '/images/poster-warteg.jpg',
            'location_tag' => 'Sidapurna, Dukuhturi, Tegal',
            'views_count' => 64500,
            'rating' => 4.8,
            'featured' => false,
        ]);

        // 4. Music Tracks (Spotify / YouTube Music style)
        // Public reliable open audio URLs (W3C/Archive/CC samples)
        $sampleAudios = [
            'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
            'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',
            'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',
            'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3',
            'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3',
            'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3',
            'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3',
        ];

        $track1 = Track::create([
            'category_id' => $catMusik->id,
            'title' => 'Tegal Laka-Laka (Lagu Kebanggaan Wong Tegal)',
            'slug' => 'tegal-laka-laka',
            'artist' => 'Alm. Ki Enthus Susmono ft. Padepokan Sebayu',
            'album' => 'Mahakarya Seni Tegalan Vol. 1',
            'release_year' => 2024,
            'duration_seconds' => 248,
            'access_type' => 'free',
            'audio_url' => $sampleAudios[0],
            'cover_url' => '/images/album-lakalaka.jpg',
            'genre' => 'Pop Tradisi Ngapak',
            'description' => 'Tembang ikonik yang menyuarakan jati diri masyarakat Tegal yang gagah, ramah, jujur, dan berani bersuara apa adanya.',
            'lyrics' => "Kutha Tegal kutha Bahari\nKeminclong kotane, jembar dalane\nAyo sedulur kabeh pada gumregah\nMbangun Tegal supaya tambah gagah!\n\nTegal Laka-Laka! Ora ana tandhinge!\nSate batibul enak rasane\nNgeteh poci wasgitel kancane\nNyong lan kowe sedulur salawase!\n\n(Terjemahan: Kota Tegal Kota Bahari, bersih kotanya, lebar jalannya. Ayo saudara sekalian bangkit bersama membangun Tegal agar semakin gagah. Tegal Tiada Duanya! Tiada tandingannya!)",
            'plays_count' => 84210,
            'featured' => true,
        ]);

        $track2 = Track::create([
            'category_id' => $catMusik->id,
            'title' => 'Man Sakwi (Ngeteh Poci Ning Prapatan)',
            'slug' => 'man-sakwi-ngeteh-poci',
            'artist' => 'Imam Joend & Sahabat Ngapak',
            'album' => 'Nostalgia Guyub Pantura',
            'release_year' => 2023,
            'duration_seconds' => 215,
            'access_type' => 'free',
            'audio_url' => $sampleAudios[1],
            'cover_url' => 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?w=500&auto=format&fit=crop&q=80',
            'genre' => 'Folk Ngapak Akustik',
            'description' => 'Balada humoris tentang Man Sakwi yang gemar nongkrong di gardu pos kamling sambil menyeruput teh poci gula batu.',
            'lyrics' => "Man Sakwi... Man Sakwi... Saban sore ngeteh poci\nGula batu kentel sepet wangi\nMangan tahu aci anget ning geni...\n\nOjo lali dulur, guyub rukun ning ati\nSanajan urip rekasa tetep disyukuri\nKupat glabed sambele pedhes trasi\nUrip ning Tegal ayem tentrem sejati!",
            'plays_count' => 62140,
            'featured' => true,
        ]);

        $track3 = Track::create([
            'category_id' => $catSintren->id,
            'title' => 'Turun Sintren (Gending Magis Pesisir)',
            'slug' => 'turun-sintren-gending-magis',
            'artist' => 'Nayaga Pesisir Muarareja',
            'album' => 'Gending Mistik Pantura',
            'release_year' => 2025,
            'duration_seconds' => 312,
            'access_type' => 'vip',
            'audio_url' => $sampleAudios[2],
            'cover_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=500&auto=format&fit=crop&q=80',
            'genre' => 'Gending Etnik Karawitan',
            'description' => 'Rekaman rekaman gamelan dan tembang pemanggil roh halus pada ritual tradisi Sintren dengan vokal sinden klasik bersahaja.',
            'lyrics' => "Turun-turun sintren...\nSintrene widadari...\nNemu kembang yun ayunan...\nNemu kembang si jambu alas...\n\nIling-iling si bidadari mudhun saka langit\nNggawa berkah slamet para nelayan\nMugo segara paring iwak kang luber\nKutha Tegal tansah tentrem ayem.",
            'plays_count' => 45190,
            'featured' => true,
        ]);

        $track4 = Track::create([
            'category_id' => $catMusik->id,
            'title' => 'Teh Poci Wasgitel Blues',
            'slug' => 'teh-poci-wasgitel-blues',
            'artist' => 'Pantura Blues Project',
            'album' => 'Kopi vs Poci',
            'release_year' => 2025,
            'duration_seconds' => 260,
            'access_type' => 'free',
            'audio_url' => $sampleAudios[3],
            'cover_url' => 'https://images.unsplash.com/photo-1518609878373-06d740f60d8b?w=500&auto=format&fit=crop&q=80',
            'genre' => 'Blues Jawa Pesisiran',
            'description' => 'Harmonisasi gitar blues berpadu kendang jawa dengan lirik puitis tentang aroma poci tanah liat Slawi.',
            'lyrics' => "Wayah surup ning emperan omah\nAroma poci anyar nentremna manah\nWasgitel... wangi sepet legi kentel...\nKabeh masalah donya sirna mendel...\n\nYeah baby, this is Tegal Blues\nOra sah bingung, ora sah bingung\nAyo ngguyu bareng nyanyi bebarengan!",
            'plays_count' => 38900,
            'featured' => false,
        ]);

        $track5 = Track::create([
            'category_id' => $catMusik->id,
            'title' => 'Keminclong Tegal Bahari',
            'slug' => 'keminclong-tegal-bahari',
            'artist' => 'Dewi Sri Pantura ft. Tegal Hip-Hop Squad',
            'album' => 'Bregas Energetic',
            'release_year' => 2026,
            'duration_seconds' => 195,
            'access_type' => 'vip',
            'audio_url' => $sampleAudios[4],
            'cover_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=500&auto=format&fit=crop&q=80',
            'genre' => 'Pop Fusion Modern',
            'description' => 'Perpaduan beat modern elektro dengan laras slendro, merayakan kemajuan dan kebersihan kota Tegal modern.',
            'lyrics' => "Keminclong... Keminclong Tegal Bahari!\nSaka Alun-alun tekan Pelabuhan\nWong Tegal kabeh pinter lan lincah\nNgapak basane, tulus atine!\n\nLaka-laka tandinge, nomer siji tekade!\nAyo jaga kutha dewek, ben tambah moncer!",
            'plays_count' => 54100,
            'featured' => true,
        ]);

        $track6 = Track::create([
            'category_id' => $catWayang->id,
            'title' => 'Lakon Lupit & Slentheng: Goyang Parlemen',
            'slug' => 'lakon-lupit-slentheng-goyang-parlemen',
            'artist' => 'Alm. Ki Enthus Susmono',
            'album' => 'Wayang Cepak Tegal Legendaris',
            'release_year' => 2024,
            'duration_seconds' => 340,
            'access_type' => 'vip',
            'audio_url' => $sampleAudios[5],
            'cover_url' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?w=500&auto=format&fit=crop&q=80',
            'genre' => 'Wayang Golek Komedi Audio',
            'description' => 'Rekaman audio humor cerdas dua punakawan khas wayang cepak Tegal: Lupit dan Slentheng yang mengkritik pejabat korup dengan banyolan pedas.',
            'lyrics' => "Lupit: 'Heh Slentheng! Kowe ngerti ora sebabe dalan ning Tegal rata-rata jembar?'\nSlentheng: 'Genah ora ngerti Kang, emange kenang apa?'\nLupit: 'Ben nek ana wong sombong liwat, isih amba dalan nggo ngedohi dosane!'\n(Gerrrrr... swara keprak lan suluk Ki Enthus)",
            'plays_count' => 77300,
            'featured' => false,
        ]);

        // 5. Existing Ticket for User (Yu Minah owns a ticket for Movie 1 - Sintren)
        Ticket::create([
            'ticket_code' => 'TGL-SINTREN-88219',
            'user_id' => $regUser->id,
            'movie_id' => $movie1->id,
            'price_paid' => 15000.00,
            'payment_method' => 'dompet_bahari',
            'status' => 'active',
            'valid_until' => Carbon::now()->addDays(7),
        ]);

        // 6. Subscriptions
        Subscription::create([
            'user_id' => $vipUser->id,
            'plan_code' => 'wasgitel_3m',
            'plan_name' => 'Paket Wasgitel (3 Bulan)',
            'price_paid' => 79000.00,
            'payment_method' => 'dompet_bahari',
            'status' => 'active',
            'starts_at' => Carbon::now()->subDays(5),
            'expires_at' => Carbon::now()->addMonths(3),
        ]);

        // 7. Playlists
        $playlist1 = Playlist::create([
            'user_id' => $vipUser->id,
            'title' => 'Tembang Poci Guyub Sore',
            'description' => 'Koleksi tembang ngapak santai nggo kanca ngeteh poci karo tahu aci ning emperan.',
            'cover_url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80',
            'is_public' => true,
        ]);

        $playlist1->tracks()->attach([
            $track1->id => ['order' => 1],
            $track2->id => ['order' => 2],
            $track4->id => ['order' => 3],
        ]);

        $playlist2 = Playlist::create([
            'user_id' => $admin->id,
            'title' => 'Warisan Karawitan & Mistik Pantura',
            'description' => 'Gending asli sintren dan rekaman wayang cepak alm. Ki Enthus Susmono.',
            'cover_url' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=500&auto=format&fit=crop&q=80',
            'is_public' => true,
        ]);

        $playlist2->tracks()->attach([
            $track3->id => ['order' => 1],
            $track5->id => ['order' => 2],
            $track6->id => ['order' => 3],
        ]);

        // 8. Initial Wallet Transactions
        WalletTransaction::create([
            'user_id' => $regUser->id,
            'type' => 'topup',
            'amount' => 100000.00,
            'reference_id' => 'TOPUP-INIT-001',
            'description' => 'Saldo Selamat Datang Warga Bahari',
        ]);

        WalletTransaction::create([
            'user_id' => $regUser->id,
            'type' => 'purchase_ticket',
            'amount' => 15000.00,
            'reference_id' => 'TGL-SINTREN-88219',
            'description' => 'Beli Tiket Premiere: Sintren Tembang Asmara',
        ]);

        WalletTransaction::create([
            'user_id' => $vipUser->id,
            'type' => 'purchase_subscription',
            'amount' => 79000.00,
            'reference_id' => 'SUB-WASGITEL-002',
            'description' => 'Langganan VIP Paket Wasgitel (3 Bulan)',
        ]);

        $this->call(TembangDolananSeeder::class);
    }
}
