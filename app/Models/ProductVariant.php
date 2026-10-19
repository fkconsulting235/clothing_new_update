<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'color', 'size', 'sku', 'price_override', 'stock'];

    protected function casts(): array
    {
        return [
            'price_override' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ProductVariant $variant) {
            if (! $variant->sku) {
                $variant->sku = $variant->generateSku();
            }
        });
    }

    public function generateSku(): string
    {
        $colorCode = Str::upper(Str::limit(Str::slug($this->color), 10, ''));
        $sizeCode = Str::upper(Str::limit(Str::slug($this->size), 6, ''));
        $base = "{$this->product->code}-{$colorCode}-{$sizeCode}";

        $sku = $base;
        $suffix = 1;
        while (static::where('sku', $sku)->where('id', '!=', $this->id ?? 0)->exists()) {
            $suffix++;
            $sku = "{$base}-{$suffix}";
        }

        return $sku;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'variant_id');
    }

    public function getPriceAttribute(): string
    {
        return $this->price_override ?? $this->product->base_price;
    }

    public function getAvailableStockAttribute(): int
    {
        $reserved = $this->reservations()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->sum('quantity');

        return max($this->stock - $reserved, 0);
    }
}
