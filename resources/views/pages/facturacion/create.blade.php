@extends('layouts.app')
@php
    $title = 'Emitir Factura DIAN';
@endphp

@section('content')

    {{-- Breadcrumb al estilo del sistema --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
            Emitir Factura Electrónica
        </h2>

        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <a class="font-medium" href="{{ route('dashboard') }}">Dashboard /</a>
                </li>
                <li>
                    <a class="font-medium" href="{{ route('facturacion.index') }}">Facturación /</a>
                </li>
                <li class="font-medium text-primary">Emitir</li>
            </ol>
        </nav>
    </div>

    {{-- Flash alerts --}}
    @if(session('error'))
    <div class="mb-6 flex w-full border-l-6 border-[#F87171] bg-[#F87171] bg-opacity-[15%] px-7 py-8 shadow-md dark:bg-[#1B1B24] dark:bg-opacity-30 md:p-9">
        <div class="mr-5 flex h-9 w-full max-w-[36px] items-center justify-center rounded-lg bg-[#F87171]">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.4917 7.65579L11.106 12.2645C11.2545 12.4128 11.4715 12.5 11.6738 12.5C11.8762 12.5 12.0931 12.4128 12.2415 12.2645C12.5621 11.9445 12.5623 11.4317 12.2423 11.1114C12.2422 11.1113 12.2422 11.1112 12.2422 11.1111L7.62783 6.50187L12.2459 1.91158C12.5825 1.5748 12.5825 1.02828 12.2459 0.691497C11.9092 0.354719 11.3627 0.354719 11.026 0.691497L6.40928 5.28182L1.79155 0.691497C1.45484 0.354719 0.908311 0.354719 0.571605 0.691497C0.2349 1.02828 0.2349 1.5748 0.571605 1.91158L5.18889 6.50187L0.57791 11.1111C0.25732 11.4313 0.257152 11.9441 0.577172 12.2641C0.577415 12.2643 0.577662 12.2644 0.57791 12.2645C0.726294 12.4128 0.943268 12.5 1.1456 12.5C1.34794 12.5 1.56491 12.4128 1.7133 12.2645L6.4917 7.65579Z" fill="#ffffff" />
            </svg>
        </div>
        <div class="w-full">
            <h5 class="mb-3 text-lg font-bold text-[#B45454]">Error al procesar</h5>
            <p class="text-[#B45454]">{{ session('error') }}</p>
        </div>
    </div>
    @endif
    @if($errors->any())
    <div class="mb-6 flex w-full border-l-6 border-[#F87171] bg-[#F87171] bg-opacity-[15%] px-7 py-8 shadow-md dark:bg-[#1B1B24] dark:bg-opacity-30 md:p-9">
        <div class="w-full">
            <h5 class="mb-3 text-lg font-bold text-[#B45454]">Validación fallida</h5>
            <ul class="list-inside list-disc text-[#B45454]">
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
    <div x-data="facturaForm()" x-init="init()" class="grid grid-cols-1 gap-9">
        <div class="flex flex-col gap-9 col-span-1">

            <!-- Contenedor Principal Estilo Sistema -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900 p-6 lg:p-8">

                <form action="{{ route('facturacion.storeManual') }}" method="POST" @submit.prevent="submitForm">
                    @csrf
                    <input type="hidden" name="items_json" id="items_json_field">
                    <input type="hidden" name="allowance_charges_json" id="allowance_charges_json_field">
                    <input type="hidden" name="cliente_id" :value="clienteId">
                    <input type="hidden" name="venta_id" :value="ventaId">

                    {{-- ────── 1. Cargar Venta Opcional ────── --}}
                    <div class="mb-6">
                        <h3 class="font-medium text-black dark:text-white mb-4">1. Importar desde POS (Opcional)</h3>
                        <div class="flex flex-col gap-6 xl:flex-row">
                            <div class="w-full xl:w-1/2">
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Venta Previa
                                </label>
                                <select x-model="ventaSeleccionadaId" @change="cargarVenta($event.target.value)"
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                                    <option value="">— Seleccionar (Omite si es venta directa) —</option>
                                    <template x-for="v in VENTAS_SIN_FACTURA" :key="v.id">
                                        <option :value="v.id" x-text="`Recibo #${v.numero} | ${v.cliente} | $${v.total.toFixed(2)}`"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-stroke dark:border-strokedark my-6"></div>

                    {{-- ────── 2. Datos del Cliente ────── --}}
                    <div class="mb-6">
                        <h3 class="font-medium text-black dark:text-white mb-4">2. Receptor <span class="text-meta-1">*</span></h3>

                        <div class="mb-4 w-full xl:w-1/2">
                            <select x-model="clienteId" @change="actualizarInfoCliente" required
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                                <option value="">— Buscar o seleccionar cliente DIAN —</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}">{{ trim(($c->nombre ?? '') . ' ' . ($c->apellido ?? '')) ?: ($c->empresa ?? '') }} ({{ $c->cedula ?? $c->ruc ?? $c->pasaporte ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Perfil DIAN --}}
                        <template x-if="clienteInfo">
                            <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.05] p-5 mt-4">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 tracking-wide">ID.</p>
                                        <p class="text-black dark:text-white font-medium" x-text="clienteInfo.documento"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 tracking-wide">Email</p>
                                        <p class="text-black dark:text-white truncate" x-text="clienteInfo.email || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 tracking-wide">Teléfono</p>
                                        <p class="text-black dark:text-white" x-text="clienteInfo.telefono || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 tracking-wide">Org. Jurídica</p>
                                        <p class="text-black dark:text-white" x-text="clienteInfo.tipo_organizacion || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 tracking-wide">Régimen</p>
                                        <p class="text-black dark:text-white" x-text="clienteInfo.tipo_regimen || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 tracking-wide">Tributo</p>
                                        <p class="text-black dark:text-white" x-text="clienteInfo.tributo || '—'"></p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="!clienteInfo && clienteId">
                            <div class="mt-4 rounded-xl border border-[#F59E0B] bg-[#F59E0B]/10 p-4">
                                <p class="text-sm text-[#B45454] dark:text-[#F59E0B]">
                                    Perfil DIAN incompleto. <a href="{{ route('clientes.index') }}" class="font-bold underline hover:opacity-80">Completar en catálogo</a>
                                </p>
                            </div>
                        </template>
                    </div>

                    <div class="border-b border-stroke dark:border-strokedark my-6"></div>

                    {{-- ────── 3. Parámetros Generales ────── --}}
                    <div class="mb-6">
                        <h3 class="font-medium text-black dark:text-white mb-4">3. Parámetros de Emisión</h3>

                        <div class="mb-4.5 grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Forma de Pago <span class="text-meta-1">*</span>
                                </label>
                                <select name="payment_form" x-model="paymentForm" required
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                                    <option value="1">Contado</option>
                                    <option value="2">Crédito</option>
                                </select>
                            </div>

                            <div x-show="paymentForm === '2'" x-cloak>
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Vencimiento (Crédito) <span class="text-meta-1">*</span>
                                </label>
                                <input type="date" name="payment_due_date" x-model="paymentDueDate" :required="paymentForm === '2'"
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Método de Pago <span class="text-meta-1">*</span>
                                </label>
                                <select name="payment_method_code" x-model="paymentMethod" required
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                                    <option value="10">Efectivo (10)</option>
                                    <option value="42">Débito automático banco (42)</option>
                                    <option value="47">Tarjeta débito (47)</option>
                                    <option value="48">Tarjeta crédito (48)</option>
                                    <option value="49">Tarjeta prepago (49)</option>
                                    <option value="1">Instrumento no definido (1)</option>
                                    <option value="20">Cheque (20)</option>
                                </select>
                            </div>

                            <div x-show="paymentMethod !== '10'" x-cloak>
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Referencia de Pago / Voucher
                                </label>
                                <input type="text" name="payment_reference" x-model="paymentReference" placeholder="N° de Transferencia, Datafono..."
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Tipo de Documento
                                </label>
                                <select name="document_type"
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                                    <option value="01">Factura de venta (01)</option>
                                    <option value="02">Factura de exportación (02)</option>
                                    <option value="03">Factura por contingencia (03)</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Resolución Rango DIAN
                                </label>
                                <input type="number" name="numbering_range_id" x-model="numberingRangeId" min="1" placeholder="(Factus ID)"
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Ref. Personalizada
                                </label>
                                <input type="text" name="reference_code" x-model="referenceCode" placeholder="Automático si vacío"
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Observaciones
                                </label>
                                <input type="text" name="observation" x-model="observation" placeholder="Comentarios..."
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-stroke dark:border-strokedark my-6"></div>

                    {{-- ────── 4. Ítems de la Factura ────── --}}
                    <div class="mb-6" x-data="{ addingItem: false }">

                        {{-- Cabecera de sección --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <h3 class="font-medium text-black dark:text-white">4. Productos Facturados</h3>
                                <span x-show="items.length > 0"
                                      class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-bold text-primary"
                                      x-text="items.length + (items.length === 1 ? ' ítem' : ' ítems')">
                                </span>
                            </div>
                            {{-- Botón compacto para abrir el panel de agregar --}}
                            <button type="button" @click="addingItem = !addingItem"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-dashed border-primary/40 px-3 py-1.5 text-xs font-semibold text-primary transition hover:border-primary hover:bg-primary/5">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                                     :class="addingItem ? 'rotate-45' : ''" style="transition: transform 0.2s;">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                <span x-text="addingItem ? 'Cancelar' : 'Agregar producto'"></span>
                            </button>
                        </div>

                        {{-- Panel de entrada — se expande al hacer click en "Agregar producto" --}}
                        <div x-show="addingItem" x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                             class="mb-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 p-4">

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

                                {{-- Catálogo --}}
                                <div class="md:col-span-3">
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Catálogo</label>
                                    <select x-model="tempProdId" @change="updateTempProduct"
                                            class="w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-800 dark:text-white/90 dark:border-gray-700 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition">
                                        <option value="">— Seleccionar —</option>
                                        <template x-for="p in PRODUCTOS_DATA" :key="p.id">
                                            <option :value="p.id" x-text="p.nombre"></option>
                                        </template>
                                    </select>
                                </div>

                                {{-- Descripción --}}
                                <div class="md:col-span-3">
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Descripción <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" x-model="tempNombre" placeholder="Nombre del producto / servicio"
                                           class="w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-800 dark:text-white/90 dark:border-gray-700 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition">
                                </div>

                                {{-- Cantidad --}}
                                <div class="md:col-span-1">
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Cant.</label>
                                    <input type="number" x-model="tempCantidad" min="1"
                                           class="w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-800 dark:text-white/90 dark:border-gray-700 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition">
                                </div>

                                {{-- Precio --}}
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Precio Un.</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-medium">$</span>
                                        <input type="number" step="0.01" x-model="tempPrecio" min="0" placeholder="0.00"
                                               class="w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 pl-6 pr-3 py-2 text-sm text-gray-800 dark:text-white/90 dark:border-gray-700 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition">
                                    </div>
                                </div>

                                {{-- IVA --}}
                                <div class="md:col-span-1">
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">IVA %</label>
                                    <select x-model="tempTax"
                                            class="w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 px-2 py-2 text-sm text-gray-800 dark:text-white/90 dark:border-gray-700 focus:border-primary outline-none transition">
                                        <option value="0.00">0%</option>
                                        <option value="5.00">5%</option>
                                        <option value="19.00" selected>19%</option>
                                    </select>
                                </div>

                                {{-- Descuento --}}
                                <div class="md:col-span-1">
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Dto %</label>
                                    <input type="number" step="0.01" x-model="tempDesc" min="0" max="100" placeholder="0"
                                           class="w-full rounded-lg border border-gray-300 bg-white dark:bg-gray-900 px-2 py-2 text-sm text-gray-800 dark:text-white/90 dark:border-gray-700 focus:border-primary outline-none transition">
                                </div>

                                {{-- Botón agregar --}}
                                <div class="md:col-span-1">
                                    <button type="button" @click="addItem(); addingItem = false"
                                            class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-dark shadow-sm hover:bg-opacity-90 transition">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                        </svg>
                                        Añadir
                                    </button>
                                </div>
                            </div>

                            {{-- Preview del subtotal mientras rellena --}}
                            <div x-show="tempNombre && parseFloat(tempPrecio) > 0"
                                 class="mt-3 flex items-center justify-end gap-2 text-xs text-gray-500 dark:text-gray-400" x-cloak>
                                <span>Subtotal estimado:</span>
                                <span class="font-bold text-black dark:text-white"
                                      x-text="'$' + (parseFloat(tempCantidad || 1) * parseFloat(tempPrecio || 0) * (1 - parseFloat(tempDesc || 0)/100)).toFixed(2)">
                                </span>
                                <span class="text-emerald-500 font-medium"
                                      x-text="'+ IVA $' + (parseFloat(tempCantidad||1) * parseFloat(tempPrecio||0) * (1 - parseFloat(tempDesc||0)/100) * parseFloat(tempTax||0)/100).toFixed(2)">
                                </span>
                            </div>
                        </div>

                        {{-- Tabla de ítems --}}
                        <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-white/[0.03]">
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Concepto</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-400">Cant.</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-400">Precio Un.</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-400">Dto</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-400">IVA</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-400">Total</th>
                                        <th class="px-4 py-3 w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="group hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition-colors">
                                            <td class="px-4 py-3">
                                                {{-- Campos hidden --}}
                                                <input type="hidden" :name="`items[${index}][producto_id]`"  :value="item.producto_id">
                                                <input type="hidden" :name="`items[${index}][nombre]`"       :value="item.nombre">
                                                <input type="hidden" :name="`items[${index}][codigo]`"       :value="item.codigo">
                                                <input type="hidden" :name="`items[${index}][cantidad]`"     :value="item.cantidad">
                                                <input type="hidden" :name="`items[${index}][precio]`"       :value="item.precio">
                                                <input type="hidden" :name="`items[${index}][tax_rate]`"     :value="item.tax_rate">
                                                <input type="hidden" :name="`items[${index}][discount_rate]`" :value="item.discount_rate">
                                                <p class="font-medium text-gray-800 dark:text-white/90" x-text="item.nombre"></p>
                                                <p class="text-[11px] text-gray-400 font-mono mt-0.5" x-text="item.codigo"></p>
                                            </td>
                                            <td class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-300"
                                                x-text="item.cantidad"></td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400 tabular-nums"
                                                x-text="'$' + parseFloat(item.precio).toLocaleString('es-CO', {minimumFractionDigits:2})"></td>
                                            <td class="px-4 py-3 text-center">
                                                <span x-show="item.discount_rate > 0"
                                                      class="inline-block rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 text-[11px] font-bold px-2 py-0.5"
                                                      x-text="item.discount_rate + '%'"></span>
                                                <span x-show="!item.discount_rate || item.discount_rate == 0" class="text-gray-300 text-xs">—</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-block rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[11px] font-bold px-2 py-0.5"
                                                      x-text="item.tax_rate + '%'"></span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold tabular-nums text-gray-900 dark:text-white"
                                                x-text="'$' + ((item.cantidad * item.precio) * (1 - (item.discount_rate/100))).toLocaleString('es-CO', {minimumFractionDigits:2})">
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" @click="removeItem(index)"
                                                        class="inline-flex items-center justify-center h-7 w-7 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors opacity-0 group-hover:opacity-100">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>

                                    {{-- Estado vacío --}}
                                    <tr x-show="items.length === 0">
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="text-gray-400">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM16 3H8a2 2 0 00-2 2v2h12V5a2 2 0 00-2-2z"/>
                                                    </svg>
                                                </div>
                                                <p class="text-sm text-gray-400 dark:text-gray-500">Sin productos agregados</p>
                                                <button type="button" @click="addingItem = true"
                                                        class="text-xs font-semibold text-primary hover:underline">
                                                    + Agregar el primer producto
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="border-b border-stroke dark:border-strokedark my-6"></div>

                    {{-- ────── 5. Cargos / Descuentos Globales ────── --}}
                    <div class="mb-6">
                        <div class="flex items-center gap-4 mb-4">
                            <h3 class="font-medium text-black dark:text-white">5. Ajustes Globales</h3>
                            <button type="button" x-show="!mostrarCargos" @click="mostrarCargos = true" class="text-sm font-medium text-brand-500 hover:underline inline-flex items-center gap-1">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Agregar propina, envío o recargo
                            </button>
                        </div>

                        <div x-show="mostrarCargos" x-cloak class="mb-6 p-5 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-medium text-gray-600 dark:text-white/90">Concepto</label>
                                    <select x-model="tempCargoConcepto" class="w-full rounded-xl border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 dark:border-gray-700 dark:text-white/90">
                                        <option value="03">Propina</option>
                                        <option value="01">Descuento Global</option>
                                        <option value="02">Envío/Flete</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-medium text-gray-600 dark:text-white/90">Naturaleza</label>
                                    <select x-model="tempCargoEsSobrecargo" class="w-full rounded-xl border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 dark:border-gray-700 dark:text-white/90">
                                        <option :value="true">Sobrecargo (+)</option>
                                        <option :value="false">Descuento (-)</option>
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="mb-1 block text-sm font-medium text-gray-600 dark:text-white/90">Razón</label>
                                    <input type="text" x-model="tempCargoRazon" placeholder="Ej: Flete rápido"
                                        class="w-full rounded-xl border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 border-gray-700 dark:text-white/90">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-medium text-gray-600 dark:text-white/90">Base ($)</label>
                                    <input type="number" step="0.01" x-model="tempCargoBase" min="0" placeholder="0.00"
                                        class="w-full rounded-xl border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 border-gray-700 dark:text-white/90">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-medium text-gray-600 dark:text-white/90">Monto ($)</label>
                                    <input type="number" step="0.01" x-model="tempCargoMonto" min="0" placeholder="0.00"
                                        class="w-full rounded-xl border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 border-gray-700 dark:text-white/90">
                                </div>
                                <div class="md:col-span-1">
                                    <button type="button" @click="addCargo" class="w-full rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>

                        <template x-if="allowanceCharges.length > 0">
                            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700 lg:w-3/4">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Concepto</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Detalle</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Monto</th>
                                            <th class="px-4 py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        <template x-for="(c, i) in allowanceCharges" :key="i">
                                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400" x-text="c.is_surcharge ? 'Sobrecargo' : 'Descuento'"></td>
                                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-white/90" x-text="c.reason"></td>
                                                <td class="px-4 py-3 text-right font-medium" :class="c.is_surcharge ? 'text-gray-800 dark:text-white/90' : 'text-danger'" x-text="(c.is_surcharge ? '+' : '-') + ' $' + parseFloat(c.amount).toFixed(2)"></td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" @click="removeCargo(i)" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:border-red-800/30 dark:bg-red-500/10 dark:text-red-400 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </div>

                    {{-- Totales integrados en el card --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700 mt-6">
                        <div class="text-sm space-y-2">
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Subtotal bruto:</span>
                                <span class="font-bold text-gray-900 dark:text-gray-100" x-text="'$' + subtotalBruto().toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-red-500" x-show="totalDescuentos() > 0">
                                <span>─ Desc. Productos:</span>
                                <span class="font-bold" x-text="'- $' + totalDescuentos().toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                                <span>+ IVA Total:</span>
                                <span class="font-bold" x-text="'$' + ivaEstimado().toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-warning" x-show="totalCargos() !== 0">
                                <span>Cargos/Desc global:</span>
                                <span class="font-bold" x-text="(totalCargos() >= 0 ? '+ $' : '- $') + Math.abs(totalCargos()).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                <span class="font-bold text-gray-900 dark:text-gray-100 mt-1">TOTAL FACTURA:</span>
                                <span class="font-black text-xl text-brand-600 dark:text-brand-400" x-text="'$' + total().toFixed(2)"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('facturacion.index') }}"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors">
                            Cancelar
                        </a>
                        <button type="button" @click="submitForm" :disabled="items.length === 0 || !clienteId || enviando"
                            class="rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-bold text-white hover:bg-brand-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
                            <template x-if="!enviando">
                                <span>✅ Emitir Factura a DIAN</span>
                            </template>
                            <template x-if="enviando">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    Enviando a Factus...
                                </span>
                            </template>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

<style>
/* Ocultar elementos x-cloak antes de cargar alpine */
[x-cloak] { display: none !important; }
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
            if (VENTA_PRELOAD) {
                this.ventaId = VENTA_PRELOAD.id;
                this.clienteId = String(VENTA_PRELOAD.cliente_id ?? '');
                this.actualizarInfoCliente();
                this.items = VENTA_PRELOAD.items.map(i => ({ ...i, discount_rate: i.discount_rate ?? 0, withholding_taxes: i.withholding_taxes ?? [] }));
            }
        },

        cargarVenta(ventaId) {
            if (!ventaId) return;
            window.location.href = `{{ route('facturacion.create') }}?venta_id=${ventaId}`;
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
            if (!this.tempNombre || this.tempCantidad <= 0 || parseFloat(this.tempPrecio) < 1) {
                alert('Completa la descripción, cantidad y un precio válido.');
                return;
            }
            const codigo = this.tempCodigo || (this.tempProdId ? String(this.tempProdId) : 'ITEM-' + (this.items.length + 1));
            const existing = this.items.findIndex(i => i.codigo === codigo);
            if (existing >= 0) {
                this.items[existing].cantidad += parseInt(this.tempCantidad);
            } else {
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
            }
            this.tempProdId = ''; this.tempNombre = ''; this.tempCodigo = '';
            this.tempCantidad = 1; this.tempPrecio = 0; this.tempTax = '19.00'; this.tempDesc = 0;
        },

        addCargo() {
            if (!this.tempCargoRazon || parseFloat(this.tempCargoMonto) <= 0) {
                alert('Ingresa la descripción y el valor del cargo.');
                return;
            }
            this.allowanceCharges.push({
                concept_type : this.tempCargoConcepto,
                is_surcharge : Boolean(this.tempCargoEsSobrecargo),
                reason       : this.tempCargoRazon,
                base_amount  : parseFloat(this.tempCargoBase).toFixed(2),
                amount       : parseFloat(this.tempCargoMonto).toFixed(2),
            });
            this.tempCargoRazon = ''; this.tempCargoBase = 0; this.tempCargoMonto = 0;
        },

        removeCargo(index) { this.allowanceCharges.splice(index, 1); },
        removeItem(index)  { this.items.splice(index, 1); },

        subtotalBruto() {
            return this.items.reduce((s, i) => s + (parseFloat(i.cantidad) * parseFloat(i.precio)), 0);
        },
        totalDescuentos() {
            return this.items.reduce((s, i) => s + (parseFloat(i.cantidad) * parseFloat(i.precio) * (parseFloat(i.discount_rate || 0) / 100)), 0);
        },
        ivaEstimado() {
            return this.items.reduce((s, i) => {
                const base = parseFloat(i.cantidad) * parseFloat(i.precio) * (1 - parseFloat(i.discount_rate || 0) / 100);
                return s + (base * parseFloat(i.tax_rate || 0) / 100);
            }, 0);
        },
        totalCargos() {
            return this.allowanceCharges.reduce((s, c) => {
                const val = parseFloat(c.amount);
                const isS = String(c.is_surcharge) === 'true' || c.is_surcharge === true;
                return s + (isS ? val : -val);
            }, 0);
        },
        total() { return this.subtotalBruto() - this.totalDescuentos() + this.ivaEstimado() + this.totalCargos(); },

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
