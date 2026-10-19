@extends('layouts.app')

@section('content')
    <h1 class="font-serif text-2xl font-bold text-navy-900 mb-6">
        @if($query) Resultados para "{{ $query }}" @else Buscar prendas @endif
    </h1>

    @if($query)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($products as $product)
                @include('catalog._product-card', ['product' => $product])
            @empty
                <p class="text-stone-500 col-span-full">No encontramos prendas que coincidan con "{{ $query }}".</p>
            @endforelse
        </div>
    @else
        <p class="text-stone-500">Escribe un nombre o código de prenda en la caja de búsqueda de arriba.</p>
    @endif
@endsection
