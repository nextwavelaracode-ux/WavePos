@extends('layouts.app')

@php
    $title = 'Detalle de Cuenta por Cobrar';
@endphp

@section('content')
<div class="mx-auto max-w-7xl">

    {{-- ── Breadcrumb & Top Bar ─────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    Cuenta <span class="text-brand-600 dark:text-brand-400">#{{ str_pad($cuenta->id, 5, '0', STR_PAD_LEFT) }}</span>
                </h2>
                <span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $cuenta->status_color }} capitalize">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span> 
                    {{ $cuenta->estado }}
                </span>
            </div>
            <nav class="mt-2">
                <ol class="flex items-center gap-2 text-sm text-gray-500">
                    <li><a class="font-medium hover:text-brand-500 transition-colors" href="{{ route('dashboard') }}">Dashboard /</a></li>
                    <li><a class="font-medium hover:text-brand-500 transition-colors" href="{{ route('cuentas.index') }}">Cuentas X Cobrar /</a></li>
                    <li class="font-medium text-brand-600 dark:text-brand-400">Detalle</li>
                </ol>
            </nav>
        </div>

        {{-- ── Botones de acción rápida ───────────────────────────────────── --}}
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('cuentas.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white dark:focus:ring-offset-neutral-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>

            <a href="{{ route('caja.ventas.pdf', $cuenta->venta_id) }}" target="_blank"
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Imprimir Venta
            </a>

            @if($cuenta->saldo_pendiente > 0)
                <button type="button" x-data x-on:click="$dispatch('open-pago-modal', { id: {{ $cuenta->id }}, saldo: {{ $cuenta->saldo_pendiente }}, cliente: '{{ $cuenta->cliente->nombre_completo }}' })"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-brand-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Abonar al Saldo
                </button>
            @endif
        </div>
    </div>

    {{-- ── Contenido Principal (División 8/4) ───────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start mt-4">

        {{-- COLUMNA IZQUIERDA: Detalles de la Venta e Historial de Pagos --}}
        <div class="flex flex-col gap-6 lg:col-span-8">
            
            {{-- Header Venta Asociada --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 dark:bg-neutral-800/20 rounded-bl-full z-0"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-4 dark:border-neutral-800/80">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Documento Respaldo</p>
                        <h3 class="text-xl font-bold flex items-center gap-2 text-gray-900 dark:text-white">
                            Factura de Venta / POS: <span class="text-brand-600 dark:text-brand-400 font-mono">{{ $cuenta->venta->numero }}</span>
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span> 
                            {{ $cuenta->sucursal->nombre }}
                        </p>
                    </div>
                    <div class="text-left md:text-right bg-gray-50 md:bg-transparent p-3 rounded-xl border border-gray-100 md:border-none md:p-0 dark:border-neutral-800 dark:bg-neutral-800/40 md:dark:bg-transparent">
                        <p class="text-xs uppercase tracking-wider font-semibold text-gray-500 dark:text-gray-400">Total Facturado</p>
                        <p class="text-2xl font-black tabular-nums text-gray-900 dark:text-white">
                            ${{ number_format($cuenta->venta->total, 2) }}
                        </p>
                    </div>
                </div>

                {{-- Tabla de Productos Facturados --}}
                <div class="mt-4 pt-2">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">Conceptos Facturados</h4>
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-neutral-700">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-neutral-800/40">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500">Producto</th>
                                    <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-widest text-gray-500">Cantidad</th>
                                    <th class="px-4 py-2.5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-500">Precio Unit.</th>
                                    <th class="px-4 py-2.5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-neutral-700/60 bg-white dark:bg-transparent">
                                @foreach($cuenta->venta->detalles as $detalle)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $detalle->producto->nombre }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $detalle->cantidad }}</td>
                                        <td class="px-4 py-3 text-right tabular-nums text-gray-600 dark:text-gray-400">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td class="px-4 py-3 text-right tabular-nums font-bold text-gray-900 dark:text-white">${{ number_format($detalle->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- AR Payment History (Abonos) --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-neutral-800/80 dark:bg-neutral-800/20 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Historial de Abonos Registrados
                    </h3>
                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded-md dark:bg-neutral-800 dark:text-gray-400">{{ $cuenta->pagos->count() }} Registros</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-neutral-800/40 border-b border-gray-100 dark:border-neutral-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500">Fecha Control</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500">Cajero / Cajera</th>
                                <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-widest text-gray-500">Comprobante</th>
                                <th class="px-6 py-3 text-right text-[10px] font-bold uppercase tracking-widest text-gray-500">Porción Abonada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-neutral-700/60">
                            @forelse($cuenta->pagos as $pago)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $pago->created_at->format('d M, Y') }}</p>
                                        <p class="text-[11px] font-mono text-gray-400">{{ $pago->created_at->format('h:i A') }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $pago->usuario->name }}</td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded bg-gray-100 px-2 py-1 text-[11px] font-bold text-gray-700 dark:bg-neutral-800 dark:text-gray-300 {{ $pago->status_color }}">
                                            {{ $pago->metodo_label }}
                                        </span>
                                        @if($pago->referencia)
                                            <p class="text-[10px] text-gray-400 mt-1 font-mono">Ref: {{ $pago->referencia }}</p>
                                        @endif
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
                                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-neutral-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <p class="font-medium text-gray-500 dark:text-gray-400">No se han registrado abonos o pagos a esta cuenta.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: Estado y Cliente --}}
        <div class="flex flex-col gap-6 lg:col-span-4">
            
            {{-- Status Card (Big Summary) --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 border-t-4 {{ $cuenta->saldo_pendiente > 0 ? ($cuenta->estado === 'vencido' ? 'border-t-red-500' : 'border-t-amber-500') : 'border-t-emerald-500' }}">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Balance Actual</p>
                <h3 class="text-4xl font-black tabular-nums tracking-tight {{ $cuenta->saldo_pendiente > 0 ? ($cuenta->estado === 'vencido' ? 'text-red-600 dark:text-red-500' : 'text-brand-600 dark:text-brand-500') : 'text-emerald-600 dark:text-emerald-500' }}">
                    ${{ number_format($cuenta->saldo_pendiente, 2) }}
                </h3>
                <p class="text-xs font-semibold text-gray-500 mt-1">Deuda Pendiente de Pago</p>
                
                <div class="mt-6 pt-5 border-t border-gray-100 dark:border-neutral-800 space-y-4">
                    <div class="flex justify-between items-end border-b border-gray-50 dark:border-neutral-800/50 pb-2">
                        <span class="text-sm text-gray-500">Monto Base Acordado</span>
                        <span class="text-sm font-bold text-gray-800 dark:text-white tabular-nums">${{ number_format($cuenta->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-end border-b border-gray-50 dark:border-neutral-800/50 pb-2 text-emerald-600 dark:text-emerald-400">
                        <span class="text-sm font-medium">Recuperado (Abonos)</span>
                        <span class="text-sm font-bold tabular-nums">-${{ number_format($cuenta->total_pagado, 2) }}</span>
                    </div>
                    <div class="pt-2">
                        <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-neutral-800">
                            @php
                                $porcentaje = $cuenta->total > 0 ? min(100, max(0, ($cuenta->total_pagado / $cuenta->total) * 100)) : 0;
                            @endphp
                            <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ $porcentaje }}%"></div>
                        </div>
                        <p class="text-[10px] font-bold text-gray-500 text-right mt-1">{{ number_format($porcentaje, 1) }}% Pagado</p>
                    </div>
                </div>

                <div class="mt-6 rounded-xl bg-gray-50 p-4 border border-gray-100 dark:bg-neutral-800/40 dark:border-neutral-700">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Cierre Previsto</p>
                            <p class="text-sm font-bold flex items-center gap-1.5 {{ $cuenta->estado === 'vencido' ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $cuenta->fecha_vencimiento->format('d M, Y') }}
                            </p>
                        </div>
                        @if($cuenta->estado === 'vencido')
                            <span class="shrink-0 bg-red-100 text-red-600 px-2.5 py-1 rounded border border-red-200 text-[10px] font-black uppercase tracking-widest dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400">Expiró</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Client Info --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Titular de la Cuenta
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-12 w-12 shrink-0 rounded-full bg-brand-50 flex items-center justify-center font-bold text-brand-600 text-lg border border-brand-100 dark:bg-brand-500/10 dark:border-brand-500/20 dark:text-brand-400">
                            {{ substr($cuenta->cliente->nombre_completo, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white leading-tight">{{ $cuenta->cliente->nombre_completo }}</p>
                            <p class="text-xs font-mono text-gray-500 mt-0.5">ID: {{ $cuenta->cliente->documento_principal }}</p>
                        </div>
                    </div>
                    
                    <div class="rounded-xl border border-gray-100 p-3 bg-gray-50/50 dark:bg-neutral-800/30 dark:border-neutral-800 space-y-2.5">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>{{ $cuenta->cliente->telefono ?? 'Sin teléfono' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="break-all">{{ $cuenta->cliente->email ?? 'Sin correo' }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center py-2 px-1 border-t border-gray-100 dark:border-neutral-800">
                        <span class="text-xs uppercase font-bold text-gray-500 space-x-1">Cupo Asignado</span>
                        <span class="font-bold text-gray-800 dark:text-white tabular-nums">${{ number_format($cuenta->cliente->limite_credito, 2) }}</span>
                    </div>

                    <a href="{{ route('clientes.show', $cuenta->cliente->id) }}"
                       class="mt-1 flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white py-2.5 text-xs font-bold text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700">
                        Abrir Ficha de Cliente
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

        </div>

    </div>

    {{-- ===== PAGAMENTO MODAL (REUSED) ===== --}}
    @include('pages.cuentas._modal-pago')

</div>
@endsection
