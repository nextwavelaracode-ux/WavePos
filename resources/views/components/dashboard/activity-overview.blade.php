@props(['actividades' => []])

<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
             border-2 border-gray-200 dark:border-neutral-700
             p-5 h-full">
    {{-- Header --}}
    <div class="mb-4">
        <h3 class="text-base font-bold text-gray-800 dark:text-white">Actividad Reciente</h3>
        <div class="flex items-center gap-1.5 mt-1">
            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">16% este mes</span>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="relative">
        @php
            $items = $actividades ?: [
                ['titulo' => '$2,400 – Venta procesada',  'tiempo' => 'Hace 10 min',   'color' => 'bg-blue-500'],
                ['titulo' => 'Nuevo pedido #8744152',      'tiempo' => 'Hace 45 min',   'color' => 'bg-blue-500'],
                ['titulo' => 'Pago de afiliado',           'tiempo' => 'Hace 2 horas',  'color' => 'bg-cyan-500'],
                ['titulo' => 'Nuevo cliente registrado',   'tiempo' => 'Hace 3 horas',  'color' => 'bg-blue-500'],
                ['titulo' => 'Producto agregado al stock', 'tiempo' => 'Hace 5 horas',  'color' => 'bg-emerald-500'],
            ];
        @endphp

        {{-- Vertical line --}}
        <div class="absolute left-[7px] top-2 bottom-2 w-px bg-gray-200 dark:bg-neutral-700"></div>

        <div class="space-y-4">
            @foreach($items as $item)
                <div class="relative flex items-start gap-4 pl-1">
                    {{-- Dot --}}
                    <div class="relative z-10 flex-shrink-0 w-3.5 h-3.5 mt-0.5 rounded-full border-2 border-white dark:border-neutral-900 {{ $item['color'] }}"></div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0 -mt-0.5">
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">
                            {{ $item['titulo'] }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ $item['tiempo'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
