@extends('layouts.app')

@php($currentCategory = $category)

@section('title', $category->name.' · '.config('store.name'))

@section('content')
    <div class="rounded-3xl bg-navy-900 px-6 sm:px-10 py-8 mb-8 relative overflow-hidden">
        <div class="absolute -top-16 -right-10 w-64 h-64 rounded-full bg-gold-500/20 blur-3xl"></div>
        <div class="relative">
            <p class="text-gold-400 text-[11px] font-semibold uppercase tracking-[0.25em] mb-1">Categoría</p>
            <h1 class="font-serif text-3xl sm:text-4xl font-bold text-white">{{ $category->name }}</h1>
            <p class="text-stone-300 text-sm mt-1">{{ $products->count() }} {{ $products->count() === 1 ? 'prenda disponible' : 'prendas disponibles' }}</p>
        </div>
    </div>

    @php($sizes = $products->flatMap(fn ($p) => $p->variants->pluck('size'))->unique()->sort()->values())
    @if($sizes->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <span class="text-xs font-semibold text-stone-500 mr-1">Filtrar por talla:</span>
            @foreach($sizes as $size)
                <a href="{{ route('catalog.by-size', ['talla' => $size]) }}" class="px-3 py-1.5 rounded-full border border-stone-300 text-xs font-semibold text-navy-900 hover:bg-navy-900 hover:text-white hover:border-navy-900 transition-colors">{{ $size }}</a>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
        @forelse($products as $product)
            @include('catalog._product-card', ['product' => $product])
        @empty
            <p class="text-stone-500 col-span-full">Todavía no hay prendas en esta categoría.</p>
        @endforelse
    </div>
@endsection
