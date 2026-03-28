{{-- Modal Crear / Editar Gasto --}}
<div x-show="showModal" x-cloak
     class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>

    {{-- Modal Panel --}}
    <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl dark:bg-gray-900"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-white/[0.05]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white" x-text="isEditing ? 'Editar Gasto' : 'Registrar Gasto'"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Complete todos los campos requeridos</p>
                </div>
            </div>
            <button @click="showModal = false" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form :action="formAction" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <template x-if="isEditing">
                <input type="hidden" name="_method" value="PUT">
            </template>

            {{-- Row 1: Categoría + Monto --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Categoría de Gasto <span class="text-red-500">*</span>
                    </label>
                    <select name="categoria_gasto_id" x-model="categoria_gasto_id" required
                            class="w-full h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-gray-800 dark:text-white">
                        <option value="">— Seleccionar categoría —</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Monto (B/.) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium text-sm">$</span>
                        <input type="number" name="monto" x-model="monto" min="0.01" step="0.01" required
                               placeholder="0.00"
                               class="w-full h-11 rounded-xl border border-gray-200 bg-white pl-8 pr-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
            </div>

            {{-- Row 2: Método de pago + Referencia --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Método de Pago <span class="text-red-500">*</span>
                    </label>
                    <select name="metodo_pago" x-model="metodo_pago" required
                            class="w-full h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-gray-800 dark:text-white">
                        <option value="efectivo">💵 Efectivo</option>
                        <option value="transferencia">🏦 Transferencia</option>
                        <option value="tarjeta">💳 Tarjeta</option>
                        <option value="cheque">📄 Cheque</option>
                        <option value="yappy">📱 Yappy / Nequi</option>
                    </select>
                    <p x-show="metodo_pago === 'efectivo'" class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                        ⚠️ Se descontará automáticamente de la caja abierta.
                    </p>
                </div>
                <div x-show="requiereReferencia" x-transition>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Nº de Referencia <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="referencia" x-model="referencia" :required="requiereReferencia"
                           placeholder="Ej: TXN-001234"
                           class="w-full h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-gray-800 dark:text-white">
                </div>
            </div>

            {{-- Row 3: Fecha + Sucursal --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Fecha <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha" x-model="fecha" required
                           class="w-full h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Sucursal <span class="text-red-500">*</span>
                    </label>
                    <select name="sucursal_id" x-model="sucursal_id" required
                            class="w-full h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-gray-800 dark:text-white">
                        <option value="">— Seleccionar sucursal —</option>
                        @foreach($sucursales as $suc)
                            <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descripción</label>
                <textarea name="descripcion" x-model="descripcion" rows="2" placeholder="Detalle del gasto..."
                          class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-gray-800 dark:text-white resize-none"></textarea>
            </div>

            {{-- Comprobante --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Comprobante (opcional)</label>
                <input type="file" name="comprobante" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-xs file:font-medium file:text-brand-600 hover:file:bg-brand-100 dark:text-gray-400 dark:file:bg-brand-500/10 dark:file:text-brand-400">
                <p class="text-xs text-gray-400 mt-1">JPG, PNG o PDF · Máx. 5MB</p>
            </div>

            {{-- Gasto Recurrente --}}
            <div class="rounded-xl border border-gray-100 p-4 dark:border-white/[0.05]">
                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="es_recurrente" x-model="es_recurrente" value="1" class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Gasto recurrente</span>
                </label>
                <div x-show="es_recurrente" x-transition class="mt-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Frecuencia</label>
                    <select name="frecuencia" x-model="frecuencia"
                            class="h-9 rounded-xl border border-gray-200 bg-white px-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-gray-800 dark:text-white">
                        <option value="">— Seleccionar —</option>
                        <option value="diario">Diario</option>
                        <option value="semanal">Semanal</option>
                        <option value="quincenal">Quincenal</option>
                        <option value="mensual">Mensual</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-white/[0.05]">
                <button type="button" @click="showModal = false"
                        class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-white/[0.1] dark:text-gray-300 dark:hover:bg-white/[0.03]">
                    Cancelar
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="isEditing ? 'Actualizar Gasto' : 'Registrar Gasto'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
