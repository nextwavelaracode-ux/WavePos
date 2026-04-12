@props(['ventasCategoria' => []])

@php
    $defaultCats = [
        ['nombre' => 'Comida',      'valor' => 251, 'color' => '#3b82f6'],
        ['nombre' => 'Electrónica', 'valor' => 176, 'color' => '#22d3ee'],
        ['nombre' => 'Ropa',        'valor' => 134, 'color' => '#818cf8'],
    ];
    $chartCats = !empty($ventasCategoria) ? $ventasCategoria : $defaultCats;
@endphp

<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
             border-2 border-gray-200 dark:border-neutral-700
             p-5 overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-bold text-gray-800 dark:text-white">Ingresos</h3>
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

    {{-- Chart + Legend --}}
    <div class="flex items-center gap-4">
        {{-- Donut chart --}}
        <div class="flex-shrink-0">
            <div id="dashboardEarningsChart" style="width:130px; height:130px;"></div>
        </div>

        {{-- Legend --}}
        <div class="space-y-3 flex-1">
            @php
                $cats = $chartCats;
            @endphp
            @foreach($cats as $cat)
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                          style="background-color: {{ $cat['color'] }}"></span>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $cat['nombre'] }}</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                            {{ number_format($cat['valor']) }}K
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('dashboardEarningsChart')) return;

    const cats = @json($chartCats);

    const isDark = document.documentElement.classList.contains('dark');

    const options = {
        series: cats.map(c => c.valor),
        labels: cats.map(c => c.nombre),
        colors: cats.map(c => c.color),
        chart: {
            type: 'donut',
            height: 130,
            width: 130,
            background: 'transparent',
            sparkline: { enabled: true }
        },
        stroke: { width: 3, colors: [isDark ? '#171717' : '#ffffff'] },
        dataLabels: { enabled: false },
        legend: { show: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: { show: false }
                }
            }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: v => v + 'K' }
        }
    };

    new ApexCharts(document.getElementById('dashboardEarningsChart'), options).render();
});
</script>
@endpush
