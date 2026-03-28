@extends('layouts.app')

@section('title', 'Dashboard Financiero')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            Dashboard Financiero
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li><a class="font-medium" href="{{ route('dashboard') }}">Dashboard /</a></li>
                <li class="font-medium text-primary">Finanzas</li>
            </ol>
        </nav>
    </div>

    <!-- Filtros -->
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/[0.05] dark:bg-boxdark">
        <form method="GET" action="{{ route('finanzas.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}"
                    class="w-full rounded-xl border border-stroke bg-transparent py-2 px-4 outline-none focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input">
            </div>
            <div class="flex-1">
                <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}"
                    class="w-full rounded-xl border border-stroke bg-transparent py-2 px-4 outline-none focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input">
            </div>
            <div class="flex-1">
                <label class="mb-2.5 block text-sm font-medium text-black dark:text-white">Sucursal</label>
                <select name="sucursal_id"
                    class="w-full rounded-xl border border-stroke bg-transparent py-2.5 px-4 outline-none focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input">
                    <option value="todas" {{ $sucursalId === 'todas' ? 'selected' : '' }}>Todas las Sucursales</option>
                    @foreach ($sucursales as $suc)
                        <option value="{{ $suc->id }}" {{ $sucursalId == $suc->id ? 'selected' : '' }}>
                            {{ $suc->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit"
                    class="flex w-full justify-center rounded-xl bg-primary p-2.5 font-medium text-white hover:bg-opacity-90 shadow-2 hover:shadow-1 transition-all sm:w-auto">
                    Aplicar Filtros
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-12 gap-4 md:gap-6">
        <div class="col-span-12 space-y-6 xl:col-span-7">

            <!-- ecommerce-metrics HTML -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
                        <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M8.80443 5.60156C7.59109 5.60156 6.60749 6.58517 6.60749 7.79851C6.60749 9.01185 7.59109 9.99545 8.80443 9.99545C10.0178 9.99545 11.0014 9.01185 11.0014 7.79851C11.0014 6.58517 10.0178 5.60156 8.80443 5.60156ZM5.10749 7.79851C5.10749 5.75674 6.76267 4.10156 8.80443 4.10156C10.8462 4.10156 12.5014 5.75674 12.5014 7.79851C12.5014 9.84027 10.8462 11.4955 8.80443 11.4955C6.76267 11.4955 5.10749 9.84027 5.10749 7.79851ZM4.86252 15.3208C4.08769 16.0881 3.70377 17.0608 3.51705 17.8611H13.4249C13.6343 18.3987 13.7992 18.3141 13.8937 18.2112C13.9797 18.1175 14.017 18.0034 13.9838 17.8611C13.7971 17.0608 13.4132 16.0881 12.6383 15.3208C11.8821 14.572 10.6899 13.955 8.75042 13.955C6.81096 13.955 5.61877 14.572 4.86252 15.3208ZM3.8071 14.2549C4.87163 13.2009 6.45602 12.455 8.75042 12.455C11.0448 12.455 12.6292 13.2009 13.6937 14.2549C14.7397 15.2906 15.2207 16.5607 15.4446 17.5202C15.7658 18.8971 14.6071 19.8987 13.4249 19.8987H4.07591C2.89369 19.8987 1.73504 18.8971 2.05628 17.5202C2.28015 16.5607 2.76117 15.2906 3.8071 14.2549ZM15.3042 11.4955C14.4702 11.4955 13.7006 11.2193 13.0821 10.7533C13.3742 10.3314 13.6054 9.86419 13.7632 9.36432C14.1597 9.75463 14.7039 9.99545 15.3042 9.99545C16.5176 9.99545 17.5012 9.01185 17.5012 7.79851C17.5012 6.58517 16.5176 5.60156 15.3042 5.60156C14.7039 5.60156 14.1597 5.84239 13.7632 6.23271C13.6054 5.73284 13.3741 5.26561 13.082 4.84371C13.7006 4.37777 14.4702 4.10156 15.3042 4.10156C17.346 4.10156 19.0012 5.75674 19.0012 7.79851C19.0012 9.84027 17.346 11.4955 15.3042 11.4955ZM19.9248 19.8987H16.3901C16.7014 19.4736 16.9159 18.969 16.9827 18.3987H19.9248C20.1341 18.3987 20.2991 18.3141 20.3936 18.2112C20.4796 18.1175 20.5169 18.0034 20.4837 17.861C20.2969 17.0607 19.913 16.088 19.1382 15.3208C18.4047 14.5945 17.261 13.9921 15.4231 13.9566C15.2232 13.6945 14.9995 13.437 14.7491 13.1891C14.5144 12.9566 14.262 12.7384 13.9916 12.5362C14.3853 12.4831 14.8044 12.4549 15.2503 12.4549C17.5447 12.4549 19.1291 13.2008 20.1936 14.2549C21.2395 15.2906 21.7206 16.5607 21.9444 17.5202C22.2657 18.8971 21.107 19.8987 19.9248 19.8987Z"
                                fill="" />
                        </svg>
                    </div>
                    <div class="flex items-end justify-between mt-5">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Ingresos Totales (Ventas)</span>
                            <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
                                ${{ number_format($totalVentas, 2) }}</h4>
                        </div>
                        <span
                            class="flex items-center gap-1 rounded-full bg-success-50 py-0.5 pl-2 pr-2.5 text-sm font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                            <svg class="fill-current" width="12" height="12" viewBox="0 0 12 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432C6.1236 1.37432 6.12391 1.37432 6.12422 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247L6.87329 10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z"
                                    fill="" />
                            </svg>
                            Mg {{ number_format($margenGanancia, 2) }}%
                        </span>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
                        <svg class="fill-gray-800 dark:fill-white/90" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M11.665 3.75621C11.8762 3.65064 12.1247 3.65064 12.3358 3.75621L18.7807 6.97856L12.3358 10.2009C12.1247 10.3065 11.8762 10.3065 11.665 10.2009L5.22014 6.97856L11.665 3.75621ZM4.29297 8.19203V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0366V11.6513C11.1631 11.6205 11.0777 11.5843 10.9942 11.5426L4.29297 8.19203ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19202L13.0066 11.5426C12.9229 11.5844 12.8372 11.6208 12.75 11.6516V20.037ZM13.0066 2.41456C12.3732 2.09786 11.6277 2.09786 10.9942 2.41456L4.03676 5.89319C3.27449 6.27432 2.79297 7.05342 2.79297 7.90566V16.0946C2.79297 16.9469 3.27448 17.726 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.726 21.2079 16.9469 21.2079 16.0946V7.90566C21.2079 7.05342 20.7264 6.27432 19.9641 5.89319L13.0066 2.41456Z"
                                fill="" />
                        </svg>
                    </div>
                    <div class="flex items-end justify-between mt-5">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Egresos Totales (Compras + Gastos)</span>
                            <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
                                ${{ number_format($totalCompras + $totalGastos, 2) }}</h4>
                        </div>
                        <span
                            class="flex items-center gap-1 rounded-full bg-error-50 py-0.5 pl-2 pr-2.5 text-sm font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                            <svg class="fill-current" width="12" height="12" viewBox="0 0 12 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257C5.8736 10.6257 5.8739 10.6257 5.87421 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753L6.62329 1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875L5.12329 8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z"
                                    fill="" />
                            </svg>
                            Utilidad ${{ number_format($utilidadNeta, 0) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- monthly-sale HTML -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 sm:px-6 sm:pt-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Historial de Ingresos</h3>
                    <x-common.dropdown-menu />
                </div>
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <div id="chartOneFinanzas" class="-ml-5 h-full min-w-[1000px] pl-2"></div>
                </div>
            </div>

        </div>

        <div class="col-span-12 xl:col-span-5">
            <!-- monthly-target HTML -->
            <div class="rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="shadow-default rounded-2xl bg-white px-5 pb-11 pt-5 dark:bg-gray-900 sm:px-6 sm:pt-6">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Progreso Utilidad</h3>
                            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">Rentabilidad (% margen)</p>
                        </div>
                        <x-common.dropdown-menu />
                    </div>
                    <div class="relative max-h-[195px]">
                        <div id="chartTwoFinanzas" class="h-full"></div>
                    </div>

                </div>
                <div class="flex items-center justify-center gap-5 px-6 py-3.5 sm:gap-8 sm:py-5">
                    <div>
                        <p class="mb-1 text-center text-theme-xs text-gray-500 dark:text-gray-400 sm:text-sm">Ventas</p>
                        <p
                            class="flex items-center justify-center gap-1 text-base font-semibold text-gray-800 dark:text-white/90 sm:text-lg">
                            ${{ number_format($totalVentas, 0) }}
                        </p>
                    </div>
                    <div class="h-7 w-px bg-gray-200 dark:bg-gray-800"></div>
                    <div>
                        <p class="mb-1 text-center text-theme-xs text-gray-500 dark:text-gray-400 sm:text-sm">Egresos</p>
                        <p
                            class="flex items-center justify-center gap-1 text-base font-semibold text-gray-800 dark:text-white/90 sm:text-lg">
                            ${{ number_format($totalCompras + $totalGastos, 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12">
            <!-- statistics-chart HTML -->
            <div
                class="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
                <div class="flex flex-col gap-5 mb-6 sm:flex-row sm:justify-between">
                    <div class="w-full">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Evolución Ingresos vs Gastos
                        </h3>
                        <p class="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">Tendencia de ventas y egresos
                            operativos</p>
                    </div>
                </div>
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <div id="chartThreeFinanzas" class="-ml-4 min-w-[700px] pl-2 xl:min-w-full"></div>
                </div>
            </div>
        </div>

        <div class="col-span-12 xl:col-span-5">
            <!-- customer-demographic HTML -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
                <div class="flex justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Utilidad por Sucursal</h3>
                        <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">Rendimiento por ubicaciones</p>
                    </div>
                    <x-common.dropdown-menu />
                </div>

                <!-- Dummy Map Graphic to perfectly match template visually -->
                <div
                    class="border-gary-200 my-6 overflow-hidden rounded-2xl border bg-gray-50 px-4 py-6 dark:border-gray-800 dark:bg-gray-900 sm:px-6">
                    <div id="mapOne"
                        class="mapOne map-btn -mx-4 -my-6 h-[212px] w-[252px] 2xsm:w-[307px] xsm:w-[358px] sm:-mx-6 md:w-[668px] lg:w-[634px] xl:w-[393px] 2xl:w-[554px]">
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach ($sucursalesKpis as $suc)
                        @php
                            $porcentaje = $totalVentas > 0 ? ($suc['ventas'] / $totalVentas) * 100 : 0;
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-full max-w-8 items-center rounded-full bg-primary/20 flex justify-center py-2 text-primary font-bold">
                                    {{ substr($suc['nombre'], 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">
                                        {{ $suc['nombre'] }}</p>
                                    <span
                                        class="block text-theme-xs text-gray-500 dark:text-gray-400">${{ number_format($suc['utilidad'], 2) }}</span>
                                </div>
                            </div>
                            <div class="flex w-full max-w-[140px] items-center gap-3">
                                <div
                                    class="relative block h-2 w-full max-w-[100px] rounded-sm bg-gray-200 dark:bg-gray-800">
                                    <div class="absolute left-0 top-0 flex h-full items-center justify-center rounded-sm bg-brand-500 text-xs font-medium text-white"
                                        style="width: {{ $porcentaje }}%"></div>
                                </div>
                                <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                    {{ number_format($porcentaje, 1) }}%</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-span-12 xl:col-span-7">
            <!-- recent-orders HTML -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
                <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Últimos Movimientos Activos</h3>
                    </div>
                </div>
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-t border-gray-100 dark:border-gray-800">
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Categoría / Ref
                                    </p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Fecha</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Monto</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Tipo</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($flujoReciente as $mov)
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $mov['categoria'] }}</p>
                                        <span
                                            class="text-gray-500 text-theme-xs dark:text-gray-400">{{ Str::limit($mov['referencia'], 20) }}</span>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($mov['fecha'])->format('d M y') }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            ${{ number_format($mov['monto'], 2) }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        @if ($mov['tipo'] == 'ingreso')
                                            <span
                                                class="rounded-full px-2 py-0.5 text-theme-xs font-medium bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">Ingreso</span>
                                        @else
                                            <span
                                                class="rounded-full px-2 py-0.5 text-theme-xs font-medium bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500">Egreso</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- Productos Más Vendidos --}}
        <div class="col-span-12 xl:col-span-6">
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
                <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Productos Más Vendidos</h3>
                        <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">Top 5 por utilidad generada</p>
                    </div>
                </div>
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-t border-gray-100 dark:border-gray-800">
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Producto</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Vendidos</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Ingreso</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Utilidad</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Margen</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProductos as $prod)
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $prod['nombre'] }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ $prod['ventas'] }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            ${{ number_format($prod['ingreso'], 2) }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="font-medium text-success-600 text-theme-sm dark:text-success-500">
                                            ${{ number_format($prod['utilidad'], 2) }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <span
                                            class="rounded-full px-2 py-0.5 text-theme-xs font-medium bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ $prod['margen'] }}%</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-400 text-theme-sm">Sin datos en
                                        este periodo</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Vendedores Top --}}
        <div class="col-span-12 xl:col-span-6">
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
                <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Vendedores Top</h3>
                        <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">Usuarios con más ventas</p>
                    </div>
                </div>
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-t border-gray-100 dark:border-gray-800">
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">#</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Usuario</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Total Ventas
                                    </p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Transacciones
                                    </p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topSellers as $index => $seller)
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="py-3 whitespace-nowrap">
                                        <div
                                            class="flex items-center justify-center w-7 h-7 rounded-full bg-brand-50 dark:bg-brand-500/15">
                                            <span
                                                class="text-theme-xs font-bold text-brand-600 dark:text-brand-400">{{ $index + 1 }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $seller['nombre'] }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="font-medium text-success-600 text-theme-sm dark:text-success-500">
                                            ${{ number_format($seller['total_ventas'], 2) }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <span
                                            class="rounded-full px-2 py-0.5 text-theme-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $seller['transacciones'] }}
                                            ventas</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-400 text-theme-sm">Sin datos en
                                        este periodo</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Historial de Turnos --}}
        <div class="col-span-12">
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
                <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Historial de Turnos</h3>
                        <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">Últimas sesiones de caja</p>
                    </div>
                </div>
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-t border-gray-100 dark:border-gray-800">
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Usuario</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Sucursal</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Apertura</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Cierre</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Total Ventas
                                    </p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Diferencia</p>
                                </th>
                                <th class="py-3 text-left">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Estado</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historialTurnos as $turno)
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $turno->usuario->name ?? 'N/A' }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ $turno->sucursal->nombre ?? 'N/A' }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ $turno->fecha_apertura ? $turno->fecha_apertura->format('d M y H:i') : '-' }}
                                        </p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ $turno->fecha_cierre ? $turno->fecha_cierre->format('d M y H:i') : '-' }}
                                        </p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            ${{ number_format($turno->total_ventas, 2) }}</p>
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        @if ($turno->diferencia < 0)
                                            <span
                                                class="text-theme-sm font-medium text-error-600 dark:text-error-500">${{ number_format($turno->diferencia, 2) }}</span>
                                        @elseif($turno->diferencia > 0)
                                            <span
                                                class="text-theme-sm font-medium text-success-600 dark:text-success-500">+${{ number_format($turno->diferencia, 2) }}</span>
                                        @else
                                            <span
                                                class="text-theme-sm font-medium text-gray-500 dark:text-gray-400">$0.00</span>
                                        @endif
                                    </td>
                                    <td class="py-3 whitespace-nowrap">
                                        @if ($turno->estado === 'abierta')
                                            <span
                                                class="rounded-full px-2 py-0.5 text-theme-xs font-medium bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">Abierta</span>
                                        @else
                                            <span
                                                class="rounded-full px-2 py-0.5 text-theme-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">Cerrada</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-400 text-theme-sm">Sin turnos en
                                        este periodo</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trendData = @json($graficaPeriodos);
            const margen = {{ $margenGanancia }};

            // --- Chart One: Sales Bar Chart (Identical styling to chart-1.js) ---
            if (document.querySelector('#chartOneFinanzas')) {
                const chartOneOptions = {
                    series: [{
                        name: "Ingresos",
                        data: trendData.ingresos,
                    }],
                    colors: ["#465fff"],
                    chart: {
                        fontFamily: "Outfit, sans-serif",
                        type: "bar",
                        height: 180,
                        toolbar: {
                            show: false,
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: "55%",
                            borderRadius: 5,
                            borderRadiusApplication: "end",
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 4,
                        colors: ["transparent"],
                    },
                    xaxis: {
                        categories: trendData.categorias,
                        tickAmount: 7,
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false,
                        },
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: "left",
                        fontFamily: "Outfit",
                        markers: {
                            radius: 99,
                        },
                    },
                    yaxis: {
                        title: false,
                    },
                    grid: {
                        yaxis: {
                            lines: {
                                show: true,
                            },
                        },
                    },
                    fill: {
                        opacity: 1,
                    },
                    tooltip: {
                        x: {
                            show: false,
                        },
                        y: {
                            formatter: function(val) {
                                return "$" + val;
                            },
                        },
                    },
                };
                new ApexCharts(document.querySelector('#chartOneFinanzas'), chartOneOptions).render();
            }

            // --- Chart Two: Radial Progress Target (Identical styling to chart-2.js) ---
            if (document.querySelector('#chartTwoFinanzas')) {
                let mVal = (margen > 100) ? 100 : ((margen < 0) ? 0 : margen);
                const chartTwoOptions = {
                    series: [mVal],
                    colors: ["#465FFF"],
                    chart: {
                        fontFamily: "Outfit, sans-serif",
                        type: "radialBar",
                        height: 330,
                        sparkline: {
                            enabled: true,
                        },
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -90,
                            endAngle: 90,
                            hollow: {
                                size: "80%",
                            },
                            track: {
                                background: "#E4E7EC",
                                strokeWidth: "100%",
                                margin: 5,
                            },
                            dataLabels: {
                                name: {
                                    show: false,
                                },
                                value: {
                                    fontSize: "36px",
                                    fontWeight: "600",
                                    offsetY: 60,
                                    color: "#1D2939",
                                    formatter: function(val) {
                                        return val + "%";
                                    },
                                },
                            },
                        },
                    },
                    fill: {
                        type: "solid",
                        colors: ["#465FFF"],
                    },
                    stroke: {
                        lineCap: "round",
                    },
                    labels: ["Progress"],
                };
                new ApexCharts(document.querySelector('#chartTwoFinanzas'), chartTwoOptions).render();
            }

            // --- Chart Three: Statistics Area Chart (Identical styling to chart-3.js) ---
            if (document.querySelector('#chartThreeFinanzas')) {
                const chartThreeOptions = {
                    series: [{
                            name: "Ingresos",
                            data: trendData.ingresos,
                        },
                        {
                            name: "Egresos",
                            data: trendData.egresos,
                        },
                    ],
                    legend: {
                        show: false,
                        position: "top",
                        horizontalAlign: "left",
                    },
                    colors: ["#465FFF", "#F87171"],
                    chart: {
                        fontFamily: "Outfit, sans-serif",
                        height: 310,
                        type: "area",
                        toolbar: {
                            show: false,
                        },
                    },
                    fill: {
                        gradient: {
                            enabled: true,
                            opacityFrom: 0.55,
                            opacityTo: 0,
                        },
                    },
                    stroke: {
                        curve: "straight",
                        width: ["2", "2"],
                    },
                    markers: {
                        size: 0,
                    },
                    labels: {
                        show: false,
                        position: "top",
                    },
                    grid: {
                        xaxis: {
                            lines: {
                                show: false,
                            },
                        },
                        yaxis: {
                            lines: {
                                show: true,
                            },
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "$" + val;
                            }
                        }
                    },
                    xaxis: {
                        type: "category",
                        categories: trendData.categorias,
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false,
                        },
                        tooltip: false,
                    },
                    yaxis: {
                        title: {
                            style: {
                                fontSize: "0px",
                            },
                        },
                    },
                };
                new ApexCharts(document.querySelector('#chartThreeFinanzas'), chartThreeOptions).render();
            }
        });
    </script>
@endpush
