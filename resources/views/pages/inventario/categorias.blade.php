@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Categorías" />

    <div x-data="{
        showCreate: false,
        showEdit: false,
        showCreateSub: false,
        showImport: false,
        keepAddingSub: true,
        editData: {},
        subData: {
            parent_id: '',
            impuesto: '7'
        },
        openEdit(data) {
            this.editData = data;
            this.showEdit = true;
        },
        openCreateSub(parentId, impuestoParent) {
            this.subData.parent_id = parentId;
            this.subData.impuesto = impuestoParent || '7';
            this.showCreateSub = true;
            this.$nextTick(() => {
                const inputNombre = document.getElementById('sub-nombre');
                if (inputNombre) inputNombre.focus();
            });
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
                        else { window.Notify.warning(data.message || 'No se pudieron eliminar los registros. Verifique que no tengan datos dependientes.'); }
                    } catch (error) {
                        window.Notify.failure('Hubo un problema al eliminar los registros.');
                    }
                },
                () => {},
                { okButtonBackground: '#ef4444' }
            );
        }
    }">

        {{-- Header con botón crear --}}
        <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Categorías</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Organiza tu inventario y gestiona los impuestos
                    (ITBMS)</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                {{-- Botón Importar --}}
                <button @click="showImport = true"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-white/[0.03] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Importar
                </button>

                {{-- Botón Exportar (Dropdown simple o Botón directo) --}}
                <div x-data="{ openExport: false }" class="relative">
                    <button @click="openExport = !openExport"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-white/[0.03] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Exportar
                        <svg class="w-4 h-4" :class="openExport ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openExport" @click.outside="openExport = false"
                        class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-200 bg-white py-2 shadow-lg dark:border-neutral-700 dark:bg-neutral-800 z-50">
                        <a href="{{ route('inventario.categorias.exportar', 'excel') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Exportar a Excel
                        </a>
                        <a href="{{ route('inventario.categorias.exportar', 'pdf') }}" target="_blank"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Exportar a PDF
                        </a>
                    </div>
                </div>

                <button @click="showCreate = true; $nextTick(() => document.getElementById('create-nombre').focus())"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Categoría
                </button>
            </div>
        </div>

        {{-- ===== BULK ACTIONS BANNER ===== --}}
        <div x-show="selectedIds.length > 0" style="display: none;" 
            class="mb-4 flex items-center justify-between rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 dark:border-brand-500/20 dark:bg-brand-500/10 transition-all">
            <div class="flex items-center gap-2 text-brand-700 dark:text-brand-400 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="`${selectedIds.length} categorías seleccionadas`"></span>
            </div>
            
            <button type="button" @click="bulkDelete('{{ route('inventario.categorias.bulk_destroy') }}')"
                class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-600 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Eliminar Seleccionados
            </button>
        </div>

        {{-- Tabla --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-neutral-800 dark:bg-neutral-800/20">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-neutral-700">
                            <th class="px-6 py-4 w-10 text-center">
                                <input type="checkbox" x-model="selectAll" @change="selectedIds = selectAll ? {{ $categorias->pluck('id') }} : []" 
                                    class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-neutral-600 dark:bg-neutral-800 dark:checked:bg-brand-500">
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                #</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Imagen</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Nombre / Detalle</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Jerarquía</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Impuesto</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Orden</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Estado</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                        @forelse($categorias as $i => $categoria)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors" :class="selectedIds.includes({{ $categoria->id }}) ? 'bg-brand-50 dark:bg-brand-500/5' : ''">
                                <td class="px-6 py-4 w-10 text-center">
                                    <input type="checkbox" value="{{ $categoria->id }}" x-model="selectedIds" 
                                        class="rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-neutral-600 dark:bg-neutral-800 dark:checked:bg-brand-500">
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-6 py-4">
                                    @if ($categoria->imagen)
                                        <img src="{{ asset('storage/' . $categoria->imagen) }}"
                                            alt="{{ $categoria->nombre }}"
                                            class="w-12 h-12 rounded object-cover border border-gray-200 dark:border-neutral-700">
                                    @else
                                        <div
                                            class="w-12 h-12 pl-2.5 rounded bg-gray-100 dark:bg-neutral-800 flex items-center justify-center border border-gray-200 dark:border-neutral-700">
                                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $categoria->nombre }}
                                    </p>
                                    @if ($categoria->detalle)
                                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[200px]">
                                            {{ $categoria->detalle }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    @if ($categoria->parent)
                                        <span
                                            class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-neutral-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-neutral-700">
                                            Subcategoría de: <strong>{{ $categoria->parent->nombre }}</strong>
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">Categoría Principal</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $badgeClass = match ($categoria->impuesto) {
                                            '15' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                            '10'
                                                => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
                                            '7' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                            '0'
                                                => 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                                            default
                                                => 'bg-gray-50 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $badgeClass }}">
                                        {{ $categoria->impuesto }}% ITBMS
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-medium text-gray-600 dark:text-gray-300">
                                    {{ $categoria->orden_visualizacion }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($categoria->estado)
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700 dark:bg-green-500/10 dark:text-green-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-500/10 dark:text-red-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if (is_null($categoria->parent_id))
                                            <button type="button"
                                                @click="openCreateSub({{ $categoria->id }}, '{{ $categoria->impuesto }}')"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-brand-200 bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-100 dark:border-brand-800/30 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500/20 transition-colors"
                                                title="Añadir subcategoría">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Subcategoría
                                            </button>
                                        @endif
                                        <button
                                            @click='openEdit({
                                        id: {{ $categoria->id }},
                                        nombre: "{{ addslashes($categoria->nombre) }}",
                                        descripcion: "{{ addslashes($categoria->descripcion ?? '') }}",
                                        parent_id: "{{ $categoria->parent_id ?? '' }}",
                                        impuesto: "{{ $categoria->impuesto }}",
                                        unidad_medida: "{{ addslashes($categoria->unidad_medida ?? '') }}",
                                        ubicacion: "{{ addslashes($categoria->ubicacion ?? '') }}",
                                        atributos_tecnicos: "{{ addslashes($categoria->atributos_tecnicos ?? '') }}",
                                        detalle: "{{ addslashes($categoria->detalle ?? '') }}",
                                        orden_visualizacion: {{ $categoria->orden_visualizacion }},
                                        estado: {{ $categoria->estado ? 'true' : 'false' }}
                                    })'
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 dark:hover:bg-white/[0.05] transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Editar
                                        </button>
                                        <button type="button"
                                            onclick="confirmarEliminar({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}')"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-800/30 dark:bg-neutral-800 dark:text-red-400 dark:hover:bg-red-500/10 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay categorías registradas
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-white/5">
                {{ $categorias->links() }}
            </div>
        </div>

        {{-- ===== MODAL CREAR ===== --}}
        <div x-show="showCreate" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showCreate = false" style="display:none">

            <div @click.outside="showCreate = false"
                class="no-scrollbar relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-neutral-900 lg:p-10 mx-4 max-h-[90vh]">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Nueva Categoría</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Registra una nueva categoría e impuestos
                            sugeridos</p>
                    </div>
                    <button @click="showCreate = false"
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('inventario.categorias.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Nombre --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Nombre de
                                Categoría <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" id="create-nombre" required
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500"
                                placeholder="Ej. Bebidas, Ferretería...">
                        </div>

                        {{-- Categoría Padre --}}
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <label class="block text-sm font-medium text-gray-800 dark:text-white/90">Categoría Padre
                                    (Subcategoría de)</label>
                            </div>
                            <select name="parent_id" id="create-parent-id"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                                <option value="">Ninguna (Categoría Principal)</option>
                                @foreach ($padres as $padre)
                                    <option value="{{ $padre->id }}">{{ $padre->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Impuesto ITBMS --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Impuesto
                                Sugerido (ITBMS Panam\u00e1) <span class="text-red-500">*</span></label>
                            <select name="impuesto" required
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                                <option value="7">7% (General)</option>
                                <option value="10">10% (Licores)</option>
                                <option value="15">15% (Cigarrillos)</option>
                                <option value="0">0% (Exento)</option>
                            </select>
                        </div>

                        {{-- Unidades de Medida --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Unidad de Medida
                                (Defecto)</label>
                            <select name="unidad_medida"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                                <option value="">Seleccione...</option>
                                <option value="Global (Par/Set)">Global (Par/Set) - Guantes, juegos</option>
                                <option value="Longitud (Pie/Metro)">Longitud (Pie/Metro) - Cables, tubos</option>
                                <option value="Peso (Lb/Kg)">Peso (Lb/Kg) - Clavos, alambre</option>
                                <option value="Volumen (Galón/Cuñete)">Volumen (Galón/Cuñete) - Pinturas</option>
                                <option value="Litro (Ltr)">Litro (Ltr) - Líquidos, aceites</option>
                                <option value="Paquete (Pqte)">Paquete (Pqte) - Tornillos, empaques</option>
                                <option value="Unidad (Und)">Unidad (Und) - Piezas individuales</option>
                                <option value="Otro">Otro/Vario</option>
                            </select>
                        </div>

                        {{-- Ubicación --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Ubicación
                                (Pasillo/Estante)</label>
                            <input type="text" name="ubicacion"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500"
                                placeholder="Ej. Pasillo 4 - Estante B">
                        </div>

                        {{-- Atributos Técnicos --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Atributos
                                Técnicos requeridos</label>
                            <input type="text" name="atributos_tecnicos"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500"
                                placeholder="Ej. Medida, Material, Voltaje (Separados por coma)">
                        </div>

                        {{-- Detalle Corto --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Detalle
                                Público</label>
                            <input type="text" name="detalle"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500"
                                placeholder="Ej. Productos para el hogar">
                        </div>

                        {{-- Descripción --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Descripción
                                Interna</label>
                            <textarea name="descripcion" rows="3"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500"
                                placeholder="Notas internas sobre esta categoría..."></textarea>
                        </div>

                        {{-- Imagen --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Imagen</label>
                            <input type="file" name="imagen" accept="image/*"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
                        </div>

                        {{-- Orden y Estado --}}
                        <div class="flex items-center gap-4">
                            <div class="w-1/2">
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Orden
                                    Visual</label>
                                <input type="number" name="orden_visualizacion" value="0" min="0" required
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                            </div>
                            <div class="w-1/2 pt-6">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" name="estado" value="1" class="sr-only group"
                                            checked>
                                        <div class="block h-6 w-10 rounded-full bg-gray-300 dark:bg-neutral-700 transition">
                                        </div>
                                        <div
                                            class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-full peer-checked:bg-white">
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">Estado Activo</span>
                                </label>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showCreate = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-white/[0.03] transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                            Guardar Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>


        {{-- ===== MODAL EDITAR ===== --}}
        <div x-show="showEdit" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showEdit = false" style="display:none">

            <div @click.outside="showEdit = false"
                class="no-scrollbar relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-neutral-900 lg:p-10 mx-4 max-h-[90vh]">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Editar Categoría</h3>
                    </div>
                    <button @click="showEdit = false"
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="`/inventario/categorias/${editData.id}`" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- Nombre --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Nombre de
                                Categoría <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" x-model="editData.nombre" required
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                        </div>

                        {{-- Categoría Padre --}}
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <label class="block text-sm font-medium text-gray-800 dark:text-white/90">Categoría
                                    Padre</label>
                            </div>
                            <select name="parent_id" id="edit-parent-id" x-model="editData.parent_id"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                                <option value="">Ninguna</option>
                                @foreach ($padres as $padre)
                                    <option value="{{ $padre->id }}">{{ $padre->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Impuesto --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Impuesto
                                Sugerido <span class="text-red-500">*</span></label>
                            <select name="impuesto" x-model="editData.impuesto" required
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                                <option value="7">7% (General)</option>
                                <option value="10">10% (Licores)</option>
                                <option value="15">15% (Cigarrillos)</option>
                                <option value="0">0% (Exento)</option>
                            </select>
                        </div>

                        {{-- Unidades de Medida --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Unidad de Medida
                                (Defecto)</label>
                            <select name="unidad_medida" x-model="editData.unidad_medida"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                                <option value="">Seleccione...</option>
                                <option value="Global (Par/Set)">Global (Par/Set) - Guantes, juegos</option>
                                <option value="Longitud (Pie/Metro)">Longitud (Pie/Metro) - Cables, tubos</option>
                                <option value="Peso (Lb/Kg)">Peso (Lb/Kg) - Clavos, alambre</option>
                                <option value="Volumen (Galón/Cuñete)">Volumen (Galón/Cuñete) - Pinturas</option>
                                <option value="Litro (Ltr)">Litro (Ltr) - Líquidos, aceites</option>
                                <option value="Paquete (Pqte)">Paquete (Pqte) - Tornillos, empaques</option>
                                <option value="Unidad (Und)">Unidad (Und) - Piezas individuales</option>
                                <option value="Otro">Otro/Vario</option>
                            </select>
                        </div>

                        {{-- Ubicación --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Ubicación
                                (Pasillo/Estante)</label>
                            <input type="text" name="ubicacion" x-model="editData.ubicacion"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500"
                                placeholder="Ej. Pasillo 4 - Estante B">
                        </div>

                        {{-- Atributos Técnicos --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Atributos
                                Técnicos requeridos</label>
                            <input type="text" name="atributos_tecnicos" x-model="editData.atributos_tecnicos"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500"
                                placeholder="Ej. Medida, Material, Voltaje (Separados por coma)">
                        </div>

                        {{-- Detalle Corto --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Detalle
                                Público</label>
                            <input type="text" name="detalle" x-model="editData.detalle"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                        </div>

                        {{-- Descripción --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Descripción
                                Interna</label>
                            <textarea name="descripcion" x-model="editData.descripcion" rows="3"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500"></textarea>
                        </div>

                        {{-- Imagen --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Reemplazar
                                Imagen</label>
                            <input type="file" name="imagen" accept="image/*"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
                        </div>

                        {{-- Orden y Estado --}}
                        <div class="flex items-center gap-4">
                            <div class="w-1/2">
                                <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Orden
                                    Visual</label>
                                <input type="number" name="orden_visualizacion" x-model="editData.orden_visualizacion"
                                    min="0" required
                                    class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                            </div>
                            <div class="w-1/2 pt-6">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" name="estado" value="1" x-model="editData.estado"
                                            class="sr-only">
                                        <div class="block h-6 w-10 rounded-full bg-gray-300 dark:bg-neutral-700 transition"
                                            :class="editData.estado ? 'bg-brand-500 dark:bg-brand-500' : ''"></div>
                                        <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition"
                                            :class="editData.estado ? 'translate-x-[100%]' : ''"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">Estado Activo</span>
                                </label>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showEdit = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-white/[0.03] transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                            Actualizar Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== MODAL CREAR SUBCATEGORÍA ===== --}}
        <div x-show="showCreateSub" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showCreateSub = false" style="display:none">

            <div @click.outside="showCreateSub = false"
                class="no-scrollbar relative w-full max-w-[600px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-neutral-900 lg:p-10 mx-4 max-h-[90vh]">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Nueva Subcategoría</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sigue agregando opciones rápidamente</p>
                    </div>
                    <button @click="showCreateSub = false"
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="form-subcategoria" onsubmit="guardarSubcategoriaAjax(event, this)">
                    @csrf
                    <input type="hidden" name="parent_id" x-model="subData.parent_id">
                    <input type="hidden" name="estado" value="1">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Nombre --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Nombre de
                                Subcategoría <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" id="sub-nombre" required
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500"
                                placeholder="Ej. Laptops, Smartphones, Herramientas manuales...">
                        </div>

                        {{-- Detalle Corto / Descripción Corta --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Descripción
                                Corta</label>
                            <input type="text" name="detalle"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500"
                                placeholder="Ej. Equipos portátiles y accesorios">
                        </div>

                        {{-- Impuesto ITBMS --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Impuesto
                                (ITBMS) <span class="text-red-500">*</span></label>
                            <select name="impuesto" x-model="subData.impuesto" required
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                                <option value="7">7% (General)</option>
                                <option value="10">10% (Licores)</option>
                                <option value="15">15% (Cigarrillos)</option>
                                <option value="0">0% (Exento)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Heredado del padre por defecto</p>
                        </div>

                        {{-- Orden de Visualización --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Orden
                                Visual</label>
                            <input type="number" name="orden_visualizacion" value="0" min="0" required
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500">
                        </div>

                        {{-- Imagen --}}
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Imagen /
                                Icono</label>
                            <input type="file" name="imagen" accept="image/*" id="sub-imagen"
                                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90 dark:focus:border-brand-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
                        </div>

                        {{-- Checkbox de mantener abierto --}}
                        <div
                            class="sm:col-span-2 mt-2 p-4 rounded-xl bg-brand-50 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-800/30">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <div class="relative pt-0.5">
                                    <input type="checkbox" x-model="keepAddingSub" class="sr-only group">
                                    <div class="block h-5 w-9 rounded-full bg-gray-300 dark:bg-neutral-700 transition"
                                        :class="keepAddingSub ? 'bg-brand-500 dark:bg-brand-500' : ''"></div>
                                    <div class="dot absolute left-1 top-1 h-3 w-3 rounded-full bg-white transition"
                                        :class="keepAddingSub ? 'translate-x-[100%]' : ''"></div>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-brand-900 dark:text-brand-300">¿Deseas seguir
                                        agregando subcategorías?</span>
                                    <span class="text-xs text-brand-700 dark:text-brand-400/80">El formulario no se cerrará
                                        para que puedas añadir de forma rápida (Laptops, Celulares, Tablets...).</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showCreateSub = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-white/[0.03] transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-submit-sub"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors flex items-center justify-center gap-2">
                            <span>Guardar Subcategoría</span>
                            <svg id="spinner-sub" class="w-4 h-4 animate-spin hidden" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== MODAL IMPORTAR ===== --}}
        <div x-show="showImport" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showImport = false" style="display:none">

            <div @click.outside="showImport = false"
                class="no-scrollbar relative w-full max-w-[500px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-neutral-900 lg:p-8 mx-4">

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Importar Categorías</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Carga masiva desde archivo Excel/CSV</p>
                    </div>
                    <button @click="showImport = false"
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('inventario.categorias.importar') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-5">
                        <div
                            class="rounded-xl border-2 border-dashed border-gray-200 p-8 text-center dark:border-neutral-700">
                            <input type="file" name="archivo" id="archivo-import" class="hidden" accept=".xlsx,.xls,.csv"
                                required onchange="updateFileName(this)">
                            <label for="archivo-import" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400" id="file-name">Haz clic para
                                    subir o arrastra un archivo</p>
                                <p class="text-xs text-gray-400 mt-1">Excel o CSV (Máx. 5MB)</p>
                            </label>
                        </div>

                        <div class="rounded-xl bg-blue-50 p-4 dark:bg-blue-500/10">
                            <div class="flex gap-3">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800 dark:text-blue-300">¿No tienes el
                                        formato?</p>
                                    <a href="{{ route('inventario.categorias.plantilla') }}"
                                        class="text-xs font-semibold text-blue-700 underline hover:text-blue-800 dark:text-blue-400">Descargar
                                        plantilla de ejemplo</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showImport = false"
                            class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-300 dark:hover:bg-white/[0.03] transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                            Iniciar Importación
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Formulario oculto para eliminar --}}
    <form id="delete-form" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            function updateFileName(input) {
                const fileName = input.files[0] ? input.files[0].name : "Haz clic para subir o arrastra un archivo";
                document.getElementById('file-name').textContent = fileName;
            }
            function crearPadreRapido(selectId, isAlpine = false) {
                // Usar mini modal inline ya que Notiflix no tiene input nativo
                const nombre = prompt('¿Nombre de la nueva categoría principal?');
                if (!nombre || !nombre.trim()) return;
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('nombre', nombre.trim());
                formData.append('impuesto', '7');
                formData.append('estado', '1');
                fetch('{{ route('inventario.categorias.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('select[name="parent_id"]').forEach(sel => {
                            const option = document.createElement('option');
                            option.value = data.categoria.id;
                            option.text = data.categoria.nombre;
                            sel.add(option);
                        });
                        const select = document.getElementById(selectId);
                        select.value = data.categoria.id;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        window.Notify.success('Categoría padre creada exitosamente.');
                    }
                })
                .catch(err => {
                    window.Notify.failure('No se pudo crear la categoría.');
                });
            }

            function guardarSubcategoriaAjax(event, form) {
                event.preventDefault();
                const btnSubmit = document.getElementById('btn-submit-sub');
                const spinner = document.getElementById('spinner-sub');
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-70', 'cursor-not-allowed');
                spinner.classList.remove('hidden');
                const formData = new FormData(form);
                formData.append('_token', '{{ csrf_token() }}');
                fetch('{{ route('inventario.categorias.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.Notify.success('Subcategoría agregada exitosamente.');
                        const alpineContext = Alpine.$data(document.querySelector('[x-data]'));
                        if (alpineContext.keepAddingSub) {
                            form.reset();
                            document.querySelector('input[name="parent_id"]').value = alpineContext.subData.parent_id;
                            document.getElementById('sub-nombre').focus();
                        } else {
                            window.location.reload();
                        }
                    } else {
                        window.Notify.failure('Hubo un error al guardar la subcategoría.');
                    }
                })
                .catch(err => {
                    window.Notify.failure('Error de conexión.');
                })
                .finally(() => {
                    btnSubmit.disabled = false;
                    btnSubmit.classList.remove('opacity-70', 'cursor-not-allowed');
                    spinner.classList.add('hidden');
                });
            }

            function confirmarEliminar(id, nombre) {
            window.Confirm.show(
                '¿Eliminar categoría?',
                `Se eliminará la categoría '${nombre}'. Asegúrate que no tenga subcategorías asociadas.`,
                'Sí, eliminar',
                'Cancelar',
                () => {
                    const form = document.getElementById('delete-form');
                    form.action = '/inventario/categorias/' + id;
                    form.submit();
                },
                () => {},
                { okButtonBackground: '#ef4444' }
            );
            }
        </script>

        <style>
            /* Estilos para el toggle alpine */
            input:checked~.dot {
                transform: translateX(100%);
            }

            input:checked~.block {
                background-color: #465dff;
                /* Brand color */
            }
        </style>
    @endpush
@endsection
