@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Clientes" />



    <div x-data="{
        showCreate: false,
        showEdit: false,
        showView: false,
        showImport: false,
        editData: {},
        viewData: {},
        openEdit(data) {
            this.editData = data;
            this.showEdit = true;
        },
        openView(data) {
            this.viewData = data;
            this.showView = true;
        },
        tipoLabel(tipo) {
            const labels = {
                natural: 'Natural',
                juridico: 'Jurídico',
                extranjero: 'Extranjero',
                b2b: 'B2B',
                b2c: 'B2C',
            };
            return labels[tipo] || tipo;
        },
        selectedIds: [],
        selectAll: false,
        async bulkDelete(routeUrl) {
            if (this.selectedIds.length === 0) return;
            const count = this.selectedIds.length;
            window.Confirm.show(
                '¿Eliminar registros?',
                `Estás a punto de eliminar ${count} registro(s). Esta acción no se puede deshacer.`,
                'Sí, eliminar',
                'Cancelar',
                async () => {
                    try {
                        const response = await fetch(routeUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        const data = await response.json();
                        if (response.ok && data.success) { window.location.reload(); }
                        else { window.Notify.warning(data.message || 'No se pudieron eliminar los registros.'); }
                    } catch (error) {
                        window.Notify.failure('Hubo un problema al eliminar los registros.');
                    }
                },
                () => {},
                { okButtonBackground: '#ef4444' }
            );
        }
    }">

        {{-- ===== HEADER / TOOLBAR ===== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">Clientes</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gestiona la cartera de clientes del POS</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button @click="showImport = true"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Importar
                </button>

                <div x-data="{ openExport: false }" class="relative">
                    <button @click="openExport = !openExport"
                        class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Exportar
                        <svg class="w-4 h-4 transition-transform" :class="openExport ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="openExport" @click.outside="openExport = false"
                        class="absolute right-0 mt-2 w-40 origin-top-right rounded-xl border border-gray-100 bg-white shadow-lg dark:border-neutral-800 dark:bg-neutral-900 z-50">
                        <div class="p-1">
                            <a href="{{ route('clientes.exportar', 'excel') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2zM14 8V3.5L18.5 8H14z"/></svg>
                                Excel (.xlsx)
                            </a>
                            <a href="{{ route('clientes.exportar', 'pdf') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                                PDF (.pdf)
                            </a>
                        </div>
                    </div>
                </div>

                <button @click="showCreate = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Cliente
                </button>
            </div>
        </div>

        {{-- ===== BULK ACTIONS BANNER ===== --}}
        <div x-show="selectedIds.length > 0" style="display: none;" 
            class="mb-4 flex items-center justify-between rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 dark:border-brand-500/20 dark:bg-brand-500/10 transition-all">
            <div class="flex items-center gap-2 text-brand-700 dark:text-brand-400 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="`${selectedIds.length} clientes seleccionados`"></span>
            </div>
            
            <button type="button" @click="bulkDelete('{{ route('clientes.bulk_destroy') }}')"
                class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-600 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Eliminar Seleccionados
            </button>
        </div>


        {{-- ===== TABLA ===== --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-neutral-800/80 bg-gray-50 dark:bg-neutral-800/20">
                            <th class="px-6 py-4 w-10 text-center">
                                <input type="checkbox" x-model="selectAll" @change="selectedIds = selectAll ? {{ $clientes->pluck('id') }} : []" 
                                    class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-neutral-600 dark:bg-neutral-800 dark:checked:bg-brand-500">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Nombre / Empresa</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Documento</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Teléfono</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Crédito</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/80">
                        @forelse ($clientes as $cliente)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors" :class="selectedIds.includes({{ $cliente->id }}) ? 'bg-brand-50 dark:bg-brand-500/5' : ''">
                                <td class="px-6 py-4 w-10 text-center">
                                    <input type="checkbox" value="{{ $cliente->id }}" x-model="selectedIds" 
                                        class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-neutral-600 dark:bg-neutral-800 dark:checked:bg-brand-500">
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $cliente->id }}</td>

                                {{-- Tipo Badge --}}
                                <td class="px-6 py-4">
                                    @php
                                        $badgeColors = [
                                            'natural'    => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                            'juridico'   => 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
                                            'extranjero' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                            'b2b'        => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                            'b2c'        => 'bg-pink-50 text-pink-700 dark:bg-pink-500/10 dark:text-pink-400',
                                        ];
                                        $tipoLabels = [
                                            'natural'    => 'Natural',
                                            'juridico'   => 'Jurídico',
                                            'extranjero' => 'Extranjero',
                                            'b2b'        => 'B2B',
                                            'b2c'        => 'B2C',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeColors[$cliente->tipo_cliente] ?? 'bg-gray-100 text-gray-600 dark:bg-neutral-800 dark:text-gray-300' }}">
                                        {{ $tipoLabels[$cliente->tipo_cliente] ?? $cliente->tipo_cliente }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-white/90">
                                    {{ $cliente->nombre_completo }}
                                </td>

                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs">
                                    {{ $cliente->documento_principal }}
                                </td>

                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $cliente->telefono ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $cliente->email ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-right text-gray-800 dark:text-white/90 font-medium">
                                    ${{ number_format($cliente->limite_credito, 2) }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($cliente->estado)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-500 dark:bg-neutral-800/40 dark:text-gray-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span> Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                            @click='openView({
                                                id: {{ $cliente->id }},
                                                tipo_cliente: @json($cliente->tipo_cliente),
                                                nombre: @json($cliente->nombre),
                                                apellido: @json($cliente->apellido),
                                                empresa: @json($cliente->empresa),
                                                cedula: @json($cliente->cedula),
                                                ruc: @json($cliente->ruc),
                                                dv: @json($cliente->dv),
                                                pasaporte: @json($cliente->pasaporte),
                                                telefono: @json($cliente->telefono),
                                                email: @json($cliente->email),
                                                direccion: @json($cliente->direccion),
                                                provincia: @json($cliente->provincia),
                                                distrito: @json($cliente->distrito),
                                                pais: @json($cliente->pais),
                                                limite_credito: @json($cliente->limite_credito),
                                                notas: @json($cliente->notas),
                                                estado: {{ $cliente->estado ? 'true' : 'false' }},
                                                nombre_completo: @json($cliente->nombre_completo),
                                                documento_principal: @json($cliente->documento_principal),
                                                tipo_documento_dian_id: {{ $cliente->tipo_documento_dian_id ?? 'null' }},
                                                tipo_organizacion_dian_id: {{ $cliente->tipo_organizacion_dian_id ?? 'null' }},
                                                tributo_dian_id: {{ $cliente->tributo_dian_id ?? 'null' }},
                                                municipio_dian_id: {{ $cliente->municipio_dian_id ?? 'null' }}
                                            })'
                                            class="inline-flex items-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 hover:text-gray-700 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 dark:hover:bg-neutral-700 transition-colors"
                                            title="Ver detalle">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>

                                        {{-- Ficha CRM --}}
                                        <a href="{{ route('clientes.show', $cliente->id) }}"
                                            class="inline-flex items-center rounded-lg border border-brand-200 bg-brand-50 p-2 text-brand-600 hover:bg-brand-100 dark:border-brand-500/20 dark:bg-brand-500/10 dark:text-brand-400 transition-colors"
                                            title="Ficha CRM">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </a>

                                        {{-- Editar --}}
                                        <button type="button"
                                            @click='openEdit({
                                                id: {{ $cliente->id }},
                                                tipo_cliente: @json($cliente->tipo_cliente),
                                                nombre: @json($cliente->nombre),
                                                apellido: @json($cliente->apellido),
                                                empresa: @json($cliente->empresa),
                                                cedula: @json($cliente->cedula),
                                                ruc: @json($cliente->ruc),
                                                dv: @json($cliente->dv),
                                                pasaporte: @json($cliente->pasaporte),
                                                telefono: @json($cliente->telefono),
                                                email: @json($cliente->email),
                                                direccion: @json($cliente->direccion),
                                                provincia: @json($cliente->provincia),
                                                distrito: @json($cliente->distrito),
                                                pais: @json($cliente->pais),
                                                limite_credito: @json($cliente->limite_credito),
                                                notas: @json($cliente->notas),
                                                estado: {{ $cliente->estado ? 'true' : 'false' }},
                                                tipo_documento_dian_id: {{ $cliente->tipo_documento_dian_id ?? 'null' }},
                                                tipo_organizacion_dian_id: {{ $cliente->tipo_organizacion_dian_id ?? 'null' }},
                                                tributo_dian_id: {{ $cliente->tributo_dian_id ?? 'null' }},
                                                municipio_dian_id: {{ $cliente->municipio_dian_id ?? 'null' }}
                                            })'
                                            class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:border-blue-800/30 dark:bg-blue-500/10 dark:text-blue-400 transition-colors"
                                            title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        {{-- Eliminar --}}
                                        <button type="button"
                                            onclick="confirmarEliminar({{ $cliente->id }}, '{{ addslashes($cliente->nombre_completo) }}')"
                                            class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:border-red-800/30 dark:bg-red-500/10 dark:text-red-400 transition-colors"
                                            title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-600">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <p class="text-sm font-medium">No hay clientes registrados</p>
                                        <button @click="showCreate = true" class="text-sm text-brand-500 hover:underline">Agregar el primero</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-white/5">
                {{ $clientes->links() }}
            </div>
        </div>

        {{-- ====================  MODAL CREAR  ==================== --}}
        <div x-show="showCreate" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showCreate = false" style="display:none">

            <div @click.outside="showCreate = false"
                class="no-scrollbar relative w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 dark:bg-neutral-900 mx-4 max-h-[92vh] lg:p-10">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Nuevo Cliente</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Completa la información del cliente</p>
                    </div>
                    <button @click="showCreate = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf
                    @include('pages.clientes._form', ['prefix' => 'create'])
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showCreate = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 transition-colors">Cancelar</button>
                        <button type="submit"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================  MODAL EDITAR  ==================== --}}
        <div x-show="showEdit" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showEdit = false" style="display:none">

            <div @click.outside="showEdit = false"
                class="no-scrollbar relative w-full max-w-2xl overflow-y-auto rounded-3xl bg-white p-6 dark:bg-neutral-900 mx-4 max-h-[92vh] lg:p-10">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Editar Cliente</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="'ID: ' + editData.id"></p>
                    </div>
                    <button @click="showEdit = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="`/clientes/${editData.id}`" method="POST">
                    @csrf
                    @method('PUT')
                    @include('pages.clientes._form', ['prefix' => 'edit', 'edit' => true])
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showEdit = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 transition-colors">Cancelar</button>
                        <button type="submit"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">Actualizar Cliente</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================  MODAL VER  ==================== --}}
        <div x-show="showView" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showView = false" style="display:none">

            <div @click.outside="showView = false"
                class="no-scrollbar relative w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-6 dark:bg-neutral-900 mx-4 max-h-[90vh] lg:p-8">

                <div class="mb-6 flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-500/10">
                            <svg class="h-7 w-7 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90" x-text="viewData.nombre_completo"></h3>
                            <span class="text-xs font-semibold"
                                :class="{
                                    'text-blue-600': viewData.tipo_cliente === 'natural',
                                    'text-purple-600': viewData.tipo_cliente === 'juridico',
                                    'text-orange-600': viewData.tipo_cliente === 'extranjero',
                                    'text-emerald-600': viewData.tipo_cliente === 'b2b',
                                    'text-pink-600': viewData.tipo_cliente === 'b2c',
                                }" x-text="tipoLabel(viewData.tipo_cliente)"></span>
                        </div>
                    </div>
                    <button @click="showView = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-if="viewData.documento_principal && viewData.documento_principal !== '—'">
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-neutral-800/20 px-4 py-2.5">
                            <span class="text-sm text-gray-500">Documento</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="viewData.documento_principal"></span>
                        </div>
                    </template>
                    <template x-if="viewData.telefono">
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-neutral-800/20 px-4 py-2.5">
                            <span class="text-sm text-gray-500">Teléfono</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="viewData.telefono"></span>
                        </div>
                    </template>
                    <template x-if="viewData.email">
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-neutral-800/20 px-4 py-2.5">
                            <span class="text-sm text-gray-500">Email</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="viewData.email"></span>
                        </div>
                    </template>
                    <template x-if="viewData.provincia || viewData.distrito">
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-neutral-800/20 px-4 py-2.5">
                            <span class="text-sm text-gray-500">Ubicación</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90" x-text="[viewData.distrito, viewData.provincia, viewData.pais].filter(Boolean).join(', ')"></span>
                        </div>
                    </template>
                    <template x-if="viewData.direccion">
                        <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-neutral-800/20 px-4 py-2.5">
                            <span class="text-sm text-gray-500">Dirección</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90 text-right max-w-[60%]" x-text="viewData.direccion"></span>
                        </div>
                    </template>
                    <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-neutral-800/20 px-4 py-2.5">
                        <span class="text-sm text-gray-500">Límite de Crédito</span>
                        <span class="text-sm font-semibold text-gray-800 dark:text-white/90" x-text="'$' + parseFloat(viewData.limite_credito || 0).toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between rounded-xl bg-gray-50 dark:bg-neutral-800/20 px-4 py-2.5">
                        <span class="text-sm text-gray-500">Estado</span>
                        <span class="inline-flex items-center gap-1 text-sm font-semibold"
                            :class="viewData.estado ? 'text-emerald-600' : 'text-gray-400'"
                            x-text="viewData.estado ? 'Activo' : 'Inactivo'"></span>
                    </div>
                    <template x-if="viewData.notas">
                        <div class="rounded-xl bg-gray-50 dark:bg-neutral-800/20 px-4 py-2.5">
                            <span class="text-sm text-gray-500">Notas</span>
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300" x-text="viewData.notas"></p>
                        </div>
                    </template>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button @click="showView = false; openEdit(viewData)"
                        class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                        Editar
                    </button>
                </div>
            </div>
        </div>

        {{-- ====================  MODAL IMPORTAR  ==================== --}}
        <div x-show="showImport" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showImport = false" style="display:none">

            <div @click.outside="showImport = false"
                class="no-scrollbar relative w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-6 dark:bg-neutral-900 mx-4 max-h-[90vh] lg:p-8">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Importar Clientes</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Carga masiva de clientes desde Excel</p>
                    </div>
                    <button @click="showImport = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('clientes.importar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div class="rounded-xl border-2 border-dashed border-gray-200 p-8 text-center dark:border-neutral-700">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <label for="archivo_import_cli" class="mt-4 block text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                <span class="text-brand-500 hover:text-brand-600">Selecciona un archivo</span> o arrastra y suelta
                                <input id="archivo_import_cli" name="archivo" type="file" class="sr-only" required accept=".xlsx,.xls,.csv">
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Excel (.xlsx, .xls) o CSV hasta 5MB</p>
                            <div id="file-name-display-cli" class="mt-2 text-sm text-brand-600 font-medium hidden"></div>
                        </div>

                        <div class="rounded-xl bg-blue-50 p-4 dark:bg-blue-500/10">
                            <div class="flex gap-3">
                                <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300">¿No tienes el formato?</h4>
                                    <p class="mt-1 text-xs text-blue-700 dark:text-blue-400">Descarga nuestra plantilla para asegurar que los datos se carguen correctamente.</p>
                                    <a href="{{ route('clientes.plantilla') }}" class="mt-2 inline-flex items-center text-xs font-bold text-blue-800 dark:text-blue-300 hover:underline">
                                        Descargar Plantilla CSV
                                        <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-neutral-800">
                        <button type="button" @click="showImport = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 transition-colors">Cancelar</button>
                        <button type="submit"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">Iniciar Importación</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('archivo_import_cli')?.addEventListener('change', function(e) {
                    const fileName = e.target.files[0]?.name;
                    const display = document.getElementById('file-name-display-cli');
                    if (fileName && display) {
                        display.textContent = 'Archivo seleccionado: ' + fileName;
                        display.classList.remove('hidden');
                    }
                });
            });
        </script>


        {{-- Formulario oculto para eliminar --}}
        <form id="delete-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

    </div>

    @push('scripts')
        <script>
            function confirmarEliminar(id, nombre) {
                window.Confirm.show(
                    '¿Eliminar cliente?',
                    `Estás a punto de eliminar a "${nombre}". Esta acción no se puede deshacer.`,
                    'Sí, eliminar',
                    'Cancelar',
                    () => {
                        const form = document.getElementById('delete-form');
                        form.action = `/clientes/${id}`;
                        form.submit();
                    },
                    () => {},
                    { okButtonBackground: '#ef4444' }
                );
            }
        </script>
    @endpush
@endsection
