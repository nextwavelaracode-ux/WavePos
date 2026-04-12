@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Movimientos de Inventario" />

    <div x-data="{
        searchTerm: '',
        filterTipo: '',
    }">
        {{-- ===== HEADER ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Historial de Movimientos</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Registro completo de todos los movimientos del inventario</p>
            </div>
            <a href="{{ route('inventario.stock') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al Stock
            </a>
        </div>

        {{-- ===== KPI CARDS ===== --}}
        <div class="mb-6 grid md:grid-cols-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-2xs rounded-xl overflow-hidden">
            <!-- Total -->
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Total Movimientos</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">{{ $totalMovimientos }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Histórico completo</p>
                    </div>
                </div>
            </div>

            <!-- Entradas -->
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" /></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Entradas</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-emerald-600 dark:text-emerald-500">{{ $totalEntradas }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Incrementos de stock</p>
                    </div>
                </div>
            </div>

            <!-- Salidas -->
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" /></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Salidas y Ventas</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-red-600 dark:text-red-500">{{ $totalSalidas }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Reducciones de stock</p>
                    </div>
                </div>
            </div>

            <!-- Ajustes -->
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Ajustes</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-amber-600 dark:text-amber-500">{{ $totalAjustes }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Correcciones manuales</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== FILTROS ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="w-full max-w-sm">
                <input x-model="searchTerm" type="text"
                    class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 dark:text-white text-sm border border-slate-200 dark:border-neutral-700 rounded-md px-4 py-2.5 transition focus:outline-none focus:border-slate-400 shadow-sm"
                    placeholder="Buscar por producto..." />
            </div>
            <select x-model="filterTipo" class="block rounded-md border border-slate-200 bg-transparent py-2.5 px-3 text-sm text-slate-700 shadow-sm focus:border-slate-400 focus:ring-0 dark:border-neutral-700 dark:text-gray-300">
                <option value="">Todos los tipos</option>
                <option value="entrada">Entradas</option>
                <option value="salida">Salidas</option>
                <option value="ajuste">Ajustes</option>
                <option value="transferencia">Transferencias</option>
                <option value="venta">Ventas</option>
            </select>
        </div>

        {{-- ===== TABLA ===== --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50 dark:bg-neutral-800/20">
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">ID</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Producto</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cantidad</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stock Anterior</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stock Nuevo</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Usuario</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/80">
                        @forelse ($movimientos as $mov)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors"
                                x-show="(searchTerm === '' || '{{ strtolower(optional($mov->producto)->nombre ?? '') }}'.includes(searchTerm.toLowerCase())) &&
                                        (filterTipo === '' || '{{ $mov->tipo }}' === filterTipo)
                                        @if(request('producto_id')) && true @endif"
                                @if(request('producto_id') && $mov->producto_id != request('producto_id'))
                                    style="display:none"
                                @endif
                            >
                                <td class="px-5 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $mov->id }}</td>
                                <td class="px-5 py-4">
                                    <p class="font-medium text-gray-800 dark:text-white/90">{{ optional($mov->producto)->nombre ?? '—' }}</p>
                                    <p class="text-xs text-gray-500">{{ $mov->motivo_label }}</p>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $badgeColors = [
                                            'entrada' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                            'salida' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                            'ajuste' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                            'transferencia' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                            'venta' => 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeColors[$mov->tipo] ?? 'bg-gray-100 text-gray-600 dark:bg-neutral-800 dark:text-gray-300' }}">
                                        {{ $mov->tipo_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="font-semibold {{ in_array($mov->tipo, ['entrada', 'ajuste']) ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ in_array($mov->tipo, ['entrada']) ? '+' : ($mov->tipo === 'salida' || $mov->tipo === 'venta' ? '-' : '±') }}{{ $mov->cantidad }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center text-gray-600 dark:text-gray-400">{{ $mov->stock_anterior }}</td>
                                <td class="px-5 py-4 text-center font-semibold text-gray-800 dark:text-white/90">{{ $mov->stock_nuevo }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ optional($mov->usuario)->name ?? 'Sistema' }}</td>
                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $mov->fecha->format('d/m/Y') }}</td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-[200px] truncate">{{ $mov->observaciones ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-600">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <p class="text-sm font-medium">No hay movimientos registrados</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if ($movimientos->hasPages())
                <div class="border-t border-gray-100 px-6 py-4 dark:border-neutral-800/80">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
