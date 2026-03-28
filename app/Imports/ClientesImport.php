<?php

namespace App\Imports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ClientesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        return new Cliente([
            'tipo_cliente'    => strtolower($row['tipo_cliente'] ?? 'natural'),
            'nombre'          => $row['nombre'],
            'apellido'        => $row['apellido'] ?? null,
            'empresa'         => $row['empresa'] ?? null,
            'cedula'          => $row['cedula'] ?? null,
            'ruc'             => $row['ruc'] ?? null,
            'dv'              => $row['dv'] ?? null,
            'pasaporte'       => $row['pasaporte'] ?? null,
            'telefono'        => $row['telefono'] ?? null,
            'email'           => $row['email'] ?? null,
            'direccion'       => $row['direccion'] ?? null,
            'provincia'       => $row['provincia'] ?? null,
            'distrito'        => $row['distrito'] ?? null,
            'pais'            => empty($row['pais']) ? 'Panamá' : $row['pais'],
            'limite_credito'  => is_numeric($row['limite_credito']) ? $row['limite_credito'] : 0,
            'estado'          => empty($row['estado']) || strtolower($row['estado']) === 'activo',
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'tipo_cliente' => 'required|in:natural,juridico,extranjero,b2b,b2c',
        ];
    }
}
