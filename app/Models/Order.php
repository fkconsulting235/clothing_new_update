<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    protected $fillable = [
        'customer_name', 'customer_phone', 'customer_email', 'status',
        'payment_proof_path', 'total', 'reservation_id',
    ];

    protected function casts(): array
    {
        return ['total' => 'decimal:2'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof_path ? asset('storage/'.$this->payment_proof_path) : null;
    }
}
