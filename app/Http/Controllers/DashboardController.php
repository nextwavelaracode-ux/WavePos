<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy       = Carbon::today();
        $anoActual = $hoy->year;
        $mesActual = $hoy->month;
        // ── Datos Cacheados para Alto Rendimiento (10 min) ──
        $data = \Illuminate\Support\Facades\Cache::remember('dashboard_stats_v1', 600, function () use ($hoy, $anoActual, $mesActual) {
            
            $totalVentas   = Venta::where('estado', 'completada')->sum('total');
            $totalGastos   = Gasto::sum('monto') ?? 0;
            $totalCostos   = $totalGastos;
            $totalGanancia = max(0, $totalVentas - $totalCostos);

            $ventasHoy = Venta::where('estado', 'completada')
                            ->whereDate('created_at', $hoy)
                            ->sum('total');

            $totalClientes  = Cliente::count();
            $totalProductos = Producto::count();
            $totalOrdenes   = Venta::where('estado', 'completada')->count();
            
            $clientesNuevos = Cliente::whereMonth('created_at', $mesActual)
                                    ->whereYear('created_at', $anoActual)
                                    ->count();

            // ── Gráfica de ventas mensuales ──
            $ventasMensualesRaw = Venta::where('estado', 'completada')
                ->whereYear('created_at', $anoActual)
                ->select(DB::raw('MONTH(created_at) as mes'), DB::raw('ROUND(SUM(total)/1000, 1) as total'))
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->pluck('total', 'mes')
                ->toArray();

            $ventasMensualesArr = [];
            for ($i = 1; $i <= 12; $i++) {
                $ventasMensualesArr[] = (float) ($ventasMensualesRaw[$i] ?? 0);
            }

            // ── Ventas por categoría (donut) ──
            $ventasCategoriaRaw = DB::table('detalle_ventas as dv')
                ->join('ventas',    'ventas.id',    '=', 'dv.venta_id')
                ->join('productos', 'productos.id', '=', 'dv.producto_id')
                ->join('categorias','categorias.id','=', 'productos.categoria_id')
                ->where('ventas.estado', 'completada')
                ->select('categorias.nombre', DB::raw('ROUND(SUM(dv.subtotal)/1000, 1) as valor'))
                ->groupBy('categorias.id', 'categorias.nombre')
                ->orderByDesc('valor')
                ->limit(4)
                ->get();

            $colores = ['#3b82f6', '#22d3ee', '#818cf8', '#34d399'];
            $ventasCategoria = $ventasCategoriaRaw->map(function ($row, $i) use ($colores) {
                return [
                    'nombre' => $row->nombre,
                    'valor'  => (float) $row->valor,
                    'color'  => $colores[$i % count($colores)],
                ];
            })->values()->toArray();

            // ── Ventas diarias (barras – últimos 11 días) ──
            $ventasDiariasRaw = Venta::where('estado', 'completada')
                ->where('created_at', '>=', now()->subDays(10)->startOfDay())
                ->select(DB::raw('DATE(created_at) as dia'), DB::raw('ROUND(SUM(total)/1000, 1) as total'))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'))
                ->pluck('total', 'dia')
                ->toArray();

            $ventasDiarias = [];
            for ($i = 10; $i >= 0; $i--) {
                $date            = now()->subDays($i)->format('Y-m-d');
                $ventasDiarias[] = (float) ($ventasDiariasRaw[$date] ?? 0);
            }

            // ── Top clientes por compras ──
            $topClientesRaw = DB::table('clientes')
                ->leftJoin('ventas', function ($join) {
                    $join->on('ventas.cliente_id', '=', 'clientes.id')
                        ->where('ventas.estado', 'completada');
                })
                ->select(
                    'clientes.tipo_cliente', 'clientes.nombre', 'clientes.apellido',
                    'clientes.empresa', 'clientes.ruc', 'clientes.dv', 'clientes.cedula',
                    DB::raw('COALESCE(SUM(ventas.total), 0) as total_compras'),
                    DB::raw('MAX(ventas.created_at) as ultima_venta_at')
                )
                ->groupBy('clientes.id', 'clientes.tipo_cliente', 'clientes.nombre', 'clientes.apellido', 'clientes.empresa', 'clientes.ruc', 'clientes.dv', 'clientes.cedula')
                ->orderByDesc('total_compras')
                ->limit(5)
                ->get();

            $topClientes = $topClientesRaw->map(function ($c) {
                $nombre = match($c->tipo_cliente ?? 'natural') {
                    'juridico', 'b2b' => $c->empresa ?: trim($c->nombre . ' ' . $c->apellido),
                    default           => trim($c->nombre . ' ' . ($c->apellido ?? '')),
                };
                $doc = match($c->tipo_cliente ?? 'natural') {
                    'juridico', 'b2b' => $c->ruc ? 'RUC: ' . $c->ruc . ($c->dv ? '-' . $c->dv : '') : '—',
                    default           => $c->cedula ? 'Céd: ' . $c->cedula : '—',
                };
                return [
                    'nombre'       => $nombre ?: '—',
                    'documento'    => $doc,
                    'total'        => (float) $c->total_compras,
                    'ultima_venta' => $c->ultima_venta_at ? Carbon::parse($c->ultima_venta_at)->diffForHumans() : '—',
                ];
            })->toArray();

            // ── Activity timeline (últimas 5 ventas) ──
            $ultimasVentas = Venta::where('estado', 'completada')->latest()->limit(5)->get();
            $actividades = $ultimasVentas->map(function ($v) {
                return [
                    'titulo' => '$' . number_format((float) $v->total) . ' – ' . ($v->numero ?? 'Venta'),
                    'tiempo' => Carbon::parse($v->created_at)->diffForHumans(),
                    'color'  => 'bg-blue-500',
                ];
            })->toArray();

            if (empty($actividades)) {
                $actividades = [['titulo' => 'Sin ventas registradas aún', 'tiempo' => 'Ahora', 'color' => 'bg-gray-300']];
            }

            return compact(
                'totalVentas', 'totalGastos', 'totalCostos', 'totalGanancia', 'ventasHoy',
                'totalClientes', 'totalProductos', 'totalOrdenes', 'clientesNuevos',
                'ventasMensualesArr', 'ventasCategoria', 'ventasDiarias', 'topClientes', 'actividades'
            );
        });
        // Desestructurar la cache y suplir los alias
        extract($data);
        
        $ventasLifetime = $totalVentas;
        $totalIngresos  = $totalVentas;
        $ingresosNetos  = $totalGanancia;
        $totalCostos    = $totalGastos;

        return view('pages.dashboard.ecommerce', [
            'title'          => 'Dashboard',
            // KPI
            'totalVentas'    => $totalVentas,
            'totalGanancia'  => $totalGanancia,
            'totalCostos'    => $totalCostos,
            'totalIngresos'  => $totalIngresos,
            'ingresosNetos'  => $ingresosNetos,
            'ventasHoy'      => $ventasHoy,
            'totalClientes'  => $totalClientes,
            // Panel derecho
            'totalProductos' => $totalProductos,
            'totalOrdenes'   => $totalOrdenes,
            'ventasLifetime' => $ventasLifetime,
            'clientesNuevos' => $clientesNuevos,
            // Gráficas
            'ventasMensuales'  => $ventasMensualesArr,
            'ventasCategoria'  => $ventasCategoria,
            'ventasDiarias'    => $ventasDiarias,
            // Tabla y timeline
            'topClientes'    => $topClientes,
            'actividades'    => $actividades,
        ]);
    }
}
