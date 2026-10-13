@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Cobro Contra Reembolso" />

    <div id="cobro-root" x-data="{
        activeTab: '{{ $tab ?? 'pendientes' }}',
        showPayModal: false,
        showOrderDrawer: false,
        selectedOrder: null,
        metodoPago: 'efectivo',
        montoRecibido: 0,
        referencia: '',
        observaciones: '',

        openModal(order) {
            this.selectedOrder = order;
            this.metodoPago = 'efectivo';
            this.montoRecibido = parseFloat(order.total || 0);
            this.referencia = 'WC-' + order.id;
            this.observaciones = '';
            this.showPayModal = true;
        },

        openDrawer(order) {
            this.selectedOrder = order;
            this.showOrderDrawer = true;
        },

        get cambio() {
            if (!this.selectedOrder) return 0;
            const tot = parseFloat(this.selectedOrder.total || 0);
            const rec = parseFloat(this.montoRecibido || 0);
            return Math.max(0, rec - tot);
        },

        setExacto() {
            if (this.selectedOrder) {
                this.montoRecibido = parseFloat(this.selectedOrder.total || 0);
            }
        }
    }">

        {{-- ===== RESUMEN CARDS (Mismo componente de Control de Stock) ===== --}}
        <div class="mb-6 grid md:grid-cols-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-2xs rounded-xl overflow-hidden">
            {{-- 1. Pedidos Pendientes --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Por Cobrar</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-orange-600 dark:text-orange-500">{{ count($pendingOrders) }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Pedidos contra reembolso</p>
                    </div>
                </div>
            </div>

            {{-- 2. Monto por Cobrar --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Monto Pendiente</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-brand-600 dark:text-brand-500">${{ number_format($totalPendienteMonto, 2) }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Total por recaudar en caja</p>
                    </div>
                </div>
            </div>

            {{-- 3. Entregados / Completados --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Completados</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-emerald-600 dark:text-emerald-500">{{ count($completedOrders) }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Entregados y cobrados</p>
                    </div>
                </div>
            </div>

            {{-- 4. Caja Física --}}
            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Caja Registradora</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold {{ $cajaAbierta ? 'text-emerald-600 dark:text-emerald-500' : 'text-red-600 dark:text-red-500' }}">
                            {{ $cajaAbierta ? 'Turno #' . $cajaAbierta->id : 'Caja Cerrada' }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">
                            {{ $cajaAbierta ? ($cajaAbierta->sucursal->nombre ?? 'Caja Principal') : 'Abre caja para registrar cobros' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== HEADER / TOOLBAR ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Cobro Contra Reembolso (Retiro en Tienda)</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Registra el pago de los clientes que hicieron pedido en WooCommerce y retiran en físico
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('woocommerce.cobro', ['refresh' => 1, 'tab' => $tab]) }}" title="Actualizar pedidos desde WooCommerce" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refrescar
                </a>
                <a href="{{ route('woocommerce.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Panel WooCommerce
                </a>
                <a href="{{ route('caja.pos') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Punto de Venta (POS)
                </a>
            </div>
        </div>

        {{-- ===== TABS TOGGLE ===== --}}
        <div class="mb-4 flex items-center gap-2 border-b border-gray-200 dark:border-neutral-700">
            <a href="{{ route('woocommerce.cobro', ['tab' => 'pendientes', 'search' => $search]) }}"
                class="inline-flex items-center gap-2 py-3 px-4 border-b-2 font-medium text-sm transition-colors {{ $tab === 'pendientes' ? 'border-brand-600 text-brand-600 dark:border-brand-500 dark:text-brand-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pedidos por Cobrar
                <span class="rounded-full bg-orange-100 dark:bg-orange-900/40 px-2.5 py-0.5 text-xs font-semibold text-orange-700 dark:text-orange-400">{{ count($pendingOrders) }}</span>
            </a>
            <a href="{{ route('woocommerce.cobro', ['tab' => 'completados']) }}"
                class="inline-flex items-center gap-2 py-3 px-4 border-b-2 font-medium text-sm transition-colors {{ $tab === 'completados' ? 'border-brand-600 text-brand-600 dark:border-brand-500 dark:text-brand-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Historial de Completados
            </a>
        </div>

        {{-- ===== FILTRO DE BÚSQUEDA (Mismo componente de Control de Stock) ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('woocommerce.cobro') }}" class="w-full max-w-lg flex items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}" />
                <div class="relative w-full">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 dark:text-white text-sm border border-slate-200 dark:border-neutral-700 rounded-md pl-10 pr-24 py-2.5 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 dark:hover:border-gray-600 shadow-sm"
                        placeholder="Buscar por orden #, nombre de cliente o teléfono..." />
                    
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <button
                        class="absolute top-1 right-1 flex items-center rounded bg-slate-800 dark:bg-brand-500 py-1.5 px-3 border border-transparent text-center text-xs font-semibold text-white transition-all shadow-sm hover:shadow"
                        type="submit">
                        Buscar
                    </button>
                </div>

                @if(!empty($search))
                    <a href="{{ route('woocommerce.cobro', ['tab' => $tab]) }}" class="py-2 px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 rounded border border-gray-200 dark:border-neutral-700 hover:bg-gray-100 dark:hover:bg-neutral-800 transition">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- ===== TABLA (Mismo componente de Control de Stock) ===== --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50 dark:bg-neutral-800/20">
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Orden</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cliente</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Artículos</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/80">
                        @php
                            $list = $tab === 'completados' ? $completedOrders : $pendingOrders;
                        @endphp
                        @forelse ($list as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-4 font-mono font-bold text-gray-900 dark:text-white">
                                    #{{ $order['id'] }}
                                </td>
                                <td class="px-5 py-4 text-gray-500 dark:text-gray-400 text-xs">
                                    {{ !empty($order['date_created']) ? \Carbon\Carbon::parse($order['date_created'])->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-medium text-gray-800 dark:text-white/90">
                                        {{ trim(($order['billing']['first_name'] ?? '') . ' ' . ($order['billing']['last_name'] ?? '')) ?: 'Cliente Web' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $order['billing']['phone'] ?? ($order['billing']['email'] ?? 'Sin contacto') }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-xs text-gray-600 dark:text-gray-300">
                                        @foreach (array_slice($order['line_items'] ?? [], 0, 2) as $it)
                                            <div>• {{ $it['quantity'] }}x {{ Str::limit($it['name'], 25) }}</div>
                                        @endforeach
                                        @if (count($order['line_items'] ?? []) > 2)
                                            <span class="text-gray-400">+{{ count($order['line_items']) - 2 }} más</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right font-bold text-base text-gray-900 dark:text-white">
                                    ${{ number_format((float)($order['total'] ?? 0), 2) }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $st = $order['status'] ?? 'unknown';
                                        $badgeClasses = match($st) {
                                            'completed'  => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                            'processing' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                            'on-hold'    => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                            default      => 'bg-gray-100 text-gray-700 dark:bg-neutral-800 dark:text-gray-300',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClasses }}">
                                        {{ ucfirst($st) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if(($order['status'] ?? '') !== 'completed')
                                            <button type="button" @click="openModal({{ json_encode($order) }})" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                Cobrar y Entregar
                                            </button>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Completado
                                            </span>
                                        @endif

                                        <button type="button" @click="openDrawer({{ json_encode($order) }})" class="p-1.5 text-gray-500 hover:text-brand-600 rounded-lg hover:bg-gray-100 dark:hover:bg-neutral-800 transition" title="Ver detalles">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="mt-2 text-base font-medium text-gray-700 dark:text-gray-300">
                                        {{ $tab === 'completados' ? 'No hay pedidos completados en el historial' : 'No hay pedidos pendientes de cobro' }}
                                    </p>
                                    <p class="text-xs text-gray-500">Todos los retiros contra reembolso están al día.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== MODAL DE COBRO FÍSICO ===== --}}
        <div x-show="showPayModal" class="relative z-50" style="display: none;">
            <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm" @click="showPayModal = false"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div @click.outside="showPayModal = false" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800">
                        
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-neutral-800">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                    Cobrar Pedido #<span x-text="selectedOrder?.id"></span>
                                </h3>
                                <p class="text-xs text-gray-500">
                                    Cliente: <span class="font-medium text-gray-700 dark:text-gray-300" x-text="((selectedOrder?.billing?.first_name || '') + ' ' + (selectedOrder?.billing?.last_name || '')).trim() || 'Cliente'"></span>
                                </p>
                            </div>
                            <button @click="showPayModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Modal Form --}}
                        <form :action="'{{ url('woocommerce/cobro') }}/' + selectedOrder?.id" method="POST" class="mt-4 space-y-4">
                            @csrf

                            {{-- Resumen Total --}}
                            <div class="p-4 rounded-xl bg-brand-50 dark:bg-brand-950/20 border border-brand-100 dark:border-brand-900/40 flex items-center justify-between">
                                <div>
                                    <span class="text-xs uppercase font-semibold text-brand-700 dark:text-brand-300">Total a Cobrar</span>
                                    <h2 class="text-2xl font-black text-brand-600 dark:text-brand-400" x-text="'$' + parseFloat(selectedOrder?.total || 0).toFixed(2)"></h2>
                                </div>
                                <button type="button" @click="setExacto()" class="rounded-lg bg-white dark:bg-neutral-800 border border-brand-200 dark:border-neutral-700 px-3 py-1.5 text-xs font-semibold text-brand-700 dark:text-brand-300 hover:bg-brand-50 shadow-xs">
                                    Monto Exacto
                                </button>
                            </div>

                            {{-- Método de Pago --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Método de Pago</label>
                                <select name="metodo_pago" x-model="metodoPago" required class="w-full rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta de Débito / Crédito</option>
                                    <option value="transferencia">Transferencia Bancaria</option>
                                    <option value="yappy">Yappy / Pago Móvil</option>
                                </select>
                            </div>

                            {{-- Monto Recibido y Cambio --}}
                            <div class="grid grid-cols-2 gap-3" x-show="metodoPago === 'efectivo'">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Monto Recibido</label>
                                    <input type="number" step="0.01" name="monto_recibido" x-model="montoRecibido" class="w-full rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm text-gray-800 dark:text-white font-semibold focus:border-brand-500 focus:outline-none" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Cambio / Vuelto</label>
                                    <div class="rounded-lg bg-gray-100 dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 px-3 py-2 text-sm font-bold text-emerald-600 dark:text-emerald-400" x-text="'$' + cambio.toFixed(2)"></div>
                                </div>
                            </div>

                            {{-- Referencia --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Referencia / Comprobante</label>
                                <input type="text" name="referencia" x-model="referencia" class="w-full rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" placeholder="WC-12345" />
                            </div>

                            {{-- Observaciones --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Observaciones</label>
                                <textarea name="observaciones" x-model="observaciones" rows="2" class="w-full rounded-lg border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm text-gray-800 dark:text-white focus:border-brand-500 focus:outline-none" placeholder="Notas sobre la entrega física..."></textarea>
                            </div>

                            {{-- Botones --}}
                            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
                                <button type="button" @click="showPayModal = false" class="rounded-xl border border-gray-300 dark:border-neutral-700 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-800">Cancelar</button>
                                <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700 transition shadow-sm">
                                    Confirmar Cobro y Completar Pedido
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== OFFCANVAS DRAWER: DETALLE DEL PEDIDO (Mismo componente de productos.blade.php) ===== --}}
        <div x-show="showOrderDrawer" class="relative z-50" style="display: none;">
            {{-- Backdrop --}}
            <div x-show="showOrderDrawer"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showOrderDrawer = false"
                 class="fixed inset-0 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"></div>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <div x-show="showOrderDrawer"
                             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             @click.outside="showOrderDrawer = false"
                             @keydown.escape.window="showOrderDrawer = false"
                             class="pointer-events-auto w-screen max-w-md">
                            <div class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-neutral-900 shadow-xl border-l border-gray-200 dark:border-neutral-800">
                                
                                {{-- Header Drawer --}}
                                <div class="px-5 py-4 bg-gray-50 dark:bg-neutral-800/50 border-b border-gray-200 dark:border-neutral-800 sticky top-0 z-10 flex items-center justify-between">
                                    <h5 class="inline-flex items-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        Detalle de Pedido #<span x-text="selectedOrder?.id"></span>
                                    </h5>
                                    <button type="button" @click="showOrderDrawer = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-neutral-800 dark:hover:text-white">
                                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>

                                {{-- Body Drawer --}}
                                <div class="relative flex-1 px-5 py-6 space-y-6" x-show="selectedOrder">
                                    <div class="bg-gray-50 dark:bg-neutral-800/50 p-4 rounded-xl border border-gray-100 dark:border-neutral-800">
                                        <p class="text-xs uppercase font-medium text-gray-500 dark:text-gray-400">Cliente</p>
                                        <h4 class="mt-1 text-base font-bold text-gray-900 dark:text-white" x-text="((selectedOrder?.billing?.first_name || '') + ' ' + (selectedOrder?.billing?.last_name || '')).trim() || 'Cliente'"></h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1" x-text="selectedOrder?.billing?.email || ''"></p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300" x-text="selectedOrder?.billing?.phone || ''"></p>
                                    </div>

                                    <div class="space-y-3">
                                        <h5 class="text-xs uppercase font-semibold text-gray-500 dark:text-gray-400 tracking-wider">Artículos a Entregar</h5>
                                        <div class="divide-y divide-gray-100 dark:divide-neutral-800 border border-gray-100 dark:border-neutral-800 rounded-xl overflow-hidden">
                                            <template x-for="item in (selectedOrder?.line_items || [])" :key="item.id">
                                                <div class="p-3 flex items-center justify-between bg-white dark:bg-neutral-900">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="item.name"></p>
                                                        <p class="text-xs text-gray-500" x-text="'Cantidad: ' + item.quantity + ' × $' + parseFloat(item.price || 0).toFixed(2)"></p>
                                                    </div>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="'$' + parseFloat(item.total || 0).toFixed(2)"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="bg-brand-50 dark:bg-brand-950/20 p-4 rounded-xl border border-brand-100 dark:border-brand-900/40 flex justify-between items-center">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white">Total a Cobrar</span>
                                        <span class="text-xl font-bold text-brand-600 dark:text-brand-400" x-text="'$' + parseFloat(selectedOrder?.total || 0).toFixed(2)"></span>
                                    </div>

                                    <template x-if="selectedOrder?.status !== 'completed'">
                                        <button type="button" @click="showOrderDrawer = false; openModal(selectedOrder)" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            Cobrar y Entregar Pedido
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
