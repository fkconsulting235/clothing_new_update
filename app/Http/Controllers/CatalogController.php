<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function home()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('order')
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->get();

        $latest = Product::where('is_active', true)
            ->with('images', 'variants')
            ->latest()
            ->take(8)
            ->get();

        $sizes = ProductVariant::whereHas('product', fn ($q) => $q->where('is_active', true))
            ->distinct()
            ->orderBy('size')
            ->pluck('size');

        return view('catalog.home', compact('categories', 'latest', 'sizes'));
    }

    public function category(Category $category)
    {
        abort_unless($category->is_active, 404);

        $products = $category->products()->where('is_active', true)->with('images', 'variants')->get();

        return view('catalog.category', compact('category', 'products'));
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load('images', 'variants', 'category');

        $whatsappMessage = "Hola, me interesa la prenda {$product->code} - {$product->name}";

        $related = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images', 'variants')
            ->latest()
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
                ->with('images', 'variants')
                ->get();
        }

        return view('catalog.search', compact('products', 'query'));
    }

    public function bySize(Request $request)
    {
        $size = trim((string) $request->get('talla', ''));

        $availableSizes = ProductVariant::whereHas('product', fn ($q) => $q->where('is_active', true))
            ->distinct()
            ->orderBy('size')
            ->pluck('size');

        $products = collect();
        if ($size !== '') {
            $products = Product::where('is_active', true)
                ->whereHas('variants', fn ($q) => $q->whereRaw('LOWER(size) = ?', [mb_strtolower($size)]))
                ->with('images', 'variants')
                ->distinct()
                ->get();
        }

        return view('catalog.by-size', ['products' => $products, 'selectedSize' => $size, 'availableSizes' => $availableSizes]);
    }
}
