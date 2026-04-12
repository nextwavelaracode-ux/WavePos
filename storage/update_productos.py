import re

with open('c:/Users/jjdia/Downloads/NextWave/WavePos/resources/views/pages/inventario/productos.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add viewMode to Alpine state
content = re.sub(
    r'(showCreate: false,)',
    r"viewMode: 'scroll',\n        \1",
    content
)

# 2. Add Switcher to Title Header
# We'll put it right after the title:
header_replacement = """        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white/90">Catálogo de Productos</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Gestiona los productos disponibles para la venta</p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <!-- Mode Switcher -->
                <div class="relative flex gap-1 bg-zinc-100 dark:bg-zinc-800/50 p-1 border border-zinc-200 dark:border-white/10 rounded-lg mr-2">
                    <div class="absolute top-1 left-1 bottom-1 w-8 rounded-md bg-white dark:bg-zinc-700 shadow-sm transition-transform duration-300 ease-out"
                        :class="{'translate-x-0': viewMode === 'scroll', 'translate-x-[36px]': viewMode === 'cards', 'translate-x-[72px]': viewMode === 'stack'}"></div>
                    <button @click="viewMode = 'scroll'" class="relative z-10 flex h-7 w-8 items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors" :class="viewMode === 'scroll' ? '!text-brand-600 dark:!text-brand-400' : ''" title="Vista Tabla">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <button @click="viewMode = 'cards'" class="relative z-10 flex h-7 w-8 items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors" :class="viewMode === 'cards' ? '!text-brand-600 dark:!text-brand-400' : ''" title="Vista Cuadrícula">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </button>
                    <button @click="viewMode = 'stack'" class="relative z-10 flex h-7 w-8 items-center justify-center text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors" :class="viewMode === 'stack' ? '!text-brand-600 dark:!text-brand-400' : ''" title="Vista Lista">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                    </button>
                </div>
"""

content = re.sub(
    r'        \{\{-- ===== HEADER / TOOLBAR ===== --\}\}\n.*?<div class="flex flex-wrap items-center gap-3">',
    "        {{-- ===== MAIN CARD WRAPPER ===== --}}\n        <div class=\"rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-[#1e1e1e] transition-colors duration-500 overflow-hidden\">\n        <div class=\"p-6\">\n        {{-- ===== HEADER / TOOLBAR ===== --}}\n" + header_replacement + '\n            <div class="flex flex-wrap items-center gap-2">',
    content,
    flags=re.DOTALL
)

# Fix inner spacing
content = content.replace('mb-6 flex flex-col', 'pb-5 border-b border-gray-100 dark:border-white/10 mb-5 flex flex-col')
content = content.replace('<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900">', '<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900/50" x-show="viewMode === \'scroll\'">')

# Create the cards view and stack view replacing table
cards_view = """
        <div x-show="viewMode === 'cards'" style="display: none;">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse ($productos as $producto)
                    <div class="group flex flex-col rounded-xl border border-gray-200 bg-white dark:border-white/[0.05] dark:bg-[#1e1e1e] hover:border-brand-300 dark:hover:border-brand-500/30 hover:shadow-md transition-all overflow-hidden" 
                        x-show="(searchTerm === '' || '{{ strtolower($producto->nombre . ' ' . $producto->codigo_barras) }}'.includes(searchTerm.toLowerCase())) &&
                                (filterCategoria === '' || '{{ optional($producto->categoria)->nombre }}' === filterCategoria) &&
                                (filterEstado === '' || (filterEstado === 'activo' && {{ $producto->estado ? 'true' : 'false' }}) || (filterEstado === 'inactivo' && {{ !$producto->estado ? 'true' : 'false' }}))">
                        
                        <div class="relative h-40 bg-gray-100 dark:bg-gray-800">
                            @if($producto->imagen_url)
                                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <div class="absolute top-2 right-2 flex gap-1">
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
                                                tasa_impuesto: @json($producto->tasa_impuesto),
                                                is_excluded: {{ $producto->is_excluded ? 'true' : 'false' }},
                                                unidad_medida_dian_id: {{ $producto->unidad_medida_dian_id ?? 'null' }},
                                                estado: {{ $producto->estado ? 'true' : 'false' }},
                                                imagen_url: @json($producto->imagen_url)
                                            })' class="rounded-full bg-white/50 dark:bg-black/50 p-1.5 text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-black transition backdrop-blur-md">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                @if ($producto->estado)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/90 px-2 py-0.5 text-[10px] font-semibold text-white shadow-sm backdrop-blur-sm">Activo</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-500/90 px-2 py-0.5 text-[10px] font-semibold text-white shadow-sm backdrop-blur-sm">Inactivo</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex flex-col flex-1 p-4">
                            <div class="mb-2 flex items-center gap-2">
                                <span class="rounded bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                                    {{ optional($producto->categoria)->nombre ?? 'Sin Categoria' }}
                                </span>
                                @if($producto->stock <= $producto->stock_minimo && $producto->stock > 0)
                                    <span class="rounded bg-orange-50 px-2 py-0.5 text-[10px] font-semibold text-orange-700 dark:bg-orange-500/10 dark:text-orange-400">Poco stock</span>
                                @elseif($producto->stock <= 0)
                                    <span class="rounded bg-red-50 px-2 py-0.5 text-[10px] font-semibold text-red-700 dark:bg-red-500/10 dark:text-red-400">Agotado</span>
                                @endif
                            </div>
                            
                            <h3 class="font-bold text-gray-800 dark:text-white/90 leading-tight mb-1" title="{{ $producto->nombre }}">{{ Str::limit($producto->nombre, 45) }}</h3>
                            <p class="text-xs text-gray-500 mb-3">{{ $producto->sku ?: 'Sin SKU' }}</p>
                            
                            <div class="mt-auto grid grid-cols-2 gap-2 text-sm pt-3 border-t border-gray-100 dark:border-white/5">
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-semibold">Precio</p>
                                    <p class="font-bold text-brand-600 dark:text-brand-400">${{ number_format($producto->precio_venta, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-400 uppercase font-semibold">Stock</p>
                                    <p class="font-medium text-gray-700 dark:text-gray-300">{{ $producto->stock }} <span class="text-[10px] text-gray-400">{{ $producto->unidad_medida }}</span></p>
                                </div>
                            </div>
                            
                            @can('productos.editar')
                            <div class="mt-4 flex items-center justify-between gap-2 pt-2">
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
                                            })' class="w-full rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 py-1.5 px-3 text-xs font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-700 hover:border-brand-300 focus:ring-1 focus:ring-brand-500">Editar</button>
                            </div>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-4 p-12 text-center text-gray-400">No hay productos registrados.</div>
                @endforelse
            </div>
        </div>
        
        <div x-show="viewMode === 'stack'" style="display: none;">
            <div class="flex flex-col gap-3">
                @forelse ($productos as $producto)
                    <div class="group flex flex-col sm:flex-row gap-4 rounded-xl border border-gray-200 bg-white dark:border-white/[0.05] dark:bg-[#1e1e1e] hover:border-brand-300 hover:bg-brand-50/20 dark:hover:border-brand-500/30 dark:hover:bg-black/20 transition p-3 items-center"
                        x-show="(searchTerm === '' || '{{ strtolower($producto->nombre . ' ' . $producto->codigo_barras) }}'.includes(searchTerm.toLowerCase())) &&
                                (filterCategoria === '' || '{{ optional($producto->categoria)->nombre }}' === filterCategoria) &&
                                (filterEstado === '' || (filterEstado === 'activo' && {{ $producto->estado ? 'true' : 'false' }}) || (filterEstado === 'inactivo' && {{ !$producto->estado ? 'true' : 'false' }}))">
                        
                        <div class="h-14 w-14 flex-shrink-0 rounded-lg bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            @if($producto->imagen_url)
                                <img src="{{ $producto->imagen_url }}" class="h-full w-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-800 dark:text-white/90 truncate">{{ $producto->nombre }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="rounded bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">{{ optional($producto->categoria)->nombre ?? 'Sin Categoria' }}</span>
                                <span class="text-[10px] text-gray-500">{{ $producto->codigo_barras ?: $producto->sku }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-6 md:gap-12 px-4 sm:border-l border-gray-100 dark:border-white/10 sm:pl-6 w-full sm:w-auto mt-2 sm:mt-0 justify-between sm:justify-start">
                            <div class="text-left sm:text-right">
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Precio</p>
                                <p class="font-bold text-gray-900 dark:text-white">${{ number_format($producto->precio_venta, 2) }}</p>
                            </div>
                            <div class="text-right w-16">
                                <p class="text-[10px] text-gray-400 uppercase font-bold">Stock</p>
                                <p class="font-medium text-gray-700 dark:text-gray-300">{{ $producto->stock }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 sm:pl-4 sm:border-l border-gray-100 dark:border-white/10 ml-auto sm:ml-0">
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
                                        })' class="rounded-lg border border-gray-200 dark:border-gray-700 p-2 text-gray-400 hover:text-brand-600 hover:border-brand-200 dark:hover:border-brand-500/50 hover:bg-white dark:hover:bg-gray-800 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400">No hay productos registrados.</div>
                @endforelse
            </div>
        </div>
"""

# Find the end of the table div to insert the other views
# and close the card wrapper around the end.
content = re.sub(
    r'(        </div>\n\n        \{\{-- ====================  MODAL CREAR  ==================== --\}\})',
    cards_view + r"\n        </div> <!-- end main card wrapper inner padding -->\n        <div class=\"border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-gray-900/50 px-6 py-4\">\n            <p class=\"text-xs text-gray-500 flex items-center justify-between\">\n                <span>Actualizado al {{ date('M Y') }}</span>\n                <span class=\"font-medium\">{{ $productos->count() }} modelos y variantes</span>\n            </p>\n        </div>\n        </div> <!-- end main card wrapper -->\n\n\1",
    content
)

# And remove mb-6 from the filters to fix spacing
content = content.replace('mb-6 flex flex-col gap-4', 'mb-5 flex flex-col gap-4')


with open('c:/Users/jjdia/Downloads/NextWave/WavePos/resources/views/pages/inventario/productos.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated productos.blade.php")
