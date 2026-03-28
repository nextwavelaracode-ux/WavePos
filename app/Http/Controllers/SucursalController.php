<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::latest()->get();
        return view('pages.configuracion.sucursales', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:200',
            'direccion' => 'nullable|string|max:300',
            'telefono'  => 'nullable|string|max:20',
            'ciudad'    => 'nullable|string|max:100',
            'pais'      => 'nullable|string|max:100',
            'estado'    => 'nullable|boolean',
        ]);

        $validated['estado'] = $request->boolean('estado', true);

        Sucursal::create($validated);

        return redirect()->route('configuracion.sucursales')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Creada!',
                'message' => 'La sucursal fue registrada correctamente.',
            ]);
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:200',
            'direccion' => 'nullable|string|max:300',
            'telefono'  => 'nullable|string|max:20',
            'ciudad'    => 'nullable|string|max:100',
            'pais'      => 'nullable|string|max:100',
            'estado'    => 'nullable|boolean',
        ]);

        $validated['estado'] = $request->boolean('estado', true);

        $sucursal->update($validated);

        return redirect()->route('configuracion.sucursales')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Actualizada!',
                'message' => 'La sucursal fue actualizada correctamente.',
            ]);
    }

    public function destroy(Sucursal $sucursal)
    {
        $sucursal->delete();

        return redirect()->route('configuracion.sucursales')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Eliminada!',
                'message' => 'La sucursal fue eliminada correctamente.',
            ]);
    }
}
