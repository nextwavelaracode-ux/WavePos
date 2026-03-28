@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('cuentas.index') }}" class="rounded-xl border border-gray-200 bg-white p-2 text-gray-400 hover:bg-gray-50 dark:border-white/[0.1] dark:bg-gray-900 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Cuenta #{{ $cuenta->id }}</h2>
                <p class="text-sm text-gray-500">Venta: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $cuenta->venta->numero }}</span> · {{ $cuenta->sucursal->nombre }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            @if($cuenta->saldo_pendiente > 0)
                <button x-on:click="$dispatch('open-pago-modal', { id: {{ $cuenta->id }}, saldo: {{ $cuenta->saldo_pendiente }}, cliente: '{{ $cuenta->cliente->nombre_completo }}' })"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition-shadow shadow-sm shadow-brand-500/20">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Abonar Saldo
                </button>
            @endif
            <a href="{{ route('caja.ventas.pdf', $cuenta->venta_id) }}" target="_blank"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-white/[0.1] dark:bg-gray-900 dark:text-white transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Imprimir Factura
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- LEFT COLUMN --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Product Details --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-white/[0.05]">
                    <h3 class="font-bold text-gray-800 dark:text-white">Resumen de la Venta</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-white/[0.02]">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-gray-500">Producto</th>
                                <th class="px-5 py-3 text-center font-semibold text-gray-500">Cantidad</th>
                                <th class="px-5 py-3 text-right font-semibold text-gray-500">Precio Unit.</th>
                                <th class="px-5 py-3 text-right font-semibold text-gray-500">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                            @foreach($cuenta->venta->detalles as $detalle)
                                <tr>
                                    <td class="px-5 py-4 font-medium text-gray-800 dark:text-white">{{ $detalle->producto->nombre }}</td>
                                    <td class="px-5 py-4 text-center">{{ $detalle->cantidad }}</td>
                                    <td class="px-5 py-4 text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="px-5 py-4 text-right font-bold text-gray-800 dark:text-white">${{ number_format($detalle->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-white/[0.02]">
                           <tr class="font-bold">
                               <td colspan="3" class="px-5 py-3 text-right uppercase text-gray-500 text-xs">Total Facturado</td>
                               <td class="px-5 py-3 text-right text-lg text-gray-800 dark:text-white">${{ number_format($cuenta->venta->total, 2) }}</td>
                           </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- AR Payment History --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-white/[0.05]">
                    <h3 class="font-bold text-gray-800 dark:text-white">Historial de Cobros Recibidos</h3>
                </div>
                <div class="p-2">
                    <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-white/[0.05]">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-white/[0.02]">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-400">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-400">Usuario</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-400">Método</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-400">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                                @if($cuenta->pagos->count() > 0)
                                    @foreach($cuenta->pagos as $pago)
                                        <tr>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $pago->usuario->name }}</td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $pago->status_color }}">
                                                    {{ $pago->metodo_label }}
                                                </span>
                                                @if($pago->referencia)
                                                    <p class="text-[10px] text-gray-400">Ref: {{ $pago->referencia }}</p>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right font-black text-gray-800 dark:text-white">
                                                ${{ number_format($pago->monto, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center text-gray-400 italic">
                                            Aún no se han registrado abonos para esta cuenta.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/[0.05] dark:bg-gray-900">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-800 dark:text-white uppercase text-xs tracking-wider">Estado de Deuda</h3>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $cuenta->status_color }}">
                        {{ ucfirst($cuenta->estado) }}
                    </span>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-end border-b border-gray-100 dark:border-white/[0.05] pb-2">
                        <span class="text-sm text-gray-500">Monto Original</span>
                        <span class="text-base font-bold text-gray-800 dark:text-white">${{ number_format($cuenta->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-end border-b border-gray-100 dark:border-white/[0.05] pb-2 text-emerald-600">
                        <span class="text-sm">Total Pagado</span>
                        <span class="text-base font-bold">${{ number_format($cuenta->total_pagado, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">SALDO PENDIENTE</span>
                        <span class="text-2xl font-black {{ $cuenta->saldo_pendiente > 0 ? 'text-brand-600' : 'text-emerald-500' }}">
                            ${{ number_format($cuenta->saldo_pendiente, 2) }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-white/[0.05]">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Fecha de Vencimiento</p>
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 {{ $cuenta->estado === 'vencido' ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span class="font-bold {{ $cuenta->estado === 'vencido' ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ $cuenta->fecha_vencimiento->format('d de F, Y') }}
                        </span>
                        @if($cuenta->estado === 'vencido')
                           <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-black uppercase grow text-center">Expirado</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Client Info --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/[0.05] dark:bg-gray-900">
                <h3 class="font-bold text-gray-800 dark:text-white uppercase text-xs tracking-wider mb-4">Información del Cliente</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 shrink-0 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center font-bold text-gray-600 dark:text-gray-400">
                            {{ substr($cuenta->cliente->nombre_completo, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 dark:text-white">{{ $cuenta->cliente->nombre_completo }}</p>
                            <p class="text-xs text-gray-500">{{ $cuenta->cliente->documento_principal }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex gap-2 text-gray-600 dark:text-gray-400">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1c-5.078 0-9.263-4.185-11.728-9.015A11.042 11.042 0 013 5z" /></svg>
                            <span>{{ $cuenta->cliente->telefono ?? 'S/T' }}</span>
                        </div>
                        <div class="flex gap-2 text-gray-600 dark:text-gray-400 border-t border-gray-100 dark:border-white/[0.05] pt-2">
                            <p class="text-xs font-medium uppercase text-gray-400 tracking-tighter w-full">Crédito del Cliente</p>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Límite Total:</span>
                            <span class="font-bold text-gray-700 dark:text-gray-300">${{ number_format($cuenta->cliente->limite_credito, 2) }}</span>
                        </div>
                    </div>
                    <a href="{{ route('configuracion.usuarios') }}?buscar={{ $cuenta->cliente->documento_principal }}" {{-- Fixed link if search works on clients list --}}
                       class="mt-4 block w-full rounded-xl border border-gray-200 py-2.5 text-center text-xs font-bold text-gray-600 hover:bg-gray-50 dark:border-white/[0.1] dark:text-gray-400 transition-colors">
                        Ver Carpeta del Cliente
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PAGAMENTO MODAL (REUSED) ===== --}}
    @include('pages.cuentas._modal-pago')
@endsection
