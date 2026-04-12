@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Cuentas por Cobrar" />

    <div class="space-y-6">
        {{-- ===== KPI CARDS ===== --}}
        <div class="grid md:grid-cols-4 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 shadow-2xs rounded-xl overflow-hidden">

            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700 first:before:hidden">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Total por Cobrar</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">${{ number_format($kpis['total_deuda'], 2) }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Saldo pendiente total</p>
                    </div>
                </div>
            </div>

            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Monto Vencido</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">${{ number_format($kpis['total_vencido'], 2) }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Cuentas vencidas</p>
                    </div>
                </div>
            </div>

            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Clientes Deudores</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">{{ $kpis['clientes_con_deuda'] }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Con saldo pendiente</p>
                    </div>
                </div>
            </div>

            <div class="block p-4 md:p-5 relative bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 transition before:absolute before:top-0 before:start-0 before:w-full before:h-px md:before:h-full before:border-s before:border-gray-200 dark:before:border-neutral-700">
                <div class="flex flex-col lg:flex-row gap-y-3 gap-x-5">
                    <svg class="shrink-0 size-5 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="grow">
                        <p class="text-xs uppercase font-medium text-gray-800 dark:text-neutral-200">Cobros del Mes</p>
                        <h3 class="mt-1 text-xl sm:text-2xl font-semibold text-blue-600 dark:text-blue-500">${{ number_format($kpis['cobros_mes'], 2) }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-neutral-400">Recaudado este mes</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== FILTERS & TABLE ===== --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden">
            {{-- Toolbar --}}
            <div class="p-5 border-b border-gray-100 dark:border-neutral-800/80 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Listado de Cuentas</h4>
                
                <form action="{{ route('cuentas.index') }}" method="GET" class="flex flex-wrap gap-3">
                    <div class="relative min-w-[240px]">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar cliente o venta..."
                            class="w-full h-10 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-neutral-800/20 dark:text-white">
                    </div>

                    <select name="estado" onchange="this.form.submit()"
                        class="h-10 rounded-xl border border-gray-200 bg-gray-50 px-4 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-neutral-800/20 dark:text-white">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="parcial" {{ request('estado') === 'parcial' ? 'selected' : '' }}>Parcial</option>
                        <option value="pagado" {{ request('estado') === 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="vencido" {{ request('estado') === 'vencido' ? 'selected' : '' }}>Vencido</option>
                    </select>

                    @if(request()->anyFilled(['buscar', 'estado']))
                        <a href="{{ route('cuentas.index') }}" class="flex h-10 items-center justify-center rounded-xl border border-gray-200 px-4 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:border-white/[0.1] dark:text-gray-400">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50 dark:border-neutral-800/80 dark:bg-neutral-800/20">
                            <th class="px-6 py-4 text-left font-semibold text-gray-500 dark:text-gray-400"># Venta</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-500 dark:text-gray-400">Cliente</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-500 dark:text-gray-400">Sucursal</th>
                            <th class="px-6 py-4 text-right font-semibold text-gray-500 dark:text-gray-400">Total Deuda</th>
                            <th class="px-6 py-4 text-right font-semibold text-gray-500 dark:text-gray-400">Saldo Pendiente</th>
                            <th class="px-6 py-4 text-center font-semibold text-gray-500 dark:text-gray-400">Vencimiento</th>
                            <th class="px-6 py-4 text-center font-semibold text-gray-500 dark:text-gray-400">Estado</th>
                            <th class="px-6 py-4 text-right font-semibold text-gray-500 dark:text-gray-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/80">
                        @forelse($cuentas as $cuenta)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-gray-800 dark:text-white">{{ $cuenta->venta->numero }}</span>
                                    <p class="text-[10px] text-gray-400">{{ $cuenta->venta->fecha }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-800 dark:text-white">{{ $cuenta->cliente->nombre_completo }}</span>
                                        <span class="text-xs text-gray-400">{{ $cuenta->cliente->documento_principal }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $cuenta->sucursal->nombre }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800 dark:text-white">
                                    ${{ number_format($cuenta->total, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold {{ $cuenta->saldo_pendiente > 0 ? 'text-brand-600 dark:text-brand-400' : 'text-emerald-600' }}">
                                    ${{ number_format($cuenta->saldo_pendiente, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs {{ $cuenta->estado === 'vencido' ? 'font-bold text-red-500' : 'text-gray-500' }}">
                                        {{ $cuenta->fecha_vencimiento->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $cuenta->status_color }}">
                                        {{ ucfirst($cuenta->estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('cuentas.show', $cuenta) }}" 
                                           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-brand-600 dark:hover:bg-white/10"
                                           title="Ver Detalles">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @if($cuenta->saldo_pendiente > 0)
                                            <button @click="$dispatch('open-pago-modal', { id: {{ $cuenta->id }}, saldo: {{ $cuenta->saldo_pendiente }}, cliente: '{{ $cuenta->cliente->nombre_completo }}' })"
                                               class="rounded-lg p-2 text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-500/10"
                                               title="Registrar Pago">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    No se encontraron cuentas por cobrar con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($cuentas->hasPages())
                <div class="p-5 border-t border-gray-100 dark:border-neutral-800/80">
                    {{ $cuentas->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ===== MODAL REGISTRAR PAGO (GLOBAL) ===== --}}
    @include('pages.cuentas._modal-pago')
@endsection
