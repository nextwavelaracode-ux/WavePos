@extends('layouts.app')

@section('content')

    {{-- SweetAlert2 Notification --}}
    @if (session('sweet_alert'))
        @php $sa = session('sweet_alert'); @endphp
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: '{{ $sa['type'] }}',
                        title: '{{ $sa['title'] }}',
                        text: '{{ $sa['message'] }}',
                        timer: 3000,
                        showConfirmButton: false,
                    });
                });
            </script>
        @endpush
    @endif

<div class="space-y-6">
    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Cuentas por Pagar</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona las deudas por compras a crédito</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('cuentas-por-pagar.historial') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-colors dark:bg-white/[0.03] dark:text-gray-300 dark:hover:bg-white/[0.05]">
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
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
        <!-- Pendiente -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 transition-all hover:border-red-300 dark:hover:border-red-500/30">
            <div class="flex items-center justify-center w-12 h-12 bg-red-50 rounded-xl dark:bg-red-500/10 mb-4">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pendiente</span>
                <h4 class="mt-1 font-bold text-gray-800 text-2xl dark:text-white/90">${{ number_format($totalPendiente, 2) }}</h4>
            </div>
        </div>

        <!-- Pagado -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 transition-all hover:border-emerald-300 dark:hover:border-emerald-500/30">
            <div class="flex items-center justify-center w-12 h-12 bg-emerald-50 rounded-xl dark:bg-emerald-500/10 mb-4">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" /></svg>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pagado</span>
                <h4 class="mt-1 font-bold text-gray-800 text-2xl dark:text-white/90">${{ number_format($totalPagado, 2) }}</h4>
            </div>
        </div>

        <!-- Vencidas -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 transition-all hover:border-orange-300 dark:hover:border-orange-500/30">
            <div class="flex items-center justify-center w-12 h-12 bg-orange-50 rounded-xl dark:bg-orange-500/10 mb-4">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Cuentas Vencidas</span>
                <h4 class="mt-1 font-bold text-gray-800 text-2xl dark:text-white/90">{{ $comprasVencidas }}</h4>
            </div>
        </div>

        <!-- Del mes -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 transition-all hover:border-brand-300 dark:hover:border-brand-500/30">
            <div class="flex items-center justify-center w-12 h-12 bg-brand-50 rounded-xl dark:bg-brand-500/10 mb-4">
                <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Créditos del Mes</span>
                <h4 class="mt-1 font-bold text-gray-800 text-2xl dark:text-white/90">{{ $comprasMes }}</h4>
            </div>
        </div>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900">
        <div class="border-b border-gray-100 dark:border-white/[0.05] px-6 py-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Listado de Compras a Crédito</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
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
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
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
                                    <a href="{{ route('cuentas-por-pagar.show', $compra->id) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition-colors" title="Ver Detalles">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver
                                    </a>
                                    @if($compra->estado_pago !== 'pagado')
                                        <a href="{{ route('cuentas-por-pagar.show', [$compra->id, 'action' => 'pay']) }}" class="inline-flex items-center gap-1 rounded-lg bg-brand-500 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-brand-600 transition-colors shadow-sm" title="Abonar / Pagar">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            Pagar
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
            <div class="border-t border-gray-100 px-6 py-4 dark:border-white/[0.05]">
                {{ $compras->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
