@extends('layouts.app')

@section('content')
<div class="p-4 mx-auto max-w-screen-2xl md:p-6">

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Historial de Ventas</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Registro completo de todas las transacciones</p>
        </div>
        <div class="flex items-center gap-3">
            <div x-data="{ openExport: false }" class="relative">
                <button @click="openExport = !openExport"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exportar
                    <svg class="h-4 w-4 transition-transform" :class="openExport ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openExport" @click.outside="openExport = false"
                    class="absolute right-0 mt-2 w-48 origin-top-right rounded-xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900 z-[60]">
                    <div class="p-1">
                        <a href="{{ route('caja.ventas.exportar', array_merge(request()->all(), ['formato' => 'excel'])) }}" 
                           class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2zM14 8V3.5L18.5 8H14z"/></svg>
                            Excel (.xlsx)
                        </a>
                        <a href="{{ route('caja.ventas.exportar', array_merge(request()->all(), ['formato' => 'pdf'])) }}"
                           class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                            PDF (.pdf)
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('caja.pos') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition">
                + Nueva Venta
            </a>
        </div>
    </div>

    {{-- Dashboard KPIs --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">
        <!-- Tarjeta 1: Ventas del Día -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
                <svg width="24" height="24" fill="none" class="stroke-current" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-gray-800 dark:text-white">${{ number_format($ventasTotalHoy, 2) }}</h4>
                    <span class="text-sm font-medium text-gray-500">Ventas Hoy</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Num Ventas -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-500 dark:bg-blue-500/10 dark:text-blue-400">
                <svg width="24" height="24" fill="none" class="stroke-current" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-gray-800 dark:text-white">{{ $ventasCountHoy }}</h4>
                    <span class="text-sm font-medium text-gray-500">Cant. Ventas Hoy</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Efectivo -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400">
                <svg width="24" height="24" fill="none" class="stroke-current" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-gray-800 dark:text-white">${{ number_format($pagosHoyEfectivo, 2) }}</h4>
                    <span class="text-sm font-medium text-gray-500">Cobrado Efectivo</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Tarjeta -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-500 dark:bg-purple-500/10 dark:text-purple-400">
                <svg width="24" height="24" fill="none" class="stroke-current" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-gray-800 dark:text-white">${{ number_format($pagosHoyTarjeta, 2) }}</h4>
                    <span class="text-sm font-medium text-gray-500">Cobrado Tarjeta</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta 5: Devoluciones -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-500 dark:bg-red-500/10 dark:text-red-400">
                <svg width="24" height="24" fill="none" class="stroke-current" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" /></svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-gray-800 dark:text-white">{{ $devolucionesCountHoy }}</h4>
                    <span class="text-sm font-medium text-gray-500">Devoluciones Hoy</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="mb-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <!-- Line Chart -->
        <div class="xl:col-span-2 rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6 shadow-sm">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white/90 mb-4">Ingresos Últimos 7 Días</h4>
            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <div id="chartVentasLine" class="-ml-4 min-w-[700px] pl-2 xl:min-w-full"></div>
            </div>
        </div>
        
        <!-- Donut Chart -->
        <div class="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6 shadow-sm">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white/90 mb-4">Métodos de Pago (Mes)</h4>
            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <div id="chartPagosDonut" class="min-h-[300px] min-w-[300px] flex items-center justify-center"></div>
            </div>
        </div>
    </div>

    <div class="mb-5 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="mb-1 block text-xs font-medium text-gray-500">Buscar (N° o cliente)</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="VTA-2025...">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500">Estado</label>
                <select name="estado" class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">Todos</option>
                    <option value="completada" @selected(request('estado') === 'completada')>Completada</option>
                    <option value="anulada" @selected(request('estado') === 'anulada')>Anulada</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                       class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                       class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="h-10 rounded-lg bg-brand-500 px-4 text-sm font-semibold text-white hover:bg-brand-600 transition">Filtrar</button>
                <a href="{{ route('caja.ventas.historial') }}" class="h-10 rounded-lg border border-gray-300 px-4 text-sm font-semibold text-gray-600 flex items-center hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">Limpiar</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">N° Venta</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pago</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Cajero</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($ventas as $venta)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <td class="px-4 py-3 font-mono text-xs text-brand-600 dark:text-brand-400 font-semibold">{{ $venta->numero }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $venta->cliente?->nombre_completo ?? 'Consumidor Final' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $venta->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            @foreach($venta->pagos->groupBy('metodo') as $metodo => $pagos)
                                <span class="inline-block text-xs rounded-full px-2 py-0.5 font-medium
                                    @if($metodo === 'efectivo') bg-emerald-100 text-emerald-700
                                    @elseif($metodo === 'tarjeta') bg-blue-100 text-blue-700
                                    @elseif($metodo === 'transferencia') bg-purple-100 text-purple-700
                                    @elseif($metodo === 'yappy') bg-amber-100 text-amber-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($metodo) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white">${{ number_format($venta->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                @if($venta->estado === 'completada') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400
                                @elseif($venta->estado === 'anulada') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ ucfirst($venta->estado) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $venta->usuario?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('caja.ventas.show', $venta) }}" class="rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">Ver</a>
                                <a href="{{ route('caja.ventas.pdf', $venta) }}" target="_blank" class="rounded-lg bg-blue-100 px-2.5 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-200 transition">A4</a>
                                <button x-data @click="$dispatch('abrir-ticket', '{{ route('caja.ventas.ticket', $venta) }}')" class="rounded-lg bg-emerald-100 px-2.5 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-200 transition">80mm</button>
                                @if($venta->estado === 'completada')
                                <button onclick="anularVenta({{ $venta->id }}, '{{ $venta->numero }}')"
                                        class="rounded-lg bg-red-100 px-2.5 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200 transition">Anular</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-400">No hay ventas registradas con los filtros aplicados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ventas->hasPages())
        <div class="p-4 border-t border-gray-200 dark:border-gray-800">
            {{ $ventas->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal anulación --}}
