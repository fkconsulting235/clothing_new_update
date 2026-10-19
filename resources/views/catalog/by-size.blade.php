@extends('layouts.app')

@section('content')
    <h1 class="font-serif text-2xl font-bold text-navy-900 mb-1">Buscar por talla</h1>
    <p class="text-sm text-stone-500 mb-6">Muestra las prendas disponibles en esa talla, sin importar la categoría.</p>

    <div class="flex flex-wrap gap-2 mb-8">
        @forelse($groups as $group)
            <a href="{{ route('catalog.by-size', ['talla' => $group['label']]) }}"
               class="px-4 py-2 rounded-full text-sm font-medium border transition-colors {{ $selectedKey === $group['key'] ? 'bg-navy-900 text-white border-navy-900' : 'bg-white text-navy-900 border-stone-300 hover:border-navy-900' }}">
                {{ $group['label'] }} <span class="text-xs {{ $selectedKey === $group['key'] ? 'text-stone-300' : 'text-stone-400' }}">({{ $group['n'] }})</span>
            </a>
        @empty
            <p class="text-stone-500">Por ahora no hay prendas disponibles.</p>
        @endforelse
    </div>

    @if($selectedSize)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
            @forelse($products as $product)
                @include('catalog._product-card', ['product' => $product])
            @empty
                <p class="text-stone-500 col-span-full">No hay prendas disponibles en la talla "{{ $selectedSize }}" por ahora. Escríbenos y te avisamos cuando llegue algo.</p>
            @endforelse
        </div>
    @else
        <p class="text-stone-500">Elige una talla para ver las prendas disponibles en ese tamaño.</p>
    @endif
@endsection
