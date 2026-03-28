<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EmpresaSeeder::class,
            SucursalSeeder::class,
            RolesPermisosSeeder::class,
            UsuarioSeeder::class,
            CategoriaSeeder::class,
            ProveedorSeeder::class,
            ProductoSeeder::class,
            MovimientoInventarioSeeder::class,
            CategoriaGastoSeeder::class,
            ClienteSeeder::class,
        ]);
    }
}
