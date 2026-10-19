@extends('layouts.admin')

@section('content')
    <h1 class="font-serif text-2xl font-bold text-navy-900 mb-6">Pedidos</h1>

    <form method="get" class="flex flex-wrap gap-3 mb-4">
        <select name="status" onchange="this.form.submit()" class="rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            <option value="">Todos los estados</option>
            @foreach($statusChoices as $value => $label)
                <option value="{{ $value }}" {{ $selectedStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-left text-xs text-stone-500 uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3">Pedido</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Artículos</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Comprobante</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Fecha</th>
                    <th class="px-4 py-3"></th>
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
                        <td class="px-4 py-3">
                            @if($order->payment_proof_path)
                                <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noopener" class="inline-block" title="Ver comprobante">
                                    <img src="{{ $order->payment_proof_url }}" alt="Comprobante" class="w-12 h-12 rounded-lg object-cover border border-stone-200 hover:scale-110 transition-transform">
                                </a>
                            @else
                                <span class="text-stone-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($order->status === 'paid')
                                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">● {{ $statusChoices[$order->status] }}</span>
                            @elseif($order->status === 'pending')
                                <span class="text-xs font-semibold text-amber-800 bg-amber-100 px-2.5 py-1 rounded-full">● {{ $statusChoices[$order->status] }}</span>
                            @else
                                <span class="text-xs font-semibold text-red-700 bg-red-50 px-2.5 py-1 rounded-full">● {{ $statusChoices[$order->status] }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ $order->created_at }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if($order->status === 'pending')
                                <form method="post" action="{{ route('admin.orders.mark-paid', $order) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-700 hover:text-emerald-900 hover:scale-105 transition-transform mr-3 inline-block">✅ Marcar como pagado</button>
                                </form>
                                <form method="post" action="{{ route('admin.orders.reject', $order) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800 hover:scale-105 transition-transform inline-block">❌ Rechazar</button>
                                </form>
                            @elseif($order->status === 'paid')
                                <form method="post" action="{{ route('admin.orders.undo-paid', $order) }}" class="inline"
                                      onsubmit="return confirmSubmit(this, '¿Regresar el pedido #{{ $order->id }} a Pendiente y devolver el stock? Úsalo solo si marcaste \'pagado\' por error.', '↩️ Deshacer pago');">
                                    @csrf
                                    <button type="submit" class="text-stone-500 hover:text-stone-800 hover:scale-105 transition-transform inline-block">↩️ Deshacer</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-6 text-center text-stone-500">No hay pedidos.</td></tr>
                @endforelse
            </tbody>
        </table>
      </div>
    </div>
@endsection
