<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── POS ────────────────────────────────────────────────
            ['group' => 'pos', 'key' => 'pos_ventas_credito',          'value' => '0'],
            ['group' => 'pos', 'key' => 'pos_ventas_sin_cliente',       'value' => '1'],
            ['group' => 'pos', 'key' => 'pos_producto_generico_id',     'value' => ''],
            ['group' => 'pos', 'key' => 'pos_expiracion_espera_min',    'value' => '60'],
            ['group' => 'pos', 'key' => 'pos_confirmacion_venta',       'value' => '1'],
            ['group' => 'pos', 'key' => 'pos_modo_tactil',              'value' => '0'],
            ['group' => 'pos', 'key' => 'pos_venta_rapida',             'value' => '0'],
            ['group' => 'pos', 'key' => 'pos_autofocus_buscador',       'value' => '1'],

            // ── CAJA ───────────────────────────────────────────────
            ['group' => 'caja', 'key' => 'caja_monto_minimo_apertura',  'value' => '0'],
            ['group' => 'caja', 'key' => 'caja_multiples_cajas',        'value' => '0'],
            ['group' => 'caja', 'key' => 'caja_arqueo_obligatorio',     'value' => '1'],
            ['group' => 'caja', 'key' => 'caja_permitir_diferencias',   'value' => '1'],

            // ── IMPUESTOS ──────────────────────────────────────────
            ['group' => 'impuestos', 'key' => 'itbms_tasa_default',     'value' => '7'],
            ['group' => 'impuestos', 'key' => 'itbms_tasas_activas',    'value' => '0,7,10,15'],

            // ── INVENTARIO ─────────────────────────────────────────
            ['group' => 'inventario', 'key' => 'inv_stock_negativo',    'value' => '0'],
            ['group' => 'inventario', 'key' => 'inv_alertas_minimo',    'value' => '1'],
            ['group' => 'inventario', 'key' => 'inv_unidad_default',    'value' => 'unidad'],
            ['group' => 'inventario', 'key' => 'inv_lotes',             'value' => '0'],

            // ── COMPRAS ────────────────────────────────────────────
            ['group' => 'compras', 'key' => 'compras_credito',          'value' => '1'],
            ['group' => 'compras', 'key' => 'compras_dias_vencimiento', 'value' => '30'],
            ['group' => 'compras', 'key' => 'compras_prefijo',          'value' => 'CMP-'],

            // ── VENTAS ─────────────────────────────────────────────
            ['group' => 'ventas', 'key' => 'ventas_prefijo',            'value' => 'VTA-'],
            ['group' => 'ventas', 'key' => 'ventas_descuentos',         'value' => '1'],
            ['group' => 'ventas', 'key' => 'ventas_limite_descuento',   'value' => '30'],
            ['group' => 'ventas', 'key' => 'ventas_cliente_obligatorio','value' => '0'],

            // ── CLIENTES ───────────────────────────────────────────
            ['group' => 'clientes', 'key' => 'clientes_limite_credito', 'value' => '500'],
            ['group' => 'clientes', 'key' => 'clientes_ruc_obligatorio','value' => '0'],
            ['group' => 'clientes', 'key' => 'clientes_tipos',          'value' => 'regular,vip,mayorista'],

            // ── PAGOS ──────────────────────────────────────────────
            ['group' => 'pagos', 'key' => 'pago_efectivo',              'value' => '1'],
            ['group' => 'pagos', 'key' => 'pago_tarjeta',               'value' => '1'],
            ['group' => 'pagos', 'key' => 'pago_transferencia',         'value' => '1'],
            ['group' => 'pagos', 'key' => 'pago_yappy',                 'value' => '1'],
            ['group' => 'pagos', 'key' => 'pago_referencia_tarjeta',    'value' => '0'],
            ['group' => 'pagos', 'key' => 'pago_referencia_transferencia','value' => '1'],

            // ── SEGURIDAD ──────────────────────────────────────────
            ['group' => 'seguridad', 'key' => 'seg_timeout_sesion_min', 'value' => '120'],
            ['group' => 'seguridad', 'key' => 'seg_intentos_fallidos',  'value' => '5'],
            ['group' => 'seguridad', 'key' => 'seg_bloqueo_auto',       'value' => '1'],
            ['group' => 'seguridad', 'key' => 'seg_auditoria',          'value' => '1'],

            // ── REPORTES ──────────────────────────────────────────
            ['group' => 'reportes', 'key' => 'rep_logo_en_pdf',         'value' => '1'],
            ['group' => 'reportes', 'key' => 'rep_datos_fiscales',      'value' => '1'],
            ['group' => 'reportes', 'key' => 'rep_formato_papel',       'value' => 'A4'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['group' => $setting['group'], 'value' => $setting['value']]
            );
        }
    }
}
