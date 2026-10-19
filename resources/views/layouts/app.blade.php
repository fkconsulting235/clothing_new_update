<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('store.name'))</title>
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
    @stack('head')
</head>
<body class="min-h-screen flex flex-col bg-cream text-navy-900 font-sans antialiased overflow-x-hidden">

<div class="bg-gold-500 text-navy-900 text-[11px] sm:text-xs font-semibold text-center py-2 px-3 tracking-wide">
    ✨ Piezas únicas · Apártala 24 horas sin pagar · Paga por transferencia
</div>

<header class="bg-white border-b border-stone-200 sticky top-0 z-40 shadow-sm">
    <div class="bg-navy-900 text-stone-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
            <a href="{{ route('catalog.home') }}" class="flex items-center gap-3" aria-label="{{ config('store.name') }}">
                <img src="{{ asset('img/logo.png') }}" alt="{{ config('store.name') }}" class="h-16 w-auto rounded-lg ring-1 ring-gold-500/40">
            </a>

            <form method="get" action="{{ route('catalog.search') }}" class="hidden sm:block flex-1 max-w-sm">
                <input type="search" name="q" placeholder="Buscar prendas..." value="{{ request('q') }}"
                       class="w-full rounded-full border-0 bg-navy-800 px-4 py-2 text-sm text-white placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-gold-500">
            </form>

            <div class="flex items-center gap-4 text-xs">
                @if(config('store.whatsapp_number'))
                    <a href="https://wa.me/{{ config('store.whatsapp_number') }}" target="_blank" rel="noopener"
                       class="hidden sm:inline-flex items-center gap-1.5 text-stone-300 hover:text-white transition-colors">
                        Escríbenos por WhatsApp
                    </a>
                @endif
                <a href="{{ route('orders.cart') }}" class="inline-flex items-center gap-1.5 text-stone-300 hover:text-white transition-colors">
                    🛒 Carrito
                    @if(app(\App\Services\Cart::class)->count())
                        <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-gold-500 text-navy-900 text-[11px] font-semibold">{{ app(\App\Services\Cart::class)->count() }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
    <div class="sm:hidden max-w-6xl mx-auto px-4 pt-3">
        <form method="get" action="{{ route('catalog.search') }}">
            <input type="search" name="q" placeholder="Buscar prendas..." value="{{ request('q') }}"
                   class="w-full rounded-full border border-stone-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
        </form>
    </div>

    <nav class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center gap-6 overflow-x-auto py-3 text-xs font-semibold uppercase tracking-wider text-stone-600">
            <a href="{{ route('catalog.by-size') }}" class="whitespace-nowrap text-navy-900 hover:text-gold-600 transition-colors">📏 Por talla</a>
            @foreach(($navCategories ?? \App\Models\Category::where('is_active', true)->orderBy('order')->get()) as $category)
                <a href="{{ route('catalog.category', $category) }}"
                   class="whitespace-nowrap pb-1 border-b-2 transition-colors {{ (isset($currentCategory) && $currentCategory->id === $category->id) ? 'border-gold-500 text-navy-900' : 'border-transparent hover:text-navy-900' }}">{{ $category->name }}</a>
            @endforeach
        </div>
    </nav>
</header>

@if(session('success'))
    <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 pt-4">
        <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    </div>
@endif
@if(session('error'))
    <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 pt-4">
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
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

<footer class="bg-navy-900 text-stone-400 mt-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 grid sm:grid-cols-3 gap-8 text-sm">
        <div>
            <img src="{{ asset('img/logo.png') }}" alt="{{ config('store.name') }}" class="h-16 w-auto rounded-lg ring-1 ring-gold-500/40 mb-3">
            <p class="text-stone-400 leading-relaxed">Ropa seleccionada pieza por pieza. Apártala, págala por transferencia y recíbela en tu casa.</p>
        </div>
        <div>
            <p class="text-white font-semibold mb-3">Compra</p>
            <ul class="space-y-2">
                <li><a href="{{ route('catalog.by-size') }}" class="hover:text-gold-400 transition-colors">📏 Buscar por talla</a></li>
                <li><a href="{{ route('orders.cart') }}" class="hover:text-gold-400 transition-colors">🛒 Mi carrito</a></li>
                <li><a href="{{ route('reservations.lookup') }}" class="hover:text-gold-400 transition-colors">📋 Consultar mi reserva</a></li>
            </ul>
        </div>
        <div>
            <p class="text-white font-semibold mb-3">¿Dudas?</p>
            @if(config('store.whatsapp_number'))
                <a href="https://wa.me/{{ config('store.whatsapp_number') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#25D366] text-white text-xs font-semibold hover:bg-[#1ebe5a] transition-colors">💬 Escríbenos por WhatsApp</a>
            @endif
            <p class="mt-3 text-xs">Respondemos lo antes posible.</p>
        </div>
    </div>
    <div class="border-t border-white/10 py-4 text-center text-xs text-stone-500">
        &copy; {{ date('Y') }} {{ config('store.name') }}. Todos los derechos reservados.
    </div>
</footer>

@if(config('store.whatsapp_number'))
    <a href="https://wa.me/{{ config('store.whatsapp_number') }}" target="_blank" rel="noopener" aria-label="Escríbenos por WhatsApp"
       class="fixed bottom-5 right-5 z-50 flex items-center justify-center w-14 h-14 rounded-full bg-[#25D366] text-2xl shadow-lg hover:scale-110 transition-transform">
        💬
    </a>
@endif

@stack('scripts')
</body>
</html>
