@extends('layouts.admin')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-2">
        <h1 class="font-serif text-2xl font-bold text-navy-900">Cuentas bancarias</h1>
        <a href="{{ route('admin.bank-accounts.create') }}" class="px-4 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-105 transition-all">+ Nueva cuenta</a>
    </div>
    <p class="text-sm text-stone-500 mb-6">Estas son las cuentas que ve el cliente al pagar por depósito/transferencia. Solo las "Visibles" aparecen en el checkout.</p>

    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-left text-xs text-stone-500 uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3">Banco</th>
                    <th class="px-4 py-3">Cuenta</th>
                    <th class="px-4 py-3">A nombre de</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($bankAccounts as $account)
                    <tr class="hover:bg-stone-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-navy-900">{{ $account->bank_name }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $account->account_number }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $account->holder_name }}</td>
                        <td class="px-4 py-3">
                            @if($account->is_active)
                                <span class="text-xs text-emerald-700 bg-emerald-50 px-2 py-1 rounded-full">Visible</span>
                            @else
                                <span class="text-xs text-stone-500 bg-stone-100 px-2 py-1 rounded-full">Oculta</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.bank-accounts.edit', $account) }}" class="text-stone-600 hover:text-navy-900 mr-3">✏️ Editar</a>
                            <a href="{{ route('admin.bank-accounts.confirm-delete', $account) }}" class="text-red-600 hover:text-red-800">🗑️ Eliminar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-stone-500">Todavía no hay cuentas bancarias.</td></tr>
                @endforelse
            </tbody>
        </table>
      </div>
    </div>
@endsection
