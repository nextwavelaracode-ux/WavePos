@props(['movimientos' => []])

<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
            border-2 border-gray-200 dark:border-neutral-700
            overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b-2 border-gray-100 dark:border-neutral-700">
        <div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white">Movimientos de Inventario</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Últimas entradas y salidas</p>
        </div>
        <a href="{{ route('inventario.movimientos') }}"
           class="text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700
                  flex items-center gap-1 transition-colors">
            Ver todos
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-neutral-800/60">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Producto</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tipo</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Cant.</th>
                    <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Stock</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                @forelse($movimientos as $mov)
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/40 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800 dark:text-white text-sm truncate max-w-[180px]">
                                {{ $mov['producto'] }}
                            </p>
                            <p class="text-xs text-gray-400 truncate">{{ $mov['motivo'] }}</p>
                        </td>
                        <td class="px-3 py-3">
                            @php
                                $badgeClass = match($mov['tipo']) {
                                    'entrada' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'salida'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'venta'   => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                    default   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ ucfirst($mov['tipo']) }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-right">
                            <span class="font-semibold {{ $mov['tipo'] === 'salida' || $mov['tipo'] === 'venta' ? 'text-red-500' : 'text-emerald-500' }}">
                                {{ $mov['tipo'] === 'salida' || $mov['tipo'] === 'venta' ? '-' : '+' }}{{ $mov['cantidad'] }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-right text-gray-700 dark:text-gray-300 font-medium text-sm">
                            {{ $mov['stock_nuevo'] }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">
                            {{ $mov['fecha'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">
                            Sin movimientos registrados en el período
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
