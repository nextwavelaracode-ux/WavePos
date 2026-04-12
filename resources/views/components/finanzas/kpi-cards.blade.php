@props([
    'totalVentas'   => 0,
    'cuentasCobrar' => 0,
    'cuentasPagar'  => 0,
    'totalGastos'   => 0,
    'totalProductos'=> 0,
    'stockTotal'    => 0,
])

@php
    $cards = [
        ['label' => 'Ventas del Mes',      'value' => '$'.number_format($totalVentas/1000,1).'K',    'color' => '#10b981', 'offset' => 160, 'up' => true,  'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ['label' => 'Ctas. por Cobrar',    'value' => '$'.number_format($cuentasCobrar/1000,1).'K',  'color' => '#3b82f6', 'offset' => 180, 'up' => true,  'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Ctas. por Pagar',     'value' => '$'.number_format($cuentasPagar/1000,1).'K',   'color' => '#f59e0b', 'offset' => 200, 'up' => false, 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
        ['label' => 'Gastos del Mes',      'value' => '$'.number_format($totalGastos/1000,1).'K',    'color' => '#ef4444', 'offset' => 220, 'up' => false, 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z'],
        ['label' => 'Productos Activos',   'value' => number_format($totalProductos),                'color' => '#8b5cf6', 'offset' => 170, 'up' => true,  'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['label' => 'Stock Total',         'value' => number_format($stockTotal).' uds.',            'color' => '#06b6d4', 'offset' => 190, 'up' => true,  'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
    ];
@endphp

<div class="relative -mt-8 mb-6 z-10">
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 lg:gap-4 px-1">
        @foreach($cards as $card)
            <div class="bg-white dark:bg-neutral-900
                        rounded-2xl shadow-sm
                        border-2 border-gray-100 dark:border-neutral-800
                        p-3 sm:p-4 flex items-center gap-2 sm:gap-3
                        hover:border-blue-400 dark:hover:border-blue-700
                        hover:shadow-md hover:-translate-y-0.5
                        transition-all duration-200 overflow-hidden">

                <div class="relative flex-shrink-0">
                    <svg width="52" height="52" viewBox="0 0 52 52">
                        <circle cx="26" cy="26" r="22" stroke="#d1d5db" stroke-width="3" fill="transparent"/>
                        <circle cx="26" cy="26" r="22" stroke="{{ $card['color'] }}" stroke-width="4"
                                fill="transparent" stroke-linecap="round"
                                stroke-dasharray="138" stroke-dashoffset="{{ $card['offset'] }}"
                                transform="rotate(-90 26 26)"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="{{ $card['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $card['icon'] }}"/>
                        </svg>
                    </div>
                </div>

                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mb-0.5">{{ $card['label'] }}</p>
                    <p class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ $card['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
