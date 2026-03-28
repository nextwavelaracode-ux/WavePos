<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function show()
    {
        $empresa = Empresa::instance();
        $zonas = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $monedas = [
            'USD' => 'Dólar Americano (USD)',
            'EUR' => 'Euro (EUR)',
            'PEN' => 'Sol Peruano (PEN)',
            'COP' => 'Peso Colombiano (COP)',
            'MXN' => 'Peso Mexicano (MXN)',
            'GTQ' => 'Quetzal Guatemalteco (GTQ)',
            'HNL' => 'Lempira Hondureño (HNL)',
            'CRC' => 'Colón Costarricense (CRC)',
            'BOB' => 'Boliviano (BOB)',
            'PYG' => 'Guaraní Paraguayo (PYG)',
            'UYU' => 'Peso Uruguayo (UYU)',
            'DOP' => 'Peso Dominicano (DOP)',
        ];

        return view('pages.configuracion.empresa', compact('empresa', 'zonas', 'monedas'));
    }

    public function update(Request $request)
    {
        $empresa = Empresa::instance();

        $validated = $request->validate([
            'nombre'                  => 'required|string|max:200',
            'ruc'                     => 'nullable|string|max:20',
            'direccion'               => 'nullable|string|max:300',
            'telefono'                => 'nullable|string|max:20',
            'email'                   => 'nullable|email|max:100',
            'logo'                    => 'nullable|file|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'moneda'                  => 'required|string|max:10',
            'zona_horaria'            => 'required|string|max:60',
            // Numeración automática
            'prefijo_factura'         => 'nullable|string|max:20',
            'prefijo_compra'          => 'nullable|string|max:20',
            'ultimo_numero_factura'   => 'nullable|integer|min:0',
            'ultimo_numero_compra'    => 'nullable|integer|min:0',
            'digitos_correlativo'     => 'nullable|integer|min:1|max:10',
        ]);

        if ($request->hasFile('logo')) {
            // Eliminar logo anterior
            if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $validated['logo'] = $request->file('logo')->store('empresa', 'public');
        }

        $empresa->update($validated);

        return redirect()->route('configuracion.empresa')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Guardado!',
                'message' => 'Los datos de la empresa han sido actualizados correctamente.',
            ]);
    }
}
