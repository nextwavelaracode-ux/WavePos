@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Historial de Cobros AR" />

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-white/[0.05] flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Registro de Cobros</h4>
                <p class="text-xs text-gray-400">Listado histórico de todos los abonos realizados a cuentas por cobrar</p>
            </div>
            
            <form action="{{ route('cuentas.historial-pagos') }}" method="GET" class="flex flex-wrap gap-3">
                <div class="relative min-w-[240px]">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar cliente o recibo..."
                        class="w-full h-10 rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-4 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-white/[0.03] dark:text-white">
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 dark:border-white/[0.05] dark:bg-white/[0.03]">
                        <th class="px-6 py-4 text-left font-semibold text-gray-500 dark:text-gray-400">Fecha / Hora</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-500 dark:text-gray-400">Cliente</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-500 dark:text-gray-400">Venta Ref.</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-500 dark:text-gray-400">Recibido por</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-500 dark:text-gray-400">Método</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-500 dark:text-gray-400">Monto</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-500 dark:text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                    @if(count($pagos) > 0)
                        @foreach($pagos as $pago)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $pago->created_at->format('d/m/Y') }}</span>
                                    <p class="text-[10px] text-gray-400">{{ $pago->created_at->format('H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800 dark:text-white">{{ $pago->cuenta->cliente->nombre_completo }}</span>
                                        <span class="text-xs text-gray-400">{{ $pago->cuenta->cliente->documento_principal }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('cuentas.show', $pago->cuenta_id) }}" class="font-bold text-brand-600 hover:underline">
                                        #{{ $pago->cuenta->venta->numero }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $pago->usuario->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $pago->status_color }}">
                                        {{ $pago->metodo_label }}
                                    </span>
                                    @if($pago->referencia)
                                        <p class="text-[10px] text-gray-400 mt-0.5">Ref: {{ $pago->referencia }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-black text-emerald-600 dark:text-emerald-400">
                                    ${{ number_format($pago->monto, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('cuentas.show', $pago->cuenta_id) }}" 
                                       class="inline-flex items-center gap-1 text-xs font-bold text-brand-600 hover:text-brand-700">
                                        Ver Detalle
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 italic">
                                No se han registrado cobros aún.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($pagos->hasPages())
            <div class="p-5 border-t border-gray-100 dark:border-white/[0.05]">
                {{ $pagos->links() }}
            </div>
        @endif
    </div>
@endsection
