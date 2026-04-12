@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Reporte Consolidado por Proveedor</h2>
            <div class="flex items-center gap-2 mt-1">
                <a href="{{ route('cuentas-por-pagar.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 transition-colors">Cuentas por Pagar</a>
                <span class="text-sm text-gray-400">/</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">Reporte</span>
            </div>
        </div>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
        <div class="border-b border-gray-100 dark:border-neutral-800/80 px-6 py-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Resumen de Deudas Activas e Históricas</h4>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50 dark:bg-neutral-800/20">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Proveedor</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cant. Compras</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Comprado (Crédito)</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Pagado</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Saldo Pendiente</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                    @php
                        $grandTotalComprado = 0;
                        $grandTotalPagado = 0;
                        $grandTotalPendiente = 0;
                    @endphp
                    @forelse($proveedores as $proveedor)
                        @php
                            $grandTotalComprado += $proveedor->total_comprado;
                            $grandTotalPagado += $proveedor->total_pagado;
                            $grandTotalPendiente += $proveedor->saldo_pendiente;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800 dark:text-white/90">{{ $proveedor->empresa }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">RUC: {{ $proveedor->ruc }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2rem] rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                                    {{ $proveedor->total_compras }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90 text-right">
                                ${{ number_format($proveedor->total_comprado, 2) }}
                            </td>
                            <td class="px-6 py-4 text-emerald-600 dark:text-emerald-400 font-medium text-right">
                                ${{ number_format($proveedor->total_pagado, 2) }}
                            </td>
                            <td class="px-6 py-4 text-red-600 dark:text-red-400 font-bold text-base text-right">
                                ${{ number_format($proveedor->saldo_pendiente, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-600">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm font-medium">No se encontraron registros consolidados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($proveedores->count() > 0)
                <tfoot class="border-t-2 border-gray-200 dark:border-white/[0.1] bg-gray-50 dark:bg-neutral-800/20">
                    <tr>
                        <th class="px-6 py-4 font-bold text-gray-800 dark:text-white/90 text-right uppercase text-xs" colspan="2">TOTAL GENERAL:</th>
                        <th class="px-6 py-4 font-bold text-gray-800 dark:text-white/90 text-right">${{ number_format($grandTotalComprado, 2) }}</th>
                        <th class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400 text-right">${{ number_format($grandTotalPagado, 2) }}</th>
                        <th class="px-6 py-4 font-bold text-red-600 dark:text-red-400 text-lg text-right">${{ number_format($grandTotalPendiente, 2) }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
    </div>
</div>
@endsection
