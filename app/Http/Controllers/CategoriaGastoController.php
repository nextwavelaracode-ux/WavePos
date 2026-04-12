<?php

namespace App\Http\Controllers;

use App\Models\CategoriaGasto;
use Illuminate\Http\Request;

class CategoriaGastoController extends Controller
{
    public function index()
    {
        $categorias = CategoriaGasto::orderBy('nombre')->paginate(25);
        return view('pages.gastos.categorias', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100|unique:categorias_gasto,nombre',
            'descripcion' => 'nullable|string|max:500',
            'estado'      => 'required|in:activo,inactivo',
        ]);

        CategoriaGasto::create($request->only('nombre', 'descripcion', 'estado'));

        return redirect()->route('gastos.categorias.index')
                         ->with('sweet_alert', [
                             'type' => 'success',
                             'title' => '¡Éxito!',
                             'message' => 'Categoría creada correctamente.'
                         ]);
    }

    public function update(Request $request, CategoriaGasto $categoria)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100|unique:categorias_gasto,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string|max:500',
            'estado'      => 'required|in:activo,inactivo',
        ]);

        $categoria->update($request->only('nombre', 'descripcion', 'estado'));

        return redirect()->route('gastos.categorias.index')
                         ->with('sweet_alert', [
                             'type' => 'success',
                             'title' => '¡Éxito!',
                             'message' => 'Categoría actualizada correctamente.'
                         ]);
    }

    public function destroy(CategoriaGasto $categoria)
    {
        if ($categoria->gastos()->exists()) {
            return back()->with('sweet_alert', [
                'type' => 'error',
                'title' => '¡Error!',
                'message' => 'No se puede eliminar: tiene gastos asociados.'
            ]);
        }
        $categoria->delete();
        return redirect()->route('gastos.categorias.index')
                         ->with('sweet_alert', [
                             'type' => 'success',
                             'title' => '¡Éxito!',
                             'message' => 'Categoría eliminada correctamente.'
                         ]);
    }
}
