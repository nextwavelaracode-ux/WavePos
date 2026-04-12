@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Categorías de Gastos" />

    <div class="space-y-6" x-data="categoriasApp()">

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800/80 dark:bg-neutral-900 overflow-hidden">

            {{-- Toolbar --}}
            <div class="p-5 border-b border-gray-100 dark:border-neutral-800/80 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('gastos.index') }}"
                       class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10" title="Volver a Gastos">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Categorías de Gastos</h4>
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-white/[0.08] dark:text-gray-300">
                        {{ $categorias->count() }}
                    </span>
                </div>
                <button @click="openModal()"
                        class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Categoría
                </button>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50 dark:border-neutral-800/80 dark:bg-neutral-800/20">
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Nombre</th>
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Descripción</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Gastos</th>
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Estado</th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800/80">
                        @forelse($categorias as $cat)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800/10 transition-colors">
                                <td class="px-5 py-4 font-bold text-gray-800 dark:text-white">{{ $cat->id }}</td>
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $cat->nombre }}</span>
                                </td>
                                <td class="px-5 py-4 text-gray-500 dark:text-gray-400 max-w-xs">
                                    {{ $cat->descripcion ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-white/[0.08] dark:text-gray-300">
                                        {{ $cat->gastos()->count() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($cat->estado === 'activo')
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">Activo</span>
                                    @else
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-neutral-800/40 dark:text-gray-400">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button @click="editCategoria({{ $cat->id }}, '{{ addslashes($cat->nombre) }}', '{{ addslashes($cat->descripcion) }}', '{{ $cat->estado }}')"
                                                class="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-500/10" title="Editar">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        @if($cat->gastos()->count() === 0)
                                            <button @click="eliminarCategoria({{ $cat->id }})"
                                                    class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-500/10" title="Eliminar">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay categorías registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-white/5">
                {{ $categorias->links() }}
            </div>
        </div>

        {{-- ===== MODAL CATEGORÍA ===== --}}
        <div x-show="showModal" x-cloak
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">

            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl dark:bg-neutral-900"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-neutral-800/80">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white" x-text="isEditing ? 'Editar Categoría' : 'Nueva Categoría'"></h3>
                    <button @click="showModal = false" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="formAction" method="POST" class="p-6 space-y-4">
                    @csrf
                    <template x-if="isEditing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" x-model="nombre" required maxlength="100"
                               placeholder="Ej: Servicios, Operativos..."
                               class="w-full h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-neutral-800 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descripción</label>
                        <textarea name="descripcion" x-model="descripcion" rows="2"
                                  placeholder="Descripción opcional..."
                                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-neutral-800 dark:text-white resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estado</label>
                        <select name="estado" x-model="estado"
                                class="w-full h-11 rounded-xl border border-gray-200 bg-white px-3 text-sm focus:border-brand-500 focus:ring-0 dark:border-white/[0.1] dark:bg-neutral-800 dark:text-white">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-neutral-800/80">
                        <button type="button" @click="showModal = false"
                                class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-white/[0.1] dark:text-gray-300">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 shadow-sm">
                            <span x-text="isEditing ? 'Actualizar' : 'Guardar'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Hidden delete form --}}
    <form id="form-eliminar-categoria" method="POST" style="display:none">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script>
function categoriasApp() {
    return {
        showModal: false,
        isEditing: false,
        formAction: '{{ route('gastos.categorias.store') }}',
        nombre: '',
        descripcion: '',
        estado: 'activo',

        openModal() {
            this.isEditing = false;
            this.formAction = '{{ route('gastos.categorias.store') }}';
            this.nombre = '';
            this.descripcion = '';
            this.estado = 'activo';
            this.showModal = true;
        },

        editCategoria(id, nombre, descripcion, estado) {
            this.isEditing = true;
            this.formAction = `/gastos/categorias/${id}`;
            this.nombre = nombre;
            this.descripcion = descripcion;
            this.estado = estado;
            this.showModal = true;
        },

        eliminarCategoria(id) {
            window.Confirm.show(
                '¿Eliminar categoría?',
                'Esta acción no se puede deshacer.',
                'Sí, eliminar',
                'Cancelar',
                () => {
                    const form = document.getElementById('form-eliminar-categoria');
                    form.action = `/gastos/categorias/${id}`;
                    form.submit();
                },
                () => {},
                { okButtonBackground: '#ef4444' }
            );
        }
    }
}
</script>
@endpush
