<?php

namespace App\Exports;

use App\Models\Categoria;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoriasExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Categoria::with('parent')->orderBy('nombre')->get();
    }

    public function headings(): array
    {
        return [
            'Categoría Principal',
            'Subcategoría',
            'Descripción',
            'ITBMS %',
            'Unidad Medida',
            'Ubicación',
            'Detalle',
            'Estado',
        ];
    }

    public function map($categoria): array
    {
        if ($categoria->parent_id) {
            $principal = $categoria->parent->nombre;
            $subcategoria = $categoria->nombre;
        } else {
            $principal = $categoria->nombre;
            $subcategoria = '';
        }

        return [
            $principal,
            $subcategoria,
            $categoria->descripcion,
            $categoria->impuesto,
            $categoria->unidad_medida,
            $categoria->ubicacion,
            $categoria->detalle,
            $categoria->estado ? 'Activo' : 'Inactivo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
