<?php

namespace App\Imports;

use App\Models\Producto;
use App\Models\Categoria;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProductosImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $categoria = Categoria::where('nombre', $row['categoria'])->whereNull('parent_id')->first();
        $subcategoria = null;
        
        if ($categoria && !empty($row['subcategoria'])) {
            $subcategoria = Categoria::where('nombre', $row['subcategoria'])
                ->where('parent_id', $categoria->id)
                ->first();
        }
        
        $precioCompra = is_numeric($row['precio_compra'] ?? null) ? (float)$row['precio_compra'] : 0;
        $precioVenta = is_numeric($row['precio_venta'] ?? null) ? (float)$row['precio_venta'] : 0;
        $margen = $precioCompra > 0 ? (($precioVenta - $precioCompra) / $precioCompra) * 100 : 0;

        return new Producto([
            'nombre'          => $row['nombre'],
            'sku'             => $row['sku'],
            'codigo_barras'   => $row['codigo_barras'],
            'categoria_id'    => $categoria?->id ?? null,
            'subcategoria_id' => $subcategoria?->id ?? null,
            'precio_compra'   => $precioCompra,
            'precio_venta'    => $precioVenta,
            'precio_minimo'   => is_numeric($row['precio_minimo'] ?? null) ? $row['precio_minimo'] : $precioVenta,
            'margen'          => $margen,
            'impuesto'        => is_numeric($row['impuesto'] ?? null) ? (string)$row['impuesto'] : '7',
            'stock'           => is_numeric($row['stock'] ?? null) ? $row['stock'] : 0,
            'stock_minimo'    => is_numeric($row['stock_minimo'] ?? null) ? $row['stock_minimo'] : 0,
            'stock_maximo'    => is_numeric($row['stock_maximo'] ?? null) ? $row['stock_maximo'] : null,
            'unidad_medida'   => empty($row['unidad_medida'] ?? null) ? 'unidad' : $row['unidad_medida'],
            'ubicacion'       => $row['ubicacion'] ?? null,
            'estado'          => empty($row['estado'] ?? null) || strtolower($row['estado']) === 'activo',
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'categoria' => 'required',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
        ];
    }
}
