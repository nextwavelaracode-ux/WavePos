<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        return [
            'nombre'              => fake()->unique()->words(2, true),
            'descripcion'         => fake()->sentence(),
            'parent_id'           => null,
            'impuesto'            => fake()->randomElement(['0', '7', '10', '15']),
            'unidad_medida'       => fake()->randomElement(['unidad', 'paquete', 'litro', 'kg']),
            'ubicacion'           => 'Pasillo ' . fake()->numberBetween(1, 10),
            'atributos_tecnicos'  => null,
            'detalle'             => fake()->sentence(),
            'orden_visualizacion' => fake()->numberBetween(1, 100),
            'estado'              => true,
        ];
    }
}
