<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sucursal_id'           => 'required|exists:sucursales,id',
            'cliente_id'            => 'nullable|exists:clientes,id',
            'items'                 => 'required|array|min:1',
            'items.*.producto_id'   => 'required|exists:productos,id',
            'items.*.cantidad'      => 'required|integer|min:1',
            'items.*.precio_unitario'=> 'required|numeric|min:0',
            'items.*.impuesto'      => 'required|numeric|min:0',
            'pagos'                 => 'required|array|min:1',
            'pagos.*.metodo'        => 'required|in:efectivo,tarjeta,transferencia,yappy,credito',
            'pagos.*.monto'         => 'required|numeric|min:0.01',
            'fecha_vencimiento'     => 'nullable|date',
            'forma_pago_dian'       => 'nullable|string',
            'metodo_pago_dian_id'   => 'nullable|integer',
        ];
    }
}
