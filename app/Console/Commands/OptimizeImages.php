<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use App\Support\ImageOptimizer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('images:optimize {--force : Rehacer también las que ya tienen versión liviana} {--limit=0 : Procesar solo N fotos (0 = todas)}')]
#[Description('Crea versiones livianas (miniatura y mediana) de las fotos de las prendas, sin tocar las originales')]
class OptimizeImages extends Command
{
    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('Este PHP no tiene la extensión GD activa. Actívala en cPanel (Select PHP Version → gd) y vuelve a correr el comando.');

            return self::FAILURE;
        }

        $limit = (int) $this->option('limit');
        $done = $skipped = $failed = 0;
        $before = $after = 0;

        foreach (ProductImage::orderBy('id')->cursor() as $image) {
            $both = ImageOptimizer::derivedExists($image->image_path, 'thumb')
                && ImageOptimizer::derivedExists($image->image_path, 'medium');

            if ($both && ! $this->option('force')) {
                $skipped++;

                continue;
            }

            if (ImageOptimizer::generate($image->image_path)) {
                $done++;
                $before += (int) @filesize(storage_path('app/public/'.$image->image_path));
                $after += (int) @filesize(storage_path('app/public/'.ImageOptimizer::derivedPath($image->image_path, 'thumb')));
            } else {
                $failed++;
                $this->warn("  no se pudo procesar: {$image->image_path}");
            }

            if ($limit && $done + $failed >= $limit) {
                break;
            }
        }

        $mb = fn (int $bytes) => number_format($bytes / 1048576, 1);
        $this->info("Fotos procesadas: {$done} | ya listas: {$skipped} | con problema: {$failed}");
        if ($done) {
            $this->line("Peso de las originales procesadas: {$mb($before)} MB -> miniaturas de catálogo: {$mb($after)} MB");
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
