{{-- Hero Banner – Finanzas (Blue/Primary Theme) --}}
<div class="relative overflow-hidden rounded-2xl mb-6 bg-gradient-to-br from-blue-800 via-blue-700 to-blue-600 dark:from-neutral-900 dark:via-neutral-800 dark:to-neutral-900 border border-transparent dark:border-neutral-800/80 shadow-sm">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full opacity-20"
             style="background: radial-gradient(circle, #60a5fa 0%, transparent 70%)"></div>
        <div class="absolute right-32 top-8 w-48 h-48 rounded-full opacity-15"
             style="background: radial-gradient(circle, #93c5fd 0%, transparent 70%)"></div>
        <svg class="absolute right-0 bottom-0 h-full opacity-10" viewBox="0 0 300 200" fill="none">
            <path d="M300 0 C200 50, 150 100, 300 200Z" fill="white"/>
            <path d="M300 50 C180 80, 120 130, 300 200Z" fill="white"/>
        </svg>
    </div>

    <div class="relative flex items-center justify-between px-6 py-6">
        <div>
            <h2 class="text-2xl font-bold text-white mb-1">
                💰 Dashboard Financiero
            </h2>
            <p class="text-blue-200 text-sm max-w-sm">
                Inventario, cuentas, vencimientos y recordatorios en un solo lugar.
            </p>
        </div>
        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('compras.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                      bg-white/20 hover:bg-white/30 text-white border border-white/30
                      backdrop-blur-sm transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Compras
            </a>
            <a href="{{ route('inventario.stock') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                      bg-white text-blue-700 hover:bg-blue-50
                      transition-all duration-200 shadow-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Inventario
            </a>
        </div>
    </div>
</div>
