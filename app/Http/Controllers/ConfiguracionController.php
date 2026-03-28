<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $settings = Setting::allGrouped();

        // Helper to read with default, resolves group not existing yet
        $get = fn(string $group, string $key, mixed $default = '') =>
            $settings[$group][$key] ?? $default;

        return view('pages.configuracion.sistema', compact('settings', 'get'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        // All toggle keys that could be submitted (when unchecked they won't appear)
        $toggles = [
            'pos_ventas_credito', 'pos_ventas_sin_cliente', 'pos_confirmacion_venta',
            'pos_modo_tactil', 'pos_venta_rapida', 'pos_autofocus_buscador',
            'caja_multiples_cajas', 'caja_arqueo_obligatorio', 'caja_permitir_diferencias',
            'inv_stock_negativo', 'inv_alertas_minimo', 'inv_lotes',
            'compras_credito',
            'ventas_descuentos', 'ventas_cliente_obligatorio',
            'clientes_ruc_obligatorio',
            'pago_efectivo', 'pago_tarjeta', 'pago_transferencia', 'pago_yappy',
            'pago_referencia_tarjeta', 'pago_referencia_transferencia',
            'seg_bloqueo_auto', 'seg_auditoria',
            'rep_logo_en_pdf', 'rep_datos_fiscales',
        ];

        // Ensure unchecked toggles are stored as '0'
        foreach ($toggles as $toggle) {
            if (!array_key_exists($toggle, $data)) {
                $data[$toggle] = '0';
            }
        }

        // Persist each setting
        foreach ($data as $key => $value) {
            if (is_string($key) && $key !== '') {
                Setting::set($key, $value ?? '');
            }
        }

        return redirect()->route('configuracion.sistema')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Guardado!',
                'message' => 'La configuración del sistema ha sido actualizada correctamente.',
            ]);
    }
}
