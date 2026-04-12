<div x-data="{ openTicket: false, ticketUrl: '' }" 
     @abrir-ticket.window="ticketUrl = $event.detail; openTicket = true"
     x-cloak>
    <!-- Overlay -->
    <div x-show="openTicket" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[9999]" 
         @click="openTicket = false; setTimeout(() => ticketUrl = '', 300); window.dispatchEvent(new CustomEvent('ticket-cerrado'))"></div>
         
    <!-- Offcanvas Panel -->
    <div class="fixed top-0 right-0 h-full w-[400px] max-w-full bg-white dark:bg-neutral-900 shadow-2xl z-[10000] transform transition-transform duration-300 flex flex-col translate-x-full"
         :class="openTicket ? 'translate-x-0' : 'translate-x-full'">
        
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-neutral-800 flex justify-between items-center bg-gray-50 dark:bg-neutral-800/50">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Vista de Ticket
            </h3>
            <button @click="openTicket = false; setTimeout(() => ticketUrl = '', 300); window.dispatchEvent(new CustomEvent('ticket-cerrado'))" class="rounded-full p-1.5 text-gray-500 hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-neutral-700 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- Content iframe -->
        <div class="flex-1 overflow-hidden bg-gray-200 dark:bg-neutral-950 flex justify-center relative">
            <template x-if="ticketUrl">
                <iframe :src="ticketUrl" class="w-full h-full border-0 absolute inset-0 z-10" id="ticketFrame"></iframe>
            </template>
            <!-- Loading indicator -->
            <div x-show="ticketUrl" class="absolute inset-0 flex items-center justify-center text-gray-400 z-0">
                <svg class="animate-spin h-8 w-8 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <!-- Empty State -->
            <div x-show="!ticketUrl" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 z-0">
                <svg class="w-12 h-12 mb-3 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <p class="text-sm">Sin documento válido</p>
            </div>
        </div>
        
        <!-- Footer actions -->
        <div class="p-4 border-t border-gray-200 dark:border-neutral-800 gap-3 flex bg-white dark:bg-neutral-900 drop-shadow-md">
            <button @click="openTicket = false; setTimeout(() => ticketUrl = '', 300); window.dispatchEvent(new CustomEvent('ticket-cerrado'))" class="flex-1 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 dark:border-neutral-700 dark:text-gray-300 dark:hover:bg-neutral-800 transition">
                Cerrar
            </button>
            <button @click="document.getElementById('ticketFrame').contentWindow.print()" class="flex-1 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition flex items-center justify-center gap-2 shadow-sm shadow-emerald-600/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Imprimir
            </button>
        </div>
    </div>
</div>
