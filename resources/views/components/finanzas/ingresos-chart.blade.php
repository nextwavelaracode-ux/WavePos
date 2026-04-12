@props(['graficaPeriodos' => ['categorias' => [], 'ingresos' => [], 'egresos' => []]])

@php
    $defaultCats = ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30'];
    $chartCats     = !empty($graficaPeriodos['categorias']) ? $graficaPeriodos['categorias'] : $defaultCats;
    $chartIngresos = !empty($graficaPeriodos['ingresos'])   ? $graficaPeriodos['ingresos']   : array_fill(0, 30, 0);
    $chartEgresos  = !empty($graficaPeriodos['egresos'])    ? $graficaPeriodos['egresos']     : array_fill(0, 30, 0);
@endphp

<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
            border-2 border-gray-200 dark:border-neutral-700
            p-5 overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white">Ingresos vs Egresos</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Evolución del período seleccionado</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Ingresos</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Egresos</span>
            </div>
        </div>
    </div>
    <div id="finanzasIngresosChart" style="min-height: 240px;"
         data-cats='{!! json_encode($chartCats) !!}'
         data-ingresos='{!! json_encode($chartIngresos) !!}'
         data-egresos='{!! json_encode($chartEgresos) !!}'>
    </div>
</div>
