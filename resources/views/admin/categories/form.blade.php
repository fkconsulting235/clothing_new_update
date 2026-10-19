@extends('layouts.admin')

@section('content')
    <h1 class="font-serif text-2xl font-bold text-navy-900 mb-6">{{ $category->exists ? 'Editar categoría' : 'Nueva categoría' }}</h1>

    <form method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="max-w-md bg-white border border-stone-200 rounded-xl p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Orden de aparición</label>
            <input type="number" min="0" name="order" value="{{ old('order', $category->order) }}" class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $category->exists ? $category->is_active : true) ? 'checked' : '' }} class="h-4 w-4 rounded border-stone-300 text-navy-900 focus:ring-gold-500">
            <label for="is_active" class="text-sm text-stone-700">Visible en la tienda</label>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 hover:scale-105 transition-all">💾 Guardar</button>
            <a href="{{ route('admin.categories.index') }}" class="text-sm text-stone-500 hover:text-stone-700">Cancelar</a>
        </div>
    </form>
@endsection
