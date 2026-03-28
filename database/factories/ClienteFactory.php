<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        $tipo = fake()->randomElement(['natural', 'juridico', 'extranjero', 'b2b', 'b2c']);
        
        return [
            'tipo_cliente'   => $tipo,
            'nombre'         => in_array($tipo, ['natural', 'extranjero', 'b2c']) ? fake()->firstName() : null,
            'apellido'       => in_array($tipo, ['natural', 'extranjero', 'b2c']) ? fake()->lastName() : null,
            'empresa'        => in_array($tipo, ['juridico', 'b2b']) ? fake()->company() : null,
            'cedula'         => $tipo === 'natural' ? fake()->numerify('#-###-####') : null,
            'ruc'            => in_array($tipo, ['juridico', 'b2b']) ? fake()->numerify('######-#-######') : null,
            'dv'             => in_array($tipo, ['juridico', 'b2b']) ? fake()->numerify('##') : null,
            'pasaporte'      => $tipo === 'extranjero' ? fake()->bothify('??#######') : null,
            'telefono'       => fake()->phoneNumber(),
            'email'          => fake()->unique()->safeEmail(),
            'direccion'      => fake()->address(),
            'provincia'      => 'Panamá',
            'distrito'       => 'Panamá',
            'pais'           => $tipo === 'extranjero' ? fake()->country() : 'Panamá',
            'limite_credito' => fake()->randomFloat(2, 0, 10000),
            'estado'          => true,
            'notas'           => fake()->sentence(),
        ];
    }
}
