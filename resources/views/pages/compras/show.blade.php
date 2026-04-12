@extends('layouts.app')
@php
    $title = 'Detalles de Compra';
@endphp

@section('content')
<div class="mx-auto max-w-7xl">

    {{-- ── Breadcrumb & Top Bar ─────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    Compra <span class="text-brand-600 dark:text-brand-400">#{{ str_pad($compra->id, 5, '0', STR_PAD_LEFT) }}</span>
                </h2>
                <span class="inline-flex items-center gap-1.5 rounded-md bg-{{ $compra->estado_badge_color }}-50 px-2.5 py-1 text-xs font-semibold text-{{ $compra->estado_badge_color }}-700 ring-1 ring-inset ring-{{ $compra->estado_badge_color }}-600/20 dark:bg-{{ $compra->estado_badge_color }}-500/10 dark:text-{{ $compra->estado_badge_color }}-400 dark:ring-{{ $compra->estado_badge_color }}-500/20 capitalize">
                    <span class="h-1.5 w-1.5 rounded-full bg-{{ $compra->estado_badge_color }}-500"></span> 
                    {{ $compra->estado }}
                </span>
            </div>
            <nav class="mt-2">
                <ol class="flex items-center gap-2 text-sm">
                    <li><a class="font-medium text-gray-500 hover:text-brand-500" href="{{ route('dashboard') }}">Dashboard /</a></li>
                    <li><a class="font-medium text-gray-500 hover:text-brand-500" href="{{ route('compras.index') }}">Compras /</a></li>
                    <li class="font-medium text-brand-600 dark:text-brand-400">Detalle</li>
                </ol>
            </nav>
        </div>

        {{-- ── Botones de acción rápida ───────────────────────────────────── --}}
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('compras.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700 dark:hover:text-white dark:focus:ring-offset-neutral-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver
            </a>

            @if ($compra->estado === 'registrada')
                <button type="button" onclick="confirmarAnulacion()"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 shadow-sm transition-all hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:border-red-800/30 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 dark:focus:ring-offset-neutral-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Anular Compra
                </button>
            @endif

            <a href="{{ route('compras.pdf', $compra->id) }}" target="_blank"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-brand-500 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF de Compra
            </a>
        </div>
    </div>



    {{-- Form oculto de anulación --}}
    @if ($compra->estado === 'registrada')
        <form id="form-anular" action="{{ route('compras.anular', $compra->id) }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="motivo_anulacion" id="input_motivo">
        </form>
    @endif

    {{-- ── Grid Content Layout (8/4) ───────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12 items-start mt-4">

        {{-- COLUMNA IZQUIERDA: Tarjeta Proveedor e Ítems --}}
        <div class="flex flex-col gap-6 xl:col-span-8">
            
            {{-- Info Proveedor --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-gray-50/50 to-transparent dark:from-neutral-800/20 rounded-bl-full z-0"></div>
                
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 relative z-10">
                    <div class="flex items-start gap-4">
                        <div class="h-16 w-16 flex-shrink-0 rounded-xl bg-gray-100 shadow-sm flex items-center justify-center dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                                {{ $compra->proveedor->empresa }}
                            </h3>
                            <p class="text-sm font-mono font-medium text-gray-500 dark:text-gray-400 mt-1">
                                RUC / ID: {{ $compra->proveedor->ruc ?? 'N/A' }}
                            </p>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 space-y-0.5">
                                <p>{{ $compra->proveedor->email ?? 'Sin correo' }}</p>
                                <p>{{ $compra->proveedor->telefono ?? 'Sin teléfono' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="md:text-right mt-4 md:mt-0 bg-gray-50 p-4 rounded-xl border border-gray-100 md:border-none md:bg-transparent md:p-0 dark:bg-neutral-800/40 dark:border-neutral-700 md:dark:bg-transparent">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Factura del Proveedor</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white font-mono mt-1 leading-none break-all">
                            {{ $compra->numero_factura ?? '—' }}
                        </p>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-2 flex items-center md:justify-end gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Adquirido: {{ $compra->fecha_compra->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tarjeta de Productos --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden">
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-neutral-800/80 dark:bg-neutral-800/20">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Artículos Ingresados a Inventario
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-neutral-800/40 border-b border-gray-100 dark:border-neutral-700">
                            <tr>
                                <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">#</th>
                                <th class="px-3 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Concepto</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Cant.</th>
                                <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Costo Und</th>
                                <th class="px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-neutral-700/60">
                            @foreach ($compra->detalles as $index => $detalle)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-6 py-4 text-[11px] font-mono text-gray-400 font-bold w-10">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-3 py-4">
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $detalle->producto->nombre }}</p>
                                    <p class="text-[11px] font-mono text-gray-400 mt-0.5">SKU: {{ $detalle->producto->sku ?? 'N/A' }}</p>
                                </td>
                                <td class="px-4 py-4 text-center font-medium text-gray-700 dark:text-gray-300">
                                    {{ $detalle->cantidad }}
                                </td>
                                <td class="px-4 py-4 text-right tabular-nums text-gray-600 dark:text-gray-400">
                                    ${{ number_format($detalle->precio_compra, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right tabular-nums font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($detalle->subtotal, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Observaciones Vistas --}}
            @if ($compra->observaciones)
            <div class="rounded-xl border border-blue-100 bg-blue-50/50 p-5 dark:border-blue-900/30 dark:bg-blue-900/10 mb-6">
                <h3 class="text-xs font-bold uppercase tracking-widest text-blue-800 dark:text-blue-400 mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Observaciones de Compra
                </h3>
                <p class="text-sm text-blue-900/80 dark:text-blue-200/80 whitespace-pre-wrap leading-relaxed">{{ $compra->observaciones }}</p>
            </div>
            @endif

        </div>

        {{-- COLUMNA DERECHA: Totales y Metadatos ───────────────────────────────── --}}
        <div class="flex flex-col gap-6 xl:col-span-4">
            
            {{-- Big Total Card --}}
            <div class="rounded-2xl bg-gray-900 px-6 py-8 shadow-md dark:border dark:border-neutral-700 dark:bg-neutral-900 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-brand-600/20 to-transparent z-0"></div>
                <div class="relative z-10">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Monto Total de Compra</p>
                    <p class="text-4xl font-black tabular-nums tracking-tight">
                        ${{ number_format($compra->total, 2) }}
                    </p>
                    <div class="mt-4 pt-4 border-t border-gray-700/50 flex justify-between items-center text-sm">
                        <span class="text-gray-400 font-medium">Estado de Pago:</span>
                        @if ($compra->estado === 'pagada')
                            <span class="font-bold text-emerald-400 tracking-wide uppercase text-xs">Abonado / Cancelado</span>
                        @elseif ($compra->estado === 'anulada')
                            <span class="font-bold text-red-400 tracking-wide uppercase text-xs">Anulada</span>
                        @else
                            <span class="font-bold text-amber-400 tracking-wide uppercase text-xs">Por Pagar / Deuda</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Condiciones (Metadatos) --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
                <div class="border-b border-gray-100 px-5 py-4 dark:border-neutral-800/80">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Condiciones de Compra
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Tipo Compra</p>
                        <span class="inline-flex rounded bg-{{ $compra->tipo_compra_badge_color }}-50 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider text-{{ $compra->tipo_compra_badge_color }}-700 ring-1 ring-inset ring-{{ $compra->tipo_compra_badge_color }}-600/20 dark:bg-{{ $compra->tipo_compra_badge_color }}-400/10 dark:text-{{ $compra->tipo_compra_badge_color }}-400">
                            {{ $compra->tipo_compra }}
                        </span>
                    </div>

                    @if ($compra->tipo_compra === 'credito')
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Fecha Vencimiento</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $compra->fecha_vencimiento ? $compra->fecha_vencimiento->format('d/m/Y') : 'Sin especificar' }}
                        </p>
                    </div>
                    @endif

                    @if ($compra->metodo_pago)
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Medio de Pago</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $compra->metodo_pago }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Sucursal de Destino</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $compra->sucursal->nombre }}</span>
                        </div>
                    </div>
                    
                    <div class="pt-4 mt-2 border-t border-gray-100 dark:border-neutral-800">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Ingresado Por</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $compra->usuario->name }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    function confirmarAnulacion() {
        // Mostrar modal de motivo
        document.getElementById('modal-anular').classList.remove('hidden');
        document.getElementById('input-motivo-anular').focus();
    }
    function cerrarModalAnular() {
        document.getElementById('modal-anular').classList.add('hidden');
        document.getElementById('input-motivo-anular').value = '';
    }
    function ejecutarAnulacion() {
        const motivo = document.getElementById('input-motivo-anular').value.trim();
        if (!motivo) {
            window.Notify.warning('Debes ingresar un motivo para la anulación.');
            return;
        }
        window.Confirm.show(
            '¿Confirmar anulación?',
            'El stock regresará a su estado anterior. ¡Esta acción no se puede deshacer!',
            'Sí, anular compra',
            'Cancelar',
            () => {
                document.getElementById('input_motivo').value = motivo;
                cerrarModalAnular();
                document.getElementById('form-anular').submit();
            },
            () => {},
            { okButtonBackground: '#d33' }
        );
    }
</script>

{{-- Modal motivo de anulación --}}
<div id="modal-anular" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1">¿Anular esta compra?</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">El stock de los productos regresará a su estado anterior.</p>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Motivo de anulación <span class="text-red-500">*</span></label>
        <input id="input-motivo-anular" type="text" placeholder="Ej. Factura duplicada, mal ingresada..."
               class="w-full rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 outline-none">
        <div class="mt-5 flex gap-3 justify-end">
            <button onclick="cerrarModalAnular()" class="rounded-xl border border-gray-200 dark:border-neutral-700 px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-800">Cancelar</button>
            <button onclick="ejecutarAnulacion()" class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition">Anular Compra</button>
        </div>
    </div>
</div>
@endsection
