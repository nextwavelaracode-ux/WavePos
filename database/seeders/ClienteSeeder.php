<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder

{
    public function run(): void
    {
        $clientes = [
            // ── Cliente Natural ──────────────────────────────
            [
                'tipo_cliente'   => 'natural',
                'nombre'         => 'María',
                'apellido'       => 'González',
                'cedula'         => '8-234-1234',
                'telefono'       => '6000-1001',
                'email'          => 'maria.gonzalez@gmail.com',
                'direccion'      => 'Calle 50, Local 12',
                'provincia'      => 'Panamá',
                'distrito'       => 'San Miguelito',
                'pais'           => 'Panamá',
                'limite_credito' => 0,
                'estado'         => true,
                'notas'          => 'Cliente frecuente de abarrotes.',
            ],

            // ── Cliente Jurídico (Empresa) ─────────────────────
            [
                'tipo_cliente'   => 'juridico',
                'empresa'        => 'Constructora Horizonte, S.A.',
                'ruc'            => '155-60-2024',
                'dv'             => '78',
                'telefono'       => '270-5000',
                'email'          => 'compras@horizontesa.com.pa',
                'direccion'      => 'Vía Brasil, Edificio Plaza 1000, Piso 3',
                'provincia'      => 'Panamá',
                'distrito'       => 'Bella Vista',
                'pais'           => 'Panamá',
                'limite_credito' => 5000.00,
                'estado'         => true,
                'notas'          => 'Empresa de construcción, maneja crédito mensual.',
            ],

            // ── Cliente Extranjero ─────────────────────────────
            [
                'tipo_cliente'   => 'extranjero',
                'nombre'         => 'James',
                'apellido'       => 'Carter',
                'empresa'        => null,
                'pasaporte'      => 'USA-20231045',
                'telefono'       => '+1-754-300-1234',
                'email'          => 'james.carter@email.com',
                'direccion'      => 'Punta Pacífica, Torre PHR, Apto 1502',
                'provincia'      => 'Panamá',
                'distrito'       => 'San Francisco',
                'pais'           => 'Estados Unidos',
                'limite_credito' => 0,
                'estado'         => true,
                'notas'          => 'Inversionista extranjero, compras en efectivo o tarjeta.',
            ],

            // ── Cliente B2B ────────────────────────────────────
            [
                'tipo_cliente'   => 'b2b',
                'empresa'        => 'Distribuidora El Éxito, S.R.L.',
                'ruc'            => '201-20-2020',
                'dv'             => '41',
                'nombre'         => 'Luis',
                'apellido'       => 'Herrera',
                'telefono'       => '6500-8888',
                'email'          => 'luis@distribuidoraelexito.pa',
                'direccion'      => 'Zona Libre de Colón, Bodega 14',
                'provincia'      => 'Colón',
                'distrito'       => 'Colón',
                'pais'           => 'Panamá',
                'limite_credito' => 15000.00,
                'estado'         => true,
                'notas'          => 'Cliente B2B de alto volumen, pago a 30 días.',
            ],

            // ── Cliente B2C ────────────────────────────────────
            [
                'tipo_cliente'   => 'b2c',
                'nombre'         => 'Carlos',
                'apellido'       => 'Morales',
                'cedula'         => '4-102-3456',
                'telefono'       => '6200-4455',
                'email'          => 'cmorales87@hotmail.com',
                'provincia'      => 'Chiriquí',
                'distrito'       => 'David',
                'pais'           => 'Panamá',
                'limite_credito' => 0,
                'estado'         => true,
                'notas'          => 'Consumidor final, compras pequeñas frecuentes.',
            ],
        ];

        foreach ($clientes as $c) {
            Cliente::updateOrCreate(['email' => $c['email']], $c);
        }

        // Crear 15 clientes adicionales usando faker
        Cliente::factory()->count(15)->create();
    }
}
