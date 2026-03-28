<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use App\Models\User;

class MovimientoInventarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuario = User::first();
        if (!$usuario) {
            return;
        }

        $productos = Producto::all();
        if ($productos->isEmpty()) {
            return;
        }

        foreach ($productos as $producto) {
            // Registrar entrada inicial del stock actual
            if ($producto->stock > 0) {
                MovimientoInventario::create([
                    'producto_id'    => $producto->id,
                    'tipo'           => 'entrada',
                    'motivo'         => 'compra',
                    'cantidad'       => $producto->stock,
                    'stock_anterior' => 0,
                    'stock_nuevo'    => $producto->stock,
                    'observaciones'  => 'Stock inicial del sistema',
                    'usuario_id'     => $usuario->id,
                    'fecha'          => now()->subDays(rand(5, 15))->toDateString(),
                ]);
            }
        }

        // Registrar una salida de ejemplo en el primer producto
        $primerProducto = $productos->first();
        if ($primerProducto && $primerProducto->stock >= 5) {
            $stockAnterior = $primerProducto->stock;
            $cantidadSalida = 5;
            $stockNuevo = $stockAnterior - $cantidadSalida;

            $primerProducto->update(['stock' => $stockNuevo]);

            MovimientoInventario::create([
                'producto_id'    => $primerProducto->id,
                'tipo'           => 'salida',
                'motivo'         => 'venta',
                'cantidad'       => $cantidadSalida,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockNuevo,
                'observaciones'  => 'Venta registrada de ejemplo',
                'usuario_id'     => $usuario->id,
                'fecha'          => now()->subDays(2)->toDateString(),
            ]);
        }

        // Registrar un ajuste de ejemplo
        $segundoProducto = $productos->skip(1)->first();
        if ($segundoProducto) {
            $stockAnterior = $segundoProducto->stock;
            $stockNuevo = $stockAnterior + 10;

            $segundoProducto->update(['stock' => $stockNuevo]);

            MovimientoInventario::create([
                'producto_id'    => $segundoProducto->id,
                'tipo'           => 'ajuste',
                'motivo'         => 'ajuste_manual',
                'cantidad'       => 10,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockNuevo,
                'observaciones'  => 'Ajuste por conteo físico',
                'usuario_id'     => $usuario->id,
                'fecha'          => now()->subDay()->toDateString(),
            ]);
        }
    }
}
