<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Relasi ke pesanan (User punya banyak pesanan)
     */
    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }

    /**
     * Relasi ke laporan (User punya banyak laporan)
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Relasi ke rating (User punya banyak rating yang diberikan)
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Relasi ke wallet (Polymorphic)
     * Setiap user memiliki satu wallet
     */
    public function wallet()
    {
        return $this->morphOne(Wallet::class, 'owner');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Mendapatkan atau membuat wallet untuk user
     */
    public function getWalletAttribute()
    {
        return Wallet::getOrCreateFor($this);
    }

    /**
     * Cek apakah user sedang memiliki pesanan aktif
     */
    public function hasActiveOrder()
    {
        return $this->pesanans()
            ->whereIn('status', [Pesanan::STATUS_PENDING, Pesanan::STATUS_ACCEPTED])
            ->exists();
    }

    /**
     * Mendapatkan pesanan aktif user
     */
    public function getActiveOrder()
    {
        return $this->pesanans()
            ->whereIn('status', [Pesanan::STATUS_PENDING, Pesanan::STATUS_ACCEPTED])
            ->with('driver')
            ->first();
    }

    /**
     * Format nomor telepon
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = $this->phone;
        if (substr($phone, 0, 1) === '0') {
            $phone = '+62' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * Cek apakah user aktif
     */
    public function isActive()
    {
        return $this->is_active ?? true;
    }
}