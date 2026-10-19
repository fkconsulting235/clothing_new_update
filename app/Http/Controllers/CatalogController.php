<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\SizeCatalog;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /** Cuántas tallas se muestran en la portada (las más usadas). */
    protected const HOME_SIZES = 12;

    public function home()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->get();

        $latest = Product::where('is_active', true)
            ->availableFirst()
            ->with('images', 'variants')
            ->take(8)
            ->get();

        $allSizes = SizeCatalog::groups();
        $popular = $allSizes->sortByDesc('n')->take(self::HOME_SIZES)->pluck('key')->all();
        $sizes = $allSizes->filter(fn ($g) => in_array($g['key'], $popular, true))->values();
        $hasMoreSizes = $allSizes->count() > $sizes->count();

        return view('catalog.home', compact('categories', 'latest', 'sizes', 'hasMoreSizes'));
    }

    public function category(Category $category)
    {
        abort_unless($category->is_active, 404);

        $products = $category->products()
            ->where('is_active', true)
            ->availableFirst()
            ->with('images', 'variants')
            ->get();

        $sizes = SizeCatalog::groups($products->pluck('id')->all());

        return view('catalog.category', compact('category', 'products', 'sizes'));
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load('images', 'variants', 'category');

        $whatsappMessage = "Hola, me interesa la prenda {$product->code} - {$product->name}";

        $related = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->availableFirst()
            ->with('images', 'variants')
            ->take(4)
            ->get();

        return view('catalog.product-detail', compact('product', 'whatsappMessage', 'related'));
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->get('q', ''));
        $products = collect();

        if ($query !== '') {
            $products = Product::where('is_active', true)
                ->where(fn ($q) => $q->where('name', 'like', "%{$query}%")->orWhere('code', 'like', "%{$query}%"))
                ->availableFirst()
                ->with('images', 'variants')
                ->get();
        }

        return view('catalog.search', compact('products', 'query'));
    }

    public function bySize(Request $request)
    {
        $selected = trim((string) $request->get('talla', ''));
        $groups = SizeCatalog::groups();

        $products = collect();
        if ($selected !== '') {
            $group = $groups->firstWhere('key', SizeCatalog::key($selected));
            $raws = $group['raws'] ?? [];

            if ($raws) {
                $products = Product::where('is_active', true)
                    ->whereHas('variants', fn ($q) => $q->whereIn('size', $raws)->where('stock', '>', 0))
                    ->availableFirst()
                    ->with('images', 'variants')
                    ->get();
            }
        }

        return view('catalog.by-size', [
            'products' => $products,
            'selectedSize' => $selected,
            'selectedKey' => $selected !== '' ? SizeCatalog::key($selected) : '',
            'groups' => $groups,
        ]);
    }
}
