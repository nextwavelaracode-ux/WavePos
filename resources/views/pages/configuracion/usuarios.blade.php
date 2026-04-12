@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Usuarios" />



    <div x-data="{
        showCreate: false,
        showEdit: false,
        editData: {},
        openEdit(data) {
            this.editData = data;
            this.showEdit = true;
        }
    }">

        {{-- Header --}}
        <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Usuarios del Sistema</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestiona los operadores y sus accesos</p>
            </div>
            <button @click="showCreate = true; $nextTick(() => document.getElementById('create-name').focus())"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Usuario
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
                                Usuario</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Email</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Teléfono</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Rol</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Sucursal</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Estado</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                        @forelse($usuarios as $usuario)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-brand-500 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-bold text-white">
                                                {{ strtoupper(substr($usuario->name, 0, 1)) }}{{ strtoupper(substr($usuario->apellido ?? '_', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                                {{ $usuario->name }} {{ $usuario->apellido }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">ID #{{ $usuario->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $usuario->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $usuario->telefono ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if ($usuario->roles->first())
                                        @php
                                            $colors = [
                                                'administrador' => 'purple',
                                                'gerente' => 'blue',
                                                'cajero' => 'green',
                                                'inventario' => 'orange',
                                                'supervisor' => 'indigo',
                                            ];
                                            $rol = $usuario->roles->first()->name;
                                            $color = $colors[$rol] ?? 'gray';
                                        @endphp
                                        <span
                                            class="inline-flex items-center rounded-full bg-{{ $color }}-50 px-2.5 py-1 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-500/10 dark:text-{{ $color }}-400 capitalize">
                                            {{ ucfirst($rol) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Sin rol</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $usuario->sucursal->nombre ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if ($usuario->estado)
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
                                        <button
                                            @click='openEdit({
                                        id: {{ $usuario->id }},
                                        name: "{{ addslashes($usuario->name) }}",
                                        apellido: "{{ addslashes($usuario->apellido ?? '') }}",
                                        email: "{{ addslashes($usuario->email) }}",
                                        telefono: "{{ addslashes($usuario->telefono ?? '') }}",
                                        rol: "{{ $usuario->roles->first()->name ?? '' }}",
                                        sucursal_id: "{{ $usuario->sucursal_id ?? '' }}",
                                        estado: {{ $usuario->estado ? 'true' : 'false' }}
                                    })'
                                            class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 p-2 text-blue-600 hover:bg-blue-100 dark:border-blue-800/30 dark:bg-blue-500/10 dark:text-blue-400 transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        @if ($usuario->id !== auth()->id())
                                            <button
                                                onclick="confirmarEliminar({{ $usuario->id }}, '{{ addslashes($usuario->name . ' ' . $usuario->apellido) }}')"
                                                class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 p-2 text-red-600 hover:bg-red-100 dark:border-red-800/30 dark:bg-red-500/10 dark:text-red-400 transition-colors" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay usuarios registrados</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-white/5">
                {{ $usuarios->links() }}
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
                    <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Nuevo Usuario</h4>
                    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">Completa los datos del nuevo usuario.
                    </p>
                </div>

                <form action="{{ route('configuracion.usuarios.store') }}" method="POST" class="flex flex-col">
                    @csrf
                    <div class="px-2 overflow-y-auto">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nombre
                                    <span class="text-red-500">*</span></label>
                                <input id="create-name" type="text" name="name"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="Nombre" required>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Apellido</label>
                                <input type="text" name="apellido"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="Apellido">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email
                                    <span class="text-red-500">*</span></label>
                                <input type="email" name="email"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="correo@ejemplo.com" required>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Teléfono</label>
                                <input type="text" name="telefono"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="+593 99 xxx-xxxx">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Contraseña
                                    <span class="text-red-500">*</span></label>
                                <input type="password" name="password"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="Mínimo 6 caracteres" required>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Confirmar
                                    Contraseña <span class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="Repite la contraseña" required>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Rol <span
                                        class="text-red-500">*</span></label>
                                <select name="rol"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:focus:border-brand-800"
                                    required>
                                    <option value="">Seleccionar rol...</option>
                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol->name }}">{{ ucfirst($rol->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sucursal</label>
                                <select name="sucursal_id"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:focus:border-brand-800">
                                    <option value="">Sin sucursal</option>
                                    @foreach ($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Estado</label>
                                <select name="estado"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:focus:border-brand-800">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
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
                            Crear Usuario
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
                    <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">Editar Usuario</h4>
                    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">Modifica los datos del usuario. Deja
                        la contraseña en blanco para no cambiarla.</p>
                </div>

                <form :action="`/configuracion/usuarios/${editData.id}`" method="POST" class="flex flex-col">
                    @csrf
                    @method('PUT')
                    <div class="px-2 overflow-y-auto">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nombre
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="name" :value="editData.name"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="Nombre" required>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Apellido</label>
                                <input type="text" name="apellido" :value="editData.apellido"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="Apellido">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email
                                    <span class="text-red-500">*</span></label>
                                <input type="email" name="email" :value="editData.email"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="correo@ejemplo.com" required>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Teléfono</label>
                                <input type="text" name="telefono" :value="editData.telefono"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="+593 99 xxx-xxxx">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nueva
                                    Contraseña</label>
                                <input type="password" name="password"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="Dejar en blanco para no cambiar">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Confirmar
                                    Contraseña</label>
                                <input type="password" name="password_confirmation"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                    placeholder="Repite la nueva contraseña">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Rol <span
                                        class="text-red-500">*</span></label>
                                <select name="rol"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:focus:border-brand-800"
                                    required>
                                    <option value="">Seleccionar rol...</option>
                                    @foreach ($roles as $rol_item)
                                        <option value="{{ $rol_item->name }}"
                                            :selected="editData.rol === '{{ $rol_item->name }}'">
                                            {{ ucfirst($rol_item->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sucursal</label>
                                <select name="sucursal_id"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:focus:border-brand-800">
                                    <option value="">Sin sucursal</option>
                                    @foreach ($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}"
                                            :selected="editData.sucursal_id == '{{ $sucursal->id }}'">
                                            {{ $sucursal->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Estado</label>
                                <select name="estado"
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:focus:border-brand-800">
                                    <option value="1" :selected="editData.estado">Activo</option>
                                    <option value="0" :selected="!editData.estado">Inactivo</option>
                                </select>
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
                            Actualizar
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
                '¿Eliminar usuario?',
                `Eliminarás al usuario "${nombre}". Esta acción no se puede deshacer.`,
                'Sí, eliminar',
                'Cancelar',
                () => {
                    const form = document.getElementById('delete-form');
                    form.action = `/configuracion/usuarios/${id}`;
                    form.submit();
                },
                () => {},
                { okButtonBackground: '#ef4444' }
            );
        }
    </script>
@endpush
