<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Spatie\LaravelPdf\Support\pdf;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'sucursal', 'usuario']);

        if ($request->has('search') && $request->search != '') {
            $query->where('numero_factura', 'like', '%' . $request->search . '%')
                  ->orWhereHas('proveedor', function ($q) use ($request) {
                      $q->where('empresa', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $compras = $query->latest('fecha_compra')->paginate(10);
        return view('pages.compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('estado', true)->orderBy('empresa')->get();
        $sucursales  = Sucursal::orderBy('nombre')->get();
        $productos   = Producto::where('estado', true)->orderBy('nombre')->get();
        $proximoNumero = Empresa::previewNumeroCompra();

        return view('pages.compras.create', compact('proveedores', 'sucursales', 'productos', 'proximoNumero'));
    }

    public function store(Request $request)
    {
        $this->authorize('compras.crear');
        \Log::info('Datos recibidos en store:', $request->all());
        $validated = $request->validate([
            'sucursal_id'      => 'required|exists:sucursales,id',
            'proveedor_id'     => 'required|exists:proveedores,id',
            // numero_factura ya NO se valida — se genera automáticamente
            'fecha_compra'     => 'required|date',
            'tipo_compra'      => 'required|in:contado,credito',
            'metodo_pago'      => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
            'observaciones'    => 'nullable|string',
            'productos'        => 'required|array|min:1',
            'productos.*.id'                  => 'required|exists:productos,id',
            'productos.*.cantidad'            => 'required|integer|min:1',
            'productos.*.precio_compra'       => 'required|numeric|min:0',
            'productos.*.tasa_impuesto'       => 'nullable|numeric|min:0|max:100',
            'productos.*.porcentaje_descuento' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $total           = 0;
            $totalBruto      = 0;
            $totalImpuestos  = 0;
            $totalDescuentos = 0;

            foreach ($request->productos as $prodReq) {
                $cantidad   = (int) $prodReq['cantidad'];
                $precio     = (float) $prodReq['precio_compra'];
                $iva        = (float) ($prodReq['tasa_impuesto'] ?? 0);
                $desc       = (float) ($prodReq['porcentaje_descuento'] ?? 0);

                $bruto       = $cantidad * $precio;
                $descMonto   = $bruto * ($desc / 100);
                $baseNeta    = $bruto - $descMonto;
                $ivaMonto    = $baseNeta * ($iva / 100);
                $subtotalNet = $baseNeta + $ivaMonto;

                $totalBruto      += $bruto;
                $totalDescuentos += $descMonto;
                $totalImpuestos  += $ivaMonto;
                $total           += $subtotalNet;
            }

            $compra = Compra::create([
                'sucursal_id'      => $validated['sucursal_id'],
                'proveedor_id'     => $validated['proveedor_id'],
                'user_id'          => auth()->id() ?? 1,
                // Generar número correlativo automático
                'numero_factura'   => Empresa::nextNumeroCompra(),
                'fecha_compra'     => $validated['fecha_compra'],
                'tipo_compra'      => $validated['tipo_compra'],
                'metodo_pago'      => $validated['metodo_pago'] ?? null,
                'fecha_vencimiento'=> $validated['fecha_vencimiento'] ?? null,
                'subtotal'         => $totalBruto - $totalDescuentos,
                'total_impuestos'  => $totalImpuestos,
                'total_descuentos' => $totalDescuentos,
                'total'            => $total,
                'estado'           => 'registrada',
                'observaciones'    => $validated['observaciones'] ?? null,
            ]);

            foreach ($request->productos as $prodReq) {
                $producto   = Producto::where('id', $prodReq['id'])->lockForUpdate()->first();
                $cantidad   = (int) $prodReq['cantidad'];
                $precio     = (float) $prodReq['precio_compra'];
                $iva        = (float) ($prodReq['tasa_impuesto'] ?? 0);
                $desc       = (float) ($prodReq['porcentaje_descuento'] ?? 0);

                $descMonto  = $cantidad * $precio * ($desc / 100);
                $baseNeta   = ($cantidad * $precio) - $descMonto;
                $ivaMonto   = $baseNeta * ($iva / 100);
                $subtotal   = $baseNeta + $ivaMonto;

                DetalleCompra::create([
                    'compra_id'            => $compra->id,
                    'producto_id'          => $producto->id,
                    'cantidad'             => $cantidad,
                    'precio_compra'        => $precio,
                    'tasa_impuesto'        => $iva,
                    'porcentaje_descuento' => $desc,
                    'monto_impuesto'       => $ivaMonto,
                    'monto_descuento'      => $descMonto,
                    'subtotal'             => $subtotal,
                ]);

                // Registrar en el Kardex ANTES de actualizar el stock (para tener el stock_anterior)
                $stockAnterior = $producto->stock;
                $stockNuevo = $stockAnterior + $cantidad;

                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'entrada',
                    'motivo' => 'compra',
                    'cantidad' => $cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'proveedor_id' => $compra->proveedor_id,
                    'precio_compra' => $precio,
                    'numero_factura' => $compra->numero_factura,
                    'observaciones' => "Compra #{$compra->id} registrada.",
                    'usuario_id' => auth()->id() ?? 1,
                    'fecha' => today(),
                ]);

                // Actualizar stock y último precio de compra
                $producto->stock = $stockNuevo;
                $producto->precio_compra = $precio; // Actualizamos el costo
                $producto->save();
            }

            DB::commit();

            return redirect()->route('compras.show', $compra->id)->with('sweet_alert', [
                'type' => 'success',
                'title' => '¡Compra Registrada!',
                'message' => "La compra #{$compra->id} ha sido registrada con éxito."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error registrando compra: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withInput()->with('sweet_alert', [
                'type' => 'error',
                'title' => 'Error Interno',
                'message' => 'Hubo un problema al registrar la compra: ' . $e->getMessage()
            ]);
        }
    }

    public function show(Compra $compra)
    {
        $compra->load(['detalles.producto', 'proveedor', 'sucursal', 'usuario']);
        return view('pages.compras.show', compact('compra'));
    }

    public function anular(Request $request, Compra $compra)
    {
        $this->authorize('compras.anular');
        
        if ($compra->estado !== 'registrada') {
            return back()->with('sweet_alert', [
                'type' => 'error',
                'title' => 'Acción no permitida',
                'message' => 'Solo se pueden anular compras registradas.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Bloquear los productos de la compra para revertir stock
            foreach ($compra->detalles as $detalle) {
                $producto = Producto::where('id', $detalle->producto_id)->lockForUpdate()->first();
                
                // Verificar si hay stock suficiente para revertir
                if ($producto->stock < $detalle->cantidad) {
                    throw new \Exception("No hay stock suficiente del producto {$producto->nombre} para revertir la compra. Stock actual: {$producto->stock}, a revertir: {$detalle->cantidad}.");
                }

                $stockAnterior = $producto->stock;
                $stockNuevo = $stockAnterior - $detalle->cantidad;

                // Registrar en kardex como ajuste por anulación
                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'salida',
                    'motivo' => 'anulacion_compra',
                    'cantidad' => $detalle->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'proveedor_id' => $compra->proveedor_id,
                    'precio_compra' => $detalle->precio_compra,
                    'numero_factura' => $compra->numero_factura,
                    'observaciones' => "Reversión por anulación de compra #{$compra->id}.",
                    'usuario_id' => auth()->id(),
                    'fecha' => today(),
                ]);

                // Actualizar stock
                $producto->stock = $stockNuevo;
                $producto->save();
            }

            // Cambiar estado de la compra
            $compra->estado = 'anulada';
            $compra->observaciones = $compra->observaciones . "\nAnulada el " . now()->format('Y-m-d H:i') . " por " . auth()->user()->name . ". Motivo: " . $request->motivo_anulacion;
            $compra->save();

            DB::commit();

            return back()->with('sweet_alert', [
                'type' => 'success',
                'title' => '¡Compra Anulada!',
                'message' => "La compra #{$compra->id} ha sido anulada y el stock revertido correctamente."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('sweet_alert', [
                'type' => 'error',
                'title' => 'Error al anular',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function pdf(Compra $compra)
    {
        $compra->load(['detalles.producto', 'proveedor', 'sucursal', 'usuario']);
        
        return pdf()
                  ->view('pages.compras.pdf', compact('compra'))
                  ->format('a4')
                  ->name("Compra_{$compra->id}_{$compra->fecha_compra->format('Ymd')}.pdf");
    }
}
