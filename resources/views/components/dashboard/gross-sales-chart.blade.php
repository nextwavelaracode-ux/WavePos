@props(['ventasMensuales' => []])

@php
    $defaultVentas = [12, 19, 14, 28, 22, 18, 30, 25, 20, 35, 28, 18];
    $chartVentas   = (!empty($ventasMensuales)) ? $ventasMensuales : $defaultVentas;
    $chartTotal    = array_sum($chartVentas);
@endphp

<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
             border-2 border-gray-200 dark:border-neutral-700
             p-5 mb-5 overflow-hidden">
    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
        <div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                ${{ number_format($chartTotal / 1000, 1) }}K
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ventas Brutas</p>
        </div>
        <div class="flex items-center gap-4">
            {{-- Legend --}}
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Ventas</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Costos</span>
            </div>
            {{-- Period dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400
                               hover:text-gray-700 dark:hover:text-white transition-colors">
                    Esta Semana
                    <svg class="w-3.5 h-3.5 transition-transform duration-200"
                         :class="open ? 'rotate-180' : ''"
                         fill="currentColor" viewBox="0 0 512 512">
                        <path d="M128 192l128 128 128-128z"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 top-6 z-50 bg-white dark:bg-neutral-800
                            border border-gray-200 dark:border-neutral-700
                            rounded-xl shadow-lg py-1 min-w-[130px]">
                    <a href="#" class="block px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300
                                       hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600">
                        Esta Semana
                    </a>
                    <a href="#" class="block px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300
                                       hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600">
                        Este Mes
                    </a>
                    <a href="#" class="block px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300
                                       hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600">
                        Este Año
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div id="dashboardGrossSalesChart" style="min-height: 220px;"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('dashboardGrossSalesChart')) return;

    const ventas = @json($chartVentas);
    const costos = ventas.map(v => Math.round(v * 0.6 + Math.random() * 4));

    const isDark = document.documentElement.classList.contains('dark');

    const options = {
        series: [
            { name: 'Ventas', data: ventas },
            { name: 'Costos', data: costos }
        ],
        chart: {
            type: 'area',
            height: 220,
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false },
            background: 'transparent',
        },
        colors: ['#3b82f6', '#22d3ee'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        stroke: { curve: 'smooth', width: 2 },
        dataLabels: { enabled: false },
        grid: {
            borderColor: isDark ? '#374151' : '#f3f4f6',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } }
        },
        xaxis: {
            categories: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: isDark ? '#9ca3af' : '#6b7280', fontSize: '11px' } }
        },
        yaxis: {
            labels: {
                style: { colors: isDark ? '#9ca3af' : '#6b7280', fontSize: '11px' },
                formatter: v => '$' + v + 'K'
            }
        },
        legend: { show: false },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: v => '$' + v + 'K' }
        }
    };

    const chart = new ApexCharts(document.getElementById('dashboardGrossSalesChart'), options);
    chart.render();
});
</script>
@endpush
