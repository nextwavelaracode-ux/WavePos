@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Datos de la Empresa" />



<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-800/20 lg:p-6">
    <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Datos de la Empresa</h3>

    <form action="{{ route('configuracion.empresa.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2 xl:grid-cols-3">

            {{-- Logo --}}
            <div class="lg:col-span-2 xl:col-span-3">
                <div class="p-5 border border-gray-200 rounded-2xl dark:border-neutral-800 lg:p-6">
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4">Logo de la Empresa</h4>
                    <div class="flex items-center gap-6">
                        <div class="w-24 h-24 rounded-xl border border-gray-200 dark:border-neutral-700 overflow-hidden flex items-center justify-center bg-gray-50 dark:bg-neutral-800">
                            @if($empresa->logo)
                                <img id="logo-preview" src="{{ Storage::url($empresa->logo) }}" alt="Logo" class="w-full h-full object-contain">
                            @else
                                <img id="logo-preview" src="" alt="Logo" class="w-full h-full object-contain hidden">
                                <svg id="logo-placeholder" class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Subir Logo
                                <input type="file" name="logo" id="logo-input" class="hidden" accept="image/*,.svg">
                            </label>
                            <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">JPG, PNG, SVG o GIF. Máx 2MB.</p>
                            @error('logo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nombre --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Nombre de la Empresa <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nombre" value="{{ old('nombre', $empresa->nombre) }}"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    placeholder="Nombre de la empresa" required>
                @error('nombre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- RUC --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">RUC / N° Identificación</label>
                <input type="text" name="ruc" value="{{ old('ruc', $empresa->ruc) }}"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    placeholder="Ej: 1791234567001">
                @error('ruc') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Teléfono --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $empresa->telefono) }}"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    placeholder="Ej: +593 2 123-4567">
            </div>

            {{-- Email --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                <input type="email" name="email" value="{{ old('email', $empresa->email) }}"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    placeholder="empresa@correo.com">
            </div>

            {{-- Moneda --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Moneda <span class="text-red-500">*</span>
                </label>
                <select name="moneda"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:focus:border-brand-800">
                    @foreach($monedas as $codigo => $nombre)
                        <option value="{{ $codigo }}" {{ old('moneda', $empresa->moneda) == $codigo ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Zona Horaria --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Zona Horaria <span class="text-red-500">*</span>
                </label>
                <select name="zona_horaria"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:focus:border-brand-800">
                    @foreach($zonas as $zona)
                        <option value="{{ $zona }}" {{ old('zona_horaria', $empresa->zona_horaria) == $zona ? 'selected' : '' }}>
                            {{ $zona }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dirección --}}
            <div class="lg:col-span-2 xl:col-span-3">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $empresa->direccion) }}"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    placeholder="Dirección completa de la empresa">
            </div>

            {{-- ─── Sección Numeración Automática ─── --}}
            <div class="lg:col-span-2 xl:col-span-3">
                <div class="rounded-2xl border border-amber-200 dark:border-amber-800/50 bg-amber-50 dark:bg-amber-900/10 p-5 lg:p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-500/10 dark:bg-amber-500/20">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300">Numeración Automática de Documentos</h4>
                            <p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">Configura prefijos y contadores para facturas y compras. El sistema generará números correlativos automáticamente.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">

                        {{-- Prefijo Factura --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Prefijo Facturas
                            </label>
                            <input type="text" name="prefijo_factura"
                                value="{{ old('prefijo_factura', $empresa->prefijo_factura ?? 'FACT-') }}"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white dark:border-neutral-600 dark:bg-neutral-800 px-4 py-2.5 text-sm font-mono text-gray-800 dark:text-white/90 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20"
                                placeholder="FACT-">
                            <p class="mt-1 text-xs text-gray-400">Ej: FACT-, FACV-, INV-</p>
                        </div>

                        {{-- Prefijo Compra --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Prefijo Compras
                            </label>
                            <input type="text" name="prefijo_compra"
                                value="{{ old('prefijo_compra', $empresa->prefijo_compra ?? 'COMP-') }}"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white dark:border-neutral-600 dark:bg-neutral-800 px-4 py-2.5 text-sm font-mono text-gray-800 dark:text-white/90 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20"
                                placeholder="COMP-">
                            <p class="mt-1 text-xs text-gray-400">Ej: COMP-, ORD-, PO-</p>
                        </div>

                        {{-- Último número factura --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Último Nº Factura
                            </label>
                            <input type="number" min="0" name="ultimo_numero_factura"
                                value="{{ old('ultimo_numero_factura', $empresa->ultimo_numero_factura ?? 0) }}"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white dark:border-neutral-600 dark:bg-neutral-800 px-4 py-2.5 text-sm font-mono text-gray-800 dark:text-white/90 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20">
                            <p class="mt-1 text-xs text-gray-400">Próximo: <span class="font-bold text-amber-600">{{ ($empresa->prefijo_factura ?? 'FACT-') . str_pad(($empresa->ultimo_numero_factura ?? 0) + 1, $empresa->digitos_correlativo ?? 5, '0', STR_PAD_LEFT) }}</span></p>
                        </div>

                        {{-- Último número compra --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Último Nº Compra
                            </label>
                            <input type="number" min="0" name="ultimo_numero_compra"
                                value="{{ old('ultimo_numero_compra', $empresa->ultimo_numero_compra ?? 0) }}"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white dark:border-neutral-600 dark:bg-neutral-800 px-4 py-2.5 text-sm font-mono text-gray-800 dark:text-white/90 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20">
                            <p class="mt-1 text-xs text-gray-400">Próximo: <span class="font-bold text-amber-600">{{ ($empresa->prefijo_compra ?? 'COMP-') . str_pad(($empresa->ultimo_numero_compra ?? 0) + 1, $empresa->digitos_correlativo ?? 5, '0', STR_PAD_LEFT) }}</span></p>
                        </div>

                        {{-- Dígitos del correlativo --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                Dígitos Correlativo
                            </label>
                            <select name="digitos_correlativo"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-white dark:border-neutral-600 dark:bg-neutral-800 px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20">
                                @foreach([3, 4, 5, 6, 7, 8] as $d)
                                    <option value="{{ $d }}" {{ ($empresa->digitos_correlativo ?? 5) == $d ? 'selected' : '' }}>
                                        {{ $d }} dígitos ({{ str_pad(1, $d, '0', STR_PAD_LEFT) }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-400">Ceros padding del número</p>
                        </div>

                    </div>

                    {{-- Preview en vivo --}}
                    <div class="mt-4 p-3 rounded-xl bg-white dark:bg-neutral-800 border border-amber-200 dark:border-amber-800/30 flex flex-wrap gap-6 text-sm">
                        <div>
                            <span class="text-gray-400 text-xs uppercase tracking-wider block mb-0.5">Ejemplo Factura</span>
                            <span class="font-mono font-bold text-brand-600 dark:text-brand-400">
                                {{ ($empresa->prefijo_factura ?? 'FACT-') . str_pad(($empresa->ultimo_numero_factura ?? 0) + 1, $empresa->digitos_correlativo ?? 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs uppercase tracking-wider block mb-0.5">Ejemplo Compra</span>
                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ ($empresa->prefijo_compra ?? 'COMP-') . str_pad(($empresa->ultimo_numero_compra ?? 0) + 1, $empresa->digitos_correlativo ?? 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <div class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Guarda para que los cambios tomen efecto en el próximo documento.
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Botón guardar --}}
        <div class="flex justify-end mt-6">
            <button type="submit"
                class="flex items-center gap-2 rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Preview logo before upload
    document.getElementById('logo-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const preview = document.getElementById('logo-preview');
                const placeholder = document.getElementById('logo-placeholder');
                preview.src = evt.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
