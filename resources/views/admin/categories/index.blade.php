@extends('layouts.admin')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h1 class="font-serif text-2xl font-bold text-navy-900">Categorías</h1>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-105 transition-all">+ Nueva categoría</a>
    </div>

    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-left text-xs text-stone-500 uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Orden</th>
                    <th class="px-4 py-3">Prendas</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-stone-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-navy-900">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $category->order }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $category->products_count }}</td>
                        <td class="px-4 py-3">
                            @if($category->is_active)
                                <span class="text-xs text-emerald-700 bg-emerald-50 px-2 py-1 rounded-full">Visible</span>
                            @else
                                <span class="text-xs text-stone-500 bg-stone-100 px-2 py-1 rounded-full">Oculta</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-stone-600 hover:text-navy-900 mr-3">✏️ Editar</a>
                            <a href="{{ route('admin.categories.confirm-delete', $category) }}" class="text-red-600 hover:text-red-800">🗑️ Eliminar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-stone-500">Todavía no hay categorías.</td></tr>
                @endforelse
            </tbody>
        </table>
      </div>
    </div>
@endsection
