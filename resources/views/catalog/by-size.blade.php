@extends('layouts.app')

@section('content')
    <h1 class="font-serif text-2xl font-bold text-navy-900 mb-1">Buscar por talla</h1>
    <p class="text-sm text-stone-500 mb-6">Muestra todas las prendas que tengan esa talla, sin importar la categoría.</p>

    <form method="get" class="flex flex-wrap gap-2 mb-8">
        @foreach($availableSizes as $size)
            <button type="submit" name="talla" value="{{ $size }}"
                    class="px-4 py-2 rounded-full text-sm font-medium border transition-colors {{ mb_strtolower($selectedSize) === mb_strtolower($size) ? 'bg-navy-900 text-white border-navy-900' : 'bg-white text-navy-900 border-stone-300 hover:border-navy-900' }}">
                {{ $size }}
            </button>
        @endforeach
    </form>

    @if($selectedSize)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($products as $product)
                @include('catalog._product-card', ['product' => $product])
            @empty
                <p class="text-stone-500 col-span-full">No encontramos prendas con la talla "{{ $selectedSize }}".</p>
            @endforelse
        </div>
    @else
        <p class="text-stone-500">Elige una talla de la lista para ver todas las prendas disponibles en ese tamaño.</p>
    @endif
@endsection
