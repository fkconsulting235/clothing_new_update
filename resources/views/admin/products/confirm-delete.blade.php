@extends('layouts.admin')

@section('content')
    <div class="max-w-md bg-white border border-stone-200 rounded-xl p-6">
        <h1 class="text-lg font-semibold text-navy-900 mb-2">⚠️ ¿Eliminar esta prenda?</h1>
        <p class="text-sm text-stone-600 mb-4">
            <strong>{{ $product->code }} · {{ $product->name }}</strong> se eliminará junto con todas sus variantes e imágenes. Esta acción no se puede deshacer.
        </p>

        @if($hasOrders)
            <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 mb-4">
                Esta prenda tiene <strong>pedidos registrados</strong> (ya se vendió alguna variante). No se va a poder eliminar — en vez de esto, edítala y desmarca "Publicada en la tienda" para quitarla de la tienda sin perder el historial de ventas.
            </div>
        @endif
        @if($activeReservations)
            <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 mb-4">
                Tiene <strong>{{ $activeReservations }} reserva(s) activa(s)</strong>. Si continúas, esas reservas se cancelarán también.
            </div>
        @endif

        <form method="post" action="{{ route('admin.products.destroy', $product) }}" class="flex items-center gap-3">
            @csrf
            <button type="submit" class="px-4 py-2.5 rounded-md bg-red-600 text-white text-sm font-medium hover:bg-red-700 hover:scale-105 transition-all">Sí, eliminar</button>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-stone-500 hover:text-stone-700">Cancelar</a>
        </form>
    </div>
@endsection
