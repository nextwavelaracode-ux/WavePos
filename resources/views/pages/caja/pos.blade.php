@extends('layouts.pos-layout')

@section('content')

{{-- Estilos personalizados y animaciones para el POS --}}
<style>
    /* Efecto de elevación suave para tarjetas */
    .pos-card-hover {
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s;
    }
    .pos-card-hover:hover {
        transform: translateY(-3px);
    }
    .pos-card-hover:active {
        transform: scale(0.97);
    }

    /* Keypad táctil */
    .numpad-btn {
        transition: background-color 0.15s, transform 0.1s;
    }
    .numpad-btn:active {
        transform: scale(0.94);
    }

    /* Scrollbar estilizada y minimalista */
    .pos-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .pos-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .pos-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 9999px;
    }
    .pos-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.5);
    }
</style>

{{-- Contenedor principal POS con Alpine.js --}}
<div class="flex flex-col h-full w-full overflow-hidden bg-slate-50/70 dark:bg-[#0b101b] select-none" 
     x-data="posApp()" 
     x-init="init()"
     @keydown.window="handleGlobalKeydown($event)">

    {{-- ── 1. TOP HEADER BAR (Inspirado en Starline & CHILI POS) ── --}}
    <header class="h-16 px-4 lg:px-6 bg-white/90 dark:bg-[#111827]/90 border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between gap-3 shrink-0 z-20 backdrop-blur-md">
        
        {{-- Saludo y Contexto de Tienda --}}
        <div class="flex items-center gap-3 min-w-0">
            <div class="hidden sm:flex flex-col">
                <div class="flex items-center gap-2">
                    <h1 class="text-sm lg:text-base font-bold text-slate-800 dark:text-white tracking-tight flex items-center gap-1.5">
                        <span>Hola, {{ auth()->user()->name ?? 'Cajero' }}</span>
                        <span class="inline-block animate-wave text-base">👋</span>
                    </h1>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <span class="font-medium truncate max-w-[140px] lg:max-w-[200px]" x-text="clockTime">--:--:--</span>
                    <span>•</span>
                    @if ($cajaAbierta)
                        <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-emerald-400">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            {{ $cajaAbierta->sucursal->nombre ?? 'Sucursal Principal' }}
                        </span>
                    @else
                        <a href="{{ route('caja.index') }}" class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 font-bold hover:underline">
                            <span>⚠️</span> Caja Cerrada (Abrir)
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Barra de Búsqueda Omnicanal & Escáner --}}
        <div class="flex-1 max-w-xl mx-2">
            <div class="relative flex items-center">
                <div class="absolute left-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input x-model="busqueda" 
                       id="busqueda-input" 
                       x-ref="buscadorPos" 
                       type="text"
                       placeholder="Buscar producto por nombre o código de barras... (Presiona '/' para buscar)"
                       autocomplete="off"
                       @keyup.enter="buscarPorCodigo()"
                       class="w-full h-10 pl-10 pr-24 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-100/70 dark:bg-slate-800/60 text-xs sm:text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-all shadow-inner" />
                
                {{-- Botones integrados en el input: limpiar y cámara --}}
                <div class="absolute right-1.5 flex items-center gap-1">
                    <button x-show="busqueda" 
                            @click="busqueda = ''; $refs.buscadorPos.focus()" 
                            type="button" 
                            class="p-1 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <button type="button"
                            onclick="abrirScannerPOS()"
                            title="Escanear con Cámara"
                            class="h-7 px-2 rounded-lg bg-brand-500/10 hover:bg-brand-500/20 text-brand-600 dark:text-brand-400 font-semibold text-xs flex items-center gap-1 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="hidden md:inline text-[11px]">Escanear</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Acciones rápidas del encabezado --}}
        <div class="flex items-center gap-2">
            
            {{-- Switcher Vista Cuadrícula / Lista --}}
            <div class="hidden sm:flex items-center p-0.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700">
                <button @click="viewMode = 'cards'" 
                        :class="viewMode === 'cards' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                        class="p-1.5 rounded-lg transition-all" 
                        title="Vista Tarjetas">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                </button>
                <button @click="viewMode = 'stack'" 
                        :class="viewMode === 'stack' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                        class="p-1.5 rounded-lg transition-all" 
                        title="Vista Lista">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                </button>
            </div>

            {{-- Botón Teclado Numérico (Keypad Starline) --}}
            <button @click="toggleKeypad()"
                    class="h-9 px-2.5 rounded-xl border flex items-center gap-1.5 text-xs font-semibold transition-all"
                    :class="showKeypad ? 'bg-brand-500 border-brand-600 text-white shadow-sm shadow-brand-500/20' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    title="Alternar Teclado Numérico">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                <span class="hidden md:inline">Keypad</span>
            </button>

            {{-- Botón Pantalla Completa (F11) --}}
            <button @click="toggleFullscreen()"
                    class="hidden lg:flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-white transition-all shadow-sm"
                    title="Pantalla Completa (F11)">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </button>

            {{-- Selector móvil Productos / Carrito --}}
            <div class="flex lg:hidden rounded-xl bg-slate-100 dark:bg-slate-800 p-0.5 border border-slate-200 dark:border-slate-700">
                <button @click="mobileTab = 'productos'"
                        :class="mobileTab === 'productos' ? 'bg-white dark:bg-slate-700 text-brand-600 font-bold shadow-sm' : 'text-slate-500'"
                        class="px-3 py-1.5 rounded-lg text-xs transition">
                    Catálogo
                </button>
                <button @click="mobileTab = 'carrito'"
                        :class="mobileTab === 'carrito' ? 'bg-white dark:bg-slate-700 text-brand-600 font-bold shadow-sm' : 'text-slate-500'"
                        class="px-3 py-1.5 rounded-lg text-xs transition relative flex items-center gap-1">
                    Ticket
                    <span x-show="carrito.length > 0" 
                          x-text="carrito.reduce((s,i)=>s+i.cantidad, 0)" 
                          class="w-4 h-4 rounded-full bg-brand-500 text-white text-[10px] font-black flex items-center justify-center"></span>
                </button>
            </div>
        </div>
    </header>

    {{-- ── 2. ÁREA CENTRAL (Catálogo + Ticket) ── --}}
    <div class="flex flex-1 min-h-0 overflow-hidden relative">

        {{-- ── COLUMNA IZQUIERDA: CATÁLOGO DE PRODUCTOS ── --}}
        <section class="flex flex-col flex-1 min-w-0 overflow-hidden transition-all duration-300"
                 :class="{ 'hidden lg:flex': mobileTab !== 'productos' }">

            {{-- Carrusel de Categorías con Contadores (Estilo CHILI POS & Starline) --}}
            <div class="px-4 lg:px-6 py-3 bg-white/60 dark:bg-[#111827]/60 border-b border-slate-200/80 dark:border-slate-800/80 backdrop-blur-sm">
                <div class="flex items-center gap-2 overflow-x-auto pos-scrollbar pb-1">
                    
                    {{-- Categoría "Todas" --}}
                    <button @click="categoriaFiltro = ''"
                            class="group shrink-0 px-4 py-2 rounded-2xl font-bold text-xs flex items-center gap-2.5 transition-all duration-200 border"
                            :class="categoriaFiltro === '' 
                                ? 'bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 shadow-md scale-[1.02]' 
                                : 'bg-white dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 border-slate-200/90 dark:border-slate-700/80 hover:border-brand-400 hover:bg-slate-50 dark:hover:bg-slate-800'">
                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-sm"
                              :class="categoriaFiltro === '' ? 'bg-white/20 dark:bg-slate-900/20' : 'bg-slate-100 dark:bg-slate-700'">
                            ✦
                        </span>
                        <span>Todas</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black"
                              :class="categoriaFiltro === '' ? 'bg-white/25 dark:bg-slate-900/20 text-white dark:text-slate-900' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">
                            {{ count($productos) }}
                        </span>
                    </button>

                    {{-- Lista de Categorías --}}
                    @php
                        $iconosCat = ['🍔', '🥤', '🍕', '🍰', '☕', '🍺', '🌮', '🥗', '🥑', '🍹', '🍪', '🍫'];
                    @endphp
                    @foreach ($categorias as $index => $cat)
                        @php
                            $prodCount = $productos->where('categoria_id', $cat->id)->count();
                            $icono = $iconosCat[$index % count($iconosCat)];
                        @endphp
                        <button @click="categoriaFiltro = '{{ $cat->id }}'"
                                class="group shrink-0 px-4 py-2 rounded-2xl font-bold text-xs flex items-center gap-2.5 transition-all duration-200 border"
                                :class="categoriaFiltro == '{{ $cat->id }}' 
                                    ? 'bg-slate-900 text-white border-slate-900 dark:bg-white dark:text-slate-900 shadow-md scale-[1.02]' 
                                    : 'bg-white dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 border-slate-200/90 dark:border-slate-700/80 hover:border-brand-400 hover:bg-slate-50 dark:hover:bg-slate-800'">
                            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-sm"
                                  :class="categoriaFiltro == '{{ $cat->id }}' ? 'bg-white/20 dark:bg-slate-900/20' : 'bg-slate-100 dark:bg-slate-700'">
                                {{ $icono }}
                            </span>
                            <span>{{ $cat->nombre }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black"
                                  :class="categoriaFiltro == '{{ $cat->id }}' ? 'bg-white/25 dark:bg-slate-900/20 text-white dark:text-slate-900' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">
                                {{ $prodCount }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Grid de Productos --}}
            <div class="flex-1 overflow-y-auto px-4 lg:px-6 py-4 pos-scrollbar">
                
                {{-- Estado Sin Productos --}}
                <div x-show="totalProductosVisibles === 0" 
                     class="flex flex-col items-center justify-center h-72 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 text-2xl mb-3">
                        🔍
                    </div>
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">No se encontraron productos</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm">Prueba ajustando el término de búsqueda o selecciona otra categoría.</p>
                    <button @click="busqueda = ''; categoriaFiltro = ''" class="mt-3 px-3 py-1.5 rounded-xl bg-brand-500 text-white text-xs font-bold hover:bg-brand-600 transition">
                        Restablecer filtros
                    </button>
                </div>

                {{-- Contenedor de Tarjetas --}}
                <div class="grid gap-3.5"
                     :class="viewMode === 'cards' 
                        ? 'grid-cols-2 sm:grid-cols-3 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5' 
                        : 'grid-cols-1 md:grid-cols-2'">
                    
                    @foreach ($productos as $producto)
                        <div x-show="productoVisible({{ $producto->id }}, {{ $producto->categoria_id ?? 'null' }})"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="pos-card-hover group relative rounded-2xl bg-white dark:bg-[#151c2c] border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-brand-300 dark:hover:border-brand-500/40 overflow-hidden cursor-pointer flex flex-col justify-between"
                             :class="viewMode === 'cards' ? 'p-3' : 'p-3 flex-row items-center gap-4'">

                            {{-- Parte Superior de la Tarjeta --}}
                            <div class="flex flex-col w-full" @click="agregarProducto({{ $producto->id }})">
                                
                                {{-- Thumbnail con Badges --}}
                                <div class="relative w-full rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800/80 mb-2.5 flex items-center justify-center"
                                     :class="viewMode === 'cards' ? 'h-32 sm:h-36' : 'w-20 h-20 shrink-0'">
                                    
                                    @if ($producto->imagen)
                                        <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                             alt="{{ $producto->nombre }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             loading="lazy" />
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 dark:text-slate-600 group-hover:scale-105 transition-transform duration-300">
                                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                    @endif

                                    {{-- Badge de Categoría flotante --}}
                                    @if ($producto->categoria)
                                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white/90 dark:bg-slate-900/90 text-slate-700 dark:text-slate-300 backdrop-blur-md shadow-xs border border-black/5 dark:border-white/10 truncate max-w-[80%]">
                                            {{ $producto->categoria->nombre }}
                                        </span>
                                    @endif

                                    {{-- Badge de Stock --}}
                                    <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-lg text-[10px] font-bold backdrop-blur-md shadow-xs"
                                          class="{{ $producto->stock > 10 ? 'bg-emerald-500/90 text-white' : ($producto->stock > 0 ? 'bg-amber-500/90 text-white' : 'bg-red-500/90 text-white') }}">
                                        {{ $producto->stock > 0 ? $producto->stock . ' ' . ($producto->unidad_medida ?? 'Und') : 'Agotado' }}
                                    </span>
                                </div>

                                {{-- Nombre y SKU --}}
                                <div class="min-w-0">
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug line-clamp-2 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors"
                                        title="{{ $producto->nombre }}">
                                        {{ $producto->nombre }}
                                    </h4>
                                    
                                    <div class="flex items-center gap-2 mt-1">
                                        @if ($producto->codigo_barras || $producto->sku)
                                            <span class="text-[10px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                                {{ $producto->codigo_barras ?? $producto->sku }}
                                            </span>
                                        @endif
                                        @if ($producto->impuesto > 0)
                                            <span class="text-[10px] text-slate-400">ITBMS {{ $producto->impuesto }}%</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Parte Inferior: Precio e Interacción Directa --}}
                            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-2">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Precio</span>
                                    <span class="text-base font-black text-slate-900 dark:text-white">
                                        ${{ number_format($producto->precio_venta, 2) }}
                                    </span>
                                </div>

                                {{-- In-Card Stepper si ya está en carrito (Estilo CHILI POS) --}}
                                <template x-if="obtenerCantidadEnCarrito({{ $producto->id }}) > 0">
                                    <div class="flex items-center rounded-xl bg-brand-50 dark:bg-brand-500/10 border border-brand-200 dark:border-brand-500/30 p-0.5" @click.stop>
                                        <button @click="cambiarCantidadPorId({{ $producto->id }}, -1)"
                                                class="w-7 h-7 rounded-lg bg-white dark:bg-slate-800 text-brand-600 dark:text-brand-400 font-bold flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-colors shadow-xs">
                                            -
                                        </button>
                                        <span class="w-7 text-center text-xs font-black text-brand-700 dark:text-brand-300"
                                              x-text="obtenerCantidadEnCarrito({{ $producto->id }})"></span>
                                        <button @click="cambiarCantidadPorId({{ $producto->id }}, 1)"
                                                class="w-7 h-7 rounded-lg bg-white dark:bg-slate-800 text-brand-600 dark:text-brand-400 font-bold flex items-center justify-center hover:bg-emerald-50 hover:text-emerald-600 transition-colors shadow-xs">
                                            +
                                        </button>
                                    </div>
                                </template>

                                {{-- Botón Agregar si NO está en carrito --}}
                                <template x-if="obtenerCantidadEnCarrito({{ $producto->id }}) === 0">
                                    <button @click.stop="agregarProducto({{ $producto->id }})"
                                            class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-brand-500 hover:text-white dark:hover:bg-brand-500 flex items-center justify-center font-bold text-sm transition-colors shadow-xs"
                                            title="Agregar al carrito">
                                        +
                                    </button>
                                </template>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── 3. BARRA INFERIOR DE VENTAS EN ESPERA (Estilo CHILI POS) ── --}}
            <footer class="h-12 px-4 lg:px-6 bg-white/90 dark:bg-[#111827]/90 border-t border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between gap-3 shrink-0 backdrop-blur-md z-10">
                <div class="flex items-center gap-2 overflow-x-auto pos-scrollbar min-w-0">
                    <span class="text-xs font-bold text-slate-400 flex items-center gap-1 shrink-0">
                        <span>⏳</span> En Espera:
                    </span>
                    
                    <template x-if="ventasEnEspera.length === 0">
                        <span class="text-xs text-slate-400 italic">No hay órdenes pausadas</span>
                    </template>

                    <template x-for="orden in ventasEnEspera" :key="orden.id">
                        <div class="shrink-0 flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-xs font-bold text-amber-800 dark:text-amber-300 transition-all hover:scale-102">
                            <button @click="retomarOrdenEspera(orden.id)" 
                                    class="flex items-center gap-1 hover:underline" 
                                    title="Retomar orden">
                                <span x-text="orden.nombre"></span>
                                <span class="text-[10px] text-amber-600 dark:text-amber-400" x-text="'(' + orden.items_count + ')'"></span>
                            </button>
                            <button @click="eliminarOrdenEspera(orden.id)" 
                                    class="p-0.5 rounded hover:bg-red-200 dark:hover:bg-red-900/50 text-amber-600 hover:text-red-600 transition" 
                                    title="Descartar">
                                ×
                            </button>
                        </div>
                    </template>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button @click="pausarVenta()" 
                            :disabled="carrito.length === 0"
                            class="px-2.5 py-1 rounded-lg text-xs font-bold text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/20 disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center gap-1">
                        <span>⏸</span> Pausar Actual
                    </button>
                </div>
            </footer>
        </section>

        {{-- ── COLUMNA DERECHA: TICKET DE VENTA (Inspirado en Starline & CHILI POS) ── --}}
        <aside class="flex flex-col bg-white dark:bg-[#111827] border-l border-slate-200/90 dark:border-slate-800/90 w-full lg:w-[410px] xl:w-[440px] shrink-0 h-full z-20 shadow-xl lg:shadow-none transition-all duration-300"
               :class="{ 'hidden lg:flex': mobileTab !== 'carrito', 'flex': mobileTab === 'carrito' }">

            {{-- Cabecera del Ticket --}}
            <div class="p-4 border-b border-slate-200/80 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-800/20">
                <div class="flex items-center justify-between mb-2.5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                            <h2 class="text-sm font-extrabold text-slate-800 dark:text-white tracking-tight">Ticket de Venta</h2>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5">Orden #{{ date('ymd') }}-<span x-text="correlativoOrden">01</span></p>
                    </div>

                    <div class="flex items-center gap-1">
                        {{-- Vaciar Carrito --}}
                        <button @click="limpiarCarrito()" 
                                :disabled="carrito.length === 0"
                                class="p-2 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors disabled:opacity-30 disabled:pointer-events-none" 
                                title="Vaciar Carrito">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Tipo de Orden: Píldoras (Estilo CHILI POS) --}}
                <div class="grid grid-cols-3 gap-1 p-1 rounded-xl bg-slate-100 dark:bg-slate-800/80 text-xs font-bold mb-2.5">
                    <button @click="tipoOrden = 'directo'"
                            :class="tipoOrden === 'directo' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                            class="py-1.5 rounded-lg transition-all text-center">
                        🍽️ Directo
                    </button>
                    <button @click="tipoOrden = 'llevar'"
                            :class="tipoOrden === 'llevar' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                            class="py-1.5 rounded-lg transition-all text-center">
                        🛍️ Para Llevar
                    </button>
                    <button @click="tipoOrden = 'delivery'"
                            :class="tipoOrden === 'delivery' ? 'bg-white dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                            class="py-1.5 rounded-lg transition-all text-center">
                        🛵 Delivery
                    </button>
                </div>

                {{-- Selector de Cliente Inteligente --}}
                <div class="relative">
                    <div class="flex items-center gap-2">
                        <select x-model="clienteId"
                                class="w-full h-9 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 text-xs font-medium text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-all">
                            <option value="">👤 Consumidor Final (General)</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Info de Crédito en Tiempo Real --}}
                    <template x-if="clienteId && clientesData[clienteId]">
                        <div class="mt-1.5 px-1 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 font-medium">Crédito disponible:</span>
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold" 
                                      :class="clientesData[clienteId].credito_disponible > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500'"
                                      x-text="'$' + clientesData[clienteId].credito_disponible.toFixed(2)"></span>
                                <template x-if="clientesData[clienteId].es_moroso">
                                    <span class="px-1.5 py-0.2 rounded bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400 font-extrabold text-[10px] animate-pulse">
                                        MOROSO
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Lista de Items del Carrito --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-2 pos-scrollbar">
                
                {{-- Carrito Vacío --}}
                <template x-if="carrito.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-center py-10 text-slate-400">
                        <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800/60 flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Tu carrito está vacío</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Toca o escanea un producto para comenzar.</p>
                    </div>
                </template>

                {{-- Tarjeta de Cada Producto en Carrito --}}
                <template x-for="(item, index) in carrito" :key="index">
                    <div class="p-3 rounded-2xl bg-slate-50/90 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/70 hover:border-brand-300 dark:hover:border-slate-600 transition-all flex flex-col gap-2">
                        
                        {{-- Fila Superior: Nombre y Eliminar --}}
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-snug truncate" x-text="item.nombre"></h5>
                                <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-400">
                                    <span x-text="'$' + item.precio_unitario.toFixed(2) + ' c/u'"></span>
                                    <span>•</span>
                                    <span x-text="'ITBMS ' + item.impuesto + '%'"></span>
                                </div>
                            </div>

                            <button @click="eliminarItem(index)" 
                                    class="p-1 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors"
                                    title="Quitar">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Fila Inferior: Controles de Cantidad y Total de Línea --}}
                        <div class="flex items-center justify-between">
                            {{-- Stepper de Cantidad --}}
                            <div class="flex items-center rounded-xl bg-white dark:bg-slate-700/80 border border-slate-200 dark:border-slate-600 p-0.5 shadow-2xs">
                                <button @click="cambiarCantidad(index, -1)" 
                                        class="w-7 h-7 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 font-black text-sm flex items-center justify-center transition">
                                    -
                                </button>
                                <input type="number" 
                                       :value="item.cantidad"
                                       @change="setCantidad(index, $event.target.value)"
                                       min="1" 
                                       :max="item.stock"
                                       class="w-10 text-center text-xs font-black bg-transparent border-0 text-slate-800 dark:text-white focus:outline-none p-0" />
                                <button @click="cambiarCantidad(index, 1)" 
                                        class="w-7 h-7 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 font-black text-sm flex items-center justify-center transition">
                                    +
                                </button>
                            </div>

                            {{-- Subtotal del Producto --}}
                            <div class="text-right">
                                <span class="text-sm font-black text-slate-900 dark:text-white"
                                      x-text="'$' + (item.precio_unitario * item.cantidad).toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ── TECLADO NUMÉRICO TÁCTIL (Inspirado en Starline Keypad) ── --}}
            <div x-show="showKeypad" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="p-3 bg-slate-100/90 dark:bg-[#161f33] border-t border-slate-200 dark:border-slate-700/80">
                
                {{-- Pestañas de Función del Keypad --}}
                <div class="grid grid-cols-4 gap-1 mb-2 text-[10px] font-bold">
                    <button @click="keypadMode = 'qty'" 
                            :class="keypadMode === 'qty' ? 'bg-brand-500 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                            class="py-1 rounded-lg transition text-center">
                        Cantidad
                    </button>
                    <button @click="keypadMode = 'disc'" 
                            :class="keypadMode === 'disc' ? 'bg-brand-500 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                            class="py-1 rounded-lg transition text-center">
                        Descuento %
                    </button>
                    <button @click="keypadMode = 'price'" 
                            :class="keypadMode === 'price' ? 'bg-brand-500 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                            class="py-1 rounded-lg transition text-center">
                        Precio
                    </button>
                    <button @click="keypadClear()" 
                            class="py-1 rounded-lg bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-400 transition text-center">
                        Borrar
                    </button>
                </div>

                {{-- Matriz 4x4 de Teclas --}}
                <div class="grid grid-cols-4 gap-1.5 text-sm font-black">
                    <button @click="keypadPress('7')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">7</button>
                    <button @click="keypadPress('8')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">8</button>
                    <button @click="keypadPress('9')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">9</button>
                    <button @click="keypadPress('+')" class="numpad-btn h-10 rounded-xl bg-slate-200 dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-xs">+</button>

                    <button @click="keypadPress('4')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">4</button>
                    <button @click="keypadPress('5')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">5</button>
                    <button @click="keypadPress('6')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">6</button>
                    <button @click="keypadPress('-')" class="numpad-btn h-10 rounded-xl bg-slate-200 dark:bg-slate-700 text-brand-600 dark:text-brand-400 shadow-xs">-</button>

                    <button @click="keypadPress('1')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">1</button>
                    <button @click="keypadPress('2')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">2</button>
                    <button @click="keypadPress('3')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">3</button>
                    <button @click="keypadBackspace()" class="numpad-btn h-10 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 shadow-xs flex items-center justify-center">⌫</button>

                    <button @click="keypadPress('.')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">.</button>
                    <button @click="keypadPress('0')" class="numpad-btn h-10 rounded-xl bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs">0</button>
                    <button @click="abrirPago()" :disabled="carrito.length === 0" class="numpad-btn col-span-2 h-10 rounded-xl bg-brand-500 hover:bg-brand-600 text-white shadow-md shadow-brand-500/20 disabled:opacity-40 flex items-center justify-center gap-1">
                        <span>Cobrar</span>
                    </button>
                </div>
            </div>

            {{-- ── RESUMEN FINANCIERO Y BOTÓN DE COBRO ── --}}
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-800/80 bg-white/95 dark:bg-[#111827]/95 space-y-2.5">
                
                {{-- Desglose de Totales --}}
                <div class="space-y-1 text-xs">
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>Subtotal</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="'$' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>ITBMS (Impuestos)</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="'$' + itbms.toFixed(2)"></span>
                    </div>
                    <template x-if="descuentoPorcentaje > 0">
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400 font-medium">
                            <span x-text="'Descuento (' + descuentoPorcentaje + '%)'"></span>
                            <span x-text="'-$' + montoDescuento.toFixed(2)"></span>
                        </div>
                    </template>
                </div>

                {{-- Total General Destacado --}}
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-baseline justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-400">Total a Pagar</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white" x-text="'$' + total.toFixed(2)"></span>
                </div>

                {{-- Métodos de Pago Rápidos (Pills estilo CHILI POS) --}}
                <div class="grid grid-cols-3 gap-1 pt-1">
                    <button @click="abrirPagoConMetodo('efectivo')" 
                            :disabled="carrito.length === 0"
                            class="py-2 px-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 hover:border-brand-500 hover:text-brand-600 dark:hover:text-brand-400 text-slate-700 dark:text-slate-300 text-[11px] font-bold transition flex items-center justify-center gap-1 disabled:opacity-40 disabled:pointer-events-none">
                        <span>💵</span> Efectivo
                    </button>
                    <button @click="abrirPagoConMetodo('tarjeta')" 
                            :disabled="carrito.length === 0"
                            class="py-2 px-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 hover:border-brand-500 hover:text-brand-600 dark:hover:text-brand-400 text-slate-700 dark:text-slate-300 text-[11px] font-bold transition flex items-center justify-center gap-1 disabled:opacity-40 disabled:pointer-events-none">
                        <span>💳</span> Tarjeta
                    </button>
                    <button @click="abrirPagoConMetodo('yappy')" 
                            :disabled="carrito.length === 0"
                            class="py-2 px-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 hover:border-brand-500 hover:text-brand-600 dark:hover:text-brand-400 text-slate-700 dark:text-slate-300 text-[11px] font-bold transition flex items-center justify-center gap-1 disabled:opacity-40 disabled:pointer-events-none">
                        <span>📱</span> Yappy / QR
                    </button>
                </div>

                {{-- Botón Principal COBRAR (Gran Jerarquía Visual) --}}
                <button @click="abrirPago()" 
                        :disabled="carrito.length === 0"
                        class="w-full h-12 rounded-2xl bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 text-white font-black text-sm sm:text-base flex items-center justify-center gap-2 shadow-lg shadow-brand-500/25 transition-all duration-200 active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>COBRAR</span>
                    <span x-text="'$' + total.toFixed(2)"></span>
                </button>

            </div>
        </aside>

    </div>

</div>

{{-- ============================================================
     MODAL DE PAGO Y FACTURACIÓN (REDESISEÑADO)
============================================================ --}}
<div x-data x-show="$store.posModal.open" x-cloak style="display:none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md">
    
    <div @click.stop 
         class="relative w-full max-w-xl rounded-3xl bg-white dark:bg-[#111827] shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col max-h-[90vh]"
         x-show="$store.posModal.open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
         x-transition:enter-end="opacity-100 scale-100 translate-y-0">

        {{-- Encabezado Modal --}}
        <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Procesar Venta & Cobro</h3>
                <p class="text-xs text-slate-400">Selecciona los métodos de pago y confirma el importe</p>
            </div>
            <button @click="$store.posModal.open = false" 
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                ✕
            </button>
        </div>

        {{-- Cuerpo Modal (Alpine data pagoModal) --}}
        <div x-data="pagoModal()" class="p-6 overflow-y-auto space-y-4 pos-scrollbar">
            
            {{-- Gran Tarjeta de Total a Pagar --}}
            <div class="p-4 rounded-2xl bg-gradient-to-br from-brand-50 to-brand-100/50 dark:from-brand-950/30 dark:to-slate-800/50 border border-brand-200/60 dark:border-brand-500/20 text-center">
                <span class="text-xs font-bold text-brand-600 dark:text-brand-400 uppercase tracking-wider">Total a Cobrar</span>
                <p class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white mt-0.5" 
                   x-text="'$' + montoTotal.toFixed(2)"></p>
            </div>

            {{-- Métodos de Pago Agregados --}}
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-400">Métodos de Pago</label>
                    <button @click="agregarPago()" type="button" 
                            class="text-xs text-brand-500 hover:text-brand-600 font-bold flex items-center gap-1">
                        + Dividir Pago
                    </button>
                </div>

                <template x-for="(pago, idx) in pagos" :key="idx">
                    <div class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/40 space-y-2.5">
                        <div class="flex items-center gap-2">
                            <select x-model="pago.metodo" @change="onMetodoChange(idx)"
                                    class="flex-1 h-10 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 text-xs font-bold text-slate-800 dark:text-white focus:ring-2 focus:ring-brand-500/20">
                                <template x-if="PAGO_EFECTIVO"><option value="efectivo">💵 Efectivo</option></template>
                                <template x-if="PAGO_TARJETA"><option value="tarjeta">💳 Tarjeta</option></template>
                                <template x-if="PAGO_TRANSFERENCIA"><option value="transferencia">🏦 Transferencia</option></template>
                                <template x-if="PAGO_YAPPY"><option value="yappy">📱 Yappy / Nequi</option></template>
                                <template x-if="POS_PERMITE_CREDITO"><option value="credito">📋 Crédito</option></template>
                            </select>

                            <div class="relative w-36">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">$</span>
                                <input x-model.number="pago.monto" 
                                       type="number" 
                                       step="0.01" 
                                       min="0"
                                       class="w-full h-10 pl-6 pr-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-right text-sm font-black text-slate-800 dark:text-white focus:ring-2 focus:ring-brand-500/20" />
                            </div>

                            <button x-show="pagos.length > 1" 
                                    @click="eliminarPago(idx)" 
                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition">
                                ×
                            </button>
                        </div>

                        {{-- Atajos de billetes para Efectivo (Estilo POS Profesional) --}}
                        <template x-if="pago.metodo === 'efectivo'">
                            <div class="flex items-center gap-1.5 pt-1 overflow-x-auto pos-scrollbar">
                                <span class="text-[10px] text-slate-400 font-bold shrink-0">Billetes:</span>
                                <button type="button" @click="pago.monto = 5" class="px-2 py-1 rounded-lg bg-white dark:bg-slate-800 border text-[11px] font-bold hover:border-brand-500">$5</button>
                                <button type="button" @click="pago.monto = 10" class="px-2 py-1 rounded-lg bg-white dark:bg-slate-800 border text-[11px] font-bold hover:border-brand-500">$10</button>
                                <button type="button" @click="pago.monto = 20" class="px-2 py-1 rounded-lg bg-white dark:bg-slate-800 border text-[11px] font-bold hover:border-brand-500">$20</button>
                                <button type="button" @click="pago.monto = 50" class="px-2 py-1 rounded-lg bg-white dark:bg-slate-800 border text-[11px] font-bold hover:border-brand-500">$50</button>
                                <button type="button" @click="pago.monto = 100" class="px-2 py-1 rounded-lg bg-white dark:bg-slate-800 border text-[11px] font-bold hover:border-brand-500">$100</button>
                                <button type="button" @click="pago.monto = montoTotal.toFixed(2)" class="px-2 py-1 rounded-lg bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-500/30 text-[11px] font-black">Exacto</button>
                            </div>
                        </template>

                        {{-- Campo Referencia (Tarjetas / Electrónicos) --}}
                        <template x-if="pago.metodo !== 'efectivo' && pago.metodo !== 'credito'">
                            <input x-model="pago.referencia" 
                                   type="text"
                                   placeholder="N° Referencia / Aprobación / Voucher"
                                   class="w-full h-8 px-3 rounded-lg border border-amber-300 dark:border-amber-700/60 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-white" />
                        </template>

                        {{-- Campo Crédito Fecha Vencimiento --}}
                        <template x-if="pago.metodo === 'credito'">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Vencimiento del Crédito</label>
                                <input x-model="fechaVencimiento" type="date"
                                       class="w-full h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs" />
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Cambio a Devolver o Faltante --}}
            <template x-if="mostrarCambio && cambio >= 0">
                <div class="p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 block">Cambio a Devolver</span>
                        <span class="text-[11px] text-emerald-600 dark:text-emerald-500">Devolver en efectivo al cliente</span>
                    </div>
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400" x-text="'$' + cambio.toFixed(2)"></span>
                </div>
            </template>

            <template x-if="faltante > 0">
                <div class="p-3 rounded-2xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 flex items-center justify-between">
                    <span class="text-xs font-bold text-red-600 dark:text-red-400">Importe pendiente de cubrir:</span>
                    <span class="text-base font-black text-red-600 dark:text-red-400" x-text="'$' + faltante.toFixed(2)"></span>
                </div>
            </template>

            {{-- Facturación Electrónica DIAN --}}
            <div class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 flex items-center justify-between">
                <div>
                    <h5 class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                        <span>⚡</span> Facturación Electrónica (DIAN)
                    </h5>
                    <p class="text-[11px] text-slate-400">Timbrar y transmitir automáticamente por Factus</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" x-model="generarFacturaDian" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                </label>
            </div>

            {{-- Botón de Confirmar Pago --}}
            <div class="pt-2">
                <button @click="confirmarPago()" 
                        :disabled="cargando || faltante > 0"
                        class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-base flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                    <template x-if="!cargando">
                        <span class="flex items-center gap-2">
                            <span>✓</span> Confirmar y Finalizar Venta
                        </span>
                    </template>
                    <template x-if="cargando">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Procesando Venta...
                        </span>
                    </template>
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Modal Scanner de Cámara (POS) --}}
<div id="modalScannerPOS"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/75 backdrop-blur-sm hidden"
     onclick="if(event.target===this) cerrarScannerPOS()">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center text-brand-600 dark:text-brand-400">
                    📷
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Escáner de Código</h3>
                    <p class="text-[10px] text-slate-400">Apunta hacia el código de barras</p>
                </div>
            </div>
            <button onclick="cerrarScannerPOS()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">✕</button>
        </div>
        <div id="scanner-pos-viewport" class="bg-black" style="height: 260px; position: relative;">
            <div id="scan-line-pos" style="display:none; position:absolute; left:0; right:0; height:2px; background: linear-gradient(90deg, transparent, #00c2ff, transparent); z-index:10; animation: scanLinePOS 2s linear infinite;"></div>
        </div>
        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/40 text-center min-h-[44px] flex items-center justify-center">
            <p id="scanner-pos-status" class="text-xs text-slate-500 dark:text-slate-400">Iniciando cámara...</p>
        </div>
        <div id="scanner-pos-result" class="hidden px-5 py-3 bg-emerald-50 dark:bg-emerald-950/30 border-t border-emerald-100 dark:border-emerald-800/50">
            <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400" id="scanner-pos-result-name">—</p>
            <p class="text-[10px] text-emerald-600 dark:text-emerald-500">Agregado al carrito ✓</p>
        </div>
        <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex gap-2">
            <button onclick="cerrarScannerPOS()" class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300">Cerrar</button>
            <button onclick="document.getElementById('scanner-pos-result').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl bg-brand-500 text-white text-xs font-bold">Continuar</button>
        </div>
    </div>