<div id="modalAnular" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-2xl p-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Anular Venta <span id="anularNumero" class="text-red-500"></span></h3>
        <form id="formAnular" method="POST">
            @csrf
            <textarea name="motivo_anulacion" id="anularMotivo" rows="3" required
                      class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-red-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                      placeholder="Motivo de la anulación (requerido)..."></textarea>
            <div class="mt-4 flex gap-3 justify-end">
                <button type="button" onclick="cerrarModalAnular()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">Cancelar</button>
                <button type="button" onclick="submitAnular()" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Anular Venta</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
let ventaAnularId = null;
function anularVenta(id, numero) {
    ventaAnularId = id;
    document.getElementById('anularNumero').textContent = numero;
    document.getElementById('anularMotivo').value = '';
    document.getElementById('modalAnular').classList.remove('hidden');
}
function cerrarModalAnular() {
    document.getElementById('modalAnular').classList.add('hidden');
}
async function submitAnular() {
    const motivo = document.getElementById('anularMotivo').value.trim();
    if (!motivo) { Swal.fire('Requerido', 'Ingresa el motivo de la anulación.', 'warning'); return; }
    const resp = await fetch(`/caja/ventas/${ventaAnularId}/anular`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ motivo_anulacion: motivo }),
    });
    const data = await resp.json();
    if (data.success) {
        cerrarModalAnular();
        Swal.fire({ icon: 'success', title: '¡Anulada!', text: data.message, timer: 2000, showConfirmButton: false }).then(() => location.reload());
    } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#9ca3af' : '#6b7280';
    
    // Line Chart
    const lineOptions = {
        series: [{ name: 'Ventas ($)', data: @json($chartLineSeries) }],
        chart: { fontFamily: 'inherit', height: 300, type: 'area', toolbar: { show: false } },
        colors: ['#3b82f6'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 100] } },
        xaxis: {
            categories: @json($chartLineLabels),
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: textColor, fontSize: '12px' } }
        },
        yaxis: { labels: { style: { colors: textColor, fontSize: '12px' }, formatter: (value) => "$" + value.toFixed(0) } },
        grid: { borderColor: isDark ? '#374151' : '#e5e7eb', strokeDashArray: 4, yaxis: { lines: { show: true } } },
        tooltip: { theme: isDark ? 'dark' : 'light' }
    };
    new ApexCharts(document.querySelector("#chartVentasLine"), lineOptions).render();

    // Donut Chart
    const donutOptions = {
        series: @json($chartPieSeries),
        chart: { type: 'donut', fontFamily: 'inherit', height: 300 },
        labels: @json($chartPieLabels),
        colors: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#6b7280'],
        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, name: { show: true }, value: { show: true, formatter: (val) => "$" + Number(val).toFixed(2) } } } } },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', labels: { colors: textColor } },
        stroke: { show: false },
        tooltip: { theme: isDark ? 'dark' : 'light' }
    };
    new ApexCharts(document.querySelector("#chartPagosDonut"), donutOptions).render();
});
</script>
@endsection
