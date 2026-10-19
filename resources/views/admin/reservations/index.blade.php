@extends('layouts.admin')

@section('content')
    <h1 class="font-serif text-2xl font-bold text-navy-900 mb-6">Reservas</h1>

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
                    <th class="px-4 py-3">Prenda</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Teléfono</th>
                    <th class="px-4 py-3">Cantidad</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Vence</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($reservations as $reservation)
                    <tr class="hover:bg-stone-50 transition-colors">
                        <td class="px-4 py-3 text-navy-900">
                            {{ $reservation->variant->product->name }}
                            <span class="block text-xs text-stone-400">{{ $reservation->variant->sku }}</span>
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ $reservation->customer_name }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $reservation->customer_phone }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $reservation->quantity }}</td>
                        <td class="px-4 py-3">
                            @if($reservation->status === 'active')
                                <span class="text-xs text-emerald-700 bg-emerald-50 px-2 py-1 rounded-full">{{ $statusChoices[$reservation->status] }}</span>
                            @else
                                <span class="text-xs text-stone-500 bg-stone-100 px-2 py-1 rounded-full">{{ $statusChoices[$reservation->status] }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-stone-600">{{ $reservation->expires_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if($reservation->status === 'active')
                                <form method="post" action="{{ route('admin.reservations.cancel', $reservation) }}" class="inline"
                                      onsubmit="return confirmSubmit(this, 'La prenda queda disponible de nuevo para otros clientes. Úsalo si el cliente ya no la quiere o hay un error.', '❌ Cancelar reserva de {{ $reservation->customer_name }}');">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800 hover:scale-105 transition-transform inline-block">❌ Cancelar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-stone-500">No hay reservas.</td></tr>
                @endforelse
            </tbody>
        </table>
      </div>
    </div>
@endsection
