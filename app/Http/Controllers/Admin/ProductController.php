<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Reservation;
use App\Support\ImageOptimizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category', 'variants', 'images')
            ->when($request->get('q'), fn (Builder $q, $term) => $q->where(
                fn ($qq) => $qq->where('name', 'like', "%{$term}%")->orWhere('code', 'like', "%{$term}%")
            ))
            ->when($request->get('category'), fn (Builder $q, $catId) => $q->where('category_id', $catId))
            ->orderByDesc('created_at')
            ->get();

        $categories = Category::orderBy('order')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return $this->form(new Product);
    }

    public function edit(Product $product)
    {
        $product->load('variants', 'images');

        return $this->form($product);
    }

    protected function form(Product $product)
    {
        $categories = Category::orderBy('order')->get();

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function store(Request $request)
    {
        return $this->save($request, new Product);
    }

    public function update(Request $request, Product $product)
    {
        return $this->save($request, $product);
    }

    protected function save(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => ['nullable', 'string', 'max:20', Rule::unique('products', 'code')->ignore($product->id)],
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'variants' => 'array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.size' => 'nullable|string|max:20',
            'variants.*.price_override' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.delete' => 'nullable|boolean',
            'images' => 'array',
            'images.*.id' => 'nullable|exists:product_images,id',
            'images.*.file' => 'nullable|image|max:5120',
            'images.*.order' => 'nullable|integer|min:0',
            'images.*.delete' => 'nullable|boolean',
        ]);

        $keptBecauseSold = [];

        DB::transaction(function () use ($data, $product, $request, &$keptBecauseSold) {
            $product->fill([
                'category_id' => $data['category_id'],
                'code' => ($data['code'] ?? null) ?: null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'base_price' => $data['base_price'],
                'is_active' => $request->boolean('is_active'),
            ]);
            $product->save();

            foreach ($data['variants'] ?? [] as $i => $row) {
                $hasId = ! empty($row['id']);
                if (! empty($row['delete']) && $hasId) {
                    if (OrderItem::where('variant_id', $row['id'])->exists()) {
                        $variant = ProductVariant::find($row['id']);
                        if ($variant) {
                            $variant->update(['stock' => 0]);
                            $keptBecauseSold[] = "{$variant->color} / {$variant->size}";
                        }
                    } else {
                        ProductVariant::where('id', $row['id'])->delete();
                    }

                    continue;
                }
                if (empty($row['color']) || empty($row['size'])) {
                    continue;
                }
                $payload = [
                    'product_id' => $product->id,
                    'color' => $row['color'],
                    'size' => $row['size'],
                    'price_override' => $row['price_override'] ?: null,
                    'stock' => $row['stock'] ?? 0,
                ];
                if ($hasId) {
                    ProductVariant::where('id', $row['id'])->update($payload);
                } else {
                    ProductVariant::create($payload);
                }
            }

            foreach ($data['images'] ?? [] as $i => $row) {
                $hasId = ! empty($row['id']);
                if (! empty($row['delete']) && $hasId) {
                    $image = ProductImage::find($row['id']);
                    if ($image) {
                        Storage::disk('public')->delete($image->image_path);
                        ImageOptimizer::forget($image->image_path);
                        $image->delete();
                    }

                    continue;
                }

                $file = $request->file("images.{$i}.file");
                if ($file) {
                    $path = $file->store('products/'.now()->format('Y/m'), 'public');
                    try {
                        ImageOptimizer::generate($path);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                    if ($hasId) {
                        $image = ProductImage::find($row['id']);
                        Storage::disk('public')->delete($image->image_path);
                        ImageOptimizer::forget($image->image_path);
                        $image->update(['image_path' => $path, 'order' => $row['order'] ?? 0]);
                    } else {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'order' => $row['order'] ?? 0,
                        ]);
                    }
                } elseif ($hasId) {
                    ProductImage::where('id', $row['id'])->update(['order' => $row['order'] ?? 0]);
                }
            }
        });

        $redirect = redirect()->route('admin.products.index')->with('success', 'Prenda guardada correctamente.');

        if ($keptBecauseSold) {
            $redirect->with('error',
                'No se pudo eliminar: '.implode(', ', $keptBecauseSold).'. Ya tiene pedidos registrados y se borraría '.
                'el historial de ventas. Quedó con 0 existencias, así que aparece como agotada.'
            );
        }

        return $redirect;
    }

    public function confirmDelete(Product $product)
    {
        $hasOrders = OrderItem::whereHas('variant', fn ($q) => $q->where('product_id', $product->id))->exists();
        $activeReservations = Reservation::whereHas('variant', fn ($q) => $q->where('product_id', $product->id))
            ->where('status', 'active')->count();

        return view('admin.products.confirm-delete', compact('product', 'hasOrders', 'activeReservations'));
    }

    public function destroy(Product $product)
    {
        $hasOrders = OrderItem::whereHas('variant', fn ($q) => $q->where('product_id', $product->id))->exists();

        if ($hasOrders) {
            return redirect()->route('admin.products.index')->with('error',
                "No se puede eliminar \"{$product->code} · {$product->name}\" porque tiene pedidos registrados ".
                '(ya se compró alguna de sus variantes). Para quitarla de la tienda sin perder ese historial, '.
                'edítala y desmarca "Publicada en la tienda".'
            );
        }

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
                        ImageOptimizer::forget($image->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', "Se eliminó la prenda {$product->code}.");
    }
}
