@extends('layouts.app')

@php
    $title = 'Detalle de Cuenta por Pagar';
@endphp

@section('content')
<div class="mx-auto max-w-7xl" x-data="{ pagoModalOpen: {{ request('action') === 'pay' ? 'true' : 'false' }}, metodoPago: '' }">



    {{-- ── Breadcrumb & Top Bar ─────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    Deuda de Factura <span class="text-brand-600 dark:text-brand-400 font-mono">#{{ $compra->numero_factura }}</span>
                </h2>
                @php
                    $estadoColors = [
                        'pagado' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                        'pendiente' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20',
                        'parcial' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
                        'vencido' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20',
                    ];
                    $badge = $estadoColors[$compra->estado_pago] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                    $dotColor = match($compra->estado_pago) {
                        'pagado' => 'bg-emerald-500',
                        'pendiente' => 'bg-amber-500',
                        'parcial' => 'bg-blue-500',
                        'vencido' => 'bg-red-500',
                        default => 'bg-gray-500',
                    };
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $badge }} capitalize">
                    <span class="h-1.5 w-1.5 rounded-full {{ $dotColor }}"></span> 
                    {{ $compra->estado_pago }}
                </span>
            </div>
            <nav class="mt-2">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li><a class="font-medium hover:text-brand-500 transition-colors" href="{{ route('dashboard') }}">Dashboard /</a></li>
                    <li><a class="font-medium hover:text-brand-500 transition-colors" href="{{ route('cuentas-por-pagar.index') }}">Cuentas por Pagar /</a></li>
                    <li class="font-medium text-brand-600 dark:text-brand-400">Detalle</li>
                </ol>
            </nav>
        </div>

        {{-- ── Botones de acción rápida ───────────────────────────────────── --}}
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('cuentas-por-pagar.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-gray-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>

            @php $saldoReal = $compra->total - $compra->total_pagado; @endphp
            @if($saldoReal > 0)
                <button type="button" @click="pagoModalOpen = true"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-brand-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Abonar a Deuda
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
            <div class="flex items-center gap-3">
                <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-500/20 dark:bg-red-500/10 flex gap-4">
            <svg class="h-6 w-6 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <h5 class="text-sm font-semibold text-red-800 dark:text-red-400">Hay errores en el pago:</h5>
                <ul class="mt-1 list-inside list-disc text-sm text-red-700 dark:text-red-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ── Grid Content Layout (8/4) ───────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start mt-4">

        {{-- COLUMNA IZQUIERDA: Detalles Compra y Abonos --}}
        <div class="flex flex-col gap-6 lg:col-span-8">
            
            {{-- Info Venta / Proveedor --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden">
                <div class="bg-gray-50 px-6 py-5 border-b border-gray-100 dark:bg-neutral-800/40 dark:border-neutral-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Entidad Acreedora</p>
                        <h3 class="text-xl font-bold flex items-center gap-2 text-gray-900 dark:text-white">
                            {{ $compra->proveedor->empresa }}
                        </h3>
                        <p class="text-sm font-mono text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1.5">
                            ID: {{ $compra->proveedor->ruc }}
                        </p>
                    </div>
                    <div class="text-left md:text-right bg-white md:bg-transparent p-3 rounded-xl border border-gray-100 md:border-none md:p-0 dark:border-neutral-800 dark:bg-neutral-800/80 md:dark:bg-transparent">
                        <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">Deuda Base Global</p>
                        <p class="text-2xl font-black tabular-nums text-gray-900 dark:text-white">
                            ${{ number_format($compra->total, 2) }}
                        </p>
                    </div>
                </div>

                <div class="px-6 py-5 grid grid-cols-2 lg:grid-cols-4 gap-4 bg-white dark:bg-transparent">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-500">Documento / Compra</p>
                        <a href="{{ route('compras.show', $compra->id) }}" class="mt-1 flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-500 hover:underline dark:text-brand-400">
                            #{{ str_pad($compra->id, 5, '0', STR_PAD_LEFT) }} <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-500">Sucursal</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $compra->sucursal->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-500">Registrado Por</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ optional($compra->usuario)->name ?? 'Administrador' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-500">Expedición</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $compra->fecha_compra->format('d M, Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Historial de Pagos --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-neutral-800/80 dark:bg-neutral-800/20 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                        Abonos y Transferencias a Proveedor
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-neutral-800/40 border-b border-gray-100 dark:border-neutral-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500">Fecha Control</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500">Método Operación</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500">Referencia</th>
                                <th class="px-6 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-500">Importe Abonado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-neutral-700/60">
                            @forelse($compra->pagos as $pago)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d M, Y') }}</p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded bg-gray-100 px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:bg-neutral-800 dark:text-gray-300">
                                            {{ str_replace('_', ' ', $pago->metodo_pago) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 font-mono text-xs text-gray-500 dark:text-gray-400">
                                        {{ $pago->referencia ?? 'S/R' }}
                                    </td>
                                    <td class="px-6 py-4 text-right tabular-nums">
                                        <span class="inline-block rounded-lg bg-emerald-50 px-2.5 py-1 text-sm font-black text-emerald-600 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                                            +${{ number_format($pago->monto, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        <p class="font-medium text-gray-500 dark:text-gray-400">Aún no se han registrado abonos o salidas de dinero bajo este concepto.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Elementos o Items original (preview) --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-neutral-800/80 dark:bg-neutral-800/20">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Artículos en Factura de Compra
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-neutral-800/40 border-b border-gray-100 dark:border-neutral-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500">Producto</th>
                                <th class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-widest text-gray-500">Cant.</th>
                                <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-500">Costo Unit</th>
                                <th class="px-6 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-500">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-neutral-700/60">
                            @foreach($compra->detalles as $detalle)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                    <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $detalle->producto->nombre }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $detalle->cantidad }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums text-gray-600 dark:text-gray-400">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="px-6 py-3 text-right tabular-nums font-bold text-gray-900 dark:text-white">${{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: Totales de Saldo --}}
        <div class="flex flex-col gap-6 lg:col-span-4 relative">
            <div class="sticky top-6 flex flex-col gap-6">

                {{-- Status Card (Big Summary) --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 border-t-4 {{ $saldoReal > 0 ? ($compra->estado_pago === 'vencido' ? 'border-t-red-500' : 'border-t-amber-500') : 'border-t-emerald-500' }}">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Monto a Liquidar</p>
                    <h3 class="text-4xl font-black tabular-nums tracking-tight {{ $saldoReal > 0 ? ($compra->estado_pago === 'vencido' ? 'text-red-600 dark:text-red-500' : 'text-brand-600 dark:text-brand-500') : 'text-emerald-600 dark:text-emerald-500' }}">
                        ${{ number_format($saldoReal, 2) }}
                    </h3>
                    <p class="text-xs font-semibold text-gray-500 mt-1">Saldo o Deuda Pendiente</p>
                    
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-neutral-800 space-y-4">
                        <div class="flex justify-between items-end border-b border-gray-50 dark:border-neutral-800/50 pb-2">
                            <span class="text-sm text-gray-500">Monto Base de Factura</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-white tabular-nums">${{ number_format($compra->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-end border-b border-gray-50 dark:border-neutral-800/50 pb-2 text-emerald-600 dark:text-emerald-400">
                            <span class="text-sm font-medium">Liquidado (Abonos)</span>
                            <span class="text-sm font-bold tabular-nums">-${{ number_format($compra->total_pagado, 2) }}</span>
                        </div>
                        <div class="pt-2">
                            <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-neutral-800">
                                @php
                                    $porcentaje = $compra->total > 0 ? min(100, max(0, ($compra->total_pagado / $compra->total) * 100)) : 0;
                                @endphp
                                <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <p class="text-[10px] font-bold text-gray-500 text-right mt-1">{{ number_format($porcentaje, 1) }}% de la obligación cubierta</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl bg-gray-50 p-4 border border-gray-100 dark:bg-neutral-800/40 dark:border-neutral-700">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Cierre Previsto</p>
                                <p class="text-sm font-bold flex items-center gap-1.5 {{ $compra->estado_pago === 'vencido' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                    <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $compra->fecha_vencimiento ? \Carbon\Carbon::parse($compra->fecha_vencimiento)->format('d M, Y') : 'Libre (Contado)' }}
                                </p>
                            </div>
                            @if($compra->estado_pago === 'vencido')
                                <span class="shrink-0 bg-red-100 text-red-600 px-2.5 py-1 rounded border border-red-200 text-[10px] font-black uppercase tracking-widest dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400">Expiró</span>
                            @endif
                        </div>
                    </div>

                    @if($saldoReal <= 0)
                        <div class="mt-6 rounded-xl bg-emerald-50 p-4 text-center border border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20">
                            <span class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 dark:text-emerald-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Obligación Cancelada
                            </span>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>

    <!-- Modal Registrar Pago -->
    <div x-show="pagoModalOpen" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity" x-transition x-cloak>
        <div @click.outside="pagoModalOpen = false" class="w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 md:scale-95" x-transition:enter-end="opacity-100 translate-y-0 md:scale-100">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-neutral-800 flex justify-between items-center bg-gray-50/50 dark:bg-white/[0.02]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Abonar Deuda a Proveedor
                </h3>
                <button @click="pagoModalOpen = false" class="text-gray-400 hover:bg-gray-100 hover:text-gray-600 w-8 h-8 rounded-full flex items-center justify-center transition-colors dark:hover:bg-neutral-800 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form action="{{ route('cuentas-por-pagar.store_pago', $compra->id) }}" method="POST" class="p-6">
                @csrf
                <div class="mb-5 bg-brand-50 rounded-xl p-4 border border-brand-100 dark:bg-brand-500/10 dark:border-brand-500/20 grid grid-cols-2 gap-4 items-center">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-brand-600/70 dark:text-brand-400/70 mb-0.5">Saldo Máximo</p>
                        <p class="text-xl font-black text-brand-700 dark:text-brand-400tabular-nums">${{ number_format($saldoReal, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-brand-600/70 dark:text-brand-400/70 mb-0.5">Proveedor</p>
                        <p class="text-sm font-semibold text-brand-700 dark:text-brand-400 truncate">{{ $compra->proveedor->empresa }}</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Importe a Liquidar</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">$</span>
                            <input type="number" step="0.01" min="0.01" max="{{ $saldoReal }}" name="monto" value="{{ $saldoReal }}" required
                                class="w-full rounded-xl border border-gray-300 bg-white py-3 pl-8 pr-4 text-sm font-semibold text-gray-900 shadow-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Forma de Pago</label>
                            <select name="metodo_pago" x-model="metodoPago" required
                                class="w-full rounded-xl border border-gray-300 bg-white py-3 px-4 text-sm font-medium text-gray-900 shadow-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-all">
                                <option value="" disabled selected>— Elegir —</option>
                                <option value="efectivo">Efectivo 💵</option>
                                <option value="transferencia">Transferencia 🏦</option>
                                <option value="cheque">Cheque 📝</option>
                                <option value="yappy">Yappy 📱</option>
                                <option value="nequi">Nequi 💸</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Fecha Operación</label>
                            <input type="date" name="fecha_pago" value="{{ date('Y-m-d') }}" required
                                class="w-full rounded-xl border border-gray-300 bg-white py-3 px-4 text-sm font-medium text-gray-900 shadow-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-all">
                        </div>
                    </div>

                    <div x-show="['transferencia', 'cheque', 'yappy', 'nequi'].includes(metodoPago)" x-collapse>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Comprobante / Referencia <span class="text-red-500">*</span></label>
                        <input type="text" name="referencia" placeholder="# Voucher, ACH, Auto..." :required="['transferencia', 'cheque', 'yappy', 'nequi'].includes(metodoPago)"
                            class="w-full rounded-xl border border-gray-300 bg-white py-3 px-4 text-sm text-gray-900 shadow-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-all">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Notas Adicionales (Opcional)</label>
                        <textarea name="observaciones" rows="2" placeholder="Agrega notas descriptivas..."
                            class="w-full rounded-xl border border-gray-300 bg-white py-3 px-4 text-sm text-gray-900 shadow-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-white transition-all"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex gap-3 pt-6 border-t border-gray-100 dark:border-neutral-800">
                    <button type="button" @click="pagoModalOpen = false" class="flex-1 rounded-xl border border-gray-200 bg-white py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 rounded-xl bg-brand-600 py-3 text-sm font-bold text-white hover:bg-brand-500 shadow-md transition-all">
                        Efectuar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
