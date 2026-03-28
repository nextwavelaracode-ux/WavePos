<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $sucursalCentral = Sucursal::where('nombre', 'Sucursal Central')->first();
        $sucursalNorte   = Sucursal::where('nombre', 'Sucursal Norte')->first();
        $sucursalGye     = Sucursal::where('nombre', 'Sucursal Guayaquil')->first();

        $usuarios = [
            [
                'data' => [
                    'name'        => 'Admin',
                    'apellido'    => 'Sistema',
                    'email'       => 'admin@pos.com',
                    'telefono'    => '+593 99 000-0001',
                    'password'    => Hash::make('admin123'),
                    'sucursal_id' => $sucursalCentral?->id,
                    'estado'      => true,
                ],
                'rol' => 'Administrador',
            ],
            [
                'data' => [
                    'name'        => 'María',
                    'apellido'    => 'López',
                    'email'       => 'gerente@pos.com',
                    'telefono'    => '+593 99 000-0002',
                    'password'    => Hash::make('gerente123'),
                    'sucursal_id' => $sucursalCentral?->id,
                    'estado'      => true,
                ],
                'rol' => 'Gerente',
            ],
            [
                'data' => [
                    'name'        => 'Pedro',
                    'apellido'    => 'García',
                    'email'       => 'cajero@pos.com',
                    'telefono'    => '+593 99 000-0003',
                    'password'    => Hash::make('cajero123'),
                    'sucursal_id' => $sucursalNorte?->id,
                    'estado'      => true,
                ],
                'rol' => 'Cajero',
            ],
            [
                'data' => [
                    'name'        => 'Laura',
                    'apellido'    => 'Martínez',
                    'email'       => 'inventario@pos.com',
                    'telefono'    => '+593 99 000-0004',
                    'password'    => Hash::make('inv123456'),
                    'sucursal_id' => $sucursalGye?->id,
                    'estado'      => true,
                ],
                'rol' => 'Inventario',
            ],
            [
                'data' => [
                    'name'        => 'Carlos',
                    'apellido'    => 'Rodríguez',
                    'email'       => 'conta@pos.com',
                    'telefono'    => '+593 99 000-0005',
                    'password'    => Hash::make('conta123'),
                    'sucursal_id' => $sucursalCentral?->id,
                    'estado'      => true,
                ],
                'rol' => 'Contabilidad',
            ],
        ];

        foreach ($usuarios as $u) {
            $usuario = User::firstOrCreate(['email' => $u['data']['email']], $u['data']);
            $usuario->syncRoles([$u['rol']]);
        }
    }
}