</div>

<style>
@keyframes scanLinePOS {
    0%   { top: 10px; opacity: 1; }
    50%  { opacity: 0.5; }
    100% { top: 240px; opacity: 1; }
}
</style>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

{{-- ── LÓGICA JAVASCRIPT COMPLETA DEL POS ── --}}
<script>
    // ─── Settings Inyectadas desde PHP ───────────────────────────────────
    const POS_PERMITE_CREDITO      = {{ ($posSettings['pos_ventas_credito']      ?? '0') == '1' ? 'true' : 'false' }};
    const POS_PERMITE_SIN_CLIENTE  = {{ ($posSettings['pos_ventas_sin_cliente']  ?? '1') == '1' ? 'true' : 'false' }};
    const POS_CONFIRMACION         = {{ ($posSettings['pos_confirmacion_venta']  ?? '1') == '1' ? 'true' : 'false' }};
    const POS_VENTA_RAPIDA         = {{ ($posSettings['pos_venta_rapida']        ?? '0') == '1' ? 'true' : 'false' }};
    const POS_AUTOFOCUS            = {{ ($posSettings['pos_autofocus_buscador']  ?? '1') == '1' ? 'true' : 'false' }};

    @php
        $pagosSettings = \App\Models\Setting::group('pagos');
    @endphp
    const PAGO_EFECTIVO            = {{ ($pagosSettings['pago_efectivo']         ?? '1') == '1' ? 'true' : 'false' }};
    const PAGO_TARJETA             = {{ ($pagosSettings['pago_tarjeta']          ?? '1') == '1' ? 'true' : 'false' }};
    const PAGO_TRANSFERENCIA       = {{ ($pagosSettings['pago_transferencia']    ?? '1') == '1' ? 'true' : 'false' }};
    const PAGO_YAPPY               = {{ ($pagosSettings['pago_yappy']            ?? '1') == '1' ? 'true' : 'false' }};
    const REF_TARJETA              = {{ ($pagosSettings['pago_referencia_tarjeta'] ?? '0') == '1' ? 'true' : 'false' }};
    const REF_TRANSFERENCIA        = {{ ($pagosSettings['pago_referencia_transferencia'] ?? '1') == '1' ? 'true' : 'false' }};

    // ─── Catálogo de Productos y Clientes ────────────────────────────────
    const productosData = {!! json_encode($productos->map(fn($p) => [
        'id' => $p->id,
        'nombre' => $p->nombre,
        'precio_unitario' => (float) $p->precio_venta,
        'impuesto' => (float) $p->impuesto,
        'stock' => (int) $p->stock,
        'codigo_barras' => $p->codigo_barras,
        'categoria_id' => $p->categoria_id,
        'sku' => $p->sku,
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
        Alpine.store('posModal', { open: false });
        Alpine.store('posData', {
            total: 0,
            carrito: [],
            clienteId: null,
            sucursalId: SUCURSAL_ID,
            metodoPredeterminado: 'efectivo'
        });
    });

    // ─── Controlador Principal del POS ──────────────────────────────────
    function posApp() {
        return {
            viewMode: 'cards',
            mobileTab: 'productos',
            busqueda: '',
            categoriaFiltro: '',
            carrito: [],
            clienteId: '',
            tipoOrden: 'directo',
            clockTime: '',
            showKeypad: false,
            keypadMode: 'qty', // 'qty', 'disc', 'price'
            keypadBuffer: '',
            descuentoPorcentaje: 0,
            ventasEnEspera: [],
            correlativoOrden: Math.floor(10 + Math.random() * 90),

            init() {
                // Reloj en tiempo real
                this.updateClock();
                setInterval(() => this.updateClock(), 1000);

                // Cargar ventas en espera
                this.cargarVentasEnEspera();

                // Auto-focus en el buscador
                if (POS_AUTOFOCUS) {
                    this.$nextTick(() => {
                        const el = document.getElementById('busqueda-input');
                        if (el) el.focus();
                    });
                }

                // Prevenir cierre accidental
                window.addEventListener('beforeunload', (e) => {
                    if (this.carrito.length > 0) {
                        e.preventDefault();
                        e.returnValue = 'Tienes productos en el carrito. Si sales, la orden se perderá.';
                        return e.returnValue;
                    }
                });
            },

            updateClock() {
                const now = new Date();
                this.clockTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            },

            handleGlobalKeydown(e) {
                // Atajo '/' para enfocar buscador
                if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
                    e.preventDefault();
                    const el = document.getElementById('busqueda-input');
                    if (el) el.focus();
                }
                // Atajo F2 o Enter para cobrar si el carrito tiene items y no hay modales abiertos
                if (e.key === 'F2' && this.carrito.length > 0 && !Alpine.store('posModal').open) {
                    e.preventDefault();
                    this.abrirPago();
                }
            },

            toggleFullscreen() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(() => {});
                } else {
                    document.exitFullscreen().catch(() => {});
                }
            },

            toggleKeypad() {
                this.showKeypad = !this.showKeypad;
            },

            // ── Totales y Cálculos ──
            get subtotal() {
                return this.carrito.reduce((s, i) => s + (i.precio_unitario * i.cantidad), 0);
            },
            get itbms() {
                return this.carrito.reduce((s, i) => s + ((i.precio_unitario * i.cantidad) * (i.impuesto / 100)), 0);
            },
            get montoDescuento() {
                if (this.descuentoPorcentaje <= 0) return 0;
                return (this.subtotal * (this.descuentoPorcentaje / 100));
            },
            get total() {
                return Math.max(0, (this.subtotal - this.montoDescuento) + this.itbms);
            },
            get totalProductosVisibles() {
                return productosData.filter(p => this.productoVisible(p.id, p.categoria_id)).length;
            },

            // ── Visibilidad y Filtros ──
            productoVisible(id, catId) {
                const prod = productosData.find(p => p.id === id);
                if (!prod) return false;
                const matchCat = !this.categoriaFiltro || catId == this.categoriaFiltro;
                if (!this.busqueda) return matchCat;
                const term = this.busqueda.toLowerCase().trim();
                const matchName = prod.nombre.toLowerCase().includes(term);
                const matchCode = prod.codigo_barras && prod.codigo_barras.toLowerCase().includes(term);
                const matchSku  = prod.sku && prod.sku.toLowerCase().includes(term);
                return matchCat && (matchName || matchCode || matchSku);
            },

            buscarPorCodigo() {
                const term = this.busqueda.trim();
                if (!term) return;
                const prod = productosData.find(p => p.codigo_barras === term || p.sku === term);
                if (prod) {
                    this.agregarProducto(prod.id);
                    this.busqueda = '';
                    this.$nextTick(() => {
                        const el = document.getElementById('busqueda-input');
                        if (el) el.focus();
                    });
                }
            },

            obtenerCantidadEnCarrito(id) {
                const item = this.carrito.find(i => i.id === id);
                return item ? item.cantidad : 0;
            },

            // ── Carrito ──
            agregarProducto(id) {
                const prod = productosData.find(p => p.id === id);
                if (!prod) return;

                const existing = this.carrito.find(i => i.id === id);
                if (existing) {
                    if (existing.cantidad < prod.stock) {
                        existing.cantidad++;
                    } else {
                        window.Notify?.warning(`Stock máximo alcanzado (${prod.stock})`);
                    }
                } else {
                    if (prod.stock <= 0) {
                        window.Notify?.warning('Producto sin inventario disponible');
                        return;
                    }
                    this.carrito.push({
                        ...prod,
                        cantidad: 1
                    });
                }

                if (POS_VENTA_RAPIDA && this.carrito.length > 0) {
                    this.$nextTick(() => this.abrirPago());
                }
            },

            cambiarCantidadPorId(id, delta) {
                const idx = this.carrito.findIndex(i => i.id === id);
                if (idx !== -1) {
                    this.cambiarCantidad(idx, delta);
                } else if (delta > 0) {
                    this.agregarProducto(id);
                }
            },

            cambiarCantidad(index, delta) {
                const item = this.carrito[index];
                const nueva = item.cantidad + delta;
                if (nueva < 1) {
                    this.eliminarItem(index);
                    return;
                }
                if (nueva > item.stock) {
                    window.Notify?.warning(`Stock disponible: ${item.stock}`);
                    return;
                }
                item.cantidad = nueva;
            },

            setCantidad(index, val) {
                const item = this.carrito[index];
                const qty = parseInt(val) || 1;
                if (qty > item.stock) {
                    item.cantidad = item.stock;
                    window.Notify?.warning(`Stock ajustado al máximo (${item.stock})`);
                    return;
                }
                if (qty < 1) {
                    this.eliminarItem(index);
                    return;
                }
                item.cantidad = qty;
            },

            eliminarItem(index) {
                this.carrito.splice(index, 1);
            },

            limpiarCarrito() {
                if (this.carrito.length === 0) return;
                window.Confirm?.show(
                    '¿Vaciar carrito?',
                    'Se removerán todos los productos de la orden actual.',
                    'Sí, vaciar',
                    'Cancelar',
                    () => { this.carrito = []; this.descuentoPorcentaje = 0; },
                    () => {},
                    { okButtonBackground: '#ef4444' }
                );
            },

            // ── Lógica del Keypad Táctil (Starline) ──
            keypadPress(char) {
                if (char === '+' || char === '-') {
                    if (this.carrito.length > 0) {
                        const lastIndex = this.carrito.length - 1;
                        this.cambiarCantidad(lastIndex, char === '+' ? 1 : -1);
                    }
                    return;
                }
                this.keypadBuffer += char;
                this.applyKeypadValue();
            },

            keypadBackspace() {
                this.keypadBuffer = this.keypadBuffer.slice(0, -1);
                this.applyKeypadValue();
            },

            keypadClear() {
                this.keypadBuffer = '';
                if (this.keypadMode === 'disc') this.descuentoPorcentaje = 0;
            },

            applyKeypadValue() {
                const val = parseFloat(this.keypadBuffer);
                if (isNaN(val)) return;

                if (this.keypadMode === 'qty' && this.carrito.length > 0) {
                    const lastIndex = this.carrito.length - 1;
                    this.setCantidad(lastIndex, Math.max(1, Math.floor(val)));
                } else if (this.keypadMode === 'disc') {
                    this.descuentoPorcentaje = Math.min(100, Math.max(0, val));
                } else if (this.keypadMode === 'price' && this.carrito.length > 0) {
                    const lastItem = this.carrito[this.carrito.length - 1];
                    lastItem.precio_unitario = val;
                }
            },

            // ── Cobro & Checkout ──
            abrirPagoConMetodo(metodo) {
                this.abrirPago(metodo);
            },

            abrirPago(metodoInicial = 'efectivo') {
                if (this.carrito.length === 0) return;

                if (!POS_PERMITE_SIN_CLIENTE && !this.clienteId) {
                    window.Notify?.warning('Cliente requerido por configuración antes de proceder al cobro.');
                    return;
                }

                Alpine.store('posData', {
                    total: this.total,
                    carrito: this.carrito,
                    clienteId: this.clienteId || null,
                    sucursalId: SUCURSAL_ID,
                    metodoPredeterminado: metodoInicial
                });

                Alpine.store('posModal').open = true;
            },

            // ── Ventas en Espera (Parked Sales) ──
            cargarVentasEnEspera() {
                fetch(ESPERA_IDX, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (Array.isArray(data)) {
                            this.ventasEnEspera = data;
                        } else if (data && data.data) {
                            this.ventasEnEspera = data.data;
                        }
                    })
                    .catch(() => {});
            },

            pausarVenta() {
                if (this.carrito.length === 0) return;
                const nombre = prompt('Escribe un nombre para pausar esta orden (Ej: Mesa 4, Carlos...):');
                if (!nombre) return;

                window.Loading?.pulse('Pausando orden...');
                fetch(ESPERA_STORE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({
                        nombre: nombre,
                        sucursal_id: SUCURSAL_ID,
                        carrito: this.carrito
                    })
                }).then(r => r.json()).then(res => {
                    window.Loading?.remove();
                    if (res.success) {
                        this.carrito = [];
                        this.cargarVentasEnEspera();
                        window.Notify?.success(res.message || 'Orden pausada con éxito');
                    }
                }).catch(() => {
                    window.Loading?.remove();
                    window.Notify?.failure('No se pudo pausar la orden');
                });
            },

            retomarOrdenEspera(id) {
                window.Loading?.pulse('Cargando orden...');
                fetch(`/caja/espera/${id}/retomar`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                }).then(r => r.json()).then(res => {
                    window.Loading?.remove();
                    if (res.success) {
                        if (Array.isArray(res.carrito)) {
                            this.carrito = res.carrito;
                        }
                        this.cargarVentasEnEspera();
                        window.Notify?.success('Orden retomada con éxito');
                    }
                }).catch(() => {
                    window.Loading?.remove();
                });
            },

            eliminarOrdenEspera(id) {
                window.Confirm?.show(
                    '¿Descartar orden?',
                    '¿Deseas descartar esta orden en espera permanentemente?',
                    'Descartar',
                    'Cancelar',
                    () => {
                        fetch(`/caja/espera/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                        }).then(() => {
                            this.cargarVentasEnEspera();
                            window.Notify?.success('Orden descartada');
                        });
                    },
                    () => {},
                    { okButtonBackground: '#ef4444' }
                );
            }
        };
    }

    // ─── Controlador del Modal de Pago ──────────────────────────────────
    function pagoModal() {
        return {
            pagos: [],
            fechaVencimiento: new Date(new Date().setDate(new Date().getDate() + 30)).toISOString().split('T')[0],
            cargando: false,
            generarFacturaDian: false,
            formaPagoDian: '1',
            metodoPagoDian: '10',

            get montoTotal() {
                return Alpine.store('posData').total;
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
                const metodoDefault = Alpine.store('posData').metodoPredeterminado || 'efectivo';
                this.pagos = [{
                    metodo: metodoDefault,
                    monto: this.montoTotal.toFixed(2),
                    referencia: '',
                    tipo_tarjeta: '',
                    banco: ''
                }];
            },

            agregarPago() {
                let metodo = 'tarjeta';
                if (!PAGO_TARJETA && PAGO_EFECTIVO) metodo = 'efectivo';
                this.pagos.push({
                    metodo: metodo,
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
                    if (!Alpine.store('posData').clienteId) {
                        window.Notify?.warning('Cliente requerido antes de usar Crédito.');
                        pago.metodo = 'efectivo';
                        return;
                    }
                    const cliente = clientesData[Alpine.store('posData').clienteId];
                    if (!cliente || cliente.credito_disponible <= 0) {
                        window.Notify?.failure('Sin crédito - El cliente no tiene crédito disponible.');
                        pago.metodo = 'efectivo';
                        return;
                    }
                    const otrosPagos = this.pagos.filter((_, i) => i !== idx).reduce((s, p) => s + (parseFloat(p.monto) || 0), 0);
                    const restante = Math.max(0, this.montoTotal - otrosPagos);
                    pago.monto = Math.min(cliente.credito_disponible, restante).toFixed(2);
                }
            },

            async confirmarPago() {
                for (const pago of this.pagos) {
                    if (pago.metodo === 'tarjeta' && REF_TARJETA && !pago.referencia?.trim()) {
                        window.Notify?.warning('Referencia requerida para tarjeta.');
                        return;
                    }
                    if ((pago.metodo === 'transferencia' || pago.metodo === 'yappy') && REF_TRANSFERENCIA && !pago.referencia?.trim()) {
                        window.Notify?.warning('Referencia requerida para transferencia / Yappy.');
                        return;
                    }
                }

                this.cargando = true;
                window.Loading?.pulse('Registrando venta...');

                try {
                    const resp = await fetch(VENTA_STORE, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                        body: JSON.stringify({
                            sucursal_id: Alpine.store('posData').sucursalId,
                            cliente_id: Alpine.store('posData').clienteId || null,
                            forma_pago_dian: this.generarFacturaDian ? this.formaPagoDian : null,
                            metodo_pago_dian_id: this.generarFacturaDian ? this.metodoPagoDian : null,
                            items: Alpine.store('posData').carrito.map(i => ({
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
                            }))
                        })
                    });

                    const data = await resp.json();
                    window.Loading?.remove();
                    this.cargando = false;

                    if (data.success) {
                        Alpine.store('posModal').open = false;

                        // Factura DIAN Automática
                        if (this.generarFacturaDian) {
                            try {
                                await fetch("{{ route('facturacion.store') }}", {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                                    body: JSON.stringify({ venta_id: data.venta_id })
                                });
                            } catch (e) {}
                        }

                        window.Notify?.success(`¡Venta Registrada! ${data.numero}`);
                        window.Confirm?.show(
                            '¿Imprimir Ticket?',
                            `Venta completada por $${this.montoTotal.toFixed(2)}. ¿Deseas imprimir el comprobante térmico?`,
                            'Imprimir Ticket',
                            'Nueva Venta',
                            () => {
                                window.dispatchEvent(new CustomEvent('abrir-ticket', { detail: `/caja/ventas/${data.venta_id}/ticket` }));
                                window.addEventListener('ticket-cerrado', function _reload() {
                                    window.removeEventListener('ticket-cerrado', _reload);
                                    location.reload();
                                });
                            },
                            () => { location.reload(); },
                            { okButtonBackground: '#00c2ff' }
                        );
                    } else {
                        window.Notify?.failure(`Error: ${data.message}`);
                    }
                } catch (e) {
                    this.cargando = false;
                    window.Loading?.remove();
                    window.Notify?.failure(`Error de red: ${e.message}`);
                }
            }
        };
    }

    // ─── Lógica del Scanner de Cámara ───────────────────────────────────
    let _scannerPOSInstance = null;
    let _scannerPOSCooldown = false;

    function abrirScannerPOS() {
        const modal = document.getElementById('modalScannerPOS');
        if (!modal) return;
        modal.classList.remove('hidden');
        document.getElementById('scan-line-pos').style.display = 'block';
        document.getElementById('scanner-pos-result')?.classList.add('hidden');
        document.getElementById('scanner-pos-status').textContent = 'Iniciando cámara...';

        if (_scannerPOSInstance) {
            _scannerPOSInstance.clear().catch(() => {});
        }

        _scannerPOSInstance = new Html5Qrcode('scanner-pos-viewport');

        _scannerPOSInstance.start(
            { facingMode: 'environment' },
            { fps: 12, qrbox: { width: 260, height: 160 }, aspectRatio: 1.6 },
            (decodedText) => {
                if (_scannerPOSCooldown) return;
                _scannerPOSCooldown = true;

                const prod = productosData.find(p => p.codigo_barras === decodedText || p.sku === decodedText);
                if (prod) {
                    const posEl = document.querySelector('[x-data]');
                    if (posEl && posEl._x_dataStack) {
                        posEl._x_dataStack[0].agregarProducto(prod.id);
                    }
                    document.getElementById('scanner-pos-result-name').textContent = prod.nombre + ' — $' + parseFloat(prod.precio_venta || prod.precio_unitario).toFixed(2);
                    document.getElementById('scanner-pos-result')?.classList.remove('hidden');
                    document.getElementById('scanner-pos-status').textContent = '✅ Detectado: ' + decodedText;
                } else {
                    document.getElementById('scanner-pos-status').textContent = '⚠️ Código no registrado: ' + decodedText;
                    document.getElementById('scanner-pos-result')?.classList.add('hidden');
                }

                setTimeout(() => { _scannerPOSCooldown = false; }, 1500);
            },
            () => {}
        ).catch(() => {
            document.getElementById('scanner-pos-status').textContent = '⚠️ No se pudo acceder a la cámara.';
        });
    }

    function cerrarScannerPOS() {
        document.getElementById('modalScannerPOS')?.classList.add('hidden');
        if (_scannerPOSInstance) {
            _scannerPOSInstance.stop().catch(() => {});
            _scannerPOSInstance.clear().catch(() => {});
            _scannerPOSInstance = null;
        }
        document.getElementById('busqueda-input')?.focus();
    }
</script>

@endsection
