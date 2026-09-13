<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'WavePOS' }} | Punto de Venta</title>
    <link rel="icon" type="image/png" href="/images/logo/logotipohd.png">

    <!-- Fonts & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        html.setAttribute('data-theme', 'dark');
                    } else {
                        html.classList.remove('dark');
                        html.setAttribute('data-theme', 'light');
                    }
                }
            });
        });
    </script>

    <!-- Apply dark mode immediately to prevent flash (FOUC) -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme');
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                const theme = savedTheme || systemTheme;
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>
</head>

<body class="h-screen w-screen overflow-hidden bg-slate-100/80 dark:bg-[#0d121d] text-slate-800 dark:text-slate-100 font-sans antialiased selection:bg-brand-500 selection:text-white" x-data="{}">

    <div class="flex h-screen w-screen overflow-hidden">

        {{-- ── SLIM ICON NAVIGATION RAIL (Inspired by Starline & CHILI POS) ── --}}
        <aside class="hidden md:flex flex-col items-center justify-between w-16 lg:w-[70px] bg-white/95 dark:bg-[#131b2c]/95 border-r border-slate-200/80 dark:border-slate-800/80 py-3.5 z-30 shrink-0 backdrop-blur-md shadow-sm transition-all duration-300">
            
            {{-- Top: Logo & Main Navigation --}}
            <div class="flex flex-col items-center gap-4 w-full">
                {{-- Brand Icon --}}
                <a href="{{ url('/') }}" class="group relative flex items-center justify-center w-11 h-11 rounded-2xl bg-gradient-to-tr from-brand-600 to-brand-400 text-white shadow-md shadow-brand-500/25 transition-transform duration-200 hover:scale-105 active:scale-95" title="Ir al Dashboard">
                    <img src="/images/logo/logotipohd.png" alt="WavePOS" class="h-7 w-7 object-contain drop-shadow-sm" />
                </a>

                {{-- Navigation Divider --}}
                <div class="w-8 h-[1px] bg-slate-200 dark:bg-slate-800 my-1"></div>

                {{-- Navigation Links --}}
                <nav class="flex flex-col items-center gap-2 w-full px-2">
                    {{-- Dashboard --}}
                    <a href="{{ url('/') }}" 
                       class="group relative flex items-center justify-center w-11 h-11 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-brand-400 dark:hover:bg-slate-800/60 transition-all duration-200"
                       title="Dashboard">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-xs font-semibold rounded-md shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">Dashboard</span>
                    </a>

                    {{-- POS (Active) --}}
                    <a href="{{ route('caja.pos') }}" 
                       class="group relative flex items-center justify-center w-11 h-11 rounded-xl bg-brand-500 text-white shadow-md shadow-brand-500/30 transition-all duration-200"
                       title="Punto de Venta">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span class="absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-xs font-semibold rounded-md shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">Punto de Venta</span>
                    </a>

                    {{-- Historial de Ventas --}}
                    <a href="{{ route('caja.ventas.historial') }}" 
                       class="group relative flex items-center justify-center w-11 h-11 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-brand-400 dark:hover:bg-slate-800/60 transition-all duration-200"
                       title="Historial de Ventas">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span class="absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-xs font-semibold rounded-md shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">Ventas Realizadas</span>
                    </a>

                    {{-- Inventario / Productos --}}
                    <a href="{{ route('inventario.productos') }}" 
                       class="group relative flex items-center justify-center w-11 h-11 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-brand-400 dark:hover:bg-slate-800/60 transition-all duration-200"
                       title="Productos & Inventario">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span class="absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-xs font-semibold rounded-md shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">Inventario</span>
                    </a>

                    {{-- Clientes --}}
                    <a href="{{ route('clientes.index') }}" 
                       class="group relative flex items-center justify-center w-11 h-11 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-brand-400 dark:hover:bg-slate-800/60 transition-all duration-200"
                       title="Clientes">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-xs font-semibold rounded-md shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">Clientes</span>
                    </a>

                    {{-- Control de Caja --}}
                    <a href="{{ route('caja.index') }}" 
                       class="group relative flex items-center justify-center w-11 h-11 rounded-xl text-slate-500 hover:text-brand-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-brand-400 dark:hover:bg-slate-800/60 transition-all duration-200"
                       title="Control de Caja">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-xs font-semibold rounded-md shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">Caja & Turnos</span>
                    </a>
                </nav>
            </div>

            {{-- Bottom: Theme & User / Exit --}}
            <div class="flex flex-col items-center gap-3 w-full px-2">
                {{-- Theme Switcher --}}
                <button @click="$store.theme.toggle()" 
                        class="group relative flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:text-amber-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-amber-400 dark:hover:bg-slate-800/60 transition-all duration-200"
                        title="Cambiar tema">
                    <template x-if="$store.theme.theme === 'dark'">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </template>
                    <template x-if="$store.theme.theme !== 'dark'">
                        <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </template>
                    <span class="absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-xs font-semibold rounded-md shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">Modo Claro / Oscuro</span>
                </button>

                {{-- User Avatar / Logout --}}
                <form action="{{ route('logout') }}" method="POST" class="w-full flex justify-center">
                    @csrf
                    <button type="submit" 
                            class="group relative flex items-center justify-center w-10 h-10 rounded-xl text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-all duration-200"
                            title="Cerrar sesión">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="absolute left-full ml-3 px-2 py-1 bg-red-600 text-white text-xs font-semibold rounded-md shadow-lg opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap">Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ── MAIN POS WORKSPACE ── --}}
        <main class="flex-1 min-w-0 h-screen overflow-hidden flex flex-col">
            @yield('content')
        </main>

    </div>

    {{-- Offcanvas de Impresión de Ticket --}}
    <x-ticket-offcanvas />

    {{-- Notiflix Global Notification System --}}
    <script>
        (function showSessionNotifications() {
            function tryNotify() {
                if (typeof window.Notify === 'undefined') {
                    requestAnimationFrame(tryNotify);
                    return;
                }

                @if(session('notiflix'))
                    @php $n = session('notiflix'); @endphp
                    (function() {
                        const type = '{{ $n['type'] === 'error' ? 'failure' : $n['type'] }}';
                        window.Notify[type]('{{ $n['title'] }}{{ $n['message'] ? " - " . addslashes($n['message']) : "" }}');
                    })();
                @endif

                @if(isset($errors) && $errors->any())
                    (function() {
                        const validationErrors = @json($errors->all());
                        validationErrors.forEach(function(err) {
                            window.Notify.failure(err);
                        });
                    })();
                @endif

                @if(session('success'))
                    window.Notify.success('{{ addslashes(session('success')) }}');
                @endif

                @if(session('error'))
                    window.Notify.failure('{{ addslashes(session('error')) }}');
                @endif
            }
            tryNotify();
        })();
    </script>

    @stack('scripts')

</body>
</html>
