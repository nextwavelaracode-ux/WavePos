@extends('layouts.app')
@php
    $title = 'Emitir Factura DIAN';
@endphp

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 lg:px-8">
    
    {{-- Breadcrumb al estilo ERP --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-neutral-800 pb-6">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xs font-medium text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-white transition">Hogar</a>
                    </li>
                    <li><div class="flex items-center"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg><a href="{{ route('facturacion.index') }}" class="ml-1 text-xs font-medium text-gray-500 hover:text-brand-600 md:ml-2 dark:text-gray-400 transition">Facturas</a></div></li>
                    <li aria-current="page"><div class="flex items-center"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg><span class="ml-1 text-xs font-bold text-gray-900 md:ml-2 dark:text-white">Emitir</span></div></li>
                </ol>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Nueva Factura DIAN</h1>
        </div>
    </div>

    {{-- Flash alerts --}}
    @if(session('error'))
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm dark:border-red-900/30 dark:bg-red-900/10 flex gap-4">
        <div class="flex-shrink-0 text-red-500">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-red-800 dark:text-red-400">Error al procesar</h3>
            <div class="mt-1 text-sm text-red-700 dark:text-red-300">{{ session('error') }}</div>
        </div>
    </div>
    @endif
    @if($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm dark:border-red-900/30 dark:bg-red-900/10 flex gap-4">
        <div class="flex-shrink-0 text-red-500">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-red-800 dark:text-red-400">Validación fallida</h3>
            <ul class="mt-1 list-inside list-disc text-sm text-red-700 dark:text-red-300">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Script data block para Alpine --}}
    <script>
        const PRODUCTOS_DATA = {!! $productosJs !!};
        const CLIENTES_DATA  = {!! $clientesJs !!};
        const VENTAS_SIN_FACTURA = {!! $ventasJs !!};
        const VENTA_PRELOAD  = {!! $ventaPreloadJs !!};
    </script>

    <!-- Alpine.js Component for reactive form -->
    <div x-data="facturaForm()" x-init="init()">
        <form action="{{ route('facturacion.storeManual') }}" method="POST" @submit.prevent="submitForm">
            @csrf
            <input type="hidden" name="items_json" id="items_json_field">
            <input type="hidden" name="allowance_charges_json" id="allowance_charges_json_field">
            <input type="hidden" name="cliente_id" :value="clienteId">
            <input type="hidden" name="venta_id" :value="ventaId">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- COLUMNA IZQUIERDA: FORMULARIO (Grid 5) --}}
                <div class="flex flex-col gap-6 lg:col-span-5 h-[calc(100vh-140px)] overflow-y-auto pr-2 custom-scrollbar">
                    
                    {{-- 1. Cliente --}}
                    <div class="bg-gray-50/50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl p-5">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900/30 flex justify-center items-center text-xs">1</span>
                            Información del Receptor
                        </h3>
                        <div class="mb-4">
                            <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Cliente <span class="text-red-500">*</span></label>
                            <select x-model="clienteId" @change="actualizarInfoCliente" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none transition-all">
                                <option value="">— Buscar o seleccionar cliente DIAN —</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}">{{ trim(($c->nombre ?? '') . ' ' . ($c->apellido ?? '')) ?: ($c->empresa ?? '') }} ({{ $c->cedula ?? $c->ruc ?? $c->pasaporte ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Detalles Cliente Ocultos --}}
                        <div x-show="clienteInfo" x-collapse x-cloak>
                            <div class="rounded-lg bg-gray-100 dark:bg-neutral-800 p-3 text-xs text-gray-600 dark:text-gray-300 mb-0">
                                <div class="grid grid-cols-2 gap-2">
                                    <div><span class="font-semibold text-gray-800 dark:text-gray-200">Doc:</span> <span x-text="clienteInfo?.documento"></span></div>
                                    <div><span class="font-semibold text-gray-800 dark:text-gray-200">Email:</span> <span class="truncate" x-text="clienteInfo?.email || '—'"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Artículos --}}
                    <div class="bg-gray-50/50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl p-5" x-data="{ addingItem: true }">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900/30 flex justify-center items-center text-xs">2</span>
                                Productos
                            </h3>
                            <button type="button" @click="addingItem = !addingItem" class="text-brand-600 dark:text-brand-400 text-xs font-semibold hover:underline">
                                <span x-text="addingItem ? 'Ocultar form' : '+ Añadir'"></span>
                            </button>
                        </div>
                        
                        {{-- Formulario rapido Items --}}
                        <div x-show="addingItem" x-collapse class="space-y-3 mb-4 p-3 border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 rounded-lg">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Catálogo</label>
                                <select x-model="tempProdId" @change="updateTempProduct" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white outline-none">
                                    <option value="">— Seleccionar —</option>
                                    <template x-for="p in PRODUCTOS_DATA" :key="p.id">
                                        <option :value="p.id" x-text="p.nombre"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Descripción</label>
                                <input type="text" x-model="tempNombre" placeholder="Nombre" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white outline-none">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Precio</label>
                                    <input type="number" step="0.01" x-model="tempPrecio" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white outline-none">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Cantidad</label>
                                    <input type="number" x-model="tempCantidad" min="1" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white outline-none">
                                </div>
                            </div>
                            <button type="button" @click="addItem()" class="w-full rounded-lg bg-gray-800 px-3 py-2 text-xs font-bold text-white hover:bg-gray-700 transition dark:bg-white dark:text-gray-900 border border-transparent mt-2">
                                + Agregar a la factura
                            </button>
                        </div>
                    </div>

                    {{-- 3. Parámetros --}}
                    <div class="bg-gray-50/50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl p-5 mb-24">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900/30 flex justify-center items-center text-xs">3</span>
                            Parámetros Técnicos
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Pago <span class="text-red-500">*</span></label>
                                    <select name="payment_form" x-model="paymentForm" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                        <option value="1">Contado</option>
                                        <option value="2">Crédito</option>
                                    </select>
                                </div>
                                <div x-show="paymentForm === '2'" x-cloak>
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Vence <span class="text-red-500">*</span></label>
                                    <input type="date" name="payment_due_date" x-model="paymentDueDate" :required="paymentForm === '2'" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                </div>
                                <div :class="paymentForm === '2' ? 'col-span-2' : ''">
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Método <span class="text-red-500">*</span></label>
                                    <select name="payment_method_code" x-model="paymentMethod" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                        <option value="10">Efectivo</option>
                                        <option value="42">Débito aut.</option>
                                        <option value="48">Crédito</option>
                                        <option value="20">Cheque</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Notas Adicionales</label>
                                <textarea name="observation" x-model="observation" rows="2" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: PREVISUALIZACIÓN VIVA DE LA FACTURA (Grid 7) --}}
                <div class="lg:col-span-7 bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-xl shadow-lg p-6 sm:p-10 sticky top-6">
                    
                    {{-- Etiqueta Flotante --}}
                    <div class="absolute -top-3 -right-3">
                        <span class="bg-gray-900 text-white dark:bg-white dark:text-gray-900 text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded shadow-lg border border-gray-800 dark:border-white">Preview en Vivo</span>
                    </div>

                        {{-- Header de la Factura --}}
                        <div class="flex flex-col sm:flex-row sm:justify-between mb-10 gap-6">
                            <div>
                                {{-- Logo desde Factus API - fondo blanco para funcionar en dark mode --}}
                                @if(!empty($companyLogoUrl))
                                    <div class="mb-4 inline-flex items-center bg-white rounded-lg px-3 py-2 shadow-sm border border-gray-200 dark:border-neutral-700" style="max-width:180px;">
                                        <img src="{{ $companyLogoUrl }}" alt="Logo Empresa" class="h-8 w-auto object-contain">
                                    </div>
                                @else
                                    <div class="h-10 mb-4 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 40" class="h-full w-auto">
                                            <text x="0" y="32" font-family="sans-serif" font-weight="900" font-size="34" fill="#1a56db">FACTUS</text>
                                        </svg>
                                    </div>
                                @endif
                                <h2 class="text-xs font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-widest mb-1">Operador Tecnológico</h2>
                                <p class="text-base font-bold text-gray-900 dark:text-white">Factus - Proveedor DIAN</p>
                            </div>
                        <div class="sm:text-right">
                            <h2 class="text-xs font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-widest mb-1">Cobrar a</h2>
                            <template x-if="clienteInfo">
                                <div>
                                    <p class="text-base font-bold text-blue-600 dark:text-blue-400" x-text="clienteInfo.nombre || clienteInfo.empresa"></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 tabular-nums">NIT/CC: <span x-text="clienteInfo.documento"></span></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="clienteInfo.email || 'Sin correo'"></p>
                                </div>
                            </template>
                            <template x-if="!clienteInfo">
                                <p class="text-sm italic text-gray-400">-- Seleccione en el formulario --</p>
                            </template>
                        </div>
                    </div>

                    {{-- Fila de Fechas y Total --}}
                    <div class="grid grid-cols-2 gap-4 mb-6 border-y border-gray-100 dark:border-neutral-800 py-6">
                        <div>
                            <p class="text-xs font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-widest mb-1">Factura Info</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Emitida hoy</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="paymentForm === '2' && paymentDueDate ? 'Vence: ' + paymentDueDate : 'Pago de Contado'"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-widest mb-1">Monto Debido</p>
                            <p class="text-2xl font-black text-brand-600 dark:text-brand-400 tabular-nums" x-text="'$' + total().toLocaleString('es-CO', {minimumFractionDigits:0})"></p>
                        </div>
                    </div>

                    {{-- Notas --}}
                    <div class="mb-6 p-4 rounded bg-gray-50 dark:bg-neutral-800/20 text-xs text-gray-600 dark:text-gray-400" x-show="observation">
                        <span class="font-bold text-gray-900 dark:text-white">Notas:</span> <span x-text="observation"></span>
                    </div>

                    {{-- Tabla de Artículos Viva --}}
                    <div class="overflow-x-auto mb-6">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-neutral-700">
                                    <th class="py-3 px-2 font-semibold text-gray-900 dark:text-white uppercase text-xs">Descripción</th>
                                    <th class="py-3 px-2 font-semibold text-gray-900 dark:text-white uppercase text-xs text-center w-16">Cant</th>
                                    <th class="py-3 px-2 font-semibold text-gray-900 dark:text-white uppercase text-xs text-right w-24">Prec.</th>
                                    <th class="py-3 px-2 font-semibold text-gray-900 dark:text-white uppercase text-xs text-right w-24">Importe</th>
                                    <th class="py-3 px-2 w-8"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/50">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="group">
                                        <td class="py-4 px-2">
                                            <p class="text-gray-900 dark:text-white font-medium" x-text="item.nombre"></p>
                                        </td>
                                        <td class="py-4 px-2 text-center text-gray-600 dark:text-gray-300" x-text="item.cantidad"></td>
                                        <td class="py-4 px-2 text-right text-gray-600 dark:text-gray-300 tabular-nums" x-text="'$' + parseFloat(item.precio).toLocaleString('es-CO')"></td>
                                        <td class="py-4 px-2 text-right font-medium text-gray-900 dark:text-white tabular-nums" x-text="'$' + (item.cantidad * item.precio).toLocaleString('es-CO')"></td>
                                        <td class="py-4 px-2 text-right">
                                            <button type="button" @click="removeItem(index)" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="items.length === 0">
                                    <td colspan="5" class="py-8 text-center text-gray-400 italic text-sm">-- Ningún artículo añadido --</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer de Totales --}}
                    <div class="flex flex-col sm:flex-row justify-end w-full border-t border-gray-100 dark:border-neutral-800 pt-6">
                        <div class="sm:w-64 bg-gray-50 dark:bg-neutral-800/40 rounded-xl p-5 border border-gray-100 dark:border-neutral-800">
                            <div class="flex justify-between mb-3 text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="text-gray-900 dark:text-white font-medium tabular-nums" x-text="'$' + subtotalBruto().toLocaleString('es-CO')"></span>
                            </div>
                            <div class="flex justify-between mb-3 text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Impuestos (19%)</span>
                                <span class="text-gray-900 dark:text-white font-medium tabular-nums" x-text="'$' + ivaEstimado().toLocaleString('es-CO')"></span>
                            </div>
                            <hr class="my-3 border-gray-200 dark:border-neutral-700">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-900 dark:text-white uppercase">Suma debida</span>
                                <span class="text-xl font-black text-brand-600 dark:text-brand-400 tabular-nums" x-text="'$' + total().toLocaleString('es-CO')"></span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Botón Envío --}}
                    <div class="mt-8 flex justify-end">
                        <button type="submit" :disabled="items.length === 0 || !clienteId || enviando"
                            class="rounded-lg bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-brand-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <template x-if="!enviando">
                                <span>Firmar y Enviar a la DIAN</span>
                            </template>
                            <template x-if="enviando">
                                <div class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    <span>Procesando...</span>
                                </div>
                            </template>
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Ocultar elementos x-cloak antes de cargar alpine */
[x-cloak] { display: none !important; }
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.3); border-radius: 10px; }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('facturaForm', () => ({
        clienteId: '',
        clienteInfo: null,
        paymentForm: '1',
        paymentDueDate: '',
        paymentMethod: '10',
        paymentReference: '',
        observation: '',
        referenceCode: '',
        numberingRangeId: '',
        ventaId: null,
        ventaSeleccionadaId: '',
        items: [],
        allowanceCharges: [],
        tempProdId: '',
        tempNombre: '',
        tempCodigo: '',
        tempCantidad: 1,
        tempPrecio: 0,
        tempTax: '19.00',
        tempDesc: 0,
        mostrarCargos: false,
        tempCargoConcepto: '03',
        tempCargoEsSobrecargo: true,
        tempCargoRazon: '',
        tempCargoBase: 0,
        tempCargoMonto: 0,
        enviando: false,

        init() {
            if (VENTA_PRELOAD && typeof VENTA_PRELOAD === 'object') {
                this.ventaId = VENTA_PRELOAD.id;
                this.clienteId = String(VENTA_PRELOAD.cliente_id ?? '');
                this.actualizarInfoCliente();
                if(Array.isArray(VENTA_PRELOAD.items)) {
                    this.items = VENTA_PRELOAD.items.map(i => ({ ...i, discount_rate: parseFloat(i.discount_rate ?? 0), withholding_taxes: i.withholding_taxes ?? [] }));
                }
            }
        },

        actualizarInfoCliente() {
            if (!this.clienteId) { this.clienteInfo = null; return; }
            this.clienteInfo = CLIENTES_DATA.find(c => c.id == this.clienteId) || null;
        },

        updateTempProduct() {
            if (!this.tempProdId) { this.tempNombre = ''; this.tempPrecio = 0; return; }
            const prod = PRODUCTOS_DATA.find(p => p.id == this.tempProdId);
            if (prod) {
                this.tempNombre  = prod.nombre;
                this.tempCodigo  = prod.codigo;
                this.tempPrecio  = prod.precio;
                this.tempTax     = prod.impuesto > 0 ? String(prod.impuesto.toFixed(2)) : '19.00';
            }
        },

        addItem() {
            if (!this.tempNombre || this.tempCantidad <= 0 || parseFloat(this.tempPrecio) < 0) {
                alert('Completa la descripción, cantidad y un precio válido.');
                return;
            }
            const codigo = this.tempCodigo || (this.tempProdId ? String(this.tempProdId) : 'ITEM-' + (this.items.length + 1));
            this.items.push({
                producto_id    : this.tempProdId || null,
                nombre         : this.tempNombre,
                codigo         : codigo,
                cantidad       : parseInt(this.tempCantidad),
                precio         : parseFloat(this.tempPrecio),
                tax_rate       : this.tempTax,
                discount_rate  : parseFloat(this.tempDesc) || 0,
                is_excluded    : 0,
                unit_measure_id: 70,
                withholding_taxes: [],
            });
            this.tempProdId = ''; this.tempNombre = ''; this.tempCodigo = '';
            this.tempCantidad = 1; this.tempPrecio = 0; this.tempTax = '19.00';
        },

        removeItem(index)  { this.items.splice(index, 1); },

        subtotalBruto() {
            return this.items.reduce((s, i) => s + (parseFloat(i.cantidad) * parseFloat(i.precio)), 0);
        },
        ivaEstimado() {
            return this.items.reduce((s, i) => {
                const base = parseFloat(i.cantidad) * parseFloat(i.precio);
                return s + (base * 0.19); // 19% simulado
            }, 0);
        },
        total() { return this.subtotalBruto() + this.ivaEstimado(); },

        submitForm() {
            if (this.items.length === 0 || !this.clienteId) return;
            document.getElementById('items_json_field').value = JSON.stringify(this.items);
            document.getElementById('allowance_charges_json_field').value = JSON.stringify(this.allowanceCharges);
            this.enviando = true;
            this.$el.closest('form').submit();
        }
    }));
});
</script>
@endsection
