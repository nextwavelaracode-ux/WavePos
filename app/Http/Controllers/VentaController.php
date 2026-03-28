<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\PagoVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Sucursal;
use App\Models\MovimientoInventario;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\VentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    // ... index, store, etc.

    public function exportarHistorial(Request $request)
    {
        $formato = $request->route('formato') ?? 'excel';
        $filters = $request->only(['desde', 'hasta', 'estado', 'sucursal_id']);

        if ($formato === 'pdf') {
            $query = Venta::with(['cliente', 'sucursal', 'usuario'])->latest('fecha');
            if ($request->filled('desde')) $query->whereDate('fecha', '>=', $request->desde);
            if ($request->filled('hasta')) $query->whereDate('fecha', '<=', $request->hasta);
            if ($request->filled('estado')) $query->where('estado', $request->estado);
            if ($request->filled('sucursal_id')) $query->where('sucursal_id', $request->sucursal_id);

            $ventas = $query->get();
            $pdf = Pdf::loadView('exports.ventas-pdf', compact('ventas'));
            return $pdf->download('historial_ventas_' . now()->format('Ymd_His') . '.pdf');
        }

        return Excel::download(new VentasExport($filters), 'historial_ventas_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Pantalla POS principal.
     */
    public function pos()
    {
        $cajaAbierta = Caja::where('estado', 'abierta')->latest()->first();
        $productos   = Producto::with('categoria')
            ->where('estado', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();
        $categorias  = Categoria::whereNull('parent_id')->orWhereNull('parent_id')->orderBy('nombre')->get();
        $clientes    = Cliente::where('estado', true)->orderBy('nombre')->get();
        $sucursales  = Sucursal::orderBy('nombre')->get();

        $clientesData = $clientes->map(function($c) {
            $deuda        = $c->cuentasCobrar()
                ->whereIn('estado', ['pendiente', 'parcial', 'vencido'])
                ->get();
            
            $saldoDeuda = $deuda->sum('saldo_pendiente');
            $esMoroso   = $deuda->where('estado', 'vencido')->count() > 0;
                
            return [
                'id' => $c->id,
                'nombre' => $c->nombre_completo,
                'limite_credito' => (float)$c->limite_credito,
                'saldo_deudor' => (float)$saldoDeuda,
                'credito_disponible' => max(0, (float)$c->limite_credito - (float)$saldoDeuda),
                'es_moroso' => $esMoroso,
            ];
        })->keyBy('id');

        // POS Settings
        $posSettings = Setting::group('pos');

        return view('pages.caja.pos', compact(
            'cajaAbierta', 'productos', 'categorias', 'clientes',
            'sucursales', 'clientesData', 'posSettings'
        ));
    }

    /**
     * Confirmar y guardar venta (transacción atómica).
     */
    public function store(Request $request)
    {
        $request->validate([
            'sucursal_id'           => 'required|exists:sucursales,id',
            'cliente_id'            => 'nullable|exists:clientes,id',
            'items'                 => 'required|array|min:1',
            'items.*.producto_id'   => 'required|exists:productos,id',
            'items.*.cantidad'      => 'required|integer|min:1',
            'items.*.precio_unitario'=> 'required|numeric|min:0',
            'items.*.impuesto'      => 'required|numeric|min:0',
            'pagos'                 => 'required|array|min:1',
            'pagos.*.metodo'        => 'required|in:efectivo,tarjeta,transferencia,yappy,credito',
            'pagos.*.monto'         => 'required|numeric|min:0.01',
            'fecha_vencimiento'     => 'nullable|date',
            'forma_pago_dian'       => 'nullable|string',
            'metodo_pago_dian_id'   => 'nullable|integer',
        ]);

        // Validate referencía required for electronic payments
        // AND validate credit requires a real client
        $hasCredit = false;
        $totalCredito = 0;
        foreach ($request->pagos as $pago) {
            if (in_array($pago['metodo'], ['tarjeta', 'transferencia', 'yappy'])) {
                if (empty($pago['referencia'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La referencia es obligatoria para pagos electrónicos (' . $pago['metodo'] . ').'
                    ], 422);
                }
            }
            if ($pago['metodo'] === 'credito') {
                $hasCredit = true;
                $totalCredito += $pago['monto'];
            }
        }

        if ($hasCredit) {
            if (!$request->cliente_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para ventas a CRÉDITO debe seleccionar un cliente registrado (No Consumidor Final).'
                ], 422);
            }

            $cliente = Cliente::find($request->cliente_id);
            if ($cliente) {
                $deuda = $cliente->cuentasCobrar()->whereIn('estado', ['pendiente', 'parcial', 'vencido'])->sum('saldo_pendiente');
                $creditoDisponible = max(0, $cliente->limite_credito - $deuda);
                
                if (round($totalCredito, 2) > round($creditoDisponible, 2)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El monto a crédito ($' . number_format($totalCredito, 2) . ') supera el crédito disponible ($' . number_format($creditoDisponible, 2) . ').'
                    ], 422);
                }
            }
        }

        try {
            DB::beginTransaction();

            // Get open caja
            $caja = Caja::where('estado', 'abierta')->latest()->first();

            if (!$caja) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => '¡Error! No se puede registrar la venta. Debe abrir un turno de caja primero.'
                ], 422);
            }

            // Calculate totals
            $subtotal = 0;
            $itbms    = 0;

            foreach ($request->items as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);

                // Stock validation
                if ($producto->stock < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para '{$producto->nombre}'. Disponible: {$producto->stock}"
                    ], 422);
                }

                $lineaSub   = round($item['precio_unitario'] * $item['cantidad'], 2);
                $lineaItbms = round($lineaSub * ($item['impuesto'] / 100), 2);
                $subtotal  += $lineaSub;
                $itbms     += $lineaItbms;
            }

            $total = round($subtotal + $itbms, 2);

            // Validate payment total
            $totalPagado = collect($request->pagos)->sum('monto');
            if (round($totalPagado, 2) < $total) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "El monto pagado ({$totalPagado}) es menor al total ({$total})."
                ], 422);
            }

            // Create Venta
            $venta = Venta::create([
                'numero'       => Venta::generateNumero(),
                'caja_id'      => $caja?->id,
                'sucursal_id'  => $request->sucursal_id,
                'cliente_id'   => $request->cliente_id,
                'user_id'      => auth()->id() ?? 1,
                'subtotal'     => $subtotal,
                'itbms'        => $itbms,
                'total'        => $total,
                'estado'       => 'completada',
                'fecha'        => now()->toDateString(),
                'forma_pago_dian'=> $request->forma_pago_dian,
                'metodo_pago_dian_id'=> $request->metodo_pago_dian_id,
            ]);

            // Create Detalle + Descuento stock + Kardex
            foreach ($request->items as $item) {
                $producto   = Producto::lockForUpdate()->findOrFail($item['producto_id']);
                $lineaSub   = round($item['precio_unitario'] * $item['cantidad'], 2);
                $lineaItbms = round($lineaSub * ($item['impuesto'] / 100), 2);

                DetalleVenta::create([
                    'venta_id'       => $venta->id,
                    'producto_id'    => $item['producto_id'],
                    'cantidad'       => $item['cantidad'],
                    'precio_unitario'=> $item['precio_unitario'],
                    'impuesto'       => $item['impuesto'],
                    'subtotal'       => $lineaSub,
                    'total'          => round($lineaSub + $lineaItbms, 2),
                ]);

                // Decrement stock
                $stockAnterior = $producto->stock;
                $producto->decrement('stock', $item['cantidad']);
                $stockNuevo = $producto->fresh()->stock;

                // Kardex movement
                MovimientoInventario::create([
                    'producto_id'    => $item['producto_id'],
                    'tipo'           => 'salida',
                    'motivo'         => 'venta',
                    'cantidad'       => $item['cantidad'],
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo'    => $stockNuevo,
                    'numero_factura' => $venta->numero,
                    'observaciones'  => "Venta #{$venta->numero}",
                    'usuario_id'     => auth()->id() ?? 1,
                    'fecha'          => now()->toDateString(),
                ]);
            }

            // Create Pagos
            $totalCredito = 0;
            foreach ($request->pagos as $pago) {
                PagoVenta::create([
                    'venta_id'    => $venta->id,
                    'metodo'      => $pago['metodo'],
                    'monto'       => $pago['monto'],
                    'referencia'  => $pago['referencia'] ?? null,
                    'tipo_tarjeta'=> $pago['tipo_tarjeta'] ?? null,
                    'banco'       => $pago['banco'] ?? null,
                    'observaciones'=> $pago['observaciones'] ?? null,
                ]);

                if ($pago['metodo'] === 'credito') {
                    $totalCredito += $pago['monto'];
                }
            }

            // Create Cuenta por Cobrar if there's credit
            if ($totalCredito > 0) {
                \App\Models\CuentaPorCobrar::create([
                    'venta_id'          => $venta->id,
                    'cliente_id'        => $venta->cliente_id,
                    'sucursal_id'       => $venta->sucursal_id,
                    'total'             => $totalCredito,
                    'total_pagado'      => 0,
                    'saldo_pendiente'   => $totalCredito,
                    'fecha_vencimiento' => $request->fecha_vencimiento ?? now()->addDays(30),
                    'estado'            => 'pendiente',
                ]);
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => "Venta {$venta->numero} registrada correctamente.",
                'venta_id' => $venta->id,
                'numero'   => $venta->numero,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Historial de ventas.
     */
    public function historial(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario', 'sucursal', 'pagos'])
            ->latest('fecha')
            ->orderBy('id', 'desc');

        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where('numero', 'like', "%{$q}%")
                  ->orWhereHas('cliente', function ($c) use ($q) {
                      $c->where('nombre_completo', 'like', "%{$q}%")
                        ->orWhere('documento_principal', 'like', "%{$q}%");
                  });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $ventas = $query->paginate(20)->withQueryString();

        // --- DASHBOARD KPI ---
        $today = now()->toDateString();
        
        $ventasHoy = Venta::whereDate('fecha', $today)->where('estado', 'completada');
        $ventasTotalHoy = (clone $ventasHoy)->sum('total');
        $ventasCountHoy = (clone $ventasHoy)->count();

        $pagosHoyEfectivo = PagoVenta::whereHas('venta', function($q) use ($today) {
            $q->whereDate('fecha', $today)->where('estado', 'completada');
        })->where('metodo', 'efectivo')->sum('monto');

        $pagosHoyTarjeta = PagoVenta::whereHas('venta', function($q) use ($today) {
            $q->whereDate('fecha', $today)->where('estado', 'completada');
        })->where('metodo', 'tarjeta')->sum('monto');

        $devolucionesCountHoy = \App\Models\Devolucion::whereDate('fecha', $today)->count();

        // CHARTS DATA
        // 1. Line Chart: Últimos 7 días
        $start7Days = now()->subDays(6)->toDateString();
        $ventas7DiasRaw = Venta::where('estado', 'completada')
            ->whereDate('fecha', '>=', $start7Days)
            ->groupBy('fecha')
            ->selectRaw('fecha, SUM(total) as total')
            ->pluck('total', 'fecha')
            ->toArray();
        
        $chartLineLabels = [];
        $chartLineSeries = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartLineLabels[] = now()->subDays($i)->format('d M');
            $chartLineSeries[] = $ventas7DiasRaw[$date] ?? 0;
        }

        // 2. Bar/Donut Chart: Métodos de Pago del mes actual
        $pagosMesRaw = PagoVenta::whereHas('venta', function($q) {
            $q->where('estado', 'completada')->whereMonth('fecha', now()->month)->whereYear('fecha', now()->year);
        })->groupBy('metodo')
          ->selectRaw('metodo, SUM(monto) as total')
          ->pluck('total', 'metodo')
          ->toArray();
          
        $chartPieLabels = array_map('ucfirst', array_keys($pagosMesRaw));
        // Apexcharts donut needs strict numbers, not numeric strings from DB sum()
        $chartPieSeries = array_map('floatval', array_values($pagosMesRaw));
        
        if (empty($chartPieSeries)) {
            $chartPieLabels = ['Sin Datos'];
            $chartPieSeries = [1];
        }

        return view('pages.caja.historial', compact(
            'ventas', 'ventasTotalHoy', 'ventasCountHoy', 'pagosHoyEfectivo', 'pagosHoyTarjeta', 'devolucionesCountHoy',
            'chartLineLabels', 'chartLineSeries', 'chartPieLabels', 'chartPieSeries'
        ));
    }

    /**
     * Detalle de una venta.
     */
    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'sucursal', 'pagos', 'detalles.producto']);
        return view('pages.caja.detalle', compact('venta'));
    }

    /**
     * Anular venta (no eliminar).
     */
    public function anular(Request $request, Venta $venta)
    {
        $request->validate([
            'motivo_anulacion' => 'required|string|min:5|max:300',
        ]);

        if ($venta->estado === 'anulada') {
            return response()->json(['success' => false, 'message' => 'La venta ya fue anulada.'], 422);
        }

        try {
            DB::beginTransaction();

            // Restore stock for each item
            foreach ($venta->detalles as $detalle) {
                $producto      = Producto::lockForUpdate()->findOrFail($detalle->producto_id);
                $stockAnterior = $producto->stock;
                $producto->increment('stock', $detalle->cantidad);
                $stockNuevo = $producto->fresh()->stock;

                // Kardex reversal
                MovimientoInventario::create([
                    'producto_id'    => $detalle->producto_id,
                    'tipo'           => 'entrada',
                    'motivo'         => 'devolucion',
                    'cantidad'       => $detalle->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo'    => $stockNuevo,
                    'numero_factura' => $venta->numero,
                    'observaciones'  => "Anulación venta #{$venta->numero}",
                    'usuario_id'     => auth()->id() ?? 1,
                    'fecha'          => now()->toDateString(),
                ]);
            }

            $venta->update([
                'estado'           => 'anulada',
                'motivo_anulacion' => $request->motivo_anulacion,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => "Venta {$venta->numero} anulada correctamente."]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generar PDF de factura.
     */
    public function pdf(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'sucursal', 'pagos', 'detalles.producto', 'facturaElectronica']);
        $empresa = \App\Models\Empresa::first();

        return view('pages.caja.pdf.factura', compact('venta', 'empresa'));
    }

    /**
     * Generar Ticket 80mm.
     */
    public function ticket(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'sucursal', 'pagos', 'detalles.producto', 'facturaElectronica']);
        $empresa = \App\Models\Empresa::first();

        return view('pages.caja.pdf.ticket', compact('venta', 'empresa'));
    }
}
