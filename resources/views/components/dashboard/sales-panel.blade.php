@props([
    'totalProductos' => 0,
    'totalOrdenes'   => 0,
    'ventasLifetime' => 0,
    'visitantes'     => 0,
    'clientesNuevos' => 0,
])

<div class="flex flex-col gap-4">

    {{-- Stats: Products & Orders --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
             border-2 border-gray-200 dark:border-neutral-700 p-5">
        <div class="flex items-center justify-between gap-4 mb-5">
            {{-- Products --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        {{ number_format($totalProductos) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Productos</p>
                </div>
            </div>

            <div class="w-px h-10 bg-gray-200 dark:bg-neutral-700"></div>

            {{-- Orders --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        {{ $totalOrdenes >= 1000 ? number_format($totalOrdenes/1000,1).'K' : number_format($totalOrdenes) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Órdenes</p>
                </div>
            </div>
        </div>

        {{-- Lifetime sales --}}
        <div class="flex items-start justify-between mb-1">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                ${{ number_format($ventasLifetime) }}
            </h2>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold
                         bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">
                YoY 24%
            </span>
        </div>
        <p class="text-sm text-cyan-500 dark:text-cyan-400 mb-5">Ventas de por vida</p>

        {{-- Action buttons --}}
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('finanzas.index') }}"
               class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-bold
                      uppercase tracking-wide text-white
                      bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                      shadow-md hover:shadow-blue-500/30 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Resumen
            </a>
            <a href="{{ route('caja.ventas.historial') }}"
               class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-bold
                      uppercase tracking-wide text-white
                      bg-cyan-500 hover:bg-cyan-600 active:bg-cyan-700
                      shadow-md hover:shadow-cyan-500/30 transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Analíticas
            </a>
        </div>
    </div>

    {{-- Visitors & New Customers --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
             border-2 border-gray-200 dark:border-neutral-700 p-5">
        <div class="flex items-center justify-around text-center divide-x divide-gray-200 dark:divide-neutral-700">
            <div class="flex-1 pr-4">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ $visitantes >= 1000 ? number_format($visitantes/1000, 0).'K' : number_format($visitantes) }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ventas Totales</p>
            </div>
            <div class="flex-1 pl-4">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ number_format($clientesNuevos) }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Clientes Nuevos</p>
            </div>
        </div>
    </div>

</div>
