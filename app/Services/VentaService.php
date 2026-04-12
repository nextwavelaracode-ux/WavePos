<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\PagoVenta;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use App\Models\CuentaPorCobrar;
use Illuminate\Support\Facades\DB;
use Exception;

class VentaService
{
    /**
     * Procesa el flujo completo de una Venta de manera unificada
     */
    public function procesarVenta(array $datos, Caja $caja, int $userId): Venta
    {
        DB::beginTransaction();

        try {
            // 1. Calcular totales previos y validar stock antes de crear nada
            ['subtotal' => $subtotal, 'itbms' => $itbms, 'total' => $total] = $this->calcularTotales($datos['items']);

            // Validar monto pagado vs total
            $totalPagado = collect($datos['pagos'])->sum('monto');
            if (round($totalPagado, 2) < round($total, 2)) {
                throw new Exception("El monto pagado ({$totalPagado}) es menor al total ({$total}).");
            }

            // 2. Crear Venta
            $venta = $this->crearCabecera($datos, $caja, $userId, $subtotal, $itbms, $total);

            // 3. Procesar Items (Detalles + Stock) y actualizar Kardex
            $this->procesarItems($venta, $datos['items'], $userId);

            // 4. Registrar Pagos y posible Cuenta por Cobrar
            $this->registrarPagos($venta, $datos['pagos'], $datos['fecha_vencimiento'] ?? null);

            DB::commit();

            return $venta;

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calcula los totales y valida existencia de stock (Bloqueo Pessimista)
     */
    private function calcularTotales(array $items): array
    {
        $subtotal = 0;
        $itbms = 0;

        foreach ($items as $item) {
            $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);

            if ($producto->stock < $item['cantidad']) {
                throw new Exception("Stock insuficiente para '{$producto->nombre}'. Disponible: {$producto->stock}");
            }

            $lineaSub   = round($item['precio_unitario'] * $item['cantidad'], 2);
            $lineaItbms = round($lineaSub * ($item['impuesto'] / 100), 2);
            $subtotal  += $lineaSub;
            $itbms     += $lineaItbms;
        }

        return [
            'subtotal' => $subtotal,
            'itbms'    => $itbms,
            'total'    => round($subtotal + $itbms, 2)
        ];
    }

    private function crearCabecera(array $datos, Caja $caja, int $userId, float $subtotal, float $itbms, float $total): Venta
    {
        return Venta::create([
            'numero'               => Venta::generateNumero(),
            'caja_id'              => $caja->id,
            'sucursal_id'          => $datos['sucursal_id'],
            'cliente_id'           => $datos['cliente_id'] ?? null,
            'user_id'              => $userId,
            'subtotal'             => $subtotal,
            'itbms'                => $itbms,
            'total'                => $total,
            'estado'               => 'completada',
            'fecha'                => now()->toDateString(),
            'forma_pago_dian'      => $datos['forma_pago_dian'] ?? null,
            'metodo_pago_dian_id'  => $datos['metodo_pago_dian_id'] ?? null,
        ]);
    }

    private function procesarItems(Venta $venta, array $items, int $userId): void
    {
        foreach ($items as $item) {
            // No requiere lockForUpdate aquí, ya se bloqueó en calcularTotales() y estamos en la misma transacción
            $producto   = Producto::findOrFail($item['producto_id']);
            $lineaSub   = round($item['precio_unitario'] * $item['cantidad'], 2);
            $lineaItbms = round($lineaSub * ($item['impuesto'] / 100), 2);

            DetalleVenta::create([
                'venta_id'        => $venta->id,
                'producto_id'     => $item['producto_id'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'impuesto'        => $item['impuesto'],
                'subtotal'        => $lineaSub,
                'total'           => round($lineaSub + $lineaItbms, 2),
            ]);

            // Descontar inventario
            $stockAnterior = $producto->stock;
            $producto->decrement('stock', $item['cantidad']);
            $stockNuevo = $producto->fresh()->stock;

            $this->actualizarKardex($producto->id, $item['cantidad'], $stockAnterior, $stockNuevo, $venta->numero, $userId);
        }
    }

    private function registrarPagos(Venta $venta, array $pagos, ?string $fechaVencimiento): void
    {
        $totalCredito = 0;

        foreach ($pagos as $pago) {
            PagoVenta::create([
                'venta_id'     => $venta->id,
                'metodo'       => $pago['metodo'],
                'monto'        => $pago['monto'],
                'referencia'   => $pago['referencia'] ?? null,
                'tipo_tarjeta' => $pago['tipo_tarjeta'] ?? null,
                'banco'        => $pago['banco'] ?? null,
                'observaciones'=> $pago['observaciones'] ?? null,
            ]);

            if ($pago['metodo'] === 'credito') {
                $totalCredito += $pago['monto'];
            }
        }

        if ($totalCredito > 0) {
            CuentaPorCobrar::create([
                'venta_id'          => $venta->id,
                'cliente_id'        => $venta->cliente_id,
                'sucursal_id'       => $venta->sucursal_id,
                'total'             => $totalCredito,
                'total_pagado'      => 0,
                'saldo_pendiente'   => $totalCredito,
                'fecha_vencimiento' => $fechaVencimiento ?? now()->addDays(30),
                'estado'            => 'pendiente',
            ]);
        }
    }

    private function actualizarKardex(int $productoId, int $cantidad, int $stockAnterior, int $stockNuevo, string $numeroVenta, int $userId): void
    {
        MovimientoInventario::create([
            'producto_id'    => $productoId,
            'tipo'           => 'salida',
            'motivo'         => 'venta',
            'cantidad'       => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo'    => $stockNuevo,
            'numero_factura' => $numeroVenta,
            'observaciones'  => "Venta #{$numeroVenta}",
            'usuario_id'     => $userId,
            'fecha'          => now()->toDateString(),
        ]);
    }
}
