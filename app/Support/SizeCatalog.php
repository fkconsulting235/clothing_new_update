<?php

namespace App\Support;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Las tallas se escriben a mano ("7 Años", "7 años", "Xs petite"...). Esta clase las
 * agrupa sin importar mayúsculas, las muestra con un formato uniforme y las ordena
 * de forma lógica (XXS, XS, S, M, L, XL..., luego meses, años y números).
 */
class SizeCatalog
{
    protected const LETTER_ORDER = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '2XL', 'XXXL', '3XL', '4XL'];

    public static function label(string $raw): string
    {
        $label = trim(preg_replace('/\s+/u', ' ', $raw));

        if (preg_match('/\b(años|año|anos|ano|meses|mes)\b/iu', $label)) {
            return mb_strtolower($label);
        }

        if (preg_match('/^[A-Za-z0-9\/\- ]+$/', $label)) {
            return strtoupper($label);
        }

        return mb_convert_case(mb_strtolower($label), MB_CASE_TITLE);
    }

    public static function key(string $raw): string
    {
        return mb_strtolower(self::label($raw));
    }

    /** @return array{0:int,1:float,2:string} */
    public static function sortKey(string $label): array
    {
        $upper = strtoupper($label);
        $position = array_search($upper, self::LETTER_ORDER, true);
        if ($position !== false) {
            return [0, (float) $position, $upper];
        }

        $number = preg_match('/\d+(?:[.,]\d+)?/', $label, $m) ? (float) str_replace(',', '.', $m[0]) : 0.0;

        if (preg_match('/\bmes(es)?\b/iu', $label)) {
            return [2, $number, $label];
        }
        if (preg_match('/\b(a[nñ]os?)\b/iu', $label)) {
            return [3, $number, $label];
        }
        if (preg_match('/^\d+(?:[.,]\d+)?$/', $label)) {
            return [4, $number, $label];
        }
        if (preg_match('/^[A-Z\/ ]+$/', $upper) || preg_match('/[A-Z]/', $upper)) {
            return [1, $number, $upper];
        }

        return [5, $number, $label];
    }

    /**
     * Tallas con prendas disponibles, ya agrupadas y ordenadas.
     * Cada elemento: label, key, raws (escrituras originales) y n (prendas distintas).
     *
     * @param  array<int>|null  $productIds  Limitar a estas prendas (por ejemplo, una categoría).
     */
    public static function groups(?array $productIds = null): Collection
    {
        $rows = ProductVariant::query()
            ->where('stock', '>', 0)
            ->whereHas('product', fn ($q) => $q->where('is_active', true))
            ->when($productIds !== null, fn ($q) => $q->whereIn('product_id', $productIds))
            ->select('size', DB::raw('COUNT(DISTINCT product_id) as n'))
            ->groupBy('size')
            ->get();

        return $rows
            ->groupBy(fn ($row) => self::key($row->size))
            ->map(fn (Collection $items, string $key) => [
                'key' => $key,
                'label' => self::label($items->first()->size),
                'raws' => $items->pluck('size')->all(),
                'n' => (int) $items->sum('n'),
            ])
            ->sortBy(fn (array $g) => self::sortKey($g['label']), SORT_REGULAR)
            ->values();
    }
}
