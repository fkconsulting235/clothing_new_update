@extends('layouts.admin')

@section('content')
    @php
        $cur = config('store.currency_symbol');
        $maxWeek = max($week->max('total'), 1);
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Buenos días' : ($hour < 19 ? 'Buenas tardes' : 'Buenas noches');
    @endphp

    <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
        <div>
            <p class="text-gold-600 text-[11px] font-semibold uppercase tracking-[0.25em]">{{ now()->translatedFormat('l j \d\e F') }}</p>
            <h1 class="font-serif text-3xl font-bold text-navy-900">{{ $greeting }} 👋</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2.5 rounded-xl bg-navy-900 text-white text-sm font-semibold hover:bg-navy-800 hover:-translate-y-0.5 transition-all">+ Nueva prenda</a>
            <a href="{{ route('admin.categories.create') }}" class="px-4 py-2.5 rounded-xl border border-stone-300 bg-white text-navy-900 text-sm font-semibold hover:bg-stone-50 transition-colors">+ Categoría</a>
        </div>
    </div>

    {{-- ALERTA PRINCIPAL --}}
    @if($pendingProofCount > 0)
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="group flex items-center justify-between gap-4 rounded-2xl bg-navy-900 px-5 sm:px-6 py-5 mb-6 hover:shadow-xl hover:shadow-navy-900/20 transition-all">
            <div class="flex items-center gap-4">
                <span class="w-12 h-12 rounded-full bg-gold-500 text-navy-900 flex items-center justify-center text-2xl shrink-0 animate-pulse">🧾</span>
                <div>
                    <p class="text-white font-semibold text-lg leading-tight">{{ $pendingProofCount }} {{ $pendingProofCount === 1 ? 'comprobante por revisar' : 'comprobantes por revisar' }}</p>
                    <p class="text-stone-400 text-sm">Un cliente ya pagó y está esperando tu confirmación.</p>
                </div>
            </div>
            <span class="text-gold-400 text-2xl group-hover:translate-x-1 transition-transform">→</span>
        </a>
    @else
        <div class="flex items-center gap-4 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 sm:px-6 py-4 mb-6">
            <span class="text-2xl">✅</span>
            <p class="text-emerald-800 text-sm font-medium">Todo al día: no hay comprobantes pendientes por revisar.</p>
        </div>
    @endif

    {{-- VENTAS --}}
    <div class="grid grid-cols-3 gap-2 sm:gap-4 mb-6">
        <div class="bg-white border border-stone-200 rounded-2xl p-3 sm:p-5 min-w-0">
            <p class="text-xs text-stone-500 mb-1">☀️ Hoy</p>
            <p class="text-base sm:text-2xl font-bold text-navy-900 truncate">{{ $cur }}{{ store_money($salesToday) }}</p>
        </div>
        <div class="bg-white border border-stone-200 rounded-2xl p-3 sm:p-5 min-w-0">
            <p class="text-xs text-stone-500 mb-1">📅 Este mes</p>
            <p class="text-base sm:text-2xl font-bold text-navy-900 truncate">{{ $cur }}{{ store_money($salesMonth) }}</p>
        </div>
        <div class="bg-gradient-to-br from-navy-900 to-navy-700 rounded-2xl p-3 sm:p-5 min-w-0">
            <p class="text-xs text-stone-400 mb-1">🏆 Total</p>
            <p class="text-base sm:text-2xl font-bold text-white truncate">{{ $cur }}{{ store_money($salesTotal) }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        {{-- PEDIDOS PENDIENTES --}}
        <section class="lg:col-span-2 bg-white border border-stone-200 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
                <h2 class="font-semibold text-navy-900">🧾 Pedidos por confirmar</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-stone-500 hover:text-navy-900">Ver todos →</a>
            </div>
            @forelse($pendingOrders as $order)
                <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-stone-100 last:border-0">
                    <div class="flex-1 min-w-[10rem]">
                        <p class="text-sm font-semibold text-navy-900">#{{ $order->id }} · {{ $order->customer_name }}</p>
                        <p class="text-xs text-stone-500">
                            @foreach($order->items as $item){{ $item->quantity }}× {{ $item->variant->product->name }} ({{ $item->variant->size }})@if(!$loop->last), @endif @endforeach
                        </p>
                        <p class="text-xs text-stone-400">{{ $order->customer_phone }} · {{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <p class="text-base font-bold text-navy-900">{{ $cur }}{{ store_money($order->total) }}</p>
                    <div class="flex items-center gap-2">
                        @if($order->payment_proof_path)
                            <a href="{{ $order->payment_proof_url }}" target="_blank" rel="noopener" class="px-3 py-2 rounded-lg border border-stone-300 text-xs font-semibold text-navy-900 hover:bg-stone-50">🖼️ Comprobante</a>
                        @endif
                        <form method="post" action="{{ route('admin.orders.mark-paid', $order) }}">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors">✅ Confirmar</button>
                        </form>
                        <form method="post" action="{{ route('admin.orders.reject', $order) }}"
                              onsubmit="return confirmSubmit(this, 'El pedido #{{ $order->id }} quedará cancelado. Úsalo si el comprobante no es válido.', '❌ Rechazar pedido');">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-lg border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 transition-colors">Rechazar</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="px-5 py-10 text-center text-sm text-stone-400">No hay pedidos pendientes con comprobante. 🎉</p>
            @endforelse
        </section>

        {{-- VENTAS 7 DIAS --}}
        <section class="bg-white border border-stone-200 rounded-2xl p-5">
            <h2 class="font-semibold text-navy-900 mb-4">📈 Ventas de la semana</h2>
            <div class="flex items-end justify-between gap-2 h-36">
                @foreach($week as $day)
                    <div class="flex-1 flex flex-col items-center justify-end h-full gap-1" title="{{ $cur }}{{ store_money($day['total']) }}">
                        <div class="w-full rounded-t-md {{ $day['total'] > 0 ? 'bg-gold-500' : 'bg-stone-200' }}" style="height: {{ max(($day['total'] / $maxWeek) * 100, 4) }}%"></div>
                        <span class="text-[10px] text-stone-400 uppercase">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-stone-400 mt-3">Total semana: <strong class="text-navy-900">{{ $cur }}{{ store_money($week->sum('total')) }}</strong></p>
        </section>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        {{-- RESERVAS POR VENCER --}}
        <section class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
                <h2 class="font-semibold text-navy-900">⏱ Reservas por vencer</h2>
                <a href="{{ route('admin.reservations.index', ['status' => 'active']) }}" class="text-xs text-stone-500 hover:text-navy-900">Ver reservas →</a>
            </div>
            @forelse($expiringReservations as $r)
                <div class="flex items-center justify-between gap-3 px-5 py-3 border-b border-stone-100 last:border-0">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-navy-900 truncate">{{ $r->variant->product->name }} · {{ $r->variant->size }}</p>
                        <p class="text-xs text-stone-500">{{ $r->customer_name }} · {{ $r->customer_phone }}</p>
                    </div>
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 shrink-0">vence {{ $r->expires_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="px-5 py-8 text-center text-sm text-stone-400">Ninguna reserva vence en las próximas 6 horas.</p>
            @endforelse
        </section>

        {{-- STOCK BAJO --}}
        <section class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
                <h2 class="font-semibold text-navy-900">📦 Stock bajo</h2>
                <a href="{{ route('admin.products.index') }}" class="text-xs text-stone-500 hover:text-navy-900">Ver prendas →</a>
            </div>
            @forelse($lowStock as $v)
                <a href="{{ route('admin.products.edit', $v->product) }}" class="flex items-center justify-between gap-3 px-5 py-3 border-b border-stone-100 last:border-0 hover:bg-stone-50 transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-navy-900 truncate">{{ $v->product->name }}</p>
                        <p class="text-xs text-stone-500">{{ $v->color }} · {{ $v->size }}</p>
                    </div>
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full shrink-0 {{ $v->stock === 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800' }}">
                        {{ $v->stock === 0 ? 'Agotada' : ($v->stock === 1 ? 'Queda 1' : 'Quedan '.$v->stock) }}
                    </span>
                </a>
            @empty
                <p class="px-5 py-8 text-center text-sm text-stone-400">Todo el inventario está bien abastecido.</p>
            @endforelse
        </section>
    </div>

    {{-- RESUMEN --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <a href="{{ route('admin.products.index') }}" class="bg-white border border-stone-200 rounded-2xl p-4 hover:shadow-md hover:-translate-y-0.5 transition-all">
            <p class="text-xs text-stone-500 mb-1">👕 Prendas publicadas</p><p class="text-2xl font-bold text-navy-900">{{ $totalProducts }}</p>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="bg-white border border-stone-200 rounded-2xl p-4 hover:shadow-md hover:-translate-y-0.5 transition-all">
            <p class="text-xs text-stone-500 mb-1">🏷️ Categorías</p><p class="text-2xl font-bold text-navy-900">{{ $totalCategories }}</p>
        </a>
        <a href="{{ route('admin.reservations.index') }}" class="bg-white border border-stone-200 rounded-2xl p-4 hover:shadow-md hover:-translate-y-0.5 transition-all">
            <p class="text-xs text-stone-500 mb-1">⏱ Reservas activas</p><p class="text-2xl font-bold text-navy-900">{{ $activeReservations }}</p>
        </a>
        <a href="{{ route('admin.sales') }}" class="bg-white border border-stone-200 rounded-2xl p-4 hover:shadow-md hover:-translate-y-0.5 transition-all">
            <p class="text-xs text-stone-500 mb-1">✅ Pedidos pagados</p><p class="text-2xl font-bold text-navy-900">{{ $paidOrdersCount }}</p>
        </a>
    </div>
@endsection
