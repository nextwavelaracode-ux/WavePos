<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    public function index()
    {
        $devoluciones = Devolucion::with(['venta', 'producto', 'usuario'])
            ->latest()
            ->paginate(20);

        $ventas = Venta::where('estado', 'completada')
            ->with('detalles.producto')
            ->latest()
            ->get();

        return view('pages.caja.devoluciones', compact('devoluciones', 'ventas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'venta_id'    => 'required|exists:ventas,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1',
            'motivo'      => 'required|string|min:5|max:300',
        ]);

        try {
            DB::beginTransaction();

            $venta    = Venta::findOrFail($request->venta_id);
            $detalle  = $venta->detalles()->where('producto_id', $request->producto_id)->first();

            if (!$detalle) {
                return back()->with('error', 'El producto no pertenece a esa venta.');
            }

            if ($request->cantidad > $detalle->cantidad) {
                return back()->with('error', "No puede devolver más de {$detalle->cantidad} unidades.");
            }

            $producto      = Producto::lockForUpdate()->findOrFail($request->producto_id);
            $stockAnterior = $producto->stock;
            $producto->increment('stock', $request->cantidad);
            $stockNuevo = $producto->fresh()->stock;

            // Kardex entrada por devolución
            MovimientoInventario::create([
                'producto_id'    => $request->producto_id,
                'tipo'           => 'entrada',
                'motivo'         => 'devolucion',
                'cantidad'       => $request->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockNuevo,
                'numero_factura' => $venta->numero,
                'observaciones'  => "Devolución venta #{$venta->numero}: {$request->motivo}",
                'usuario_id'     => auth()->id() ?? 1,
                'fecha'          => now()->toDateString(),
            ]);

            Devolucion::create([
                'venta_id'    => $request->venta_id,
                'producto_id' => $request->producto_id,
                'user_id'     => auth()->id() ?? 1,
                'cantidad'    => $request->cantidad,
                'motivo'      => $request->motivo,
                'fecha'       => now()->toDateString(),
            ]);

            DB::commit();

            return back()->with('success', 'Devolución registrada. Stock actualizado correctamente.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar devolución: ' . $e->getMessage());
        }
    }
}
