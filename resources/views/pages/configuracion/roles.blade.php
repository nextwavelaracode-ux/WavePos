@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Roles y Permisos" />



    <div x-data="{
        showCreate: false,
        showEdit: false,
        editData: { id: null, name: '', permissions: [] },
        openEdit(data) {
            this.editData = data;
            this.showEdit = true;
        },
        hasPermission(perms, perm) {
            return perms.includes(perm);
        }
    }">

        {{-- Header --}}
        <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Roles y Permisos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Define los roles y sus permisos de acceso al sistema
                </p>
            </div>
            <button @click="showCreate = true; $nextTick(() => document.getElementById('create-rol-name').focus())"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Rol
            </button>
        </div>

        {{-- Tabla --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-neutral-800 dark:bg-neutral-800/20">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-neutral-700">
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                #</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Rol</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Permisos Asignados</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Total</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                        @forelse($roles as $i => $rol)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $colors = [
                                            'administrador' => 'purple',
                                            'gerente' => 'blue',
                                            'cajero' => 'green',
                                            'inventario' => 'orange',
                                            'supervisor' => 'indigo',
                                        ];
                                        $color = $colors[$rol->name] ?? 'gray';
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-{{ $color }}-50 dark:bg-{{ $color }}-500/10 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-{{ $color }}-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-800 dark:text-white/90 capitalize">{{ $rol->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($rol->permissions->take(4) as $perm)
                                            <span
                                                class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-neutral-700 dark:text-gray-300">
                                                {{ $perm->name }}
                                            </span>
                                        @endforeach
                                        @if ($rol->permissions->count() > 4)
                                            <span
                                                class="inline-flex items-center rounded-md bg-brand-50 px-2 py-0.5 text-xs text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                                +{{ $rol->permissions->count() - 4 }} más
                                            </span>
                                        @endif
                                        @if ($rol->permissions->count() === 0)
                                            <span class="text-xs text-gray-400 dark:text-gray-500">Sin permisos</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-brand-50 text-xs font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                        {{ $rol->permissions->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            @click='openEdit({
                                        id: {{ $rol->id }},
                                        name: "{{ addslashes($rol->name) }}",
                                        permissions: @json($rol->permissions->pluck("name")->toArray())
                                    })'
                                            class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:border-blue-800/30 dark:bg-blue-500/10 dark:text-blue-400 transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            onclick="confirmarEliminar({{ $rol->id }}, '{{ addslashes($rol->name) }}')"
                                            class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:border-red-800/30 dark:bg-red-500/10 dark:text-red-400 transition-colors" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay roles registrados</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-white/5">
                {{ $roles->links() }}
            </div>
        </div>

        {{-- ===== MODAL CREAR ===== --}}
        <div x-show="showCreate" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showCreate = false" style="display:none">

            <div @click.outside="showCreate = false"
                class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-neutral-900 lg:p-10 mx-4 max-h-[90vh]">

                <button @click="showCreate = false" type="button"
                    class="absolute right-4 top-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-400 dark:hover:bg-neutral-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="px-2 pr-10">
                    <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Nuevo Rol</h4>
                    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">Crea un rol y asígnale los permisos
                        correspondientes.</p>
                </div>

                <form action="{{ route('configuracion.roles.store') }}" method="POST" class="flex flex-col">
                    @csrf
                    <div class="px-2 overflow-y-auto">
                        <div class="mb-5">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nombre del Rol
                                <span class="text-red-500">*</span></label>
                            <input id="create-rol-name" type="text" name="name"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                placeholder="Ej: supervisor" required>
                        </div>

                        <div>
                            <p class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-400">Permisos de Acceso</p>
                            <div class="space-y-4">
                                @foreach ($permisos as $modulo => $permisosGrupo)
                                    <div class="rounded-xl border border-gray-200 dark:border-neutral-700 p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 capitalize">
                                                {{ $modulo }}</h5>
                                            <button type="button" onclick="toggleGrupo(this, 'create')"
                                                class="text-xs text-brand-500 hover:text-brand-600 dark:text-brand-400">Seleccionar
                                                todos</button>
                                        </div>
                                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            @foreach ($permisosGrupo as $permiso)
                                                <label class="flex items-center gap-2.5 cursor-pointer">
                                                    <input type="checkbox" name="permissions[]"
                                                        value="{{ $permiso->name }}"
                                                        class="w-4 h-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-neutral-600 dark:bg-neutral-800 create-perm-{{ $modulo }}">
                                                    <span
                                                        class="text-sm text-gray-600 dark:text-gray-400">{{ $permiso->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mt-6 lg:justify-end">
                        <button @click="showCreate = false" type="button"
                            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                            Guardar Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== MODAL EDITAR ===== --}}
        <div x-show="showEdit" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
            @keydown.escape.window="showEdit = false" style="display:none">

            <div @click.outside="showEdit = false"
                class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-neutral-900 lg:p-10 mx-4 max-h-[90vh]">

                <button @click="showEdit = false" type="button"
                    class="absolute right-4 top-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-neutral-800 dark:text-gray-400 dark:hover:bg-neutral-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="px-2 pr-10">
                    <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Editar Rol</h4>
                    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">Modifica el nombre y los permisos del
                        rol.</p>
                </div>

                <form :action="`/configuracion/roles/${editData.id}`" method="POST" class="flex flex-col">
                    @csrf
                    @method('PUT')
                    <div class="px-2 overflow-y-auto">
                        <div class="mb-5">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nombre del Rol
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="name" :value="editData.name"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                placeholder="Nombre del rol" required>
                        </div>

                        <div>
                            <p class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-400">Permisos de Acceso</p>
                            <div class="space-y-4">
                                @foreach ($permisos as $modulo => $permisosGrupo)
                                    <div class="rounded-xl border border-gray-200 dark:border-neutral-700 p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 capitalize">
                                                {{ $modulo }}</h5>
                                            <button type="button" onclick="toggleGrupoEdit(this, '{{ $modulo }}')"
                                                class="text-xs text-brand-500 hover:text-brand-600 dark:text-brand-400">Seleccionar
                                                todos</button>
                                        </div>
                                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            @foreach ($permisosGrupo as $permiso)
                                                <label class="flex items-center gap-2.5 cursor-pointer">
                                                    <input type="checkbox" name="permissions[]"
                                                        value="{{ $permiso->name }}"
                                                        :checked="editData.permissions && editData.permissions.includes(
                                                            '{{ $permiso->name }}')"
                                                        class="w-4 h-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-neutral-600 dark:bg-neutral-800 edit-perm-{{ $modulo }}">
                                                    <span
                                                        class="text-sm text-gray-600 dark:text-gray-400">{{ $permiso->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mt-6 lg:justify-end">
                        <button @click="showEdit = false" type="button"
                            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                            Actualizar Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <form id="delete-form" method="POST" style="display:none">
            @csrf
            @method('DELETE')
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        function confirmarEliminar(id, nombre) {
            window.Confirm.show(
                '¿Eliminar rol?',
                `Eliminarás el rol "${nombre}". Los usuarios con este rol perderán sus permisos.`,
                'Sí, eliminar',
                'Cancelar',
                () => {
                    const form = document.getElementById('delete-form');
                    form.action = `/configuracion/roles/${id}`;
                    form.submit();
                },
                () => {},
                { okButtonBackground: '#ef4444' }
            );
        }

        function toggleGrupo(btn, prefix) {
            const modulo = btn.closest('.rounded-xl').querySelector('h5').textContent.trim();
            const checkboxes = btn.closest('.rounded-xl').querySelectorAll('input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            btn.textContent = allChecked ? 'Seleccionar todos' : 'Deseleccionar todos';
        }

        function toggleGrupoEdit(btn, modulo) {
            const checkboxes = btn.closest('.rounded-xl').querySelectorAll('input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            btn.textContent = allChecked ? 'Seleccionar todos' : 'Deseleccionar todos';
        }
    </script>
@endpush
