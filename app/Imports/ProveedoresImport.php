<?php

namespace App\Imports;

use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProveedoresImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        return new Proveedor([
            'empresa'    => $row['empresa'],
            'ruc'        => $row['ruc'] ?? null,
            'dv'         => $row['dv'] ?? null,
            'contacto'   => $row['contacto'] ?? null,
            'telefono'   => $row['telefono'] ?? null,
            'email'      => $row['email'] ?? null,
            'direccion'  => $row['direccion'] ?? null,
            'provincia'  => $row['provincia'] ?? null,
            'ciudad'     => $row['ciudad'] ?? null,
            'pais'       => empty($row['pais']) ? 'Panamá' : $row['pais'],
            'notas'      => $row['notas'] ?? null,
            'estado'     => empty($row['estado']) || strtolower($row['estado']) === 'activo',
        ]);
    }

    public function rules(): array
    {
        return [
            'empresa' => 'required|string|max:150',
        ];
    }
}
