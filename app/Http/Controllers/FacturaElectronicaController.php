<?php

/**
 * FacturaElectronicaController — Controlador de Facturación Electrónica DIAN
 *
 * Orquesta todo el flujo de negocio del módulo de facturación electrónica
 * en WavePOS. Actúa como intermediario entre las vistas/POS y el servicio
 * FactusService que se comunica con la API de Factus/DIAN.
 *
 * Rutas registradas (prefix: /facturacion, name: facturacion.*):
 *  GET    /facturacion                  → index()       Listado paginado
 *  GET    /facturacion/create           → create()      Formulario manual
 *  POST   /facturacion/store            → store()       Desde POS (AJAX/JSON)
 *  POST   /facturacion/store-manual     → storeManual() Desde formulario web
 *  GET    /facturacion/{id}             → show()        Detalle con sync live
 *  GET    /facturacion/{id}/pdf         → pdf()         Descarga PDF DIAN
 *  POST   /facturacion/{id}/send-email  → sendEmail()   Envío por correo
 *
 * Principios de diseño aplicados:
 *  - Single Responsibility: lógica HTTP delegada a FactusService.
 *  - Database Transactions: store() y storeManual() usan DB::beginTransaction().
 *  - Duplicate Prevention: verifica existencia antes de facturar.
 *  - Standardized Responses: store() siempre retorna JSON (llamado via fetch).
 *
 * @package App\Http\Controllers
 * @author  WavePOS — NextWave
 * @version 1.0.0
 */

namespace App\Http\Controllers;

