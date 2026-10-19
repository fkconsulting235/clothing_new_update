@extends('layouts.admin')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="font-serif text-3xl font-bold text-navy-900">Prendas</h1>
            <p class="text-sm text-stone-500">{{ $products->count() }} {{ $products->count() === 1 ? 'prenda' : 'prendas' }}</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2.5 rounded-xl bg-navy-900 text-white text-sm font-semibold hover:bg-navy-800 hover:-translate-y-0.5 transition-all">+ Nueva prenda</a>
    </div>

    <form method="get" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍 Buscar por nombre o código"
               class="flex-1 min-w-[12rem] rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        <select name="category" onchange="this.form.submit()" class="rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            <option value="">Todas las categorías</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-navy-900 text-white text-sm font-semibold hover:bg-navy-800 transition-colors">Buscar</button>
    </form>

    <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-left text-[11px] text-stone-500 uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3">Prenda</th>
                    <th class="px-4 py-3">Categoría</th>
                    <th class="px-4 py-3">Precio</th>
                    <th class="px-4 py-3">Stock</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($products as $product)
                    @php($stock = $product->variants->sum('stock'))
                    <tr class="hover:bg-stone-50/70 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-14 rounded-lg overflow-hidden bg-gradient-to-br from-stone-100 to-stone-200 shrink-0 flex items-center justify-center text-lg">
                                    @if($product->images->first())
                                        <img src="{{ $product->images->first()->url }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        👗
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-navy-900 truncate">{{ $product->name }}</p>
                                    <p class="text-xs text-stone-400">{{ $product->code }} · {{ $product->variants->count() }} {{ $product->variants->count() === 1 ? 'variante' : 'variantes' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ $product->category->name }}</td>
                        <td class="px-4 py-3 font-semibold text-navy-900">{{ config('store.currency_symbol') }}{{ store_money($product->base_price) }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $stock === 0 ? 'bg-red-100 text-red-700' : ($stock <= 2 ? 'bg-amber-100 text-amber-800' : 'bg-stone-100 text-stone-700') }}">
                                {{ $stock === 0 ? 'Agotada' : $stock.' en stock' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($product->is_active)
                                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">● Visible</span>
                            @else
                                <span class="text-xs font-semibold text-stone-500 bg-stone-100 px-2.5 py-1 rounded-full">○ Oculta</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.products.edit', $product) }}" class="px-3 py-1.5 rounded-lg border border-stone-300 text-xs font-semibold text-navy-900 hover:bg-stone-50 mr-1">✏️ Editar</a>
                            <a href="{{ route('admin.products.confirm-delete', $product) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 hover:bg-red-50">🗑️</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-stone-400">Todavía no hay prendas. ¡Agrega la primera!</td></tr>
                @endforelse
            </tbody>
        </table>
      </div>
    </div>
@endsection
