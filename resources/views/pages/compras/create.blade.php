@extends('layouts.app')
@php
    $title = 'Registrar Compra';
@endphp

@section('content')

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            Registrar Nueva Compra
        </h2>

        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <a class="font-medium" href="{{ route('dashboard') }}">Dashboard /</a>
                </li>
                <li>
                    <a class="font-medium" href="{{ route('compras.index') }}">Compras /</a>
                </li>
                <li class="font-medium text-primary">Registrar</li>
            </ol>
        </nav>
    </div>

    @if (session('sweet_alert'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '{{ session('sweet_alert.type') }}',
                    title: '{{ session('sweet_alert.title') }}',
                    text: '{!! session('sweet_alert.message') !!}',
                    confirmButtonColor: '#3C50E0',
                });
            });
        </script>
    @endif

    @if ($errors->any())
        <div
            class="mb-6 flex w-full border-l-6 border-[#F87171] bg-[#F87171] bg-opacity-[15%] px-7 py-8 shadow-md dark:bg-[#1B1B24] dark:bg-opacity-30 md:p-9">
            <div class="mr-5 flex h-9 w-full max-w-[36px] items-center justify-center rounded-lg bg-[#F87171]">
                <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.4917 7.65579L11.106 12.2645C11.2545 12.4128 11.4715 12.5 11.6738 12.5C11.8762 12.5 12.0931 12.4128 12.2415 12.2645C12.5621 11.9445 12.5623 11.4317 12.2423 11.1114C12.2422 11.1113 12.2422 11.1112 12.2422 11.1111L7.62783 6.50187L12.2459 1.91158C12.5825 1.5748 12.5825 1.02828 12.2459 0.691497C11.9092 0.354719 11.3627 0.354719 11.026 0.691497L6.40928 5.28182L1.79155 0.691497C1.45484 0.354719 0.908311 0.354719 0.571605 0.691497C0.2349 1.02828 0.2349 1.5748 0.571605 1.91158L5.18889 6.50187L0.57791 11.1111C0.25732 11.4313 0.257152 11.9441 0.577172 12.2641C0.577415 12.2643 0.577662 12.2644 0.57791 12.2645C0.726294 12.4128 0.943268 12.5 1.1456 12.5C1.34794 12.5 1.56491 12.4128 1.7133 12.2645L6.4917 7.65579Z"
                        fill="#ffffff" />
                </svg>
            </div>
            <div class="w-full">
                <h5 class="mb-3 text-lg font-bold text-[#B45454]">
                    Hubo un problema con la información enviada
                </h5>
                <ul class="list-inside list-disc text-[#B45454]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Alpine.js Component for reactive form -->
    <div x-data="compraForm()" class="grid grid-cols-1 gap-9 sm:grid-cols-2">

        <div class="flex flex-col gap-9 col-span-1 sm:col-span-2">
            <!-- Form Card -->
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900 p-6 lg:p-8">

                <form action="{{ route('compras.store') }}" method="POST">
                    @csrf

                    <div class="p-6.5">

                        <h3 class="font-medium text-black dark:text-white mb-4">Información General de la Factura</h3>

                        <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                            <div class="w-full xl:w-1/2">
                                <label class="mb-3.5 block text-sm font-medium text-black dark:text-white">
                                    Proveedor <span class="text-meta-1">*</span>
                                </label>
                                <select name="proveedor_id" required
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                                    <option value="" disabled selected>Seleccione un proveedor</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">{{ $proveedor->empresa }}</option>
                                    @endforeach
                                </select>
                                @error('proveedor_id')
                                    <span class="text-sm text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="w-full xl:w-1/2">
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Sucursal Destino <span class="text-meta-1">*</span>
                                </label>
                                <select name="sucursal_id" required
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                                    @foreach ($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('sucursal_id')
                                    <span class="text-sm text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4.5 grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Número auto-generado (solo lectura) --}}
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Nº Compra <span class="text-xs text-gray-400 font-normal ml-1">(auto)</span>
                                </label>
                                <div class="flex items-center h-12 w-full rounded-xl border border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 px-4 gap-2">
                                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                    <span class="font-mono text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ $proximoNumero }}</span>
                                    <span class="ml-auto text-xs text-emerald-500 dark:text-emerald-600 italic">Correlativo</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Generado automáticamente al guardar.</p>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Fecha de Compra <span class="text-meta-1">*</span>
                                </label>
                                <input type="date" name="fecha_compra" required value="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 dark:border-gray-700 dark:text-white/90">
                                @error('fecha_compra')
                                    <span class="text-sm text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Tipo de Compra <span class="text-meta-1">*</span>
                                </label>
                                <select name="tipo_compra" required x-model="tipoCompra"
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 dark:border-gray-700 dark:text-white/90">
                                    <option value="contado">Contado</option>
                                    <option value="credito">Crédito</option>
                                </select>
                                @error('tipo_compra')
                                    <span class="text-sm text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div x-show="tipoCompra === 'credito'" style="display: none;"
                            class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                            <div class="w-full xl:w-1/2">
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Fecha de Vencimiento
                                </label>
                                <input type="date" name="fecha_vencimiento" :required="tipoCompra === 'credito'"
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                            </div>
                            <div class="w-full xl:w-1/2">
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                    Método de Pago
                                </label>
                                <input type="text" name="metodo_pago" placeholder="Ej. Transferencia, Cheque..."
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">
                                Observaciones
                            </label>
                            <textarea name="observaciones" rows="2"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90"></textarea>
                        </div>

                        <!-- SEPARATOR -->
                        <div class="border-b border-stroke dark:border-strokedark my-6"></div>

                        <h3 class="font-medium text-black dark:text-white mb-4">Detalle de Productos</h3>

                        <!-- Add product form -->
                        <div class="mb-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                <div class="md:col-span-3">
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-white/90">Producto</label>
                                    <select x-model="tempProdId" @change="updateTempProduct"
                                        class="w-full rounded-xl border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 focus:border-brand-500 dark:border-gray-700 dark:text-white/90">
                                        <option value="">Seleccione un producto...</option>
                                        @foreach ($productos as $producto)
                                            <option value="{{ $producto->id }}"
                                                data-precio="{{ $producto->precio_compra }}"
                                                data-nombre="{{ $producto->nombre }}"
                                                data-iva="{{ $producto->tasa_impuesto ?? 0 }}">
                                                {{ $producto->nombre }} (Stock: {{ $producto->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-white/90">Cant.</label>
                                    <input type="number" x-model="tempCantidad" min="1"
                                        class="w-full rounded-xl border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-white/90">Precio Compra</label>
                                    <input type="number" step="0.01" x-model="tempPrecio" min="0"
                                        class="w-full rounded-xl border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-white/90">IVA % (DIAN)</label>
                                    <select x-model="tempIva"
                                        class="w-full rounded-xl border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                                        <option value="0">0% (Excluido)</option>
                                        <option value="5">5%</option>
                                        <option value="19">19% (General)</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-white/90">Descuento %</label>
                                    <input type="number" step="0.01" x-model="tempDesc" min="0" max="100" placeholder="0"
                                        class="w-full rounded-xl border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">
                                </div>
                                <div class="md:col-span-2">
                                    <button type="button" @click="addProducto"
                                        class="w-full rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                                        + Agregar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Table of exact items -->
                        <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700 mb-6">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Producto</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cant.</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">P. Compra</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Desc %</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">IVA %</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">IVA ($)</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Subtotal</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acc.</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                                <span x-text="index + 1"></span>
                                                <input type="hidden" :name="`productos[${index}][id]`" :value="item.id">
                                                <input type="hidden" :name="`productos[${index}][cantidad]`" :value="item.cantidad">
                                                <input type="hidden" :name="`productos[${index}][precio_compra]`" :value="item.precio">
                                                <input type="hidden" :name="`productos[${index}][tasa_impuesto]`" :value="item.iva">
                                                <input type="hidden" :name="`productos[${index}][porcentaje_descuento]`" :value="item.descuento">
                                            </td>
                                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-white/90" x-text="item.nombre"></td>
                                            <td class="px-4 py-3 text-right text-gray-800 dark:text-white/90" x-text="item.cantidad"></td>
                                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400" x-text="'$' + parseFloat(item.precio).toFixed(2)"></td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 text-xs font-bold px-2 py-0.5" x-text="(item.descuento || 0) + '%'"></span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-bold px-2 py-0.5" x-text="item.iva + '%'"></span>
                                            </td>
                                            <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-medium" x-text="'$' + calcIva(item).toFixed(2)"></td>
                                            <td class="px-4 py-3 text-right font-bold text-gray-800 dark:text-white/90" x-text="'$' + calcSubtotal(item).toFixed(2)"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button" @click="removeItem(index)"
                                                    class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:border-red-800/30 dark:bg-red-500/10 dark:text-red-400 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="items.length === 0">
                                        <td colspan="9" class="px-6 py-8 text-center text-gray-400 dark:text-gray-500">
                                            <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" class="mx-auto mb-2 opacity-40"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            No se han agregado productos a la compra.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                                    <span x-text="index + 1"></span>
                                                    <input type="hidden" :name="`productos[${index}][id]`"
                                                        :value="item.id">
                                                    <input type="hidden" :name="`productos[${index}][cantidad]`"
                                                        :value="item.cantidad">
                                                    <input type="hidden" :name="`productos[${index}][precio_compra]`"
                                                        :value="item.precio">
                                                </td>
                                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90">
                                                    <span x-text="item.nombre"></span>
                                                </td>
                                                <td class="px-6 py-4 text-gray-800 dark:text-white/90 text-right">
                                                    <span x-text="item.cantidad"></span>
                                                </td>
                                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-right">
                                                    <span x-text="'$' + parseFloat(item.precio).toFixed(2)"></span>
                                                </td>
                                                <td
                                                    class="px-6 py-4 text-gray-800 dark:text-white/90 font-medium text-right">
                                                    <span x-text="'$' + (item.cantidad * item.precio).toFixed(2)"></span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <button type="button" @click="removeItem(index)"
                                                        class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:border-red-800/30 dark:bg-red-500/10 dark:text-red-400 transition-colors"
                                                        title="Eliminar">
                                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="items.length === 0">
                                            <td colspan="6"
                                                class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                                No se han agregado productos a la compra.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Totals -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                                <div class="text-sm space-y-2">
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>Subtotal bruto:</span>
                                        <span class="font-bold text-gray-900 dark:text-gray-100" x-text="'$' + getTotalBruto()"></span>
                                    </div>
                                    <div class="flex justify-between text-red-500" x-show="getTotalDescuentos() !== '0.00'">
                                        <span>─ Descuentos:</span>
                                        <span class="font-bold" x-text="'- $' + getTotalDescuentos()"></span>
                                    </div>
                                    <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                                        <span>+ IVA Total:</span>
                                        <span class="font-bold" x-text="'$' + getTotalIva()"></span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <span class="font-bold text-gray-900 dark:text-gray-100">TOTAL A PAGAR:</span>
                                        <span class="font-black text-xl text-brand-600 dark:text-brand-400" x-text="'$' + getTotal()"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit action -->
                            <div class="mt-6 flex justify-end gap-3">
                                <a href="{{ route('compras.index') }}"
                                    class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors">Cancelar</a>
                                <button type="submit" :disabled="items.length === 0"
                                    class="rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-bold text-white hover:bg-brand-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    ✅ Registrar Compra
                                </button>
                            </div>
                            <p x-show="items.length === 0" class="text-right text-sm text-red-500 mt-2">Agregue al menos un producto para continuar.</p>
                        </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Alpine -->
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
                        Swal.fire({ icon: 'warning', title: 'Campos incompletos', text: 'Asegúrese de seleccionar un producto y que cantidad/precio sean válidos.' });
                        return;
                    }

                    const select = document.querySelector('select[x-model="tempProdId"]');
                    const opt    = select ? select.options[select.selectedIndex] : null;
                    const nombre = opt ? opt.dataset.nombre : '';

                    const existingIndex = this.items.findIndex(i => i.id == this.tempProdId);
                    if (existingIndex >= 0) {
                        this.items[existingIndex].cantidad += parseInt(this.tempCantidad);
                        this.items[existingIndex].precio    = parseFloat(this.tempPrecio);
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
