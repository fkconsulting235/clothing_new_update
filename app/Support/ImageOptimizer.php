<?php

namespace App\Support;

class ImageOptimizer
{
    /** Ancho máximo en píxeles de cada versión liviana. */
    public const SIZES = ['thumb' => 600, 'medium' => 1200];

    public const QUALITY = 82;

    public static function derivedPath(string $path, string $size): string
    {
        return '_cache/'.$size.'/'.preg_replace('/\.[^.\/]+$/', '', $path).'.jpg';
    }

    /** Borra las versiones livianas de una foto (al eliminarla o reemplazarla). */
    public static function forget(string $path): void
    {
        foreach (array_keys(self::SIZES) as $size) {
            @unlink(storage_path('app/public/'.self::derivedPath($path, $size)));
        }
    }

    public static function derivedExists(string $path, string $size): bool
    {
        return is_file(storage_path('app/public/'.self::derivedPath($path, $size)));
    }

    /**
     * Crea las versiones livianas de una foto, sin tocar la original.
     * Devuelve false si no se pudo (sin GD, archivo ilegible, etc.).
     */
    public static function generate(string $path): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        $source = storage_path('app/public/'.$path);
        if (! is_file($source)) {
            return false;
        }

        @ini_set('memory_limit', '512M');

        $info = @getimagesize($source);
        if (! $info) {
            return false;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
            IMAGETYPE_PNG => @imagecreatefrompng($source),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false,
            default => false,
        };
        if (! $image) {
            return false;
        }

        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $orientation = @exif_read_data($source)['Orientation'] ?? 1;
            $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;
            if ($angle) {
                $rotated = imagerotate($image, $angle, 0);
                if ($rotated) {
                    imagedestroy($image);
                    $image = $rotated;
                }
            }
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $ok = true;

        foreach (self::SIZES as $name => $maxWidth) {
            $scale = min(1, $maxWidth / $width);
            $newWidth = max(1, (int) round($width * $scale));
            $newHeight = max(1, (int) round($height * $scale));

            $canvas = imagecreatetruecolor($newWidth, $newHeight);
            imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
            imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imageinterlace($canvas, true);

            $target = storage_path('app/public/'.self::derivedPath($path, $name));
            if (! is_dir(dirname($target))) {
                @mkdir(dirname($target), 0755, true);
            }
            $ok = imagejpeg($canvas, $target, self::QUALITY) && $ok;
            imagedestroy($canvas);
        }

        imagedestroy($image);

        return $ok;
    }
}
