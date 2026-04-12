<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\PagoCuentaCobrar;
use App\Models\Caja;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuentaPorCobrarController extends Controller
{
    /**
     * Dashboard y Listado Principal.
     */
    public function index(Request $request)
    {
        $query = CuentaPorCobrar::with(['cliente', 'venta', 'sucursal'])
            ->latest();

        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->whereHas('cliente', function ($c) use ($q) {
                $c->where('nombre_completo', 'like', "%{$q}%")
                  ->orWhere('documento_principal', 'like', "%{$q}%");
            })->orWhereHas('venta', function ($v) use ($q) {
                $v->where('numero', 'like', "%{$q}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $cuentas = $query->paginate(25)->withQueryString();

        // KPIs
        $kpis = [
            'total_deuda' => CuentaPorCobrar::whereIn('estado', ['pendiente', 'parcial', 'vencido'])->sum('saldo_pendiente'),
            'total_vencido' => CuentaPorCobrar::where('estado', 'vencido')->sum('saldo_pendiente'),
            'clientes_con_deuda' => Cliente::whereHas('cuentasCobrar', function($q) {
                $q->whereIn('estado', ['pendiente', 'parcial', 'vencido']);
            })->count(),
            'cobros_mes' => PagoCuentaCobrar::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('monto'),
        ];

        return view('pages.cuentas.index', compact('cuentas', 'kpis'));
    }

    /**
     * Detalle de una cuenta.
     */
    public function show(CuentaPorCobrar $cuenta)
    {
        $cuenta->load(['cliente', 'venta.detalles.producto', 'sucursal', 'pagos.usuario']);
        return view('pages.cuentas.show', compact('cuenta'));
    }

    /**
     * Registrar un pago.
     */
    public function pagar(Request $request, CuentaPorCobrar $cuenta)
    {
        $request->validate([
            'monto'       => 'required|numeric|min:0.01|max:' . $cuenta->saldo_pendiente,
            'metodo'      => 'required|in:efectivo,tarjeta,transferencia,yappy',
            'referencia'  => 'nullable|string|max:100',
            'observaciones'=> 'nullable|string|max:255',
        ]);

        // Validar caja abierta
        $caja = Caja::where('estado', 'abierta')->latest()->first();
        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'No hay una caja abierta para registrar este cobro.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $pago = PagoCuentaCobrar::create([
                'cuenta_id'    => $cuenta->id,
                'caja_id'      => $caja->id,
                'user_id'      => auth()->id() ?? 1,
                'monto'        => $request->monto,
                'metodo'       => $request->metodo,
                'referencia'   => $request->referencia,
                'observaciones'=> $request->observaciones,
            ]);

            // Actualizar saldos de la cuenta
            $cuenta->total_pagado += $request->monto;
            $cuenta->saldo_pendiente = $cuenta->total - $cuenta->total_pagado;

            if ($cuenta->saldo_pendiente <= 0) {
                $cuenta->estado = 'pagado';
            } else {
                $cuenta->estado = 'parcial';
                // Si la fecha ya pasó, pero aún debe algo, podría seguir siendo 'vencido' 
                // o volver a evaluarse en el scheduler? Por simplicidad aquí lo dejamos en parcial.
            }
            
            $cuenta->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado con éxito.',
                'saldo'   => $cuenta->saldo_pendiente
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Historial de todos los pagos de AR.
     */
    public function historialPagos(Request $request)
    {
        $pagos = PagoCuentaCobrar::with(['cuenta.cliente', 'usuario', 'caja'])
            ->latest()
            ->paginate(25);
            
        return view('pages.cuentas.historial', compact('pagos'));
    }
}
