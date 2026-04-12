@extends('layouts.app')
@php
    $title = 'Registrar Compra';
@endphp

@section('content')
<div class="mx-auto max-w-7xl">
    
    {{-- Breadcrumb al estilo ERP --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                Registrar Nueva Compra
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ingresa inventario y registra cuentas por pagar a proveedores</p>
        </div>

        <nav>
            <ol class="flex items-center gap-2 text-sm">
                <li><a class="font-medium text-gray-500 hover:text-brand-500" href="{{ route('dashboard') }}">Dashboard /</a></li>
                <li><a class="font-medium text-gray-500 hover:text-brand-500" href="{{ route('compras.index') }}">Compras /</a></li>
                <li class="font-medium text-brand-600 dark:text-brand-400">Registrar</li>
            </ol>
        </nav>
    </div>



    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm dark:border-red-800/30 dark:bg-red-900/10 flex gap-4">
            <div class="flex-shrink-0 text-red-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="w-full">
                <h3 class="text-sm font-semibold text-red-800 dark:text-red-400">Hubo un problema con la información</h3>
                <ul class="mt-1 list-inside list-disc text-sm text-red-700 dark:text-red-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Alpine.js Component -->
    <div x-data="compraForm()">
        <form action="{{ route('compras.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start">
                
                {{-- COLUMNA IZQUIERDA: Proveedor e Ítems --}}
                <div class="flex flex-col gap-6 lg:col-span-8">
                    
                    {{-- 1. Proveedor --}}
                    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-neutral-800/80 dark:bg-neutral-800/20">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Información del Proveedor
                            </h3>
                        </div>
                        <div class="p-6">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Seleccionar Proveedor <span class="text-red-500">*</span>
                                </label>
                                <select name="proveedor_id" required class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none transition-all">
                                    <option value="" disabled selected>— Buscar o seleccionar proveedor —</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">{{ $proveedor->empresa }}</option>
                                    @endforeach
                                </select>
                                @error('proveedor_id')
                                    <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 2. Artículos a Comprar --}}
                    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden" x-data="{ addingItem: false }">
                        <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 flex items-center justify-between dark:border-neutral-800/80 dark:bg-neutral-800/20">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    Detalle de Compra
                                </h3>
                                <span x-show="items.length > 0" class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-bold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400" x-text="items.length + (items.length === 1 ? ' ítem' : ' ítems')"></span>
                            </div>
                            <button type="button" @click="addingItem = !addingItem" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-gray-900 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" :class="addingItem ? 'rotate-45' : ''" class="transition-transform"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                <span x-text="addingItem ? 'Cancelar' : 'Añadir ítem'"></span>
                            </button>
                        </div>
                        
                        <div class="p-6">
                            
                            {{-- Form Modal para agregar Item --}}
                            <div x-show="addingItem" x-cloak class="mb-6 rounded-xl border border-blue-100 bg-blue-50/50 p-5 shadow-inner dark:border-blue-900/30 dark:bg-blue-900/10">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                    {{-- Catálogo --}}
                                    <div class="md:col-span-5">
                                        <label class="mb-1.5 block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Producto <span class="text-red-500">*</span></label>
                                        <select x-model="tempProdId" @change="updateTempProduct" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                            <option value="">— Buscar en inventario —</option>
                                            @foreach ($productos as $producto)
                                                <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_compra }}" data-nombre="{{ $producto->nombre }}" data-iva="{{ $producto->tasa_impuesto ?? 0 }}">
                                                    {{ $producto->nombre }} (Stock: {{ $producto->stock }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Cantidad --}}
                                    <div class="md:col-span-3">
                                        <label class="mb-1.5 block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Cant.</label>
                                        <input type="number" x-model="tempCantidad" min="1" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                    </div>
                                    {{-- Precio Compra --}}
                                    <div class="md:col-span-4">
                                        <label class="mb-1.5 block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Costo Unit.</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-medium">$</span>
                                            <input type="number" step="0.01" x-model="tempPrecio" min="0" placeholder="0.00" class="w-full rounded-lg border border-gray-300 bg-white pl-6 pr-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-full border-t border-blue-200/50 dark:border-blue-800/30 my-2 pt-2 grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                        {{-- IVA --}}
                                        <div class="md:col-span-3">
                                            <label class="mb-1.5 block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">IVA %</label>
                                            <select x-model="tempIva" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                                <option value="0">0% (Excluido)</option>
                                                <option value="5">5%</option>
                                                <option value="19">19% (General)</option>
                                            </select>
                                        </div>
                                        {{-- Descuento --}}
                                        <div class="md:col-span-3">
                                            <label class="mb-1.5 block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Desc % (Opc)</label>
                                            <input type="number" step="0.01" x-model="tempDesc" min="0" max="100" placeholder="0" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                        </div>
                                        {{-- Action --}}
                                        <div class="md:col-span-6 flex justify-end items-center gap-4">
                                            <button type="button" @click="addProducto(); addingItem = false" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition">
                                                Añadir a lista &rarr;
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tabla Principal de Ítems --}}
                            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-neutral-700 relative shadow-sm">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 dark:bg-neutral-800/40">
                                        <tr>
                                            <th class="border-b border-gray-200 dark:border-neutral-700 px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Producto</th>
                                            <th class="border-b border-gray-200 dark:border-neutral-700 px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-20">Cant.</th>
                                            <th class="border-b border-gray-200 dark:border-neutral-700 px-4 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-28">P. Unit</th>
                                            <th class="border-b border-gray-200 dark:border-neutral-700 px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-16">D. %</th>
                                            <th class="border-b border-gray-200 dark:border-neutral-700 px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-16">IVA %</th>
                                            <th class="border-b border-gray-200 dark:border-neutral-700 px-4 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-28">Subtotal</th>
                                            <th class="border-b border-gray-200 dark:border-neutral-700 px-3 py-3 w-12"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-neutral-700/60 dark:bg-neutral-900">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr class="group hover:bg-gray-50 dark:hover:bg-neutral-800/20 transition-colors">
                                                <td class="px-4 py-3">
                                                    {{-- Campos hidden --}}
                                                    <input type="hidden" :name="`productos[${index}][id]`" :value="item.id">
                                                    <input type="hidden" :name="`productos[${index}][cantidad]`" :value="item.cantidad">
                                                    <input type="hidden" :name="`productos[${index}][precio_compra]`" :value="item.precio">
                                                    <input type="hidden" :name="`productos[${index}][tasa_impuesto]`" :value="item.iva">
                                                    <input type="hidden" :name="`productos[${index}][porcentaje_descuento]`" :value="item.descuento">
                                                    
                                                    <p class="font-semibold text-gray-900 dark:text-white" x-text="item.nombre"></p>
                                                </td>
                                                <td class="px-4 py-3 text-center font-medium text-gray-700 dark:text-gray-300" x-text="item.cantidad"></td>
                                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400 tabular-nums" x-text="'$' + parseFloat(item.precio).toLocaleString('es-CO', {minimumFractionDigits:2})"></td>
                                                <td class="px-4 py-3 text-center">
                                                    <span x-show="item.descuento > 0" class="inline-flex items-center rounded-md bg-orange-50 px-1.5 py-0.5 text-[11px] font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20 dark:bg-orange-400/10 dark:text-orange-400" x-text="item.descuento + '%'"></span>
                                                    <span x-show="!item.descuento" class="text-gray-300">—</span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-1.5 py-0.5 text-[11px] font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-400/10 dark:text-blue-400" x-text="item.iva + '%'"></span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white tabular-nums" x-text="'$' + calcSubtotal(item).toLocaleString('es-CO', {minimumFractionDigits:2})"></td>
                                                <td class="px-3 py-3 text-center">
                                                    <button type="button" @click="removeItem(index)" class="inline-flex items-center justify-center rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500/20 dark:hover:bg-red-500/10 group-hover:text-red-500">
                                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>

                                        {{-- Estado vacío --}}
                                        <tr x-show="items.length === 0">
                                            <td colspan="7" class="px-6 py-12 text-center bg-gray-50/30 dark:bg-neutral-800/10">
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="h-12 w-12 rounded-full border-2 border-dashed border-gray-300 dark:border-neutral-600 flex items-center justify-center bg-gray-100 dark:bg-neutral-800">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                    </div>
                                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-2">La lista está vacía</p>
                                                    <button type="button" @click="addingItem = true" class="text-sm font-medium text-brand-600 hover:text-brand-500 dark:text-brand-400">Añadir el primer producto</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: Configuración General & Totales (Sidebar) --}}
                <div class="flex flex-col gap-6 lg:col-span-4 sticky top-6">
                    
                    {{-- Tarjeta de Configuración (Parámetros) --}}
                    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
                        <div class="border-b border-gray-100 px-6 py-4 dark:border-neutral-800/80">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Parámetros de Compra
                            </h3>
                        </div>
                        <div class="p-6 space-y-5">
                            
                            {{-- Sucursal --}}
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Sucursal de Ingreso <span class="text-red-500">*</span></label>
                                <select name="sucursal_id" required class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                    @foreach ($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('sucursal_id')
                                    <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Control Number --}}
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Nº de Compra</label>
                                <div class="flex items-center h-10 w-full rounded-xl border border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 px-4 gap-2">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                    <span class="font-mono text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ $proximoNumero }}</span>
                                    <span class="ml-auto text-[10px] uppercase font-bold text-emerald-500 dark:text-emerald-600 tracking-wider">Automático</span>
                                </div>
                            </div>

                            <hr class="border-gray-100 dark:border-neutral-800">

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Tipo <span class="text-red-500">*</span></label>
                                    <select name="tipo_compra" required x-model="tipoCompra" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                        <option value="contado">Contado</option>
                                        <option value="credito">Crédito</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Fecha Docto <span class="text-red-500">*</span></label>
                                    <input type="date" name="fecha_compra" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                </div>
                            </div>

                            <div x-show="tipoCompra === 'credito'" x-cloak class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Vence</label>
                                    <input type="date" name="fecha_vencimiento" :required="tipoCompra === 'credito'" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Método Pago</label>
                                    <input type="text" name="metodo_pago" placeholder="Transf, Cheque..." class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold text-gray-500 uppercase tracking-widest dark:text-gray-400">Notas Adicionales</label>
                                <textarea name="observaciones" rows="2" placeholder="Detalles o referencia de factura física..." class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white outline-none"></textarea>
                            </div>

                        </div>

                        {{-- Total Summary Area --}}
                        <div class="bg-gray-50 p-6 border-t border-gray-100 dark:bg-neutral-800/30 dark:border-neutral-800">
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 dark:text-gray-400 font-medium">Subtotal Neto</span>
                                    <span class="text-gray-800 dark:text-gray-200 font-semibold tabular-nums" x-text="'$' + Number(getTotalBruto()).toLocaleString('es-CO', {minimumFractionDigits:2})"></span>
                                </div>
                                <div class="flex justify-between items-center text-sm" x-show="getTotalDescuentos() > 0">
                                    <span class="text-red-500 font-medium">Descuentos</span>
                                    <span class="text-red-600 dark:text-red-400 font-semibold tabular-nums" x-text="'- $' + Number(getTotalDescuentos()).toLocaleString('es-CO', {minimumFractionDigits:2})"></span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 dark:text-gray-400 font-medium">IVA / Impuestos</span>
                                    <span class="text-gray-800 dark:text-gray-200 font-semibold tabular-nums" x-text="'$' + Number(getTotalIva()).toLocaleString('es-CO', {minimumFractionDigits:2})"></span>
                                </div>
                                <div class="pt-4 mt-4 border-t border-gray-200 dark:border-neutral-700 flex justify-between items-end">
                                    <span class="text-xs uppercase font-bold tracking-widest text-gray-500 dark:text-gray-400">Total a Pagar</span>
                                    <span class="text-3xl font-black text-brand-600 dark:text-brand-400 tabular-nums" x-text="'$' + Number(getTotal()).toLocaleString('es-CO', {minimumFractionDigits:2})"></span>
                                </div>
                            </div>

                            <button type="submit" :disabled="items.length === 0"
                                class="w-full rounded-xl bg-brand-600 px-6 py-4 text-sm font-bold text-white shadow-md shadow-brand-500/20 hover:bg-brand-500 hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-brand-600 disabled:hover:shadow-md flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>Registrar Compra y CxP</span>
                            </button>
                            <p x-show="items.length === 0" class="text-center text-xs text-red-500 mt-3 font-semibold" x-cloak>Agrega al menos 1 producto para continuar</p>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Ocultar elementos x-cloak antes de cargar alpine */
