<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Exports\ClientesExport;
use App\Imports\ClientesImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::latest()->get();
        return view('pages.clientes.clientes', compact('clientes'));
    }

    public function exportar($formato)
    {
        if ($formato === 'pdf') {
            $clientes = Cliente::orderBy('nombre')->get();
            $pdf = Pdf::loadView('exports.clientes-pdf', compact('clientes'));
            return $pdf->download('clientes_' . now()->format('Ymd_His') . '.pdf');
        }

        return Excel::download(new ClientesExport, 'clientes_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|extensions:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new ClientesImport, $request->file('archivo'));

            return redirect()->route('clientes.index')
                ->with('sweet_alert', [
                    'type'    => 'success',
                    'title'   => '¡Importación exitosa!',
                    'message' => 'Los clientes han sido cargados correctamente.',
                ]);
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')
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
            'tipo_cliente',
            'nombre',
            'apellido',
            'empresa',
            'cedula',
            'ruc',
            'dv',
            'pasaporte',
            'telefono',
            'email',
            'direccion',
            'provincia',
            'distrito',
            'pais',
            'limite_credito',
            'estado'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            // Añadir BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            // Usar punto y coma como delimitador
            fputcsv($file, $headers, ';');
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=plantilla_clientes.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_cliente'    => 'required|in:natural,juridico,extranjero,b2b,b2c',
            'nombre'          => 'nullable|string|max:150',
            'apellido'        => 'nullable|string|max:150',
            'empresa'         => 'nullable|string|max:200',
            'cedula'          => 'nullable|string|max:30',
            'ruc'             => 'nullable|string|max:50',
            'dv'              => 'nullable|string|max:5',
            'pasaporte'       => 'nullable|string|max:50',
            'telefono'        => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:200',
            'direccion'       => 'nullable|string|max:500',
            'provincia'       => 'nullable|string|max:100',
            'distrito'        => 'nullable|string|max:100',
            'pais'            => 'nullable|string|max:100',
            'limite_credito'  => 'nullable|numeric|min:0',
            'notas'           => 'nullable|string|max:1000',
            'estado'          => 'nullable',
            'tipo_documento_dian_id'    => 'nullable|integer',
            'tipo_organizacion_dian_id' => 'nullable|integer',
            'tributo_dian_id'           => 'nullable|integer',
            'municipio_dian_id'         => 'nullable|integer',
        ]);

        $validated['estado']         = $request->boolean('estado');
        $validated['pais']           = $validated['pais'] ?? 'Panamá';
        $validated['limite_credito'] = is_numeric($request->limite_credito) ? $request->limite_credito : 0;

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Cliente creado!',
                'message' => 'El cliente fue registrado correctamente.',
            ]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'tipo_cliente'    => 'required|in:natural,juridico,extranjero,b2b,b2c',
            'nombre'          => 'nullable|string|max:150',
            'apellido'        => 'nullable|string|max:150',
            'empresa'         => 'nullable|string|max:200',
            'cedula'          => 'nullable|string|max:30',
            'ruc'             => 'nullable|string|max:50',
            'dv'              => 'nullable|string|max:5',
            'pasaporte'       => 'nullable|string|max:50',
            'telefono'        => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:200',
            'direccion'       => 'nullable|string|max:500',
            'provincia'       => 'nullable|string|max:100',
            'distrito'        => 'nullable|string|max:100',
            'pais'            => 'nullable|string|max:100',
            'limite_credito'  => 'nullable|numeric|min:0',
            'notas'           => 'nullable|string|max:1000',
            'estado'          => 'nullable',
            'tipo_documento_dian_id'    => 'nullable|integer',
            'tipo_organizacion_dian_id' => 'nullable|integer',
            'tributo_dian_id'           => 'nullable|integer',
            'municipio_dian_id'         => 'nullable|integer',
        ]);

        $validated['estado']         = $request->boolean('estado');
        $validated['limite_credito'] = is_numeric($request->limite_credito) ? $request->limite_credito : 0;

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Actualizado!',
                'message' => 'El cliente fue actualizado correctamente.',
            ]);
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Eliminado!',
                'message' => 'El cliente fue eliminado correctamente.',
            ]);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:clientes,id',
        ]);

        try {
            Cliente::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' clientes eliminados correctamente.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pueden eliminar los clientes seleccionados porque tienen transacciones asociadas.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar eliminar los registros.'
            ], 500);
        }
    }
}
