<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;
use App\Exports\ProductosExport;
use App\Imports\ProductosImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'subcategoria'])->latest()->get();
        // Categorias base (sin padre) para el select principal
        $categorias = Categoria::whereNull('parent_id')->where('estado', true)->orderBy('nombre')->get();
        // Todas las categorías para subcategorías y filtros
        $todasCategorias = Categoria::where('estado', true)->orderBy('nombre')->get();

        return view('pages.inventario.productos', compact('productos', 'categorias', 'todasCategorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'          => 'required|string|max:150',
            'descripcion'     => 'nullable|string',
            'categoria_id'    => 'required|exists:categorias,id',
            'subcategoria_id' => 'nullable|exists:categorias,id',
            'sku'             => 'nullable|string|max:50',
            'codigo_barras'   => 'nullable|string|max:50',
            'precio_compra'   => 'required|numeric|min:0',
            'precio_venta'    => 'required|numeric|min:0',
            'precio_minimo'   => 'nullable|numeric|min:0',
            'margen'          => 'nullable|numeric',
            'impuesto'        => 'required|in:0,7,10,15',
            'stock'           => 'required|integer|min:0',
            'stock_minimo'    => 'nullable|integer|min:0',
            'stock_maximo'    => 'nullable|integer|min:0',
            'unidad_medida'   => 'required|string|max:50',
            'ubicacion'       => 'nullable|string|max:255',
            'pasillo'         => 'nullable|string|max:50',
            'estante'         => 'nullable|string|max:50',
            'imagen'          => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'estado'          => 'nullable',
            'tasa_impuesto'   => 'nullable|numeric|min:0',
            'is_excluded'     => 'nullable',
            'unidad_medida_dian_id' => 'nullable|integer',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $validated['estado']         = $request->boolean('estado');
        
        // Manejo de valores numéricos para evitar errores de SQL con strings vacíos
        $validated['precio_compra'] = is_numeric($request->precio_compra) ? $request->precio_compra : 0;
        $validated['precio_venta']  = is_numeric($request->precio_venta) ? $request->precio_venta : 0;
        $validated['precio_minimo'] = is_numeric($request->precio_minimo) ? $request->precio_minimo : $validated['precio_venta'];
        $validated['stock']         = is_numeric($request->stock) ? (int)$request->stock : 0;
        $validated['stock_minimo']  = is_numeric($request->stock_minimo) ? (int)$request->stock_minimo : 0;
        $validated['stock_maximo']  = is_numeric($request->stock_maximo) ? (int)$request->stock_maximo : null;
        $validated['impuesto']      = is_numeric($request->impuesto) ? $request->impuesto : 7;
        
        $validated['is_excluded']   = $request->boolean('is_excluded');
        $validated['tasa_impuesto'] = is_numeric($request->tasa_impuesto) ? $request->tasa_impuesto : 19.00;
        
        // Calcular margen si no viene
        if (!isset($request->margen) || !is_numeric($request->margen) || $request->margen == 0) {
            if ($validated['precio_compra'] > 0) {
                $validated['margen'] = (($validated['precio_venta'] - $validated['precio_compra']) / $validated['precio_compra']) * 100;
            } else {
                $validated['margen'] = 0;
            }
        } else {
            $validated['margen'] = $request->margen;
        }

        Producto::create($validated);

        return redirect()->route('inventario.productos')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Producto creado!',
                'message' => 'El producto fue registrado correctamente.',
            ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre'          => 'required|string|max:150',
            'descripcion'     => 'nullable|string',
            'categoria_id'    => 'required|exists:categorias,id',
            'subcategoria_id' => 'nullable|exists:categorias,id',
            'sku'             => 'nullable|string|max:50',
            'codigo_barras'   => 'nullable|string|max:50',
            'precio_compra'   => 'required|numeric|min:0',
            'precio_venta'    => 'required|numeric|min:0',
            'precio_minimo'   => 'nullable|numeric|min:0',
            'margen'          => 'nullable|numeric',
            'impuesto'        => 'required|in:0,7,10,15',
            'stock'           => 'required|integer|min:0',
            'stock_minimo'    => 'nullable|integer|min:0',
            'stock_maximo'    => 'nullable|integer|min:0',
            'unidad_medida'   => 'required|string|max:50',
            'ubicacion'       => 'nullable|string|max:255',
            'pasillo'         => 'nullable|string|max:50',
            'estante'         => 'nullable|string|max:50',
            'imagen'          => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'estado'          => 'nullable',
            'tasa_impuesto'   => 'nullable|numeric|min:0',
            'is_excluded'     => 'nullable',
            'unidad_medida_dian_id' => 'nullable|integer',
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $validated['estado']         = $request->boolean('estado');
        
        // Manejo de valores numéricos para evitar errores de SQL con strings vacíos
        $validated['precio_compra'] = is_numeric($request->precio_compra) ? $request->precio_compra : 0;
        $validated['precio_venta']  = is_numeric($request->precio_venta) ? $request->precio_venta : 0;
        $validated['precio_minimo'] = is_numeric($request->precio_minimo) ? $request->precio_minimo : $validated['precio_venta'];
        $validated['stock']         = is_numeric($request->stock) ? (int)$request->stock : 0;
        $validated['stock_minimo']  = is_numeric($request->stock_minimo) ? (int)$request->stock_minimo : 0;
        $validated['stock_maximo']  = is_numeric($request->stock_maximo) ? (int)$request->stock_maximo : null;
        $validated['impuesto']      = is_numeric($request->impuesto) ? $request->impuesto : 7;

        $validated['is_excluded']   = $request->boolean('is_excluded');
        $validated['tasa_impuesto'] = is_numeric($request->tasa_impuesto) ? $request->tasa_impuesto : 19.00;

        // Recalcular margen
        if ($validated['precio_compra'] > 0) {
            $validated['margen'] = (($validated['precio_venta'] - $validated['precio_compra']) / $validated['precio_compra']) * 100;
        } else {
            $validated['margen'] = 0;
        }

        $producto->update($validated);

        return redirect()->route('inventario.productos')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Actualizado!',
                'message' => 'El producto fue actualizado correctamente.',
            ]);
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('inventario.productos')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Eliminado!',
                'message' => 'El producto fue eliminado correctamente.',
            ]);
    }

    public function exportar($formato)
    {
        if ($formato === 'pdf') {
            $productos = Producto::with(['categoria', 'subcategoria'])->orderBy('nombre')->get();
            $pdf = Pdf::loadView('exports.productos-pdf', compact('productos'));
            return $pdf->download('productos_' . now()->format('Ymd_His') . '.pdf');
        }

        return Excel::download(new ProductosExport, 'productos_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|extensions:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new ProductosImport, $request->file('archivo'));

            return redirect()->route('inventario.productos')
                ->with('sweet_alert', [
                    'type'    => 'success',
                    'title'   => '¡Importación exitosa!',
                    'message' => 'Los productos han sido cargados correctamente.',
                ]);
        } catch (\Exception $e) {
            return redirect()->route('inventario.productos')
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
            'nombre',
            'sku',
            'codigo_barras',
            'categoria',
            'subcategoria',
            'precio_compra',
            'precio_venta',
            'precio_minimo',
            'impuesto',
            'stock',
            'stock_minimo',
            'unidad_medida',
            'ubicacion',
            'estado'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            // Añadir BOM para UTF-8 para que Excel reconozca el formato y carácteres especiales
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            // Usar punto y coma como delimitador para mejor compatibilidad con Excel en español
            fputcsv($file, $headers, ';');
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=plantilla_productos.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:productos,id',
        ]);

        try {
            $productos = Producto::whereIn('id', $request->ids)->get();
            foreach ($productos as $producto) {
                if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                    Storage::disk('public')->delete($producto->imagen);
                }
            }

            Producto::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' productos eliminados correctamente.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden eliminar los productos porque tienen transacciones asociadas.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar eliminar los registros.'
            ], 500);
        }
    }
}

