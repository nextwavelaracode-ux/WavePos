@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Gastos" />

    <div class="space-y-6" x-data="gastosApp()">

        {{-- ===== DASHBOARD KPI CARDS ===== --}}
        <div class="mb-6 grid md:grid-cols-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-2xs rounded-xl overflow-hidden">

            {{-- Total Hoy --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Gastos Hoy</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-red-600 dark:text-red-500">${{ number_format($totalHoy, 2) }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Total diario</p>
                    </div>
                </div>
            </div>

            {{-- Total Mes --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Gastos del Mes</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-orange-600 dark:text-orange-500">${{ number_format($totalMes, 2) }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Total mensual</p>
                    </div>
                </div>
            </div>

            {{-- Por Categoría --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Top Categorías</p>
                        <div class="mt-2 space-y-1">
                            @forelse($porCategoria as $cat)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="truncate text-gray-500 dark:text-neutral-400 max-w-[100px]">{{ $cat['nombre'] }}</span>
                                    <span class="font-semibold text-gray-800 dark:text-neutral-200">${{ number_format($cat['total'], 0) }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-neutral-400">Sin datos</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Por Sucursal --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Por Sucursal</p>
                        <div class="mt-2 space-y-1">
                            @forelse($porSucursal as $suc)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="truncate text-gray-500 dark:text-neutral-400 max-w-[100px]">{{ $suc['nombre'] }}</span>
                                    <span class="font-semibold text-gray-800 dark:text-neutral-200">${{ number_format($suc['total'], 0) }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-neutral-400">Sin datos</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== ALERTS ===== --}}


        {{-- ===== FILTROS + TABLA ===== --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden">

            {{-- Toolbar --}}
            <div class="p-5 border-b border-gray-100 dark:border-neutral-800/80 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Listado de Gastos</h4>
                    <a href="{{ route('gastos.categorias.index') }}"
                       class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-50 dark:border-white/[0.1] dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Categorías
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    {{-- Filtros --}}
                    <form action="{{ route('gastos.index') }}" method="GET" class="flex flex-wrap gap-2 items-center" id="filtro-form">
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                               class="h-9 rounded-xl border border-gray-200 bg-gray-50 px-3 text-xs focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-neutral-800/20 dark:text-white">
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                               class="h-9 rounded-xl border border-gray-200 bg-gray-50 px-3 text-xs focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-neutral-800/20 dark:text-white">

                        <select name="categoria_id" onchange="this.form.submit()"
                                class="h-9 rounded-xl border border-gray-200 bg-gray-50 px-3 text-xs focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-neutral-800/20 dark:text-white">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                            @endforeach
                        </select>

                        <select name="sucursal_id" onchange="this.form.submit()"
                                class="h-9 rounded-xl border border-gray-200 bg-gray-50 px-3 text-xs focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-neutral-800/20 dark:text-white">
                            <option value="">Todas las sucursales</option>
                            @foreach($sucursales as $suc)
                                <option value="{{ $suc->id }}" {{ request('sucursal_id') == $suc->id ? 'selected' : '' }}>{{ $suc->nombre }}</option>
                            @endforeach
                        </select>

                        <select name="metodo" onchange="this.form.submit()"
                                class="h-9 rounded-xl border border-gray-200 bg-gray-50 px-3 text-xs focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-neutral-800/20 dark:text-white">
                            <option value="">Todos los métodos</option>
                            <option value="efectivo"      {{ request('metodo') === 'efectivo'      ? 'selected' : '' }}>Efectivo</option>
                            <option value="transferencia" {{ request('metodo') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="tarjeta"       {{ request('metodo') === 'tarjeta'       ? 'selected' : '' }}>Tarjeta</option>
                            <option value="cheque"        {{ request('metodo') === 'cheque'        ? 'selected' : '' }}>Cheque</option>
                            <option value="yappy"         {{ request('metodo') === 'yappy'         ? 'selected' : '' }}>Yappy/Nequi</option>
                        </select>

                        <button type="submit" class="h-9 rounded-xl bg-brand-500 px-4 text-xs font-medium text-white hover:bg-brand-600">Filtrar</button>

                        @if(request()->anyFilled(['fecha_desde','fecha_hasta','categoria_id','sucursal_id','metodo']))
                            <a href="{{ route('gastos.index') }}" class="h-9 flex items-center rounded-xl border border-gray-200 px-3 text-xs text-gray-500 hover:bg-gray-50 dark:border-white/[0.1] dark:text-gray-400">Limpiar</a>
                        @endif
                    </form>

                    <div x-data="{ openExport: false }" class="relative">
                        <button @click="openExport = !openExport"
                            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Exportar
                            <svg class="w-4 h-4 transition-transform" :class="openExport ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openExport" @click.outside="openExport = false"
                            class="absolute right-0 mt-2 w-48 origin-top-right rounded-xl border border-gray-100 bg-white shadow-lg dark:border-neutral-800 dark:bg-neutral-900 z-50">
                            <div class="p-1 text-xs">
                                <a href="{{ route('gastos.exportar', array_merge(request()->all(), ['formato' => 'excel'])) }}" 
                                   class="flex items-center gap-2 rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2zM14 8V3.5L18.5 8H14z"/></svg>
                                    Excel (.xlsx)
                                </a>
                                <a href="{{ route('gastos.exportar', array_merge(request()->all(), ['formato' => 'pdf'])) }}"
                                   class="flex items-center gap-2 rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                                    PDF (.pdf)
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Botón Nuevo Gasto --}}
                    <button @click="openModal()"
                            class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo Gasto
                    </button>

                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50 dark:border-neutral-800/80 dark:bg-neutral-800/20">
                            <th class="px-5 py-4 text-left font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">#</th>
                            <th class="px-5 py-4 text-left font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Categoría</th>
                            <th class="px-5 py-4 text-right font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Monto</th>
                            <th class="px-5 py-4 text-center font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Método</th>
                            <th class="px-5 py-4 text-center font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Fecha</th>
                            <th class="px-5 py-4 text-left font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Sucursal</th>
                            <th class="px-5 py-4 text-left font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Usuario</th>
                            <th class="px-5 py-4 text-center font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Estado</th>
                            <th class="px-5 py-4 text-right font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/80">
                        @forelse($gastos as $gasto)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="font-bold text-gray-800 dark:text-white">#{{ $gasto->id }}</span>
                                    @if($gasto->es_recurrente)
                                        <span class="ml-1 inline-flex items-center rounded px-1 py-0.5 text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">↺ Recurrente</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-medium text-gray-800 dark:text-white">{{ $gasto->categoria?->nombre ?? '—' }}</span>
                                    @if($gasto->descripcion)
                                        <p class="text-xs text-gray-400 truncate max-w-[150px]">{{ $gasto->descripcion }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <span class="font-bold text-red-600 dark:text-red-400">${{ number_format($gasto->monto, 2) }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $gasto->metodo_badge }}">
                                        {{ $gasto->metodo_pago_label }}
                                    </span>
                                    @if($gasto->referencia)
                                        <p class="text-[10px] text-gray-400 mt-0.5">Ref: {{ $gasto->referencia }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center text-gray-600 dark:text-gray-400">
                                    {{ $gasto->fecha->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $gasto->sucursal?->nombre ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-400 text-xs">
                                    {{ $gasto->usuario?->name ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $gasto->estado_badge }}">
                                        {{ ucfirst($gasto->estado) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-1">
                                        {{-- Ver --}}
                                        <button @click="verGasto({{ $gasto->id }}, '{{ addslashes($gasto->categoria?->nombre) }}', '{{ number_format($gasto->monto, 2) }}', '{{ $gasto->metodo_pago_label }}', '{{ $gasto->fecha->format('d/m/Y') }}', '{{ addslashes($gasto->sucursal?->nombre) }}', '{{ addslashes($gasto->usuario?->name) }}', '{{ addslashes($gasto->descripcion) }}', '{{ $gasto->referencia }}', '{{ $gasto->estado }}', '{{ $gasto->es_recurrente ? $gasto->frecuencia : '' }}')"
                                                class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-600 dark:hover:bg-white/10" title="Ver detalle">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        {{-- Editar --}}
                                        <button @click="editGasto({{ $gasto->id }}, {{ $gasto->categoria_gasto_id }}, '{{ number_format($gasto->monto, 2) }}', '{{ $gasto->metodo_pago }}', '{{ $gasto->referencia }}', '{{ $gasto->fecha->format('Y-m-d') }}', {{ $gasto->sucursal_id }}, '{{ addslashes($gasto->descripcion) }}', {{ $gasto->es_recurrente ? 'true' : 'false' }}, '{{ $gasto->frecuencia }}')"
                                                class="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-500/10" title="Editar">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        {{-- Eliminar --}}
                                        <button @click="eliminarGasto({{ $gasto->id }})"
                                                class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10" title="Eliminar">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <span>No se encontraron gastos con los filtros aplicados.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($gastos->hasPages())
                <div class="p-5 border-t border-gray-100 dark:border-neutral-800/80">
                    {{ $gastos->links() }}
                </div>
            @endif
        </div>

        {{-- ===== MODALES ===== --}}
        @include('pages.gastos._modal-gasto')
        @include('pages.gastos._modal-ver')

        {{-- Hidden forms for delete --}}
        <form id="form-eliminar-gasto" method="POST" style="display:none">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection

@push('scripts')
<script>
function gastosApp() {
    return {
        // Modal gasto
        showModal: false,
        isEditing: false,
        gastoId: null,
        formAction: '{{ route('gastos.store') }}',
        formMethod: 'POST',

        // Modal ver
        showVerModal: false,
        verData: {},

        // Fields
        categoria_gasto_id: '',
        monto: '',
        metodo_pago: 'efectivo',
        referencia: '',
        fecha: '{{ now()->format('Y-m-d') }}',
        sucursal_id: '',
        descripcion: '',
        es_recurrente: false,
        frecuencia: '',

        get requiereReferencia() {
            return ['transferencia', 'tarjeta', 'yappy'].includes(this.metodo_pago);
        },

        openModal() {
            this.isEditing = false;
            this.gastoId = null;
            this.formAction = '{{ route('gastos.store') }}';
            this.formMethod = 'POST';
            this.categoria_gasto_id = '';
            this.monto = '';
            this.metodo_pago = 'efectivo';
            this.referencia = '';
            this.fecha = '{{ now()->format('Y-m-d') }}';
            this.sucursal_id = '';
            this.descripcion = '';
            this.es_recurrente = false;
            this.frecuencia = '';
            this.showModal = true;
        },

        editGasto(id, catId, monto, metodo, referencia, fecha, sucId, desc, recurrente, frecuencia) {
            this.isEditing = true;
            this.gastoId = id;
            this.formAction = `/gastos/${id}`;
            this.formMethod = 'PUT';
            this.categoria_gasto_id = catId;
            this.monto = monto;
            this.metodo_pago = metodo;
            this.referencia = referencia;
            this.fecha = fecha;
            this.sucursal_id = sucId;
            this.descripcion = desc;
            this.es_recurrente = recurrente;
            this.frecuencia = frecuencia;
            this.showModal = true;
        },

        verGasto(id, categoria, monto, metodo, fecha, sucursal, usuario, descripcion, referencia, estado, frecuencia) {
            this.verData = { id, categoria, monto, metodo, fecha, sucursal, usuario, descripcion, referencia, estado, frecuencia };
            this.showVerModal = true;
        },

        eliminarGasto(id) {
            window.Confirm.show(
                '¿Eliminar gasto?',
                'Esta acción anulará el gasto. Si fue pagado en efectivo, el monto se revertirá en caja.',
                'Sí, eliminar',
                'Cancelar',
                () => {
                    const form = document.getElementById('form-eliminar-gasto');
                    form.action = `/gastos/${id}`;
                    form.submit();
                },
                () => {},
                { okButtonBackground: '#ef4444' }
            );
        }
    }
}
</script>
@endpush
