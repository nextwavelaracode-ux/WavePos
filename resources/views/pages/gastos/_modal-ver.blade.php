{{-- Modal Ver Detalle de Gasto --}}
<div x-show="showVerModal" x-cloak
     class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100">

    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showVerModal = false"></div>

    <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-gray-900"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-white/[0.05]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Detalle del Gasto</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'#' + verData.id"></p>
                </div>
            </div>
            <button @click="showVerModal = false" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-4">
            {{-- Monto destacado --}}
            <div class="rounded-xl bg-red-50 dark:bg-red-500/10 p-4 text-center">
                <p class="text-xs font-medium text-red-500 dark:text-red-400 uppercase tracking-wider mb-1">Monto del Gasto</p>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400" x-text="'$' + verData.monto"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-0.5">Categoría</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white" x-text="verData.categoria || '—'"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-0.5">Método de Pago</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white" x-text="verData.metodo || '—'"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-0.5">Fecha</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white" x-text="verData.fecha || '—'"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-0.5">Sucursal</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white" x-text="verData.sucursal || '—'"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-0.5">Usuario</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white" x-text="verData.usuario || '—'"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-0.5">Estado</p>
                    <span x-text="verData.estado ? verData.estado.charAt(0).toUpperCase() + verData.estado.slice(1) : '—'"
                          :class="verData.estado === 'activo' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400'"
                          class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"></span>
                </div>
                <div x-show="verData.referencia">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-0.5">Referencia</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white font-mono" x-text="verData.referencia"></p>
                </div>
                <div x-show="verData.frecuencia">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-0.5">Frecuencia</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white capitalize" x-text="verData.frecuencia"></p>
                </div>
            </div>

            <div x-show="verData.descripcion">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Descripción</p>
                <p class="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-white/[0.03] rounded-xl p-3" x-text="verData.descripcion"></p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end border-t border-gray-100 px-6 py-4 dark:border-white/[0.05]">
            <button @click="showVerModal = false"
                    class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-white/[0.1] dark:text-gray-300 dark:hover:bg-white/[0.03]">
                Cerrar
            </button>
        </div>
    </div>
</div>
