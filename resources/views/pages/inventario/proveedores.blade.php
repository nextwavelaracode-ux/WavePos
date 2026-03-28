@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Proveedores" />

@if(session('sweet_alert'))
    @php $sa = session('sweet_alert'); @endphp
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

<div x-data="{
    showCreate: false,
    showEdit: false,
    showImport: false,
    editData: {},
    openEdit(data) {
        this.editData = data;
        this.showEdit = true;
    },
    selectedIds: [],
    selectAll: false,
    async bulkDelete(routeUrl) {
        if (this.selectedIds.length === 0) return;
        
        const result = await Swal.fire({
            title: '¿Eliminar registros?',
            html: `<p class='text-gray-500'>Estás a punto de eliminar <strong>${this.selectedIds.length}</strong> registros.<br>Esta acción no se puede deshacer.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(routeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: this.selectedIds })
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    window.location.reload();
                } else {
                    Swal.fire('Atención', data.message || 'No se pudieron eliminar los registros. Verifique que no tengan datos dependientes.', 'warning');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema al eliminar los registros.', 'error');
            }
        }
    }
}">

    {{-- Header con botones de acción --}}
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Proveedores</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Directorio de proveedores y empresas asociadas</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <button @click="showImport = true"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Importar
            </button>

            <div x-data="{ openExport: false }" class="relative">
                <button @click="openExport = !openExport"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exportar
                    <svg class="w-4 h-4 transition-transform" :class="openExport ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openExport" @click.outside="openExport = false"
                    class="absolute right-0 mt-2 w-40 origin-top-right rounded-xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50">
                    <div class="p-1">
                        <a href="{{ route('inventario.proveedores.exportar', 'excel') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2zM14 8V3.5L18.5 8H14z"/></svg>
                            Excel (.xlsx)
                        </a>
                        <a href="{{ route('inventario.proveedores.exportar', 'pdf') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                            PDF (.pdf)
                        </a>
                    </div>
                </div>
            </div>

            <button @click="showCreate = true; $nextTick(() => document.getElementById('create-empresa').focus())"
                class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Proveedor
            </button>
        </div>
    </div>


    {{-- ===== BULK ACTIONS BANNER ===== --}}
    <div x-show="selectedIds.length > 0" style="display: none;" 
        class="mb-4 flex items-center justify-between rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 dark:border-brand-500/20 dark:bg-brand-500/10 transition-all">
        <div class="flex items-center gap-2 text-brand-700 dark:text-brand-400 font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span x-text="`${selectedIds.length} proveedores seleccionados`"></span>
        </div>
        
        <button type="button" @click="bulkDelete('{{ route('inventario.proveedores.bulk_destroy') }}')"
            class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-600 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Eliminar Seleccionados
        </button>
    </div>

    {{-- Tabla --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-4 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="selectedIds = selectAll ? {{ $proveedores->pluck('id') }} : []" 
                                class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:checked:bg-brand-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Empresa / Contacto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Identificación (RUC)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Contacto Directo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ubicación</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($proveedores as $i => $proveedor)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors" :class="selectedIds.includes({{ $proveedor->id }}) ? 'bg-brand-50 dark:bg-brand-500/5' : ''">
                        <td class="px-6 py-4 w-10 text-center">
                            <input type="checkbox" value="{{ $proveedor->id }}" x-model="selectedIds" 
                                class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:checked:bg-brand-500">
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-brand-500 font-semibold">{{ strtoupper(substr($proveedor->empresa, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $proveedor->empresa }}</p>
                                    @if($proveedor->contacto)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Atención: {{ $proveedor->contacto }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            @if($proveedor->ruc)
                                {{ $proveedor->ruc }} {{ $proveedor->dv ? '- '.$proveedor->dv : '' }}
                            @else
                                <span class="text-gray-400 dark:text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            @if($proveedor->telefono || $proveedor->email)
                                <div class="flex flex-col gap-0.5">
                                    @if($proveedor->telefono)<span>{{ $proveedor->telefono }}</span>@endif
                                    @if($proveedor->email)<span class="text-xs text-gray-500">{{ $proveedor->email }}</span>@endif
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            @if($proveedor->ciudad || $proveedor->pais)
                                {{ $proveedor->ciudad }}{{ $proveedor->ciudad && $proveedor->pais ? ', ' : '' }}{{ $proveedor->pais }}
                            @else
                                <span class="text-gray-400 dark:text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($proveedor->estado)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700 dark:bg-green-500/10 dark:text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-500/10 dark:text-red-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click='openEdit({
                                        id: {{ $proveedor->id }},
                                        empresa: "{{ addslashes($proveedor->empresa) }}",
                                        ruc: "{{ addslashes($proveedor->ruc ?? '') }}",
                                        dv: "{{ addslashes($proveedor->dv ?? '') }}",
                                        contacto: "{{ addslashes($proveedor->contacto ?? '') }}",
                                        telefono: "{{ addslashes($proveedor->telefono ?? '') }}",
                                        email: "{{ addslashes($proveedor->email ?? '') }}",
                                        direccion: "{{ addslashes($proveedor->direccion ?? '') }}",
                                        provincia: "{{ addslashes($proveedor->provincia ?? '') }}",
                                        ciudad: "{{ addslashes($proveedor->ciudad ?? '') }}",
                                        pais: "{{ addslashes($proveedor->pais ?? '') }}",
                                        notas: "{{ addslashes(str_replace("\n", " ", $proveedor->notas ?? '')) }}",
                                        estado: {{ $proveedor->estado ? 'true' : 'false' }}
                                    })'
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.05] transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </button>
                                <button type="button" onclick="confirmarEliminar({{ $proveedor->id }}, '{{ addslashes($proveedor->empresa) }}')"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-800/30 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-500/10 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">No hay proveedores registrados</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== MODAL CREAR ===== --}}
    <div x-show="showCreate" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
        @keydown.escape.window="showCreate = false" style="display:none">

        <div @click.outside="showCreate = false"
            class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-10 mx-4 max-h-[90vh]">
            
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Nuevo Proveedor</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ingresa los datos fiscales y de contacto</p>
                </div>
                <button @click="showCreate = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('inventario.proveedores.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    
                    {{-- Empresa --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Razón Social / Empresa <span class="text-red-500">*</span></label>
                        <input type="text" name="empresa" id="create-empresa" required
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- RUC --}}
                    <div class="sm:col-span-1 lg:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">RUC</label>
                        <input type="text" name="ruc"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- DV --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">DV</label>
                        <input type="text" name="dv"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- Contacto --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Atención / Representante</label>
                        <input type="text" name="contacto"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- Teléfono --}}
                    <div class="lg:col-span-1">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Teléfono</label>
                        <input type="text" name="telefono"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- Email --}}
                    <div class="sm:col-span-2 lg:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Email</label>
                        <input type="email" name="email"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- Dirección --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Dirección</label>
                        <input type="text" name="direccion"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    <div class="lg:col-span-1">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">País</label>
                        <input type="text" name="pais"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>
                    
                    <div class="lg:col-span-1">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Provincia</label>
                        <input type="text" name="provincia"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    <div class="lg:col-span-1">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Ciudad</label>
                        <input type="text" name="ciudad"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- Notas --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Notas / Comentarios</label>
                        <textarea name="notas" rows="2"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500"></textarea>
                    </div>

                    {{-- Estado --}}
                    <div class="sm:col-span-2 lg:col-span-3 pt-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="estado" value="1" class="sr-only group" checked>
                                <div class="block h-6 w-10 rounded-full bg-gray-300 dark:bg-gray-700 transition"></div>
                                <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-full peer-checked:bg-white"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90">Proveedor Activo</span>
                        </label>
                    </div>

                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="showCreate = false" class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03] transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                        Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ===== MODAL EDITAR ===== --}}
    <div x-show="showEdit" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
        @keydown.escape.window="showEdit = false" style="display:none">

        <div @click.outside="showEdit = false"
            class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-10 mx-4 max-h-[90vh]">
            
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Editar Proveedor</h3>
                </div>
                <button @click="showEdit = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="`/inventario/proveedores/${editData.id}`" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    
                    {{-- Empresa --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Razón Social / Empresa <span class="text-red-500">*</span></label>
                        <input type="text" name="empresa" x-model="editData.empresa" required
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- RUC --}}
                    <div class="sm:col-span-1 lg:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">RUC</label>
                        <input type="text" name="ruc" x-model="editData.ruc"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- DV --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">DV</label>
                        <input type="text" name="dv" x-model="editData.dv"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- Contacto --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Atención / Representante</label>
                        <input type="text" name="contacto" x-model="editData.contacto"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- Teléfono --}}
                    <div class="lg:col-span-1">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Teléfono</label>
                        <input type="text" name="telefono" x-model="editData.telefono"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- Email --}}
                    <div class="sm:col-span-2 lg:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Email</label>
                        <input type="email" name="email" x-model="editData.email"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- Dirección --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Dirección</label>
                        <input type="text" name="direccion" x-model="editData.direccion"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    <div class="lg:col-span-1">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">País</label>
                        <input type="text" name="pais" x-model="editData.pais"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>
                    
                    <div class="lg:col-span-1">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Provincia</label>
                        <input type="text" name="provincia" x-model="editData.provincia"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    <div class="lg:col-span-1">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Ciudad</label>
                        <input type="text" name="ciudad" x-model="editData.ciudad"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                    </div>

                    {{-- Notas --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Notas / Comentarios</label>
                        <textarea name="notas" x-model="editData.notas" rows="2"
                            class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500"></textarea>
                    </div>

                    {{-- Estado --}}
                    <div class="sm:col-span-2 lg:col-span-3 pt-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="estado" value="1" x-model="editData.estado" class="sr-only">
                                <div class="block h-6 w-10 rounded-full bg-gray-300 dark:bg-gray-700 transition" :class="editData.estado ? 'bg-brand-500 dark:bg-brand-500' : ''"></div>
                                <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition" :class="editData.estado ? 'translate-x-[100%]' : ''"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-800 dark:text-white/90">Proveedor Activo</span>
                        </label>
                    </div>

                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="showEdit = false" class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03] transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                        Actualizar Proveedor
                    </button>
                </div>
            </form>
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
            class="no-scrollbar relative w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 mx-4 max-h-[90vh] lg:p-8">

            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Importar Proveedores</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Carga masiva de proveedores desde Excel</p>
                </div>
                <button @click="showImport = false" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('inventario.proveedores.importar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div class="rounded-xl border-2 border-dashed border-gray-200 p-8 text-center dark:border-gray-700">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <label for="archivo_import_prov" class="mt-4 block text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            <span class="text-brand-500 hover:text-brand-600">Selecciona un archivo</span> o arrastra y suelta
                            <input id="archivo_import_prov" name="archivo" type="file" class="sr-only" required accept=".xlsx,.xls,.csv">
                        </label>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Excel (.xlsx, .xls) o CSV hasta 5MB</p>
                        <div id="file-name-display-prov" class="mt-2 text-sm text-brand-600 font-medium hidden"></div>
                    </div>

                    <div class="rounded-xl bg-blue-50 p-4 dark:bg-blue-500/10">
                        <div class="flex gap-3">
                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300">¿No tienes el formato?</h4>
                                <p class="mt-1 text-xs text-blue-700 dark:text-blue-400">Descarga nuestra plantilla para asegurar que los datos se carguen correctamente.</p>
                                <a href="{{ route('inventario.proveedores.plantilla') }}" class="mt-2 inline-flex items-center text-xs font-bold text-blue-800 dark:text-blue-300 hover:underline">
                                    Descargar Plantilla CSV
                                    <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" @click="showImport = false"
                        class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors">Cancelar</button>
                    <button type="submit"
                        class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">Iniciar Importación</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('archivo_import_prov')?.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name;
                const display = document.getElementById('file-name-display-prov');
                if (fileName && display) {
                    display.textContent = 'Archivo seleccionado: ' + fileName;
                    display.classList.remove('hidden');
                }
            });
        });
    </script>


</div>

{{-- Formulario oculto para eliminar --}}
<form id="delete-form" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmarEliminar(id, nombre) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminará el proveedor '" + nombre + "'. ¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-form');
                form.action = '/inventario/proveedores/' + id;
                form.submit();
            }
        });
    }
</script>

<style>
/* Estilos para el toggle alpine */
input:checked ~ .dot {
  transform: translateX(100%);
}
input:checked ~ .block {
  background-color: #465dff; /* Brand color */
}
</style>
@endpush
@endsection
