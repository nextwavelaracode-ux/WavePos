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

<div class="space-y-6" x-data="{ pagoModalOpen: {{ request('action') === 'pay' ? 'true' : 'false' }}, metodoPago: '' }">
    
    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                Detalle de Cuenta: #{{ $compra->numero_factura }}
            </h2>
            <div class="flex items-center gap-2 mt-1">
                <a href="{{ route('cuentas-por-pagar.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 transition-colors">Cuentas por Pagar</a>
                <span class="text-sm text-gray-400">/</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">Detalle de Deuda</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
            <div class="flex items-center gap-3">
                <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-500/20 dark:bg-red-500/10">
            <div class="flex items-start gap-3">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h5 class="text-sm font-semibold text-red-800 dark:text-red-200">Se encontraron errores:</h5>
                    <ul class="mt-1 ml-4 list-disc text-sm text-red-700 dark:text-red-300">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        
        <!-- Información de Deuda -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 col-span-1 lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Resumen de la Deuda</h3>
                    @php
                        $estadoColors = [
                            'pagado' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                            'pendiente' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                            'parcial' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                            'vencido' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                        ];
                        $badge = $estadoColors[$compra->estado_pago] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $badge }}">
                        {{ $compra->estado_pago }}
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Proveedor</p>
                        <p class="mt-1 text-gray-800 dark:text-white/90 font-medium">{{ $compra->proveedor->empresa }} <span class="text-gray-500 text-xs">({{ $compra->proveedor->ruc }})</span></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sucursal</p>
                        <p class="mt-1 text-gray-800 dark:text-white/90 font-medium">{{ $compra->sucursal->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha Vencimiento</p>
                        <p class="mt-1 text-gray-800 dark:text-white/90 font-medium">{{ $compra->fecha_vencimiento ? \Carbon\Carbon::parse($compra->fecha_vencimiento)->format('d/m/Y') : 'No definida' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Registrada por</p>
                        <p class="mt-1 text-gray-800 dark:text-white/90 font-medium">{{ optional($compra->usuario)->name ?? 'Admin' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <a href="{{ route('compras.show', $compra->id) }}" class="inline-flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors dark:bg-white/[0.03] dark:text-gray-300 dark:hover:bg-white/[0.05]">
                    Ver Detalle de Compra Original
                </a>
            </div>
        </div>

        <!-- Saldos -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 col-span-1">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 mb-6">Saldos</h3>
            <div class="flex flex-col gap-4">
                <div class="flex justify-between items-center bg-gray-50 dark:bg-white/[0.02] p-3 rounded-xl border border-gray-100 dark:border-white/[0.05]">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Compra</span>
                    <span class="font-bold text-gray-800 dark:text-white/90">${{ number_format($compra->total, 2) }}</span>
                </div>
                <div class="flex justify-between items-center bg-emerald-50 dark:bg-emerald-500/10 p-3 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                    <span class="text-sm font-medium text-emerald-700 dark:text-emerald-400">Total Pagado</span>
                    <span class="font-bold text-emerald-700 dark:text-emerald-400">${{ number_format($compra->total_pagado, 2) }}</span>
                </div>
                @php $saldoReal = $compra->total - $compra->total_pagado; @endphp
                <div class="flex justify-between items-center bg-red-50 dark:bg-red-500/10 p-3 rounded-xl border border-red-100 dark:border-red-500/20">
                    <span class="text-sm font-bold text-red-700 dark:text-red-400">Saldo Pendiente</span>
                    <span class="font-bold text-xl text-red-700 dark:text-red-400">${{ number_format($saldoReal, 2) }}</span>
                </div>

                @if($saldoReal > 0)
                    <button @click="pagoModalOpen = true" class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-5 py-3 text-sm font-bold text-white hover:bg-brand-600 transition-colors shadow-sm w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Abonar / Pagar
                    </button>
                @else
                    <button disabled class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-5 py-3 text-sm font-bold text-white opacity-80 cursor-not-allowed w-full shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Cuenta Saldada
                    </button>
                @endif
            </div>
        </div>

    </div>

    <!-- Historial de Pagos y Productos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        
        <!-- Pestaña Historial de Pagos -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900">
            <div class="border-b border-gray-100 dark:border-white/[0.05] px-6 py-4">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Historial de Pagos</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Monto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Método</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ref.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                        @forelse($compra->pagos as $pago)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-emerald-600 dark:text-emerald-400 font-semibold">
                                    +${{ number_format($pago->monto, 2) }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90 uppercase text-xs">
                                    {{ str_replace('_', ' ', $pago->metodo_pago) }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $pago->referencia ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Aún no se han registrado abonos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Productos de la Compra -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900">
            <div class="border-b border-gray-100 dark:border-white/[0.05] px-6 py-4">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Artículos Comprados</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Producto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cant.</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Costo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                        @foreach($compra->detalles as $detalle)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90">
                                    {{ $detalle->producto->nombre }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ $detalle->cantidad }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    ${{ number_format($detalle->precio_unitario, 2) }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90">
                                    ${{ number_format($detalle->subtotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Registrar Pago -->
    <div x-show="pagoModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm transition-opacity" x-transition>
        <div @click.outside="pagoModalOpen = false" class="w-full max-w-md rounded-2xl bg-white shadow-xl dark:border-white/[0.05] dark:bg-gray-900 overflow-hidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-white/[0.05] flex justify-between items-center bg-gray-50 dark:bg-white/[0.02]">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Registrar Pago a Proveedor</h3>
                <button @click="pagoModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form action="{{ route('cuentas-por-pagar.store_pago', $compra->id) }}" method="POST" class="p-6">
                @csrf
                <div class="mb-4 bg-red-50 dark:bg-red-500/10 p-4 rounded-xl border border-red-100 dark:border-red-500/20 text-center">
                    <p class="text-xs text-red-600 dark:text-red-400 font-semibold uppercase tracking-wider mb-1">Monto restante a pagar</p>
                    <p class="text-2xl font-bold text-red-700 dark:text-red-300">${{ number_format($compra->total - $compra->total_pagado, 2) }}</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Monto a abonar</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" step="0.01" min="0.01" max="{{ $compra->total - $compra->total_pagado }}" name="monto" value="{{ $compra->total - $compra->total_pagado }}" required
                                class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-8 pr-4 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-white/[0.05] dark:bg-gray-900 dark:text-white/90">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Método de Pago</label>
                        <select name="metodo_pago" x-model="metodoPago" required
                            class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-4 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-white/[0.05] dark:bg-gray-900 dark:text-white/90">
                            <option value="">Seleccione el método</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="cheque">Cheque</option>
                            <option value="yappy">Yappy</option>
                            <option value="nequi">Nequi</option>
                        </select>
                    </div>

                    <div x-show="['transferencia', 'cheque', 'yappy', 'nequi'].includes(metodoPago)">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Referencia / Comprobante <span class="text-red-500">*</span></label>
                        <input type="text" name="referencia" placeholder="Ej. 12345678"
                            class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-4 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-white/[0.05] dark:bg-gray-900 dark:text-white/90">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Pago</label>
                        <input type="date" name="fecha_pago" value="{{ date('Y-m-d') }}" required
                            class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-4 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-white/[0.05] dark:bg-gray-900 dark:text-white/90">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Observaciones</label>
                        <textarea name="observaciones" rows="2" placeholder="Opcional..."
                            class="w-full rounded-xl border border-gray-200 bg-white py-2.5 px-4 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-white/[0.05] dark:bg-gray-900 dark:text-white/90"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" @click="pagoModalOpen = false" class="flex-1 rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors dark:border-white/[0.05] dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 rounded-xl bg-brand-500 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition-colors shadow-sm">
                        Procesar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</div>
@endsection
