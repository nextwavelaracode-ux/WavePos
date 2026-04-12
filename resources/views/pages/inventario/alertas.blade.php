@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Alertas de Stock" />

    <div>
        {{-- ===== HEADER ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Alertas de Stock Bajo</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Productos que necesitan reabastecimiento urgente</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventario.stock') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver al Stock
                </a>
            </div>
        </div>

        @if($alertas->count() > 0)
            {{-- Resumen --}}
            <div class="mb-6 rounded-2xl border border-orange-200 bg-orange-50 p-4 dark:border-orange-800/30 dark:bg-orange-500/5">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-orange-800 dark:text-orange-300">{{ $alertas->count() }} producto(s) con stock bajo o agotado</p>
                        <p class="text-xs text-orange-600 dark:text-orange-400 mt-0.5">Se recomienda reabastecer estos productos lo antes posible para evitar quiebres de stock.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- ===== TABLA DE ALERTAS ===== --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50 dark:bg-neutral-800/20">
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Producto</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stock Actual</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stock Mínimo</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Déficit</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categoría</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Nivel</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/80">
                        @forelse ($alertas as $producto)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 flex-shrink-0 rounded-lg bg-gray-100 dark:bg-neutral-800 overflow-hidden flex items-center justify-center border border-gray-200 dark:border-neutral-700">
                                            @if($producto->imagen_url)
                                                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="h-full w-full object-cover">
                                            @else
                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white/90">{{ $producto->nombre }}</p>
                                            <p class="text-xs text-gray-500">{{ $producto->sku ?: $producto->codigo_barras ?: 'Sin código' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if($producto->stock <= 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700 dark:bg-red-500/10 dark:text-red-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> {{ $producto->stock }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-orange-50 px-3 py-1 text-xs font-bold text-orange-700 dark:bg-orange-500/10 dark:text-orange-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-orange-500 animate-pulse"></span> {{ $producto->stock }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center text-sm text-gray-600 dark:text-gray-400">{{ $producto->stock_minimo }}</td>

                                <td class="px-5 py-4 text-center">
                                    <span class="text-sm font-semibold text-red-600 dark:text-red-400">-{{ max(0, $producto->stock_minimo - $producto->stock) }}</span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                        {{ optional($producto->categoria)->nombre ?? 'Sin Categoria' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if($producto->stock <= 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800 dark:bg-red-500/20 dark:text-red-300">
                                            AGOTADO
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-800 dark:bg-orange-500/20 dark:text-orange-300">
                                            BAJO
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('inventario.stock') }}"
                                        class="inline-flex items-center gap-1 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Reabastecer
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-emerald-500 dark:text-emerald-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-sm font-medium">¡Todo bien! No hay alertas de stock</p>
                                        <p class="text-xs text-gray-400">Todos los productos tienen stock por encima del mínimo establecido.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
