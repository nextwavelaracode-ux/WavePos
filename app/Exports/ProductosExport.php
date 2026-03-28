<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductosExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Producto::with(['categoria', 'subcategoria'])->orderBy('nombre')->get();
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'SKU',
            'Código Barras',
            'Categoría',
            'Subcategoría',
            'Precio Compra',
            'Precio Venta',
            'Precio Mínimo',
            'Margen %',
            'Impuesto %',
            'Stock',
            'Stock Mínimo',
            'Stock Máximo',
            'Unidad Medida',
            'Ubicación',
            'Estado',
        ];
    }

    public function map($producto): array
    {
        return [
            $producto->nombre,
            $producto->sku,
            $producto->codigo_barras,
            $producto->categoria?->nombre ?? '',
            $producto->subcategoria?->nombre ?? '',
            $producto->precio_compra,
            $producto->precio_venta,
            $producto->precio_minimo,
            $producto->margen,
            $producto->impuesto,
            $producto->stock,
            $producto->stock_minimo,
            $producto->stock_maximo,
            $producto->unidad_medida,
            $producto->ubicacion,
            $producto->estado ? 'Activo' : 'Inactivo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
