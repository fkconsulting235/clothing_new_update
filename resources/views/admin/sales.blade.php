@extends('layouts.admin')

@section('content')
    <h1 class="font-serif text-2xl font-bold text-navy-900 mb-6">💰 Ventas</h1>

    <form method="get" class="flex flex-wrap items-end gap-3 mb-6">
        <div>
            <label class="block text-xs font-medium text-stone-500 mb-1">Período</label>
            <select name="period" onchange="this.form.submit()" class="rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
                <option value="" {{ !$period ? 'selected' : '' }}>Todo el tiempo</option>
                <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hoy</option>
                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Esta semana</option>
                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Este mes</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-500 mb-1">Desde</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-stone-500 mb-1">Hasta</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>
        <button type="submit" class="px-4 py-2 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 transition-colors">Filtrar</button>
        @if($period || $dateFrom || $dateTo)
            <a href="{{ route('admin.sales') }}" class="text-sm text-stone-500 hover:text-stone-700 pb-2">Limpiar filtros</a>
        @endif
    </form>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6">
        <div class="bg-white border border-stone-200 rounded-xl p-4">
            <p class="text-xs text-stone-500 mb-1">📦 Pedidos pagados</p>
            <p class="text-xl sm:text-2xl font-semibold text-navy-900">{{ $totalCount }}</p>
        </div>
        <div class="bg-navy-900 rounded-xl p-4">
            <p class="text-xs text-stone-400 mb-1">💵 Total vendido</p>
            <p class="text-xl sm:text-2xl font-semibold text-white truncate">{{ config('store.currency_symbol') }}{{ store_money($totalAmount) }}</p>
        </div>
    </div>

    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-left text-xs text-stone-500 uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3">Pedido</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Artículos</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-stone-50 transition-colors">
                        <td class="px-4 py-3 text-navy-900">#{{ $order->id }}</td>
                        <td class="px-4 py-3 text-stone-600">
                            {{ $order->customer_name }}
                            <span class="block text-xs text-stone-400">{{ $order->customer_phone }}</span>
                        </td>
                        <td class="px-4 py-3 text-stone-600">
                            @foreach($order->items as $item)
                                <span class="block">{{ $item->quantity }}× {{ $item->variant->sku }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ config('store.currency_symbol') }}{{ store_money($order->total) }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $order->created_at }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-stone-500">No hay ventas en este período.</td></tr>
                @endforelse
            </tbody>
        </table>
      </div>
    </div>
@endsection
