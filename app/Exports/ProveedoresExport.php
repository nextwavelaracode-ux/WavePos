<?php

namespace App\Exports;

use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProveedoresExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Proveedor::orderBy('empresa')->get();
    }

    public function headings(): array
    {
        return [
            'Empresa',
            'RUC',
            'DV',
            'Contacto',
            'Teléfono',
            'Email',
            'Dirección',
            'Provincia',
            'Ciudad',
            'País',
            'Notas',
            'Estado',
        ];
    }

    public function map($proveedor): array
    {
        return [
            $proveedor->empresa,
            $proveedor->ruc,
            $proveedor->dv,
            $proveedor->contacto,
            $proveedor->telefono,
            $proveedor->email,
            $proveedor->direccion,
            $proveedor->provincia,
            $proveedor->ciudad,
            $proveedor->pais,
            $proveedor->notas,
            $proveedor->estado ? 'Activo' : 'Inactivo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
