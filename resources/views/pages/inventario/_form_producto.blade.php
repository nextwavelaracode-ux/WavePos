<div class="space-y-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        {{-- Nombre --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Nombre del Producto <span class="text-red-500">*</span></label>
            <input type="text" name="nombre" required
                @if(isset($edit) && $edit) x-model="editData.nombre" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. Lata de Soda">
        </div>

        {{-- Categoría y Subcategoría --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Categoría <span class="text-red-500">*</span></label>
            <select name="categoria_id" required
                @if(isset($edit) && $edit) x-model="editData.categoria_id" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
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
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
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
                        class="w-full rounded-xl border border-gray-300 bg-transparent pl-10 pr-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Precio de Venta <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">$</span>
                    <input type="number" step="0.01" name="precio_venta" id="precio_venta_{{ $prefix }}" required 
                        @if(isset($edit) && $edit) x-model="editData.precio_venta" @endif oninput="calcularMargen_{{ $prefix }}()"
                        class="w-full rounded-xl border border-gray-300 bg-transparent pl-10 pr-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
                </div>
            </div>
            
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Precio Min. Venta</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">$</span>
                    <input type="number" step="0.01" name="precio_minimo"
                        @if(isset($edit) && $edit) x-model="editData.precio_minimo" @else value="0" @endif
                        class="w-full rounded-xl border border-gray-300 bg-transparent pl-10 pr-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Margen (%)</label>
                <input type="number" step="0.01" name="margen" id="margen_{{ $prefix }}" readonly
                    @if(isset($edit) && $edit) x-model="editData.margen" @endif
                    class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm text-gray-600 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:bg-neutral-800/50 dark:text-gray-400">
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
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Cód. Barras</label>
            <div class="flex gap-0">
                <input type="text" name="codigo_barras" id="codigo_barras_{{ $prefix }}"
                    @if(isset($edit) && $edit) x-model="editData.codigo_barras" @endif
                    placeholder="Escanea o escribe el código"
                    onchange="buscarDatosAPIProducto(this.value, '{{ $prefix }}')"
                    class="w-full rounded-l-xl border border-gray-300 border-r-0 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">

                {{-- Botón: Escanear con cámara --}}
                <button type="button"
                    onclick="abrirScannerProducto('{{ $prefix }}')"
                    title="Escanear con cámara"
                    class="inline-flex items-center border border-gray-300 border-r-0 bg-brand-50 px-3 text-brand-600 hover:bg-brand-100 dark:border-neutral-700 dark:bg-brand-500/10 dark:text-brand-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </button>

                {{-- Botón: Generar código interno --}}
                <button type="button" @click="document.getElementById('codigo_barras_{{ $prefix }}').value = generateBarcode('{{ $prefix }}')"
                    title="Generar código interno"
                    class="inline-flex items-center rounded-r-xl border border-gray-300 bg-gray-50 px-4 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 dark:hover:bg-neutral-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </button>
            </div>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">📷 Usa la cámara o conecta un scanner físico para escanear directamente.</p>
        </div>

        {{-- Fiscal y Cantidad --}}

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">ITBMS <span class="text-red-500">*</span></label>
            <select name="impuesto" required
                @if(isset($edit) && $edit) x-model="editData.impuesto" @else value="7" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
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
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
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
            <h4 class="text-md font-bold text-gray-700 dark:text-gray-300 border-b pb-2 dark:border-neutral-700">Información Fiscal (DIAN)</h4>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Tasa de Impuesto (%) DIAN</label>
            <input type="number" step="0.01" name="tasa_impuesto"
                @if(isset($edit) && $edit) x-model="editData.tasa_impuesto" @else value="19.00" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. 19.00">
        </div>

        <div>
            <label class="flex items-center gap-3 cursor-pointer w-max mt-8">
                <div class="relative">
                    <input type="hidden" name="is_excluded" value="0">
                    <input type="checkbox" name="is_excluded" value="1" class="sr-only peer"
                        @if(isset($edit) && $edit) :checked="editData.is_excluded" @change="editData.is_excluded = $event.target.checked" @endif>
                    <div class="block h-6 w-10 rounded-full bg-gray-300 dark:bg-neutral-700 peer-checked:bg-brand-500 transition"></div>
                    <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-full"></div>
                </div>
                <span class="text-sm font-medium text-gray-800 dark:text-white/90">Producto Exento/Excluido (Sin IVA)</span>
            </label>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">ID Unidad de Medida (Factus)</label>
            <input type="number" name="unidad_medida_dian_id"
                @if(isset($edit) && $edit) x-model="editData.unidad_medida_dian_id" @else value="70" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. 70 (Unidad)">
            <p class="text-xs text-gray-500 mt-1">Busque el código exacto de la tabla de Factus (ej. 70 = Unidad Libre, 94 = 100 Unds)</p>
        </div>
        
        <div class="sm:col-span-2 border-b border-gray-100 dark:border-neutral-800 mt-2 mb-4"></div>
        {{-- ============================================================= --}}

        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Stock Inicial <span class="text-red-500">*</span></label>
            <input type="number" name="stock" required min="0"
                @if(isset($edit) && $edit) x-model="editData.stock" @else value="0" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Stock Mínimo</label>
            <input type="number" name="stock_minimo" min="0" placeholder="Ej: 10"
                @if(isset($edit) && $edit) x-model="editData.stock_minimo" @else value="0" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
        </div>

        {{-- Ubicación --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Ubicación (Almacén)</label>
            <input type="text" name="ubicacion" placeholder="Ej: Pasillo 3, Estante B"
                @if(isset($edit) && $edit) x-model="editData.ubicacion" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
        </div>

        {{-- Descripción --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Descripción</label>
            <textarea name="descripcion" rows="2"
                @if(isset($edit) && $edit) x-model="editData.descripcion" @endif
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"></textarea>
        </div>

        {{-- Imagen --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Imagen del Producto</label>
            <input type="file" name="imagen" accept="image/*"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 dark:border-neutral-700 dark:file:bg-brand-500/10 dark:file:text-brand-400 transition-colors">
            @if(isset($edit) && $edit)
                <div class="mt-3" x-show="editData.imagen_url">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Imagen actual:</p>
                    <img :src="editData.imagen_url" class="h-20 w-20 rounded-xl object-cover border border-gray-200 dark:border-neutral-700">
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
                    <div class="block h-6 w-10 rounded-full bg-gray-300 dark:bg-neutral-700 peer-checked:bg-brand-500 transition"></div>
                    <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-full"></div>
                </div>
                <span class="text-sm font-medium text-gray-800 dark:text-white/90">Estado Activo</span>
            </label>
            <p class="text-xs text-gray-500 mt-1 pl-13">Si está inactivo, no aparecerá en el POS.</p>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL SCANNER DE CÁMARA — Módulo Productos
     Cargado una sola vez, reutilizable por prefix
═══════════════════════════════════════════════════════ --}}
@once
<div id="modalScannerProducto"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm hidden"
     onclick="if(event.target===this) cerrarScannerProducto()">
    <div class="relative bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-neutral-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800 dark:text-white">Escanear Código de Barras</h3>
            </div>
            <button onclick="cerrarScannerProducto()" class="rounded-full p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-neutral-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <!-- Camera viewport -->
        <div id="scanner-producto-viewport" class="bg-black" style="height: 260px; position: relative;">
            <!-- Line animada de escaneo -->
            <div id="scan-line-producto" style="display:none; position:absolute; left:0; right:0; height:2px; background: linear-gradient(90deg, transparent, #3b82f6, transparent); z-index:10; animation: scanLine 2s linear infinite;"></div>
        </div>
        <!-- Status -->
        <div class="px-5 py-3 bg-gray-50 dark:bg-neutral-800/40 text-center">
            <p id="scanner-producto-status" class="text-xs text-gray-500 dark:text-gray-400">Apunta la cámara al código de barras o QR...</p>
        </div>
        <!-- Manual input fallback -->
        <div class="px-5 py-4 border-t border-gray-100 dark:border-neutral-800">
            <p class="text-xs text-gray-400 mb-2 text-center">O ingresa el código manualmente:</p>
            <div class="flex gap-2">
                <input type="text" id="scanner-producto-manual"
                       placeholder="Ej: 7501055312152"
                       class="flex-1 rounded-xl border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                       onkeydown="if(event.key==='Enter') confirmarCodigoManualProducto()">
                <button onclick="confirmarCodigoManualProducto()"
                        class="rounded-xl bg-brand-600 px-4 py-2 text-xs font-bold text-white hover:bg-brand-700 transition">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scanLine {
    0%   { top: 10px; opacity: 1; }
    50%  { opacity: 0.6; }
    100% { top: 240px; opacity: 1; }
}


</style>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let _scannerProductoInstance = null;
    let _scannerProductoPrefix = null;

    function abrirScannerProducto(prefix) {
        _scannerProductoPrefix = prefix;
        document.getElementById('modalScannerProducto').classList.remove('hidden');
        document.getElementById('scan-line-producto').style.display = 'block';
        document.getElementById('scanner-producto-status').textContent = 'Iniciando cámara...';
        document.getElementById('scanner-producto-manual').value = '';

        if (_scannerProductoInstance) {
            _scannerProductoInstance.clear().catch(() => {});
        }

        _scannerProductoInstance = new Html5Qrcode('scanner-producto-viewport');

        const config = {
            fps: 15,
            qrbox: { width: 260, height: 160 },
            aspectRatio: 1.7,
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        };

        _scannerProductoInstance.start(
            { facingMode: 'environment' },
            config,
            (decodedText) => {
                aplicarCodigoProducto(decodedText);
            },
            () => {} // Errores de frame ignorados
        ).then(() => {
            document.getElementById('scanner-producto-status').textContent = 'Apunta al código de barras...';
        }).catch((err) => {
            document.getElementById('scanner-producto-status').textContent = '⚠ No se pudo acceder a la cámara. Usa la entrada manual.';
            console.error('Scanner error:', err);
        });
    }

    function cerrarScannerProducto() {
        document.getElementById('modalScannerProducto').classList.add('hidden');
        document.getElementById('scan-line-producto').style.display = 'none';
        if (_scannerProductoInstance) {
            _scannerProductoInstance.stop().catch(() => {});
            _scannerProductoInstance.clear().catch(() => {});
            _scannerProductoInstance = null;
        }
    }

    function aplicarCodigoProducto(codigo) {
        const input = document.getElementById('codigo_barras_' + _scannerProductoPrefix);
        if (input) {
            input.value = codigo;
            // Disparar eventos para Alpine.js reactivity
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
        document.getElementById('scanner-producto-status').textContent = '✅ Código detectado: ' + codigo;
        setTimeout(() => cerrarScannerProducto(), 800);
    }

    function confirmarCodigoManualProducto() {
        const manual = document.getElementById('scanner-producto-manual').value.trim();
        if (manual) aplicarCodigoProducto(manual);
    }

    async function buscarDatosAPIProducto(codigo, prefix) {
        if (!codigo || codigo.trim() === '') return;
        codigo = codigo.trim();

        window.Notify.info('Buscando producto en Internet...', { timeout: 2000 });

        try {
            const response = await fetch(`/inventario/productos/buscar-api/${codigo}`);
            const result = await response.json();

            if (result.success && result.data && result.data.nombre) {
                window.Confirm.show(
                    'Producto encontrado',
                    `¿Quieres usar los datos de "${result.data.nombre}"?`,
                    'Sí, usar datos',
                    'Cancelar',
                    () => {
                        const inputNombre = document.querySelector(`form:has(#codigo_barras_${prefix}) input[name="nombre"]`) || document.querySelector(`input[name="nombre"]`);
                        if (inputNombre) {
                            let nombreCompleto = result.data.nombre;
                            if (result.data.marca) nombreCompleto += ` - ${result.data.marca}`;
                            inputNombre.value = nombreCompleto;
                            inputNombre.dispatchEvent(new Event('input', { bubbles: true }));
                            inputNombre.dispatchEvent(new Event('change', { bubbles: true }));
                            window.Notify.success('Datos aplicados correctamente.');
                        }
                    },
                    () => {}
                );
            } else {
                window.Notify.warning('No se encontraron datos automáticos para este código.');
            }
        } catch (error) {
            console.error('Error buscando en API:', error);
            window.Notify.failure('Error de conexión al buscar el producto.');
        }
    }
</script>
@endonce
