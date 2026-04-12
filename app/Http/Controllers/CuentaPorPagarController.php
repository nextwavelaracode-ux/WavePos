<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\PagoCompra;
use App\Models\Proveedor;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuentaPorPagarController extends Controller
{
    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'sucursal'])
            ->where('tipo_compra', 'credito');

        // Filtros
        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }
        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }
        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_compra', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_compra', '<=', $request->fecha_fin);
        }

        $compras = $query->orderBy('fecha_compra', 'desc')->paginate(25);

        // Dashboard stats
        $totalPendiente = Compra::where('tipo_compra', 'credito')->sum('saldo_pendiente');
        $totalPagado = Compra::where('tipo_compra', 'credito')->sum('total_pagado');
        $comprasVencidas = Compra::where('tipo_compra', 'credito')
            ->where('saldo_pendiente', '>', 0)
            ->whereDate('fecha_vencimiento', '<', now()->toDateString())
            ->count();
        $comprasMes = Compra::where('tipo_compra', 'credito')
            ->whereMonth('fecha_compra', now()->month)
            ->whereYear('fecha_compra', now()->year)
            ->count();

        $proveedores = Proveedor::orderBy('empresa')->get();
        $sucursales = Sucursal::orderBy('nombre')->get();

        return view('cuentas-por-pagar.index', compact(
            'compras', 'totalPendiente', 'totalPagado', 'comprasVencidas', 'comprasMes',
            'proveedores', 'sucursales'
        ));
    }

    public function show(Compra $compra)
    {
        abort_if($compra->tipo_compra !== 'credito', 404);
        
        $compra->load(['proveedor', 'sucursal', 'detalles.producto', 'pagos.usuario']);
        
        return view('cuentas-por-pagar.show', compact('compra'));
    }

    public function storePago(Request $request, Compra $compra)
    {
        abort_if($compra->tipo_compra !== 'credito', 404);

        $saldoReal = $compra->total - $compra->total_pagado;

        $request->validate([
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque,yappy,nequi,yappy_nequi',
            'monto' => 'required|numeric|min:0.01|max:' . $saldoReal,
            'referencia' => 'nullable|string|max:255',
            'fecha_pago' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        if (in_array($request->metodo_pago, ['transferencia', 'cheque', 'yappy', 'nequi', 'yappy_nequi']) && empty($request->referencia)) {
            return back()->withErrors(['referencia' => 'La referencia es obligatoria para este método de pago.'])->withInput();
        }

        DB::beginTransaction();
        try {
            // Guardar el pago
            $pago = new PagoCompra();
            $pago->compra_id = $compra->id;
            $pago->user_id = auth()->id() ?? 1; // Fallback
            $pago->metodo_pago = $request->metodo_pago;
            $pago->monto = $request->monto;
            $pago->referencia = $request->referencia;
            $pago->fecha_pago = $request->fecha_pago;
            $pago->observaciones = $request->observaciones;
            $pago->save();

            // Actualizar compra
            $compra->total_pagado += $request->monto;
            $compra->saldo_pendiente = $compra->total - $compra->total_pagado;

            // Determinar estado
            if ($compra->saldo_pendiente <= 0) {
                $compra->estado_pago = 'pagado';
                $mensaje_alert = 'Pago completo registrado con éxito.';
            } elseif ($compra->fecha_vencimiento && $compra->fecha_vencimiento < now()->toDateString()) {
                $compra->estado_pago = 'vencido';
                $mensaje_alert = 'Abono realizado correctamente.';
            } else {
                $compra->estado_pago = 'parcial';
                $mensaje_alert = 'Abono realizado correctamente.';
            }

            $compra->save();

            DB::commit();
            return redirect()->route('cuentas-por-pagar.show', $compra->id)
                ->with('sweet_alert', [
                    'type'    => 'success',
                    'title'   => '¡Éxito!',
                    'message' => $mensaje_alert,
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('sweet_alert', [
                'type'    => 'error',
                'title'   => 'Error',
                'message' => 'No se pudo registrar el pago: ' . $e->getMessage(),
            ]);
        }
    }

    public function historial(Request $request)
    {
        $pagos = PagoCompra::with(['compra.proveedor', 'usuario'])
            ->orderBy('fecha_pago', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25);

        return view('cuentas-por-pagar.historial', compact('pagos'));
    }

    public function vencidas()
    {
        $compras = Compra::with(['proveedor', 'sucursal'])
            ->where('tipo_compra', 'credito')
            ->where('saldo_pendiente', '>', 0)
            ->whereDate('fecha_vencimiento', '<', now()->toDateString())
            ->orderBy('fecha_vencimiento', 'asc')
            ->paginate(25);

        return view('cuentas-por-pagar.vencidas', compact('compras'));
    }

    public function reporteProveedores(Request $request)
    {
        // Reporte agrupado por proveedores
        $proveedores = Proveedor::withSum(['compras as total_comprado' => function($q) {
                $q->where('tipo_compra', 'credito');
            }], 'total')
            ->withSum(['compras as total_pagado' => function($q) {
                $q->where('tipo_compra', 'credito');
            }], 'total_pagado')
            ->withSum(['compras as saldo_pendiente' => function($q) {
                $q->where('tipo_compra', 'credito');
            }], 'saldo_pendiente')
            ->withCount(['compras as total_compras' => function($q) {
                $q->where('tipo_compra', 'credito');
            }])
            ->get();

        $proveedores = $proveedores->filter(function($p) {
            return $p->total_compras > 0;
        });

        // NOTA: Acá podríamos implementar la exportación Excel si la librería estuviera lista.
        // O retornar una vista normal por ahora.
        
        return view('cuentas-por-pagar.reporte', compact('proveedores'));
    }
}
