@extends('layouts.app')

@section('content')



<div class="space-y-6">
    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Cuentas por Pagar</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona las deudas por compras a crédito</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('cuentas-por-pagar.historial') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-colors dark:bg-neutral-800/20 dark:text-gray-300 dark:hover:bg-white/[0.05]">
                Historial de Pagos
            </a>
            <a href="{{ route('cuentas-por-pagar.vencidas') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-red-100 px-5 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-200 transition-colors dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20">
                Ver Vencidas
            </a>
            <a href="{{ route('cuentas-por-pagar.reporte') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition-colors shadow-sm">
                Reporte por Proveedor
            </a>
        </div>
    </div>

    {{-- ===== METRICAS ===== --}}
    <div class="mb-6 grid md:grid-cols-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-2xs rounded-xl overflow-hidden">
        <!-- Pendiente -->
        <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
            <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div class="grow">
                    <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Total Pendiente</p>
                    <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">${{ number_format($totalPendiente, 2) }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Total adeudado</p>
                </div>
            </div>
        </div>

        <!-- Pagado -->
        <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
            <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                <div class="grow">
                    <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Total Pagado</p>
                    <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">${{ number_format($totalPagado, 2) }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Abonos realizados</p>
                </div>
            </div>
        </div>

        <!-- Vencidas -->
        <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
            <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <div class="grow">
                    <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Cuentas Vencidas</p>
                    <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">{{ $comprasVencidas }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Plazo superado</p>
                </div>
            </div>
        </div>

        <!-- Del mes -->
        <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
            <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <div class="grow">
                    <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Créditos del Mes</p>
                    <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">{{ $comprasMes }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Generados este mes</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
        <div class="border-b border-gray-100 dark:border-neutral-800/80 px-6 py-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Listado de Compras a Crédito</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50 dark:bg-neutral-800/20">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Factura</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Proveedor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Compra</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Pago / Saldo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Vencimiento</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                    @forelse($compras as $compra)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90">
                                #{{ $compra->numero_factura }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-800 dark:text-white/90 font-medium">{{ $compra->proveedor->empresa }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-800 dark:text-white/90 font-medium">
                                ${{ number_format($compra->total, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-emerald-600 dark:text-emerald-400 font-medium">A: ${{ number_format($compra->total_pagado, 2) }}</div>
                                <div class="text-red-600 dark:text-red-400 font-semibold mt-1">S: ${{ number_format($compra->saldo_pendiente, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $compra->fecha_vencimiento ? \Carbon\Carbon::parse($compra->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $estadoColors = [
                                        'pagado' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                        'pendiente' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                        'parcial' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                        'vencido' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                    ];
                                    $badge = $estadoColors[$compra->estado_pago] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold uppercase {{ $badge }}">
                                    {{ $compra->estado_pago }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('cuentas-por-pagar.show', $compra->id) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:border-gray-700 dark:bg-neutral-800 dark:text-gray-400 transition-colors" title="Ver Detalles">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($compra->estado_pago !== 'pagado')
                                        <a href="{{ route('cuentas-por-pagar.show', [$compra->id, 'action' => 'pay']) }}" class="inline-flex items-center rounded-lg bg-emerald-600 p-2 text-white hover:bg-emerald-700 transition-colors shadow-sm" title="Abonar / Pagar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-600">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm font-medium">No hay compras a crédito registradas</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($compras->hasPages())
            <div class="border-t border-gray-100 px-6 py-4 dark:border-neutral-800/80">
                {{ $compras->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
