<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Proveedor;
use App\Exports\ProveedoresExport;
use App\Imports\ProveedoresImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::latest()->get();
        return view('pages.inventario.proveedores', compact('proveedores'));
    }

    public function exportar($formato)
    {
        if ($formato === 'pdf') {
            $proveedores = Proveedor::orderBy('empresa')->get();
            $pdf = Pdf::loadView('exports.proveedores-pdf', compact('proveedores'));
            return $pdf->download('proveedores_' . now()->format('Ymd_His') . '.pdf');
        }

        return Excel::download(new ProveedoresExport, 'proveedores_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|extensions:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new ProveedoresImport, $request->file('archivo'));

            return redirect()->route('inventario.proveedores')
                ->with('sweet_alert', [
                    'type'    => 'success',
                    'title'   => '¡Importación exitosa!',
                    'message' => 'Los proveedores han sido cargados correctamente.',
                ]);
        } catch (\Exception $e) {
            return redirect()->route('inventario.proveedores')
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
            'empresa',
            'ruc',
            'dv',
            'contacto',
            'telefono',
            'email',
            'direccion',
            'provincia',
            'ciudad',
            'pais',
            'notas',
            'estado'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_proveedores.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa'    => 'required|string|max:200',
            'ruc'        => 'nullable|string|max:50',
            'dv'         => 'nullable|string|max:10',
            'contacto'   => 'nullable|string|max:150',
            'telefono'   => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:150',
            'direccion'  => 'nullable|string|max:500',
            'provincia'  => 'nullable|string|max:100',
            'ciudad'     => 'nullable|string|max:100',
            'pais'       => 'nullable|string|max:100',
            'notas'      => 'nullable|string|max:1000',
            'estado'     => 'nullable|boolean',
        ]);

        $validated['estado'] = $request->boolean('estado', true);

        Proveedor::create($validated);

        return redirect()->route('inventario.proveedores')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Creado!',
                'message' => 'El proveedor fue registrado correctamente.',
            ]);
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'empresa'    => 'required|string|max:200',
            'ruc'        => 'nullable|string|max:50',
            'dv'         => 'nullable|string|max:10',
            'contacto'   => 'nullable|string|max:150',
            'telefono'   => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:150',
            'direccion'  => 'nullable|string|max:500',
            'provincia'  => 'nullable|string|max:100',
            'ciudad'     => 'nullable|string|max:100',
            'pais'       => 'nullable|string|max:100',
            'notas'      => 'nullable|string|max:1000',
            'estado'     => 'nullable|boolean',
        ]);

        $validated['estado'] = $request->boolean('estado', true);

        $proveedor->update($validated);

        return redirect()->route('inventario.proveedores')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Actualizado!',
                'message' => 'El proveedor fue actualizado correctamente.',
            ]);
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()->route('inventario.proveedores')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Eliminado!',
                'message' => 'El proveedor fue eliminado correctamente.',
            ]);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:proveedores,id',
        ]);

        try {
            Proveedor::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' proveedores eliminados correctamente.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden eliminar los proveedores porque tienen transacciones asociadas.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar eliminar los registros.'
            ], 500);
        }
    }
}
