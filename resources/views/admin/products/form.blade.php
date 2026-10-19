@extends('layouts.admin')

@section('content')
    <h1 class="font-serif text-2xl font-bold text-navy-900 mb-1">{{ $product->exists ? 'Editar prenda' : 'Nueva prenda' }}</h1>
    <p class="text-sm text-stone-500 mb-6">Si dejas el código vacío, se genera solo (ej. PR-0001).</p>

    <form method="post" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="bg-white border border-stone-200 rounded-xl p-6 space-y-4">
            <h2 class="text-sm font-semibold text-navy-900">📋 Datos generales</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Categoría</label>
                    <select name="category_id" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                        <option value="">- Elige una -</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Código</label>
                    <input type="text" name="code" value="{{ old('code', $product->code) }}" placeholder="Se genera solo si lo dejas vacío"
                           class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                           class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Precio base</label>
                    <input type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price', $product->base_price) }}"
                           class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                </div>
                <div class="flex items-center gap-2 mt-6">
                    <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $product->exists ? $product->is_active : true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-stone-300 text-navy-900 focus:ring-gold-500">
                    <label for="is_active" class="text-sm text-stone-700">Publicada en la tienda</label>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-stone-700 mb-1">Descripción</label>
                    <textarea name="description" rows="4" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-xl p-6">
            <h2 class="text-sm font-semibold text-navy-900 mb-1">🎨 Colores, tallas y existencias</h2>
            <p class="text-xs text-stone-500 mb-4">Deja el "precio especial" vacío para usar el precio base. Marca "Eliminar" para quitar una variante existente.</p>

            <div class="hidden sm:grid sm:grid-cols-5 gap-2 mb-1 px-0.5">
                <p class="text-xs font-medium text-stone-500">Color</p>
                <p class="text-xs font-medium text-stone-500">Talla</p>
                <p class="text-xs font-medium text-stone-500">Precio especial (opcional)</p>
                <p class="text-xs font-medium text-stone-500">Existencias</p>
            </div>

            <div id="variant-rows" class="space-y-3">
                @foreach($product->variants ?? [] as $i => $variant)
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 items-start border-b border-stone-100 pb-3">
                        <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant->id }}">
                        <div>
                            <p class="text-xs text-stone-400 mb-1 sm:hidden">Color</p>
                            <input type="text" name="variants[{{ $i }}][color]" value="{{ $variant->color }}" placeholder="Color" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <p class="text-xs text-stone-400 mb-1 sm:hidden">Talla</p>
                            <input type="text" name="variants[{{ $i }}][size]" value="{{ $variant->size }}" placeholder="Talla" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <p class="text-xs text-stone-400 mb-1 sm:hidden">Precio especial (opcional)</p>
                            <input type="number" step="0.01" name="variants[{{ $i }}][price_override]" value="{{ $variant->price_override }}" placeholder="Precio especial" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <p class="text-xs text-stone-400 mb-1 sm:hidden">Existencias</p>
                            <input type="number" min="0" name="variants[{{ $i }}][stock]" value="{{ $variant->stock }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                        </div>
                        <label class="flex items-center gap-1.5 text-xs text-stone-500 mt-2">
                            <input type="checkbox" name="variants[{{ $i }}][delete]" value="1"> Eliminar
                        </label>
                    </div>
                @endforeach
            </div>

            <template id="variant-row-template">
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 items-start border-b border-stone-100 pb-3">
                    <div><input type="text" name="variants[__INDEX__][color]" placeholder="Color" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><input type="text" name="variants[__INDEX__][size]" placeholder="Talla" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><input type="number" step="0.01" name="variants[__INDEX__][price_override]" placeholder="Precio especial" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm"></div>
                    <div><input type="number" min="0" name="variants[__INDEX__][stock]" value="0" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm"></div>
                </div>
            </template>
            <button type="button" onclick="addRow('variant-rows', 'variant-row-template')" class="mt-3 text-sm text-gold-700 hover:text-gold-800 font-medium">+ Agregar otra variante</button>
        </div>

        <div class="bg-white border border-stone-200 rounded-xl p-6">
            <h2 class="text-sm font-semibold text-navy-900 mb-1">📸 Fotos</h2>
            <p class="text-xs text-stone-500 mb-4">El orden más bajo se muestra primero en la ficha de la prenda.</p>

            <div id="image-rows" class="space-y-4">
                @foreach($product->images ?? [] as $i => $image)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 border-b border-stone-100 pb-4">
                        <input type="hidden" name="images[{{ $i }}][id]" value="{{ $image->id }}">
                        <img src="{{ $image->thumb_url }}" alt="" class="w-16 h-16 rounded-lg object-cover border border-stone-200 shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-stone-400 mb-1">Reemplazar por otra foto (opcional)</p>
                            <input type="file" name="images[{{ $i }}][file]" accept="image/*" class="text-sm text-stone-700">
                        </div>
                        <div class="w-full sm:w-24 shrink-0">
                            <p class="text-xs text-stone-400 mb-1">Orden</p>
                            <input type="number" min="0" name="images[{{ $i }}][order]" value="{{ $image->order }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                        </div>
                        <label class="flex items-center gap-1.5 text-xs text-stone-500 shrink-0">
                            <input type="checkbox" name="images[{{ $i }}][delete]" value="1"> Eliminar esta foto
                        </label>
                    </div>
                @endforeach
            </div>

            <template id="image-row-template">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 border-b border-stone-100 pb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-stone-400 mb-1">Foto</p>
                        <input type="file" name="images[__INDEX__][file]" accept="image/*" class="text-sm text-stone-700">
                    </div>
                    <div class="w-full sm:w-24 shrink-0">
                        <p class="text-xs text-stone-400 mb-1">Orden</p>
                        <input type="number" min="0" name="images[__INDEX__][order]" value="0" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm">
                    </div>
                </div>
            </template>
            <button type="button" onclick="addRow('image-rows', 'image-row-template')" class="mt-1 text-sm text-gold-700 hover:text-gold-800 font-medium">+ Agregar otra foto</button>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-105 transition-all">💾 Guardar prenda</button>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-stone-500 hover:text-stone-700">Cancelar</a>
        </div>
    </form>

    <script>
        let rowIndex = {{ max(count($product->variants ?? []), count($product->images ?? [])) + 1000 }};
        function addRow(containerId, templateId) {
            const template = document.getElementById(templateId).innerHTML.replaceAll('__INDEX__', rowIndex++);
            document.getElementById(containerId).insertAdjacentHTML('beforeend', template);
        }
        document.addEventListener('DOMContentLoaded', function () {
            addRow('variant-rows', 'variant-row-template');
            addRow('variant-rows', 'variant-row-template');
            addRow('variant-rows', 'variant-row-template');
            addRow('image-rows', 'image-row-template');
            addRow('image-rows', 'image-row-template');
        });
    </script>
@endsection
