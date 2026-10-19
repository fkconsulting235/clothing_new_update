@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white border border-stone-200 rounded-xl p-6 text-center">
        <p class="text-xs uppercase tracking-wide text-emerald-600 font-medium mb-2">✅ Reserva confirmada</p>
        <h1 class="font-serif text-xl font-bold text-navy-900 mb-1">{{ $reservation->variant->product->name }}</h1>
        <p class="text-sm text-stone-500 mb-1">{{ $reservation->variant->color }} · {{ $reservation->variant->size }} · Cantidad {{ $reservation->quantity }}</p>
        <p class="text-xs text-stone-400 mb-6">Código {{ $reservation->variant->sku }}</p>

        <div class="rounded-md bg-stone-50 border border-stone-200 px-4 py-3 text-sm text-stone-700 mb-6">
            Tienes hasta el <strong>{{ $reservation->expires_at->format('d/m/Y H:i') }}</strong> para pasar por la prenda o pagarla en línea.
        </div>

        <div class="flex flex-col gap-2">
            <a href="{{ route('orders.checkout', $reservation->variant) }}?reservation_id={{ $reservation->id }}" class="w-full px-4 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-[1.02] transition-all">Pagar en línea ahora</a>
            @if(config('store.whatsapp_number'))
                <a href="https://wa.me/{{ config('store.whatsapp_number') }}?text=Hola,%20reserv%C3%A9%20la%20prenda%20{{ $reservation->variant->sku }}" target="_blank" rel="noopener" class="w-full px-4 py-2.5 rounded-md border border-stone-300 text-stone-700 text-sm font-medium hover:bg-stone-50 transition-colors">💬 Coordinar por WhatsApp</a>
            @endif
            <a href="{{ route('catalog.product', $reservation->variant->product) }}" class="text-xs text-stone-400 hover:text-stone-600 mt-2">Volver a la prenda</a>
        </div>
    </div>
@endsection
