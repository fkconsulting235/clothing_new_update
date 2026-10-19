@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto">
        <div class="bg-white border border-stone-200 rounded-xl p-6 mb-6">
            <p class="text-xs uppercase tracking-wide text-stone-400 mb-1 text-center">📋 Consultar reserva</p>
            <h1 class="font-serif text-xl font-bold text-navy-900 mb-4 text-center">¿Ya reservaste?</h1>
            <form method="get" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Tu teléfono / WhatsApp</label>
                    <input type="text" name="phone" value="{{ $phone }}" placeholder="El número con el que reservaste"
                           class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                </div>
                <button type="submit" class="w-full px-4 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-[1.02] transition-all">Buscar mi reserva</button>
            </form>
        </div>

        @if($searched)
            @forelse($reservations as $reservation)
                <div class="bg-white border border-stone-200 rounded-xl p-5 mb-3">
                    <h2 class="text-base font-semibold text-navy-900">{{ $reservation->variant->product->name }}</h2>
                    <p class="text-sm text-stone-500 mb-1">{{ $reservation->variant->color }} · {{ $reservation->variant->size }} · Cantidad {{ $reservation->quantity }}</p>
                    <p class="text-xs text-stone-400 mb-4">Código {{ $reservation->variant->sku }}</p>
                    <div class="rounded-md bg-stone-50 border border-stone-200 px-3 py-2 text-xs text-stone-600 mb-4">
                        Tienes hasta el <strong>{{ $reservation->expires_at->format('d/m/Y H:i') }}</strong> para pagarla.
                    </div>
                    <a href="{{ route('orders.checkout', $reservation->variant) }}?reservation_id={{ $reservation->id }}" class="block w-full text-center px-4 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-[1.02] transition-all">Pagar en línea ahora</a>
                </div>
            @empty
                <div class="bg-white border border-stone-200 rounded-xl p-6 text-center">
                    <p class="text-sm text-stone-600 mb-2">No encontramos ninguna reserva activa con ese número.</p>
                    <p class="text-xs text-stone-400">Puede que ya haya vencido o que el número esté escrito distinto a como lo diste al reservar.</p>
                    @if(config('store.whatsapp_number'))
                        <a href="https://wa.me/{{ config('store.whatsapp_number') }}" target="_blank" rel="noopener" class="inline-block mt-4 text-sm text-stone-700 hover:text-navy-900 underline">💬 Escríbenos por WhatsApp</a>
                    @endif
                </div>
            @endforelse
        @endif
    </div>
@endsection
