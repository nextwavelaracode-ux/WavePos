<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        $sucursales = [
            [
                'nombre'    => 'Sucursal Central',
                'direccion' => 'Av. Amazonas 456 y Naciones Unidas',
                'telefono'  => '+593 2 234-5678',
                'ciudad'    => 'Quito',
                'pais'      => 'Ecuador',
                'estado'    => true,
            ],
            [
                'nombre'    => 'Sucursal Norte',
                'direccion' => 'Av. El Inca 789 y De los Shyris',
                'telefono'  => '+593 2 345-6789',
                'ciudad'    => 'Quito',
                'pais'      => 'Ecuador',
                'estado'    => true,
            ],
            [
                'nombre'    => 'Sucursal Guayaquil',
                'direccion' => 'Av. 9 de Octubre 101 y García Moreno',
                'telefono'  => '+593 4 456-7890',
                'ciudad'    => 'Guayaquil',
                'pais'      => 'Ecuador',
                'estado'    => true,
            ],
        ];

        foreach ($sucursales as $sucursal) {
            Sucursal::firstOrCreate(['nombre' => $sucursal['nombre']], $sucursal);
        }
    }
}
