<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'empresa' => 'Distribuidora del Istmo S.A.',
                'ruc' => '155698-1-55555',
                'dv' => '98',
                'contacto' => 'Juan Pérez',
                'telefono' => '+507 222-3333',
                'email' => 'ventas@distribuidoraistmo.com',
                'direccion' => 'Via España, Edificio Central, Piso 2',
                'provincia' => 'Panamá',
                'ciudad' => 'Ciudad de Panamá',
                'pais' => 'Panamá',
                'estado' => true,
            ],
            [
                'empresa' => 'Tecnología Avanzada Corp.',
                'ruc' => '548796-2-8547',
                'dv' => '15',
                'contacto' => 'Ana Gómez',
                'telefono' => '+507 333-4444',
                'email' => 'info@tecavanzada.com',
                'direccion' => 'Calle 50, Plaza Nueva',
                'provincia' => 'Panamá',
                'ciudad' => 'Ciudad de Panamá',
                'pais' => 'Panamá',
                'estado' => true,
            ]
        ];

        foreach ($proveedores as $prov) {
            Proveedor::updateOrCreate(['ruc' => $prov['ruc']], $prov);
        }

        // Crear 10 proveedores adicionales usando faker
        Proveedor::factory()->count(10)->create();
    }
}
