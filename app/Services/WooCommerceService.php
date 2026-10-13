<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WooCommerceService
{
    protected string $storeUrl;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected int $timeout;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->storeUrl = rtrim(Setting::get('wc_store_url') ?: config('woocommerce.store_url', 'http://tienda.test'), '/');
        $this->consumerKey = Setting::get('wc_consumer_key') ?: config('woocommerce.consumer_key', '');
        $this->consumerSecret = Setting::get('wc_consumer_secret') ?: config('woocommerce.consumer_secret', '');
        $this->timeout = 8;
        $this->verifySsl = (bool) config('woocommerce.verify_ssl', false);
    }

    public function getStoreUrl(): string
    {
        return $this->storeUrl;
    }

    /**
     * Check if credentials are present.
     */
    public function isConfigured(): bool
    {
        return !empty($this->storeUrl) && !empty($this->consumerKey) && !empty($this->consumerSecret);
    }

    /**
     * Base HTTP request builder with IPv4 enforcement for fast local/remote resolution.
     */
    protected function request(?int $timeout = null)
    {
        return Http::timeout($timeout ?? $this->timeout)
            ->withoutVerifying()
            ->withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ])
            ->withHeaders([
                'Accept' => 'application/json',
            ]);
    }

    /**
     * Cleanly parse JSON from response, stripping BOM if present.
     */
    protected function parseResponse($response): ?array
    {
        $body = $response->body();
        $body = preg_replace('/^\xEF\xBB\xBF/', '', $body);
        return json_decode($body, true);
    }

    /**
     * Build endpoint URL with consumer query parameters for seamless local/remote support.
     */
    protected function buildUrl(string $endpoint, array $queryParams = []): string
    {
        $endpoint = ltrim($endpoint, '/');
        $url = "{$this->storeUrl}/wp-json/wc/v3/{$endpoint}";

        $authParams = [
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
        ];

        $params = array_merge($authParams, $queryParams);
        return $url . '?' . http_build_query($params);
    }

    /**
     * Extraer mensaje de error limpio y legible, sin tags HTML ni cadenas crudas.
     */
    protected function extractErrorMessage($response, string $prefix = ''): string
    {
        if (!$response) {
            return $prefix ? rtrim($prefix, ': ') . ': Error desconocido.' : 'Error desconocido de WooCommerce.';
        }

        $parsed = $this->parseResponse($response);
        $message = null;

        if (is_array($parsed)) {
            if (!empty($parsed['message']) && is_string($parsed['message'])) {
                $message = $parsed['message'];
            } elseif (!empty($parsed['error']) && is_string($parsed['error'])) {
                $message = $parsed['error'];
            }
        }

        if (!$message) {
            $message = $response->body();
        }

        // Quitar etiquetas HTML (<p>, <a>, etc.) y decodificar entidades
        $clean = strip_tags(html_entity_decode((string) $message, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $clean = trim(preg_replace('/\s+/', ' ', $clean));

        if (empty($clean)) {
            $clean = "HTTP " . $response->status() . " - Error en el servidor de WooCommerce.";
        }

        $clean = Str::limit($clean, 220);

        return $prefix ? rtrim($prefix, ': ') . ': ' . $clean : $clean;
    }

    /**
     * Sanitizar mensajes de excepción evitando mostrar URLs internas, claves de API o errores cURL crudos.
     */
    protected function sanitizeExceptionMessage(\Throwable $e, string $prefix = ''): string
    {
        $raw = $e->getMessage();

        if (str_contains($raw, 'timed out') || str_contains($raw, 'cURL error 28')) {
            $msg = 'El servidor de WooCommerce tardó demasiado en responder (tiempo de espera agotado).';
        } elseif (str_contains($raw, 'Failed to connect') || str_contains($raw, 'Connection refused')) {
            $msg = 'No se pudo conectar con la tienda WooCommerce (conexión rechazada o no disponible).';
        } elseif (str_contains($raw, 'Could not resolve host')) {
            $msg = 'No se pudo resolver el dominio de la tienda WooCommerce.';
        } else {
            // Ocultar cualquier parámetro que exponga consumer_key o consumer_secret
            $sanitized = preg_replace('/(consumer_key|consumer_secret)=[^&\s]+/i', '$1=***', $raw);
            $clean = strip_tags(html_entity_decode($sanitized, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $clean = trim(preg_replace('/\s+/', ' ', $clean));
            $msg = Str::limit($clean, 200) ?: 'Error de comunicación inesperado.';
        }

        return $prefix ? rtrim($prefix, ': ') . ': ' . $msg : $msg;
    }

    /**
     * Test connection to WooCommerce.
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Faltan credenciales de WooCommerce configuradas.',
            ];
        }

        try {
            $url = $this->buildUrl('system_status');
            $response = $this->request(10)->get($url);

            if ($response->successful()) {
                $data = $this->parseResponse($response);
                return [
                    'success' => true,
                    'message' => 'Conexión exitosa con WooCommerce.',
                    'version' => $data['environment']['version'] ?? 'N/A',
                    'store'   => $data['environment']['site_url'] ?? $this->storeUrl,
                ];
            }

            // Fallback test via products list
            $fallbackUrl = $this->buildUrl('products', ['per_page' => 1]);
            $fallbackRes = $this->request(10)->get($fallbackUrl);

            if ($fallbackRes->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexión exitosa con WooCommerce (vía catálogo).',
                    'store'   => $this->storeUrl,
                ];
            }

            return [
                'success' => false,
                'message' => $this->extractErrorMessage($response, 'Error al conectar con WooCommerce'),
            ];
        } catch (\Throwable $e) {
            Log::error('WooCommerce testConnection error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $this->sanitizeExceptionMessage($e, 'Excepción de conexión'),
            ];
        }
    }

    /**
     * Synchronize a single WavePos product to WooCommerce.
     */
    public function syncProduct(Producto $producto): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'WooCommerce no está configurado.'];
        }

        $sku = $producto->sku ?: ($producto->codigo_barras ?: 'WAVE-' . str_pad($producto->id, 5, '0', STR_PAD_LEFT));

        $payload = [
            'name'           => $producto->nombre,
            'type'           => 'simple',
            'regular_price'  => (string) $producto->precio_venta,
            'description'    => (string) ($producto->descripcion ?? ''),
            'sku'            => $sku,
            'manage_stock'   => true,
            'stock_quantity' => (int) $producto->stock,
            'status'         => $producto->estado ? 'publish' : 'draft',
        ];

        // Si el producto tiene categoría en WavePos, intentar vincular o crear en WC
        if ($producto->categoria) {
            $wcCatId = $this->findOrCreateCategory($producto->categoria->nombre);
            if ($wcCatId) {
                $payload['categories'] = [['id' => $wcCatId]];
            }
        }

        // Si el producto tiene imagen en WavePos, enviarla a WooCommerce para que se suba a la biblioteca de medios
        $imageUrl = $this->getProductPublicImageUrl($producto);
        if ($imageUrl) {
            $payload['images'] = [
                ['src' => $imageUrl]
            ];
        }

        // Si no tiene woocommerce_id pero tiene sku, buscamos si ya existe en la tienda
        if (!$producto->woocommerce_id && $sku) {
            $existing = $this->findProductBySku($sku);
            if ($existing && isset($existing['id'])) {
                $producto->updateQuietly(['woocommerce_id' => $existing['id']]);
            }
        }

        try {
            // Si ya existe woocommerce_id, intentamos actualizar (tiempo de espera de 35s para permitir procesamiento de medios)
            if ($producto->woocommerce_id) {
                $url = $this->buildUrl("products/{$producto->woocommerce_id}");
                $response = $this->request(35)->put($url, $payload);

                if ($response->successful()) {
                    $wcData = $this->parseResponse($response);
                    $producto->updateQuietly([
                        'ultima_sincronizacion_wc' => now(),
                        'sincronizar_woocommerce'  => true,
                    ]);

                    return [
                        'success'        => true,
                        'action'         => 'updated',
                        'woocommerce_id' => $wcData['id'] ?? $producto->woocommerce_id,
                        'data'           => $wcData,
                    ];
                }

                // Si fue 404 en WC (se borró en WordPress), permitimos recrearlo abajo
                if ($response->status() !== 404) {
                    return [
                        'success' => false,
                        'message' => $this->extractErrorMessage($response, 'Error al actualizar en WooCommerce'),
                    ];
                }
            }

            // Crear nuevo producto en WooCommerce (tiempo de espera de 35s para descarga y generación de miniaturas)
            $url = $this->buildUrl('products');
            $response = $this->request(35)->post($url, $payload);

            if ($response->successful()) {
                $wcData = $this->parseResponse($response);
                $wcId = $wcData['id'] ?? null;
                $producto->updateQuietly([
                    'woocommerce_id'           => $wcId,
                    'sincronizar_woocommerce'  => true,
                    'ultima_sincronizacion_wc' => now(),
                ]);

                return [
                    'success'        => true,
                    'action'         => 'created',
                    'woocommerce_id' => $wcId,
                    'data'           => $wcData,
                ];
            }

            $errData = $this->parseResponse($response);
            if (isset($errData['data']['resource_id'])) {
                $resourceId = $errData['data']['resource_id'];
                $producto->updateQuietly(['woocommerce_id' => $resourceId]);
                return $this->syncProduct($producto);
            }

            return [
                'success' => false,
                'message' => $this->extractErrorMessage($response, 'Error al crear en WooCommerce'),
            ];
        } catch (\Throwable $e) {
            Log::error("WooCommerce syncProduct error for Producto #{$producto->id}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $this->sanitizeExceptionMessage($e, 'Error al sincronizar producto'),
            ];
        }
    }

    /**
     * Push all active products from WavePos to WooCommerce.
     */
    public function syncAllProducts(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'WooCommerce no está configurado.'];
        }

        $productos = Producto::where('estado', true)->get();
        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($productos as $producto) {
            $res = $this->syncProduct($producto);
            if (!empty($res['success'])) {
                if (($res['action'] ?? '') === 'created') {
                    $created++;
                } else {
                    $updated++;
                }
            } else {
                $errors[] = "Producto #{$producto->id} ({$producto->nombre}): " . ($res['message'] ?? 'Error desconocido');
            }
        }

        return [
            'success' => true,
            'total'   => $productos->count(),
            'created' => $created,
            'updated' => $updated,
            'errors'  => $errors,
        ];
    }

    /**
     * Find a category in WooCommerce by name or create it.
     */
    public function findOrCreateCategory(string $name): ?int
    {
        try {
            $url = $this->buildUrl('products/categories', ['search' => $name]);
            $res = $this->request()->get($url);
            if ($res->successful()) {
                $cats = $this->parseResponse($res);
                if (!empty($cats) && is_array($cats)) {
                    foreach ($cats as $cat) {
                        if (strcasecmp($cat['name'], $name) === 0) {
                            return (int) $cat['id'];
                        }
                    }
                }
            }

            // Crear categoria en WC si no existe
            $createUrl = $this->buildUrl('products/categories');
            $createRes = $this->request()->post($createUrl, ['name' => $name]);
            if ($createRes->successful()) {
                $created = $this->parseResponse($createRes);
                return (int) ($created['id'] ?? null);
            }
        } catch (\Throwable $e) {
            Log::error("WooCommerce findOrCreateCategory error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch products from WooCommerce.
     */
    public function getProducts(array $params = []): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        $defaultParams = ['per_page' => 50, 'page' => 1];
        $params = array_merge($defaultParams, $params);

        try {
            $url = $this->buildUrl('products', $params);
            $response = $this->request()->get($url);

            if ($response->successful()) {
                return $this->parseResponse($response) ?? [];
            }
        } catch (\Throwable $e) {
            Log::error("WooCommerce getProducts error: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Import or update a single product from WooCommerce into WavePos.
     */
    public function importProductFromWooCommerce(array $wcProduct): array
    {
        $wcId = $wcProduct['id'] ?? null;
        if (!$wcId) {
            return ['success' => false, 'message' => 'El producto de WooCommerce no tiene ID.'];
        }

        $sku = trim($wcProduct['sku'] ?? '');
        if (empty($sku)) {
            $sku = 'WC-' . str_pad($wcId, 5, '0', STR_PAD_LEFT);
        }

        // Buscar producto existente por woocommerce_id o por sku
        $producto = Producto::where('woocommerce_id', $wcId)->first();
        if (!$producto && $sku) {
            $producto = Producto::where('sku', $sku)->orWhere('codigo_barras', $sku)->first();
        }

        // Determinar categoría
        $categoriaId = null;
        if (!empty($wcProduct['categories']) && is_array($wcProduct['categories'])) {
            $catName = trim($wcProduct['categories'][0]['name'] ?? '');
            if ($catName) {
                $cat = Categoria::firstOrCreate(
                    ['nombre' => $catName],
                    ['estado' => true, 'impuesto' => '7']
                );
                $categoriaId = $cat->id;
            }
        }

        if (!$categoriaId) {
            $defaultCat = Categoria::firstOrCreate(
                ['nombre' => 'General'],
                ['estado' => true, 'impuesto' => '7']
            );
            $categoriaId = $defaultCat->id;
        }

        $regularPrice = !empty($wcProduct['regular_price']) ? (float)$wcProduct['regular_price'] : (!empty($wcProduct['price']) ? (float)$wcProduct['price'] : 0.0);
        $stock = !empty($wcProduct['manage_stock']) ? (int)($wcProduct['stock_quantity'] ?? 0) : 10;
        $description = strip_tags($wcProduct['description'] ?? ($wcProduct['short_description'] ?? ''));

        $data = [
            'nombre'                   => $wcProduct['name'] ?? 'Producto WooCommerce #' . $wcId,
            'descripcion'              => $description ?: null,
            'categoria_id'             => $categoriaId,
            'sku'                      => $sku,
            'codigo_barras'            => $sku,
            'precio_venta'             => $regularPrice,
            'precio_minimo'            => $regularPrice,
            'stock'                    => $stock,
            'estado'                   => ($wcProduct['status'] ?? 'publish') === 'publish',
            'woocommerce_id'           => $wcId,
            'sincronizar_woocommerce'  => true,
            'ultima_sincronizacion_wc' => now(),
            'impuesto'                 => '7',
            'unidad_medida'            => 'unidad',
        ];

        if (!empty($wcProduct['images'][0]['src'])) {
            $data['imagen'] = $wcProduct['images'][0]['src'];
        }

        try {
            if ($producto) {
                // Actualizar existente
                $producto->updateQuietly($data);
                return [
                    'success'  => true,
                    'action'   => 'updated',
                    'producto' => $producto,
                ];
            } else {
                // Calcular precio de compra estimado si es nuevo
                $data['precio_compra'] = round($regularPrice * 0.7, 2);
                $data['margen'] = $data['precio_compra'] > 0 ? round((($regularPrice - $data['precio_compra']) / $data['precio_compra']) * 100, 2) : 0;
                $data['stock_minimo'] = 2;
                $data['tasa_impuesto'] = 19.00;
                $data['is_excluded'] = false;
                $data['unidad_medida_dian_id'] = 70;

                $newProd = Producto::create($data);
                return [
                    'success'  => true,
                    'action'   => 'created',
                    'producto' => $newProd,
                ];
            }
        } catch (\Throwable $e) {
            Log::error("WooCommerce importProduct error for WC #{$wcId}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Pull all products from WooCommerce and sync/create them in WavePos.
     */
    public function importAllProducts(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'WooCommerce no está configurado.'];
        }

        $page = 1;
        $imported = 0;
        $updated = 0;
        $errors = [];

        do {
            $wcProducts = $this->getProducts(['per_page' => 100, 'page' => $page]);
            if (empty($wcProducts)) {
                break;
            }

            foreach ($wcProducts as $wcProd) {
                $res = $this->importProductFromWooCommerce($wcProd);
                if (!empty($res['success'])) {
                    if (($res['action'] ?? '') === 'created') {
                        $imported++;
                    } else {
                        $updated++;
                    }
                } else {
                    $errors[] = "WC #{$wcProd['id']}: " . ($res['message'] ?? 'Error');
                }
            }

            $page++;
        } while (count($wcProducts) === 100);

        return [
            'success'  => true,
            'imported' => $imported,
            'updated'  => $updated,
            'errors'   => $errors,
        ];
    }

    /**
     * Find a product in WooCommerce by its SKU.
     */
    public function findProductBySku(string $sku): ?array
    {
        try {
            $url = $this->buildUrl('products', ['sku' => $sku]);
            $response = $this->request()->get($url);
            if ($response->successful()) {
                $products = $this->parseResponse($response);
                if (!empty($products) && is_array($products)) {
                    return $products[0];
                }
            }
        } catch (\Throwable $e) {
            Log::error("WooCommerce findProductBySku error for SKU {$sku}: " . $e->getMessage());
        }
        return null;
    }

    /**
     * Fast stock update for a product.
     */
    public function updateStock(Producto $producto, int $newStock): array
    {
        if (!$this->isConfigured() || !$producto->woocommerce_id) {
            return ['success' => false, 'message' => 'Producto no vinculado con WooCommerce.'];
        }

        try {
            $url = $this->buildUrl("products/{$producto->woocommerce_id}");
            $response = $this->request()->put($url, [
                'stock_quantity' => $newStock,
                'manage_stock'   => true,
            ]);

            if ($response->successful()) {
                $producto->updateQuietly(['ultima_sincronizacion_wc' => now()]);
                return ['success' => true, 'stock' => $newStock];
            }

            return ['success' => false, 'message' => $this->extractErrorMessage($response, 'Error al actualizar stock')];
        } catch (\Throwable $e) {
            Log::error("WooCommerce updateStock error for Producto #{$producto->id}: " . $e->getMessage());
            return ['success' => false, 'message' => $this->sanitizeExceptionMessage($e, 'Error al actualizar stock')];
        }
    }

    /**
     * Unlink product from WooCommerce without deleting it in WooCommerce.
     */
    public function unlinkProduct(Producto $producto): bool
    {
        $producto->updateQuietly([
            'woocommerce_id'          => null,
            'sincronizar_woocommerce' => false,
        ]);
        return true;
    }

    /**
     * Delete product from WooCommerce.
     */
    public function deleteProduct(Producto $producto, bool $force = true): bool
    {
        if (!$this->isConfigured() || !$producto->woocommerce_id) {
            return false;
        }

        try {
            $url = $this->buildUrl("products/{$producto->woocommerce_id}", ['force' => $force ? 'true' : 'false']);
            $res = $this->request()->delete($url);
            if ($res->successful()) {
                $producto->updateQuietly(['woocommerce_id' => null]);
                return true;
            }
        } catch (\Throwable $e) {
            Log::error("WooCommerce deleteProduct error: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Fetch orders from WooCommerce.
     */
    public function getOrders(array $params = []): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        $defaultParams = ['per_page' => 20, 'page' => 1];
        $params = array_merge($defaultParams, $params);

        try {
            $url = $this->buildUrl('orders', $params);
            $response = $this->request()->get($url);

            if ($response->successful()) {
                return $this->parseResponse($response) ?? [];
            }
        } catch (\Throwable $e) {
            Log::error("WooCommerce getOrders error: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Fetch a single order by ID.
     */
    public function getOrder(int $orderId): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $url = $this->buildUrl("orders/{$orderId}");
            $response = $this->request()->get($url);

            if ($response->successful()) {
                return $this->parseResponse($response);
            }
        } catch (\Throwable $e) {
            Log::error("WooCommerce getOrder #{$orderId} error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Get store statistics and summary with caching for lightning-fast page loads.
     */
    public function getStoreSummary(bool $forceRefresh = false): array
    {
        $cacheKey = 'wc_store_summary';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 60, function () {
            $localLinkedCount = Producto::whereNotNull('woocommerce_id')->count();
            $localTotalCount = Producto::count();

            if (!$this->isConfigured()) {
                return [
                    'connection'        => ['success' => false, 'message' => 'WooCommerce no está configurado.'],
                    'wc_products_count' => 0,
                    'orders'            => [],
                    'orders_count'      => 0,
                    'total_sales'       => 0,
                    'status_counts'     => [],
                    'local_linked'      => $localLinkedCount,
                    'local_total'       => $localTotalCount,
                ];
            }

            // Obtenemos los últimos pedidos (máx 15 para carga ultra rápida)
            $orders = $this->getOrders(['per_page' => 15]);
            $isConnected = !empty($orders) || $this->checkFastConnection();

            // Total de productos en WC sin descargar catálogo completo
            $wcProductsCount = $this->getProductsCount();

            // Calcular métricas
            $totalOrdersCount = count($orders);
            $totalSales = 0;
            $statusCounts = [];

            foreach ($orders as $ord) {
                $totalSales += (float)($ord['total'] ?? 0);
                $status = $ord['status'] ?? 'pending';
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
            }

            return [
                'connection'        => [
                    'success' => $isConnected,
                    'message' => $isConnected ? 'Conexión activa con WooCommerce.' : 'Sin conexión.',
                    'version' => 'WC',
                    'store'   => $this->storeUrl,
                ],
                'wc_products_count' => $wcProductsCount,
                'orders'            => $orders,
                'orders_count'      => $totalOrdersCount,
                'total_sales'       => $totalSales,
                'status_counts'     => $statusCounts,
                'local_linked'      => $localLinkedCount,
                'local_total'       => $localTotalCount,
            ];
        });
    }

    /**
     * Fast check if store responds.
     */
    public function checkFastConnection(): bool
    {
        try {
            $url = $this->buildUrl('products', ['per_page' => 1]);
            $res = $this->request()->get($url);
            return $res->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get total WooCommerce products count using headers or count.
     */
    public function getProductsCount(): int
    {
        try {
            $url = $this->buildUrl('products', ['per_page' => 1]);
            $res = $this->request()->get($url);
            if ($res->successful()) {
                $totalHeader = $res->header('X-WP-Total');
                if ($totalHeader !== null) {
                    return (int) $totalHeader;
                }
                $data = $this->parseResponse($res);
                return count($data ?? []);
            }
        } catch (\Throwable $e) {
            Log::error("WooCommerce getProductsCount error: " . $e->getMessage());
        }
        return 0;
    }

    /**
     * Update order in WooCommerce.
     */
    public function updateOrder(int $orderId, array $data): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'WooCommerce no está configurado.'];
        }

        try {
            $url = $this->buildUrl("orders/{$orderId}");
            $response = $this->request()->put($url, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $this->parseResponse($response),
                ];
            }

            return [
                'success' => false,
                'message' => $this->extractErrorMessage($response, 'Error al actualizar pedido en WooCommerce'),
            ];
        } catch (\Throwable $e) {
            Log::error("WooCommerce updateOrder #{$orderId} error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $this->sanitizeExceptionMessage($e, 'Error al actualizar pedido'),
            ];
        }
    }

    /**
     * Update order status (e.g. from 'processing' to 'completed').
     */
    public function updateOrderStatus(int $orderId, string $status = 'completed'): array
    {
        return $this->updateOrder($orderId, ['status' => $status]);
    }

    /**
     * Add a note to a WooCommerce order.
     */
    public function addOrderNote(int $orderId, string $note, bool $customerNote = false): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $url = $this->buildUrl("orders/{$orderId}/notes");
            $response = $this->request()->post($url, [
                'note'          => $note,
                'customer_note' => $customerNote,
            ]);

            if ($response->successful()) {
                return $this->parseResponse($response);
            }
        } catch (\Throwable $e) {
            Log::error("WooCommerce addOrderNote #{$orderId} error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch pending pickup / cash on delivery orders with caching.
     */
    public function getPendingCodOrders(array $params = [], bool $forceRefresh = false): array
    {
        $cacheKey = 'wc_pending_cod_orders';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 30, function () use ($params) {
            $defaultParams = [
                'status'   => 'processing,on-hold,pending',
                'per_page' => 50,
                'orderby'  => 'date',
                'order'    => 'desc',
            ];
            $merged = array_merge($defaultParams, $params);
            return $this->getOrders($merged);
        });
    }

    /**
     * Get a public URL for the product image that WooCommerce can access.
     */
    public function getProductPublicImageUrl(Producto $producto): ?string
    {
        if (!$producto->imagen) {
            return null;
        }

        // Si ya es una URL completa (http/https):
        if (str_starts_with($producto->imagen, 'http://') || str_starts_with($producto->imagen, 'https://')) {
            return $producto->imagen;
        }

        $path = 'storage/' . ltrim($producto->imagen, '/');

        // Si estamos en el contexto de una petición web real (no CLI)
        if (!app()->runningInConsole() && app()->bound('request') && request()->hasHeader('host')) {
            $root = request()->root();
            // Si el root es localhost sin puerto pero el servidor corre en 8000
            if (in_array($root, ['http://localhost', 'http://127.0.0.1'])) {
                $root .= ':8000';
            }
            return rtrim($root, '/') . '/' . $path;
        }

        // Contexto CLI / background: asegurar puerto 8000 si está en local
        $appUrl = config('app.url', 'http://127.0.0.1:8000');
        if (in_array($appUrl, ['http://localhost', 'http://127.0.0.1'])) {
            $appUrl = 'http://127.0.0.1:8000';
        }

        return rtrim($appUrl, '/') . '/' . $path;
    }
}


