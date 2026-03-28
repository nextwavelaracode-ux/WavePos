<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Abarrotes y Alimentos',
                'descripcion' => 'Productos de consumo básico diarios.',
                'impuesto' => '0', // Exento
                'detalle' => 'Canasta básica, granos, etc.',
                'orden_visualizacion' => 1,
            ],
            [
                'nombre' => 'Bebidas no alcohólicas',
                'descripcion' => 'Jugos, sodas, agua.',
                'impuesto' => '7', // 7% ITBMS
                'detalle' => 'Refrescos y más',
                'orden_visualizacion' => 2,
            ],
            [
                'nombre' => 'Licores y Cervezas',
                'descripcion' => 'Bebidas alcohólicas.',
                'impuesto' => '10', // 10% ITBMS
                'detalle' => 'Venta restringida a mayores de edad',
                'orden_visualizacion' => 3,
            ],
            [
                'nombre' => 'Cigarrillos y Tabaco',
                'descripcion' => 'Productos de tabaco.',
                'impuesto' => '15', // 15% ITBMS
                'detalle' => 'Derivados del tabaco',
                'orden_visualizacion' => 4,
            ],
            [
                'nombre' => 'Tecnología y Electrónica',
                'descripcion' => 'Gadgets y accesorios.',
                'impuesto' => '7',
                'detalle' => 'Cargadores, audífonos, etc.',
                'orden_visualizacion' => 5,
            ]
        ];

        foreach ($categorias as $cat) {
            Categoria::updateOrCreate(['nombre' => $cat['nombre']], $cat);
        }

        // Crear 10 categorías adicionales usando faker
        Categoria::factory()->count(10)->create();
    }
}
