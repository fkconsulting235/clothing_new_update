@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white border border-stone-200 rounded-xl p-6">
        <p class="text-xs uppercase tracking-wide text-stone-400 mb-1">{{ $variant->product->code }} · {{ $variant->color }}/{{ $variant->size }}</p>
        <h1 class="font-serif text-xl font-bold text-navy-900 mb-1">Reservar {{ $variant->product->name }}</h1>
        <p class="text-sm text-stone-600 mb-6">Tienes {{ config('store.reservation_hours') }} horas para pasar por la prenda o pagarla en línea.</p>

        <form method="post" action="{{ route('reservations.create', $variant) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Nombre completo</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required placeholder="Tu nombre completo"
                       class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Teléfono / WhatsApp</label>
                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required placeholder="Tu número de WhatsApp"
                       class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Cantidad</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}"
                       class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <button type="submit" class="w-full px-4 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-[1.02] transition-all">Confirmar reserva</button>
        </form>
    </div>
@endsection
