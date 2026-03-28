<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Categoria;
use App\Models\MovimientoInventario;

class InventarioController extends Controller
{
    // ─── Listado de stock ─────────────────────────────────────
    public function index()
    {
        $productos   = Producto::with(['categoria'])->latest()->get();
        $categorias  = Categoria::where('estado', true)->whereNull('parent_id')->orderBy('nombre')->get();
        $proveedores = Proveedor::where('estado', true)->orderBy('empresa')->get();

        // Contadores resumen
        $totalProductos = $productos->count();
        $stockBajo      = $productos->filter(fn ($p) => $p->stock <= $p->stock_minimo && $p->stock > 0)->count();
        $sinStock        = $productos->where('stock', '<=', 0)->count();
        $totalAlertas   = $stockBajo + $sinStock;

        return view('pages.inventario.inventario', compact(
            'productos', 'categorias', 'proveedores',
            'totalProductos', 'stockBajo', 'sinStock', 'totalAlertas'
        ));
    }

    // ─── Movimientos ──────────────────────────────────────────
    public function movimientos()
    {
        $movimientos = MovimientoInventario::with(['producto', 'proveedor', 'usuario'])
            ->latest()
            ->get();

        return view('pages.inventario.movimientos', compact('movimientos'));
    }

    // ─── Alertas ──────────────────────────────────────────────
    public function alertas()
    {
        $alertas = Producto::with('categoria')
            ->where('estado', true)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')
            ->get();

        return view('pages.inventario.alertas', compact('alertas'));
    }

    // ─── Registrar entrada ────────────────────────────────────
    public function entrada(Request $request)
    {
        $validated = $request->validate([
            'producto_id'    => 'required|exists:productos,id',
            'proveedor_id'   => 'nullable|exists:proveedores,id',
            'cantidad'       => 'required|integer|min:1',
            'precio_compra'  => 'nullable|numeric|min:0',
            'fecha'          => 'required|date',
            'numero_factura' => 'nullable|string|max:100',
            'observaciones'  => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $producto = Producto::lockForUpdate()->findOrFail($validated['producto_id']);
            $stockAnterior = $producto->stock;
            $stockNuevo    = $stockAnterior + $validated['cantidad'];

            $producto->update(['stock' => $stockNuevo]);

            MovimientoInventario::create([
                'producto_id'    => $producto->id,
                'tipo'           => 'entrada',
                'motivo'         => 'compra',
                'cantidad'       => $validated['cantidad'],
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockNuevo,
                'proveedor_id'   => $validated['proveedor_id'] ?? null,
                'precio_compra'  => $validated['precio_compra'] ?? null,
                'numero_factura' => $validated['numero_factura'] ?? null,
                'observaciones'  => $validated['observaciones'] ?? null,
                'usuario_id'     => auth()->id() ?? 1,
                'fecha'          => $validated['fecha'],
            ]);
        });

        return redirect()->route('inventario.stock')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Entrada registrada!',
                'message' => 'El inventario fue actualizado correctamente.',
            ]);
    }

    // ─── Registrar salida ─────────────────────────────────────
    public function salida(Request $request)
    {
        $validated = $request->validate([
            'producto_id'  => 'required|exists:productos,id',
            'cantidad'     => 'required|integer|min:1',
            'motivo'       => 'required|in:venta,producto_dañado,devolucion_proveedor,ajuste_manual,transferencia',
            'fecha'        => 'required|date',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $producto = Producto::findOrFail($validated['producto_id']);

        // No permitir stock negativo
        if ($producto->stock < $validated['cantidad']) {
            return redirect()->route('inventario.stock')
                ->with('sweet_alert', [
                    'type'    => 'error',
                    'title'   => 'Stock insuficiente',
                    'message' => "El producto \"{$producto->nombre}\" solo tiene {$producto->stock} unidades disponibles.",
                ]);
        }

        DB::transaction(function () use ($validated, $producto) {
            $producto = Producto::lockForUpdate()->findOrFail($validated['producto_id']);
            $stockAnterior = $producto->stock;
            $stockNuevo    = $stockAnterior - $validated['cantidad'];

            $producto->update(['stock' => $stockNuevo]);

            MovimientoInventario::create([
                'producto_id'    => $producto->id,
                'tipo'           => 'salida',
                'motivo'         => $validated['motivo'],
                'cantidad'       => $validated['cantidad'],
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockNuevo,
                'observaciones'  => $validated['observaciones'] ?? null,
                'usuario_id'     => auth()->id() ?? 1,
                'fecha'          => $validated['fecha'],
            ]);
        });

        return redirect()->route('inventario.stock')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Salida registrada!',
                'message' => 'El inventario fue actualizado correctamente.',
            ]);
    }

    // ─── Ajustar inventario ───────────────────────────────────
    public function ajuste(Request $request)
    {
        $validated = $request->validate([
            'producto_id'   => 'required|exists:productos,id',
            'stock_nuevo'   => 'required|integer|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $producto = Producto::lockForUpdate()->findOrFail($validated['producto_id']);
            $stockAnterior = $producto->stock;
            $stockNuevo    = $validated['stock_nuevo'];
            $diferencia    = $stockNuevo - $stockAnterior;

            $producto->update(['stock' => $stockNuevo]);

            MovimientoInventario::create([
                'producto_id'    => $producto->id,
                'tipo'           => 'ajuste',
                'motivo'         => 'ajuste_manual',
                'cantidad'       => abs($diferencia),
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockNuevo,
                'observaciones'  => $validated['observaciones'] ?? 'Ajuste manual de inventario',
                'usuario_id'     => auth()->id() ?? 1,
                'fecha'          => now()->toDateString(),
            ]);
        });

        return redirect()->route('inventario.stock')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Ajuste realizado!',
                'message' => 'El stock fue ajustado correctamente.',
            ]);
    }
}
