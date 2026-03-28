<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Models\Categoria;
use App\Exports\CategoriasExport;
use App\Imports\CategoriasImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::with('parent')->orderBy('orden_visualizacion')->get();
        // Categorias that can act as parents (we can restrict to those without parents themselves if we want, but generally all are fine)
        $padres = Categoria::where('estado', true)->orderBy('nombre')->get();
        
        return view('pages.inventario.categorias', compact('categorias', 'padres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'              => 'required|string|max:150',
            'descripcion'         => 'nullable|string|max:500',
            'parent_id'           => 'nullable|exists:categorias,id',
            'impuesto'            => 'required|in:0,7,10,15',
            'unidad_medida'       => 'nullable|string|max:50',
            'ubicacion'           => 'nullable|string|max:100',
            'atributos_tecnicos'  => 'nullable|string|max:255',
            'detalle'             => 'nullable|string|max:500',
            'imagen'              => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'orden_visualizacion' => 'nullable|integer',
            'estado'              => 'nullable|boolean',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('categorias', 'public');
        }

        // Determinar estado: si viene en el request (ej: checkbox marcado, hidden input, AJAX), es true, si no, false.
        // Ojo: en creación por defecto queremos que sea activo a menos que se indique lo contrario. 
        // Si no se manda el campo pero es un request JSON, a veces puede omitirse... pero en form normal un checkbox no marcado no se manda.
        // Dado el form 'Nueva Categoría', si se desmarca, no se manda. Así que si no viene, es falso.
        $validated['estado'] = $request->has('estado') ? filter_var($request->estado, FILTER_VALIDATE_BOOLEAN) : false;
        
        $validated['orden_visualizacion'] = $validated['orden_visualizacion'] ?? 0;

        $categoria = Categoria::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'categoria' => $categoria
            ]);
        }

        return redirect()->route('inventario.categorias')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Creada!',
                'message' => 'La categoría fue registrada correctamente.',
            ]);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre'              => 'required|string|max:150',
            'descripcion'         => 'nullable|string|max:500',
            // Prevenir que una categoría sea padre de sí misma
            'parent_id'           => 'nullable|exists:categorias,id|not_in:' . $categoria->id,
            'impuesto'            => 'required|in:0,7,10,15',
            'unidad_medida'       => 'nullable|string|max:50',
            'ubicacion'           => 'nullable|string|max:100',
            'atributos_tecnicos'  => 'nullable|string|max:255',
            'detalle'             => 'nullable|string|max:500',
            'imagen'              => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'orden_visualizacion' => 'nullable|integer',
            'estado'              => 'nullable|boolean',
        ]);

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            if ($categoria->imagen && Storage::disk('public')->exists($categoria->imagen)) {
                Storage::disk('public')->delete($categoria->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('categorias', 'public');
        }

        // Para checkboxes (edit form), si 'estado' no está presente significa que se desmarcó.
        $validated['estado'] = $request->has('estado');
        $validated['orden_visualizacion'] = $validated['orden_visualizacion'] ?? 0;

        $categoria->update($validated);

        return redirect()->route('inventario.categorias')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Actualizada!',
                'message' => 'La categoría fue actualizada correctamente.',
            ]);
    }

    public function destroy(Categoria $categoria)
    {
        // Validar si tiene subcategorías antes de eliminar
        if ($categoria->children()->exists()) {
            return redirect()->route('inventario.categorias')
                ->with('sweet_alert', [
                    'type'    => 'error',
                    'title'   => 'Error',
                    'message' => 'No se puede eliminar la categoría porque tiene subcategorías asociadas.',
                ]);
        }

        if ($categoria->imagen && Storage::disk('public')->exists($categoria->imagen)) {
            Storage::disk('public')->delete($categoria->imagen);
        }

        $categoria->delete();

        return redirect()->route('inventario.categorias')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Eliminada!',
                'message' => 'La categoría fue eliminada correctamente.',
            ]);
    }

    public function exportar($formato)
    {
        if ($formato === 'pdf') {
            $categorias = Categoria::with('parent')->orderBy('nombre')->get();
            $pdf = Pdf::loadView('exports.categorias-pdf', compact('categorias'));
            return $pdf->download('categorias_' . now()->format('Ymd_His') . '.pdf');
        }

        return Excel::download(new CategoriasExport, 'categorias_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|extensions:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new CategoriasImport, $request->file('archivo'));

            return redirect()->route('inventario.categorias')
                ->with('sweet_alert', [
                    'type'    => 'success',
                    'title'   => '¡Importación exitosa!',
                    'message' => 'Las categorías han sido cargadas correctamente.',
                ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsg = "Errores en filas: ";
            foreach ($failures as $failure) {
                $errorMsg .= "Fila " . $failure->row() . " (" . implode(', ', $failure->errors()) . "); ";
            }
            return redirect()->route('inventario.categorias')
                ->with('sweet_alert', [
                    'type'    => 'error',
                    'title'   => 'Error de validación en importación',
                    'message' => $errorMsg,
                ]);
        } catch (\Exception $e) {
            return redirect()->route('inventario.categorias')
                ->with('sweet_alert', [
                    'type'    => 'error',
                    'title'   => 'Error en importación',
                    'message' => 'Ocurrió un error: ' . $e->getMessage(),
                ]);
        }
    }

    public function plantilla()
    {
        $headers = [
            'categoria_principal',
            'subcategoria',
            'descripcion',
            'itbms_percent',
            'unidad_medida',
            'ubicacion',
            'detalle',
            'estado'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers, ';');
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=plantilla_categorias.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:categorias,id',
        ]);

        try {
            Categoria::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' categorías eliminadas correctamente.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden eliminar las categorías seleccionadas porque están en uso (tienen subcategorías o productos asociados).'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar eliminar los registros.'
            ], 500);
        }
    }
}
