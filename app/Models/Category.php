<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            if (! $category->slug) {
                $category->slug = $category->uniqueSlug();
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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
