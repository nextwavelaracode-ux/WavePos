<?php

namespace Database\Seeders;

use App\Models\CategoriaGasto;
use Illuminate\Database\Seeder;

class CategoriaGastoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Servicios',       'descripcion' => 'Agua, luz, internet, teléfono y otros servicios públicos.',  'estado' => 'activo'],
            ['nombre' => 'Administrativos', 'descripcion' => 'Gastos de oficina, papelería, útiles y administración general.','estado' => 'activo'],
            ['nombre' => 'Operativos',      'descripcion' => 'Gastos propios de la operación diaria del negocio.',            'estado' => 'activo'],
            ['nombre' => 'Transporte',      'descripcion' => 'Combustible, fletes, mensajería y logística.',                  'estado' => 'activo'],
            ['nombre' => 'Mantenimiento',   'descripcion' => 'Reparaciones, mantenimiento de equipos e instalaciones.',       'estado' => 'activo'],
            ['nombre' => 'Impuestos',       'descripcion' => 'ITBMS, impuesto de renta y otras obligaciones fiscales.',       'estado' => 'activo'],
            ['nombre' => 'Salarios',        'descripcion' => 'Nómina, bonificaciones y carga social de empleados.',           'estado' => 'activo'],
            ['nombre' => 'Alquiler',        'descripcion' => 'Arrendamiento de local, bodega u oficinas.',                    'estado' => 'activo'],
            ['nombre' => 'Marketing',       'descripcion' => 'Publicidad, redes sociales, materiales y promociones.',         'estado' => 'activo'],
            ['nombre' => 'Otros',           'descripcion' => 'Gastos varios no clasificados.',                                'estado' => 'activo'],
        ];

        foreach ($categorias as $cat) {
            CategoriaGasto::firstOrCreate(['nombre' => $cat['nombre']], $cat);
        }
    }
}
