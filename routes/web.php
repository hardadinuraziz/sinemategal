<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Beranda Sinema Tegal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Autentikasi & Demo Switcher
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/demo-switch/{id}', [AuthController::class, 'switchDemoUser'])->name('demo.switch');

// Sinema / Film
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{slug}', [MovieController::class, 'show'])->name('movies.show');
Route::get('/movies/{slug}/watch', [MovieController::class, 'watch'])->name('movies.watch');
Route::post('/movies/{id}/buy-ticket', [MovieController::class, 'buyTicket'])->name('movies.buy-ticket');

// Musik / Nada Tegalan
Route::get('/music', [MusicController::class, 'index'])->name('music.index');
Route::get('/music/{slug}', [MusicController::class, 'show'])->name('music.show');
Route::post('/api/tracks/{id}/play', [MusicController::class, 'playTrack'])->name('api.tracks.play');
Route::get('/api/playlists/{id}', [MusicController::class, 'getPlaylistData'])->name('api.playlists.data');

// Langganan VIP
Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::post('/subscriptions/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');

// Tiket Bioskop Rakyat
Route::get('/my-tickets', [TicketController::class, 'myTickets'])->name('tickets.mine');

// Dompet Bahari & Transaksi
Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
Route::post('/wallet/topup', [WalletController::class, 'topup'])->name('wallet.topup');

// Payment Gateway (Midtrans Snap + Webhook + Checkout)
Route::post('/payments/midtrans/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');
Route::get('/payments/{orderId}', [PaymentController::class, 'checkout'])->name('payments.checkout');
Route::post('/payments/{orderId}/check-status', [PaymentController::class, 'checkStatus'])->name('payments.check-status');
Route::post('/payments/{orderId}/simulate-paid', [PaymentController::class, 'simulatePaid'])->name('payments.simulate-paid');
Route::post('/payments/{orderId}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');

// Admin / Kurator Hub
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/movies', [AdminController::class, 'storeMovie'])->name('admin.movies.store');
    Route::put('/movies/{id}', [AdminController::class, 'updateMovie'])->name('admin.movies.update');
    Route::delete('/movies/{id}', [AdminController::class, 'destroyMovie'])->name('admin.movies.destroy');
    Route::post('/tracks', [AdminController::class, 'storeTrack'])->name('admin.tracks.store');
    Route::put('/tracks/{id}', [AdminController::class, 'updateTrack'])->name('admin.tracks.update');
    Route::delete('/tracks/{id}', [AdminController::class, 'destroyTrack'])->name('admin.tracks.destroy');
    Route::post('/users/{id}/toggle-vip', [AdminController::class, 'toggleUserVip'])->name('admin.users.toggle-vip');
    Route::post('/users/{id}/add-balance', [AdminController::class, 'addUserBalance'])->name('admin.users.add-balance');
    Route::post('/users/{id}/change-role', [AdminController::class, 'updateUserRole'])->name('admin.users.change-role');
});
