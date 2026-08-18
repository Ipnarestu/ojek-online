<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    /**
     * ==============================
     * STATUS CONSTANTS
     * ==============================
     */
    public const STATUS_PENDING = 'pending';       // Menunggu driver
    public const STATUS_ACCEPTED = 'accepted';     // Driver menerima, menuju jemput
    public const STATUS_ON_PROGRESS = 'on_progress'; // Driver sedang menuju jemput (alternatif)
    public const STATUS_ON_TRIP = 'on_trip';       // Driver sedang mengantar ke tujuan
    public const STATUS_COMPLETED = 'completed';   // Pesanan selesai
    public const STATUS_CANCELLED = 'cancelled';   // Dibatalkan user
    public const STATUS_EXPIRED = 'expired';       // Kadaluarsa (tidak ada driver)

    /**
     * ==============================
     * MASS ASSIGNMENT
     * ==============================
     */
    protected $fillable = [
        'user_id',
        'driver_id',
        'pickup_location',
        'pickup_lat',
        'pickup_lng',
        'destination',
        'dest_lat',
        'dest_lng',
        'pickup_note',
        'price',
        'admin_fee',
        'driver_earning',
        'payment_method',
        'payment_status',
        'status',
        'user_notified',
        'tip_amount',           // Tip dari user ke driver
        'completed_at',         // Waktu pesanan selesai
    ];

    /**
     * ==============================
     * CASTING
     * ==============================
     */
    protected $casts = [
        'pickup_lat' => 'float',
        'pickup_lng' => 'float',
        'dest_lat' => 'float',
        'dest_lng' => 'float',
        'price' => 'float',
        'tip_amount' => 'float',
        'user_notified' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * ==============================
     * RELATIONSHIPS
     * ==============================
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'pesanan_id');
    }

    /**
     * ==============================
     * HELPER METHODS - STATUS CHECK
     * ==============================
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isOnProgress(): bool
    {
        return $this->status === self::STATUS_ON_PROGRESS;
    }

    public function isOnTrip(): bool
    {
        return $this->status === self::STATUS_ON_TRIP;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    /**
     * ==============================
     * HELPER METHODS - STATUS LABEL
     * ==============================
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu Driver',
            self::STATUS_ACCEPTED => 'Driver Menuju Lokasi',
            self::STATUS_ON_PROGRESS => 'Driver Menuju Lokasi',
            self::STATUS_ON_TRIP => 'Perjalanan ke Tujuan',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_EXPIRED => 'Kadaluarsa',
            default => 'Unknown',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_ACCEPTED => 'blue',
            self::STATUS_ON_PROGRESS => 'blue',
            self::STATUS_ON_TRIP => 'purple',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_EXPIRED => 'gray',
            default => 'gray',
        };
    }

    /**
     * ==============================
     * HELPER METHODS - PRICE & TIP
     * ==============================
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->price + ($this->tip_amount ?? 0);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedTipAttribute(): string
    {
        return 'Rp ' . number_format($this->tip_amount ?? 0, 0, ',', '.');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->getTotalAmountAttribute(), 0, ',', '.');
    }

    /**
     * ==============================
     * HELPER METHODS - SCOPE
     * ==============================
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_ACCEPTED, self::STATUS_ON_PROGRESS, self::STATUS_ON_TRIP]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * ==============================
     * HELPER METHODS - ACTIONS
     * ==============================
     */
    public function accept($driverId): void
    {
        $this->update([
            'driver_id' => $driverId,
            'status' => self::STATUS_ACCEPTED
        ]);
    }

    public function startToPickup(): void
    {
        $this->update(['status' => self::STATUS_ON_PROGRESS]);
    }

    public function startTrip(): void
    {
        $this->update(['status' => self::STATUS_ON_TRIP]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now()
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function addTip(float $amount): void
    {
        $this->update(['tip_amount' => $amount]);
    }

    /**
     * ==============================
     * ACCESSORS & MUTATORS
     * ==============================
     */
    public function getDurationAttribute(): ?string
    {
        if (!$this->completed_at) {
            return null;
        }
        
        $minutes = $this->created_at->diffInMinutes($this->completed_at);
        
        if ($minutes < 60) {
            return $minutes . ' menit';
        }
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        return $hours . ' jam ' . $mins . ' menit';
    }
}