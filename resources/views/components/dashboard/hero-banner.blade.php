{{-- Hero Banner HopeUI Style --}}
<div class="relative overflow-hidden rounded-2xl mb-6 bg-gradient-to-br from-blue-800 via-brand-600 to-blue-700 dark:from-neutral-900 dark:via-neutral-800 dark:to-neutral-900 border border-transparent dark:border-neutral-800/80 shadow-sm">

    {{-- Decorative background shapes --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full opacity-20"
             style="background: radial-gradient(circle, #93c5fd 0%, transparent 70%)"></div>
        <div class="absolute right-32 top-8 w-48 h-48 rounded-full opacity-15"
             style="background: radial-gradient(circle, #bfdbfe 0%, transparent 70%)"></div>
        <div class="absolute right-0 bottom-0 w-64 h-full opacity-10"
             style="background: linear-gradient(135deg, transparent 40%, #1e40af 100%)"></div>
        {{-- Wave decorative element --}}
        <svg class="absolute right-0 bottom-0 h-full opacity-10" viewBox="0 0 300 200" fill="none">
            <path d="M300 0 C200 50, 150 100, 300 200Z" fill="white"/>
            <path d="M300 50 C180 80, 120 130, 300 200Z" fill="white"/>
        </svg>
    </div>

    <div class="relative flex items-center justify-between px-6 py-6">
        <div>
            <h2 class="text-2xl font-bold text-white mb-1">
                ¡Bienvenido a WavePOS! 👋
            </h2>
            <p class="text-blue-200 text-sm max-w-sm">
                Tu sistema de punto de venta inteligente. Aquí tienes el resumen de hoy.
            </p>
        </div>
        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('facturacion.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                      bg-white/20 hover:bg-white/30 text-white border border-white/30
                      backdrop-blur-sm transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Nueva Venta
            </a>
            <a href="{{ route('inventario.productos') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                      bg-white text-blue-700 hover:bg-blue-50
                      transition-all duration-200 shadow-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Inventario
            </a>
        </div>
    </div>
</div>
