<?php

namespace App\Models;

use App\Support\ImageOptimizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image_path', 'order'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Foto original, tal como se subió. */
    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->image_path);
    }

    /** Versión pequeña para tarjetas y miniaturas (cae a la original si no existe). */
    public function getThumbUrlAttribute(): string
    {
        return $this->derivedUrl('thumb');
    }

    /** Versión mediana para la foto grande de la ficha (cae a la original si no existe). */
    public function getMediumUrlAttribute(): string
    {
        return $this->derivedUrl('medium');
    }

    protected function derivedUrl(string $size): string
    {
        return ImageOptimizer::derivedExists($this->image_path, $size)
            ? asset('storage/'.ImageOptimizer::derivedPath($this->image_path, $size))
            : $this->url;
    }
}
