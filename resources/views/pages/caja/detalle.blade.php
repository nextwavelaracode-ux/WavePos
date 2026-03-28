@extends('layouts.app')

@section('content')
<div class="p-4 mx-auto max-w-screen-xl md:p-6">

    {{-- Header --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('caja.ventas.historial') }}" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ $venta->numero }}</h2>
            <p class="text-sm text-gray-500">{{ $venta->fecha->format('d/m/Y') }} · {{ $venta->sucursal?->nombre }}</p>
        </div>
        <span class="ml-auto inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold
            @if($venta->estado === 'completada') bg-emerald-100 text-emerald-700
            @elseif($venta->estado === 'anulada') bg-red-100 text-red-700
            @else bg-amber-100 text-amber-700 @endif">
            {{ ucfirst($venta->estado) }}
        </span>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Left: Products --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Productos Vendidos</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Producto</th>
                            <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Cant.</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Precio</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">ITBMS</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($venta->detalles as $detalle)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $detalle->producto?->nombre ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $detalle->cantidad }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">${{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ $detalle->impuesto }}%</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white">${{ number_format($detalle->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-right text-xs text-gray-500">Subtotal</td>
                            <td class="px-4 py-2 text-right text-sm font-semibold text-gray-700 dark:text-white">${{ number_format($venta->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-right text-xs text-gray-500">ITBMS</td>
                            <td class="px-4 py-2 text-right text-sm font-semibold text-gray-700 dark:text-white">${{ number_format($venta->itbms, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">TOTAL</td>
                            <td class="px-4 py-3 text-right text-lg font-black text-brand-600 dark:text-brand-400">${{ number_format($venta->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Pagos --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Pagos Recibidos</h3>
                <div class="space-y-2">
                    @foreach($venta->pagos as $pago)
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 dark:border-gray-800 p-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs rounded-full px-2.5 py-1 font-semibold
                                @if($pago->metodo === 'efectivo') bg-emerald-100 text-emerald-700
                                @elseif($pago->metodo === 'tarjeta') bg-blue-100 text-blue-700
                                @elseif($pago->metodo === 'transferencia') bg-purple-100 text-purple-700
                                @elseif($pago->metodo === 'yappy') bg-amber-100 text-amber-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ $pago->metodo_label }}
                            </span>
                            @if($pago->referencia)
                                <span class="text-xs text-gray-400">Ref: {{ $pago->referencia }}</span>
                            @endif
                            @if($pago->tipo_tarjeta)
                                <span class="text-xs text-gray-400">{{ ucfirst($pago->tipo_tarjeta) }}</span>
                            @endif
                        </div>
                        <span class="font-bold text-gray-800 dark:text-white">${{ number_format($pago->monto, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($venta->estado === 'anulada' && $venta->motivo_anulacion)
            <div class="rounded-2xl border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20 p-4">
                <p class="text-sm font-semibold text-red-700 dark:text-red-400">Motivo de anulación:</p>
                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $venta->motivo_anulacion }}</p>
            </div>
            @endif
        </div>

        {{-- Right: Info + Actions --}}
        <div class="space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
                <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Información</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Cajero</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $venta->usuario?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Sucursal</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $venta->sucursal?->nombre ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Cliente</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $venta->cliente?->nombre_completo ?? 'Consumidor Final' }}</dd>
                    </div>
                    @if($venta->cliente)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Doc.</dt>
                        <dd class="font-medium text-gray-800 dark:text-white text-xs">{{ $venta->cliente->documento_principal }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div class="space-y-2">
                <a href="{{ route('caja.ventas.pdf', $venta) }}" target="_blank"
                   class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white hover:bg-blue-700 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Imprimir Factura A4
                </a>
                <button type="button" x-data @click="$dispatch('abrir-ticket', '{{ route('caja.ventas.ticket', $venta) }}')"
                   class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 py-3 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimir Ticket 80mm
                </button>
                <a href="{{ route('caja.ventas.historial') }}"
                   class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-300 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition mt-4">
                    Volver al Historial
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
