@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Compras" />

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

    {{-- ===== HEADER / TOOLBAR ===== --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Historial de Compras</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona el registro de compras y abastecimiento</p>
        </div>
        <a href="{{ route('compras.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva Compra
        </a>
    </div>

    {{-- ===== BARRA DE FILTROS ===== --}}
    <div class="mb-6">
        <form action="{{ route('compras.index') }}" method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por factura o proveedor..." 
                    class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-white/[0.05] dark:bg-gray-900 dark:text-white/90">
            </div>
            
            <div class="sm:w-48">
                <select name="estado" class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 px-4 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 dark:border-white/[0.05] dark:bg-gray-900 dark:text-white/90">
                    <option value="">Todos los estados</option>
                    <option value="registrada" {{ request('estado') === 'registrada' ? 'selected' : '' }}>Registrada</option>
                    <option value="anulada" {{ request('estado') === 'anulada' ? 'selected' : '' }}>Anulada</option>
                    <option value="devuelta" {{ request('estado') === 'devuelta' ? 'selected' : '' }}>Devuelta</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors dark:bg-white/[0.03] dark:text-gray-300 dark:hover:bg-white/[0.05]">
                    Filtrar
                </button>
                @if(request('search') || request('estado'))
                    <a href="{{ route('compras.index') }}" class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ===== TABLA ===== --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/[0.05] dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/[0.05] bg-gray-50 dark:bg-white/[0.03]">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Nº Factura</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Proveedor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                    @forelse ($compras as $compra)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $compra->fecha_compra->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90">
                                {{ $compra->numero_factura }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-800 dark:text-white/90 font-medium">{{ $compra->proveedor->empresa }}</div>
                                <div class="text-xs text-gray-500">{{ $compra->proveedor->ruc ?? $compra->proveedor->contacto }}</div>
                            </td>

                            {{-- Tipo Compra Badge --}}
                            <td class="px-6 py-4">
                                @php
                                    $tipoColors = [
                                        'contado' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                        'credito' => 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold uppercase {{ $tipoColors[$compra->tipo_compra] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $compra->tipo_compra }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right text-gray-800 dark:text-white/90 font-medium">
                                ${{ number_format($compra->total, 2) }}
                            </td>

                            {{-- Estado Badge --}}
                            <td class="px-6 py-4 text-center">
                                @if ($compra->estado === 'registrada')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Registrada
                                    </span>
                                @elseif ($compra->estado === 'anulada')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-500/10 dark:text-red-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Anulada
                                    </span>
                                @elseif ($compra->estado === 'devuelta')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-700 dark:bg-orange-500/10 dark:text-orange-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span> Devuelta
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('compras.show', $compra->id) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition-colors" title="Ver Detalles">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver
                                    </a>
                                    
                                    <a href="{{ route('compras.pdf', $compra->id) }}" target="_blank" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition-colors" title="Descargar PDF">
                                       <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                       PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-600">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm font-medium">No hay compras registradas</p>
                                    <a href="{{ route('compras.create') }}" class="text-sm text-brand-500 hover:underline">Registrar la primera</a>
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
@endsection