[x-cloak] { display: none !important; }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('compraForm', () => ({
        tipoCompra: 'contado',
        items: [],
        tempProdId: '',
        tempCantidad: 1,
        tempPrecio: 0,
        tempIva: 0,
        tempDesc: 0,

        updateTempProduct() {
            if (!this.tempProdId) { this.tempPrecio = 0; this.tempIva = 0; return; }
            const select = document.querySelector('select[x-model="tempProdId"]');
            const opt = select ? select.options[select.selectedIndex] : null;
            if (opt) {
                this.tempPrecio = parseFloat(opt.dataset.precio || 0);
                this.tempIva    = parseFloat(opt.dataset.iva || 0);
            }
        },

        addProducto() {
            if (!this.tempProdId || this.tempCantidad <= 0 || this.tempPrecio < 0) {
                window.Notify.warning('Selecciona un producto y asegúrate de que los precios y cantidades sean válidos.');
                return;
            }

            const select = document.querySelector('select[x-model="tempProdId"]');
            const opt    = select ? select.options[select.selectedIndex] : null;
            const nombre = opt ? opt.dataset.nombre : '';

            const existingIndex = this.items.findIndex(i => i.id == this.tempProdId && i.precio == this.tempPrecio);
            if (existingIndex >= 0) {
                this.items[existingIndex].cantidad += parseInt(this.tempCantidad);
                this.items[existingIndex].iva       = parseFloat(this.tempIva);
                this.items[existingIndex].descuento = parseFloat(this.tempDesc);
            } else {
                this.items.push({
                    id       : this.tempProdId,
                    nombre   : nombre,
                    cantidad : parseInt(this.tempCantidad),
                    precio   : parseFloat(this.tempPrecio),
                    iva      : parseFloat(this.tempIva),
                    descuento: parseFloat(this.tempDesc) || 0,
                });
            }

            this.tempProdId = ''; this.tempCantidad = 1; this.tempPrecio = 0; this.tempIva = 0; this.tempDesc = 0;
        },

        removeItem(index) { this.items.splice(index, 1); },

        calcBaseNeta(item) {
            return item.cantidad * item.precio * (1 - (item.descuento || 0) / 100);
        },
        calcIva(item) {
            return this.calcBaseNeta(item) * (item.iva / 100);
        },
        calcSubtotal(item) {
            return this.calcBaseNeta(item) + this.calcIva(item);
        },

        getTotalBruto() {
            return this.items.reduce((s, i) => s + (i.cantidad * i.precio), 0).toFixed(2);
        },
        getTotalDescuentos() {
            return this.items.reduce((s, i) => s + (i.cantidad * i.precio * (i.descuento || 0) / 100), 0).toFixed(2);
        },
        getTotalIva() {
            return this.items.reduce((s, i) => s + this.calcIva(i), 0).toFixed(2);
        },
        getTotal() {
            return this.items.reduce((s, i) => s + this.calcSubtotal(i), 0).toFixed(2);
        }
    }));
});
</script>
@endsection
