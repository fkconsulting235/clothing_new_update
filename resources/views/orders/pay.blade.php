@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white border border-stone-200 rounded-xl p-6">
        <p class="text-xs uppercase tracking-wide text-stone-400 mb-1">Pedido #{{ $order->id }}</p>
        <h1 class="font-serif text-xl font-bold text-navy-900 mb-1">Total a pagar: {{ config('store.currency_symbol') }}{{ store_money($order->total) }}</h1>
        <p class="text-sm text-stone-600 mb-6">Haz tu depósito o transferencia a una de estas cuentas y sube la foto o captura del comprobante. Validaremos tu pago y te contactaremos por WhatsApp.</p>

        @if($bankAccounts->isNotEmpty())
            <div class="space-y-3 mb-6">
                @foreach($bankAccounts as $account)
                    <div class="border border-stone-200 rounded-lg px-4 py-3">
                        <p class="text-sm font-semibold text-navy-900">{{ $account->bank_name }}</p>
                        @if($account->account_type)
                            <p class="text-xs text-stone-500">{{ $account->account_type }}</p>
                        @endif
                        <p class="text-sm text-stone-700 mt-1">Cuenta: {{ $account->account_number }}</p>
                        <p class="text-xs text-stone-500">A nombre de {{ $account->holder_name }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-md px-4 py-3 mb-6">Todavía no hay cuentas bancarias configuradas. Escríbenos por WhatsApp para coordinar el pago.</p>
        @endif

        <form method="post" action="{{ route('orders.pay', $order) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Foto del comprobante</label>
                <input type="file" name="payment_proof" accept="image/*" required class="text-sm text-stone-700">
            </div>
            <button type="submit" class="w-full px-4 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-[1.02] transition-all">Subir comprobante</button>
        </form>
    </div>
@endsection
