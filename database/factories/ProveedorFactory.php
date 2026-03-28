<?php

namespace Database\Factories;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    protected $model = Proveedor::class;

    public function definition(): array
    {
        return [
            'empresa'   => fake()->company(),
            'ruc'       => fake()->numerify('######-#-######'),
            'dv'        => fake()->numerify('##'),
            'contacto'  => fake()->name(),
            'telefono'  => fake()->phoneNumber(),
            'email'     => fake()->unique()->companyEmail(),
            'direccion' => fake()->address(),
            'provincia' => 'Panamá',
            'ciudad'    => 'Ciudad de Panamá',
            'pais'      => 'Panamá',
            'notas'     => fake()->text(100),
            'estado'    => true,
        ];
    }
}
