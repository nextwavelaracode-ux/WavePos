{{-- Calendario de Vencimientos (automático: compras crédito + cuentas por cobrar) --}}
<div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm
            border-2 border-gray-200 dark:border-neutral-700 p-5 overflow-hidden">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Vencimientos
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Compras a crédito y cuentas por cobrar</p>
        </div>
        <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>Por pagar</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>Por cobrar</span>
        </div>
    </div>
    <div id="calendarioVencimientos"></div>
</div>

{{-- Modal detalle vencimientos del día --}}
<div id="modalVencimientos"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-lg
                border-2 border-gray-200 dark:border-neutral-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-bold text-gray-800 dark:text-white" id="modalVencimientosTitle">
                Vencimientos del día
            </h4>
            <button onclick="document.getElementById('modalVencimientos').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="modalVencimientosBody" class="space-y-3 max-h-80 overflow-y-auto"></div>
    </div>
</div>

