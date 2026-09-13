<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'WavePOS' }} | Sistema de Punto de Venta</title>
    <link rel="icon" type="image/png" href="/images/logo/logotipohd.png">

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
                        html.setAttribute('data-theme', 'dark');
                    } else {
                        html.classList.remove('dark');
                        html.setAttribute('data-theme', 'light');
                    }
                }
            });

            Alpine.store('sidebar', {
                isExpanded: window.innerWidth >= 1024,
                isMobileOpen: false,
                isHovered: false,
                toggleExpanded() { this.isExpanded = !this.isExpanded; },
                toggleMobileOpen() { this.isMobileOpen = !this.isMobileOpen; },
                setMobileOpen(val) { this.isMobileOpen = val; },
                setHovered(val) { this.isHovered = val; }
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

<body x-data="{}">

    <div class="min-h-screen bg-base-100 text-base-content relative">

        @include('layouts.sidebar')

        {{-- Main area: shifts when FlyonUI collapsible sidebar is open --}}
        <div class="sm:overlay-layout-open:ps-64 min-h-screen flex flex-col transition-all duration-300">
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
