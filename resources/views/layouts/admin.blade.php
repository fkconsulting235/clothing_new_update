<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel · '.config('store.name'))</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        serif: ['Playfair Display', 'ui-serif', 'Georgia', 'serif'],
                    },
                    colors: {
                        cream: '#faf9f5',
                        navy: { 900: '#0f1629', 800: '#182038', 700: '#232c48' },
                        gold: { 600: '#b8860b', 500: '#c9961f', 400: '#d4a62e' },
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen flex flex-col bg-cream text-navy-900 font-sans antialiased overflow-x-hidden">

<header class="bg-navy-900 text-stone-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.home') }}" class="flex items-center gap-2">
                <img src="{{ asset('img/favicon.png') }}" alt="" class="h-8 w-8 rounded">
                <span class="font-serif text-lg font-bold text-white">Panel</span>
            </a>
            <span class="text-stone-500 text-sm">· {{ config('store.name') }}</span>
        </div>
        <nav class="flex items-center gap-1 text-sm text-stone-300 w-full sm:w-auto min-w-0 overflow-x-auto whitespace-nowrap -mx-4 px-4 sm:mx-0 sm:px-0">
            <a href="{{ route('admin.home') }}" class="px-3 py-1.5 rounded-full transition-colors {{ request()->routeIs('admin.home') ? 'bg-navy-800 text-white' : 'hover:text-white hover:bg-navy-800/60' }}">🏠 Inicio</a>
            <a href="{{ route('admin.products.index') }}" class="px-3 py-1.5 rounded-full transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-navy-800 text-white' : 'hover:text-white hover:bg-navy-800/60' }}">👕 Prendas</a>
            <a href="{{ route('admin.categories.index') }}" class="px-3 py-1.5 rounded-full transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-navy-800 text-white' : 'hover:text-white hover:bg-navy-800/60' }}">🏷️ Categorías</a>
            <a href="{{ route('admin.reservations.index') }}" class="px-3 py-1.5 rounded-full transition-colors {{ request()->routeIs('admin.reservations.*') ? 'bg-navy-800 text-white' : 'hover:text-white hover:bg-navy-800/60' }}">⏱ Reservas</a>
            <a href="{{ route('admin.sales') }}" class="px-3 py-1.5 rounded-full transition-colors {{ request()->routeIs('admin.sales') ? 'bg-navy-800 text-white' : 'hover:text-white hover:bg-navy-800/60' }}">💰 Ventas</a>
            <a href="{{ route('admin.orders.index') }}" class="px-3 py-1.5 rounded-full transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-navy-800 text-white' : 'hover:text-white hover:bg-navy-800/60' }}">📦 Pedidos
                @php($pendingBadge = \App\Models\Order::where('status', 'pending')->whereNotNull('payment_proof_path')->count())
                @if($pendingBadge > 0)<span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-gold-500 text-navy-900 text-[11px] font-bold">{{ $pendingBadge }}</span>@endif
            </a>
            <a href="{{ route('admin.bank-accounts.index') }}" class="px-3 py-1.5 rounded-full transition-colors {{ request()->routeIs('admin.bank-accounts.*') ? 'bg-navy-800 text-white' : 'hover:text-white hover:bg-navy-800/60' }}">🏦 Cuentas</a>
        </nav>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('catalog.home') }}" target="_blank" rel="noopener" class="text-stone-300 hover:text-white transition-colors">Ver tienda</a>
            <form method="post" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-stone-300 hover:text-white transition-colors">Cerrar sesión</button>
            </form>
        </div>
    </div>
</header>

@if(session('success') || session('error'))
    <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 pt-4">
        @if(session('success'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 mb-2">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 mb-2">{{ session('error') }}</div>
        @endif
    </div>
@endif
@if ($errors->any())
    <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 pt-4">
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
@endif

<main class="flex-1 max-w-6xl mx-auto w-full px-4 sm:px-6 py-8">
    @yield('content')
</main>

<div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-navy-900/60 px-4">
    <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
        <div class="flex items-start gap-3 mb-5">
            <span class="text-2xl leading-none">⚠️</span>
            <div>
                <h2 id="confirm-modal-title" class="text-base font-semibold text-navy-900 mb-1">¿Confirmar acción?</h2>
                <p id="confirm-modal-message" class="text-sm text-stone-600 leading-relaxed"></p>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 rounded-md text-sm font-medium text-stone-600 hover:bg-stone-100 transition-colors">Cancelar</button>
            <button type="button" id="confirm-modal-accept" class="px-4 py-2 rounded-md bg-navy-900 text-white text-sm font-medium hover:bg-navy-800 transition-colors">Sí, continuar</button>
        </div>
    </div>
</div>

<script>
    let confirmModalForm = null;

    function confirmSubmit(form, message, title) {
        confirmModalForm = form;
        document.getElementById('confirm-modal-message').textContent = message;
        document.getElementById('confirm-modal-title').textContent = title || '¿Confirmar acción?';
        const modal = document.getElementById('confirm-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        return false;
    }

    function closeConfirmModal() {
        const modal = document.getElementById('confirm-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        confirmModalForm = null;
    }

    document.getElementById('confirm-modal-accept').addEventListener('click', function () {
        if (confirmModalForm) {
            const form = confirmModalForm;
            confirmModalForm = null;
            closeConfirmModal();
            form.submit();
        }
    });

    document.getElementById('confirm-modal').addEventListener('click', function (e) {
        if (e.target === this) closeConfirmModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeConfirmModal();
    });
</script>

@stack('scripts')
</body>
</html>
