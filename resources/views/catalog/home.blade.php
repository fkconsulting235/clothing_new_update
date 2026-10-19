@extends('layouts.app')

@section('content')
    @php
        $heroImages = $latest->map(fn ($p) => $p->mainImage())->filter()->take(3)->values();
        $wa = config('store.whatsapp_number');
    @endphp

    {{-- HERO --}}
    <section class="relative rounded-3xl overflow-hidden bg-navy-900 mb-8">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-gold-500/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-16 w-80 h-80 rounded-full bg-rose-400/10 blur-3xl"></div>

        <div class="relative grid lg:grid-cols-2 gap-8 items-center px-6 sm:px-12 py-12 sm:py-16">
            <div>
                <p class="inline-flex items-center gap-2 text-gold-400 text-[11px] font-semibold uppercase tracking-[0.25em] mb-4">
                    <span class="w-6 h-px bg-gold-400"></span> Bienvenida a
                </p>
                <h1 class="font-serif text-4xl sm:text-6xl font-bold text-white leading-[1.05] mb-5">
                    Ropa única,<br><span class="text-gold-400">lista para ti.</span>
                </h1>
                <p class="text-stone-300 max-w-md text-sm sm:text-base leading-relaxed mb-8">
                    Prendas seleccionadas una por una, casi todas únicas. Apártala 24 horas sin pagar, págala por transferencia y recíbela en tu casa.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="#nuevos" class="px-6 py-3 rounded-full bg-gold-500 hover:bg-gold-400 text-navy-900 text-sm font-semibold shadow-lg shadow-gold-500/20 hover:-translate-y-0.5 transition-all">Ver nuevos ingresos</a>
                    @if($wa)
                        <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Hola, quiero ver las prendas disponibles') }}" target="_blank" rel="noopener"
                           class="px-6 py-3 rounded-full border border-white/25 hover:bg-white/10 text-white text-sm font-semibold transition-all">💬 Escríbenos por WhatsApp</a>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-4 mt-10 max-w-md">
                    <div><p class="font-serif text-2xl font-bold text-white">24 h</p><p class="text-[11px] text-stone-400 leading-tight">para apartar sin pagar</p></div>
                    <div><p class="font-serif text-2xl font-bold text-white">1 de 1</p><p class="text-[11px] text-stone-400 leading-tight">piezas únicas, sin repetir</p></div>
                    <div><p class="font-serif text-2xl font-bold text-white">100%</p><p class="text-[11px] text-stone-400 leading-tight">pago seguro por transferencia</p></div>
                </div>
            </div>

            <div class="hidden lg:block relative h-[380px]">
                @if($heroImages->count() >= 1)
                    @foreach($heroImages as $i => $img)
                        <div class="absolute rounded-2xl overflow-hidden shadow-2xl ring-4 ring-navy-900
                            {{ $i === 0 ? 'top-0 right-24 w-56 h-72 rotate-[-4deg] z-10' : '' }}
                            {{ $i === 1 ? 'top-12 right-0 w-48 h-64 rotate-[5deg]' : '' }}
                            {{ $i === 2 ? 'bottom-0 right-40 w-44 h-56 rotate-[-2deg] z-20' : '' }}">
                            <img src="{{ $img->thumb_url }}" alt="" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                @else
                    <div class="absolute top-4 right-20 w-56 h-72 rounded-2xl bg-gradient-to-br from-rose-200 to-rose-300 rotate-[-4deg] shadow-2xl flex items-center justify-center text-6xl">👗</div>
                    <div class="absolute top-16 right-0 w-48 h-64 rounded-2xl bg-gradient-to-br from-amber-100 to-gold-400 rotate-[5deg] shadow-2xl flex items-center justify-center text-6xl">👚</div>
                    <div class="absolute bottom-0 right-44 w-44 h-56 rounded-2xl bg-gradient-to-br from-stone-100 to-stone-300 rotate-[-2deg] shadow-2xl flex items-center justify-center text-6xl">🧥</div>
                @endif
            </div>
        </div>
    </section>

    {{-- SELLOS DE CONFIANZA --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-12">
        @foreach([
            ['⏱', 'Apártala 24 h', 'Sin pagar nada por adelantado'],
            ['🏦', 'Paga por transferencia', 'Sube tu comprobante y listo'],
            ['📦', 'Recíbela en casa', 'Coordinamos la entrega contigo'],
            ['💬', 'Atención directa', 'Respondemos por WhatsApp'],
        ] as [$icon, $title, $text])
            <div class="flex items-start gap-3 bg-white border border-stone-200/80 rounded-2xl px-4 py-4">
                <span class="text-2xl leading-none">{{ $icon }}</span>
                <div>
                    <p class="text-sm font-semibold text-navy-900 leading-tight">{{ $title }}</p>
                    <p class="text-xs text-stone-500 mt-0.5">{{ $text }}</p>
                </div>
            </div>
        @endforeach
    </section>

    {{-- TALLAS RAPIDAS --}}
    @if($sizes->isNotEmpty())
        <section class="bg-white border border-stone-200/80 rounded-2xl px-5 sm:px-8 py-5 mb-14 flex flex-col sm:flex-row sm:items-center gap-4">
            <h2 class="font-serif text-xl font-bold text-navy-900 shrink-0">¿Cuál es tu talla?</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($sizes as $size)
                    <a href="{{ route('catalog.by-size', ['talla' => $size['label']]) }}"
                       class="min-w-[3rem] text-center px-4 py-2 rounded-xl border border-stone-300 text-sm font-semibold text-navy-900 hover:bg-navy-900 hover:text-white hover:border-navy-900 transition-colors">{{ $size['label'] }}</a>
                @endforeach
                @if($hasMoreSizes)
                    <a href="{{ route('catalog.by-size') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-gold-600 hover:text-navy-900 transition-colors">Ver todas las tallas →</a>
                @endif
            </div>
        </section>
    @endif

    {{-- NUEVOS INGRESOS --}}
    <section id="nuevos" class="mb-16 scroll-mt-24">
        <div class="flex items-end justify-between mb-6">
            <div>
                <p class="text-gold-600 text-[11px] font-semibold uppercase tracking-[0.25em] mb-1">Recién llegadas</p>
                <h2 class="font-serif text-3xl font-bold text-navy-900">Nuevos ingresos</h2>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
            @forelse($latest as $product)
                @include('catalog._product-card', ['product' => $product])
            @empty
                <p class="text-stone-500 col-span-full">Pronto tendremos prendas nuevas. ¡Vuelve a visitarnos!</p>
            @endforelse
        </div>
    </section>

    {{-- CATEGORIAS --}}
    @if($categories->isNotEmpty())
        <section class="mb-16">
            <h2 class="font-serif text-3xl font-bold text-navy-900 mb-6">Compra por categoría</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($categories as $i => $category)
                    @php $tones = ['from-rose-100 to-rose-200', 'from-amber-100 to-amber-200', 'from-emerald-100 to-emerald-200', 'from-sky-100 to-sky-200', 'from-violet-100 to-violet-200']; @endphp
                    <a href="{{ route('catalog.category', $category) }}"
                       class="group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $tones[$i % count($tones)] }} p-5 h-32 flex flex-col justify-end hover:-translate-y-1 hover:shadow-lg transition-all">
                        <span class="font-serif text-xl font-bold text-navy-900">{{ $category->name }}</span>
                        <span class="text-xs text-navy-900/60">{{ $category->products_count }} {{ $category->products_count === 1 ? 'prenda' : 'prendas' }}</span>
                        <span class="absolute top-4 right-4 text-navy-900/40 group-hover:text-navy-900 group-hover:translate-x-1 transition-all">→</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- COMO FUNCIONA --}}
    <section class="mb-16">
        <div class="text-center mb-8">
            <p class="text-gold-600 text-[11px] font-semibold uppercase tracking-[0.25em] mb-1">Fácil y seguro</p>
            <h2 class="font-serif text-3xl font-bold text-navy-900">Así de simple es comprar</h2>
        </div>
        <div class="grid sm:grid-cols-3 gap-5">
            @foreach([
                ['1', 'Elige tu prenda', 'Mira el catálogo y escoge tu talla y color favorito.'],
                ['2', 'Apártala 24 horas', 'Resérvala sin pagar y tómate tu tiempo para decidir.'],
                ['3', 'Paga y recíbela', 'Transfiere, sube tu comprobante y coordinamos la entrega.'],
            ] as [$n, $title, $text])
                <div class="relative bg-white border border-stone-200/80 rounded-2xl p-6">
                    <span class="w-10 h-10 rounded-full bg-navy-900 text-gold-400 font-serif font-bold flex items-center justify-center mb-4">{{ $n }}</span>
                    <h3 class="font-semibold text-navy-900 mb-1">{{ $title }}</h3>
                    <p class="text-sm text-stone-500 leading-relaxed">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CONOCENOS --}}
    <section class="grid md:grid-cols-2 gap-8 items-center bg-white border border-stone-200/80 rounded-3xl p-6 sm:p-10 mb-16">
        <img src="{{ asset('img/TIENDA.jpg') }}" alt="{{ config('store.name') }} - Donde tú eliges tu estilo" loading="lazy"
             class="w-full max-w-sm mx-auto rounded-2xl shadow-xl shadow-navy-900/20">
        <div>
            <p class="text-gold-600 text-[11px] font-semibold uppercase tracking-[0.25em] mb-1">Conócenos</p>
            <h2 class="font-serif text-3xl font-bold text-navy-900 mb-3">Donde tú eliges tu estilo</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-5">
                En {{ config('store.name') }} seleccionamos cada prenda pensando en ti. Son piezas únicas, así que cuando algo te guste,
                apártalo: tienes 24 horas para decidir sin pagar nada por adelantado.
            </p>
            <ul class="space-y-2 text-sm text-stone-700">
                <li class="flex items-center gap-2">✅ Prendas seleccionadas una por una</li>
                <li class="flex items-center gap-2">✅ Apartado gratis por 24 horas</li>
                <li class="flex items-center gap-2">✅ Pago por transferencia, sin complicaciones</li>
            </ul>
        </div>
    </section>

    {{-- CTA WHATSAPP --}}
    @if($wa)
        <section class="relative overflow-hidden rounded-3xl bg-navy-900 px-6 sm:px-12 py-10 text-center">
            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-96 h-40 bg-gold-500/20 blur-3xl"></div>
            <div class="relative">
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-white mb-2">¿No encuentras tu talla o estilo?</h2>
                <p class="text-stone-300 text-sm mb-6">Cuéntanos qué buscas y te avisamos cuando llegue algo para ti.</p>
                <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Hola, estoy buscando una prenda en talla ...') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-[#25D366] hover:bg-[#1ebe5a] text-white text-sm font-semibold shadow-lg hover:-translate-y-0.5 transition-all">💬 Escríbenos por WhatsApp</a>
            </div>
        </section>
    @endif
@endsection
