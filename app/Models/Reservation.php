<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    protected $fillable = ['variant_id', 'customer_name', 'customer_phone', 'quantity', 'status', 'expires_at'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Reservation $reservation) {
            if (! $reservation->expires_at) {
                $hours = config('store.reservation_hours', 24);
                $reservation->expires_at = now()->addHours($hours);
            }
        });
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'active' && $this->expires_at->isPast();
    }
}
