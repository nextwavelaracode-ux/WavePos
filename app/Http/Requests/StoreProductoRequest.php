<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
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
            'nombre'          => 'required|string|max:150',
            'descripcion'     => 'nullable|string',
            'categoria_id'    => 'required|exists:categorias,id',
            'subcategoria_id' => 'nullable|exists:categorias,id',
            'sku'             => 'nullable|string|max:50',
            'codigo_barras'   => 'nullable|string|max:50',
            'precio_compra'   => 'required|numeric|min:0',
            'precio_venta'    => 'required|numeric|min:0',
            'precio_minimo'   => 'nullable|numeric|min:0',
            'margen'          => 'nullable|numeric',
            'impuesto'        => 'required|in:0,7,10,15',
            'stock'           => 'required|integer|min:0',
            'stock_minimo'    => 'nullable|integer|min:0',
            'stock_maximo'    => 'nullable|integer|min:0',
            'unidad_medida'   => 'required|string|max:50',
            'ubicacion'       => 'nullable|string|max:255',
            'pasillo'         => 'nullable|string|max:50',
            'estante'         => 'nullable|string|max:50',
            'imagen'          => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'estado'          => 'nullable|boolean',
            'tasa_impuesto'   => 'nullable|numeric|min:0',
            'is_excluded'     => 'nullable|boolean',
            'unidad_medida_dian_id' => 'nullable|integer',
        ];
    }
}
