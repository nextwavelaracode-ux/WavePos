# Integración WooCommerce y WavePos

Este documento resume la solución implementada para corregir el error SSL en WooCommerce local (Laragon) y la integración bidireccional completa construida entre **WavePos** y **WooCommerce**.

---

## 1. Diagnóstico y Corrección del Error SSL (`ERR_SSL_PROTOCOL_ERROR`)

### Causa Raíz
1. **Entorno local en Laragon sin SSL activo**: El VirtualHost de Apache en Laragon para `tienda.test` escucha exclusivamente en el puerto 80 (HTTP estándar). No tiene certificado SSL configurado en el puerto 443.
2. **Uso de WooCommerce Blocks Checkout y REST API**: Al finalizar una compra en WooCommerce con el bloque moderno de finalizar compra, el navegador envía una petición REST a `/wp-json/wc/store/v1/checkout`.
3. **Simulación SSL global**: Para permitir que las credenciales Basic Auth de la REST API v3 funcionaran por HTTP en local, se había configurado `$_SERVER['HTTPS'] = 'on'`. Esto provocaba que durante el checkout de los clientes, WooCommerce creyera que el sitio operaba sobre HTTPS y devolviera la URL de confirmación de pedido (`order-received`) con prefijo `https://tienda.test/...`. Al redirigir el navegador hacia HTTPS, el servidor fallaba con `ERR_SSL_PROTOCOL_ERROR`.
4. **Permalinks con `/index.php/`**: Las reglas de reescritura de WordPress tenían prefijado `/index.php/`, lo que generaba errores 404 al resolver páginas amigables y endpoints como `/finalizar-compra/order-received/...`.

### Soluciones Aplicadas y Verificadas
1. **Nuevo `mu-plugin` (`allow-local-auth.php`)**:
   - Se modificó para **activar la simulación de autenticación únicamente durante la comprobación de claves REST API** (en el hook `determine_current_user` antes de WooCommerce y limpiándola inmediatamente después).
   - Para las compras de los clientes y el Store API, `$_SERVER['HTTPS']` nunca se activa.
   - Se forzó el filtro `woocommerce_force_ssl_checkout` a `false`.
   - Se añadieron filtros para interceptar cualquier URL generada (`woocommerce_get_checkout_url`, `woocommerce_get_checkout_order_received_url`, `woocommerce_get_endpoint_url`, `wp_redirect` y respuestas de Store API) garantizando que siempre se devuelva `http://tienda.test`.
2. **Ajuste en `wp-config.php`**:
   - Se fijaron `WP_SITEURL` y `WP_HOME` explícitamente a `'http://tienda.test'` (eliminando lecturas variables de `$_SERVER['REQUEST_SCHEME']` que provocaban inconsistencias en CLI y Apache).
   - Se definieron `FORCE_SSL_ADMIN = false` y `FORCE_SSL_LOGIN = false`.
3. **Estructura de Enlaces Permanentes**:
   - Se actualizó la estructura de permalinks a `/%postname%/` y se regeneraron las reglas de reescritura incluyendo los endpoints de WooCommerce (`order-received`, `order-pay`).
   - Comprobación: Se probó una orden de compra completa y la URL `http://tienda.test/finalizar-compra/order-received/19/?key=...` respondió con **HTTP 200 OK**.

---

## 2. Integración Bidireccional de Productos en WavePos

Se integró una solución completa para sincronizar productos y ver datos de la tienda WooCommerce directamente desde WavePos:

### Funcionalidades Implementadas
1. **Subir Productos hacia WooCommerce (Push)**:
   - **Individual**: Botón en la tabla para subir o actualizar cualquier producto de WavePos en WooCommerce.
   - **Masivo**: Botón **"Subir Todo a WooCommerce"** para enviar y sincronizar todos los productos activos de WavePos a la tienda online.
2. **Traer Productos desde WooCommerce hacia WavePos (Pull)**:
   - Botón **"Traer desde WooCommerce"**: Descarga el catálogo online y, si el producto no existe en WavePos, lo crea automáticamente con su categoría, precio, stock, SKU y código de barras. Si ya existe, actualiza su stock y precio.
