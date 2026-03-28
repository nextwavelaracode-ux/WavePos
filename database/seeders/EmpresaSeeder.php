<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        Empresa::firstOrCreate(
            ['id' => 1],
            [
                'nombre'       => 'Mi Empresa S.A.',
                'ruc'          => '1790123456001',
                'direccion'    => 'Av. Principal 123 y Secundaria, Quito',
                'telefono'     => '+593 2 234-5678',
                'email'        => 'info@miempresa.com',
                'moneda'       => 'USD',
                'zona_horaria' => 'America/Guayaquil',
            ]
        );
    }
}
