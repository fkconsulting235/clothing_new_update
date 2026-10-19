@extends('layouts.admin')

@section('content')
    <h1 class="font-serif text-2xl font-bold text-navy-900 mb-6">{{ $bankAccount->exists ? 'Editar cuenta bancaria' : 'Nueva cuenta bancaria' }}</h1>

    <form method="post" action="{{ $bankAccount->exists ? route('admin.bank-accounts.update', $bankAccount) : route('admin.bank-accounts.store') }}" class="max-w-md bg-white border border-stone-200 rounded-xl p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Banco</label>
            <input type="text" name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" placeholder="Ej. BAC Credomatic" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Tipo de cuenta</label>
            <input type="text" name="account_type" value="{{ old('account_type', $bankAccount->account_type) }}" placeholder="Ej. Cuenta de ahorro" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Número de cuenta</label>
            <input type="text" name="account_number" value="{{ old('account_number', $bankAccount->account_number) }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">A nombre de</label>
            <input type="text" name="holder_name" value="{{ old('holder_name', $bankAccount->holder_name) }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Orden de aparición</label>
            <input type="number" min="0" name="order" value="{{ old('order', $bankAccount->order) }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $bankAccount->exists ? $bankAccount->is_active : true) ? 'checked' : '' }} class="h-4 w-4 rounded border-stone-300 text-navy-900 focus:ring-gold-500">
            <label for="is_active" class="text-sm text-stone-700">Visible en el checkout</label>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-105 transition-all">💾 Guardar</button>
            <a href="{{ route('admin.bank-accounts.index') }}" class="text-sm text-stone-500 hover:text-stone-700">Cancelar</a>
        </div>
    </form>
@endsection
