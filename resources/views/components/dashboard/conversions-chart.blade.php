@props(['ventasDiarias' => []])

<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
             border-2 border-gray-200 dark:border-neutral-700
             p-5 overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-bold text-gray-800 dark:text-white">Conversiones</h3>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 transition-colors">
                Esta Semana
                <svg class="w-3.5 h-3.5" :class="open ? 'rotate-180' : ''"
                     fill="currentColor" viewBox="0 0 512 512">
                    <path d="M128 192l128 128 128-128z"/>
                </svg>
            </button>
            <div x-show="open" @click.outside="open = false"
                 class="absolute right-0 top-6 z-50 bg-white dark:bg-neutral-800
                        border border-gray-200 dark:border-neutral-700
                        rounded-xl shadow-lg py-1 min-w-[120px]">
                <a href="#" class="block px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">Esta Semana</a>
                <a href="#" class="block px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">Este Mes</a>
                <a href="#" class="block px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">Este Año</a>
            </div>
        </div>
    </div>

    {{-- Bar chart --}}
    <div id="dashboardConversionsChart" style="min-height: 130px;"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('dashboardConversionsChart')) return;

    const raw = @json($ventasDiarias ?? []);
    const isDark = document.documentElement.classList.contains('dark');

    const dias = ['D','L','M','X','J','V','S','D','L','M','X'];
    const s1 = raw.length > 0 ? raw : [40, 70, 55, 90, 45, 120, 65, 80, 30, 95, 70];
    const s2 = s1.map(v => Math.round(v * 0.4 + 10));

    const options = {
        series: [
            { name: 'Ventas', data: s1 },
            { name: 'Clientes', data: s2 }
        ],
        chart: {
            type: 'bar',
            height: 130,
            stacked: true,
            background: 'transparent',
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        colors: ['#3b82f6', '#22d3ee'],
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '60%',
                borderRadius: 3,
                borderRadiusApplication: 'end',
                borderRadiusWhenStacked: 'last'
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: false },
        grid: {
            borderColor: isDark ? '#374151' : '#f3f4f6',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } }
        },
        xaxis: {
            categories: dias,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: isDark ? '#9ca3af' : '#6b7280', fontSize: '11px' } }
        },
        yaxis: {
            labels: {
                style: { colors: isDark ? '#9ca3af' : '#6b7280', fontSize: '11px' },
                formatter: v => v
            }
        },
        legend: { show: false },
        tooltip: {
            theme: isDark ? 'dark' : 'light'
        }
    };

    new ApexCharts(document.getElementById('dashboardConversionsChart'), options).render();
});
</script>
@endpush
