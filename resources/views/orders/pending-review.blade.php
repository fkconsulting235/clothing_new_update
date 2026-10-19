@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white border border-stone-200 rounded-xl p-6 text-center">
        <p class="text-xs uppercase tracking-wide text-amber-600 font-medium mb-2">🕐 Comprobante recibido</p>
        <h1 class="font-serif text-xl font-bold text-navy-900 mb-1">Estamos validando tu pago</h1>
        <p class="text-sm text-stone-600 mb-6">Pedido #{{ $order->id }} · Total {{ config('store.currency_symbol') }}{{ store_money($order->total) }}. Te confirmaremos apenas revisemos el comprobante.</p>

        <div class="flex flex-col gap-2">
            @if(config('store.whatsapp_number'))
                <a href="https://wa.me/{{ config('store.whatsapp_number') }}" target="_blank" rel="noopener" class="w-full px-4 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-[1.02] transition-all">💬 Escríbenos por WhatsApp</a>
            @endif
            <a href="{{ route('catalog.home') }}" class="text-xs text-stone-400 hover:text-stone-600 mt-2">Volver a la tienda</a>
        </div>
    </div>
@endsection
