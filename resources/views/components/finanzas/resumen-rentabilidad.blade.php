@props(['utilidadBruta', 'utilidadNeta', 'margenGanancia', 'totalVentas', 'totalCompras', 'totalGastos'])

<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 p-6 relative overflow-hidden h-full">
    {{-- Decorative Background Elements --}}
    <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-50/50 dark:bg-blue-900/20 rounded-full blur-3xl"></div>

    <div class="relative z-10 flex flex-col h-full">
        <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider mb-5 flex items-center justify-between flex-shrink-0">
            <span>Rentabilidad del Periodo</span>
            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
        </h3>

        {{-- Main KPI: Utilidad Neta --}}
        <div class="mb-6 flex-shrink-0">
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Utilidad Neta (Ingresos - Costos - Gastos)</p>
            <div class="flex items-end gap-3">
                <h4 class="text-3xl font-black text-gray-900 dark:text-white">
                    ${{ number_format($utilidadNeta, 2) }}
                </h4>
                <div class="pb-1.5">
                    @if($margenGanancia >= 0)
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                            {{ number_format($margenGanancia, 1) }}% Margen
                        </span>
                    @else
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                            {{ number_format($margenGanancia, 1) }}% Margen
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Breakdown --}}
        <div class="space-y-3 pt-4 flex-shrink-0 border-t border-gray-100 dark:border-neutral-800/80">
            <div class="flex justify-between items-center group">
                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                    Total Ventas
                </div>
                <span class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                    +${{ number_format($totalVentas, 2) }}
                </span>
            </div>
            
            <div class="flex justify-between items-center group">
                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <div class="w-1.5 h-1.5 rounded-full bg-orange-400"></div>
                    Costos (Compras)
                </div>
                <span class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-orange-500 dark:group-hover:text-orange-400 transition-colors">
                    -${{ number_format($totalCompras, 2) }}
                </span>
            </div>

            <div class="flex justify-between items-center group">
                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                    Gastos Operativos
                </div>
                <span class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                    -${{ number_format($totalGastos, 2) }}
                </span>
            </div>
        </div>

        {{-- Gráfica Visual de Distribución (Barra Apilada) --}}
        @php
            $total = $totalVentas > 0 ? $totalVentas : 1; // Evitar división por cero
            $pctCompras = min(100, round(($totalCompras / $total) * 100));
            $pctGastos = min(100, round(($totalGastos / $total) * 100));
            $pctUtilidad = min(100, max(0, round(($utilidadNeta / $total) * 100)));
        @endphp
        <div class="mt-6 flex-shrink-0 border-t border-gray-100 dark:border-neutral-800/80 pt-4">
            <div class="flex justify-between text-[11px] font-bold text-gray-500 dark:text-gray-400 mb-2">
                <span>Distribución de Ingresos</span>
                <span>100%</span>
            </div>
            
            <div class="w-full h-3 flex rounded-full overflow-hidden bg-gray-100 dark:bg-neutral-800 shadow-inner">
                @if($pctUtilidad > 0)
                <div style="width: {{ $pctUtilidad }}%" class="bg-emerald-500 hover:bg-emerald-400 transition-all duration-500" title="Utilidad: {{ $pctUtilidad }}%"></div>
                @endif
                @if($pctCompras > 0)
                <div style="width: {{ $pctCompras }}%" class="bg-orange-400 hover:bg-orange-300 transition-all duration-500" title="Costos: {{ $pctCompras }}%"></div>
                @endif
                @if($pctGastos > 0)
                <div style="width: {{ $pctGastos }}%" class="bg-red-500 hover:bg-red-400 transition-all duration-500" title="Gastos: {{ $pctGastos }}%"></div>
                @endif
            </div>
            
            <div class="flex justify-between mt-2 mb-2">
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">Neta {{ $pctUtilidad }}%</span>
                <span class="text-[10px] text-orange-600 dark:text-orange-400 font-bold">Costos {{ $pctCompras }}%</span>
                <span class="text-[10px] text-red-600 dark:text-red-400 font-bold">Gastos {{ $pctGastos }}%</span>
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="mt-2 flex-shrink-0 text-center">
            <p class="text-[10px] text-gray-400 dark:text-gray-500">Las métricas reflejan el rango de fechas seleccionado en la parte superior.</p>
        </div>

        {{-- Ilustración dinámica para el espacio vacío --}}
        <div class="mt-auto pt-4 flex-1 flex items-center justify-center animate-fade-in-up">
            <img src="{{ asset('images/login/vecteezy_money-growth-success-and-progress-report-vector-illustration_11754616.svg') }}" 
                 alt="Reporte Financiero" 
                 class="w-[98%] max-h-[240px] xl:max-h-[280px] object-contain opacity-95 filter drop-shadow-md hover:scale-105 hover:-translate-y-1 transition-all duration-500 ease-out">
        </div>
    </div>
</div>
