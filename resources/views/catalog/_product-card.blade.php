@php
    $totalStock = $product->variants->sum(fn ($v) => $v->available_stock);
    $isNew = $product->created_at && $product->created_at->gt(now()->subDays(7));
    $mainImage = $product->mainImage();
    $colors = $product->variants->pluck('color')->unique()->count();
@endphp
<a href="{{ route('catalog.product', $product) }}" class="group block bg-white rounded-2xl overflow-hidden border border-stone-200/80 hover:border-gold-400 hover:shadow-xl hover:shadow-navy-900/10 hover:-translate-y-1 transition-all duration-300">
    <div class="relative aspect-[4/5] bg-gradient-to-br from-stone-100 to-stone-200 overflow-hidden">
        @if($mainImage)
            <img src="{{ $mainImage->url }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center text-stone-400">
                <span class="text-4xl mb-1">👗</span>
                <span class="font-serif text-lg tracking-widest text-stone-400">K<span class="text-gold-500">E</span>I</span>
            </div>
        @endif

        <div class="absolute top-3 left-3 flex flex-col gap-1.5 items-start">
            @if($totalStock <= 0)
                <span class="text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-stone-900/80 text-white backdrop-blur">Agotada</span>
            @else
                @if($isNew)
                    <span class="text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-rose-500 text-white shadow">Nuevo</span>
                @endif
                @if($totalStock === 1)
                    <span class="text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-gold-500 text-navy-900 shadow">¡Última pieza!</span>
                @elseif($totalStock <= 3)
                    <span class="text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">Quedan {{ $totalStock }}</span>
                @endif
            @endif
        </div>

        <div class="absolute inset-x-0 bottom-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
            <span class="block text-center text-xs font-semibold bg-navy-900 text-white rounded-full py-2">Ver detalle y apartar →</span>
        </div>
    </div>
    <div class="p-4">
        <h3 class="text-sm font-medium text-navy-900 truncate group-hover:text-gold-600 transition-colors">{{ $product->name }}</h3>
        <div class="flex items-center justify-between mt-1.5">
            <p class="text-base font-bold text-navy-900">{{ config('store.currency_symbol') }}{{ store_money($product->base_price) }}</p>
            @if($colors > 0)
                <p class="text-[11px] text-stone-400">{{ $colors }} {{ $colors === 1 ? 'color' : 'colores' }}</p>
            @endif
        </div>
    </div>
</a>
