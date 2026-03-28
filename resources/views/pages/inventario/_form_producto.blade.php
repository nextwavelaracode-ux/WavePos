<div class="space-y-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        {{-- Nombre --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Nombre del Producto <span class="text-red-500">*</span></label>
            <input type="text" name="nombre" required
                @if(isset($edit) && $edit) x-model="editData.nombre" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90"
                placeholder="Ej. Lata de Soda">
        </div>

        {{-- Categoría y Subcategoría --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Categoría <span class="text-red-500">*</span></label>
            <select name="categoria_id" required
                @if(isset($edit) && $edit) x-model="editData.categoria_id" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                <option value="">Selecciona...</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Subcategoría (Opcional)</label>
            <select name="subcategoria_id"
                @if(isset($edit) && $edit) x-model="editData.subcategoria_id" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                <option value="">Ninguna</option>
                @foreach($todasCategorias->whereNotNull('parent_id') as $sub)
                    <option value="{{ $sub->id }}">{{ $sub->nombre }} (De: {{ optional($sub->parent)->nombre }})</option>
                @endforeach
            </select>
        </div>

        {{-- Precios --}}
        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Precio de Compra <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">$</span>
                    <input type="number" step="0.01" name="precio_compra" id="precio_compra_{{ $prefix }}" required 
                        @if(isset($edit) && $edit) x-model="editData.precio_compra" @endif oninput="calcularMargen_{{ $prefix }}()"
                        class="w-full rounded-xl border border-gray-300 bg-transparent pl-10 pr-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Precio de Venta <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">$</span>
                    <input type="number" step="0.01" name="precio_venta" id="precio_venta_{{ $prefix }}" required 
                        @if(isset($edit) && $edit) x-model="editData.precio_venta" @endif oninput="calcularMargen_{{ $prefix }}()"
                        class="w-full rounded-xl border border-gray-300 bg-transparent pl-10 pr-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                </div>
            </div>
            
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Precio Min. Venta</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">$</span>
                    <input type="number" step="0.01" name="precio_minimo"
                        @if(isset($edit) && $edit) x-model="editData.precio_minimo" @else value="0" @endif
                        class="w-full rounded-xl border border-gray-300 bg-transparent pl-10 pr-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Margen (%)</label>
                <input type="number" step="0.01" name="margen" id="margen_{{ $prefix }}" readonly
                    @if(isset($edit) && $edit) x-model="editData.margen" @endif
                    class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-600 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800/50 dark:text-gray-400">
            </div>
        </div>

        <script>
            function calcularMargen_{{ $prefix }}() {
                const compra = parseFloat(document.getElementById('precio_compra_{{ $prefix }}').value) || 0;
                const venta = parseFloat(document.getElementById('precio_venta_{{ $prefix }}').value) || 0;
                const margenInput = document.getElementById('margen_{{ $prefix }}');
                if(compra > 0) {
                    margenInput.value = (((venta - compra) / compra) * 100).toFixed(2);
                    // Disparar evento de input para que Alpine lo detecte
                    margenInput.dispatchEvent(new Event('input', { bubbles: true }));
                } else {
                    margenInput.value = 0;
                    margenInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        </script>

        {{-- Identificación --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">SKU</label>
            <input type="text" name="sku" placeholder="Código interno"
                @if(isset($edit) && $edit) x-model="editData.sku" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Cód. Barras</label>
            <div class="flex">
                <input type="text" name="codigo_barras" id="codigo_barras_{{ $prefix }}"
                    @if(isset($edit) && $edit) x-model="editData.codigo_barras" @endif
                    class="w-full rounded-l-xl border border-gray-300 border-r-0 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                <button type="button" @click="document.getElementById('codigo_barras_{{ $prefix }}').value = generateBarcode('{{ $prefix }}')" 
                    class="inline-flex items-center rounded-r-xl border border-gray-300 bg-gray-50 px-4 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </button>
            </div>
        </div>

        {{-- Fiscal y Cantidad --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">ITBMS <span class="text-red-500">*</span></label>
            <select name="impuesto" required
                @if(isset($edit) && $edit) x-model="editData.impuesto" @else value="7" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                <option value="7">7% (General)</option>
                <option value="10">10% (Licores)</option>
                <option value="15">15% (Tabaco)</option>
                <option value="0">0% (Exento)</option>
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Unidad de Medida <span class="text-red-500">*</span></label>
            <select name="unidad_medida" required
                @if(isset($edit) && $edit) x-model="editData.unidad_medida" @else value="Unidad (Und)" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
                <option value="">Seleccione...</option>
                <option value="Global (Par/Set)">Global (Par/Set) - Guantes, juegos</option>
                <option value="Longitud (Pie/Metro)">Longitud (Pie/Metro) - Cables, tubos</option>
                <option value="Peso (Lb/Kg)">Peso (Lb/Kg) - Clavos, alambre</option>
                <option value="Volumen (Galón/Cuñete)">Volumen (Galón/Cuñete) - Pinturas</option>
                <option value="Litro (Ltr)">Litro (Ltr) - Líquidos, aceites</option>
                <option value="Paquete (Pqte)">Paquete (Pqte) - Tornillos, empaques</option>
                <option value="Unidad (Und)">Unidad (Und) - Piezas individuales</option>
                <option value="Otro">Otro/Vario</option>
            </select>
        </div>

        {{-- ==================== DATOS DIAN PRODUCTO ==================== --}}
        <div class="sm:col-span-2 mt-4 mb-2">
            <h4 class="text-md font-bold text-gray-700 dark:text-gray-300 border-b pb-2 dark:border-gray-700">Información Fiscal (DIAN)</h4>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Tasa de Impuesto (%) DIAN</label>
            <input type="number" step="0.01" name="tasa_impuesto"
                @if(isset($edit) && $edit) x-model="editData.tasa_impuesto" @else value="19.00" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90"
                placeholder="Ej. 19.00">
        </div>

        <div>
            <label class="flex items-center gap-3 cursor-pointer w-max mt-8">
                <div class="relative">
                    <input type="hidden" name="is_excluded" value="0">
                    <input type="checkbox" name="is_excluded" value="1" class="sr-only peer"
                        @if(isset($edit) && $edit) :checked="editData.is_excluded" @change="editData.is_excluded = $event.target.checked" @endif>
                    <div class="block h-6 w-10 rounded-full bg-gray-300 dark:bg-gray-700 peer-checked:bg-brand-500 transition"></div>
                    <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-full"></div>
                </div>
                <span class="text-sm font-medium text-gray-800 dark:text-white/90">Producto Exento/Excluido (Sin IVA)</span>
            </label>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">ID Unidad de Medida (Factus)</label>
            <input type="number" name="unidad_medida_dian_id"
                @if(isset($edit) && $edit) x-model="editData.unidad_medida_dian_id" @else value="70" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90"
                placeholder="Ej. 70 (Unidad)">
            <p class="text-xs text-gray-500 mt-1">Busque el código exacto de la tabla de Factus (ej. 70 = Unidad Libre, 94 = 100 Unds)</p>
        </div>
        
        <div class="sm:col-span-2 border-b border-gray-100 dark:border-gray-800 mt-2 mb-4"></div>
        {{-- ============================================================= --}}

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Stock Inicial <span class="text-red-500">*</span></label>
            <input type="number" name="stock" required min="0"
                @if(isset($edit) && $edit) x-model="editData.stock" @else value="0" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Stock Mínimo</label>
            <input type="number" name="stock_minimo" min="0" placeholder="Ej: 10"
                @if(isset($edit) && $edit) x-model="editData.stock_minimo" @else value="0" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
        </div>

        {{-- Ubicación --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Ubicación (Almacén)</label>
            <input type="text" name="ubicacion" placeholder="Ej: Pasillo 3, Estante B"
                @if(isset($edit) && $edit) x-model="editData.ubicacion" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90">
        </div>

        {{-- Descripción --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Descripción</label>
            <textarea name="descripcion" rows="2"
                @if(isset($edit) && $edit) x-model="editData.descripcion" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90"></textarea>
        </div>

        {{-- Imagen --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Imagen del Producto</label>
            <input type="file" name="imagen" accept="image/*"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 dark:border-gray-700 dark:file:bg-brand-500/10 dark:file:text-brand-400 transition-colors">
            @if(isset($edit) && $edit)
                <div class="mt-3" x-show="editData.imagen_url">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Imagen actual:</p>
                    <img :src="editData.imagen_url" class="h-20 w-20 rounded-xl object-cover border border-gray-200 dark:border-gray-700">
                </div>
            @endif
        </div>

        {{-- Estado --}}
        <div class="sm:col-span-2">
            <input type="hidden" name="estado" value="0">
            <label class="flex items-center gap-3 cursor-pointer w-max">
                <div class="relative">
                    <input type="checkbox" name="estado" value="1" class="sr-only peer"
                        @if(isset($edit) && $edit) :checked="editData.estado" @change="editData.estado = $event.target.checked" @else checked @endif>
                    <div class="block h-6 w-10 rounded-full bg-gray-300 dark:bg-gray-700 peer-checked:bg-brand-500 transition"></div>
                    <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-full"></div>
                </div>
                <span class="text-sm font-medium text-gray-800 dark:text-white/90">Estado Activo</span>
            </label>
            <p class="text-xs text-gray-500 mt-1 pl-13">Si está inactivo, no aparecerá en el POS.</p>
        </div>
    </div>
</div>
