@extends('layouts.app')

@section('content')

    {{-- SweetAlert2 Notification --}}
    @if (session('sweet_alert'))
        @php $sa = session('sweet_alert'); @endphp
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: '{{ $sa['type'] }}',
                        title: '{{ $sa['title'] }}',
                        text: '{{ $sa['message'] }}',
                        timer: 3000,
                        showConfirmButton: false,
                    });
                });
            </script>
        @endpush
    @endif

<div class="space-y-6">
    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Compras Atrasadas (Vencidas)</h2>
            <div class="flex items-center gap-2 mt-1">
                <a href="{{ route('cuentas-por-pagar.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 transition-colors">Cuentas por Pagar</a>
                <span class="text-sm text-gray-400">/</span>
                <span class="text-sm font-medium text-red-500">Vencidas</span>
            </div>
        </div>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900">
        <div class="border-b border-gray-100 dark:border-white/[0.05] px-6 py-4">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Listado de Compras Vencidas</h4>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Factura</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Proveedor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Vencimiento</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Atraso</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Saldo Pendiente</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                    @forelse($compras as $compra)
                        @php
                            $diasAtraso = \Carbon\Carbon::parse($compra->fecha_vencimiento)->diffInDays(now(), false);
                        @endphp
                        <tr class="hover:bg-red-50/50 dark:hover:bg-red-500/5 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90">
                                #{{ $compra->numero_factura }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $compra->proveedor->empresa }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90">
                                {{ \Carbon\Carbon::parse($compra->fecha_vencimiento)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-red-600 dark:text-red-400 font-semibold">
                                {{ intval($diasAtraso) }} días
                            </td>
                            <td class="px-6 py-4 text-red-600 dark:text-red-400 font-bold text-base">
                                ${{ number_format($compra->saldo_pendiente, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('cuentas-por-pagar.show', $compra->id) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition-colors" title="Ver Detalles">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver
                                    </a>
                                    @if($compra->estado_pago !== 'pagado')
                                        <a href="{{ route('cuentas-por-pagar.show', [$compra->id, 'action' => 'pay']) }}" class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-red-700 transition-colors shadow-sm" title="Abonar a cuenta atrasada">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            Pagar
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-emerald-500 dark:text-emerald-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">¡Excelente! No tienes cuentas vencidas por pagar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($compras->hasPages())
            <div class="border-t border-gray-100 px-6 py-4 dark:border-white/[0.05]">
                {{ $compras->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
