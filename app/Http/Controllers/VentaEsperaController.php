<?php

namespace App\Http\Controllers;

use App\Models\VentaEspera;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class VentaEsperaController extends Controller
{
    public function index()
    {
        $ventasEspera = VentaEspera::with(['sucursal', 'usuario'])
            ->latest()
            ->get();

        return view('pages.caja.espera', compact('ventasEspera'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'nullable|string|max:100',
            'sucursal_id' => 'required|exists:sucursales,id',
            'carrito'     => 'required|array|min:1',
        ]);

        VentaEspera::create([
            'nombre'      => $request->nombre ?? 'Venta en espera',
            'sucursal_id' => $request->sucursal_id,
            'user_id'     => auth()->id() ?? 1,
            'carrito'     => $request->carrito,
        ]);

        return response()->json(['success' => true, 'message' => 'Venta guardada en espera.']);
    }

    public function retomar(int $id)
    {
        $ventaEspera = VentaEspera::findOrFail($id);
        return response()->json([
            'success' => true,
            'carrito' => $ventaEspera->carrito,
            'nombre'  => $ventaEspera->nombre,
        ]);
    }

    public function destroy(int $id)
    {
        VentaEspera::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Venta en espera eliminada.']);
    }
}
