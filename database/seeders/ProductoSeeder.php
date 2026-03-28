<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Str;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar las categorias creadas por el CategoriaSeeder
        $catAbarrotes = Categoria::where('nombre', 'Abarrotes y Alimentos')->first() ?? Categoria::first();
        $catBebidas = Categoria::where('nombre', 'Bebidas no alcohólicas')->first() ?? Categoria::first();
        $catLicores = Categoria::where('nombre', 'Licores y Cervezas')->first() ?? Categoria::first();
        $catTecnologia = Categoria::where('nombre', 'Tecnología y Electrónica')->first() ?? Categoria::first();

        // Calcular margen automatico para el seeder
        $calcMargen = function($compra, $venta) {
            return (($venta - $compra) / $compra) * 100;
        };

        $productos = [
            [
                'nombre' => 'Coca Cola Lata 355ml',
                'descripcion' => 'Bebida carbonatada refrescante.',
                'categoria_id' => $catBebidas->id,
                'sku' => 'BEB-001',
                'codigo_barras' => '7451000000010',
                'precio_compra' => 0.50,
                'precio_venta' => 0.75,
                'precio_minimo' => 0.65,
                'margen' => $calcMargen(0.50, 0.75),
                'impuesto' => '7',
                'stock' => 100,
                'stock_minimo' => 24,
                'unidad_medida' => 'unidad',
            ],
            [
                'nombre' => 'Agua Embotellada 500ml',
                'descripcion' => 'Agua purificada sin gas.',
                'categoria_id' => $catBebidas->id,
                'sku' => 'BEB-002',
                'codigo_barras' => '7451000000027',
                'precio_compra' => 0.25,
                'precio_venta' => 0.50,
                'precio_minimo' => 0.40,
                'margen' => $calcMargen(0.25, 0.50),
                'impuesto' => '7',
                'stock' => 200,
                'stock_minimo' => 50,
                'unidad_medida' => 'unidad',
            ],
            [
                'nombre' => 'Arroz Especial 1kg',
                'descripcion' => 'Arroz de primera calidad.',
                'categoria_id' => $catAbarrotes->id,
                'sku' => 'ABA-001',
                'codigo_barras' => '7451000000034',
                'precio_compra' => 0.90,
                'precio_venta' => 1.20,
                'precio_minimo' => 1.10,
                'margen' => $calcMargen(0.90, 1.20),
                'impuesto' => '0',
                'stock' => 150,
                'stock_minimo' => 30,
                'unidad_medida' => 'paquete',
            ],
            [
                'nombre' => 'Leche Entera 946ml',
                'descripcion' => 'Leche de vaca pasteurizada.',
                'categoria_id' => $catAbarrotes->id,
                'sku' => 'ABA-002',
                'codigo_barras' => '7451000000041',
                'precio_compra' => 1.80,
                'precio_venta' => 2.25,
                'precio_minimo' => 2.10,
                'margen' => $calcMargen(1.80, 2.25),
                'impuesto' => '0',
                'stock' => 80,
                'stock_minimo' => 15,
                'unidad_medida' => 'unidad',
            ],
            [
                'nombre' => 'Pan Molde Blanco',
                'descripcion' => 'Pan tajado blanco suave.',
                'categoria_id' => $catAbarrotes->id,
                'sku' => 'ABA-003',
                'codigo_barras' => '7451000000058',
                'precio_compra' => 0.70,
                'precio_venta' => 1.05,
                'precio_minimo' => 0.90,
                'margen' => $calcMargen(0.70, 1.05),
                'impuesto' => '0',
                'stock' => 60,
                'stock_minimo' => 10,
                'unidad_medida' => 'paquete',
            ],
            [
                'nombre' => 'Cerveza Nacional Lata',
                'descripcion' => 'Cerveza tipo lager de Panamá.',
                'categoria_id' => $catLicores->id,
                'sku' => 'LIC-001',
                'codigo_barras' => '7451000000065',
                'precio_compra' => 0.85,
                'precio_venta' => 1.25,
                'precio_minimo' => 1.10,
                'margen' => $calcMargen(0.85, 1.25),
                'impuesto' => '10',
                'stock' => 120,
                'stock_minimo' => 24,
                'unidad_medida' => 'unidad',
            ],
        ];

        foreach ($productos as $producto) {
            Producto::updateOrCreate(['sku' => $producto['sku']], $producto);
        }

        // Crear 15 productos adicionales usando faker
        Producto::factory()->count(15)->create();
    }
}
