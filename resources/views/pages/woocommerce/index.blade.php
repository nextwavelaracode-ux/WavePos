@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Integración WooCommerce" />

    <div id="woocommerce-root" x-data="{
        activeTab: 'productos',
        showSettingsModal: false,
        showProductDrawer: false,
        showOrderDrawer: false,
        selectedProduct: null,
        selectedOrder: null,

        openProduct(prod) {
            this.selectedProduct = prod;
            this.showProductDrawer = true;
        },
        openOrder(ord) {
            this.selectedOrder = ord;
            this.showOrderDrawer = true;
        }
    }">

        {{-- ===== RESUMEN CARDS (Mismo componente de Control de Stock) ===== --}}
        <div class="mb-6 grid md:grid-cols-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-2xs rounded-xl overflow-hidden">
            {{-- 1. Total Productos --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Total Productos</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-brand-600 dark:text-brand-500">{{ $summary['local_total'] }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Registrados en WavePos</p>
                    </div>
                </div>
            </div>

            {{-- 2. Sincronizados en WooCommerce --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Sincronizados</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-emerald-600 dark:text-emerald-500">{{ $summary['local_linked'] }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Enlazados a WooCommerce</p>
                    </div>
                </div>
            </div>

            {{-- 3. Sin Vincular / Pendientes --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Sin Vincular</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-orange-600 dark:text-orange-500">{{ max(0, $summary['local_total'] - $summary['local_linked']) }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Pendientes por sincronizar</p>
                    </div>
                </div>
            </div>

            {{-- 4. Pedidos Online --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Pedidos Tienda</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">{{ $summary['orders_count'] }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">${{ number_format($summary['total_sales'], 2) }} facturado</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== HEADER / TOOLBAR ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Gestión de WooCommerce</h2>
                <div class="mt-1 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>Estado:</span>
                    @if (!empty($summary['connection']['success']))
                        <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-emerald-400">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Conectado (v{{ $summary['connection']['version'] ?? 'WC' }})
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 font-semibold text-red-600 dark:text-red-400">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span> Desconectado
                        </span>
                    @endif
                    <span>•</span>
                    <a href="{{ $settings['store_url'] }}" target="_blank" class="hover:underline text-brand-600 dark:text-brand-400">{{ $settings['store_url'] }}</a>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                {{-- Subir todo --}}
                <form action="{{ route('woocommerce.sync.all') }}" method="POST" onsubmit="return confirm('¿Deseas enviar y actualizar todos los productos activos de WavePos hacia WooCommerce?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-purple-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        Subir a WC
                    </button>
                </form>

                {{-- Traer todo --}}
                <form action="{{ route('woocommerce.pull.all') }}" method="POST" onsubmit="return confirm('¿Deseas descargar e importar todos los productos desde WooCommerce hacia WavePos?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Importar de WC
                    </button>
                </form>

                {{-- Cobro Contra Reembolso --}}
                <a href="{{ route('woocommerce.cobro') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Cobro Contra Reembolso
                </a>

                {{-- Refrescar --}}
                <a href="{{ route('woocommerce.index', ['refresh' => 1]) }}" title="Actualizar datos desde WooCommerce" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refrescar
                </a>

                {{-- Ajustes --}}
                <button @click="showSettingsModal = true" type="button" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Ajustes
                </button>
            </div>
        </div>

        {{-- ===== TABS TOGGLE ===== --}}
        <div class="mb-4 flex items-center gap-2 border-b border-gray-200 dark:border-neutral-700">
            <button type="button" @click="activeTab = 'productos'"
                :class="activeTab === 'productos' ? 'border-brand-600 text-brand-600 dark:border-brand-500 dark:text-brand-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                class="inline-flex items-center gap-2 py-3 px-4 border-b-2 font-medium text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Productos Locales
                <span class="rounded-full bg-gray-100 dark:bg-neutral-800 px-2.5 py-0.5 text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $productos->total() }}</span>
            </button>
            <button type="button" @click="activeTab = 'pedidos'"
                :class="activeTab === 'pedidos' ? 'border-brand-600 text-brand-600 dark:border-brand-500 dark:text-brand-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                class="inline-flex items-center gap-2 py-3 px-4 border-b-2 font-medium text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Pedidos WooCommerce
                <span class="rounded-full bg-orange-100 dark:bg-orange-900/40 px-2.5 py-0.5 text-xs font-semibold text-orange-700 dark:text-orange-400">{{ count($summary['orders']) }}</span>
            </button>
        </div>

        {{-- ============================================================== --}}
        {{-- TAB 1: PRODUCTOS --}}
        {{-- ============================================================== --}}
        <div x-show="activeTab === 'productos'">
            {{-- FILTROS (Mismo diseño de Control de Stock) --}}
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('woocommerce.index') }}" class="w-full max-w-lg flex items-center gap-2">
                    <div class="relative w-full">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 dark:text-white text-sm border border-slate-200 dark:border-neutral-700 rounded-md pl-10 pr-24 py-2.5 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 dark:hover:border-gray-600 shadow-sm"
                            placeholder="Buscar producto, SKU o código..." />
                        
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <input type="hidden" name="filter" value="{{ request('filter', 'all') }}" />
                        @if(request('categoria_id'))
                            <input type="hidden" name="categoria_id" value="{{ request('categoria_id') }}" />
                        @endif

                        <button
                            class="absolute top-1 right-1 flex items-center rounded bg-slate-800 dark:bg-brand-500 py-1.5 px-3 border border-transparent text-center text-xs font-semibold text-white transition-all shadow-sm hover:shadow"
                            type="submit">
                            Buscar
                        </button>
                    </div>

                    @if(request()->hasAny(['search', 'filter', 'categoria_id']))
                        <a href="{{ route('woocommerce.index') }}" class="py-2 px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 rounded border border-gray-200 dark:border-neutral-700 hover:bg-gray-100 dark:hover:bg-neutral-800 transition">
                            Limpiar
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2">
                    {{-- Filtro de Categoría --}}
                    <form method="GET" action="{{ route('woocommerce.index') }}" id="catForm">
                        <input type="hidden" name="search" value="{{ request('search') }}" />
                        <input type="hidden" name="filter" value="{{ request('filter', 'all') }}" />
                        <select name="categoria_id" onchange="document.getElementById('catForm').submit()" class="rounded-md border border-slate-200 bg-transparent py-2 px-3 text-sm text-slate-700 shadow-sm focus:border-slate-400 focus:ring-0 dark:border-neutral-700 dark:text-gray-300">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </form>

                    {{-- Filtro Estado Sincronización --}}
                    <form method="GET" action="{{ route('woocommerce.index') }}" id="filterForm">
                        <input type="hidden" name="search" value="{{ request('search') }}" />
                        @if(request('categoria_id'))
                            <input type="hidden" name="categoria_id" value="{{ request('categoria_id') }}" />
                        @endif
                        <select name="filter" onchange="document.getElementById('filterForm').submit()" class="rounded-md border border-slate-200 bg-transparent py-2 px-3 text-sm text-slate-700 shadow-sm focus:border-slate-400 focus:ring-0 dark:border-neutral-700 dark:text-gray-300">
                            <option value="all" {{ request('filter', 'all') === 'all' ? 'selected' : '' }}>Todos los estados</option>
                            <option value="linked" {{ request('filter') === 'linked' ? 'selected' : '' }}>Sincronizados en WC</option>
                            <option value="unlinked" {{ request('filter') === 'unlinked' ? 'selected' : '' }}>No vinculados</option>
                        </select>
                    </form>
                </div>
            </div>

            {{-- TABLA DE PRODUCTOS (Mismo componente de Control de Stock) --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50 dark:bg-neutral-800/20">
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">ID</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Producto</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categoría</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">SKU / Código</th>
                                <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Precio</th>
                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stock</th>
                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado WC</th>
                                <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/80">
                            @forelse ($productos as $producto)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs">#{{ $producto->id }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            {{-- Miniatura de imagen optimizada idéntica a Control de Stock --}}
                                            <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-gray-100 dark:bg-neutral-800 overflow-hidden flex items-center justify-center border border-gray-200 dark:border-neutral-700">
                                                @if($producto->imagen_url)
                                                    <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="h-full w-full object-cover" onerror="this.onerror=null; this.src=''; this.parentElement.innerHTML='<svg class=\'h-4 w-4 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'/></svg>';">
                                                @else
                                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <button type="button" @click="openProduct({{ json_encode([
                                                    'id' => $producto->id,
                                                    'nombre' => $producto->nombre,
                                                    'descripcion' => $producto->descripcion,
                                                    'categoria' => $producto->categoria->nombre ?? 'General',
                                                    'sku' => $producto->sku,
                                                    'codigo_barras' => $producto->codigo_barras,
                                                    'precio_venta' => number_format($producto->precio_venta, 2),
                                                    'precio_compra' => number_format($producto->precio_compra, 2),
                                                    'stock' => $producto->stock,
                                                    'stock_minimo' => $producto->stock_minimo,
                                                    'woocommerce_id' => $producto->woocommerce_id,
                                                    'ultima_sincronizacion_wc' => $producto->ultima_sincronizacion_wc ? $producto->ultima_sincronizacion_wc->format('d/m/Y H:i') : null,
                                                    'imagen_url' => $producto->imagen_url,
                                                ]) }})" class="font-medium text-gray-800 dark:text-white/90 hover:text-brand-600 dark:hover:text-brand-400 text-left transition">
                                                    {{ $producto->nombre }}
                                                </button>
                                                <p class="text-xs text-gray-500 font-mono">{{ $producto->sku ?: ($producto->codigo_barras ?: 'Sin código') }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                            {{ $producto->categoria->nombre ?? 'General' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-gray-600 dark:text-gray-400 text-xs font-mono">
                                        {{ $producto->sku ?: ($producto->codigo_barras ?: '—') }}
                                    </td>

                                    <td class="px-5 py-4 text-right font-semibold text-gray-800 dark:text-white/90">
                                        ${{ number_format($producto->precio_venta, 2) }}
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        @if($producto->stock <= 0)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-500/10 dark:text-red-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Agotado
                                            </span>
                                        @elseif($producto->stock <= $producto->stock_minimo)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-700 dark:bg-orange-500/10 dark:text-orange-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-orange-500 animate-pulse"></span> {{ $producto->stock }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                {{ $producto->stock }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        @if ($producto->woocommerce_id)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                WC #{{ $producto->woocommerce_id }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-neutral-800 dark:text-gray-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> No vinculado
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Subir / Actualizar --}}
                                            <form action="{{ route('woocommerce.sync.product', $producto->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" title="{{ $producto->woocommerce_id ? 'Actualizar en WooCommerce' : 'Subir a WooCommerce' }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-neutral-700 transition">
                                                    <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                    {{ $producto->woocommerce_id ? 'Actualizar' : 'Subir' }}
                                                </button>
                                            </form>

                                            {{-- Ver Detalle --}}
                                            <button type="button" @click="openProduct({{ json_encode([
                                                'id' => $producto->id,
                                                'nombre' => $producto->nombre,
                                                'descripcion' => $producto->descripcion,
                                                'categoria' => $producto->categoria->nombre ?? 'General',
                                                'sku' => $producto->sku,
                                                'codigo_barras' => $producto->codigo_barras,
                                                'precio_venta' => number_format($producto->precio_venta, 2),
                                                'precio_compra' => number_format($producto->precio_compra, 2),
                                                'stock' => $producto->stock,
                                                'stock_minimo' => $producto->stock_minimo,
                                                'woocommerce_id' => $producto->woocommerce_id,
                                                'ultima_sincronizacion_wc' => $producto->ultima_sincronizacion_wc ? $producto->ultima_sincronizacion_wc->format('d/m/Y H:i') : null,
                                                'imagen_url' => $producto->imagen_url,
                                            ]) }})" class="p-1.5 text-gray-500 hover:text-brand-600 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-800 transition" title="Ver detalles">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        <p class="mt-2 text-base font-medium text-gray-700 dark:text-gray-300">No se encontraron productos</p>
                                        <p class="text-xs text-gray-500">Prueba con otro término de búsqueda o limpia los filtros.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-5 py-4 border-t border-gray-100 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-800/10">
                    {{ $productos->links() }}
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- TAB 2: PEDIDOS WOOCOMMERCE --}}
        {{-- ============================================================== --}}
        <div x-show="activeTab === 'pedidos'" style="display: none;">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50 dark:bg-neutral-800/20">
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Orden</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cliente</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Método de Pago</th>
                                <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</th>
                                <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado</th>
                                <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/80">
                            @forelse ($summary['orders'] as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                    <td class="px-5 py-4 font-mono font-bold text-gray-900 dark:text-white">
                                        #{{ $order['id'] }}
                                    </td>
                                    <td class="px-5 py-4 text-gray-500 dark:text-gray-400 text-xs">
                                        {{ !empty($order['date_created']) ? \Carbon\Carbon::parse($order['date_created'])->format('d/m/Y H:i') : '—' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-gray-800 dark:text-white/90">
                                            {{ trim(($order['billing']['first_name'] ?? '') . ' ' . ($order['billing']['last_name'] ?? '')) ?: 'Cliente Web' }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $order['billing']['email'] ?? 'Sin correo' }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-gray-600 dark:text-gray-300 text-xs">
                                        {{ $order['payment_method_title'] ?? ($order['payment_method'] ?? 'N/A') }}
                                    </td>
                                    <td class="px-5 py-4 text-right font-bold text-gray-900 dark:text-white">
                                        ${{ number_format((float)($order['total'] ?? 0), 2) }}
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @php
                                            $st = $order['status'] ?? 'unknown';
                                            $badgeClasses = match($st) {
                                                'completed'  => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                'processing' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                                'on-hold'    => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                                'cancelled'  => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                                default      => 'bg-gray-100 text-gray-700 dark:bg-neutral-800 dark:text-gray-300',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClasses }}">
                                            {{ ucfirst($st) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if(($order['status'] ?? '') === 'processing' || ($order['status'] ?? '') === 'on-hold')
                                                <a href="{{ route('woocommerce.cobro', ['search' => $order['id']]) }}" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 shadow-sm transition">
                                                    Cobrar
                                                </a>
                                            @endif
                                            <button type="button" @click="openOrder({{ json_encode($order) }})" class="p-1.5 text-gray-500 hover:text-brand-600 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-800 transition" title="Ver detalle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        <p class="mt-2 text-base font-medium text-gray-700 dark:text-gray-300">No hay pedidos recientes</p>
                                        <p class="text-xs text-gray-500">Los nuevos pedidos de WooCommerce aparecerán aquí automáticamente.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===== OFFCANVAS DRAWER: DETALLE DEL PRODUCTO (Mismo componente de productos.blade.php) ===== --}}
        <div x-show="showProductDrawer" class="relative z-50" style="display: none;">
            {{-- Backdrop --}}
            <div x-show="showProductDrawer"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showProductDrawer = false"
                 class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"></div>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <div x-show="showProductDrawer"
                             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             @click.outside="showProductDrawer = false"
                             @keydown.escape.window="showProductDrawer = false"
                             class="pointer-events-auto w-screen max-w-md">
                            <div class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-neutral-900 shadow-xl border-l border-gray-200 dark:border-neutral-800">
                                
                                {{-- Header Drawer --}}
                                <div class="px-5 py-4 bg-gray-50 dark:bg-neutral-800/50 border-b border-gray-200 dark:border-neutral-800 sticky top-0 z-10 flex items-center justify-between">
                                    <h5 class="inline-flex items-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        Detalle de Producto en WooCommerce
                                    </h5>
                                    <button type="button" @click="showProductDrawer = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-neutral-800 dark:hover:text-white">
                                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>

                                {{-- Body Drawer --}}
                                <div class="relative flex-1 px-5 py-6 space-y-6" x-show="selectedProduct">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-20 w-20 overflow-hidden rounded-xl bg-gray-100 dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 flex-shrink-0 items-center justify-center">
                                            <template x-if="selectedProduct?.imagen_url">
                                                <img :src="selectedProduct.imagen_url" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!selectedProduct?.imagen_url">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            </template>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight" x-text="selectedProduct?.nombre"></h3>
                                            <span class="inline-flex mt-1 items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400" x-text="selectedProduct?.categoria"></span>
                                        </div>
                                    </div>

                                    <dl class="text-sm divide-y divide-gray-100 dark:divide-neutral-800">
                                        <div class="py-3 flex justify-between">
                                            <dt class="text-gray-500 dark:text-gray-400">ID WavePos</dt>
                                            <dd class="font-mono font-semibold text-gray-800 dark:text-white" x-text="'#' + selectedProduct?.id"></dd>
                                        </div>
                                        <div class="py-3 flex justify-between">
                                            <dt class="text-gray-500 dark:text-gray-400">SKU / Código</dt>
                                            <dd class="font-mono font-semibold text-gray-800 dark:text-white" x-text="(selectedProduct?.sku || '—') + ' / ' + (selectedProduct?.codigo_barras || '—')"></dd>
                                        </div>
                                        <div class="py-3 flex justify-between">
                                            <dt class="text-gray-500 dark:text-gray-400">Precio de Venta</dt>
                                            <dd class="text-base font-bold text-brand-600 dark:text-brand-500" x-text="'$' + selectedProduct?.precio_venta"></dd>
                                        </div>
                                        <div class="py-3 flex justify-between">
                                            <dt class="text-gray-500 dark:text-gray-400">Stock Actual</dt>
                                            <dd class="font-semibold text-gray-800 dark:text-white" x-text="selectedProduct?.stock + ' unidades'"></dd>
                                        </div>
                                        <div class="py-3 flex justify-between items-center">
                                            <dt class="text-gray-500 dark:text-gray-400">Estado en WooCommerce</dt>
                                            <dd>
                                                <template x-if="selectedProduct?.woocommerce_id">
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                        WC ID: <span x-text="selectedProduct?.woocommerce_id"></span>
                                                    </span>
                                                </template>
                                                <template x-if="!selectedProduct?.woocommerce_id">
                                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-neutral-800 dark:text-gray-400">
                                                        Sin vincular
                                                    </span>
                                                </template>
                                            </dd>
                                        </div>
                                        <div class="py-3 flex justify-between">
                                            <dt class="text-gray-500 dark:text-gray-400">Última Sincronización</dt>
                                            <dd class="text-gray-600 dark:text-gray-300" x-text="selectedProduct?.ultima_sincronizacion_wc || 'Nunca'"></dd>
                                        </div>
                                        <div class="py-3 flex flex-col gap-1">
                                            <dt class="text-gray-500 dark:text-gray-400">Descripción</dt>
                                            <dd class="text-xs text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-neutral-800 p-3 rounded-lg" x-text="selectedProduct?.descripcion || 'Sin descripción'"></dd>
                                        </div>
                                    </dl>

                                    {{-- Botón de acción directo --}}
                                    <div class="pt-4 border-t border-gray-100 dark:border-neutral-800" x-show="selectedProduct">
                                        <form :action="'{{ url('woocommerce/sync/product') }}/' + selectedProduct?.id" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-purple-700 transition shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                <span x-text="selectedProduct?.woocommerce_id ? 'Actualizar Producto en WooCommerce' : 'Subir Producto a WooCommerce'"></span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== OFFCANVAS DRAWER: DETALLE DEL PEDIDO ===== --}}
        <div x-show="showOrderDrawer" class="relative z-50" style="display: none;">
            {{-- Backdrop --}}
            <div x-show="showOrderDrawer"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showOrderDrawer = false"
                 class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"></div>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <div x-show="showOrderDrawer"
                             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             @click.outside="showOrderDrawer = false"
                             @keydown.escape.window="showOrderDrawer = false"
                             class="pointer-events-auto w-screen max-w-md">
                            <div class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-neutral-900 shadow-xl border-l border-gray-200 dark:border-neutral-800">
                                
                                {{-- Header Drawer --}}
                                <div class="px-5 py-4 bg-gray-50 dark:bg-neutral-800/50 border-b border-gray-200 dark:border-neutral-800 sticky top-0 z-10 flex items-center justify-between">
                                    <h5 class="inline-flex items-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        Pedido #<span x-text="selectedOrder?.id"></span>
                                    </h5>
                                    <button type="button" @click="showOrderDrawer = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-neutral-800 dark:hover:text-white">
                                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>

                                {{-- Body Drawer --}}
                                <div class="relative flex-1 px-5 py-6 space-y-6" x-show="selectedOrder">
                                    <div class="bg-gray-50 dark:bg-neutral-800/50 p-4 rounded-xl border border-gray-100 dark:border-neutral-800">
                                        <p class="text-xs uppercase font-medium text-gray-500 dark:text-gray-400">Cliente</p>
                                        <h4 class="mt-1 text-base font-bold text-gray-900 dark:text-white" x-text="((selectedOrder?.billing?.first_name || '') + ' ' + (selectedOrder?.billing?.last_name || '')).trim() || 'Cliente'"></h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1" x-text="selectedOrder?.billing?.email || ''"></p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300" x-text="selectedOrder?.billing?.phone || ''"></p>
                                    </div>

                                    <div class="space-y-3">
                                        <h5 class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400 tracking-wider">Artículos del Pedido</h5>
                                        <div class="divide-y divide-gray-100 dark:divide-neutral-800 border border-gray-100 dark:border-neutral-800 rounded-xl overflow-hidden">
                                            <template x-for="item in (selectedOrder?.line_items || [])" :key="item.id">
                                                <div class="p-3 flex items-center justify-between bg-white dark:bg-neutral-900">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="item.name"></p>
                                                        <p class="text-xs text-gray-500" x-text="'Cant: ' + item.quantity + ' × $' + parseFloat(item.price || 0).toFixed(2)"></p>
                                                    </div>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="'$' + parseFloat(item.total || 0).toFixed(2)"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="bg-brand-50 dark:bg-brand-950/20 p-4 rounded-xl border border-brand-100 dark:border-brand-900/40 flex justify-between items-center">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white">Total del Pedido</span>
                                        <span class="text-xl font-bold text-brand-600 dark:text-brand-400" x-text="'$' + parseFloat(selectedOrder?.total || 0).toFixed(2)"></span>
                                    </div>

                                    {{-- Ir a cobrar si aplica --}}
                                    <template x-if="selectedOrder?.status === 'processing' || selectedOrder?.status === 'on-hold'">
                                        <a :href="'{{ url('woocommerce/cobro') }}?search=' + selectedOrder?.id" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            Cobrar y Entregar en Tienda
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== MODAL DE AJUSTES ===== --}}
        <div x-show="showSettingsModal" class="relative z-50" style="display: none;">
            <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm" @click="showSettingsModal = false"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div @click.outside="showSettingsModal = false" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-neutral-800">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Ajustes de Conexión WooCommerce</h3>
                            <button @click="showSettingsModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form action="{{ route('woocommerce.settings.update') }}" method="POST" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">URL de la Tienda</label>
                                <input type="url" name="store_url" value="{{ $settings['store_url'] }}" required class="w-full rounded-lg border border-gray-300 dark:border-neutral-700 bg-transparent px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" placeholder="http://tienda.test" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Consumer Key</label>
                                <input type="text" name="consumer_key" value="{{ $settings['consumer_key'] }}" required class="w-full rounded-lg border border-gray-300 dark:border-neutral-700 bg-transparent px-3 py-2 text-sm text-gray-800 dark:text-white font-mono focus:border-brand-500 focus:outline-none" placeholder="ck_..." />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Consumer Secret</label>
                                <input type="password" name="consumer_secret" value="{{ $settings['consumer_secret'] }}" required class="w-full rounded-lg border border-gray-300 dark:border-neutral-700 bg-transparent px-3 py-2 text-sm text-gray-800 dark:text-white font-mono focus:border-brand-500 focus:outline-none" placeholder="cs_..." />
                            </div>
                            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
                                <button type="button" @click="showSettingsModal = false" class="rounded-xl border border-gray-300 dark:border-neutral-700 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-800">Cancelar</button>
                                <button type="submit" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
