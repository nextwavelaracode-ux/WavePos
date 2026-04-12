@props([
    'totalVentas'   => 0,
    'totalGanancia' => 0,
    'totalCostos'   => 0,
    'totalIngresos' => 0,
    'ingresosNetos' => 0,
    'ventasHoy'     => 0,
    'totalClientes' => 0,
])

@php
    $cards = [
        ['label' => 'Total Ventas',   'value' => '$'.number_format($totalVentas/1000,0).'K',   'color' => '#3b82f6', 'offset' => 160, 'up' => true],
        ['label' => 'Ganancia',       'value' => '$'.number_format($totalGanancia/1000,0).'K', 'color' => '#22d3ee', 'offset' => 200, 'up' => false],
        ['label' => 'Costos',         'value' => '$'.number_format($totalCostos/1000,0).'K',   'color' => '#3b82f6', 'offset' => 240, 'up' => false],
        ['label' => 'Ingresos',       'value' => '$'.number_format($totalIngresos/1000,0).'K', 'color' => '#22d3ee', 'offset' => 180, 'up' => true],
        ['label' => 'Ingresos Netos', 'value' => '$'.number_format($ingresosNetos/1000,0).'K', 'color' => '#3b82f6', 'offset' => 200, 'up' => true],
        ['label' => 'Hoy',            'value' => '$'.number_format($ventasHoy,0),               'color' => '#22d3ee', 'offset' => 190, 'up' => true],
        ['label' => 'Clientes',       'value' => number_format($totalClientes/1000,1).'K',      'color' => '#3b82f6', 'offset' => 170, 'up' => true],
    ];
@endphp

<div class="relative -mt-8 mb-6 z-10">
    {{-- Centrado con flex-wrap para pantallas grandes, scroll en móvil --}}
    <div class="flex flex-wrap justify-center gap-3 overflow-x-auto pb-1">
        @foreach($cards as $card)
            <div class="flex-shrink-0 w-44 bg-white dark:bg-neutral-900
                        rounded-2xl shadow-sm
                        border-2 border-gray-200 dark:border-neutral-700
                        p-4 flex items-center gap-3
                        hover:border-blue-300 dark:hover:border-blue-700
                        hover:shadow-md hover:-translate-y-0.5
                        transition-all duration-200">

                {{-- Circular SVG progress --}}
                <div class="relative flex-shrink-0">
                    <svg width="52" height="52" viewBox="0 0 52 52">
                        <circle cx="26" cy="26" r="22"
                                stroke="#d1d5db" stroke-width="3"
                                fill="transparent"/>
                        <circle cx="26" cy="26" r="22"
                                stroke="{{ $card['color'] }}" stroke-width="4"
                                fill="transparent"
                                stroke-linecap="round"
                                stroke-dasharray="138"
                                stroke-dashoffset="{{ $card['offset'] }}"
                                transform="rotate(-90 26 26)"/>
                    </svg>
                    {{-- Arrow icon --}}
                    <div class="absolute inset-0 flex items-center justify-center">
                        @if($card['up'])
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $card['color'] }}">
                                <path d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z"/>
                            </svg>
                        @else
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $card['color'] }}">
                                <path d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z"/>
                            </svg>
                        @endif
                    </div>
                </div>

                {{-- Label & Value --}}
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mb-0.5">{{ $card['label'] }}</p>
                    <p class="text-base font-bold text-gray-800 dark:text-white truncate">{{ $card['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
