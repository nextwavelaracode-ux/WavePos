<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | TailAdmin - Laravel Tailwind CSS Admin Dashboard Template</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
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
                    const body = document.body;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });

            Alpine.store('sidebar', {
                // Initialize based on screen size
                isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover effects on desktop when sidebar is collapsed
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <!-- Apply dark mode immediately to prevent flash -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark', 'bg-gray-900');
            } else {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark', 'bg-gray-900');
            }
        })();
    </script>
    
</head>

<body
    x-data="{ 'loaded': true}"
    x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
    const checkMobile = () => {
        if (window.innerWidth < 1280) {
            $store.sidebar.setMobileOpen(false);
            $store.sidebar.isExpanded = false;
        } else {
            $store.sidebar.isMobileOpen = false;
            $store.sidebar.isExpanded = true;
        }
    };
    window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader/>
    {{-- preloader end --}}

    <div class="min-h-screen bg-neutral-100 dark:bg-neutral-950">

        @include('layouts.sidebar')

        {{-- Main area: shifts when sidebar is open, full-width when hidden --}}
        <div
            class="flex flex-col min-h-screen transition-all duration-300 ease-in-out lg:ml-[260px]"
            :class="{
                'lg:ml-[260px]': $store.sidebar.isExpanded,
                'lg:ml-0'      : !$store.sidebar.isExpanded
            }"
        >
            @include('layouts.app-header')

            <main class="flex-1 p-4 md:p-6 max-w-screen-2xl w-full mx-auto">
                @yield('content')
            </main>
        </div>

    </div>

    <x-ticket-offcanvas />

    {{-- Notiflix Global Notification System --}}
    <script type="module">
        if (typeof window.Notify !== 'undefined') {
            
            // 1. Manejo nativo de notificaciones Notify() desde PHP
            @if(session('notiflix'))
                @php $n = session('notiflix'); @endphp
                const type = '{{ $n['type'] === 'error' ? 'failure' : $n['type'] }}';
                window.Notify[type]('{{ $n['title'] }} {{ $n['message'] ? " - " . $n['message'] : "" }}');
            @endif

            // 2. Errores de Validación (Request Validation)
            @if($errors->any())
                const validationErrors = @json($errors->all());
                validationErrors.forEach(err => {
                    window.Notify.failure(err);
                });
            @endif

            // 3. Fallbacks para redirects antiguos
            @if(session('success'))
                window.Notify.success('{{ session('success') }}');
            @endif

            @if(session('error'))
                window.Notify.failure('{{ session('error') }}');
            @endif

            // 4. Fallback del antiguo sweet_alert
            @if(session('sweet_alert'))
                @php $sa = session('sweet_alert'); @endphp
                const saType = '{{ strtolower($sa['type'] ?? 'success') }}' === 'error' ? 'failure' : '{{ strtolower($sa['type'] ?? 'success') }}';
                window.Notify[saType]('{{ $sa['title'] ?? '' }} {{ $sa['message'] ?? '' }}');
            @endif
        }
    </script>

    {{-- Guardián de Sesión por Expirar --}}
    <script type="module">
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof window.Confirm === 'undefined') return;
            
            // Sesión aproximada (asumiendo 120min). Alertamos a los 115min.
            const LIMITE_MINUTOS = 115; 
            let warningTime = LIMITE_MINUTOS * 60 * 1000;
            let inactivityTimer;

            function startInactivityTimer() {
                clearTimeout(inactivityTimer);
                inactivityTimer = setTimeout(() => {
                    window.Confirm.show(
                        'Sesión a punto de Expirar',
                        'Por seguridad del sistema y privacidad, tu sesión se cerrará en 5 minutos por inactividad.',
                        'Extender Sesión',
                        'Dejar que expire',
                        () => {
                            fetch('/sanctum/csrf-cookie').then(() => {
                                window.Notify.success('Sesión prolongada con éxito. Ya puedes seguir facturando.');
                                startInactivityTimer(); 
                            });
                        },
                        () => {
                            window.location.href = '/logout';
                        },
                        { okButtonBackground: '#3b82f6' }
                    );
                }, warningTime);
            }

            window.addEventListener('load', startInactivityTimer);
            window.addEventListener('mousemove', startInactivityTimer);
            window.addEventListener('click', startInactivityTimer);
            window.addEventListener('keypress', startInactivityTimer);
        });
    </script>
</body>

@stack('scripts')

</html>
