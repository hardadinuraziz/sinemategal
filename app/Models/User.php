<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_vip',
        'vip_plan',
        'vip_expires_at',
        'wallet_balance',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'vip_expires_at' => 'datetime',
            'is_vip' => 'boolean',
            'wallet_balance' => 'decimal:2',
            'password' => 'hashed',
        ];
    }

    public function hasVipAccess(): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        if (!$this->is_vip) {
            return false;
        }

        if ($this->vip_expires_at === null) {
            return true;
        }

        return $this->vip_expires_at->isFuture();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function activeTickets()
    {
        return $this->hasMany(Ticket::class)
            ->where('status', 'active')
            ->where('valid_until', '>', now());
    }

    public function hasTicketForMovie($movieId): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        return $this->activeTickets()->where('movie_id', $movieId)->exists();
    }

    public function canAccessMovie(Movie $movie): array
    {
        if ($this->role === 'admin') {
            return ['allowed' => true, 'reason' => 'admin'];
        }

        if ($movie->access_type === 'free') {
            return ['allowed' => true, 'reason' => 'free'];
        }

        if ($movie->access_type === 'vip') {
            if ($this->hasVipAccess()) {
                return ['allowed' => true, 'reason' => 'vip_active'];
            }
            return [
                'allowed' => false,
                'reason' => 'need_vip',
                'message' => 'Film kiye butuh status Langganan VIP Sebayu.'
            ];
        }

        if ($movie->access_type === 'ticket') {
            if ($this->hasTicketForMovie($movie->id)) {
                return ['allowed' => true, 'reason' => 'ticket_owned'];
            }
            return [
                'allowed' => false,
                'reason' => 'need_ticket',
                'message' => 'Film kiye butuh Tiket Premiere khusus.'
            ];
        }

        return ['allowed' => true, 'reason' => 'default'];
    }

    public function canAccessTrack(Track $track): array
    {
        return ['allowed' => true, 'reason' => 'free'];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }
}
