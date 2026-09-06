<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | TailAdmin - Laravel Tailwind CSS Admin Dashboard Template</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js x-cloak helper to eliminate FOUC -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

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
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        if (document.body) document.body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        if (document.body) document.body.classList.remove('dark', 'bg-gray-900');
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

    <!-- Apply dark mode immediately to prevent flash (FOUC) -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme');
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                const theme = savedTheme || systemTheme;
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}

            // Pre-calculate sidebar margin before Alpine boots to prevent layout flash
            document.addEventListener('DOMContentLoaded', function() {
                const mainContent = document.getElementById('main-content-wrapper');
                if (mainContent && window.innerWidth >= 1280) {
                    mainContent.style.marginLeft = '260px';
                }
            });
        })();
    </script>
    
</head>

<body
    x-data="{}"
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



    <div class="min-h-screen bg-neutral-100 dark:bg-neutral-950">

        @include('layouts.sidebar')

        {{-- Main area: shifts when sidebar is open, full-width when hidden --}}
        <div
            id="main-content-wrapper"
            class="flex flex-col min-h-screen transition-[margin-left] duration-300 ease-in-out"
            x-effect="$el.style.marginLeft = (window.innerWidth >= 1280 && $store.sidebar.isExpanded) ? '260px' : '0px'"
        >
            @include('layouts.app-header')

            <main class="flex-1 p-4 md:p-6 max-w-screen-2xl w-full mx-auto">
                @yield('content')
            </main>
        </div>

    </div>

    <x-ticket-offcanvas />

    {{-- Notiflix Global Notification System --}}
    <script>
        (function showSessionNotifications() {
            function tryNotify() {
                if (typeof window.Notify === 'undefined') {
                    // Retry on next frame if Notiflix hasn't loaded yet
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

                @if($errors->any())
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

                @if(session('sweet_alert'))
                    @php $sa = session('sweet_alert'); @endphp
                    (function() {
                        const saType = '{{ strtolower($sa['type'] ?? 'success') }}' === 'error' ? 'failure' : '{{ strtolower($sa['type'] ?? 'success') }}';
                        window.Notify[saType]('{{ addslashes(($sa['title'] ?? '') . ($sa['message'] ? ' ' . $sa['message'] : '')) }}');
                    })();
                @endif
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', tryNotify);
            } else {
                tryNotify();
            }
        })();
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
    @stack('scripts')

</body>

</html>
