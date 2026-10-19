<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'code', 'description', 'base_price', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'base_price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (! $product->slug) {
                $product->slug = $product->uniqueSlug();
            }
        });

        static::created(function (Product $product) {
            if (! $product->code) {
                $product->code = sprintf('PR-%04d', $product->id);
                $product->saveQuietly();
            }
        });
    }

    public function uniqueSlug(): string
    {
        $base = Str::slug($this->name);
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $i++;
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order')->orderBy('id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('color')->orderBy('size');
    }

    public function mainImage(): ?ProductImage
    {
        return $this->images->first();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
