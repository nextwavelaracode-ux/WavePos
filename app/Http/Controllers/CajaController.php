<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    /**
     * Muestra el estado de la caja actual o el formulario de apertura.
     */
    public function index()
    {
        $cajaAbierta = Caja::with(['ventas.detalles', 'ventas.pagos', 'usuario', 'pagosCuentasCobrar'])->where('estado', 'abierta')->latest()->first();
        $sucursales  = Sucursal::orderBy('nombre')->get();

        // Variables por defecto
        $totalVentas = 0;
        $totalEfectivo = 0;
        $totalTarjeta = 0;
        $totalTransferencia = 0;
        $montoEsperado = 0;
        $movimientosTurno = collect();

        if ($cajaAbierta) {
            $ventas = $cajaAbierta->ventas()->where('estado', 'completada')->get();
            $totalVentas = $ventas->sum('total');

            // Calcular subtotales por tipo
            foreach ($ventas as $venta) {
                foreach ($venta->pagos as $pago) {
                    if ($pago->metodo === 'efectivo') $totalEfectivo += $pago->monto;
                    if ($pago->metodo === 'tarjeta') $totalTarjeta += $pago->monto;
                    if (in_array($pago->metodo, ['transferencia', 'yappy'])) $totalTransferencia += $pago->monto;
                }
            }
            
            // Sumar cobros de AR (Cuentas por Cobrar)
            foreach ($cajaAbierta->pagosCuentasCobrar as $pagoAr) {
                if ($pagoAr->metodo === 'efectivo') $totalEfectivo += $pagoAr->monto;
                if ($pagoAr->metodo === 'tarjeta') $totalTarjeta += $pagoAr->monto;
                if (in_array($pagoAr->metodo, ['transferencia', 'yappy'])) $totalTransferencia += $pagoAr->monto;
            }

            $montoEsperado = (float) $cajaAbierta->monto_inicial + (float) $totalEfectivo;
            $movimientosTurno = $cajaAbierta->ventas()->with(['usuario', 'pagos'])->latest('created_at')->get();
        }

        // Historial de cajas cerradas recientes
        $historialCajas = Caja::with(['sucursal', 'usuario'])
            ->where('estado', 'cerrada')
            ->latest('fecha_cierre')
            ->take(20) // Mostramos más turnos en el historial
            ->get();

        return view('pages.caja.index', compact(
            'cajaAbierta', 'sucursales', 'historialCajas', 
            'totalVentas', 'totalEfectivo', 'totalTarjeta', 'totalTransferencia', 
            'montoEsperado', 'movimientosTurno'
        ));
    }

    /**
     * Abre una nueva caja de turno.
     */
    public function abrir(Request $request)
    {
        $request->validate([
            'sucursal_id'   => 'required|exists:sucursales,id',
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        // Verificar que no haya caja abierta
        $cajaActiva = Caja::where('estado', 'abierta')->first();
        if ($cajaActiva) {
            return back()->with('error', 'Ya existe una caja abierta. Debe cerrarla primero.');
        }

        Caja::create([
            'sucursal_id'   => $request->sucursal_id,
            'user_id'       => auth()->id() ?? 1,
            'monto_inicial' => $request->monto_inicial,
            'fecha_apertura'=> now(),
            'estado'        => 'abierta',
        ]);

        return redirect()->route('caja.pos')->with('success', 'Caja abierta correctamente. ¡Buenas ventas!');
    }

    /**
     * Cierra la caja del turno actual.
     */
    public function cerrar(Request $request)
    {
        $request->validate([
            'caja_id'           => 'required|exists:cajas,id',
            'monto_real_cierre' => 'required|numeric|min:0',
            'observaciones'     => 'nullable|string|max:500',
        ]);

        $caja = Caja::findOrFail($request->caja_id);

        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'Esta caja ya fue cerrada.');
        }

        // Calcular totales de la caja desde sus ventas
        $ventas = $caja->ventas()->where('estado', 'completada');

        $totalVentas       = $ventas->sum('total');
        $totalEfectivo     = $caja->ventas()->whereHas('pagos', fn($q) => $q->where('metodo', 'efectivo'))->join('pagos_venta', 'ventas.id', '=', 'pagos_venta.venta_id')->where('pagos_venta.metodo', 'efectivo')->where('ventas.estado', 'completada')->sum('pagos_venta.monto');
        $totalTarjeta      = $caja->ventas()->join('pagos_venta', 'ventas.id', '=', 'pagos_venta.venta_id')->where('pagos_venta.metodo', 'tarjeta')->where('ventas.estado', 'completada')->sum('pagos_venta.monto');
        $totalTransferencia= $caja->ventas()->join('pagos_venta', 'ventas.id', '=', 'pagos_venta.venta_id')->where('pagos_venta.metodo', 'transferencia')->where('ventas.estado', 'completada')->sum('pagos_venta.monto');
        $totalYappy        = $caja->ventas()->join('pagos_venta', 'ventas.id', '=', 'pagos_venta.venta_id')->where('pagos_venta.metodo', 'yappy')->where('ventas.estado', 'completada')->sum('pagos_venta.monto');
        $totalCredito      = $caja->ventas()->join('pagos_venta', 'ventas.id', '=', 'pagos_venta.venta_id')->where('pagos_venta.metodo', 'credito')->where('ventas.estado', 'completada')->sum('pagos_venta.monto');

        // Calcular cobros AR recibidos en este turno
        $totalArEfectivo = $caja->pagosCuentasCobrar()->where('metodo', 'efectivo')->sum('monto');
        $totalArOtros    = $caja->pagosCuentasCobrar()->where('metodo', '!=', 'efectivo')->sum('monto');

        $totalEfectivo += $totalArEfectivo;
        // Los otros métodos no afectan el efectivo esperado pero sí el reporte
        
        $montoEsperado = (float) $caja->monto_inicial + (float) $totalEfectivo;
        $diferencia    = (float) $request->monto_real_cierre - $montoEsperado;

        $caja->update([
            'fecha_cierre'       => now(),
            'total_ventas'       => $totalVentas,
            'total_efectivo'     => $totalEfectivo,
            'total_tarjeta'      => $totalTarjeta,
            'total_transferencia'=> $totalTransferencia,
            'total_yappy'        => $totalYappy,
            'total_credito'      => $totalCredito,
            'monto_real_cierre'  => $request->monto_real_cierre,
            'diferencia'         => $diferencia,
            'estado'             => 'cerrada',
            'observaciones'      => $request->observaciones,
        ]);

        return redirect()->route('caja.index')->with('success', 'Caja cerrada correctamente.');
    }

    /**
     * Muestra el detalle de una caja (para reporte).
     */
    public function show(Caja $caja)
    {
        $caja->load(['sucursal', 'usuario', 'ventas.pagos', 'ventas.cliente']);
        return view('pages.caja.cierre', compact('caja'));
    }
}
