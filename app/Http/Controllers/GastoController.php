<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CategoriaGasto;
use App\Models\Gasto;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\GastosExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class GastoController extends Controller
{
    // Existing index, store, show, update, destroy...

    public function exportar(Request $request)
    {
        $formato = $request->formato ?? 'excel';
        $filters = $request->only(['fecha_desde', 'fecha_hasta', 'categoria_id', 'sucursal_id', 'metodo']);

        if ($formato === 'pdf') {
            $query = Gasto::with(['categoria', 'sucursal'])->orderByDesc('fecha');
            if ($request->filled('fecha_desde')) $query->whereDate('fecha', '>=', $request->fecha_desde);
            if ($request->filled('fecha_hasta')) $query->whereDate('fecha', '<=', $request->fecha_hasta);
            if ($request->filled('categoria_id')) $query->where('categoria_gasto_id', $request->categoria_id);
            if ($request->filled('sucursal_id')) $query->where('sucursal_id', $request->sucursal_id);
            if ($request->filled('metodo')) $query->where('metodo_pago', $request->metodo);

            $gastos = $query->get();
            $fecha_desde = $request->fecha_desde;
            $fecha_hasta = $request->fecha_hasta;

            $pdf = Pdf::loadView('exports.gastos-pdf', compact('gastos', 'fecha_desde', 'fecha_hasta'));
            return $pdf->download('gastos_' . now()->format('Ymd_His') . '.pdf');
        }

        return Excel::download(new GastosExport($filters), 'gastos_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Listado principal con dashboard KPIs + filtros.
     */
    public function index(Request $request)
    {
        $query = Gasto::with(['categoria', 'sucursal', 'usuario'])
            ->where('estado', 'activo');

        // Filtros
        if ($request->filled('categoria_id')) {
            $query->where('categoria_gasto_id', $request->categoria_id);
        }
        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }
        if ($request->filled('metodo')) {
            $query->where('metodo_pago', $request->metodo);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $gastos = $query->latest('fecha')->latest('id')->paginate(25)->withQueryString();

        // ── Dashboard KPIs ──────────────────────────────────────────────
        $hoy = now()->toDateString();
        $inicioMes = now()->startOfMonth()->toDateString();

        $totalHoy = Gasto::where('estado', 'activo')
            ->whereDate('fecha', $hoy)
            ->sum('monto');

        $totalMes = Gasto::where('estado', 'activo')
            ->whereDate('fecha', '>=', $inicioMes)
            ->sum('monto');

        $porCategoria = Gasto::with('categoria')
            ->where('estado', 'activo')
            ->whereDate('fecha', '>=', $inicioMes)
            ->get()
            ->groupBy('categoria_gasto_id')
            ->map(fn($g) => [
                'nombre' => $g->first()->categoria->nombre ?? 'Sin categoría',
                'total'  => $g->sum('monto'),
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $porSucursal = Gasto::with('sucursal')
            ->where('estado', 'activo')
            ->whereDate('fecha', '>=', $inicioMes)
            ->get()
            ->groupBy('sucursal_id')
            ->map(fn($g) => [
                'nombre' => $g->first()->sucursal->nombre ?? 'Sin sucursal',
                'total'  => $g->sum('monto'),
            ])
            ->sortByDesc('total')
            ->values();

        // ── Datos para los selects del formulario ────────────────────────
        $categorias  = CategoriaGasto::activas()->orderBy('nombre')->get();
        $sucursales  = Sucursal::orderBy('nombre')->get();

        return view('pages.gastos.index', compact(
            'gastos', 'categorias', 'sucursales',
            'totalHoy', 'totalMes', 'porCategoria', 'porSucursal'
        ));
    }

    /**
     * Guardar nuevo gasto y descontar de caja si es efectivo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'categoria_gasto_id' => 'required|exists:categorias_gasto,id',
            'sucursal_id'        => 'required|exists:sucursales,id',
            'monto'              => 'required|numeric|min:0.01',
            'metodo_pago'        => 'required|in:efectivo,transferencia,tarjeta,cheque,yappy',
            'referencia'         => 'nullable|string|max:100',
            'fecha'              => 'required|date',
            'descripcion'        => 'nullable|string|max:500',
            'es_recurrente'      => 'nullable|boolean',
            'frecuencia'         => 'nullable|in:diario,semanal,quincenal,mensual,anual',
            'fecha_programada'   => 'nullable|date',
            'comprobante'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Referencia obligatoria para métodos electrónicos
        if (in_array($request->metodo_pago, ['transferencia', 'tarjeta', 'yappy']) && empty($request->referencia)) {
            return back()->withErrors(['referencia' => 'La referencia es obligatoria para pagos electrónicos.'])->withInput();
        }

        $comprobantePath = null;
        if ($request->hasFile('comprobante')) {
            $comprobantePath = $request->file('comprobante')->store('comprobantes/gastos', 'public');
        }

        $gasto = Gasto::create([
            'categoria_gasto_id' => $request->categoria_gasto_id,
            'sucursal_id'        => $request->sucursal_id,
            'user_id'            => auth()->id() ?? 1,
            'monto'              => $request->monto,
            'metodo_pago'        => $request->metodo_pago,
            'referencia'         => $request->referencia,
            'fecha'              => $request->fecha,
            'descripcion'        => $request->descripcion,
            'comprobante'        => $comprobantePath,
            'es_recurrente'      => $request->boolean('es_recurrente'),
            'frecuencia'         => $request->frecuencia,
            'fecha_programada'   => $request->fecha_programada,
            'estado'             => 'activo',
        ]);

        // ── Integración con Caja: descontar efectivo si aplica ────────────
        if ($request->metodo_pago === 'efectivo') {
            $cajaAbierta = Caja::where('estado', 'abierta')->latest()->first();
            if ($cajaAbierta) {
                $cajaAbierta->decrement('total_efectivo', $request->monto);
            }
        }

        return redirect()->route('gastos.index')
                         ->with('sweet_alert', [
                             'type' => 'success',
                             'title' => '¡Éxito!',
                             'message' => 'Gasto registrado correctamente.'
                         ]);
    }

    /**
     * Ver detalle de un gasto.
     */
    public function show(Gasto $gasto)
    {
        $gasto->load(['categoria', 'sucursal', 'usuario']);
        return view('pages.gastos.show', compact('gasto'));
    }

    /**
     * Actualizar un gasto existente.
     */
    public function update(Request $request, Gasto $gasto)
    {
        $request->validate([
            'categoria_gasto_id' => 'required|exists:categorias_gasto,id',
            'sucursal_id'        => 'required|exists:sucursales,id',
            'monto'              => 'required|numeric|min:0.01',
            'metodo_pago'        => 'required|in:efectivo,transferencia,tarjeta,cheque,yappy',
            'referencia'         => 'nullable|string|max:100',
            'fecha'              => 'required|date',
            'descripcion'        => 'nullable|string|max:500',
            'es_recurrente'      => 'nullable|boolean',
            'frecuencia'         => 'nullable|in:diario,semanal,quincenal,mensual,anual',
            'fecha_programada'   => 'nullable|date',
            'comprobante'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if (in_array($request->metodo_pago, ['transferencia', 'tarjeta', 'yappy']) && empty($request->referencia)) {
            return back()->withErrors(['referencia' => 'La referencia es obligatoria para pagos electrónicos.'])->withInput();
        }

        // Revertir efecto en caja del monto antiguo
        $montoAnterior    = (float) $gasto->monto;
        $metodoAnterior   = $gasto->metodo_pago;
        $montoNuevo       = (float) $request->monto;
        $metodoNuevo      = $request->metodo_pago;

        $comprobantePath = $gasto->comprobante;
        if ($request->hasFile('comprobante')) {
            if ($comprobantePath) {
                Storage::disk('public')->delete($comprobantePath);
            }
            $comprobantePath = $request->file('comprobante')->store('comprobantes/gastos', 'public');
        }

        $gasto->update([
            'categoria_gasto_id' => $request->categoria_gasto_id,
            'sucursal_id'        => $request->sucursal_id,
            'monto'              => $montoNuevo,
            'metodo_pago'        => $metodoNuevo,
            'referencia'         => $request->referencia,
            'fecha'              => $request->fecha,
            'descripcion'        => $request->descripcion,
            'comprobante'        => $comprobantePath,
            'es_recurrente'      => $request->boolean('es_recurrente'),
            'frecuencia'         => $request->frecuencia,
            'fecha_programada'   => $request->fecha_programada,
        ]);

        // Ajustar caja: revertir anterior y aplicar nuevo
        $cajaAbierta = Caja::where('estado', 'abierta')->latest()->first();
        if ($cajaAbierta) {
            if ($metodoAnterior === 'efectivo') {
                $cajaAbierta->increment('total_efectivo', $montoAnterior);
            }
            if ($metodoNuevo === 'efectivo') {
                $cajaAbierta->decrement('total_efectivo', $montoNuevo);
            }
        }

        return redirect()->route('gastos.index')
                         ->with('sweet_alert', [
                             'type' => 'success',
                             'title' => '¡Éxito!',
                             'message' => 'Gasto actualizado correctamente.'
                         ]);
    }

    /**
     * Soft-delete (anular) un gasto y revertir efecto en caja.
     */
    public function destroy(Gasto $gasto)
    {
        // Revertir efecto en caja si fue efectivo
        if ($gasto->metodo_pago === 'efectivo' && $gasto->estado === 'activo') {
            $cajaAbierta = Caja::where('estado', 'abierta')->latest()->first();
            if ($cajaAbierta) {
                $cajaAbierta->increment('total_efectivo', $gasto->monto);
            }
        }

        $gasto->update(['estado' => 'anulado', 'notas_anulacion' => 'Eliminado por ' . (auth()->user()->name ?? 'Sistema')]);
        $gasto->delete();

        return redirect()->route('gastos.index')
                         ->with('sweet_alert', [
                             'type' => 'success',
                             'title' => '¡Éxito!',
                             'message' => 'Gasto eliminado correctamente.'
                         ]);
    }
}
