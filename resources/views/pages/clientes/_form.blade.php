{{--
    Formulario parcial de Cliente (reutilizado en modal Crear y Editar).
    Depende del componente Alpine `clienteForm()` definido en el <script>.
    Variables:
      $prefix = 'create' | 'edit'
      $edit   = true | false (opcional)
--}}
<div x-data="clienteForm()"
     @if(isset($edit) && $edit) x-effect="if(editData && editData.id) init(editData)" @endif
     class="space-y-5">

    {{-- ── Tipo de cliente ── --}}
    <div>
        <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Tipo de Cliente <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
            @foreach ([
                'natural'    => ['label' => 'Natural',    'color' => 'blue'],
                'juridico'   => ['label' => 'Jurídico',   'color' => 'purple'],
                'extranjero' => ['label' => 'Extranjero', 'color' => 'orange'],
                'b2b'        => ['label' => 'B2B',        'color' => 'emerald'],
                'b2c'        => ['label' => 'B2C',        'color' => 'pink'],
            ] as $val => $cfg)
                <label class="cursor-pointer">
                    <input type="radio" name="tipo_cliente" value="{{ $val }}"
                        x-model="tipo"
                        @change="onTipoChange()"
                        class="sr-only peer">
                    <div class="rounded-xl border-2 px-3 py-2 text-center text-xs font-semibold transition-all
                        border-gray-200 text-gray-600 dark:border-neutral-700 dark:text-gray-400
                        peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700
                        dark:peer-checked:bg-brand-500/10 dark:peer-checked:text-brand-400 dark:peer-checked:border-brand-500
                        hover:border-gray-300 dark:hover:border-gray-600">
                        {{ $cfg['label'] }}
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- ── Nombre ── (Natural, Extranjero, B2C) --}}
        <div x-show="showNombre">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Nombre</label>
            <input type="text" name="nombre" x-model="nombre"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. María">
        </div>

        {{-- ── Apellido ── --}}
        <div x-show="showNombre">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Apellido</label>
            <input type="text" name="apellido" x-model="apellido"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. González">
        </div>

        {{-- ── Empresa ── (Jurídico, B2B, Extranjero opcional) --}}
        <div x-show="showEmpresa" :class="showNombre ? '' : 'sm:col-span-2'">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Empresa / Razón Social</label>
            <input type="text" name="empresa" x-model="empresa"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. Constructora Horizonte, S.A.">
        </div>

        {{-- ── Cédula ── --}}
        <div x-show="showCedula">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Cédula</label>
            <input type="text" name="cedula" x-model="cedula"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. 8-234-1234">
        </div>

        {{-- ── RUC ── --}}
        <div x-show="showRuc">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">RUC</label>
            <input type="text" name="ruc" x-model="ruc"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. 155-60-2024">
        </div>

        {{-- ── DV ── --}}
        <div x-show="showRuc">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">DV</label>
            <input type="text" name="dv" x-model="dv" maxlength="3"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="78">
        </div>

        {{-- ── Pasaporte ── --}}
        <div x-show="showPasaporte" class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Pasaporte / Documento Internacional</label>
            <input type="text" name="pasaporte" x-model="pasaporte"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. USA-20231045">
        </div>

        {{-- ── Teléfono ── --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Teléfono</label>
            <input type="text" name="telefono" x-model="telefono"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. 6000-1001">
        </div>

        {{-- ── Email ── --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Email</label>
            <input type="email" name="email" x-model="email"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="correo@dominio.com">
        </div>

        {{-- ── Dirección ── --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Dirección</label>
            <input type="text" name="direccion" x-model="direccion"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Calle, Barrio, Edificio, Local...">
        </div>

        {{-- ── Provincia ── --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Provincia</label>
            <select name="provincia" x-model="provincia"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
                <option value="">Seleccione...</option>
                @foreach (['Bocas del Toro','Chiriquí','Coclé','Colón','Darién','Emberá','Guna Yala','Herrera','Los Santos','Ngäbe-Buglé','Panamá','Panamá Oeste','Veraguas'] as $prov)
                    <option value="{{ $prov }}">{{ $prov }}</option>
                @endforeach
            </select>
        </div>

        {{-- ── Distrito ── --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Distrito / Ciudad</label>
            <input type="text" name="distrito" x-model="distrito"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. San Miguelito">
        </div>

        {{-- ── País ── --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">País</label>
            <input type="text" name="pais" x-model="pais"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Panamá">
        </div>

        {{-- ==================== DATOS DIAN ==================== --}}
        <div class="sm:col-span-2 mt-4 mb-2">
            <h4 class="text-md font-bold text-gray-700 dark:text-gray-300 border-b pb-2 dark:border-neutral-700">Información para Facturación Electrónica (DIAN)</h4>
        </div>

        {{-- ── Tipo de Documento DIAN ── --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Documento DIAN</label>
            <select name="tipo_documento_dian_id" x-model="tipo_documento_dian_id"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
                <option value="11">11 - Registro civil</option>
                <option value="12">12 - Tarjeta de identidad</option>
                <option value="13">13 - Cédula de ciudadanía</option>
                <option value="21">21 - Tarjeta de extranjería</option>
                <option value="22">22 - Cédula de extranjería</option>
                <option value="31">31 - NIT</option>
                <option value="41">41 - Pasaporte</option>
                <option value="42">42 - Documento de identificación extranjero</option>
                <option value="47">47 - PEP</option>
                <option value="50">50 - NIT de otro país</option>
                <option value="91">91 - NUIP</option>
            </select>
        </div>

        {{-- ── Tipo de Organización DIAN ── --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Organización DIAN</label>
            <select name="tipo_organizacion_dian_id" x-model="tipo_organizacion_dian_id"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
                <option value="1">1 - Persona Jurídica</option>
                <option value="2">2 - Persona Natural</option>
            </select>
        </div>

        {{-- ── Tributo DIAN ── --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Resp. Tributaria DIAN</label>
            <select name="tributo_dian_id" x-model="tributo_dian_id"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90">
                <option value="01">01 - IVA</option>
                <option value="04">04 - INC</option>
                <option value="05">05 - IVA e INC</option>
                <option value="21">21 - No aplica</option>
            </select>
        </div>

        {{-- ── Municipio Factus ID ── --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">ID Municipio Factus</label>
            <input type="number" name="municipio_dian_id" x-model="municipio_dian_id"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Ej. 149 (Bogotá)">
        </div>
        {{-- ==================================================== --}}

        {{-- ── Límite de Crédito ── (B2B y Jurídico) --}}
        <div x-show="showCredito">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Límite de Crédito ($)</label>
            <input type="number" name="limite_credito" x-model="limite_credito" min="0" step="0.01"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="0.00">
            <p class="text-xs text-gray-400 mt-1">Monto máximo que puede quedar pendiente de cobro</p>
        </div>

        {{-- ── Notas ── --}}
        <div class="sm:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-800 dark:text-white/90">Notas Internas</label>
            <textarea name="notas" x-model="notas" rows="2"
                class="w-full rounded-xl border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder-gray-400 focus:border-brand-500 focus:ring-brand-500 dark:border-neutral-700 dark:text-white/90"
                placeholder="Observaciones, preferencias, condiciones especiales..."></textarea>
        </div>

        {{-- ── Estado ── --}}
        <div class="sm:col-span-2">
            <input type="hidden" name="estado" value="0">
            <label class="flex items-center gap-3 cursor-pointer w-max">
                <div class="relative">
                    <input type="checkbox" name="estado" value="1" x-model="estado" class="sr-only peer">
                    <div class="block h-6 w-10 rounded-full bg-gray-300 dark:bg-neutral-700 peer-checked:bg-brand-500 transition"></div>
                    <div class="dot absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-full"></div>
                </div>
                <span class="text-sm font-medium text-gray-800 dark:text-white/90">Estado Activo</span>
            </label>
        </div>

    </div>
</div>

@once
@push('scripts')
<script>
    function clienteForm(data = {}) {
        return {
            tipo:            data.tipo_cliente    || 'natural',
            nombre:          data.nombre          || '',
            apellido:        data.apellido        || '',
            empresa:         data.empresa         || '',
            cedula:          data.cedula          || '',
            ruc:             data.ruc             || '',
            dv:              data.dv              || '',
            pasaporte:       data.pasaporte       || '',
            telefono:        data.telefono        || '',
            email:           data.email           || '',
            direccion:       data.direccion       || '',
            provincia:       data.provincia       || '',
            distrito:        data.distrito        || '',
            pais:            data.pais            || 'Panamá',
            limite_credito:  data.limite_credito  || 0,
            notas:           data.notas           || '',
            estado:          data.estado !== undefined ? data.estado : true,
            tipo_documento_dian_id:    data.tipo_documento_dian_id    || 13,
            tipo_organizacion_dian_id: data.tipo_organizacion_dian_id || 2,
            tributo_dian_id:           data.tributo_dian_id           || 21,
            municipio_dian_id:         data.municipio_dian_id         || 980,

            // ── Visibilidad dinámica ──
            get showNombre()    { return ['natural','b2c','extranjero'].includes(this.tipo); },
            get showEmpresa()   { return ['juridico','b2b','extranjero'].includes(this.tipo); },
            get showCedula()    { return ['natural','b2c'].includes(this.tipo); },
            get showRuc()       { return ['juridico','b2b'].includes(this.tipo); },
            get showPasaporte() { return this.tipo === 'extranjero'; },
            get showCredito()   { return ['juridico','b2b'].includes(this.tipo); },

            onTipoChange() {
                // Al cambiar tipo, resetear crédito si no aplica
                if (!this.showCredito) this.limite_credito = 0;
            },

            init(externalData) {
                if (externalData && externalData.id) {
                    Object.assign(this, {
                        tipo:           externalData.tipo_cliente    || 'natural',
                        nombre:         externalData.nombre          || '',
                        apellido:       externalData.apellido        || '',
                        empresa:        externalData.empresa         || '',
                        cedula:         externalData.cedula          || '',
                        ruc:            externalData.ruc             || '',
                        dv:             externalData.dv              || '',
                        pasaporte:      externalData.pasaporte       || '',
                        telefono:       externalData.telefono        || '',
                        email:          externalData.email           || '',
                        direccion:      externalData.direccion       || '',
                        provincia:      externalData.provincia       || '',
                        distrito:       externalData.distrito        || '',
                        pais:           externalData.pais            || 'Panamá',
                        limite_credito: externalData.limite_credito  || 0,
                        notas:          externalData.notas           || '',
                        estado:         externalData.estado,
                        tipo_documento_dian_id:    externalData.tipo_documento_dian_id    || 13,
                        tipo_organizacion_dian_id: externalData.tipo_organizacion_dian_id || 2,
                        tributo_dian_id:           externalData.tributo_dian_id           || 21,
                        municipio_dian_id:         externalData.municipio_dian_id         || 980,
                    });
                }
            },

            syncEdit(event) {
                // Observador de editData desde el padre Alpine
            }
        };
    }
</script>
@endpush
@endonce
