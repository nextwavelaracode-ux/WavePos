@extends('layouts.app')

@section('title', 'Facturas Electrónicas DIAN')

@section('content')
<div class="p-4 md:p-6 mt-2">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" class="text-brand-500">
                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Facturas Electrónicas DIAN
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Historial de facturas enviadas y validadas mediante Factus.</p>
        </div>
        <a href="{{ route('facturacion.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-semibold text-sm px-5 py-2.5 transition shadow-sm">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nueva Factura
        </a>
    </div>

    {{-- Flash Alerts --}}
    @if(session('success'))
    <div class="mb-4 flex items-center gap-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 flex items-center gap-3 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-300">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="flex-shrink-0"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Tabla --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/80 border-b border-gray-100 dark:border-gray-700 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <th class="px-4 py-3 font-semibold">N° DIAN</th>
                        <th class="px-4 py-3 font-semibold">Código Ref.</th>
                        <th class="px-4 py-3 font-semibold">Cliente</th>
                        <th class="px-4 py-3 font-semibold">Documento</th>
                        <th class="px-4 py-3 font-semibold">Total</th>
                        <th class="px-4 py-3 font-semibold">Estado</th>
                        <th class="px-4 py-3 font-semibold">Fecha</th>
                        <th class="px-4 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($facturas as $factura)
                    <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/40 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm font-bold text-brand-600 dark:text-brand-400">
                                {{ $factura->numero ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">
                            POS-{{ $factura->venta_id }}
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $factura->venta->cliente->nombre ?? 'Consumidor Final' }}
                            </p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                            {{ $factura->venta->cliente->documento ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                ${{ number_format($factura->total, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($factura->status === 'Validado')
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 text-xs font-semibold px-2.5 py-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Validado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 text-xs font-semibold px-2.5 py-1">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> {{ $factura->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            {{ $factura->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('facturacion.show', $factura->id) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-brand-50 dark:bg-brand-900/30 border border-brand-200 dark:border-brand-700 text-brand-600 dark:text-brand-400 px-3 py-1.5 text-xs font-semibold hover:bg-brand-100 transition">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Ver
                                </a>
                                @if($factura->qr)
                                <a href="{{ $factura->qr }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-600 dark:text-emerald-400 px-3 py-1.5 text-xs font-semibold hover:bg-emerald-100 transition">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    DIAN
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center">
                            <div class="flex flex-col items-center text-gray-400 dark:text-gray-500">
                                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="mb-4 opacity-40">
                                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"/>
                                </svg>
                                <p class="text-base font-semibold text-gray-700 dark:text-gray-300">No hay facturas electrónicas registradas</p>
                                <p class="text-sm mt-1">Activa el toggle "Factura Electrónica API" al cobrar una venta en el POS.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($facturas->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $facturas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
