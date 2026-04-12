@extends('layouts.app')

@section('content')

{{-- Modo táctil CSS --}}
@if(($posSettings['pos_modo_tactil'] ?? '0') == '1')
<style>
.pos-tactil .pos-product-card { min-height: 160px; }
.pos-tactil .pos-product-card img { height: 100px; }
.pos-tactil .pos-product-card .pos-product-placeholder { height: 100px; }
.pos-tactil .pos-product-card .pos-product-name { font-size: 0.9rem; }
.pos-tactil .pos-product-card .pos-product-price { font-size: 1.1rem; }
.pos-tactil .pos-product-card .pos-product-stock { font-size: 0.8rem; }
.pos-tactil .pos-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 0.75rem !important; }
@media (min-width: 640px) { .pos-tactil .pos-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; } }
@media (min-width: 1024px) { .pos-tactil .pos-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; } }
@media (min-width: 1280px) { .pos-tactil .pos-grid { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; } }
</style>
@endif

    {{-- Full-height POS layout --}}
    <div class="flex flex-col h-[calc(100vh-64px)] overflow-hidden bg-gray-100 dark:bg-neutral-950{{ ($posSettings['pos_modo_tactil'] ?? '0') == '1' ? ' pos-tactil' : '' }}" x-data="posApp()">

        {{-- ── MOBILE TAB BAR (visible only on small screens) ── --}}
        <div class="flex lg:hidden border-b border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900">
            <button @click="mobileTab = 'productos'"
                :class="mobileTab === 'productos' ? 'border-b-2 border-brand-500 text-brand-600 font-semibold' : 'text-gray-500'"
                class="flex-1 py-3 text-sm transition">
                🛒 Productos
            </button>
            <button @click="mobileTab = 'carrito'"
                :class="mobileTab === 'carrito' ? 'border-b-2 border-brand-500 text-brand-600 font-semibold' : 'text-gray-500'"
                class="flex-1 py-3 text-sm transition relative">
                🧾 Carrito
                <span x-show="carrito.length > 0"
                    x-text="carrito.length"
                    class="absolute top-1.5 right-6 inline-flex items-center justify-center w-5 h-5 rounded-full bg-brand-500 text-white text-xs font-bold">
                </span>
            </button>
        </div>

        {{-- ── MAIN AREA ── --}}
        <div class="flex flex-1 min-h-0 overflow-hidden">

            {{-- LEFT PANEL: Product Catalog --}}
            <div class="flex flex-col flex-1 min-w-0 overflow-hidden"
                 :class="{ 'hidden lg:flex': mobileTab !== 'productos' }">

                {{-- Search & Filters Bar --}}
                                <div class="p-3 bg-white dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-800 flex flex-wrap gap-2 items-center">
                    <!-- Mode Switcher -->
                    <div class="relative flex gap-1 bg-zinc-100 dark:bg-zinc-800/50 p-1 border border-zinc-200 dark:border-white/10 rounded-lg hidden sm:flex shrink-0">
                        <div class="absolute top-1 left-1 bottom-1 w-8 rounded-md bg-white dark:bg-zinc-700 shadow-sm transition-transform duration-300 ease-out"
                            :class="{'translate-x-0': viewMode === 'cards', 'translate-x-[36px]': viewMode === 'stack'}"></div>
                        <button @click="viewMode = 'cards'" class="relative z-10 flex h-7 w-8 items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors" :class="viewMode === 'cards' ? '!text-brand-600 dark:!text-brand-400' : ''" title="Vista Cuadrícula">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        </button>
                        <button @click="viewMode = 'stack'" class="relative z-10 flex h-7 w-8 items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors" :class="viewMode === 'stack' ? '!text-brand-600 dark:!text-brand-400' : ''" title="Vista Lista">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        </button>
                    </div>
                    <div class="relative flex-1 min-w-[160px]">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input x-model="busqueda" id="busqueda-input" x-ref="buscadorPos" type="text"
                            placeholder="Buscar o escanear código..."
                            autocomplete="off"
                            class="w-full h-9 rounded-l-lg border border-gray-300 border-r-0 bg-white pl-9 pr-4 text-sm focus:border-brand-500 focus:ring-0 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            @keyup.enter="buscarPorCodigo()">
                    </div>
                    {{-- Botón Cámara POS --}}
                    <button type="button"
                        onclick="abrirScannerPOS()"
                        title="Escanear con cámara"
                        class="h-9 inline-flex items-center gap-1.5 rounded-r-lg border border-gray-300 border-l-0 bg-brand-50 px-3 text-brand-600 hover:bg-brand-100 dark:border-neutral-700 dark:bg-brand-500/10 dark:text-brand-400 transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-xs font-semibold hidden sm:inline">Cámara</span>
                    </button>
                    <select x-model="categoriaFiltro"
                        class="h-9 rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">Todas las categorías</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>

                    @if ($cajaAbierta)
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $cajaAbierta->sucursal->nombre }}
                        </span>
                    @else
                        <a href="{{ route('caja.index') }}"
                            class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                            ⚠ Abrir Caja
                        </a>
                    @endif

                    <button @click="verEspera()"
                        class="relative inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Espera
                    </button>
                </div>

                {{-- Product Grid --}}
                <div class="flex-1 overflow-y-auto p-3">
                    <div class="pos-grid grid gap-3" :class="viewMode === 'cards' ? 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5' : 'grid-cols-1 md:grid-cols-2'">
                        @foreach ($productos as $producto)
                            <div x-show="productoVisible({{ $producto->id }}, {{ $producto->categoria_id ?? 'null' }})"
                                @click="agregarProductoMobile({{ $producto->id }})"
                                class="group cursor-pointer rounded-xl border border-gray-200 bg-white hover:border-brand-400 hover:shadow-md transition-all duration-200 dark:border-neutral-800/80 dark:bg-[#1e1e1e] dark:hover:border-brand-500/50 overflow-hidden"
                                :class="viewMode === 'cards' ? 'pos-product-card flex flex-col' : 'flex flex-row items-center p-3 gap-4 hover:bg-brand-50/20 dark:hover:bg-neutral-800/10'"
                                data-id="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}"
                                data-precio="{{ $producto->precio_venta }}" data-impuesto="{{ $producto->impuesto }}"
                                data-stock="{{ $producto->stock }}" data-codigo="{{ $producto->codigo_barras }}"
                                data-categoria="{{ $producto->categoria_id }}">
                                
                                <div :class="viewMode === 'cards' ? 'pos-product-placeholder h-24 w-full bg-gray-100 dark:bg-neutral-800' : 'h-14 w-14 flex-shrink-0 rounded-lg bg-gray-100 dark:bg-neutral-800 overflow-hidden'">
                                    @if ($producto->imagen_url)
                                        <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}"
                                            class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center">
                                            <svg class="w-1/2 h-1/2 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div :class="viewMode === 'cards' ? 'p-2' : 'flex-1 min-w-0 flex items-center justify-between'">
                                    <div :class="viewMode === 'cards' ? '' : 'flex-1 pr-2'">
                                        <p class="pos-product-name font-bold text-gray-800 dark:text-white/90 leading-tight truncate" :class="viewMode === 'cards' ? 'text-xs' : 'text-sm'">
                                            {{ $producto->nombre }}
                                        </p>
                                        <template x-if="viewMode === 'stack'">
                                            <div class="mt-1 flex items-center gap-2">
                                                <span class="rounded bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">{{ optional($producto->categoria)->nombre ?? 'Base' }}</span>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <div :class="viewMode === 'cards' ? 'mt-0.5' : 'text-right'">
                                        <p class="pos-product-price font-bold text-brand-600 dark:text-brand-400" :class="viewMode === 'cards' ? 'text-sm' : 'text-base'">
                                            ${{ number_format($producto->precio_venta, 2) }}
                                        </p>
                                        <p class="pos-product-stock text-xs text-gray-400 font-medium">Stock: {{ $producto->stock }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT PANEL: Cart --}}
            <div class="flex flex-col bg-white dark:bg-neutral-900 border-l border-gray-200 dark:border-neutral-800
                        w-full lg:w-96 lg:flex-shrink-0"
                 :class="{ 'hidden lg:flex': mobileTab !== 'carrito', 'flex': mobileTab === 'carrito' }">

                {{-- Cart Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-neutral-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-800 dark:text-white">Carrito de Venta</h2>
                        <p x-text="'N° de venta: ' + (ventaNumero || 'Pendiente')" class="text-xs text-gray-400"></p>
                    </div>
                    <button @click="limpiarCarrito()"
                        class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-500 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>

                {{-- Cliente selector --}}
                <div class="px-4 py-2 border-b border-gray-100 dark:border-neutral-800 space-y-1">
                    <select x-model="clienteId"
                        class="h-9 w-full rounded-lg border border-gray-300 bg-white px-3 text-xs focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">👤 Consumidor Final</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre_completo }}</option>
                        @endforeach
                    </select>
                    
                    {{-- Información de Crédito del Cliente --}}
                    <template x-if="clienteId && clientesData[clienteId]">
                        <div class="flex items-center justify-between px-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Crédito Disp:</span>
                                <span class="text-xs font-bold" 
                                      :class="clientesData[clienteId].credito_disponible > 0 ? 'text-emerald-600' : 'text-red-500'"
                                      x-text="'$' + clientesData[clienteId].credito_disponible.toFixed(2)"></span>
                            </div>
                            <template x-if="clientesData[clienteId].es_moroso">
                                <span class="flex items-center gap-1 rounded bg-red-100 px-1.5 py-0.5 text-[10px] font-black uppercase text-red-600 animate-pulse">
                                    ⚠️ Moroso
                                </span>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Cart Items --}}
                <div class="flex-1 overflow-y-auto p-3 space-y-2">
                    <template x-if="carrito.length === 0">
                        <div class="flex flex-col items-center justify-center h-full text-center py-12">
                            <svg class="h-16 w-16 text-gray-200 dark:text-gray-700 mb-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-sm text-gray-400">Haz clic en un producto para agregarlo</p>
                        </div>
                    </template>
                    <template x-for="(item, index) in carrito" :key="index">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-neutral-700 dark:bg-neutral-800">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-xs font-semibold text-gray-800 dark:text-white leading-tight flex-1"
                                    x-text="item.nombre"></p>
                                <button @click="eliminarItem(index)" class="text-gray-400 hover:text-red-500 flex-shrink-0">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <button @click="cambiarCantidad(index, -1)"
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 dark:border-neutral-600 dark:bg-neutral-700 dark:text-gray-300">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input type="number" :value="item.cantidad"
                                        @change="setCantidad(index, $event.target.value)" min="1"
                                        :max="item.stock"
                                        class="w-12 h-7 rounded-lg border border-gray-300 bg-white text-center text-xs font-medium focus:border-brand-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                                    <button @click="cambiarCantidad(index, 1)"
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 dark:border-neutral-600 dark:bg-neutral-700 dark:text-gray-300">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-400" x-text="'$' + item.precio_unitario.toFixed(2) + ' c/u'">
                                    </p>
                                    <p class="text-sm font-bold text-gray-800 dark:text-white"
                                        x-text="'$' + (item.precio_unitario * item.cantidad).toFixed(2)"></p>
                                </div>
                            </div>
                            <div class="mt-1 text-xs text-gray-400 flex justify-between">
                                <span x-text="'ITBMS ' + item.impuesto + '%'"></span>
                                <span
                                    x-text="'$' + ((item.precio_unitario * item.cantidad) * (item.impuesto/100)).toFixed(2)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Totals & Actions --}}
                <div class="border-t border-gray-200 dark:border-neutral-800 p-4 space-y-2">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span x-text="'$' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>ITBMS</span>
                        <span x-text="'$' + itbms.toFixed(2)"></span>
                    </div>
                    <div
                        class="flex justify-between text-lg font-bold text-gray-800 dark:text-white border-t border-gray-200 dark:border-neutral-700 pt-2">
                        <span>TOTAL</span>
                        <span x-text="'$' + total.toFixed(2)"></span>
                    </div>

                    <div class="flex gap-2 pt-1">
                        <button @click="pausarVenta()" :disabled="carrito.length === 0"
                            class="flex-1 rounded-lg border border-amber-400 py-2.5 text-xs font-semibold text-amber-600 hover:bg-amber-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            Pausar
                        </button>
                        <button @click="abrirPago()" :disabled="carrito.length === 0"
                            class="flex-2 flex-1 rounded-lg bg-brand-500 py-2.5 text-sm font-bold text-white hover:bg-brand-600 disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center justify-center gap-1.5">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Cobrar $<span x-text="total.toFixed(2)"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
     MODAL PAGO
