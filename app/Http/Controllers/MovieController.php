<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Movie;
use App\Models\Ticket;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $query = Movie::with('category');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('synopsis', 'like', "%{$q}%")
                    ->orWhere('director', 'like', "%{$q}%")
                    ->orWhere('cast', 'like', "%{$q}%")
                    ->orWhere('location_tag', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($c) use ($request) {
                $c->where('slug', $request->category);
            });
        }

        if ($request->filled('access')) {
            $query->where('access_type', $request->access);
        }

        $movies = $query->orderBy('release_year', 'desc')->paginate(12)->withQueryString();
        $categories = Category::whereIn('type', ['movie', 'both'])->get();

        return view('movies.index', compact('movies', 'categories'));
    }

    public function show($slug)
    {
        $movie = Movie::with('category')->where('slug', $slug)->firstOrFail();
        $relatedMovies = Movie::where('category_id', $movie->category_id)
            ->where('id', '!=', $movie->id)
            ->take(4)
            ->get();

        $user = Auth::user();
        $accessCheck = $user ? $user->canAccessMovie($movie) : ['allowed' => ($movie->access_type === 'free'), 'reason' => 'guest'];
        $hasTicket = $user ? $user->hasTicketForMovie($movie->id) : false;

        return view('movies.show', compact('movie', 'relatedMovies', 'accessCheck', 'hasTicket'));
    }

    public function watch($slug)
    {
        $movie = Movie::with('category')->where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        if (! $user && $movie->access_type !== 'free') {
            return redirect()->route('login')->with('warning', 'Mangga mlebu akun dhisit nggo nonton film kiye.');
        }

        $accessCheck = $user ? $user->canAccessMovie($movie) : ['allowed' => true, 'reason' => 'free'];

        if (! $accessCheck['allowed']) {
            return view('movies.locked', compact('movie', 'accessCheck', 'user'));
        }

        $movie->increment('views_count');

        $relatedMovies = Movie::where('id', '!=', $movie->id)
            ->take(5)
            ->get();

        return view('movies.watch', compact('movie', 'relatedMovies', 'accessCheck'));
    }

    public function buyTicket(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('warning', 'Mlebu akun dhisik nggo tuku tiket.');
        }

        if ($user->hasTicketForMovie($movie->id)) {
            return redirect()->route('movies.watch', $movie->slug)->with('info', 'Sampeyan wis duwe tiket aktif nggo film kiye!');
        }

        $paymentMethod = $request->input('payment_method', 'dompet_bahari');
        $ticketPrice = $movie->ticket_price;

        $methodLabels = [
            'dompet_bahari' => 'Dompet Bahari',
            'qris' => 'QRIS Instan',
            'tf_bca' => 'Transfer Bank BCA (VA)',
            'tf_bri' => 'Transfer Bank BRI (VA)',
            'tf_mandiri' => 'Transfer Bank Mandiri (VA)',
            'tf_bni' => 'Transfer Bank BNI (VA)',
        ];
        $methodLabel = $methodLabels[$paymentMethod] ?? strtoupper($paymentMethod);

        if ($paymentMethod === 'dompet_bahari') {
            if ($user->wallet_balance < $ticketPrice) {
                return back()->with('error', 'Saldo Dompet Bahari sampeyan ora cukup (Kurang Rp '.number_format($ticketPrice - $user->wallet_balance, 0, ',', '.').'). Mangga top-up saldo dhisik utawa pilih metode QRIS / Transfer Bank.');
            }

            // Kurangi saldo user
            $user->wallet_balance -= $ticketPrice;
            $user->save();
        }

        // Buat tiket
        $ticketCode = 'TGL-'.strtoupper(Str::random(8));
        $ticket = Ticket::create([
            'ticket_code' => $ticketCode,
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'price_paid' => $ticketPrice,
            'payment_method' => $paymentMethod,
            'status' => 'active',
            'valid_until' => Carbon::now()->addDays(7),
        ]);

        // Catat transaksi
        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'purchase_ticket',
            'amount' => $ticketPrice,
            'reference_id' => $ticketCode,
            'description' => "Pembelian Tiket Premiere: {$movie->title} ({$methodLabel})",
        ]);

        return redirect()->route('movies.watch', $movie->slug)->with('success', "Pembayaran via {$methodLabel} berhasil! Kode Tiket: {$ticketCode}. Selamat nonton, dulur!");
    }
}
