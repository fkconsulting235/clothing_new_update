@extends('layouts.app')

@php
    $currentCategory = $product->category;
    $totalStock = $product->variants->sum(fn ($v) => $v->available_stock);
    $wa = config('store.whatsapp_number');
@endphp

@section('title', $product->name.' · '.config('store.name'))

@section('content')

    <nav class="text-xs text-stone-500 mb-5 flex items-center gap-2">
        <a href="{{ route('catalog.home') }}" class="hover:text-navy-900">Inicio</a>
        <span>›</span>
        <a href="{{ route('catalog.category', $product->category) }}" class="hover:text-navy-900">{{ $product->category->name }}</a>
        <span>›</span>
        <span class="text-navy-900 font-medium truncate">{{ $product->name }}</span>
    </nav>

    <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
        <div>
            <div class="aspect-[4/5] bg-gradient-to-br from-stone-100 to-stone-200 rounded-3xl overflow-hidden border border-stone-200/80">
                @if($product->images->isNotEmpty())
                    <img id="main-product-image" src="{{ $product->images->first()->medium_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-opacity duration-200">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-stone-400">
                        <span class="text-6xl mb-2">👗</span>
                        <span class="font-serif text-2xl tracking-widest">K<span class="text-gold-500">E</span>I</span>
                    </div>
                @endif
            </div>
            @if($product->images->count() > 1)
                <div class="grid grid-cols-5 gap-2 mt-3">
                    @foreach($product->images as $image)
                        <button type="button" onclick="swapMainImage('{{ $image->medium_url }}', this)"
                                class="thumb-btn aspect-square bg-stone-100 rounded-xl overflow-hidden border-2 {{ $loop->first ? 'border-gold-500' : 'border-transparent' }} hover:border-gold-400 transition-colors">
                            <img src="{{ $image->thumb_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[11px] uppercase tracking-wider text-stone-400">Código {{ $product->code }}</span>
                @if($totalStock <= 0)
                    <span class="text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-stone-900 text-white">Agotada</span>
                @elseif($totalStock <= 3)
                    <span class="text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">🔥 Quedan {{ $totalStock }}</span>
                @else
                    <span class="text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">Disponible</span>
                @endif
            </div>
            <h1 class="font-serif text-3xl sm:text-4xl font-bold text-navy-900 leading-tight">{{ $product->name }}</h1>
            <p class="text-3xl font-bold text-navy-900 mt-3">{{ config('store.currency_symbol') }}{{ store_money($product->base_price) }}</p>

            @if($product->description)
                <p class="text-sm text-stone-600 mt-4 leading-relaxed">{{ $product->description }}</p>
            @endif

            <h2 class="text-sm font-semibold text-navy-900 mt-8 mb-3">Elige tu color y talla</h2>
            <div class="space-y-3">
                @forelse($product->variants as $variant)
                    <div class="rounded-2xl border {{ $variant->available_stock > 0 ? 'border-stone-200 bg-white' : 'border-stone-200 bg-stone-50 opacity-70' }} p-4">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex items-center justify-center min-w-[2.5rem] h-9 px-3 rounded-lg bg-navy-900 text-white text-sm font-bold">{{ \App\Support\SizeCatalog::label($variant->size) }}</span>
                                <div>
                                    <p class="text-sm font-semibold text-navy-900 leading-tight">{{ $variant->color }}</p>
                                    <p class="text-xs {{ $variant->available_stock > 0 && $variant->available_stock <= 2 ? 'text-amber-700 font-medium' : 'text-stone-400' }}">
                                        @if($variant->available_stock <= 0)
                                            Sin existencias
                                        @elseif($variant->available_stock === 1)
                                            ¡Última pieza!
                                        @else
                                            {{ $variant->available_stock }} disponibles
                                        @endif
                                        @if($variant->price_override)
                                            · {{ config('store.currency_symbol') }}{{ store_money($variant->price) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @if($variant->available_stock > 0)
                            <div class="grid grid-cols-2 gap-2">
                                <form method="post" action="{{ route('orders.cart.add', $variant) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full px-3 py-2.5 rounded-xl bg-navy-900 text-white text-sm font-semibold hover:bg-navy-800 hover:-translate-y-0.5 transition-all">🛒 Agregar al carrito</button>
                                </form>
                                <a href="{{ route('reservations.create', $variant) }}" class="text-center px-3 py-2.5 rounded-xl border-2 border-gold-500 text-gold-700 text-sm font-semibold hover:bg-gold-500 hover:text-navy-900 transition-colors">⏱ Apartar 24 h</a>
                            </div>
                        @else
                            <p class="text-xs text-stone-500">Esta combinación se agotó. Escríbenos y te avisamos si vuelve.</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-stone-500">Todavía no hay variantes cargadas para esta prenda.</p>
                @endforelse
            </div>

            <ul class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs text-stone-600">
                <li class="flex items-center gap-2 bg-white border border-stone-200/80 rounded-xl px-3 py-2.5">⏱ <span>Apártala sin pagar</span></li>
                <li class="flex items-center gap-2 bg-white border border-stone-200/80 rounded-xl px-3 py-2.5">🏦 <span>Paga por transferencia</span></li>
                <li class="flex items-center gap-2 bg-white border border-stone-200/80 rounded-xl px-3 py-2.5">📦 <span>Entrega coordinada</span></li>
            </ul>

            @if($wa)
                <a href="https://wa.me/{{ $wa }}?text={{ urlencode($whatsappMessage) }}" target="_blank" rel="noopener"
                   class="mt-5 flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-[#25D366] text-white text-sm font-semibold hover:bg-[#1ebe5a] hover:-translate-y-0.5 transition-all">
                    💬 Preguntar por esta prenda en WhatsApp
                </a>
            @endif
        </div>
    </div>

    @if($related->isNotEmpty())
        <section class="mt-16">
            <h2 class="font-serif text-2xl font-bold text-navy-900 mb-5">También te puede gustar</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach($related as $product)
                    @include('catalog._product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    <script>
        function swapMainImage(url, btn) {
            document.getElementById('main-product-image').src = url;
            document.querySelectorAll('.thumb-btn').forEach(b => {
                b.classList.remove('border-gold-500');
                b.classList.add('border-transparent');
            });
            btn.classList.remove('border-transparent');
            btn.classList.add('border-gold-500');
        }
    </script>
@endsection
