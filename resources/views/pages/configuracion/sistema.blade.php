@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Configuración del Sistema" />



{{-- Shared input/select/toggle CSS classes --}}
@php
$inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800';
$selectClass = 'h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white/90 dark:focus:border-brand-800';
$labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
$sectionCard = 'rounded-2xl border border-gray-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900';
$sectionTitle = 'mb-5 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400';

function s($settings, $group, $key, $default = '') {
    return $settings[$group][$key] ?? $default;
}
@endphp

<div x-data="{ tab: 'general' }" class="space-y-5">

    {{-- Tab Navigation --}}
    <div class="flex flex-wrap gap-1 rounded-2xl border border-gray-200 bg-white p-1 dark:border-neutral-800 dark:bg-neutral-900 shadow-sm">
        @foreach([
            ['id' => 'general',      'label' => 'General & POS',    'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['id' => 'facturacion',  'label' => 'Facturación',       'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
            ['id' => 'inventario',   'label' => 'Inventario',        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['id' => 'pagos',        'label' => 'Pagos',             'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['id' => 'seguridad',    'label' => 'Seguridad',         'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['id' => 'reportes',     'label' => 'Reportes',          'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ] as $t)
        <button @click="tab = '{{ $t['id'] }}'"
                :class="tab === '{{ $t['id'] }}' ? 'bg-brand-500 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-neutral-800'"
                class="flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition-all">
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}" />
            </svg>
            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isMobileOpen || true">{{ $t['label'] }}</span>
        </button>
        @endforeach
    </div>

    <form action="{{ route('configuracion.sistema.update') }}" method="POST">
        @csrf

        {{-- ========================================================
             TAB: GENERAL & POS
        ======================================================== --}}
        <div x-show="tab === 'general'" class="space-y-5">

            {{-- POS --}}
            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">🖥️ Punto de Venta (POS)</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

                    {{-- Toggles --}}
                    @foreach([
                        ['key' => 'pos_ventas_credito',      'label' => 'Permitir ventas a crédito'],
                        ['key' => 'pos_ventas_sin_cliente',  'label' => 'Permitir ventas sin cliente'],
                        ['key' => 'pos_confirmacion_venta',  'label' => 'Confirmación antes de finalizar venta'],
                        ['key' => 'pos_modo_tactil',         'label' => 'Modo táctil activado'],
                        ['key' => 'pos_venta_rapida',        'label' => 'Venta rápida (sin pasos intermedios)'],
                        ['key' => 'pos_autofocus_buscador',  'label' => 'Auto-focus en buscador de productos'],
                    ] as $toggle)
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-neutral-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $toggle['label'] }}</span>
                        <div class="relative">
                            <input type="checkbox" name="{{ $toggle['key'] }}" value="1"
                                   class="sr-only peer"
                                   {{ ($settings['pos'][$toggle['key']] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 transition-colors dark:bg-neutral-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                    @endforeach

                    {{-- Tiempo expiración espera --}}
                    <div>
                        <label class="{{ $labelClass }}">Tiempo expiración ventas en espera (minutos)</label>
                        <input type="number" name="pos_expiracion_espera_min" min="1" max="1440"
                               value="{{ $settings['pos']['pos_expiracion_espera_min'] ?? '60' }}"
                               class="{{ $inputClass }}">
                    </div>
                </div>
            </div>

            {{-- CAJA --}}
            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">💰 Caja</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

                    @foreach([
                        ['key' => 'caja_multiples_cajas',       'label' => 'Permitir múltiples cajas abiertas'],
                        ['key' => 'caja_arqueo_obligatorio',     'label' => 'Arqueo obligatorio al cerrar'],
                        ['key' => 'caja_permitir_diferencias',   'label' => 'Permitir diferencias en arqueo'],
                    ] as $toggle)
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-neutral-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $toggle['label'] }}</span>
                        <div class="relative">
                            <input type="checkbox" name="{{ $toggle['key'] }}" value="1"
                                   class="sr-only peer"
                                   {{ ($settings['caja'][$toggle['key']] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 transition-colors dark:bg-neutral-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                    @endforeach

                    <div>
                        <label class="{{ $labelClass }}">Monto mínimo de apertura de caja</label>
                        <input type="number" name="caja_monto_minimo_apertura" min="0" step="0.01"
                               value="{{ $settings['caja']['caja_monto_minimo_apertura'] ?? '0' }}"
                               class="{{ $inputClass }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================
             TAB: FACTURACIÓN (Impuestos + Ventas + Compras)
        ======================================================== --}}
        <div x-show="tab === 'facturacion'" class="space-y-5">

            {{-- Impuestos (ITBMS) --}}
            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">🧾 Impuestos — ITBMS</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                    <div>
                        <label class="{{ $labelClass }}">Tasa ITBMS por defecto (%)</label>
                        <select name="itbms_tasa_default" class="{{ $selectClass }}">
                            @foreach(['0' => '0% — Exento', '7' => '7% — Estándar', '10' => '10% — Alcohol / Turismo', '15' => '15% — Cigarrillos'] as $val => $lbl)
                                <option value="{{ $val }}" {{ ($settings['impuestos']['itbms_tasa_default'] ?? '7') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Tasas activas (separadas por coma)</label>
                        <input type="text" name="itbms_tasas_activas"
                               value="{{ $settings['impuestos']['itbms_tasas_activas'] ?? '0,7,10,15' }}"
                               class="{{ $inputClass }}" placeholder="0,7,10,15">
                        <p class="mt-1 text-xs text-gray-400">Define qué tasas pueden seleccionarse al crear productos.</p>
                    </div>
                </div>
            </div>

            {{-- Ventas --}}
            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">📋 Ventas</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                    <div>
                        <label class="{{ $labelClass }}">Prefijo numeración de facturas</label>
                        <input type="text" name="ventas_prefijo"
                               value="{{ $settings['ventas']['ventas_prefijo'] ?? 'VTA-' }}"
                               class="{{ $inputClass }}" placeholder="VTA-" maxlength="10">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Límite máximo de descuento (%)</label>
                        <input type="number" name="ventas_limite_descuento" min="0" max="100"
                               value="{{ $settings['ventas']['ventas_limite_descuento'] ?? '30' }}"
                               class="{{ $inputClass }}">
                    </div>

                    @foreach([
                        ['key' => 'ventas_descuentos',         'label' => 'Permitir descuentos en ventas'],
                        ['key' => 'ventas_cliente_obligatorio','label' => 'Cliente obligatorio para vender'],
                    ] as $toggle)
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-neutral-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $toggle['label'] }}</span>
                        <div class="relative">
                            <input type="checkbox" name="{{ $toggle['key'] }}" value="1" class="sr-only peer"
                                   {{ ($settings['ventas'][$toggle['key']] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 transition-colors dark:bg-neutral-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Compras --}}
            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">🛒 Compras</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                    <div>
                        <label class="{{ $labelClass }}">Prefijo numeración de compras</label>
                        <input type="text" name="compras_prefijo"
                               value="{{ $settings['compras']['compras_prefijo'] ?? 'CMP-' }}"
                               class="{{ $inputClass }}" placeholder="CMP-" maxlength="10">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Días por defecto para vencimiento (crédito)</label>
                        <input type="number" name="compras_dias_vencimiento" min="1" max="365"
                               value="{{ $settings['compras']['compras_dias_vencimiento'] ?? '30' }}"
                               class="{{ $inputClass }}">
                    </div>

                    <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-neutral-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Compras a crédito habilitadas</span>
                        <div class="relative">
                            <input type="checkbox" name="compras_credito" value="1" class="sr-only peer"
                                   {{ ($settings['compras']['compras_credito'] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 transition-colors dark:bg-neutral-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ========================================================
             TAB: INVENTARIO & CLIENTES
        ======================================================== --}}
        <div x-show="tab === 'inventario'" class="space-y-5">

            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">📦 Inventario</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                    <div>
                        <label class="{{ $labelClass }}">Unidad de medida por defecto</label>
                        <select name="inv_unidad_default" class="{{ $selectClass }}">
                            @foreach(['unidad' => 'Unidad', 'kg' => 'Kilogramo (kg)', 'g' => 'Gramo (g)', 'l' => 'Litro (lt)', 'ml' => 'Mililitro (ml)', 'caja' => 'Caja', 'par' => 'Par'] as $val => $lbl)
                                <option value="{{ $val }}" {{ ($settings['inventario']['inv_unidad_default'] ?? 'unidad') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    @foreach([
                        ['key' => 'inv_stock_negativo', 'label' => 'Permitir stock negativo'],
                        ['key' => 'inv_alertas_minimo', 'label' => 'Alertas de stock mínimo activas'],
                        ['key' => 'inv_lotes',          'label' => 'Control por lotes (avanzado)'],
                    ] as $toggle)
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-neutral-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $toggle['label'] }}</span>
                        <div class="relative">
                            <input type="checkbox" name="{{ $toggle['key'] }}" value="1" class="sr-only peer"
                                   {{ ($settings['inventario'][$toggle['key']] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 transition-colors dark:bg-neutral-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">👥 Clientes</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                    <div>
                        <label class="{{ $labelClass }}">Límite de crédito por defecto</label>
                        <input type="number" name="clientes_limite_credito" min="0" step="0.01"
                               value="{{ $settings['clientes']['clientes_limite_credito'] ?? '500' }}"
                               class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Tipos de cliente (separados por coma)</label>
                        <input type="text" name="clientes_tipos"
                               value="{{ $settings['clientes']['clientes_tipos'] ?? 'regular,vip,mayorista' }}"
                               class="{{ $inputClass }}" placeholder="regular,vip,mayorista">
                    </div>

                    <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-neutral-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">RUC obligatorio para clientes</span>
                        <div class="relative">
                            <input type="checkbox" name="clientes_ruc_obligatorio" value="1" class="sr-only peer"
                                   {{ ($settings['clientes']['clientes_ruc_obligatorio'] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 transition-colors dark:bg-neutral-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ========================================================
             TAB: PAGOS
        ======================================================== --}}
        <div x-show="tab === 'pagos'" class="space-y-5">
            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">💳 Métodos de Pago Habilitados</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach([
                        ['key' => 'pago_efectivo',       'label' => 'Efectivo', 'icon' => 'text-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-500/10'],
                        ['key' => 'pago_tarjeta',        'label' => 'Tarjeta de crédito/débito', 'icon' => 'text-blue-500', 'bg' => 'bg-blue-50 dark:bg-blue-500/10'],
                        ['key' => 'pago_transferencia',  'label' => 'Transferencia bancaria', 'icon' => 'text-purple-500', 'bg' => 'bg-purple-50 dark:bg-purple-500/10'],
                        ['key' => 'pago_yappy',          'label' => 'Yappy / Nequi', 'icon' => 'text-amber-500', 'bg' => 'bg-amber-50 dark:bg-amber-500/10'],
                    ] as $pago)
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-neutral-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg {{ $pago['bg'] }}">
                                <svg class="h-5 w-5 {{ $pago['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $pago['label'] }}</span>
                        </div>
                        <div class="relative">
                            <input type="checkbox" name="{{ $pago['key'] }}" value="1" class="sr-only peer"
                                   {{ ($settings['pagos'][$pago['key']] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 transition-colors dark:bg-neutral-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">🔒 Validaciones de Pago</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach([
                        ['key' => 'pago_referencia_tarjeta',       'label' => 'Requerir referencia en pagos con tarjeta'],
                        ['key' => 'pago_referencia_transferencia',  'label' => 'Requerir referencia en transferencias'],
                    ] as $toggle)
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-neutral-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $toggle['label'] }}</span>
                        <div class="relative">
                            <input type="checkbox" name="{{ $toggle['key'] }}" value="1" class="sr-only peer"
                                   {{ ($settings['pagos'][$toggle['key']] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 transition-colors dark:bg-neutral-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ========================================================
             TAB: SEGURIDAD
        ======================================================== --}}
        <div x-show="tab === 'seguridad'" class="space-y-5">
            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">🔐 Seguridad de Sesión</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                    <div>
                        <label class="{{ $labelClass }}">Tiempo máximo de sesión inactiva (minutos)</label>
                        <input type="number" name="seg_timeout_sesion_min" min="5" max="480"
                               value="{{ $settings['seguridad']['seg_timeout_sesion_min'] ?? '120' }}"
                               class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Intentos fallidos antes de bloqueo</label>
                        <input type="number" name="seg_intentos_fallidos" min="1" max="20"
                               value="{{ $settings['seguridad']['seg_intentos_fallidos'] ?? '5' }}"
                               class="{{ $inputClass }}">
                    </div>

                    @foreach([
                        ['key' => 'seg_bloqueo_auto', 'label' => 'Bloqueo automático de cuenta tras intentos fallidos'],
                        ['key' => 'seg_auditoria',    'label' => 'Habilitar auditoría de acciones del sistema'],
                    ] as $toggle)
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-neutral-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $toggle['label'] }}</span>
                        <div class="relative">
                            <input type="checkbox" name="{{ $toggle['key'] }}" value="1" class="sr-only peer"
                                   {{ ($settings['seguridad'][$toggle['key']] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 transition-colors dark:bg-neutral-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ========================================================
             TAB: REPORTES
        ======================================================== --}}
        <div x-show="tab === 'reportes'" class="space-y-5">
            <div class="{{ $sectionCard }}">
                <h4 class="{{ $sectionTitle }}">📄 Formato de Reportes PDF</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                    <div>
                        <label class="{{ $labelClass }}">Tamaño de papel para reportes</label>
                        <select name="rep_formato_papel" class="{{ $selectClass }}">
                            <option value="A4" {{ ($settings['reportes']['rep_formato_papel'] ?? 'A4') == 'A4' ? 'selected' : '' }}>A4 (210 × 297 mm)</option>
                            <option value="letter" {{ ($settings['reportes']['rep_formato_papel'] ?? '') == 'letter' ? 'selected' : '' }}>Carta / Letter (216 × 279 mm)</option>
                        </select>
                    </div>

                    @foreach([
                        ['key' => 'rep_logo_en_pdf',    'label' => 'Mostrar logo de empresa en PDF'],
                        ['key' => 'rep_datos_fiscales', 'label' => 'Mostrar datos fiscales (RUC/DV en PDF)'],
                    ] as $toggle)
                    <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 p-4 dark:border-neutral-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $toggle['label'] }}</span>
                        <div class="relative">
                            <input type="checkbox" name="{{ $toggle['key'] }}" value="1" class="sr-only peer"
                                   {{ ($settings['reportes'][$toggle['key']] ?? '0') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full bg-gray-200 peer-checked:bg-brand-500 transition-colors dark:bg-neutral-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ===== Save Button (always visible) ===== --}}
        <div class="flex justify-end pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 shadow-sm transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar Configuración
            </button>
        </div>
    </form>
</div>
@endsection
