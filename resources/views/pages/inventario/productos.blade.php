@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Productos" />

    <div x-data="{
        viewMode: 'scroll',
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
            window.Confirm.show(
                '¿Eliminar registros?',
                `Estás a punto de eliminar ${this.selectedIds.length} registros. Esta acción no se puede deshacer.`,
                'Sí, eliminar',
                'Cancelar',
                async () => {
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
                            window.Notify.warning(data.message || 'No se pudieron eliminar los registros. Verifique que no tengan datos dependientes.', { timeout: 8000 });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        window.Notify.failure('Hubo un problema al eliminar los registros.');
                    }
                },
                () => {},
                { okButtonBackground: '#ef4444' }
            );
        }
    }">

        
        {{-- ===== MAIN CARD WRAPPER ===== --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-neutral-800 dark:bg-neutral-800/20">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                        <div class="flex-1 flex items-center space-x-2">
                            <h5>
                                <span class="text-gray-500">Todos los Productos:</span>
                                <span class="dark:text-white font-medium">{{ $productos->total() ?? $productos->count() }}</span>
                            </h5>
                        </div>
                        <div class="flex-shrink-0 flex flex-col items-start md:flex-row md:items-center lg:justify-end space-y-3 md:space-y-0 md:space-x-3">
                            @can('productos.crear')
                            <button type="button" @click="showImport = true" class="flex-shrink-0 inline-flex items-center justify-center py-2 px-3 text-xs font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-brand-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                Importar Excel
                            </button>
                            @endcan
                            
                            <div x-data="{ openExport: false }" class="relative flex-shrink-0">
                                <button type="button" @click="openExport = !openExport" class="flex-shrink-0 inline-flex items-center justify-center py-2 px-3 text-xs font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-brand-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                    <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    Exportar
                                </button>
                                <div x-show="openExport" @click.outside="openExport = false" style="display:none;" class="absolute right-0 mt-2 w-40 origin-top-right rounded-lg border border-gray-100 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800 z-50 overflow-hidden">
                                    <a href="{{ route('inventario.productos.exportar', 'excel') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">Excel (.xlsx)</a>
                                    <a href="{{ route('inventario.productos.exportar', 'pdf') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">PDF (.pdf)</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Bar --}}
                    <div class="flex flex-col md:flex-row items-stretch md:items-center md:space-x-3 space-y-3 md:space-y-0 justify-between mx-4 py-4 border-t dark:border-gray-700">
                        <div class="w-full md:w-1/2">
                            <form class="flex items-center" submit.prevent>
                                <label for="simple-search" class="sr-only">Search</label>
                                <div class="relative w-full">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" /></svg>
                                    </div>
                                    <input type="text" x-model="searchTerm" id="simple-search" placeholder="Buscar por nombre o código..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500">
                                </div>
                            </form>
                        </div>
                        <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                            @can('productos.crear')
                            <button type="button" @click="showCreate = true" class="flex items-center justify-center text-white bg-brand-600 hover:bg-brand-700 focus:ring-4 focus:ring-brand-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-brand-500 dark:hover:bg-brand-600 focus:outline-none dark:focus:ring-brand-800 transition-colors">
                                <svg class="h-3.5 w-3.5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" /></svg>
                                Agregar Producto
                            </button>
                            @endcan

                            <div x-data="{ openFiltros: false }" class="relative">
                                <button type="button" @click="openFiltros = !openFiltros" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-brand-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 mr-2 -ml-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" /></svg>
                                    Filtros
                                    <svg class="-mr-1 ml-1.5 w-5 h-5 transition-transform" :class="openFiltros ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                                </button>
                                
                                <div x-show="openFiltros" @click.outside="openFiltros = false" style="display:none;" class="z-10 absolute right-0 mt-2 px-3 pt-1 bg-white rounded-lg shadow w-64 dark:bg-gray-700 border border-gray-100 dark:border-gray-600 overflow-hidden">
                                    <div class="flex items-center justify-between pt-2 pb-2 border-b border-gray-200 dark:border-gray-600">
                                        <h6 class="text-sm font-medium text-black dark:text-white">Filtros</h6>
                                        <button @click="filterCategoria = ''; filterEstado = '';" class="text-xs text-brand-600 hover:underline dark:text-brand-500">Limpiar</button>
                                    </div>
                                    <div class="py-3 space-y-3">
                                        <div>
                                            <label class="text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 mb-1 block">Categoría</label>
                                            <select x-model="filterCategoria" class="w-full text-sm border-gray-300 rounded bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-brand-500 focus:border-brand-500 p-2">
                                                <option value="">Todas</option>
                                                @foreach($todasCategorias as $cat)
                                                    <option value="{{ addslashes($cat->nombre) }}">{{ $cat->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 mb-1 block">Estado</label>
                                            <select x-model="filterEstado" class="w-full text-sm border-gray-300 rounded bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-brand-500 focus:border-brand-500 p-2">
                                                <option value="">Todos</option>
                                                <option value="activo">Activos</option>
                                                <option value="inactivo">Inactivos</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Bulk Actions --}}
                            <div x-show="selectedIds.length > 0" style="display: none;" class="flex items-center">
                                <button type="button" @click="bulkDelete('{{ route('inventario.productos.bulk_destroy') }}')" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-red-700 focus:outline-none bg-red-50 rounded-lg border border-red-200 hover:bg-red-100 focus:z-10 focus:ring-4 focus:ring-red-200 dark:focus:ring-red-900 dark:bg-red-900/20 dark:text-red-500 dark:border-red-800 dark:hover:bg-red-900/40 transition-colors">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Eliminar (<span x-text="selectedIds.length"></span>)
                                </button>
                            </div>

                        </div>
                    </div>

                    {{-- Data Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/5 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="p-4 w-4">
                                        <div class="flex items-center">
                                            <input id="checkbox-all" type="checkbox" x-model="selectAll" @change="selectedIds = selectAll ? {{ $productos->pluck('id') }} : []" class="w-4 h-4 text-brand-600 bg-gray-100 rounded border-gray-300 focus:ring-brand-500 dark:focus:ring-brand-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label for="checkbox-all" class="sr-only">checkbox</label>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-4 py-3">Producto</th>
                                    <th scope="col" class="px-4 py-3">Categoría</th>
                                    <th scope="col" class="px-4 py-3 text-center">Código</th>
                                    <th scope="col" class="px-4 py-3 text-right">Stock</th>
                                    <th scope="col" class="px-4 py-3 text-right">Precio</th>
                                    <th scope="col" class="px-4 py-3 text-center">Estado</th>
                                    <th scope="col" class="px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                                @forelse ($productos as $producto)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors"
                                    :class="selectedIds.includes({{ $producto->id }}) ? 'bg-brand-50 dark:bg-brand-900/20' : ''"
                                    x-show="(searchTerm === '' || '{{ strtolower($producto->nombre . ' ' . $producto->codigo_barras) }}'.includes(searchTerm.toLowerCase())) && (filterCategoria === '' || '{{ optional($producto->categoria)->nombre }}' === filterCategoria) && (filterEstado === '' || (filterEstado === 'activo' && {{ $producto->estado ? 'true' : 'false' }}) || (filterEstado === 'inactivo' && {{ !$producto->estado ? 'true' : 'false' }}))"
                                >
                                    <td class="p-4 w-4 relative">
                                        <div class="flex items-center">
                                            <input type="checkbox" value="{{ $producto->id }}" x-model="selectedIds" onclick="event.stopPropagation()" class="w-4 h-4 text-brand-600 bg-gray-100 rounded border-gray-300 focus:ring-brand-500 dark:focus:ring-brand-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                    </td>
                                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="flex items-center mr-3">
                                            @if($producto->imagen_url)
                                                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="h-8 w-8 rounded object-cover mr-3 border border-gray-200 dark:border-gray-600">
                                            @else
                                                <div class="h-8 w-8 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3 border border-gray-200 dark:border-gray-600">
                                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                            <div class="max-w-[200px] sm:max-w-xs md:max-w-sm flex flex-col">
                                                <span class="truncate block" title="{{ $producto->nombre }}">{{ Str::limit($producto->nombre, 50) }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-normal mt-0.5">SKU: {{ $producto->sku ?: '—' }}</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="px-4 py-3">
                                        <span class="bg-brand-100 text-brand-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-brand-900 dark:text-brand-300 whitespace-nowrap">
                                            {{ optional($producto->categoria)->nombre ?? 'Sin Categoría' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono text-xs">
                                        {{ $producto->codigo_barras ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        <div class="flex items-center justify-end">
                                            @if($producto->stock <= $producto->stock_minimo && $producto->stock > 0)
                                                <div class="h-2.5 w-2.5 rounded-full inline-block mr-2 bg-yellow-400" title="Poco Stock"></div>
                                            @elseif($producto->stock <= 0)
                                                <div class="h-2.5 w-2.5 rounded-full inline-block mr-2 bg-red-600" title="Agotado"></div>
                                            @else
                                                <div class="h-2.5 w-2.5 rounded-full inline-block mr-2 bg-green-500" title="Stock Óptimo"></div>
                                            @endif
                                            {{ $producto->stock }} <span class="text-xs text-gray-500 ml-1 font-normal">{{ $producto->unidad_medida }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <p class="font-medium text-gray-900 dark:text-white">${{ number_format($producto->precio_venta, 2) }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($producto->estado)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap inline-flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>Activo</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap inline-flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-gray-500 mr-1.5"></span>Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right relative">
                                        <div class="flex items-center justify-end space-x-1">
                                            {{-- Vista Previa --}}
                                            <button type="button" @click='openView({
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
                                                    estado: {{ $producto->estado ? 'true' : 'false' }},
                                                    imagen_url: @json($producto->imagen_url)
                                                })' 
                                                class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 transition" title="Ver Detalle">
                                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>

                                            {{-- Editar --}}
                                            @can('productos.editar')
                                            <button type="button" @click='openEdit({
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
                                                class="p-2 text-brand-600 rounded-lg hover:text-brand-800 hover:bg-brand-50 dark:text-brand-500 dark:hover:text-white dark:hover:bg-brand-900/50 transition" title="Editar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            @endcan

                                            {{-- Eliminar --}}
                                            @can('productos.eliminar')
                                            <button type="button" onclick="confirmarEliminar({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')" class="p-2 text-red-600 rounded-lg hover:text-red-800 hover:bg-red-50 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-900/50 transition" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            <p class="text-lg font-medium text-gray-900 dark:text-white">No hay productos registrados</p>
                                            <p class="text-sm mt-1 mb-4">Comienza agregando un nuevo producto para tu catálogo.</p>
                                            <button type="button" @click="showCreate = true" class="flex items-center text-brand-600 hover:text-brand-800 dark:text-brand-500 font-medium hover:underline">Agregar nuevo producto</button>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($productos, 'hasPages') && $productos->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-white/5">
                        {{ $productos->links() }}
                    </div>
                    @endif
        </div>
        {{-- ===== END MAIN CARD WRAPPER ===== --}}


        
        {{-- ====================  MODAL CREAR (Drawer) ==================== --}}
        <div x-show="showCreate" style="display:none;" class="relative z-[99999]" aria-labelledby="drawer-create-product-label" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showCreate"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"></div>

            {{-- Drawer Content --}}
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <div x-show="showCreate"
                             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             @click.outside="showCreate = false"
                             @keydown.escape.window="showCreate = false"
                             class="pointer-events-auto w-screen max-w-3xl">
                            <div class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-gray-800 shadow-xl border-l border-gray-200 dark:border-gray-700">
                                
                                <div class="px-4 py-4 sm:px-6 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10 flex items-center justify-between">
                                    <h5 id="drawer-create-product-label" class="inline-flex items-center text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                                        Nuevo Producto
                                    </h5>
                                    <button type="button" @click="showCreate = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        <span class="sr-only">Close menu</span>
                                    </button>
                                </div>
                                <div class="relative flex-1 px-4 py-6 sm:px-6">
                                    <form action="{{ route('inventario.productos.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @include('pages.inventario._form_producto', ['prefix' => 'create'])
                                        <div class="mt-8 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                                            <button type="button" @click="showCreate = false" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-brand-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancelar</button>
                                            <button type="submit" class="text-white bg-brand-700 hover:bg-brand-800 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand-600 dark:hover:bg-brand-700 dark:focus:ring-brand-800">Guardar Producto</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================  MODAL EDITAR (Drawer) ==================== --}}
        <div x-show="showEdit" style="display:none;" class="relative z-[99999]" aria-labelledby="drawer-edit-product-label" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showEdit"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"></div>

            {{-- Drawer Content --}}
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <div x-show="showEdit"
                             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             @click.outside="showEdit = false"
                             @keydown.escape.window="showEdit = false"
                             class="pointer-events-auto w-screen max-w-3xl">
                            <div class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-gray-800 shadow-xl border-l border-gray-200 dark:border-gray-700">
                                
                                <div class="px-4 py-4 sm:px-6 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10 flex items-center justify-between">
                                    <h5 id="drawer-edit-product-label" class="inline-flex items-center text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                                        Editar Producto <span class="ml-2 px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 lowercase text-xs" x-text="editData.sku"></span>
                                    </h5>
                                    <button type="button" @click="showEdit = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                                <div class="relative flex-1 px-4 py-6 sm:px-6">
                                    <form :action="`/inventario/productos/${editData.id}`" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        @include('pages.inventario._form_producto', ['prefix' => 'edit', 'edit' => true])
                                        <div class="mt-8 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                                            <button type="button" @click="showEdit = false" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-brand-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancelar</button>
                                            <button type="submit" class="text-white bg-brand-700 hover:bg-brand-800 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand-600 dark:hover:bg-brand-700 dark:focus:ring-brand-800">Actualizar Producto</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================  MODAL VER DETALLE (Drawer) ==================== --}}
        <div x-show="showView" style="display:none;" class="relative z-[99999]" aria-labelledby="drawer-read-product-label" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showView"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"></div>

            {{-- Drawer Content --}}
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <div x-show="showView"
                             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             @click.outside="showView = false"
                             @keydown.escape.window="showView = false"
                             class="pointer-events-auto w-screen max-w-md">
                            <div class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-gray-800 shadow-xl border-l border-gray-200 dark:border-gray-700">
                                
                                <div class="px-4 py-4 sm:px-6 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10 flex items-center justify-between">
                                    <h5 id="drawer-read-product-label" class="inline-flex items-center text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                                        Detalle del Producto
                                    </h5>
                                    <button type="button" @click="showView = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                                <div class="relative flex-1 px-4 py-6 sm:px-6">

                                    <div class="mb-4 flex items-center gap-4">
                                        <div class="flex h-20 w-20 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
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
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1 leading-tight" x-text="viewData.nombre"></h3>
                                            <span class="bg-brand-100 text-brand-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-brand-200 dark:text-brand-800" x-text="viewData.categoria"></span>
                                        </div>
                                    </div>

                                    <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700">
                                        <div class="flex flex-col pb-3">
                                            <dt class="mb-1 text-gray-500 md:text-sm dark:text-gray-400">Descripción</dt>
                                            <dd class="text-sm font-semibold" x-text="viewData.descripcion || 'Sin descripción'"></dd>
                                        </div>
                                        <div class="flex flex-col py-3">
                                            <dt class="mb-1 text-gray-500 md:text-sm dark:text-gray-400">Código de Barras / SKU</dt>
                                            <dd class="text-sm font-mono font-semibold" x-text="(viewData.codigo_barras || '—') + ' / ' + (viewData.sku || '—')"></dd>
                                        </div>
                                        <div class="flex flex-col py-3">
                                            <dt class="mb-1 text-gray-500 md:text-sm dark:text-gray-400">Precios</dt>
                                            <dd class="text-lg font-bold text-brand-600 dark:text-brand-500" x-text="'$' + viewData.precio_venta"></dd>
                                            <dd class="text-xs text-gray-500 dark:text-gray-400" x-text="'Costo: $' + viewData.precio_compra + ' | Margen: ' + viewData.margen + '%'"></dd>
                                        </div>
                                        <div class="flex flex-col py-3">
                                            <dt class="mb-1 text-gray-500 md:text-sm dark:text-gray-400">Inventario</dt>
                                            <dd class="text-sm font-semibold" x-text="viewData.stock + ' ' + viewData.unidad_medida + ' (Min: ' + viewData.stock_minimo + ')'"></dd>
                                        </div>
                                        <div class="flex flex-col pt-3">
                                            <dt class="mb-1 text-gray-500 md:text-sm dark:text-gray-400">Estado</dt>
                                            <dd class="text-sm font-semibold">
                                                <span x-show="viewData.estado" class="text-green-600 dark:text-green-500">Activo</span>
                                                <span x-show="!viewData.estado" class="text-gray-600 dark:text-gray-400">Inactivo</span>
                                            </dd>
                                        </div>
                                    </dl>

                                </div>
                                <div class="px-4 py-4 sm:px-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 sticky bottom-0 z-10">
                                    <div class="flex items-center space-x-3">
                                        <button type="button" @click="showView = false; openEdit(viewData)" class="text-white bg-brand-700 hover:bg-brand-800 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand-600 dark:hover:bg-brand-700 dark:focus:ring-brand-800 flex items-center w-full justify-center">
                                            <svg aria-hidden="true" class="mr-1 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                                            Editar Producto
                                        </button>
                                        <button type="button" @click="confirmarEliminar(viewData.id, viewData.nombre)" class="inline-flex items-center text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">
                                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================  MODAL IMPORTAR  ==================== --}}
        <div x-show="showImport" style="display:none;" class="relative z-[99999]" aria-labelledby="modal-import-product-label" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="showImport"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"></div>

            {{-- Modal Content --}}
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showImport"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         @click.outside="showImport = false"
                         @keydown.escape.window="showImport = false"
                         class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-gray-700">
                        
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 id="modal-import-product-label" class="text-xl font-semibold text-gray-900 dark:text-white">
                                Importar Productos
                            </h3>
                            <button type="button" @click="showImport = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <div class="p-6 space-y-6">
                            <form action="{{ route('inventario.productos.importar') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="flex items-center justify-center w-full mb-4">
                                    <label for="archivo_import" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/></svg>
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold text-brand-600">Click para subir</span> o arrastra y suelta</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Excel (.xlsx, .xls) o CSV</p>
                                        </div>
                                        <input id="archivo_import" name="archivo" type="file" class="hidden" required accept=".xlsx,.xls,.csv" />
                                    </label>
                                </div>
                                
                                <div class="flex items-center p-4 mb-4 text-sm text-blue-800 border border-blue-300 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-800" role="alert">
                                    <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                                    <span class="sr-only">Info</span>
                                    <div>
                                        ¿No tienes la plantilla? <a href="{{ route('inventario.productos.plantilla') }}" class="font-medium underline hover:text-blue-900 dark:hover:text-blue-300">Descarga la plantilla aquí</a>.
                                    </div>
                                </div>

                                <div class="flex items-center justify-end space-x-2 border-t border-gray-200 dark:border-gray-600 rounded-b pt-4 mt-4">
                                    <button type="button" @click="showImport = false" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-brand-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancelar</button>
                                    <button type="submit" class="text-white bg-brand-700 hover:bg-brand-800 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand-600 dark:hover:bg-brand-700 dark:focus:ring-brand-800">Subir e Importar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-neutral-800">
                        <button type="button" @click="showImport = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 transition-colors">Cancelar</button>
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
                window.Confirm.show(
                    '¿Eliminar producto?',
                    `Estás a punto de eliminar "${nombre}". Esta acción no se puede deshacer.`,
                    'Sí, eliminar',
                    'Cancelar',
                    () => {
                        const form = document.getElementById('delete-form');
                        form.action = `/inventario/productos/${id}`;
                        form.submit();
                    },
                    () => {},
                    { okButtonBackground: '#ef4444' }
                );
            }
        </script>
    @endpush
@endsection
