<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $precioCompra = fake()->randomFloat(2, 1, 100);
        $precioVenta = $precioCompra * 1.3;

        return [
            'nombre'          => fake()->words(3, true),
            'descripcion'     => fake()->sentence(),
            'categoria_id'    => Categoria::inRandomOrder()->first()?->id ?? Categoria::factory(),
            'sku'             => fake()->unique()->bothify('PROD-####'),
            'codigo_barras'   => fake()->unique()->ean13(),
            'precio_compra'   => $precioCompra,
            'precio_venta'    => $precioVenta,
            'precio_minimo'   => $precioVenta * 0.9,
            'margen'          => 30.00,
            'impuesto'        => fake()->randomElement(['0', '7', '10', '15']),
            'stock'           => fake()->numberBetween(10, 500),
            'stock_minimo'    => 10,
            'unidad_medida'   => 'unidad',
            'estado'          => true,
        ];
    }
}
