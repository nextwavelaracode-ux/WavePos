<?php

namespace App\Exports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Cliente::orderBy('nombre')->get();
    }

    public function headings(): array
    {
        return [
            'Tipo Cliente',
            'Nombre',
            'Apellido',
            'Empresa',
            'Cédula',
            'RUC',
            'DV',
            'Pasaporte',
            'Teléfono',
            'Email',
            'Dirección',
            'Provincia',
            'Distrito',
            'País',
            'Límite Crédito',
            'Estado',
        ];
    }

    public function map($cliente): array
    {
        return [
            $cliente->tipo_cliente,
            $cliente->nombre,
            $cliente->apellido,
            $cliente->empresa,
            $cliente->cedula,
            $cliente->ruc,
            $cliente->dv,
            $cliente->pasaporte,
            $cliente->telefono,
            $cliente->email,
            $cliente->direccion,
            $cliente->provincia,
            $cliente->distrito,
            $cliente->pais,
            $cliente->limite_credito,
            $cliente->estado ? 'Activo' : 'Inactivo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
