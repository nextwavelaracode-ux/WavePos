<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\PagoVenta;
use App\Models\Producto;
use App\Models\Setting;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Services\WooCommerceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WooCommerceController extends Controller
{
    protected WooCommerceService $wcService;

    public function __construct(WooCommerceService $wcService)
    {
        $this->wcService = $wcService;
    }

    /**
     * Dashboard principal de WooCommerce en WavePos.
     */
    public function index(Request $request)
    {
        $summary = $this->wcService->getStoreSummary($request->has('refresh'));

        // Productos locales con paginación y filtro de sincronización
        $filter = $request->get('filter', 'all');
        $query = Producto::with(['categoria'])->latest();

        if ($filter === 'linked') {
            $query->whereNotNull('woocommerce_id');
        } elseif ($filter === 'unlinked') {
            $query->whereNull('woocommerce_id');
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('codigo_barras', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->get('categoria_id'));
        }

        $productos = $query->paginate(15)->withQueryString();
        $categorias = \App\Models\Categoria::where('estado', true)->orderBy('nombre')->get();

        $settings = [
            'store_url'       => Setting::get('wc_store_url') ?: config('woocommerce.store_url', 'http://tienda.test'),
            'consumer_key'    => Setting::get('wc_consumer_key') ?: config('woocommerce.consumer_key', ''),
            'consumer_secret' => Setting::get('wc_consumer_secret') ?: config('woocommerce.consumer_secret', ''),
        ];

        return view('pages.woocommerce.index', compact('summary', 'productos', 'categorias', 'settings', 'filter'));
    }

    /**
     * Minimódulo de Cobro y Entrega de Pedidos Contra Reembolso (Retiro en Tienda).
     */
    public function cobroIndex(Request $request)
    {
        $search = trim($request->get('search', ''));
        $tab = $request->get('tab', 'pendientes');

        // Pedidos pendientes de cobro y entrega (processing, on-hold)
        $rawPending = $this->wcService->getPendingCodOrders(['per_page' => 50], $request->has('refresh'));

        // Filtrar si hay búsqueda
        if (!empty($search)) {
            $rawPending = array_filter($rawPending, function ($ord) use ($search) {
                $idMatch = str_contains((string)($ord['id'] ?? ''), $search);
                $name = strtolower(($ord['billing']['first_name'] ?? '') . ' ' . ($ord['billing']['last_name'] ?? ''));
                $nameMatch = str_contains($name, strtolower($search));
                $phoneMatch = str_contains(($ord['billing']['phone'] ?? ''), $search);
                $emailMatch = str_contains(strtolower($ord['billing']['email'] ?? ''), strtolower($search));
                return $idMatch || $nameMatch || $phoneMatch || $emailMatch;
            });
        }

        $pendingOrders = array_values($rawPending);

        // Pedidos ya completados (para historial)
        $completedOrders = [];
        if ($tab === 'completados') {
            $completedOrders = $this->wcService->getOrders(['status' => 'completed', 'per_page' => 20]);
        }

        // Información de la caja física abierta en WavePos
        $cajaAbierta = Caja::with(['sucursal', 'usuario'])
            ->where('estado', 'abierta')
            ->latest()
            ->first();

        // Totales para métricas
        $totalPendienteMonto = 0;
        foreach ($pendingOrders as $ord) {
            $totalPendienteMonto += (float)($ord['total'] ?? 0);
        }

        return view('pages.woocommerce.cobro', compact(
            'pendingOrders',
            'completedOrders',
            'cajaAbierta',
            'totalPendienteMonto',
            'search',
            'tab'
        ));
    }

    /**
     * Procesar el cobro y entrega física de un pedido de WooCommerce.
     */
    public function cobrarPedido(Request $request, int $orderId)
    {
        $request->validate([
            'metodo_pago'    => 'required|string|in:efectivo,tarjeta,transferencia,yappy',
            'monto_recibido' => 'nullable|numeric|min:0',
            'referencia'     => 'nullable|string|max:100',
            'observaciones'  => 'nullable|string|max:500',
        ]);

        $wcOrder = $this->wcService->getOrder($orderId);
        if (!$wcOrder) {
            return redirect()->back()->with('sweet_alert', [
                'type'    => 'error',
                'title'   => 'Pedido No Encontrado',
                'message' => "No se pudo recuperar la información del pedido #{$orderId} desde WooCommerce.",
            ]);
        }

        $total = (float)($wcOrder['total'] ?? 0);
        $metodoPago = $request->input('metodo_pago', 'efectivo');
        $montoRecibido = $request->filled('monto_recibido') ? (float)$request->input('monto_recibido') : $total;
        $cambio = max(0, $montoRecibido - $total);
        $referencia = $request->input('referencia', 'WC-ORD-' . $orderId);
        $observaciones = $request->input('observaciones', '');

        // 1. Actualizar estado en WooCommerce a "completed"
        $updateRes = $this->wcService->updateOrderStatus($orderId, 'completed');
        if (!$updateRes['success']) {
            return redirect()->back()->with('sweet_alert', [
                'type'    => 'error',
                'title'   => 'Error en WooCommerce',
                'message' => 'No se pudo actualizar el estado en WooCommerce: ' . ($updateRes['message'] ?? 'Error desconocido'),
            ]);
        }

        // 2. Agregar nota de auditoría en la orden de WooCommerce
        $userName = auth()->user()->name ?? 'Cajero WavePOS';
        $nota = "Pedido #{$orderId} cobrado y entregado en mostrador WavePOS por $" . number_format($total, 2) . ". Método: " . ucfirst($metodoPago) . ". Atendido por: {$userName}.";
        $this->wcService->addOrderNote($orderId, $nota);

        // 3. Registrar la venta en la caja física de WavePos
        try {
            DB::transaction(function () use ($wcOrder, $orderId, $metodoPago, $total, $montoRecibido, $cambio, $referencia, $observaciones, $userName) {
                $cajaAbierta = Caja::where('estado', 'abierta')->latest()->first();
                $sucursalId = $cajaAbierta ? $cajaAbierta->sucursal_id : (Sucursal::first()->id ?? 1);

                // Resolver o registrar cliente
                $clienteNombre = trim(($wcOrder['billing']['first_name'] ?? '') . ' ' . ($wcOrder['billing']['last_name'] ?? ''));
                if (empty($clienteNombre)) {
                    $clienteNombre = 'Cliente WC #' . $orderId;
                }
                $clienteEmail = $wcOrder['billing']['email'] ?? null;
                $clienteTelefono = $wcOrder['billing']['phone'] ?? null;
                $clienteDireccion = trim(($wcOrder['billing']['address_1'] ?? '') . ' ' . ($wcOrder['billing']['city'] ?? ''));

                $cliente = null;
                if ($clienteEmail) {
                    $cliente = Cliente::where('email', $clienteEmail)->first();
                }
                if (!$cliente) {
                    $cliente = Cliente::firstOrCreate(
                        ['nombre' => $clienteNombre],
                        [
                            'email'     => $clienteEmail,
                            'telefono'  => $clienteTelefono,
                            'direccion' => $clienteDireccion ?: 'Pedido Online',
                            'estado'    => true,
                        ]
                    );
                }

                // Crear o actualizar Venta en WavePos
                $venta = Venta::updateOrCreate(
                    ['woocommerce_order_id' => $orderId],
                    [
                        'numero'               => Venta::generateNumero(),
                        'caja_id'              => $cajaAbierta?->id,
                        'sucursal_id'          => $sucursalId,
                        'cliente_id'           => $cliente->id,
                        'user_id'              => auth()->id(),
                        'subtotal'             => (float)($total - (float)($wcOrder['total_tax'] ?? 0)),
                        'itbms'                => (float)($wcOrder['total_tax'] ?? 0),
                        'total'                => $total,
                        'estado'               => 'completada',
                        'fecha'                => now()->toDateString(),
                        'origen'               => 'woocommerce',
                        'woocommerce_order_id' => $orderId,
                    ]
                );

                // Registrar detalles de venta
                $venta->detalles()->delete();
                if (!empty($wcOrder['line_items'])) {
                    foreach ($wcOrder['line_items'] as $item) {
                        $wcProdId = $item['product_id'] ?? null;
                        $localProd = $wcProdId ? Producto::where('woocommerce_id', $wcProdId)->first() : null;
                        if (!$localProd && !empty($item['sku'])) {
                            $localProd = Producto::where('sku', $item['sku'])->first();
                        }
                        if (!$localProd) {
                            $localProd = Producto::first();
                        }

                        DetalleVenta::create([
                            'venta_id'        => $venta->id,
                            'producto_id'     => $localProd?->id,
                            'cantidad'        => (int)($item['quantity'] ?? 1),
                            'precio_unitario' => (float)($item['price'] ?? 0),
                            'impuesto'        => (float)($item['total_tax'] ?? 0),
                            'subtotal'        => (float)($item['subtotal'] ?? 0),
                            'total'           => (float)($item['total'] ?? 0),
                        ]);
                    }
                }

                // Registrar pago en caja
                $obsPago = "Cobro contra reembolso pedido WC #{$orderId}";
                if ($metodoPago === 'efectivo' && $montoRecibido > $total) {
                    $obsPago .= " (Recibido: $" . number_format($montoRecibido, 2) . ", Cambio: $" . number_format($cambio, 2) . ")";
                }
                if ($observaciones) {
                    $obsPago .= " | " . $observaciones;
                }

                $venta->pagos()->delete();
                PagoVenta::create([
                    'venta_id'      => $venta->id,
                    'metodo'        => $metodoPago,
                    'monto'         => $total,
                    'referencia'    => $referencia,
                    'observaciones' => $obsPago,
                ]);
            });
        } catch (\Throwable $e) {
            \Log::error("Error registrando venta local de WC #{$orderId}: " . $e->getMessage());
        }

        Cache::forget('wc_pending_cod_orders');
        Cache::forget('wc_store_summary');

        $cambioTexto = ($metodoPago === 'efectivo' && $cambio > 0)
            ? ' (Cambio entregado: $' . number_format($cambio, 2) . ')'
            : '';

        return redirect()->route('woocommerce.cobro')->with('sweet_alert', [
            'type'    => 'success',
            'title'   => "¡Pedido #{$orderId} Cobrado y Entregado!",
            'message' => "El pedido cambió a 'Completado' en WooCommerce y el ingreso de $" . number_format($total, 2) . " fue registrado en WavePos.{$cambioTexto}",
        ]);
    }

    /**
     * Probar la conexión con la tienda WooCommerce.
     */
    public function testConnection()
    {
        $result = $this->wcService->testConnection();

        if ($result['success']) {
            return redirect()->route('woocommerce.index')->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Conexión Exitosa!',
                'message' => 'Se conectó correctamente a ' . ($result['store'] ?? 'WooCommerce') . ' (Versión: ' . ($result['version'] ?? 'N/A') . ').',
            ]);
        }

        return redirect()->route('woocommerce.index')->with('sweet_alert', [
            'type'    => 'error',
            'title'   => 'Error de Conexión',
            'message' => $result['message'],
        ]);
    }

    /**
     * Subir un producto individual de WavePos a WooCommerce.
     */
    public function syncProduct(Producto $producto)
    {
        $result = $this->wcService->syncProduct($producto);
        Cache::forget('wc_store_summary');

        if ($result['success']) {
            $actionWord = ($result['action'] ?? '') === 'created' ? 'creado' : 'actualizado';
            return redirect()->back()->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Producto Sincronizado!',
                'message' => "El producto '{$producto->nombre}' fue {$actionWord} en WooCommerce con ID #{$result['woocommerce_id']}.",
            ]);
        }

        return redirect()->back()->with('sweet_alert', [
            'type'    => 'error',
            'title'   => 'Error de Sincronización',
            'message' => $result['message'] ?? 'No se pudo sincronizar el producto.',
        ]);
    }

    /**
     * Desvincular un producto de WooCommerce en WavePos.
     */
    public function unlinkProduct(Producto $producto)
    {
        $this->wcService->unlinkProduct($producto);

        return redirect()->back()->with('sweet_alert', [
            'type'    => 'info',
            'title'   => 'Producto Desvinculado',
            'message' => "El producto '{$producto->nombre}' ya no está vinculado a WooCommerce.",
        ]);
    }

    /**
     * Subir todos los productos activos de WavePos a WooCommerce (Push masivo).
     */
    public function syncAll()
    {
        $result = $this->wcService->syncAllProducts();
        Cache::forget('wc_store_summary');

        if ($result['success']) {
            $msg = "Se procesaron {$result['total']} productos: {$result['created']} creados, {$result['updated']} actualizados.";
            if (!empty($result['errors'])) {
                $msg .= " (" . count($result['errors']) . " errores reportados)";
            }

            return redirect()->route('woocommerce.index')->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Sincronización Completada!',
                'message' => $msg,
            ]);
        }

        return redirect()->route('woocommerce.index')->with('sweet_alert', [
            'type'    => 'error',
            'title'   => 'Error en Sincronización',
            'message' => $result['message'] ?? 'Hubo un error al sincronizar productos.',
        ]);
    }

    /**
     * Traer / importar todos los productos desde WooCommerce hacia WavePos (Pull masivo).
     */
    public function pullAll()
    {
        $result = $this->wcService->importAllProducts();
        Cache::forget('wc_store_summary');

        if ($result['success']) {
            $msg = "Catálogo importado desde WooCommerce: {$result['imported']} productos nuevos creados, {$result['updated']} productos existentes actualizados.";
            if (!empty($result['errors'])) {
                $msg .= " (" . count($result['errors']) . " advertencias)";
            }

            return redirect()->route('woocommerce.index')->with('sweet_alert', [
                'type'    => 'success',
                'title'   => '¡Importación Exitosa!',
                'message' => $msg,
            ]);
        }

        return redirect()->route('woocommerce.index')->with('sweet_alert', [
            'type'    => 'error',
            'title'   => 'Error en Importación',
            'message' => $result['message'] ?? 'No se pudieron descargar los productos de WooCommerce.',
        ]);
    }

    /**
     * Guardar configuración de WooCommerce (credenciales y URL).
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'store_url'       => 'required|url',
            'consumer_key'    => 'required|string',
            'consumer_secret' => 'required|string',
        ]);

        Setting::set('wc_store_url', rtrim($validated['store_url'], '/'));
        Setting::set('wc_consumer_key', trim($validated['consumer_key']));
        Setting::set('wc_consumer_secret', trim($validated['consumer_secret']));

        return redirect()->route('woocommerce.index')->with('sweet_alert', [
            'type'    => 'success',
            'title'   => 'Configuración Guardada',
            'message' => 'Los datos de conexión con WooCommerce se actualizaron con éxito.',
        ]);
    }
}
