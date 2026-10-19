@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white border border-stone-200 rounded-xl p-6">
        <p class="text-xs uppercase tracking-wide text-stone-400 mb-1">{{ $variant->product->code }} · {{ $variant->color }}/{{ $variant->size }}</p>
        <h1 class="font-serif text-xl font-bold text-navy-900 mb-1">Pagar {{ $variant->product->name }}</h1>
        <p class="text-sm text-stone-600 mb-6">{{ config('store.currency_symbol') }}{{ store_money($variant->price) }} c/u. Completa tus datos para ver los datos de pago.</p>

        <form method="post" action="{{ route('orders.checkout', $variant) }}{{ $reservation ? '?reservation_id='.$reservation->id : '' }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Nombre completo</label>
                <input type="text" name="customer_name" value="{{ old('customer_name', $reservation->customer_name ?? '') }}" required
                       class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Teléfono / WhatsApp</label>
                <input type="text" name="customer_phone" value="{{ old('customer_phone', $reservation->customer_phone ?? '') }}" required
                       class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Correo (opcional)</label>
                <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                       class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            </div>
            @if(!$reservation)
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Cantidad</label>
                    <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}"
                           class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                </div>
            @endif
            <button type="submit" class="w-full px-4 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-[1.02] transition-all">Continuar al pago →</button>
        </form>
    </div>
@endsection
