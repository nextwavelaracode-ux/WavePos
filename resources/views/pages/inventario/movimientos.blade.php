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
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al Stock
            </a>
        </div>

        {{-- ===== FILTROS ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="w-full max-w-sm">
                <input x-model="searchTerm" type="text"
                    class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 dark:text-white text-sm border border-slate-200 dark:border-gray-700 rounded-md px-4 py-2.5 transition focus:outline-none focus:border-slate-400 shadow-sm"
                    placeholder="Buscar por producto..." />
            </div>
            <select x-model="filterTipo" class="block rounded-md border border-slate-200 bg-transparent py-2.5 px-3 text-sm text-slate-700 shadow-sm focus:border-slate-400 focus:ring-0 dark:border-gray-700 dark:text-gray-300">
                <option value="">Todos los tipos</option>
                <option value="entrada">Entradas</option>
                <option value="salida">Salidas</option>
                <option value="ajuste">Ajustes</option>
                <option value="transferencia">Transferencias</option>
                <option value="venta">Ventas</option>
            </select>
        </div>

        {{-- ===== TABLA ===== --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
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
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                        @forelse ($movimientos as $mov)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors"
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
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeColors[$mov->tipo] ?? 'bg-gray-100 text-gray-600' }}">
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
        </div>
    </div>
@endsection
