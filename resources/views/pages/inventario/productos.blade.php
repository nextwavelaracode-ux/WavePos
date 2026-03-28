@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Productos" />

    <div x-data="{
        showCreate: false,
        showEdit: false,
        showView: false,
        showImport: false,
        editData: {},
        viewData: {},
        searchTerm: '',
        filterCategoria: '',
        filterEstado: '',
        
        openEdit(data) {
            this.editData = data;
            this.showEdit = true;
        },
        openView(data) {
            this.viewData = data;
            this.showView = true;
        },
        generateBarcode(prefix) {
            const num = Math.floor(Math.random() * 1000000000000).toString().padStart(12, '0');
            return prefix === 'create' ? num : num;
        },
        selectedIds: [],
        selectAll: false,
        async bulkDelete(routeUrl) {
            if (this.selectedIds.length === 0) return;
            
            const result = await Swal.fire({
                title: '¿Eliminar registros?',
                html: `<p class='text-gray-500'>Estás a punto de eliminar <strong>${this.selectedIds.length}</strong> registros.<br>Esta acción no se puede deshacer.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(routeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ids: this.selectedIds })
                    });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        window.location.reload();
                    } else {
                        Swal.fire('Atención', data.message || 'No se pudieron eliminar los registros. Verifique que no tengan datos dependientes.', 'warning');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Hubo un problema al eliminar los registros.', 'error');
                }
            }
        }
    }">

        {{-- ===== HEADER / TOOLBAR ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Catálogo de Productos</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona los productos disponibles para la venta en el POS</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                @can('productos.crear')
                <button @click="showImport = true"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Importar
                </button>
                @endcan

                <div x-data="{ openExport: false }" class="relative">
                    <button @click="openExport = !openExport"
                        class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Exportar
                        <svg class="w-4 h-4 transition-transform" :class="openExport ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openExport" @click.outside="openExport = false"
                        class="absolute right-0 mt-2 w-40 origin-top-right rounded-xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50">
                        <div class="p-1">
                            <a href="{{ route('inventario.productos.exportar', 'excel') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2zM14 8V3.5L18.5 8H14z"/></svg>
                                Excel (.xlsx)
                            </a>
                            <a href="{{ route('inventario.productos.exportar', 'pdf') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                                PDF (.pdf)
                            </a>
                        </div>
                    </div>
                </div>

                @can('productos.crear')
                <button @click="showCreate = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Producto
                </button>
                @endcan
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
                                @foreach($todasCategorias as $cat)
                                <li @click="setCategoria('{{ addslashes($cat->nombre) }}')" class="px-4 py-2 text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-gray-700 text-sm cursor-pointer whitespace-nowrap">{{ $cat->nombre }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <input
                        x-model="searchTerm"
                        type="text"
                        class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 dark:text-white text-sm border border-slate-200 dark:border-gray-700 rounded-md pl-32 pr-24 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 dark:hover:border-gray-600 shadow-sm focus:shadow"
                        placeholder="Buscar producto..." />
                    
                    <button
                        class="absolute top-1 right-1 flex items-center rounded bg-slate-800 dark:bg-brand-500 py-1 px-2.5 border border-transparent text-center text-sm text-white transition-all shadow-sm hover:shadow focus:bg-slate-700 dark:focus:bg-brand-600 focus:shadow-none active:bg-slate-700 dark:active:bg-brand-600 hover:bg-slate-700 dark:hover:bg-brand-600 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
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
                <select x-model="filterEstado" class="block w-full rounded-md border border-slate-200 bg-transparent py-2.5 px-3 text-sm text-slate-700 shadow-sm focus:border-slate-400 focus:ring-0 dark:border-gray-700 dark:text-gray-300">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activos</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>
        </div>

        {{-- ===== TABLA ===== --}}
        {{-- ===== BULK ACTIONS BANNER ===== --}}
        <div x-show="selectedIds.length > 0" style="display: none;" 
            class="mb-4 flex items-center justify-between rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 dark:border-brand-500/20 dark:bg-brand-500/10 transition-all">
            <div class="flex items-center gap-2 text-brand-700 dark:text-brand-400 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="`${selectedIds.length} productos seleccionados`"></span>
            </div>
            
            <button type="button" @click="bulkDelete('{{ route('inventario.productos.bulk_destroy') }}')"
                class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-600 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Eliminar Seleccionados
            </button>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
                            <th class="px-6 py-4 w-10 text-center">
                                <input type="checkbox" x-model="selectAll" @change="selectedIds = selectAll ? {{ $productos->pluck('id') }} : []" 
                                    class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:checked:bg-brand-500">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Producto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categoría</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Código</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Precio Venta</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stock</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                        @forelse ($productos as $producto)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors"
                                :class="selectedIds.includes({{ $producto->id }}) ? 'bg-brand-50 dark:bg-brand-500/5' : ''"
                                x-show="(searchTerm === '' || '{{ strtolower($producto->nombre . ' ' . $producto->codigo_barras) }}'.includes(searchTerm.toLowerCase())) &&
                                        (filterCategoria === '' || '{{ optional($producto->categoria)->nombre }}' === filterCategoria) &&
                                        (filterEstado === '' || (filterEstado === 'activo' && {{ $producto->estado ? 'true' : 'false' }}) || (filterEstado === 'inactivo' && {{ !$producto->estado ? 'true' : 'false' }}))"
                            >
                                <td class="px-6 py-4 w-10 text-center">
                                    <input type="checkbox" value="{{ $producto->id }}" x-model="selectedIds" 
                                        class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:checked:bg-brand-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-gray-100 dark:bg-gray-800 overflow-hidden flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                            @if($producto->imagen_url)
                                                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="h-full w-full object-cover">
                                            @else
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white/90">{{ $producto->nombre }}</p>
                                            <p class="text-xs text-gray-500">{{ $producto->sku ?: 'Sin SKU' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                        {{ optional($producto->categoria)->nombre ?? 'Sin Categoria' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs font-mono">
                                    {{ $producto->codigo_barras ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <span class="font-medium text-gray-800 dark:text-white/90">${{ number_format($producto->precio_venta, 2) }}</span>
                                    <p class="text-[10px] text-gray-500 mt-0.5">ITBMS: {{ $producto->impuesto }}%</p>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    @if($producto->stock <= $producto->stock_minimo && $producto->stock > 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-700 dark:bg-orange-500/10 dark:text-orange-400">
                                            {{ $producto->stock }} {{ $producto->unidad_medida }}
                                        </span>
                                    @elseif($producto->stock <= 0)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-500/10 dark:text-red-400">
                                            Agotado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $producto->stock }} <span class="text-xs text-gray-400">{{ $producto->unidad_medida }}</span>
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
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

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Ver --}}
                                        <button type="button"
                                            @click='openView({
                                                id: {{ $producto->id }},
                                                nombre: @json($producto->nombre),
                                                descripcion: @json($producto->descripcion),
                                                categoria: @json(optional($producto->categoria)->nombre),
                                                sku: @json($producto->sku),
                                                codigo_barras: @json($producto->codigo_barras),
                                                precio_compra: @json(number_format($producto->precio_compra, 2)),
                                                precio_venta: @json(number_format($producto->precio_venta, 2)),
                                                margen: @json(number_format($producto->margen, 2)),
                                                impuesto: @json($producto->impuesto),
                                                stock: @json($producto->stock),
                                                stock_minimo: @json($producto->stock_minimo),
                                                unidad_medida: @json($producto->unidad_medida),
                                                ubicacion: @json($producto->ubicacion),
                                                tasa_impuesto: @json($producto->tasa_impuesto),
                                                is_excluded: {{ $producto->is_excluded ? 'true' : 'false' }},
                                                unidad_medida_dian_id: {{ $producto->unidad_medida_dian_id ?? 'null' }},
                                                estado: {{ $producto->estado ? 'true' : 'false' }},
                                                imagen_url: @json($producto->imagen_url)
                                            })'
                                            class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition-colors"
                                            title="Ver detalle">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>

                                        {{-- Editar --}}
                                        @can('productos.editar')
                                        <button type="button"
                                            @click='openEdit({
                                                id: {{ $producto->id }},
                                                nombre: @json($producto->nombre),
                                                descripcion: @json($producto->descripcion),
                                                categoria_id: @json($producto->categoria_id),
                                                subcategoria_id: @json($producto->subcategoria_id),
                                                sku: @json($producto->sku),
                                                codigo_barras: @json($producto->codigo_barras),
                                                precio_compra: @json($producto->precio_compra),
                                                precio_venta: @json($producto->precio_venta),
                                                precio_minimo: @json($producto->precio_minimo),
                                                margen: @json($producto->margen),
                                                impuesto: @json($producto->impuesto),
                                                stock: @json($producto->stock),
                                                stock_minimo: @json($producto->stock_minimo),
                                                stock_maximo: @json($producto->stock_maximo),
                                                unidad_medida: @json($producto->unidad_medida),
                                                ubicacion: @json($producto->ubicacion),
                                                pasillo: @json($producto->pasillo),
                                                estante: @json($producto->estante),
                                                tasa_impuesto: @json($producto->tasa_impuesto),
                                                is_excluded: {{ $producto->is_excluded ? 'true' : 'false' }},
                                                unidad_medida_dian_id: {{ $producto->unidad_medida_dian_id ?? 'null' }},
                                                estado: {{ $producto->estado ? 'true' : 'false' }},
                                                imagen_url: @json($producto->imagen_url)
                                            })'
                                            class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition-colors"
                                            title="Editar">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        @endcan

                                        {{-- Eliminar --}}
                                        @can('productos.eliminar')
                                        <button type="button"
                                            onclick="confirmarEliminar({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')"
                                            class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 dark:border-red-800/30 dark:bg-red-500/10 dark:text-red-400 transition-colors"
                                            title="Eliminar">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-600">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        <p class="text-sm font-medium">No hay productos registrados</p>
                                        <button @click="showCreate = true" class="text-sm text-brand-500 hover:underline">Agregar el primero</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ====================  MODAL CREAR  ==================== --}}
        <div x-show="showCreate" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showCreate = false" style="display:none">

            <div @click.outside="showCreate = false"
                class="no-scrollbar relative w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 mx-4 max-h-[92vh] lg:p-10">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Nuevo Producto</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Registra un nuevo producto en el catálogo</p>
                    </div>
                    <button @click="showCreate = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('inventario.productos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('pages.inventario._form_producto', ['prefix' => 'create'])
                    <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                        <button type="button" @click="showCreate = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors">Cancelar</button>
                        <button type="submit"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================  MODAL EDITAR  ==================== --}}
        <div x-show="showEdit" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showEdit = false" style="display:none">

            <div @click.outside="showEdit = false"
                class="no-scrollbar relative w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 mx-4 max-h-[92vh] lg:p-10">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Editar Producto</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="'Editando ID: ' + editData.id"></p>
                    </div>
                    <button @click="showEdit = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="`/inventario/productos/${editData.id}`" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('pages.inventario._form_producto', ['prefix' => 'edit', 'edit' => true])
                    <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                        <button type="button" @click="showEdit = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors">Cancelar</button>
                        <button type="submit"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">Actualizar Producto</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================  MODAL VER DETALLE  ==================== --}}
        <div x-show="showView" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showView = false" style="display:none">

            <div @click.outside="showView = false"
                class="no-scrollbar relative w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 mx-4 max-h-[90vh] lg:p-8">

                <div class="mb-6 flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <template x-if="viewData.imagen_url">
                                <img :src="viewData.imagen_url" class="h-full w-full object-cover">
                            </template>
                            <template x-if="!viewData.imagen_url">
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </template>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="viewData.nombre"></h3>
                            <span class="text-sm text-gray-500" x-text="viewData.categoria"></span>
                        </div>
                    </div>
                    <button @click="showView = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Precios e ITBMS --}}
                    <div class="space-y-3">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Precios e Impuestos</h4>
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-2.5">
                            <span class="text-sm text-gray-500">Precio Venta</span>
                            <span class="text-sm font-semibold text-brand-600 dark:text-brand-400" x-text="'$' + viewData.precio_venta"></span>
                        </div>
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-2.5">
                            <span class="text-sm text-gray-500">Precio Compra</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="'$' + viewData.precio_compra"></span>
                        </div>
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-2.5">
                            <span class="text-sm text-gray-500">ITBMS</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="viewData.impuesto + '%'"></span>
                        </div>
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-2.5">
                            <span class="text-sm text-gray-500">Margen</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="viewData.margen + '%'"></span>
                        </div>
                    </div>

                    {{-- Inventario e Identificación --}}
                    <div class="space-y-3">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Inventario & ID</h4>
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-2.5">
                            <span class="text-sm text-gray-500">Stock Actual</span>
                            <span class="text-sm font-semibold text-gray-800 dark:text-white/90" x-text="viewData.stock + ' ' + viewData.unidad_medida"></span>
                        </div>
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-2.5">
                            <span class="text-sm text-gray-500">Stock Mínimo</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="viewData.stock_minimo"></span>
                        </div>
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-2.5">
                            <span class="text-sm text-gray-500">Cód. Barras</span>
                            <span class="text-sm font-mono text-gray-800 dark:text-white/90" x-text="viewData.codigo_barras || '—'"></span>
                        </div>
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-2.5">
                            <span class="text-sm text-gray-500">SKU</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="viewData.sku || '—'"></span>
                        </div>
                    </div>
                </div>
                
                <template x-if="viewData.descripcion">
                    <div class="mt-4 rounded-xl bg-gray-50 dark:bg-white/[0.03] px-4 py-3">
                        <span class="text-sm text-gray-500 block mb-1">Descripción</span>
                        <p class="text-sm text-gray-800 dark:text-white/90" x-text="viewData.descripcion"></p>
                    </div>
                </template>

                <div class="mt-8 flex justify-end gap-3">
                    <button @click="showView = false; openEdit(viewData)"
                        class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                        Editar
                    </button>
                </div>
            </div>
        </div>

        {{-- ====================  MODAL IMPORTAR  ==================== --}}
        <div x-show="showImport" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showImport = false" style="display:none">

            <div @click.outside="showImport = false"
                class="no-scrollbar relative w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 mx-4 max-h-[90vh] lg:p-8">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Importar Productos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Carga masiva de productos desde Excel</p>
                    </div>
                    <button @click="showImport = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('inventario.productos.importar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div class="rounded-xl border-2 border-dashed border-gray-200 p-8 text-center dark:border-gray-700">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <label for="archivo_import" class="mt-4 block text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                <span class="text-brand-500 hover:text-brand-600">Selecciona un archivo</span> o arrastra y suelta
                                <input id="archivo_import" name="archivo" type="file" class="sr-only" required accept=".xlsx,.xls,.csv">
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Excel (.xlsx, .xls) o CSV hasta 5MB</p>
                            <div id="file-name-display" class="mt-2 text-sm text-brand-600 font-medium hidden"></div>
                        </div>

                        <div class="rounded-xl bg-blue-50 p-4 dark:bg-blue-500/10">
                            <div class="flex gap-3">
                                <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300">¿No tienes el formato?</h4>
                                    <p class="mt-1 text-xs text-blue-700 dark:text-blue-400">Descarga nuestra plantilla para asegurar que los datos se carguen correctamente.</p>
                                    <a href="{{ route('inventario.productos.plantilla') }}" class="mt-2 inline-flex items-center text-xs font-bold text-blue-800 dark:text-blue-300 hover:underline">
                                        Descargar Plantilla CSV
                                        <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" @click="showImport = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors">Cancelar</button>
                        <button type="submit"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">Iniciar Importación</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('archivo_import')?.addEventListener('change', function(e) {
                    const fileName = e.target.files[0]?.name;
                    const display = document.getElementById('file-name-display');
                    if (fileName && display) {
                        display.textContent = 'Archivo seleccionado: ' + fileName;
                        display.classList.remove('hidden');
                    }
                });
            });
        </script>


        {{-- Formulario oculto para eliminar --}}
        <form id="delete-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

    </div>

    @push('scripts')
        <script>
            function confirmarEliminar(id, nombre) {
                Swal.fire({
                    title: '¿Eliminar producto?',
                    html: `<p class="text-gray-500">Estás a punto de eliminar <strong>${nombre}</strong>.<br>Esta acción no se puede deshacer.</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-form');
                        form.action = `/inventario/productos/${id}`;
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
