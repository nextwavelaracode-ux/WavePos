<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Gasto;
use App\Models\Sucursal;
use App\Models\Caja;
use App\Models\User;
use App\Models\Producto;
use App\Models\CuentaPorCobrar;
use App\Models\MovimientoInventario;
use App\Models\FinanzasRecordatorio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanzasController extends Controller
{
    public function index(Request $request)
    {
        // Rango de fechas por defecto: este mes
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin    = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());
        $sucursalId  = $request->input('sucursal_id', 'todas');

        // Query Base Ventas (Solo ventas completadas)
        $ventasQuery = Venta::where('estado', 'completada')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        // Query Base Compras (Solo compras completadas/registradas)
        $comprasQuery = Compra::where('estado', 'registrada')
            ->whereBetween('fecha_compra', [$fechaInicio, $fechaFin]);

        // Query Base Gastos (Solo activos)
        $gastosQuery = Gasto::where('estado', 'activo')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        if ($sucursalId !== 'todas') {
            $ventasQuery->where('sucursal_id', $sucursalId);
            $comprasQuery->where('sucursal_id', $sucursalId);
            $gastosQuery->where('sucursal_id', $sucursalId);
        }

        // Totales básicos KPI
        $totalVentas  = $ventasQuery->sum('total');
        $totalGastos  = $gastosQuery->sum('monto');
        $totalCompras = $comprasQuery->sum('total'); // no es KPI principal pero sirve de referencia
        
        $cuentasCobrar = CuentaPorCobrar::whereIn('estado', ['pendiente', 'parcial', 'vencido'])->sum('saldo_pendiente');
        $cuentasPagar  = Compra::where('tipo_compra', 'credito')->whereIn('estado_pago', ['pendiente', 'parcial', 'vencido'])->sum('saldo_pendiente');
        
        $totalProductos = Producto::count();
        $stockTotal     = Producto::sum('stock');

        // Utilidad y margen (para referencias si se necesitan en otro lado)
        $utilidadBruta  = $totalVentas - $totalCompras;
        $utilidadNeta   = $utilidadBruta - $totalGastos;
        $margenGanancia = $totalVentas > 0 ? ($utilidadNeta / $totalVentas) * 100 : 0;

        // ── Gráfica de Periodos (Evolución de Ingresos vs Egresos por día) ──
        $fechas = [];
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);
        while ($inicio->lte($fin)) {
            $fechas[] = $inicio->format('Y-m-d');
            $inicio->addDay();
        }

        $ventasDiarias = $ventasQuery->clone()
            ->selectRaw('DATE(fecha) as date, SUM(total) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date')->toArray();

        $gastosDiarios = $gastosQuery->clone()
            ->selectRaw('DATE(fecha) as date, SUM(monto) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date')->toArray();

        $comprasDiarias = $comprasQuery->clone()
            ->selectRaw('DATE(fecha_compra) as date, SUM(total) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date')->toArray();

        $serieIngresos = [];
        $serieEgresos = [];
        $categoriasGrafica = [];

        foreach ($fechas as $fechaStr) {
            $ingresoDelDia = $ventasDiarias[$fechaStr] ?? 0;
            $gastoDelDia = $gastosDiarios[$fechaStr] ?? 0;
            $compraDelDia = $comprasDiarias[$fechaStr] ?? 0;
            $egresoTotalDia = $gastoDelDia + $compraDelDia;

            $serieIngresos[] = round((float)$ingresoDelDia, 2);
            $serieEgresos[] = round((float)$egresoTotalDia, 2);
            $categoriasGrafica[] = Carbon::parse($fechaStr)->format('d M');
        }

        $graficaPeriodos = [
            'categorias' => $categoriasGrafica,
            'ingresos'   => $serieIngresos,
            'egresos'    => $serieEgresos,
        ];

        // ── Movimientos de Inventario (Últimos 10) ──
        $ultimosMovimientos = MovimientoInventario::with('producto')
            ->latest('fecha')
            ->take(10)
            ->get()
            ->map(function($m) {
                return [
                    'producto'    => $m->producto->nombre ?? 'Producto Eliminado',
                    'tipo'        => $m->tipo,
                    'motivo'      => $m->motivo_label,
                    'cantidad'    => $m->cantidad,
                    'stock_nuevo' => $m->stock_nuevo,
                    'fecha'       => Carbon::parse($m->fecha)->format('d M Y'),
                ];
            });

        // ── Cuentas Resumen (Panel derecho) ──
        $cuentasCobrarLista = CuentaPorCobrar::with('cliente')
            ->whereIn('estado', ['pendiente', 'parcial', 'vencido'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->take(10)
            ->get()
            ->map(function($c) {
                return [
                    'cliente'     => $c->cliente->nombre_completo ?? 'Desconocido',
                    'vencimiento' => $c->fecha_vencimiento ? Carbon::parse($c->fecha_vencimiento)->format('d M Y') : '—',
                    'saldo'       => $c->saldo_pendiente,
                    'estado'      => $c->estado,
                ];
            });

        $cuentasPagarLista = Compra::with('proveedor')
            ->where('tipo_compra', 'credito')
            ->whereIn('estado_pago', ['pendiente', 'parcial', 'vencido'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->take(10)
            ->get()
            ->map(function($c) {
                return [
                    'proveedor'   => $c->proveedor->empresa ?? 'Desconocido',
                    'numero'      => $c->numero_factura ?? 'S/N',
                    'vencimiento' => $c->fecha_vencimiento ? Carbon::parse($c->fecha_vencimiento)->format('d M Y') : '—',
                    'saldo'       => $c->saldo_pendiente,
                    'estado_pago' => $c->estado_pago,
                ];
            });

        // Sucursales activas para el select de filtro
        $sucursales = Sucursal::where('estado', true)->get();

        return view('pages.finanzas.index', compact(
            'fechaInicio', 'fechaFin', 'sucursalId', 'sucursales',
            'totalVentas', 'totalGastos', 'cuentasCobrar', 'cuentasPagar',
            'totalProductos', 'stockTotal', 'graficaPeriodos', 
            'ultimosMovimientos', 'cuentasCobrarLista', 'cuentasPagarLista',
            'utilidadBruta', 'utilidadNeta', 'margenGanancia', 'totalCompras'
        ));
    }

    public function reportes(Request $request)
    {
        return back()->with('sweet_alert', [
            'type' => 'info',
            'title' => 'En desarrollo',
            'message' => 'El módulo de exportación de reportes financieros estará disponible próximamente.'
        ]);
    }

    // =========================================================================
    // API ENDPOINTS PARA CALENDARIOS
    // =========================================================================

    /**
     * Calendario automático: Vencimientos (Cuentas por Pagar y por Cobrar)
     */
    public function calendarEvents(Request $request)
    {
        $start = $request->input('start');
        $end   = $request->input('end');

        // 1. Cuentas por Pagar (Compras a Crédito)
        $comprasQuery = Compra::with('proveedor')
            ->where('tipo_compra', 'credito')
            ->whereNotNull('fecha_vencimiento')
            ->whereIn('estado_pago', ['pendiente', 'parcial', 'vencido']);

        if ($start && $end) {
            $startDate = \Carbon\Carbon::parse($start)->startOfDay()->toDateTimeString();
            $endDate   = \Carbon\Carbon::parse($end)->endOfDay()->toDateTimeString();
            $comprasQuery->whereBetween('fecha_vencimiento', [$startDate, $endDate]);
        }
        
        $compras = $comprasQuery->get();

        // 2. Cuentas por Cobrar
        $cobrarQuery = CuentaPorCobrar::with('cliente')
            ->whereNotNull('fecha_vencimiento')
            ->whereIn('estado', ['pendiente', 'parcial', 'vencido']);

        if ($start && $end) {
            $startDate = \Carbon\Carbon::parse($start)->startOfDay()->toDateTimeString();
            $endDate   = \Carbon\Carbon::parse($end)->endOfDay()->toDateTimeString();
            $cobrarQuery->whereBetween('fecha_vencimiento', [$startDate, $endDate]);
        }
        
        $cobros = $cobrarQuery->get();

        // Agrupamos por fecha para mostrar un solo evento por día con un modal
        $eventosPorDia = [];

        foreach ($compras as $c) {
            $fecha = Carbon::parse($c->fecha_vencimiento)->format('Y-m-d');
            if (!isset($eventosPorDia[$fecha])) {
                $eventosPorDia[$fecha] = ['pagar' => 0, 'cobrar' => 0, 'items' => []];
            }
            $eventosPorDia[$fecha]['pagar']++;
            $eventosPorDia[$fecha]['items'][] = [
                'tipo'        => 'pagar',
                'referencia'  => 'Fac: ' . ($c->numero_factura ?? 'S/N') . ' - ' . ($c->proveedor->empresa ?? 'Proveedor'),
                'descripcion' => 'Compra ' . $c->id,
                'monto'       => (float) $c->saldo_pendiente,
                'estado_pago' => ucfirst($c->estado_pago),
            ];
        }

        foreach ($cobros as $c) {
            $fecha = Carbon::parse($c->fecha_vencimiento)->format('Y-m-d');
            if (!isset($eventosPorDia[$fecha])) {
                $eventosPorDia[$fecha] = ['pagar' => 0, 'cobrar' => 0, 'items' => []];
            }
            $eventosPorDia[$fecha]['cobrar']++;
            $eventosPorDia[$fecha]['items'][] = [
                'tipo'        => 'cobrar',
                'referencia'  => 'Venta: #' . $c->venta_id . ' - ' . ($c->cliente->nombre_completo ?? 'Cliente'),
                'descripcion' => 'Cuenta ' . $c->id,
                'monto'       => (float) $c->saldo_pendiente,
                'estado_pago' => ucfirst($c->estado),
            ];
        }

        $formatedEvents = [];
        foreach ($eventosPorDia as $fecha => $data) {
            $titulo = [];
            if ($data['pagar'] > 0) $titulo[]  = $data['pagar'] . ' Por pagar';
            if ($data['cobrar'] > 0) $titulo[] = $data['cobrar'] . ' Por cobrar';
            
            // Color based on majority or logic:
            $color = $data['pagar'] > 0 ? '#f59e0b' : '#3b82f6'; // amber if any payables, else blue

            $formatedEvents[] = [
                'id'              => 'vencimiento-' . $fecha,
                'title'           => implode(' | ', $titulo),
                'start'           => $fecha,
                'allDay'          => true,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'grupo' => $data['items']
                ]
            ];
        }

        return response()->json($formatedEvents);
    }

    /**
     * API: Listar recordatorios de base de datos
     */
    public function getRecordatorios(Request $request)
    {
        $start = $request->input('start');
        $end   = $request->input('end');

        $query = FinanzasRecordatorio::where('user_id', auth()->id());
        if ($start && $end) {
            $query->whereBetween('fecha', [$start, $end]);
        }

        $recordatorios = $query->get()->map(function($r) {
            return [
                'id'              => $r->id,
                'title'           => $r->titulo,
                'start'           => $r->fecha->format('Y-m-d'),
                'allDay'          => true,
                'backgroundColor' => $r->color ?? '#3b82f6',
                'borderColor'     => $r->color ?? '#3b82f6',
                'extendedProps'   => [
                    'descripcion' => $r->descripcion,
                ]
            ];
        });

        return response()->json($recordatorios);
    }

    /**
     * API: Crear recordatorio
     */
    public function storeRecordatorio(Request $request)
    {
        $validated = $request->validate([
            'fecha'       => 'required|date',
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color'       => 'nullable|string|max:20',
        ]);

        $validated['user_id'] = auth()->id();

        $recordatorio = FinanzasRecordatorio::create($validated);
        return response()->json(['success' => true, 'data' => $recordatorio]);
    }

    /**
     * API: Actualizar recordatorio
     */
    public function updateRecordatorio(Request $request, $id)
    {
        $recordatorio = FinanzasRecordatorio::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        
        $validated = $request->validate([
            'fecha'       => 'required|date',
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'color'       => 'nullable|string|max:20',
        ]);

        $recordatorio->update($validated);
        return response()->json(['success' => true, 'data' => $recordatorio]);
    }

    /**
     * API: Eliminar recordatorio
     */
    public function destroyRecordatorio($id)
    {
        $recordatorio = FinanzasRecordatorio::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $recordatorio->delete();
        return response()->json(['success' => true]);
    }
}
