<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Gasto;
use App\Models\Sucursal;
use App\Models\Caja;
use App\Models\User;
use Carbon\Carbon;

class FinanzasController extends Controller
{
    public function index(Request $request)
    {
        // Rango de fechas por defecto: este mes
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());
        $sucursalId = $request->input('sucursal_id', 'todas');

        // Query Base Ventas (Solo ventas completadas)
        $ventasQuery = Venta::where('ventas.estado', 'completada')
            ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin]);

        // Query Base Compras (Solo compras completadas)
        $comprasQuery = Compra::where('compras.estado', 'completada')
            ->whereBetween('compras.fecha_compra', [$fechaInicio, $fechaFin]);

        // Query Base Gastos (Solo activos)
        $gastosQuery = Gasto::where('gastos.estado', 'activo')
            ->whereBetween('gastos.fecha', [$fechaInicio, $fechaFin]);

        // Filtrar por sucursal si aplica
        if ($sucursalId !== 'todas') {
            $ventasQuery->where('ventas.sucursal_id', $sucursalId);
            $comprasQuery->where('compras.sucursal_id', $sucursalId);
            $gastosQuery->where('gastos.sucursal_id', $sucursalId);
        }

        // Totales básicos
        $totalVentas = $ventasQuery->sum('total');
        $totalCompras = $comprasQuery->sum('total');
        $totalGastos = $gastosQuery->sum('monto');

        $utilidadBruta = $totalVentas - $totalCompras;
        $utilidadNeta = $utilidadBruta - $totalGastos;
        
        $margenGanancia = 0;
        if ($totalVentas > 0) {
            $margenGanancia = ($utilidadNeta / $totalVentas) * 100;
        }

        // --- DATOS PARA GRÁFICAS ---
        
        // 1. Gráfica de Periodos (Evolución de Ingresos vs Egresos por día)
        // Obtenemos un array de fechas en el rango
        $fechas = [];
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);
        while ($inicio->lte($fin)) {
            $fechas[] = $inicio->format('Y-m-d');
            $inicio->addDay();
        }

        // Agrupar ventas diarias
        $ventasDiarias = $ventasQuery->clone()
            ->selectRaw('DATE(fecha) as date, SUM(total) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date')->toArray();

        // Agrupar gastos diarios
        $gastosDiarios = $gastosQuery->clone()
            ->selectRaw('DATE(fecha) as date, SUM(monto) as sum')
            ->groupBy('date')
            ->pluck('sum', 'date')->toArray();

        // Agrupar compras diarias
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
            'ingresos' => $serieIngresos,
            'egresos' => $serieEgresos,
        ];

        // 2. Gastos por Categoría (Donut Chart)
        $gastosPorCategoria = $gastosQuery->clone()
            ->join('categorias_gasto', 'gastos.categoria_gasto_id', '=', 'categorias_gasto.id')
            ->selectRaw('categorias_gasto.nombre as categoria, SUM(gastos.monto) as total')
            ->groupBy('categorias_gasto.nombre')
            ->orderByDesc('total')
            ->get();

        // 3. Top Productos Rentables
        // Se asume que detalle_ventas existe y tiene producto_id, cantidad, precio_unitario, y precio_compra si lo almacena, 
        // o sino tomamos el precio_compra actual del producto (esto puede variar según modelo exacto, usare precio_compra del producto con join)
        $topProductos = \DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->where('ventas.estado', 'completada')
            ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin]);
        
        if ($sucursalId !== 'todas') {
            $topProductos->where('ventas.sucursal_id', $sucursalId);
        }

        $topProductos = $topProductos->selectRaw('
                productos.nombre, 
                SUM(detalle_ventas.cantidad) as cant_vendida,
                MAX(productos.precio_venta) as precio_v,
                MAX(productos.precio_compra) as precio_c,
                SUM(detalle_ventas.subtotal) as total_ingreso,
                SUM(detalle_ventas.cantidad * productos.precio_compra) as total_costo
            ')
            ->groupBy('productos.id', 'productos.nombre')
            ->get()
            ->map(function ($item) {
                $utilidad = $item->total_ingreso - $item->total_costo;
                $margen = $item->total_ingreso > 0 ? ($utilidad / $item->total_ingreso) * 100 : 0;
                return [
                    'nombre' => $item->nombre,
                    'ventas' => $item->cant_vendida,
                    'precio_compra' => round($item->precio_c, 2),
                    'precio_venta' => round($item->precio_v, 2),
                    'ganancia_unitaria' => round($item->precio_v - $item->precio_c, 2),
                    'ingreso' => round($item->total_ingreso, 2),
                    'utilidad' => round($utilidad, 2),
                    'margen' => round($margen, 2)
                ];
            })
            ->sortByDesc('utilidad')
            ->take(5)
            ->values();

        // Extraer series para grafica Top Productos
        $topProductosNombres = $topProductos->pluck('nombre')->toArray();
        $topProductosVentas = $topProductos->pluck('ventas')->toArray();
        $topProductosUtilidad = $topProductos->pluck('utilidad')->toArray();

        // 4. Rentabilidad por Sucursal
        $sucursalesKpis = Sucursal::where('estado', true)->get()->map(function($suc) use ($fechaInicio, $fechaFin) {
            $v = Venta::where('sucursal_id', $suc->id)->where('estado', 'completada')->whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('total');
            $c = Compra::where('sucursal_id', $suc->id)->where('estado', 'completada')->whereBetween('fecha_compra', [$fechaInicio, $fechaFin])->sum('total');
            $g = Gasto::where('sucursal_id', $suc->id)->where('estado', 'activo')->whereBetween('fecha', [$fechaInicio, $fechaFin])->sum('monto');
            $u = $v - $c - $g;
            return [
                'nombre' => $suc->nombre,
                'ventas' => round($v, 2),
                'costos' => round($c + $g, 2),
                'utilidad' => round($u, 2)
            ];
        })->sortByDesc('utilidad')->values();

        // Últimos Movimientos (Flujo Combinado)
        $entradas = $ventasQuery->clone()->orderByDesc('created_at')->take(10)->get()
            ->map(fn($v) => ['tipo' => 'ingreso', 'categoria' => 'Venta', 'referencia' => $v->numero_comprobante, 'monto' => $v->total, 'fecha' => $v->fecha]);
        $salidasGastos = $gastosQuery->clone()->with('categoria')->orderByDesc('created_at')->take(10)->get()
            ->map(fn($g) => ['tipo' => 'egreso', 'categoria' => 'Gasto / ' . ($g->categoria->nombre ?? ''), 'referencia' => $g->descripcion, 'monto' => $g->monto, 'fecha' => $g->fecha]);
        $salidasCompras = $comprasQuery->clone()->orderByDesc('created_at')->take(10)->get()
            ->map(fn($c) => ['tipo' => 'egreso', 'categoria' => 'Compra', 'referencia' => $c->comprobante, 'monto' => $c->total, 'fecha' => $c->fecha_compra]);

        
        $flujoReciente = collect([])->merge($entradas)->merge($salidasGastos)->merge($salidasCompras)
            ->sortByDesc('fecha')->take(10)->values();

        // 5. Top Vendedores (usuarios que más han vendido)
        $topSellersQuery = Venta::where('estado', 'completada')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        if ($sucursalId !== 'todas') {
            $topSellersQuery->where('sucursal_id', $sucursalId);
        }
        $topSellers = $topSellersQuery
            ->selectRaw('user_id, SUM(total) as total_ventas, COUNT(*) as num_transacciones')
            ->groupBy('user_id')
            ->orderByDesc('total_ventas')
            ->take(5)
            ->get()
            ->map(function($item) {
                $user = User::find($item->user_id);
                return [
                    'nombre' => $user ? $user->name : 'Usuario #' . $item->user_id,
                    'total_ventas' => round($item->total_ventas, 2),
                    'transacciones' => $item->num_transacciones,
                ];
            });

        // 6. Historial de Turnos (cajas)
        $turnosQuery = Caja::whereBetween('fecha_apertura', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        if ($sucursalId !== 'todas') {
            $turnosQuery->where('sucursal_id', $sucursalId);
        }
        $historialTurnos = $turnosQuery->with(['usuario', 'sucursal'])
            ->orderByDesc('fecha_apertura')
            ->take(10)
            ->get();

        // Sucursales activas para el select de filtro
        $sucursales = Sucursal::where('estado', true)->get();

        return view('pages.finanzas.index', compact(
            'fechaInicio',
            'fechaFin',
            'sucursalId',
            'totalVentas',
            'totalCompras',
            'totalGastos',
            'utilidadBruta',
            'utilidadNeta',
            'margenGanancia',
            'sucursales',
            'graficaPeriodos',
            'topProductos',
            'topProductosNombres',
            'topProductosVentas',
            'topProductosUtilidad',
            'gastosPorCategoria',
            'sucursalesKpis',
            'flujoReciente',
            'topSellers',
            'historialTurnos'
        ));
    }

    public function reportes(Request $request)
    {
        // Futura implementación de exportación PDF / Excel
        return back()->with('sweet_alert', [
            'type' => 'info',
            'title' => 'En desarrollo',
            'message' => 'El módulo de exportación de reportes financieros estará disponible próximamente.'
        ]);
    }
}
