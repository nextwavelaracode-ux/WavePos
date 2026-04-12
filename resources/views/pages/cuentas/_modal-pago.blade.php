{{-- ===== MODAL REGISTRAR PAGO (REUSABLE) ===== --}}
<div x-data="{ 
    open: false,
    cuentaId: null,
    monto: 0,
    metodo: 'efectivo',
    referencia: '',
    observaciones: '',
    cliente: '',
    saldoMax: 0,
    cargando: false,
    
    async confirmarPago() {
        if (this.monto <= 0 || this.monto > (this.saldoMax + 0.01)) {
            window.Notify.warning(`El monto debe ser entre $0.01 y $${this.saldoMax}`);
            return;
        }
        
        this.cargando = true;
        try {
            const response = await fetch(`/cuentas-por-cobrar/${this.cuentaId}/pagar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    monto: this.monto,
                    metodo: this.metodo,
                    referencia: this.referencia,
                    observaciones: this.observaciones
                })
            });
            
            const data = await response.json();
            this.cargando = false;
            
            if (data.success) {
                this.open = false;
                window.Notify.success(data.message, { timeout: 2000 });
                setTimeout(() => window.location.reload(), 2000);
            } else {
                window.Notify.failure(data.message);
            }
        } catch (e) {
            this.cargando = false;
            window.Notify.failure('Error de red: ' + e.message);
        }
    }
}" 
@open-pago-modal.window="open = true; cuentaId = $event.detail.id; saldoMax = $event.detail.saldo; monto = $event.detail.saldo; cliente = $event.detail.cliente;"
x-show="open" x-cloak
class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
    
    <div @click.outside="open = false" class="relative w-full max-w-md rounded-2xl bg-white dark:bg-neutral-900 shadow-2xl overflow-hidden shadow-emerald-500/10 border border-gray-100 dark:border-neutral-800/80">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-neutral-800/80">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Registrar Cobro</h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div class="p-6 space-y-5">
            <div class="rounded-2xl bg-emerald-50 p-4 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20">
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Cliente</p>
                <p class="text-sm font-bold text-emerald-900 dark:text-emerald-300" x-text="cliente"></p>
                <div class="mt-3 flex justify-between items-end border-t border-emerald-200 pt-2 dark:border-emerald-500/20">
                    <span class="text-xs font-medium text-emerald-600">Saldo Pendiente:</span>
                    <span class="text-xl font-black text-emerald-700 dark:text-emerald-300" x-text="'$' + saldoMax.toFixed(2)"></span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Monto del Abono / Pago</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-400">$</span>
                        <input type="number" x-model.number="monto" step="0.01" :max="saldoMax" 
                            class="w-full h-12 rounded-xl border-2 border-gray-100 bg-gray-50 pl-8 pr-4 text-lg font-black text-gray-800 focus:border-brand-500 focus:bg-white focus:ring-0 dark:border-neutral-800/80 dark:bg-neutral-800/10 dark:text-white transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Método de Cobro</label>
                        <select x-model="metodo"
                            class="w-full h-11 rounded-xl border border-gray-100 bg-white px-4 text-sm font-semibold focus:border-brand-500 focus:ring-0 dark:border-neutral-800/80 dark:bg-neutral-800 dark:text-white">
                            <option value="efectivo">💵 Efectivo</option>
                            <option value="tarjeta">💳 Tarjeta</option>
                            <option value="transferencia">🏦 Transferencia</option>
                            <option value="yappy">📱 Yappy / Nequi</option>
                        </select>
                    </div>

                    <template x-if="metodo !== 'efectivo'">
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">N° Referencia / Voucher</label>
                            <input type="text" x-model="referencia" placeholder="Obligatorio para electrónicos"
                                class="w-full h-11 rounded-xl border border-gray-100 bg-white px-4 text-sm focus:border-brand-500 focus:ring-0 dark:border-neutral-800/80 dark:bg-neutral-800 dark:text-white">
                        </div>
                    </template>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Observaciones</label>
                        <textarea x-model="observaciones" rows="2" placeholder="Opcional..."
                            class="w-full rounded-xl border border-gray-100 bg-white px-4 py-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-neutral-800/80 dark:bg-neutral-800 dark:text-white"></textarea>
                    </div>
                </div>
            </div>

            <button @click="confirmarPago()" :disabled="cargando"
                class="w-full h-14 rounded-2xl bg-emerald-600 text-base font-black text-white hover:bg-emerald-700 disabled:opacity-50 transition-all flex items-center justify-center gap-3 shadow-lg shadow-emerald-600/20 active:scale-[0.98]">
                <span x-show="!cargando" class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Confirmar Cobro
                </span>
                <span x-show="cargando" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Procesando cobro...
                </span>
            </button>
        </div>
    </div>
</div>
