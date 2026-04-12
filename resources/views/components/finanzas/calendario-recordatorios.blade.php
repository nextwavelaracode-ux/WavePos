{{-- Calendario de Recordatorios Personales --}}
<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
            border-2 border-gray-200 dark:border-neutral-700 p-5 overflow-hidden">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Mis Recordatorios
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Clic en un día vacío para agregar</p>
        </div>
    </div>
    <div id="calendarioRecordatorios"></div>
</div>

{{-- Modal Recordatorio --}}
<div id="modalRecordatorio"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden"
     x-data>
    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-md
                border-2 border-gray-200 dark:border-neutral-700 p-6">
        <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-1" id="modalRecordatorioTitle">
            Nuevo Recordatorio
        </h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="modalRecordatorioFecha"></p>

        <form id="formRecordatorio" class="space-y-4">
            @csrf
            <input type="hidden" id="recordatorioFechaInput" name="fecha">
            <input type="hidden" id="recordatorioIdInput" name="id">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título *</label>
                <input type="text" id="recordatorioTitulo" name="titulo" required
                       placeholder="Ej: Pagar proveedor XYZ"
                       class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 dark:border-neutral-700
                              bg-white dark:bg-neutral-800 text-gray-800 dark:text-white
                              focus:border-blue-500 focus:outline-none text-sm transition-colors">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                <textarea id="recordatorioDescripcion" name="descripcion" rows="2"
                          placeholder="Detalles opcionales..."
                          class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 dark:border-neutral-700
                                 bg-white dark:bg-neutral-800 text-gray-800 dark:text-white
                                 focus:border-blue-500 focus:outline-none text-sm transition-colors resize-none"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color</label>
                <div class="flex gap-2" id="colorPicker">
                    @foreach(['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4'] as $col)
                        <button type="button" data-color="{{ $col }}"
                                class="w-7 h-7 rounded-full border-2 border-white dark:border-neutral-700
                                       shadow ring-2 ring-transparent hover:ring-gray-400
                                       transition-all color-option"
                                style="background:{{ $col }}"></button>
                    @endforeach
                </div>
                <input type="hidden" id="recordatorioColor" name="color" value="#3b82f6">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl
                               text-sm font-semibold transition-colors shadow-md">
                    Guardar
                </button>
                <button type="button" id="btnBorrarRecordatorio"
                        class="hidden px-4 py-2.5 bg-red-100 hover:bg-red-200 text-red-700
                               dark:bg-red-900/30 dark:text-red-400 rounded-xl text-sm font-semibold transition-colors">
                    Eliminar
                </button>
                <button type="button" onclick="cerrarModalRecordatorio()"
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-neutral-800 dark:hover:bg-neutral-700
                               text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-colors">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

