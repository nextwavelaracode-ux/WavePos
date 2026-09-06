<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            // POS
            'pos_ventas_credito' => '0',
            'pos_ventas_sin_cliente' => '1',
            'pos_confirmacion_venta' => '1',
            'pos_modo_tactil' => '0',
            'pos_venta_rapida' => '0',
            'pos_autofocus_buscador' => '1',
            'pos_expiracion_espera_min' => '60',

            // Caja
            'caja_multiples_cajas' => '0',
            'caja_arqueo_obligatorio' => '1',
            'caja_permitir_diferencias' => '0',
            'caja_monto_minimo_apertura' => '0',

            // Inventario
            'inv_unidad_default' => 'unidad',
            'inv_stock_negativo' => '0',
            'inv_alertas_minimo' => '1',
            'inv_lotes' => '0',

            // Compras
            'compras_credito' => '0',
            'compras_prefijo' => 'CMP-',
            'compras_dias_vencimiento' => '30',

            // Ventas
            'ventas_descuentos' => '1',
            'ventas_cliente_obligatorio' => '0',
            'ventas_prefijo' => 'VTA-',
            'ventas_limite_descuento' => '30',

            // Clientes
            'clientes_ruc_obligatorio' => '0',
            'clientes_limite_credito' => '500',
            'clientes_tipos' => 'regular,vip,mayorista',

            // Pagos
            'pago_efectivo' => '1',
            'pago_tarjeta' => '1',
            'pago_transferencia' => '1',
            'pago_yappy' => '0',
            'pago_referencia_tarjeta' => '1',
            'pago_referencia_transferencia' => '1',

            // Seguridad
            'seg_bloqueo_auto' => '1',
            'seg_auditoria' => '1',
            'seg_timeout_sesion_min' => '120',
            'seg_intentos_fallidos' => '5',

            // Reportes
            'rep_formato_papel' => 'A4',
            'rep_logo_en_pdf' => '1',
            'rep_datos_fiscales' => '1',

            // Impuestos
            'itbms_tasa_default' => '7',
            'itbms_tasas_activas' => '0,7,10,15',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
