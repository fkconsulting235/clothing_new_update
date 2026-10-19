@extends('layouts.app')

@section('content')
    <h1 class="font-serif text-2xl font-bold text-navy-900 mb-6">Tu carrito</h1>

    @if(count($lines))
        <div class="bg-white border border-stone-200 rounded-xl overflow-hidden mb-6">
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-stone-50 text-left text-xs text-stone-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3">Prenda</th>
                        <th class="px-4 py-3">Cantidad</th>
                        <th class="px-4 py-3">Subtotal</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($lines as $line)
                        <tr>
                            <td class="px-4 py-3 text-navy-900">
                                {{ $line['variant']->product->name }}
                                <span class="block text-xs text-stone-400">{{ $line['variant']->color }} / {{ $line['variant']->size }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <form method="post" action="{{ route('orders.cart.update', $line['variant']->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="number" name="quantity" min="0" value="{{ $line['quantity'] }}" class="w-16 rounded-md border border-stone-300 px-2 py-1 text-sm">
                                    <button type="submit" class="text-xs text-stone-500 hover:text-navy-900 underline">Actualizar</button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-stone-700">{{ config('store.currency_symbol') }}{{ store_money($line['subtotal']) }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="post" action="{{ route('orders.cart.remove', $line['variant']->id) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800">Quitar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
          </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-xl p-6 max-w-md ml-auto">
            <p class="text-lg font-semibold text-navy-900 mb-4">Total: {{ config('store.currency_symbol') }}{{ store_money($total) }}</p>
            <form method="post" action="{{ route('orders.cart') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Nombre completo</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Teléfono / WhatsApp</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Correo (opcional)</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                </div>
                <button type="submit" class="w-full px-4 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-[1.02] transition-all">Continuar al pago →</button>
            </form>
        </div>
    @else
        <p class="text-stone-500">Tu carrito está vacío. <a href="{{ route('catalog.home') }}" class="text-gold-600 hover:underline">Ver la tienda →</a></p>
    @endif
@endsection
