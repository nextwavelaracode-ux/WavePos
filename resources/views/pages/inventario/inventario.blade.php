@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Control de Inventario" />

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

    <div id="inventario-root" x-data="{
        showEntrada: false,
        showSalida: false,
        showAjuste: false,
        searchTerm: '',
        filterCategoria: '',
        filterStock: '',
    }">

        {{-- ===== RESUMEN CARDS ===== --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-white/[0.05] dark:bg-gray-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10">
                        <svg class="h-6 w-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Productos</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ $totalProductos }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-white/[0.05] dark:bg-gray-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/10">
                        <svg class="h-6 w-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Stock Bajo</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stockBajo }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-white/[0.05] dark:bg-gray-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 dark:bg-red-500/10">
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sin Stock</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $sinStock }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-white/[0.05] dark:bg-gray-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
                        <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Stock Normal</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $totalProductos - $stockBajo - $sinStock }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== HEADER / TOOLBAR ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Listado de Inventario</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Estado actual del stock de todos los productos</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showEntrada = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nueva Entrada
                </button>
                <button @click="showSalida = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-red-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-600 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    Nueva Salida
                </button>
            </div>
        </div>

        {{-- ===== FILTROS ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm min-w-[200px]" x-data="{
                dropdownOpen: false,
                setCategoria(cat) {
                    $data.filterCategoria = cat;
                    this.dropdownOpen = false;
                }
            }" @click.outside="dropdownOpen = false">
                <div class="relative mt-2">
                    <div class="absolute top-1 left-1 flex items-center">
                        <button type="button" @click="dropdownOpen = !dropdownOpen" class="rounded border border-transparent py-1 px-1.5 text-center flex items-center text-sm transition-all text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-gray-800">
                            <span class="text-ellipsis overflow-hidden whitespace-nowrap max-w-[120px]" x-text="filterCategoria === '' ? 'Categorías' : filterCategoria">Categorías</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 ml-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <div class="h-6 border-l border-slate-200 dark:border-gray-700 ml-1"></div>
                        
                        <div x-show="dropdownOpen" x-transition.opacity class="min-w-[200px] overflow-hidden absolute left-0 top-full mt-2 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-md shadow-lg z-50" style="display:none;">
                            <ul class="max-h-64 overflow-y-auto py-1">
                                <li @click="setCategoria('')" class="px-4 py-2 text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-gray-700 text-sm cursor-pointer whitespace-nowrap">Todas las categorías</li>
                                @foreach($categorias as $cat)
                                <li @click="setCategoria('{{ addslashes($cat->nombre) }}')" class="px-4 py-2 text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-gray-700 text-sm cursor-pointer whitespace-nowrap">{{ $cat->nombre }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <input
                        x-model="searchTerm"
                        type="text"
                        class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 dark:text-white text-sm border border-slate-200 dark:border-gray-700 rounded-md pl-32 pr-24 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 dark:hover:border-gray-600 shadow-sm focus:shadow"
                        placeholder="Buscar producto o código..." />
                    
                    <button
                        class="absolute top-1 right-1 flex items-center rounded bg-slate-800 dark:bg-brand-500 py-1 px-2.5 border border-transparent text-center text-sm text-white transition-all shadow-sm hover:shadow focus:bg-slate-700 dark:focus:bg-brand-600"
                        type="button"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 mr-1.5">
                            <path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" />
                        </svg>
                        Buscar
                    </button> 
                </div>   
            </div>

            <div class="sm:w-auto mt-0">
                <select x-model="filterStock" class="block w-full rounded-md border border-slate-200 bg-transparent py-2.5 px-3 text-sm text-slate-700 shadow-sm focus:border-slate-400 focus:ring-0 dark:border-gray-700 dark:text-gray-300">
                    <option value="">Todos los niveles</option>
                    <option value="bajo">Stock bajo</option>
                    <option value="agotado">Agotado</option>
                    <option value="normal">Stock normal</option>
                </select>
            </div>
        </div>

        {{-- ===== TABLA DE STOCK ===== --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">ID</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Producto</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categoría</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Código Barras</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stock Actual</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Mín</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Máx</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ubicación</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                        @forelse ($productos as $producto)
                            @php
                                $stockStatus = 'normal';
                                if ($producto->stock <= 0) $stockStatus = 'agotado';
                                elseif ($producto->stock <= $producto->stock_minimo) $stockStatus = 'bajo';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors"
                                x-show="(searchTerm === '' || '{{ strtolower($producto->nombre . ' ' . $producto->codigo_barras . ' ' . $producto->sku) }}'.includes(searchTerm.toLowerCase())) &&
                                        (filterCategoria === '' || '{{ optional($producto->categoria)->nombre }}' === filterCategoria) &&
                                        (filterStock === '' || filterStock === '{{ $stockStatus }}')"
                            >
                                <td class="px-5 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $producto->id }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 flex-shrink-0 rounded-lg bg-gray-100 dark:bg-gray-800 overflow-hidden flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                            @if($producto->imagen_url)
                                                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="h-full w-full object-cover">
                                            @else
                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white/90">{{ $producto->nombre }}</p>
                                            <p class="text-xs text-gray-500">{{ $producto->sku ?: 'Sin SKU' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                        {{ optional($producto->categoria)->nombre ?? 'Sin Categoria' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-gray-600 dark:text-gray-400 text-xs font-mono">
                                    {{ $producto->codigo_barras ?? '—' }}
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
                                        <span class="inline-flex items-center gap-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $producto->stock }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ $producto->stock_minimo }}</td>
                                <td class="px-5 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ $producto->stock_maximo ?? '—' }}</td>

                                <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    @if($producto->ubicacion || $producto->pasillo || $producto->estante)
                                        <span class="text-xs">{{ implode(' / ', array_filter([$producto->ubicacion, $producto->pasillo, $producto->estante])) }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if ($producto->estado)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-500 dark:bg-white/[0.05] dark:text-gray-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Ver movimientos --}}
                                        <a href="{{ route('inventario.movimientos', ['producto_id' => $producto->id]) }}"
                                            class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition-colors"
                                            title="Ver movimientos">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </a>
                                        {{-- Ajustar --}}
                                        <button type="button"
                                            onclick="abrirAjuste({{ $producto->id }}, '{{ addslashes($producto->nombre) }}', {{ $producto->stock }})"
                                            class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-100 dark:border-blue-800/30 dark:bg-blue-500/10 dark:text-blue-400 transition-colors"
                                            title="Ajustar inventario">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-600">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        <p class="text-sm font-medium">No hay productos registrados en el inventario</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ====================  MODAL ENTRADA  ==================== --}}
        <div x-show="showEntrada" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showEntrada = false" style="display:none">

            <div @click.outside="showEntrada = false"
                class="no-scrollbar relative w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 mx-4 max-h-[92vh] lg:p-10">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Nueva Entrada de Inventario</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Registra productos que ingresan al almacén</p>
                    </div>
                    <button @click="showEntrada = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('inventario.entrada') }}" method="POST" id="form-entrada">
                    @csrf
                    <div class="space-y-5">
                        {{-- Producto --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Producto <span class="text-red-500">*</span></label>
                            <select name="producto_id" required class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700">
                                <option value="">Seleccionar producto...</option>
                                @foreach($productos as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }} (Stock: {{ $p->stock }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Proveedor --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Proveedor</label>
                            <select name="proveedor_id" class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700">
                                <option value="">Sin proveedor</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->empresa }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- Cantidad --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Cantidad <span class="text-red-500">*</span></label>
                                <input type="number" name="cantidad" min="1" required class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700" placeholder="0">
                            </div>
                            {{-- Precio de compra --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Precio de Compra</label>
                                <input type="number" name="precio_compra" min="0" step="0.01" class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700" placeholder="0.00">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- Fecha --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Entrada <span class="text-red-500">*</span></label>
                                <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700">
                            </div>
                            {{-- Factura --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nro. Factura</label>
                                <input type="text" name="numero_factura" class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700" placeholder="FAC-000">
                            </div>
                        </div>

                        {{-- Observaciones --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Observaciones</label>
                            <textarea name="observaciones" rows="2" class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700" placeholder="Notas adicionales..."></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                        <button type="button" @click="showEntrada = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors">Cancelar</button>
                        <button type="button" onclick="confirmarEntrada()"
                            class="rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-600 transition-colors">Registrar Entrada</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================  MODAL SALIDA  ==================== --}}
        <div x-show="showSalida" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showSalida = false" style="display:none">

            <div @click.outside="showSalida = false"
                class="no-scrollbar relative w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 mx-4 max-h-[92vh] lg:p-10">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Nueva Salida de Inventario</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Registra productos que salen del almacén</p>
                    </div>
                    <button @click="showSalida = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('inventario.salida') }}" method="POST" id="form-salida">
                    @csrf
                    <div class="space-y-5">
                        {{-- Producto --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Producto <span class="text-red-500">*</span></label>
                            <select name="producto_id" required class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700">
                                <option value="">Seleccionar producto...</option>
                                @foreach($productos as $p)
                                    <option value="{{ $p->id }}">{{ $p->nombre }} (Stock: {{ $p->stock }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- Cantidad --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Cantidad <span class="text-red-500">*</span></label>
                                <input type="number" name="cantidad" min="1" required class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700" placeholder="0">
                            </div>
                            {{-- Motivo --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo <span class="text-red-500">*</span></label>
                                <select name="motivo" required class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700">
                                    <option value="">Seleccionar motivo...</option>
                                    <option value="venta">Venta</option>
                                    <option value="producto_dañado">Producto dañado</option>
                                    <option value="devolucion_proveedor">Devolución a proveedor</option>
                                    <option value="ajuste_manual">Ajuste manual</option>
                                    <option value="transferencia">Transferencia entre sucursales</option>
                                </select>
                            </div>
                        </div>

                        {{-- Fecha --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700">
                        </div>

                        {{-- Observaciones --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Observaciones</label>
                            <textarea name="observaciones" rows="2" class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700" placeholder="Notas adicionales..."></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                        <button type="button" @click="showSalida = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors">Cancelar</button>
                        <button type="button" onclick="confirmarSalida()"
                            class="rounded-xl bg-red-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-600 transition-colors">Registrar Salida</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================  MODAL AJUSTE  ==================== --}}
        <div x-show="showAjuste" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showAjuste = false" style="display:none">

            <div @click.outside="showAjuste = false"
                class="no-scrollbar relative w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 mx-4 max-h-[92vh] lg:p-10">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Ajustar Inventario</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" id="ajuste-subtitle">Modificar stock de producto</p>
                    </div>
                    <button @click="showAjuste = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('inventario.ajuste') }}" method="POST" id="form-ajuste">
                    @csrf
                    <input type="hidden" name="producto_id" id="ajuste-producto-id">

                    <div class="space-y-5">
                        <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-3">
                            <p class="text-sm text-gray-500">Producto</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90" id="ajuste-producto-nombre">—</p>
                        </div>

                        <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-3">
                            <p class="text-sm text-gray-500">Stock Actual</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white/90" id="ajuste-stock-actual">0</p>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Nuevo Stock <span class="text-red-500">*</span></label>
                            <input type="number" name="stock_nuevo" id="ajuste-stock-nuevo" min="0" required class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700" placeholder="0">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Observaciones</label>
                            <textarea name="observaciones" rows="2" class="w-full rounded-xl border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:ring-0 dark:border-gray-700" placeholder="Motivo del ajuste..."></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                        <button type="button" @click="showAjuste = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors">Cancelar</button>
                        <button type="button" onclick="confirmarAjuste()"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">Realizar Ajuste</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function confirmarEntrada() {
                // Close modal first so SweetAlert is visible
                const mainEl = document.getElementById('inventario-root');
                mainEl._x_dataStack[0].showEntrada = false;

                setTimeout(() => {
                    Swal.fire({
                        title: '¿Registrar entrada?',
                        text: 'Se aumentará el stock del producto seleccionado.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, registrar',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('form-entrada').submit();
                        } else {
                            mainEl._x_dataStack[0].showEntrada = true;
                        }
                    });
                }, 200);
            }

            function confirmarSalida() {
                // Close modal first so SweetAlert is visible
                const mainEl = document.getElementById('inventario-root');
                mainEl._x_dataStack[0].showSalida = false;

                setTimeout(() => {
                    Swal.fire({
                        title: '¿Registrar salida?',
                        text: 'Se reducirá el stock del producto seleccionado.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, registrar',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('form-salida').submit();
                        } else {
                            mainEl._x_dataStack[0].showSalida = true;
                        }
                    });
                }, 200);
            }

            function abrirAjuste(id, nombre, stock) {
                document.getElementById('ajuste-producto-id').value = id;
                document.getElementById('ajuste-producto-nombre').textContent = nombre;
                document.getElementById('ajuste-stock-actual').textContent = stock;
                document.getElementById('ajuste-stock-nuevo').value = stock;
                document.getElementById('ajuste-subtitle').textContent = 'Modificar stock de ' + nombre;

                // Alpine.js v3 compatible way to open the modal
                const mainEl = document.getElementById('inventario-root');
                mainEl._x_dataStack[0].showAjuste = true;
            }

            function confirmarAjuste() {
                const stockNuevo = document.getElementById('ajuste-stock-nuevo').value;
                const stockActual = document.getElementById('ajuste-stock-actual').textContent;
                const nombre = document.getElementById('ajuste-producto-nombre').textContent;

                // Close modal first so SweetAlert is visible
                const mainEl = document.getElementById('inventario-root');
                mainEl._x_dataStack[0].showAjuste = false;

                setTimeout(() => {
                    Swal.fire({
                        title: '¿Ajustar inventario?',
                        html: `<p class="text-gray-500">Stock de <strong>${nombre}</strong> cambiará de <strong>${stockActual}</strong> a <strong>${stockNuevo}</strong>.</p>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#465fff',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, ajustar',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('form-ajuste').submit();
                        } else {
                            mainEl._x_dataStack[0].showAjuste = true;
                        }
                    });
                }, 200);
            }
        </script>
    @endpush
@endsection