use App\Models\FacturaElectronica;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Services\FactusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacturaElectronicaController extends Controller
{
    /** @var FactusService Servicio de comunicación con la API de Factus */
    protected FactusService $factusService;

    /**
     * Inyección de dependencia del servicio Factus mediante el Service Container de Laravel.
     * Laravel resuelve automáticamente FactusService al instanciar este controlador.
     *
     * @param FactusService $factusService
     */
    public function __construct(FactusService $factusService)
    {
        $this->factusService = $factusService;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MÉTODOS PÚBLICOS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Muestra el listado paginado de todas las facturas electrónicas.
     *
     * Usa eager loading (with) para cargar la relación venta→cliente en
     * una sola consulta SQL eficiente (evita el problema N+1).
     *
     * Vista: pages/facturacion/index.blade.php
     * Ruta:  GET /facturacion  (facturacion.index)
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $facturas = FacturaElectronica::with('venta.cliente')->latest()->paginate(25);
        
        $stats = [
            'validado_monto' => FacturaElectronica::where('status', 'Validado')->sum('total'),
            'validado_count' => FacturaElectronica::where('status', 'Validado')->count(),
            'pendiente_monto' => FacturaElectronica::where('status', '!=', 'Validado')->sum('total'),
            'pendiente_count' => FacturaElectronica::where('status', '!=', 'Validado')->count(),
        ];

        return view('pages.facturacion.index', compact('facturas', 'stats'));
    }

    /**
     * Muestra el formulario para crear una factura electrónica manualmente.
     *
     * Prepara los datos de clientes, productos y ventas sin facturar para
     * ser usados por Alpine.js en el frontend. Serializa los datos como JSON
     * para inyectarlos de forma segura en el componente reactivo sin que Blade
     * interprete las llaves de JavaScript como directivas Blade.
     *
     * Parámetro opcional: ?venta_id={id} — pre-carga una venta existente
     * en el formulario (flujo desde historial de ventas → facturar).
     *
     * Vista: pages/facturacion/create.blade.php
     * Ruta:  GET /facturacion/create  (facturacion.create)
     *
     * Variables enviadas a la vista:
     *  - $clientes, $productos, $ventas       → Colecciones Eloquent
     *  - $productosJs, $clientesJs, $ventasJs → JSON para Alpine.js
     *  - $ventaPreload, $ventaPreloadJs        → Venta pre-cargada (nullable)
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $clientes  = Cliente::where('estado', true)->orderBy('nombre')->get();
        $productos = Producto::where('estado', true)->orderBy('nombre')->get();
        $ventas    = Venta::with('cliente')
            ->whereDoesntHave('facturaElectronica')
            ->where('estado', 'completada')
            ->latest()->take(50)->get();

        $ventaPreload = null;
        if ($request->has('venta_id')) {
            $ventaPreload = Venta::with(['cliente', 'detalles.producto'])->findOrFail($request->venta_id);
        }

        // Preparar JSON para Alpine.js (evita ParseError de Blade con fn() => [])
        $productosJs = json_encode($productos->map(function ($p) {
            return [
                'id'       => $p->id,
                'nombre'   => $p->nombre,
                'codigo'   => $p->codigo_barras ?? (string) $p->id,
                'precio'   => (float) $p->precio_venta,
                'impuesto' => (float) ($p->impuesto ?? 19.00),
            ];
        })->values());

        $clientesJs = json_encode($clientes->map(function ($c) {
            return [
                'id'        => $c->id,
                'nombre'    => trim(($c->nombre ?? '') . ' ' . ($c->apellido ?? '')),
                'empresa'   => $c->empresa ?? '',
                'documento' => $c->cedula ?? $c->ruc ?? $c->pasaporte ?? '',
                'email'     => $c->email ?? '',
                'telefono'  => (string) ($c->telefono ?? ''),
                'direccion' => $c->direccion ?? '',
            ];
        })->values());

        $ventasJs = json_encode($ventas->map(function ($v) {
            return [
                'id'         => $v->id,
                'numero'     => $v->numero,
                'total'      => (float) $v->total,
                'cliente'    => $v->cliente?->nombre ?? 'Consumidor Final',
                'cliente_id' => $v->cliente_id,
                'fecha'      => $v->created_at->format('d/m/Y'),
            ];
        })->values());

        if ($ventaPreload) {
            $ventaPreloadJs = json_encode([
                'id'         => $ventaPreload->id,
                'cliente_id' => $ventaPreload->cliente_id,
                'items'      => $ventaPreload->detalles->map(function ($d) {
                    return [
                        'producto_id'    => $d->producto_id,
                        'nombre'         => $d->producto->nombre ?? 'Producto',
                        'codigo'         => $d->producto->codigo_barras ?? (string) $d->producto_id,
                        'cantidad'       => (int) $d->cantidad,
                        'precio'         => (float) $d->precio_unitario,
                        'tax_rate'       => '19.00',
                        'discount_rate'  => 0,
                        'subtotal'       => (float) $d->precio_unitario * (int) $d->cantidad,
                    ];
                })->values(),
            ]);
        } else {
            $ventaPreloadJs = 'null';
        }

        // Obtener el logo de la empresa desde el último json_response guardado de Factus
        $companyLogoUrl = null;
        $latestFactura = FacturaElectronica::whereNotNull('json_response')->latest()->first();
        if ($latestFactura && $latestFactura->json_response) {
            $latestData = is_string($latestFactura->json_response)
                ? json_decode($latestFactura->json_response, true)
                : $latestFactura->json_response;
            $companyLogoUrl = $latestData['data']['company']['url_logo'] ?? null;
        }

        return view('pages.facturacion.create', compact(
            'clientes', 'productos', 'ventas', 'ventaPreload',
            'productosJs', 'clientesJs', 'ventasJs', 'ventaPreloadJs',
            'companyLogoUrl'
        ));
    }

    /**
     * Procesa el formulario manual y envía la factura a Factus para validación DIAN.
     *
     * Flujo completo:
     *  1. Valida los campos del Request (cliente, método de pago, items JSON).
     *  2. Deserializa los items enviados desde Alpine.js (items_json).
     *  3. Valida que cada item tenga precio >= $1 COP (requisito Factus).
     *  4. Mapea los datos del cliente según tipo (natural, jurídico, extranjero).
     *  5. Construye el payload $invoiceData conforme a la especificación de Factus.
     *  6. Llama a FactusService::validateBill() dentro de una DB Transaction.
     *  7. Si la DIAN aprueba → guarda FacturaElectronica y hace commit.
     *  8. Si falla → rollback y retorna al formulario con los errores.
     *
     * Nota sobre reference_code: Se genera con timestamp 'MAN-YmdHis' para
     * garantizar unicidad y evitar el error de colisión de referencia en Factus.
     *
     * Vista de retorno: facturacion.show (éxito) | facturacion.create (error)
     * Ruta: POST /facturacion/store-manual  (facturacion.storeManual)
     *
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'cliente_id'             => 'required|exists:clientes,id',
            'payment_method_code'    => 'required|string',
            'items_json'             => 'required|string',
            'allowance_charges_json' => 'nullable|string',
            'document_type'          => 'nullable|string',
            'numbering_range_id'     => 'nullable|integer',
            'reference_code'         => 'nullable|string',
            'observation'            => 'nullable|string',
        ]);

        // Los items vienen como JSON serializado (Alpine.js template no emite inputs reales al DOM)
        $itemsRaw = json_decode($request->items_json, true);
        if (empty($itemsRaw) || !is_array($itemsRaw)) {
            return back()->withInput()->with('error', 'Debes agregar al menos un producto a la factura.');
        }

        $cliente = Cliente::findOrFail($request->cliente_id);
        $estab   = config('factus.establishment');

        $items = [];
        foreach ($itemsRaw as $item) {
            // Soportar diferentes keys por compatibilidad con JS
            $precioItem = (float) ($item['precio'] ?? $item['precio_unitario'] ?? $item['price'] ?? 0);
            
            // Factus API exige que el precio sea al menos 1 COP
            if ($precioItem < 1) {
                return back()->withInput()->with('error', 'Error de validación: El producto "' . ($item['nombre'] ?? 'Item') . '" tiene un precio inválido ($' . $precioItem . '). Factus exige que el precio sea al menos $1 COP.');
            }

            $items[] = [
                "code_reference"    => (string) ($item['codigo'] ?? $item['code_reference'] ?? 'ITEM'),
                "name"              => $item['nombre'] ?? $item['name'] ?? 'Producto',
                "quantity"          => (int) ($item['cantidad'] ?? 1),
                "discount_rate"     => (float) ($item['discount_rate'] ?? 0),
                "price"             => $precioItem,
                "tax_rate"          => (string) ($item['tax_rate'] ?? '19.00'),
                "unit_measure_id"   => (int) ($item['unit_measure_id'] ?? 70),
                "standard_code_id"  => 1,
                "is_excluded"       => (int) ($item['is_excluded'] ?? 0),
                "tribute_id"        => 1,
                "withholding_taxes" => is_array($item['withholding_taxes'] ?? null) ? $item['withholding_taxes'] : []
            ];
        }

        $allowanceCharges = [];
        if ($request->filled('allowance_charges_json')) {
            $allowanceArr = json_decode($request->allowance_charges_json, true);
            if (is_array($allowanceArr)) {
                $allowanceCharges = $allowanceArr;
            }
        }

        $docCliente = match($cliente->tipo_cliente ?? 'natural') {
            'juridico', 'b2b' => $cliente->ruc ?? '0',
            'extranjero'      => $cliente->pasaporte ?? '0',
            default           => $cliente->cedula ?? '123456789',
        };
        $esJuridico = in_array($cliente->tipo_cliente ?? '', ['juridico', 'b2b']);

        $obs = $request->observation ?? '';
        if ($request->filled('payment_reference')) {
            $obs .= ($obs ? ' - ' : '') . 'Ref. Pago/Voucher: ' . $request->payment_reference;
        }

        $invoiceData = [
            "document"            => $request->document_type ?? "01",
            "numbering_range_id"  => $request->filled('numbering_range_id') ? (int) $request->numbering_range_id : (int) config('factus.numbering_range_id', 8),
            "reference_code"      => $request->filled('reference_code') ? $request->reference_code : ('MAN-' . now()->format('YmdHis')),
            "observation"         => $obs,
            "payment_form"        => $request->payment_form ?? "1",
            "payment_method_code" => $request->payment_method_code,
            "establishment"       => [
                "name"            => $estab['name'],
                "address"         => $estab['address'],
                "phone_number"    => $estab['phone_number'],
                "email"           => $estab['email'],
                "municipality_id" => (int) $estab['municipality_id'],
            ],
            "customer" => [
                "identification"             => (string) $docCliente,
                "dv"                         => (string) ($cliente->dv ?? '0'),
                "company"                    => $cliente->empresa ?? '',
                "trade_name"                 => $cliente->empresa ?? '',
                "names"                      => trim(trim((string)($cliente->nombre ?? '')) . ' ' . trim((string)($cliente->apellido ?? ''))) ?: ($cliente->empresa ?: 'Consumidor Final'),
                "address"                    => $cliente->direccion ?? 'Calle falsa 123',
                "email"                      => $cliente->email ?? 'correo@example.com',
                "phone"                      => (string) ($cliente->telefono ?? '3000000000'),
                "legal_organization_id"      => (string) ($cliente->tipo_organizacion_dian_id ?? ($esJuridico ? "1" : "2")),
                "tribute_id"                 => (string) ($cliente->tributo_dian_id ?? "21"),
                "identification_document_id" => (int) match((string)($cliente->tipo_documento_dian_id ?? '')) {
                    '11' => 1, '12' => 2, '13' => 3, '21' => 4, '22' => 5, '31' => 6, '41' => 7, '42' => 8,
                    ''   => ($esJuridico ? 6 : 3),
                    default => $cliente->tipo_documento_dian_id
                },
                "municipality_id"            => (string) ($cliente->municipio_dian_id ?? config('factus.default_municipality_id', 980)),
            ],
            "items" => $items
        ];

        if (!empty($allowanceCharges)) {
            $invoiceData["allowance_charges"] = $allowanceCharges;
        }

        if ($request->payment_form == "2" && $request->filled('payment_due_date')) {
            $invoiceData["payment_due_date"] = $request->payment_due_date;
        }

        Log::info('Factus storeManual payload:', $invoiceData);

        try {
            DB::beginTransaction();
            $response = $this->factusService->validateBill($invoiceData);

            if ($response['success'] && isset($response['data']['data']['bill'])) {
                $billInfo = $response['data']['data']['bill'];
                $total    = collect($items)->sum(fn($i) => $i['price'] * $i['quantity']);

                $factura = FacturaElectronica::create([
                    'venta_id'      => $request->venta_id ?? null,
                    'factus_id'     => $billInfo['id'] ?? null,
                    'numero'        => $billInfo['number'] ?? 'N/A',
                    'cufe'          => $billInfo['cufe'] ?? null,
                    'qr'            => $billInfo['qr'] ?? null,
                    'status'        => ($billInfo['status'] ?? 0) == 1 ? 'Validado' : 'Pendiente',
                    'total'         => $total,
                    'json_request'  => $invoiceData,
                    'json_response' => $response['data'],
                ]);

                DB::commit();
                return redirect()->route('facturacion.show', $factura->id)
                    ->with('success', '¡Factura ' . $factura->numero . ' generada y validada en la DIAN!');

            } else {
                DB::rollBack();
                $errDetail = is_array($response['errors'] ?? null)
                    ? collect($response['errors'])->flatten()->implode(' | ')
                    : ($response['message'] ?? 'Error desconocido de Factus');

                Log::error('Factus storeManual error:', $response);
                return back()->withInput()->with('error', 'Error DIAN: ' . $errDetail);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception storeManual:', ['msg' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Excepción: ' . $e->getMessage());
        }
    }

    /**
     * Convierte una venta existente del POS en Factura Electrónica DIAN.
     *
     * Este método es llamado exclusivamente via AJAX (fetch) desde el
     * punto de venta. Siempre retorna JSON — nunca hace redirect.
     *
     * Validaciones de negocio antes de procesar:
     *  - La venta debe existir en la tabla ventas.
     *  - La venta NO debe tener ya una factura electrónica (HTTP 409).
     *  - La venta DEBE tener un cliente con datos completos (HTTP 422).
     *
     * Construcción del payload:
     *  - Itera los detalles de la venta para construir los items.
     *  - Determina el número de documento según tipo_cliente.
     *  - Usa reference_code = 'POS-{venta_id}-{timestamp}' para unicidad.
     *  - Lee la configuración del establecimiento desde config/factus.php.
     *
     * Respuestas JSON:
     *  - Éxito (200):  {success:true, factura_id, numero, cufe}
     *  - Duplicado (409): {success:false, message}
     *  - Error DIAN (422): {success:false, message, details}
     *  - Error interno (500): {success:false, message}
     *
     * Ruta: POST /facturacion/store  (facturacion.store)
     *
     * @param  Request $request  Requiere: venta_id (integer, exists:ventas,id)
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id'
        ]);

        $venta = Venta::with(['cliente', 'detalles.producto'])->findOrFail($request->venta_id);

        // Prevenir facturación duplicada
        if ($venta->facturaElectronica()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta venta ya fue facturada electrónicamente.'
            ], 409);
        }

        // El cliente es requerido para DIAN
        if (!$venta->cliente) {
            return response()->json([
                'success' => false,
                'message' => 'La venta no tiene un cliente asignado. Para facturar a la DIAN es obligatorio tener cliente con datos completos (nombre, documento, email).'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Construir items segun spec de Factus
            $items = [];
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                $items[] = [
                    "code_reference"    => (string) ($producto->codigo_barras ?? (string) $detalle->producto_id),
                    "name"              => $producto->nombre ?? 'Producto',
                    "quantity"          => (int) $detalle->cantidad,
                    "discount_rate"     => 0,
                    "price"             => (float) $detalle->precio_unitario,
                    "tax_rate"          => (string) ($producto->tasa_impuesto ?? '19.00'),
                    "unit_measure_id"   => (int) ($producto->unidad_medida_dian_id ?? 70),
                    "standard_code_id"  => 1,
                    "is_excluded"       => (int) ($producto->is_excluded ?? 0),
                    "tribute_id"        => 1,
                    "withholding_taxes" => []
                ];
            }

            $cliente = $venta->cliente;
            $estab   = config('factus.establishment');

            $docCliente = match($cliente->tipo_cliente ?? 'natural') {
                'juridico', 'b2b' => $cliente->ruc ?? '0',
                'extranjero'      => $cliente->pasaporte ?? '0',
                default           => $cliente->cedula ?? '123456789',
            };
            $esJuridico = in_array($cliente->tipo_cliente ?? '', ['juridico', 'b2b']);

            // Payload exacto segun documentacion oficial de Factus API
            $invoiceData = [
                "document"            => "01",
                "numbering_range_id"  => (int) config('factus.numbering_range_id', 8),
                "reference_code"      => 'POS-' . $venta->id . '-' . time(),
                "observation"         => "Factura generada desde sistema POS Wave",
                "payment_method_code" => (string) ($venta->metodo_pago_dian_id ?? "10"),
                "payment_form"        => (string) ($venta->forma_pago_dian ?? "1"),
                "establishment"       => [
                    "name"            => $estab['name'],
                    "address"         => $estab['address'],
                    "phone_number"    => $estab['phone_number'],
                    "email"           => $estab['email'],
                    "municipality_id" => (int) $estab['municipality_id'],
                ],
                "customer" => [
                    "identification"             => (string) $docCliente,
                    "dv"                         => (string) ($cliente->dv ?? '0'),
                    "company"                    => $cliente->empresa ?? "",
                    "trade_name"                 => $cliente->empresa ?? "",
                    "names"                      => trim(trim((string)($cliente->nombre ?? '')) . ' ' . trim((string)($cliente->apellido ?? ''))) ?: ($cliente->empresa ?: 'Consumidor Final'),
                    "address"                    => $cliente->direccion ?? 'Calle falsa 123',
                    "email"                      => $cliente->email ?? 'correo@example.com',
                    "phone"                      => (string) ($cliente->telefono ?? '3000000000'),
                    "legal_organization_id"      => (string) ($cliente->tipo_organizacion_dian_id ?? ($esJuridico ? "1" : "2")),
                    "tribute_id"                 => (string) ($cliente->tributo_dian_id ?? "21"),
                    "identification_document_id" => (int) match((string)($cliente->tipo_documento_dian_id ?? '')) {
                        '11' => 1, '12' => 2, '13' => 3, '21' => 4, '22' => 5, '31' => 6, '41' => 7, '42' => 8,
                        ''   => ($esJuridico ? 6 : 3),
                        default => $cliente->tipo_documento_dian_id
                    },
                    "municipality_id"            => (string) ($cliente->municipio_dian_id ?? config('factus.default_municipality_id', 980)),
                ],
                "items" => $items
            ];

            Log::info('Factus payload enviado:', $invoiceData);

            $response = $this->factusService->validateBill($invoiceData);

            Log::info('Factus response raw:', [
                'success'   => $response['success'],
                'has_bill'  => isset($response['data']['data']['bill']),
                'data_keys' => isset($response['data']) ? array_keys($response['data']) : [],
            ]);

            if ($response['success'] && isset($response['data']['data']['bill'])) {
                $billInfo = $response['data']['data']['bill'];

                $factura = FacturaElectronica::create([
                    'venta_id'      => $venta->id,
                    'factus_id'     => $billInfo['id'] ?? null,
                    'numero'        => $billInfo['number'] ?? 'N/A',
                    'cufe'          => $billInfo['cufe'] ?? null,
                    'qr'            => $billInfo['qr'] ?? null,
                    'status'        => ($billInfo['status'] ?? 0) == 1 ? 'Validado' : 'Pendiente',
                    'total'         => $venta->total,
                    'json_request'  => $invoiceData,
                    'json_response' => $response['data'],
                ]);

                DB::commit();

                return response()->json([
                    'success'    => true,
                    'message'    => 'Factura electronica generada exitosamente.',
                    'factura_id' => $factura->id,
                    'numero'     => $factura->numero,
                    'cufe'       => $factura->cufe,
                ]);

            } else {
                DB::rollBack();

                // Extraer el mensaje de error especifico de Factus
                $apiErrors  = $response['errors'] ?? null;
                $apiMessage = $response['message'] ?? 'Error desconocido de Factus';

                if (is_array($apiErrors)) {
                    $parts = [];
                    array_walk_recursive($apiErrors, function ($val, $key) use (&$parts) {
                        $parts[] = is_string($key) ? "$key: $val" : $val;
                    });
                    $errDetail = implode(' | ', $parts);
                } else {
                    $errDetail = $apiMessage;
                }

                Log::error('Factus API error:', ['response' => $response]);

                return response()->json([
                    'success' => false,
                    'message' => $errDetail,
                    'details' => $response,
                ], 422);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception Factus Store:', [
                'msg'   => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Excepcion interna: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra el detalle completo de una factura electrónica con sincronización en vivo.
     *
     * Al cargar la vista de detalle, siempre consulta la API de Factus
     * (FactusService::showBill) para obtener el estado más reciente desde
     * la DIAN. Si la respuesta es exitosa, actualiza en BD:
     *  - status        → 'Validado' o 'Pendiente'
     *  - cufe          → Código fiscal actualizado
     *  - qr            → URL del QR DIAN
     *  - json_response → Data fresca completa (para renderizado en vista)
     *
     * Si la API de Factus falla (timeout, error), registra un warning en Log
     * pero NO interrumpe la carga de la vista (muestra los datos en BD).
     *
     * Vista: pages/facturacion/show.blade.php
     * Ruta:  GET /facturacion/{id}  (facturacion.show)
     *
     * @param  int $id  ID local de la FacturaElectronica.
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $factura = FacturaElectronica::with('venta.cliente')->findOrFail($id);

        // Consultar siempre la API de Factus si existe un número válido (Preview/Refresh)
        if ($factura->numero && $factura->numero !== 'N/A') {
            try {
                $freshData = $this->factusService->showBill($factura->numero);

                if ($freshData['success'] && isset($freshData['data']['data']['bill'])) {
                    $billFresh = $freshData['data']['data']['bill'];

                    $factura->update([
                        'status'        => ($billFresh['status'] ?? 0) == 1 ? 'Validado' : 'Pendiente',
                        'cufe'          => $billFresh['cufe'] ?? $factura->cufe,
                        'qr'            => $billFresh['qr']   ?? $factura->qr,
                        // Guardar la data fresca para que la vista renderice la factura con todo
                        'json_response' => $freshData['data'],
                    ]);

                    $factura->refresh();
                }
            } catch (\Exception $e) {
                Log::warning('No se pudo refrescar factura desde Factus: ' . $e->getMessage());
            }
        }

        return view('pages.facturacion.show', compact('factura'));
    }

    /**
     * Descarga el PDF de representación gráfica de la factura desde Factus.
     *
     * Factus retorna el archivo PDF como string codificado en Base64.
     * Este método lo decodifica y lo sirve directamente al navegador con
     * los headers correctos para visualización inline (no fuerza descarga).
     *
     * Formato de respuesta:
     *  Content-Type: application/pdf
     *  Content-Disposition: inline; filename="{nombre_archivo}.pdf"
     *
     * Requisito: la factura debe tener un número DIAN válido (no 'N/A').
     * Si no tiene número, redirige atrás con mensaje de error.
     *
     * Ruta: GET /facturacion/{id}/pdf  (facturacion.pdf)
     *
     * @param  int $id  ID local de la FacturaElectronica.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function pdf($id)
    {
        $factura = FacturaElectronica::findOrFail($id);

        if (!$factura->numero || $factura->numero === 'N/A') {
            return back()->with('error', 'La factura aún no ha sido validada por la DIAN (No tiene número).');
        }

        try {
            $response = $this->factusService->downloadPdf($factura->numero);

            if ($response['success'] && !empty($response['data']['data']['pdf_base_64_encoded'])) {
                
                $base64 = $response['data']['data']['pdf_base_64_encoded'];
                $fileName = $response['data']['data']['file_name'] ?? ('Factura_' . $factura->numero . '.pdf');
                
                $pdfData = base64_decode($base64);

                return response($pdfData, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
            }

            return back()->with('error', 'Factus no pudo generar o devolver el PDF en este momento. Intenta más tarde.');
        } catch (\Exception $e) {
            Log::error('Excepcion descargando PDF DIAN: ' . $e->getMessage());
            return back()->with('error', 'Error interno al conectarse a Factus. ' . $e->getMessage());
        }
    }

    /**
     * Solicita a Factus el envío de la factura por correo electrónico al cliente.
     *
     * Factus gestiona el envío completo — incluyendo el PDF de representación
     * gráfica y el XML del documento electrónico — directamente desde su
     * plataforma al email indicado.
     *
     * Validaciones:
     *  - El campo email debe ser un email válido (RFC 5322).
     *  - La factura debe tener número DIAN válido (estado != 'N/A').
     *
     * Los resultados se comunican al usuario mediante SweetAlert2 (sweet_alert
     * session flash) para una experiencia de usuario más limpia que un simple
     * mensaje de texto.
     *
     * Nota sandbox: En el entorno de pruebas de Factus, el envío puede
     * comportarse diferente o rechazar ciertos dominios de correo.
     *
     * Ruta: POST /facturacion/{id}/send-email  (facturacion.sendEmail)
     *
     * @param  Request $request  Requiere: email (string|email|max:255)
     * @param  int     $id       ID local de la FacturaElectronica.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendEmail(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        $factura = FacturaElectronica::findOrFail($id);

        if (!$factura->numero || $factura->numero === 'N/A') {
            return back()->with('sweet_alert', [
                'type'    => 'error',
                'title'   => 'Factura no validada',
                'message' => 'La factura aún no se ha validado en la DIAN. No se puede enviar.'
            ]);
        }

        try {
            $response = $this->factusService->sendEmail($factura->numero, $request->email);

            if ($response['success']) {
                return back()->with('sweet_alert', [
                    'type'    => 'success',
                    'title'   => '¡Enviado!',
                    'message' => 'La factura ha sido enviada al correo ' . $request->email
                ]);
            }

            return back()->with('sweet_alert', [
                'type'    => 'error',
                'title'   => 'Error en el envío',
                'message' => $response['message'] ?? 'Hubo un problema al enviar el correo desde Factus.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando factura por email: ' . $e->getMessage());
            return back()->with('sweet_alert', [
                'type'    => 'error',
                'title'   => 'Error interno',
                'message' => 'Problemas de conexión con Factus: ' . $e->getMessage()
            ]);
        }
    }
}
