@extends('layouts.admin')

@section('content')
    <div class="max-w-md bg-white border border-stone-200 rounded-xl p-6">
        <h1 class="text-lg font-semibold text-navy-900 mb-2">⚠️ ¿Eliminar esta cuenta?</h1>
        <p class="text-sm text-stone-600 mb-6"><strong>{{ $bankAccount->bank_name }} · {{ $bankAccount->account_number }}</strong> se eliminará. Esta acción no se puede deshacer.</p>
        <form method="post" action="{{ route('admin.bank-accounts.destroy', $bankAccount) }}" class="flex items-center gap-3">
            @csrf
            <button type="submit" class="px-4 py-2.5 rounded-md bg-red-600 text-white text-sm font-medium hover:bg-red-700 hover:scale-105 transition-all">Sí, eliminar</button>
            <a href="{{ route('admin.bank-accounts.index') }}" class="text-sm text-stone-500 hover:text-stone-700">Cancelar</a>
        </form>
    </div>
@endsection