3. **Módulo de Gestión y Estadísticas en WavePos (`/woocommerce`)**:
   - **Tarjetas de Estado (KPIs)**:
     - Estado de la conexión con WooCommerce (Conectado / Desconectado, versión, botón de prueba).
     - Total de productos vinculados en WavePos vs total de la tienda.
     - Total de pedidos online registrados en WooCommerce con montos acumulados.
   - **Pestaña de Productos**:
     - Listado con imagen, SKU, precio, stock local, ID de WooCommerce y fecha de última sincronización.
     - Filtros por: *Todos*, *Sincronizados* y *Pendientes*.
     - Buscador por nombre, SKU o código de barra.
     - Acciones de sincronización y desvinculación individual.
   - **Pestaña de Pedidos Online**:
     - Tabla en vivo con los pedidos generados en WooCommerce (número de orden, cliente, correo, pasarela de pago, estado y total).
   - **Pestaña de Configuración**:
     - Formulario intuitivo para actualizar la URL de la tienda, *Consumer Key* y *Consumer Secret* en cualquier momento.

---

## 3. Rutas y Menú en WavePos

- **Ruta de Acceso**: `http://localhost:8000/woocommerce`
- **Menú Lateral**: Se añadió el acceso directo **WooCommerce** en la barra lateral de navegación con el badge `Online`.
- **Rutas creadas**:
  - `GET /woocommerce`: Dashboard principal de la integración.
  - `GET /woocommerce/test-connection`: Prueba de conectividad con la tienda.
  - `POST /woocommerce/sync-all`: Sincronización masiva de WavePos a WooCommerce.
  - `POST /woocommerce/pull-all`: Importación masiva de WooCommerce a WavePos.
  - `POST /woocommerce/sync-product/{producto}`: Sincronización de un producto específico.
  - `POST /woocommerce/unlink-product/{producto}`: Desvinculación de un producto.
  - `POST /woocommerce/settings`: Guardado de credenciales API.

---

## 4. Estado de las Pruebas

| Prueba | Resultado | Detalle |
|---|---|---|
| Compra en WooCommerce (`tienda.test`) | **FUNCIONÓ (OK)** | No más error SSL. La página de agradecimiento responde con HTTP 200 en `http://tienda.test/finalizar-compra/order-received/...`. |
| REST API WooCommerce con WavePos | **FUNCIONÓ (OK)** | Conexión probada con claves `ck_...` y `cs_...`. Código HTTP 200 en `/wp-json/wc/v3/`. |
| Subida individual y masiva (Push) | **FUNCIONÓ (OK)** | Los productos se crean/actualizan en WooCommerce con SKU y stock sincronizado. |
| Descarga e importación (Pull) | **FUNCIONÓ (OK)** | Los productos existentes en WooCommerce se importan a WavePos creando categorías y mapeando stock y precios. |
| Visualización de Pedidos y KPIs | **FUNCIONÓ (OK)** | Las órdenes y estadísticas de ventas de WooCommerce se visualizan en `/woocommerce`. |
| Cobro Contra Reembolso (Pick & Pay) | **FUNCIONÓ (OK)** | Cambia la orden en WooCommerce a 'completed', añade nota de auditoría y registra la venta en la caja física de WavePOS. |

---

## 5. Minimódulo de Cobro y Entrega Contra Reembolso (`/woocommerce/cobro`)

### Flujo de Trabajo en Mostrador:
1. El cliente compra en la tienda online `tienda.test` con método **Contra reembolso** (su pedido queda en estado `processing`).
2. El cliente llega al establecimiento a retirar y pagar su pedido.
3. El cajero ingresa a:
   - **Caja & Ventas > Cobro Contra Reembolso (WC)**, o
   - **WooCommerce > Cobro Contra Reembolso**, o
   - Clic en el botón **"Cobrar Retiros"** desde el dashboard de WooCommerce.
4. El cajero busca el pedido por número `#`, nombre del cliente o teléfono.
5. Presiona **"Cobrar y Entregar"**:
   - Selecciona el método de pago recibido (*Efectivo*, *Tarjeta*, *Transferencia*, *Yappy/Nequi*).
   - En efectivo, ingresa el monto entregado por el cliente y el sistema calcula automáticamente el **cambio / vuelto a devolver**.
6. Al presionar **"Confirmar Cobro y Completar"**:
   - **En WooCommerce**: El pedido pasa inmediatamente de `processing` a **`completed`** y se adjunta una nota de auditoría interna con el método de pago y el usuario que atendió.
   - **En WavePos**: Se registra la `Venta` en la caja física abierta con sus `DetalleVenta` y `PagoVenta`, garantizando que el dinero cuadre en el turno de caja.

