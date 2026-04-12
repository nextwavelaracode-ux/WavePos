@extends('layouts.app')

@section('content')



<div class="space-y-6">
    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Historial General de Pagos</h2>
            <div class="flex items-center gap-2 mt-1">
                <a href="{{ route('cuentas-por-pagar.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 transition-colors">Cuentas por Pagar</a>
                <span class="text-sm text-gray-400">/</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">Historial global</span>
            </div>
        </div>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
        <div class="border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50 dark:bg-neutral-800/20 px-6 py-4 flex justify-between items-center">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pagos Registrados</h4>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50 dark:bg-neutral-800/20">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">ID Pago</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Proveedor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Compra</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Monto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Método y Ref.</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Registrado por</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                    @forelse($pagos as $pago)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-medium">
                                #{{ $pago->id }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90">
                                {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $pago->compra->proveedor->empresa }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('cuentas-por-pagar.show', $pago->compra_id) }}" class="text-brand-500 hover:text-brand-600 font-medium underline">
                                    Fact. #{{ $pago->compra->numero_factura }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-emerald-600 dark:text-emerald-400 font-semibold">
                                +${{ number_format($pago->monto, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-800 dark:text-white/90 font-medium uppercase text-xs">{{ str_replace('_', ' ', $pago->metodo_pago) }}</p>
                                @if($pago->referencia)
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">Ref: {{ $pago->referencia }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-gray-500 dark:text-gray-400">
                                {{ optional($pago->usuario)->name ?? 'Admin' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-600">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm font-medium">No se encontraron pagos registrados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($pagos->hasPages())
            <div class="border-t border-gray-100 px-6 py-4 dark:border-neutral-800/80">
                {{ $pagos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