============================================================ --}}
    <div x-data x-show="$store.posModal.open" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-neutral-900 shadow-2xl overflow-hidden"
            x-show="$store.posModal.open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-neutral-800">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Procesar Pago</h3>
                <button @click="$store.posModal.open = false"
                    class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 dark:hover:bg-neutral-800">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal content rendered by Alpine --}}
            <div x-data="pagoModal()" class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                {{-- Total a pagar --}}
                <div class="rounded-xl bg-brand-50 dark:bg-brand-900/20 p-4 text-center">
                    <p class="text-xs font-medium text-brand-600 dark:text-brand-400 uppercase tracking-wider">Total a
                        Pagar</p>
                    <p class="text-3xl font-black text-brand-700 dark:text-brand-300 mt-1"
                        x-text="'$' + $store.posData.total.toFixed(2)"></p>
                </div>

                {{-- Métodos de pago --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Métodos de Pago</label>
                        <button @click="agregarPago()" type="button"
                            class="text-xs text-brand-500 hover:text-brand-600 font-medium">+ Agregar</button>
                    </div>

                    <template x-for="(pago, idx) in pagos" :key="idx">
                        <div class="mb-3 rounded-xl border border-gray-200 dark:border-neutral-700 p-3 space-y-2">
                            <div class="flex gap-2">
                                    <select x-model="pago.metodo" @change="onMetodoChange(idx)"
                                        class="flex-1 h-9 rounded-lg border border-gray-300 bg-white px-2 text-xs focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                        <template x-if="PAGO_EFECTIVO"><option value="efectivo">💵 Efectivo</option></template>
                                        <template x-if="PAGO_TARJETA"><option value="tarjeta">💳 Tarjeta</option></template>
                                        <template x-if="PAGO_TRANSFERENCIA"><option value="transferencia">🏦 Transferencia</option></template>
                                        <template x-if="PAGO_YAPPY"><option value="yappy">📱 Yappy / Nequi</option></template>
                                        <template x-if="POS_PERMITE_CREDITO"><option value="credito">📋 Crédito</option></template>
                                    </select>
                                <input x-model.number="pago.monto" type="number" step="0.01" min="0"
                                    placeholder="0.00" @input="calcularCambio()"
                                    class="w-28 h-9 rounded-lg border border-gray-300 bg-white px-2 text-xs text-right focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                <button x-show="pagos.length > 1" @click="eliminarPago(idx)"
                                    class="text-gray-400 hover:text-red-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            {{-- Referencia (obligatoria para electrónicos) --}}
                            <template x-if="pago.metodo !== 'efectivo' && pago.metodo !== 'credito'">
                                <input x-model="pago.referencia" type="text"
                                    placeholder="N° Referencia / Voucher (obligatorio)"
                                    class="w-full h-8 rounded-lg border border-orange-300 bg-white px-2 text-xs focus:border-orange-400 dark:border-orange-700 dark:bg-neutral-800 dark:text-white">
                            </template>
                            {{-- Fecha Vencimiento (solo para Crédito) --}}
                            <template x-if="pago.metodo === 'credito'">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-gray-400 uppercase">Fecha de Vencimiento</label>
                                    <input x-model="fechaVencimiento" type="date"
                                        class="w-full h-8 rounded-lg border border-brand-300 bg-white px-2 text-xs focus:border-brand-500 dark:border-brand-700 dark:bg-neutral-800 dark:text-white">
                                </div>
                            </template>

                            <template x-if="pago.metodo === 'tarjeta'">
                                <div class="flex gap-2">
                                    <select x-model="pago.tipo_tarjeta"
                                        class="flex-1 h-8 rounded-lg border border-gray-300 bg-white px-2 text-xs dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                        <option value="">Tipo tarjeta</option>
                                        <option value="debito">Débito</option>
                                        <option value="credito">Crédito</option>
                                    </select>
                                    <input x-model="pago.banco" type="text" placeholder="Banco (opcional)"
                                        class="flex-1 h-8 rounded-lg border border-gray-300 bg-white px-2 text-xs dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Cambio (efectivo) --}}
                <template x-if="mostrarCambio && cambio >= 0">
                    <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 p-3 flex justify-between items-center">
                        <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Cambio a devolver</span>
                        <span class="text-xl font-black text-emerald-700 dark:text-emerald-400"
                            x-text="'$' + cambio.toFixed(2)"></span>
                    </div>
                </template>
                <template x-if="faltante > 0">
                    <div class="rounded-xl bg-red-50 dark:bg-red-900/20 p-3 flex justify-between items-center">
                        <span class="text-sm font-semibold text-red-600">Falta pagar</span>
                        <span class="text-xl font-black text-red-600" x-text="'$' + faltante.toFixed(2)"></span>
                    </div>
                </template>

                {{-- Facturación Electrónica DIAN Toggle --}}
                <div class="rounded-xl border border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/50 p-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="text-brand-500">
                                <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Factura Electrónica API
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Timbrar y enviar a la DIAN</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="generarFacturaDian" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-neutral-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-neutral-600 peer-checked:bg-brand-500"></div>
                    </label>
                </div>

                <template x-if="generarFacturaDian">
                    <div class="rounded-xl border border-brand-200 dark:border-brand-900/50 bg-brand-50/50 dark:bg-brand-900/20 p-3 space-y-3 mt-2">
                        <div>
                            <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Forma de Pago (DIAN)</label>
                            <select x-model="formaPagoDian" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                <option value="1">1 - Contado</option>
                                <option value="2">2 - Crédito</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Método de Pago (DIAN)</label>
                            <select x-model="metodoPagoDian" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs focus:border-brand-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                <option value="10">10 - Efectivo</option>
                                <option value="42">42 - Consignación bancaria</option>
                                <option value="47">47 - Transferencia Débito Bancaria</option>
                                <option value="48">48 - Tarjeta de crédito</option>
                                <option value="49">49 - Tarjeta débito</option>
                                <option value="1">1 - Instrumento no definido</option>
                            </select>
                        </div>
                    </div>
                </template>

                <button @click="confirmarPago()" :disabled="cargando || faltante > 0"
                    class="w-full rounded-xl bg-emerald-600 py-3 text-base font-bold text-white hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center gap-2">
                    <template x-if="!cargando">
                        <span>✓ Confirmar Venta</span>
                    </template>
                    <template x-if="cargando">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Procesando...
                        </span>
                    </template>
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Ventas en Espera --}}
    <div x-data x-show="$store.posEspera.open" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-neutral-900 shadow-2xl overflow-hidden" @click.stop>
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-neutral-800">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Ventas en Espera</h3>
                <button @click="$store.posEspera.open = false" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-5 max-h-96 overflow-y-auto" x-data="esperaModal()">
                <template x-if="lista.length === 0">
                    <p class="text-center text-sm text-gray-400 py-8">No hay ventas en espera.</p>
                </template>
                <template x-for="item in lista" :key="item.id">
                    <div
                        class="mb-3 rounded-xl border border-gray-200 dark:border-neutral-700 p-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white" x-text="item.nombre"></p>
                            <p class="text-xs text-gray-400" x-text="item.created_at"></p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="retomar(item.id)"
                                class="rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-600">Retomar</button>
                            <button @click="eliminar(item.id)"
                                class="rounded-lg border border-red-300 px-2 py-1.5 text-xs font-semibold text-red-500 hover:bg-red-50">✕</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        // ─── PHP → JS Settings ──────────────────────────────────────────────
        const POS_PERMITE_CREDITO      = {{ ($posSettings['pos_ventas_credito']      ?? '0') == '1' ? 'true' : 'false' }};
        const POS_PERMITE_SIN_CLIENTE  = {{ ($posSettings['pos_ventas_sin_cliente']  ?? '1') == '1' ? 'true' : 'false' }};
        const POS_CONFIRMACION         = {{ ($posSettings['pos_confirmacion_venta']  ?? '1') == '1' ? 'true' : 'false' }};
        const POS_VENTA_RAPIDA         = {{ ($posSettings['pos_venta_rapida']        ?? '0') == '1' ? 'true' : 'false' }};
        const POS_AUTOFOCUS            = {{ ($posSettings['pos_autofocus_buscador']  ?? '1') == '1' ? 'true' : 'false' }};

        // Payment Methods (using get as they are in the 'pagos' group, we get them via Setting model directly here)
        @php
            $pagosSettings = \App\Models\Setting::group('pagos');
        @endphp
        const PAGO_EFECTIVO            = {{ ($pagosSettings['pago_efectivo']         ?? '1') == '1' ? 'true' : 'false' }};
        const PAGO_TARJETA             = {{ ($pagosSettings['pago_tarjeta']          ?? '1') == '1' ? 'true' : 'false' }};
        const PAGO_TRANSFERENCIA       = {{ ($pagosSettings['pago_transferencia']    ?? '1') == '1' ? 'true' : 'false' }};
        const PAGO_YAPPY               = {{ ($pagosSettings['pago_yappy']            ?? '1') == '1' ? 'true' : 'false' }};
        const REF_TARJETA              = {{ ($pagosSettings['pago_referencia_tarjeta'] ?? '0') == '1' ? 'true' : 'false' }};
        const REF_TRANSFERENCIA        = {{ ($pagosSettings['pago_referencia_transferencia'] ?? '1') == '1' ? 'true' : 'false' }};

        // ─── Productos Data ─────────────────────────────────────────────────
        const productosData = {!! json_encode($productos->map(fn($p) => [
            'id' => $p->id,
            'nombre' => $p->nombre,
            'precio_unitario' => (float) $p->precio_venta,
            'impuesto' => (float) $p->impuesto,
            'stock' => $p->stock,
            'codigo_barras' => $p->codigo_barras,
            'categoria_id' => $p->categoria_id,
        ])->values()->all()) !!};

        const clientesData = {!! json_encode($clientesData) !!};



        const SUCURSAL_ID = {{ $cajaAbierta?->sucursal_id ?? ($sucursales->first()?->id ?? 0) }};
        const CAJA_ID = {{ $cajaAbierta?->id ?? 'null' }};
        const VENTA_STORE = '{{ route('caja.ventas.store') }}';
        const ESPERA_STORE = '{{ route('caja.espera.store') }}';
        const ESPERA_IDX = '{{ route('caja.espera.index') }}';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ─── Alpine Stores ──────────────────────────────────────────────────
        document.addEventListener('alpine:init', () => {
            Alpine.store('posModal', {
                open: false
            });
            Alpine.store('posEspera', {
                open: false
            });
            Alpine.store('posData', {
                total: 0,
                carrito: [],
                clienteId: null,
                sucursalId: SUCURSAL_ID
            });
        });

        // ─── Main POS App ───────────────────────────────────────────────────
        function posApp() {
            return {
                viewMode: 'cards',
                mobileTab: 'productos',
                busqueda: '',
                categoriaFiltro: '',
                carrito: [],
                clienteId: null,

                init() {
                    // Auto-focus search input
                    if (POS_AUTOFOCUS) {
                        this.$nextTick(() => {
                            const el = document.getElementById('busqueda-input');
                            if (el) el.focus();
                        });
                    }

                    // Prevenir cierre accidental de pestaña si hay items en carrito
                    window.addEventListener('beforeunload', (evento) => {
                        if (this.carrito.length > 0) {
                            evento.preventDefault();
                            evento.returnValue = 'Tienes un proceso de venta a medias en el POS. Si cierras la ventana el carrito se perderá.';
                            return evento.returnValue;
                        }
                    });
                },

                get subtotal() {
                    return this.carrito.reduce((s, i) => s + i.precio_unitario * i.cantidad, 0);
                },
                get itbms() {
                    return this.carrito.reduce((s, i) => s + (i.precio_unitario * i.cantidad) * (i.impuesto / 100), 0);
                },
                get total() {
                    return this.subtotal + this.itbms;
                },
                get ventaNumero() {
                    return null;
                },

                productoVisible(id, catId) {
                    const prod = productosData.find(p => p.id === id);
                    if (!prod) return false;
                    const matchCat = !this.categoriaFiltro || catId == this.categoriaFiltro;
                    if (!this.busqueda) return matchCat;
                    const term = this.busqueda.toLowerCase();
                    const matchName = prod.nombre.toLowerCase().includes(term);
                    const matchCode = prod.codigo_barras && prod.codigo_barras.toLowerCase().includes(term);
                    return matchCat && (matchName || matchCode);
                },

                buscarPorCodigo() {
                    const term = this.busqueda.trim();
                    if (!term) return;
                    const prod = productosData.find(p => p.codigo_barras === term);
                    if (prod) {
                        this.agregarProducto(prod.id);
                        this.busqueda = '';
                        // Refocus buscador para el próximo scan
                        this.$nextTick(() => {
                            const el = document.getElementById('busqueda-input');
                            if (el) el.focus();
                        });
                    }
                },

                // Mobile: add product (no auto-switch per user request)
                agregarProductoMobile(id) {
                    this.agregarProducto(id);
                },

                agregarProducto(id) {
                    const prod = productosData.find(p => p.id === id);
                    if (!prod) return;
                    const existing = this.carrito.find(i => i.id === id);
                    if (existing) {
                        if (existing.cantidad < prod.stock) {
                            existing.cantidad++;
                        } else {
                            window.Notify.warning(`Sin stock - Stock disponible: ${prod.stock}`);
                        }
                    } else {
                        this.carrito.push({
                            ...prod,
                            cantidad: 1
                        });
                    }

                    // Venta rápida: abrir pago automáticamente si el carrito tiene al menos 1 item
                    if (POS_VENTA_RAPIDA && this.carrito.length > 0) {
                        this.$nextTick(() => this.abrirPago());
                    }
                },

                eliminarItem(index) {
                    this.carrito.splice(index, 1);
                },

                cambiarCantidad(index, delta) {
                    const item = this.carrito[index];
                    const nueva = item.cantidad + delta;
                    if (nueva < 1) {
                        this.eliminarItem(index);
                        return;
                    }
                    if (nueva > item.stock) {
                        window.Notify.warning(`Sin stock - Stock disponible: ${item.stock}`);
                        return;
                    }
                    item.cantidad = nueva;
                },

                setCantidad(index, val) {
                    const item = this.carrito[index];
                    const qty = parseInt(val) || 1;
                    if (qty > item.stock) {
                        item.cantidad = item.stock;
                        return;
                    }
                    if (qty < 1) {
                        this.eliminarItem(index);
                        return;
                    }
                    item.cantidad = qty;
                },

                limpiarCarrito() {
                    if (this.carrito.length === 0) return;
                    window.Confirm.show(
                        '¿Limpiar carrito?',
                        '¿Deseas vaciar el carrito actual?',
                        'Sí, limpiar',
                        'Cancelar',
                        () => { this.carrito = []; },
                        () => {},
                        { okButtonBackground: '#dc2626' }
                    );
                },

                abrirPago() {
                    if (this.carrito.length === 0) return;

                    // Check: si venta sin cliente está deshabilitada
                    if (!POS_PERMITE_SIN_CLIENTE && !this.clienteId) {
                        window.Notify.warning('Cliente requerido - La configuración del sistema requiere seleccionar un cliente antes de cobrar.');
                        return;
                    }

                    Alpine.store('posData', {
                        total: this.total,
                        carrito: this.carrito,
                        clienteId: this.clienteId,
                        sucursalId: SUCURSAL_ID
                    });
                    Alpine.store('posModal').open = true;
                },

                pausarVenta() {
                    if (this.carrito.length === 0) return;
                    let nombre = prompt('Escribe un nombre para identificar esta orden en espera (Ej: Mesa 3, Pedro...):');
                    if (!nombre) return;
                    
                    window.Loading.pulse('Guardando...');
                    fetch(ESPERA_STORE, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify({
                            nombre: nombre,
                            sucursal_id: SUCURSAL_ID,
                            carrito: this.carrito
                        }),
                    }).then(r => r.json()).then(data => {
                        window.Loading.remove();
                        if (data.success) {
                            this.carrito = [];
                            window.Notify.success(data.message || 'Venta guardada');
                        }
                    }).catch(err => {
                        window.Loading.remove();
                        window.Notify.failure('Error al pausar la venta');
                    });
                },

                verEspera() {
                    Alpine.store('posEspera').open = true;
                }
            };
        }

        // ─── Pago Modal ─────────────────────────────────────────────────────
        function pagoModal() {
            return {
                pagos: [{
                    metodo: 'efectivo',
                    monto: 0,
                    referencia: '',
                    tipo_tarjeta: '',
                    banco: ''
                }],
                fechaVencimiento: new Date(new Date().setDate(new Date().getDate() + 30)).toISOString().split('T')[0],
                cargando: false,
                generarFacturaDian: false,
                formaPagoDian: '1',
                metodoPagoDian: '10',

                get montoTotal() {
                    return this.$store.posData.total;
                },
                get totalPagado() {
                    return this.pagos.reduce((s, p) => s + (parseFloat(p.monto) || 0), 0);
                },
                get cambio() {
                    return this.totalPagado - this.montoTotal;
                },
                get faltante() {
                    return Math.max(0, this.montoTotal - this.totalPagado);
                },
                get mostrarCambio() {
                    return this.pagos.some(p => p.metodo === 'efectivo') && this.cambio >= 0;
                },

                init() {
                    let defaultMetodo = 'efectivo';
                    if (!PAGO_EFECTIVO) {
                        if (PAGO_TARJETA) defaultMetodo = 'tarjeta';
                        else if (PAGO_YAPPY) defaultMetodo = 'yappy';
                        else if (PAGO_TRANSFERENCIA) defaultMetodo = 'transferencia';
                        else if (POS_PERMITE_CREDITO) defaultMetodo = 'credito';
                    }

                    this.pagos = [{
                        metodo: defaultMetodo,
                        monto: this.$store.posData.total.toFixed(2),
                        referencia: '',
                        tipo_tarjeta: '',
                        banco: ''
                    }];
                },

                agregarPago() {
                    let defaultMetodo = 'tarjeta';
                    if (!PAGO_TARJETA) {
                        if (PAGO_EFECTIVO) defaultMetodo = 'efectivo';
                        else if (PAGO_YAPPY) defaultMetodo = 'yappy';
                        else if (PAGO_TRANSFERENCIA) defaultMetodo = 'transferencia';
                    }

                    this.pagos.push({
                        metodo: defaultMetodo,
                        monto: this.faltante.toFixed(2),
                        referencia: '',
                        tipo_tarjeta: '',
                        banco: ''
                    });
                },

                eliminarPago(idx) {
                    this.pagos.splice(idx, 1);
                },

                onMetodoChange(idx) {
                    const pago = this.pagos[idx];
                    if (pago.metodo === 'credito') {
                        if (!this.$store.posData.clienteId) {
                            window.Notify.warning('Cliente requerido - Debe seleccionar un cliente antes de usar Crédito.');
                            pago.metodo = 'efectivo';
                            return;
                        }
                        
                        const cliente = clientesData[this.$store.posData.clienteId];
                        if (!cliente || cliente.credito_disponible <= 0) {
                            window.Notify.failure('Sin crédito - El cliente no tiene crédito disponible o rebasó su límite.');
                            pago.metodo = 'efectivo';
                            pago.monto = 0;
                            return;
                        }

                        // Calcular el resto por pagar omitiendo el monto actual de este método
                        const otrosPagosTotal = this.pagos.filter((_, i) => i !== idx).reduce((s, p) => s + (parseFloat(p.monto) || 0), 0);
                        const restanteStr = Math.max(0, this.montoTotal - otrosPagosTotal).toFixed(2);
                        const restante = parseFloat(restanteStr);
                        
                        // Auto-completar el monto máximo permitido (el menor entre lo adeudado y su crédito disponible)
                        const maxPermitido = Math.min(cliente.credito_disponible, restante);
                        pago.monto = maxPermitido.toFixed(2);
                    }
                },

                calcularCambio() {},

                async confirmarPago() {
                    // Validate referencias and credit
                    let totalCreditoSolicitado = 0;
                    for (const pago of this.pagos) {
                        if (pago.metodo === 'tarjeta' && REF_TARJETA && !pago.referencia.trim()) {
                            window.Notify.warning('Referencia requerida - La configuración exige una referencia para pagos con tarjeta.');
                            return;
                        }
                        if ((pago.metodo === 'transferencia' || pago.metodo === 'yappy') && REF_TRANSFERENCIA && !pago.referencia.trim()) {
                            window.Notify.warning('Referencia requerida - La configuración exige una referencia para transferencias/Yappy.');
                            return;
                        }

                        if (pago.metodo === 'credito') {
                            totalCreditoSolicitado += parseFloat(pago.monto);
                        }
                    }

                    if (totalCreditoSolicitado > 0) {
                        if (!this.$store.posData.clienteId) {
                            window.Notify.failure('Cliente requerido para realizar una venta a crédito.');
                            return;
                        }

                        const cliente = clientesData[this.$store.posData.clienteId];
                        if (cliente && totalCreditoSolicitado > cliente.credito_disponible) {
                            window.Notify.failure(`Crédito insuficiente - Solo tiene $${cliente.credito_disponible.toFixed(2)} disponible.`);
                            return;
                        }
                    }

                    const isConfirmed = POS_CONFIRMACION ? await new Promise(resolve => {
                        window.Confirm.show('¿Confirmar venta?', `Total: $${this.montoTotal.toFixed(2)}`, 'Sí, confirmar', 'Revisar', () => resolve(true), () => resolve(false), { okButtonBackground: '#059669' });
                    }) : true;
                    if (!isConfirmed) return;

                    this.cargando = true;
                    if(typeof window.Loading !== 'undefined') window.Loading.pulse('Procesando pago y facturación...');
                    try {
                        const resp = await fetch(VENTA_STORE, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN
                            },
                            body: JSON.stringify({
                                sucursal_id: this.$store.posData.sucursalId,
                                cliente_id: this.$store.posData.clienteId || null,
                                fecha_vencimiento: totalCreditoSolicitado > 0 ? this.fechaVencimiento : null,
                                forma_pago_dian: this.generarFacturaDian ? this.formaPagoDian : null,
                                metodo_pago_dian_id: this.generarFacturaDian ? this.metodoPagoDian : null,
                                items: this.$store.posData.carrito.map(i => ({
                                    producto_id: i.id,
                                    cantidad: i.cantidad,
                                    precio_unitario: i.precio_unitario,
                                    impuesto: i.impuesto,
                                })),
                                pagos: this.pagos.map(p => ({
                                    metodo: p.metodo,
                                    monto: parseFloat(p.monto),
                                    referencia: p.referencia || null,
                                    tipo_tarjeta: p.tipo_tarjeta || null,
                                    banco: p.banco || null,
                                })),
                            }),
                        });
                        const data = await resp.json();
                        
                        if(typeof window.Loading !== 'undefined') window.Loading.remove();
                        this.cargando = false;

                        if (data.success) {
                            Alpine.store('posModal').open = false;

                            if (data.alertas_stock && data.alertas_stock.length > 0) {
                                let htmlStock = '<ul><li>' + data.alertas_stock.join('</li><li>') + '</li></ul>';
                                if(typeof window.Notify !== 'undefined') {
                                    window.Notify.warning(`¡Atención! Stock crítico alcanzado:<br><br>${htmlStock}`, { timeout: 7000, plainText: false, width: '360px' });
                                }
                            }

                            // Flujo Factura DIAN Automática
                            if (this.generarFacturaDian) {
                                window.Loading.pulse('Generando Factura DIAN con Factus...');

                                try {
                                    const dianResp = await fetch("{{ route('facturacion.store') }}", {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': CSRF_TOKEN
                                        },
                                        body: JSON.stringify({ venta_id: data.venta_id })
                                    });
                                    const dianData = await dianResp.json();
                                    
                                    if(!dianData.success) {
                                        window.Loading.remove();
                                        window.Notify.warning('Venta completada localmente, pero falló la generación DIAN. Reintenta desde el Historial.', { timeout: 8000 });
                                    } else {
                                        window.Loading.remove();
                                        window.Notify.success(`¡Factura DIAN procesada! ${data.numero} enviada a Factus.`);
                                        // Redirigir directamente a la vista detallada de la nueva factura DIAN
                                        if (dianData.factura_id) {
                                            window.location.href = `/facturacion/${dianData.factura_id}`;
                                            return; // Stop further execution so the default POS prompt doesn't show
                                        }
                                    }
                                } catch(e) {
                                    console.error(e);
                                    window.Loading.remove();
                                    window.Notify.failure('Error de Red con DIAN. La venta se registró localmente.', { timeout: 8000 });
                                }
                            }

                            window.Notify.success(`¡Venta Registrada! ${data.numero}`);
                            window.Confirm.show(
                                '¿Imprimir Ticket?',
                                `Total: $${this.montoTotal.toFixed(2)}. ¿Deseas imprimir el Ticket de 80mm?`,
                                'Imprimir Ticket',
                                'Nueva Venta',
                                () => {
                                    window.dispatchEvent(new CustomEvent('abrir-ticket', { detail: `/caja/ventas/${data.venta_id}/ticket` }));
                                    window.addEventListener('ticket-cerrado', function _reload() {
                                        window.removeEventListener('ticket-cerrado', _reload);
                                        location.reload();
                                    });
                                },
                                () => { location.reload(); }
                            );
                        } else {
                            window.Notify.failure(`Error - ${data.message}`);
                        }
                    } catch (e) {
                        this.cargando = false;
                        if(typeof window.Loading !== 'undefined') window.Loading.remove();
                        window.Notify.failure(`Error de red - ${e.message}`);
                    }
                }
            };
        }

        // ─── Espera Modal ───────────────────────────────────────────────────
        function esperaModal() {
            return {
                lista: [],
                async init() {
                    await this.cargar();
                },
                async cargar() {
                    try {
                        const r = await fetch(ESPERA_IDX, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        // Espera page returns HTML; use a dedicated JSON endpoint if needed
                        this.lista = [];
                    } catch (e) {}
                },
                async retomar(id) {
                    const r = await fetch(`/caja/espera/${id}/retomar`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        }
                    });
                    const data = await r.json();
                    if (data.success) {
                        Alpine.store('posEspera').open = false;
                        window.Notify.success('Venta retomada - El carrito ha sido cargado.');
                    }
                },
                async eliminar(id) {
                    window.Confirm.show('¿Eliminar espera?', '¿Deseas descartar esta venta en espera?', 'Eliminar', 'Cancelar', async () => {
                        await fetch(`/caja/espera/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': CSRF_TOKEN
                            }
                        });
                        await this.cargar();
                    }, () => {}, { okButtonBackground: '#dc2626' });
                }
            };
        }
    </script>

{{-- ══════════════════════════════════════════════════════
     MODAL SCANNER DE CÁMARA — Módulo POS
═══════════════════════════════════════════════════════ --}}
<div id="modalScannerPOS"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm hidden"
     onclick="if(event.target===this) cerrarScannerPOS()">
    <div class="relative bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-800/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white">Scanner POS</h3>
                    <p class="text-[10px] text-gray-400">El producto se agrega automáticamente</p>
                </div>
            </div>
            <button onclick="cerrarScannerPOS()" class="rounded-full p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-neutral-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <!-- Camera viewport -->
        <div id="scanner-pos-viewport" class="bg-black" style="height: 280px; position: relative;">
            <div id="scan-line-pos" style="display:none; position:absolute; left:0; right:0; height:2px; background: linear-gradient(90deg, transparent, #22c55e, transparent); z-index:10; animation: scanLinePOS 2s linear infinite;"></div>
        </div>
        <!-- Status -->
        <div class="px-5 py-3 bg-gray-50 dark:bg-neutral-800/40 text-center min-h-[44px] flex items-center justify-center">
            <p id="scanner-pos-status" class="text-xs text-gray-500 dark:text-gray-400">Apunta la cámara al código de barras del producto...</p>
        </div>
        <!-- Último producto escaneado -->
        <div id="scanner-pos-result" class="hidden px-5 py-3 bg-emerald-50 dark:bg-emerald-900/20 border-t border-emerald-100 dark:border-emerald-900/30">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400" id="scanner-pos-result-name">—</p>
                    <p class="text-[10px] text-emerald-600 dark:text-emerald-500">Agregado al carrito ✓</p>
                </div>
            </div>
        </div>
        <!-- Footer: cerrar o continuar escaneando -->
        <div class="px-5 py-4 border-t border-gray-100 dark:border-neutral-800 flex gap-3">
            <button onclick="cerrarScannerPOS()"
                    class="flex-1 rounded-xl border border-gray-300 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-800 transition">
                Cerrar
            </button>
            <button onclick="document.getElementById('scanner-pos-result').classList.add('hidden')"
                    class="flex-1 rounded-xl bg-brand-600 py-2.5 text-sm font-bold text-white hover:bg-brand-700 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Escanear otro
            </button>
        </div>
    </div>
</div>

<style>
@keyframes scanLinePOS {
    0%   { top: 10px; opacity: 1; }
    50%  { opacity: 0.5; }
    100% { top: 260px; opacity: 1; }
}
</style>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let _scannerPOSInstance = null;
    let _scannerPOSCooldown = false;

    function abrirScannerPOS() {
        document.getElementById('modalScannerPOS').classList.remove('hidden');
        document.getElementById('scan-line-pos').style.display = 'block';
        document.getElementById('scanner-pos-result').classList.add('hidden');
        document.getElementById('scanner-pos-status').textContent = 'Iniciando cámara...';

        if (_scannerPOSInstance) {
            _scannerPOSInstance.clear().catch(() => {});
        }

        _scannerPOSInstance = new Html5Qrcode('scanner-pos-viewport');

        const config = {
            fps: 12,
            qrbox: { width: 280, height: 180 },
            aspectRatio: 1.7,
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        };

        _scannerPOSInstance.start(
            { facingMode: 'environment' },
            config,
            (decodedText) => {
                if (_scannerPOSCooldown) return;
                _scannerPOSCooldown = true;

                // Buscar producto por código de barras
                const prod = productosData.find(p => p.codigo_barras === decodedText);

                if (prod) {
                    // Agregar al carrito via Alpine
                    const posEl = document.querySelector('[x-data]');
                    if (posEl && posEl._x_dataStack) {
                        posEl._x_dataStack[0].agregarProducto(prod.id);
                    }
                    document.getElementById('scanner-pos-result-name').textContent = prod.nombre + ' — $' + parseFloat(prod.precio_venta).toFixed(2);
                    document.getElementById('scanner-pos-result').classList.remove('hidden');
                    document.getElementById('scanner-pos-status').textContent = '✅ Código: ' + decodedText;
                } else {
                    document.getElementById('scanner-pos-status').textContent = '⚠ Código no encontrado: ' + decodedText;
                    document.getElementById('scanner-pos-result').classList.add('hidden');
                }

                // Cooldown de 1.5s para no leer el mismo código múltiples veces
                setTimeout(() => { _scannerPOSCooldown = false; }, 1500);
            },
            () => {} // Frame errors ignorados
        ).then(() => {
            document.getElementById('scanner-pos-status').textContent = 'Apunta al código de barras del producto...';
        }).catch((err) => {
            document.getElementById('scanner-pos-status').textContent = '⚠ No se pudo acceder a la cámara.';
            console.error('POS Scanner error:', err);
        });
    }

    function cerrarScannerPOS() {
        document.getElementById('modalScannerPOS').classList.add('hidden');
        document.getElementById('scan-line-pos').style.display = 'none';
        if (_scannerPOSInstance) {
            _scannerPOSInstance.stop().catch(() => {});
            _scannerPOSInstance.clear().catch(() => {});
            _scannerPOSInstance = null;
        }
        // Devolver el foco al input de búsqueda para scanner físico
        const el = document.getElementById('busqueda-input');
        if (el) el.focus();
    }
</script>

@endsection
