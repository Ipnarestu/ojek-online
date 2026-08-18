<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
        'online',
        'work_status',
        'lat',
        'lng',
        'vehicle_type',
        'vehicle_brand',
        'vehicle_plate',
        'rating',
        'rating_count',
        'ktp',
        'sim',
        // Field verifikasi
        'ktp_path',
        'sim_path',
        'stnk_path',
        'photo_path',
        'verification_status',
        'rejection_reason',
        'verified_at',
        'verified_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'online' => 'boolean',
        'rating' => 'float',
        'verified_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // ✅ DRIVER → PESANAN (UNTUK STATISTIK & ADMIN)
    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'driver_id');
    }

    // ✅ DRIVER → RATING
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'driver_id');
    }

    // ✅ DRIVER → WALLET
    public function wallet()
    {
        return $this->morphOne(Wallet::class, 'owner');
    }

    // ✅ DRIVER → KENDARAAN
    public function kendaraan()
    {
        return $this->hasOne(Kendaraan::class, 'driver_id', 'id');
    }

    // ✅ DRIVER → ADMIN YANG VERIFIKASI
    public function verifiedBy()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeOnline($query)
    {
        return $query->where('online', 1);
    }

    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'approved');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */
    public function isVerified()
    {
        return $this->verification_status === 'approved';
    }

    public function isPending()
    {
        return $this->verification_status === 'pending';
    }

    public function isRejected()
    {
        return $this->verification_status === 'rejected';
    }

    public function canLogin()
    {
        return $this->isVerified() && $this->status === 'aktif';
    }
}