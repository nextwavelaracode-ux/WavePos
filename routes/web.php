<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RolController;
// use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\VentaEsperaController;
use App\Http\Controllers\CuentaPorPagarController;
use App\Http\Controllers\CuentaPorCobrarController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\CategoriaGastoController;
use App\Http\Controllers\ConfiguracionController;
use Illuminate\Support\Facades\Route;

// authentication pages
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::get('/signin', function () {
    return redirect()->route('login');
})->name('signin')->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup')->middleware('guest');


// TODAS LAS RUTAS PROTEGIDAS DEL SISTEMA POS
Route::middleware(['auth'])->group(function () {

    // dashboard pages
    Route::get('/', function () {
        return view('pages.dashboard.ecommerce', ['title' => 'E-commerce Dashboard']);
    })->name('dashboard');


    // ============================================================
    // POS - CONFIGURACIÓN
    // ============================================================
    Route::group(['prefix' => 'configuracion', 'as' => 'configuracion.'], function () {

        // Empresa (singleton - show + update)
        Route::get('/empresa', [EmpresaController::class, 'show'])->name('empresa')->middleware('permission:empresa.ver');
        Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update')->middleware('permission:empresa.editar');

        // Sucursales (CRUD)
        Route::get('/sucursales', [SucursalController::class, 'index'])->name('sucursales')->middleware('permission:sucursales.ver');
        Route::post('/sucursales', [SucursalController::class, 'store'])->name('sucursales.store')->middleware('permission:sucursales.crear');
        Route::put('/sucursales/{sucursal}', [SucursalController::class, 'update'])->name('sucursales.update')->middleware('permission:sucursales.editar');
        Route::delete('/sucursales/{sucursal}', [SucursalController::class, 'destroy'])->name('sucursales.destroy')->middleware('permission:sucursales.editar'); // sucursales.editar instead of eliminar, no eliminar permission requested

        // Usuarios (CRUD)
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios')->middleware('permission:usuarios.ver');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store')->middleware('permission:usuarios.crear');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update')->middleware('permission:usuarios.editar');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy')->middleware('permission:usuarios.eliminar');

        // Roles y Permisos (CRUD)
        Route::get('/roles', [RolController::class, 'index'])->name('roles')->middleware('permission:roles.ver');
        Route::post('/roles', [RolController::class, 'store'])->name('roles.store')->middleware('permission:roles.crear');
        Route::put('/roles/{rol}', [RolController::class, 'update'])->name('roles.update')->middleware('permission:roles.editar');
        Route::delete('/roles/{rol}', [RolController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.editar');

        // Configuración del Sistema (Settings)
        Route::get('/sistema', [ConfiguracionController::class, 'index'])->name('sistema');
        Route::post('/sistema', [ConfiguracionController::class, 'update'])->name('sistema.update');
    });

    // ============================================================
    // POS - INVENTARIO
    // ============================================================
    Route::prefix('inventario')->name('inventario.')->group(function () {

        // Categorias (CRUD)
        Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias')->middleware('permission:categorias.ver');
        Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store')->middleware('permission:categorias.crear');
        Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update')->middleware('permission:categorias.editar');
        Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy')->middleware('permission:categorias.eliminar');
        Route::post('/categorias/bulk-delete', [CategoriaController::class, 'bulkDestroy'])->name('categorias.bulk_destroy')->middleware('permission:categorias.eliminar');
        Route::get('/categorias/exportar/{formato}', [CategoriaController::class, 'exportar'])->name('categorias.exportar')->middleware('permission:categorias.ver');
        Route::post('/categorias/importar', [CategoriaController::class, 'importar'])->name('categorias.importar')->middleware('permission:categorias.crear');
        Route::get('/categorias/plantilla', [CategoriaController::class, 'plantilla'])->name('categorias.plantilla')->middleware('permission:categorias.ver');

        // Subcategorias (CRUD) - controlador no existe
        // Route::get('/subcategorias', [SubcategoriaController::class, 'index'])->name('subcategorias')->middleware('permission:categorias.ver');
        // Route::post('/subcategorias', [SubcategoriaController::class, 'store'])->name('subcategorias.store')->middleware('permission:categorias.crear');
        // Route::put('/subcategorias/{subcategoria}', [SubcategoriaController::class, 'update'])->name('subcategorias.update')->middleware('permission:categorias.editar');
        // Route::delete('/subcategorias/{subcategoria}', [SubcategoriaController::class, 'destroy'])->name('subcategorias.destroy')->middleware('permission:categorias.eliminar');

        // Proveedores (CRUD)
        Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores')->middleware('permission:proveedores.ver');
        Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store')->middleware('permission:proveedores.crear');
        Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update')->middleware('permission:proveedores.editar');
        Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy')->middleware('permission:proveedores.eliminar');
        Route::post('/proveedores/bulk-delete', [ProveedorController::class, 'bulkDestroy'])->name('proveedores.bulk_destroy')->middleware('permission:proveedores.eliminar');
        Route::get('/proveedores/exportar/{formato}', [ProveedorController::class, 'exportar'])->name('proveedores.exportar')->middleware('permission:proveedores.ver');
        Route::post('/proveedores/importar', [ProveedorController::class, 'importar'])->name('proveedores.importar')->middleware('permission:proveedores.crear');
        Route::get('/proveedores/plantilla', [ProveedorController::class, 'plantilla'])->name('proveedores.plantilla')->middleware('permission:proveedores.ver');

        // Productos (CRUD)
        Route::get('/productos', [ProductoController::class, 'index'])->name('productos')->middleware('permission:productos.ver');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store')->middleware('permission:productos.crear');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update')->middleware('permission:productos.editar');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy')->middleware('permission:productos.eliminar');
        Route::post('/productos/bulk-delete', [ProductoController::class, 'bulkDestroy'])->name('productos.bulk_destroy')->middleware('permission:productos.eliminar');
        Route::get('/productos/exportar/{formato}', [ProductoController::class, 'exportar'])->name('productos.exportar')->middleware('permission:productos.ver');
        Route::post('/productos/importar', [ProductoController::class, 'importar'])->name('productos.importar')->middleware('permission:productos.crear');
        Route::get('/productos/plantilla', [ProductoController::class, 'plantilla'])->name('productos.plantilla')->middleware('permission:productos.ver');

        // Control de Inventario
        Route::get('/stock', [InventarioController::class, 'index'])->name('stock')->middleware('permission:stock.ver');
        Route::get('/movimientos', [InventarioController::class, 'movimientos'])->name('movimientos')->middleware('permission:movimientos.ver');
        Route::get('/alertas', [InventarioController::class, 'alertas'])->name('alertas')->middleware('permission:alertas.ver');
        Route::post('/entrada', [InventarioController::class, 'entrada'])->name('entrada')->middleware('permission:stock.ver'); // Should ideally have its own permission but user said 'stock.ver', we'll use stock.ver
        Route::post('/salida', [InventarioController::class, 'salida'])->name('salida')->middleware('permission:stock.ver');
        Route::post('/ajuste', [InventarioController::class, 'ajuste'])->name('ajuste')->middleware('permission:stock.ver');

    });

    // ============================================================
    // POS - CLIENTES
    // ============================================================
    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('index')->middleware('permission:clientes.ver');
        Route::post('/', [ClienteController::class, 'store'])->name('store')->middleware('permission:clientes.crear');
        Route::put('/{cliente}', [ClienteController::class, 'update'])->name('update')->middleware('permission:clientes.editar');
        Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('destroy')->middleware('permission:clientes.eliminar');
        Route::post('/bulk-delete', [ClienteController::class, 'bulkDestroy'])->name('bulk_destroy')->middleware('permission:clientes.eliminar');
        Route::get('/exportar/{formato}', [ClienteController::class, 'exportar'])->name('exportar')->middleware('permission:clientes.ver');
        Route::post('/importar', [ClienteController::class, 'importar'])->name('importar')->middleware('permission:clientes.crear');
        Route::get('/plantilla', [ClienteController::class, 'plantilla'])->name('plantilla')->middleware('permission:clientes.ver');
    });

    // ============================================================
    // POS - COMPRAS
    // ============================================================
    Route::prefix('compras')->name('compras.')->group(function () {
        Route::get('/', [CompraController::class, 'index'])->name('index')->middleware('permission:compras.ver');
        Route::get('/crear', [CompraController::class, 'create'])->name('create')->middleware('permission:compras.crear');
        Route::post('/', [CompraController::class, 'store'])->name('store')->middleware('permission:compras.crear');
        Route::get('/{compra}', [CompraController::class, 'show'])->name('show')->middleware('permission:compras.ver');
        Route::post('/{compra}/anular', [CompraController::class, 'anular'])->name('anular')->middleware('permission:compras.anular');
        Route::get('/{compra}/pdf', [CompraController::class, 'pdf'])->name('pdf')->middleware('permission:compras.ver');
        Route::get('/exportar/excel', [CompraController::class, 'exportarExcel'])->name('exportar.excel')->middleware('permission:compras.ver');
    });

    // ============================================================
    // POS - CAJA (VENTAS)
    // ============================================================
    Route::prefix('caja')->name('caja.')->group(function () {

        // Estado / Apertura de caja
        Route::get('/', [CajaController::class, 'index'])->name('index')->middleware('permission:caja.ver');
        Route::post('/abrir', [CajaController::class, 'abrir'])->name('abrir')->middleware('permission:caja.abrir');
        Route::post('/cerrar', [CajaController::class, 'cerrar'])->name('cerrar')->middleware('permission:caja.cerrar');
        Route::get('/historial-cajas/{caja}', [CajaController::class, 'show'])->name('show')->middleware('permission:caja.ver');

        // Pantalla POS
        Route::get('/pos', [VentaController::class, 'pos'])->name('pos')->middleware('permission:pos.ver');

        // Ventas
        Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store')->middleware('permission:pos.vender');
        Route::get('/ventas/historial', [VentaController::class, 'historial'])->name('ventas.historial')->middleware('permission:ventas.ver');
        Route::get('/ventas/exportar/{formato}', [VentaController::class, 'exportarHistorial'])->name('ventas.exportar')->middleware('permission:ventas.ver');
        Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show')->middleware('permission:ventas.detalle');
        Route::post('/ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular')->middleware('permission:pos.anular');
        Route::get('/ventas/{venta}/pdf', [VentaController::class, 'pdf'])->name('ventas.pdf')->middleware('permission:ventas.detalle');
        Route::get('/ventas/{venta}/ticket', [VentaController::class, 'ticket'])->name('ventas.ticket')->middleware('permission:ventas.detalle');

        // Devoluciones
        Route::get('/devoluciones', [DevolucionController::class, 'index'])->name('devoluciones.index')->middleware('permission:devoluciones.ver');
        Route::post('/devoluciones', [DevolucionController::class, 'store'])->name('devoluciones.store')->middleware('permission:devoluciones.crear');

        // Ventas en espera
        Route::get('/espera', [VentaEsperaController::class, 'index'])->name('espera.index')->middleware('permission:ventas_espera.crear'); // the index lists them
        Route::post('/espera', [VentaEsperaController::class, 'store'])->name('espera.store')->middleware('permission:ventas_espera.crear');
        Route::post('/espera/{id}/retomar', [VentaEsperaController::class, 'retomar'])->name('espera.retomar')->middleware('permission:ventas_espera.cargar');
        Route::delete('/espera/{id}', [VentaEsperaController::class, 'destroy'])->name('espera.destroy')->middleware('permission:ventas_espera.cargar');
    });

    // ============================================================
    // POS - CUENTAS POR PAGAR
    // ============================================================
    Route::prefix('cuentas-por-pagar')->name('cuentas-por-pagar.')->group(function () {
        Route::get('/', [CuentaPorPagarController::class, 'index'])->name('index')->middleware('permission:compras.ver'); // Not explicitly mentioned, fallback to compras
        Route::get('/historial', [CuentaPorPagarController::class, 'historial'])->name('historial')->middleware('permission:compras.ver');
        Route::get('/vencidas', [CuentaPorPagarController::class, 'vencidas'])->name('vencidas')->middleware('permission:compras.ver');
        Route::get('/reporte', [CuentaPorPagarController::class, 'reporteProveedores'])->name('reporte')->middleware('permission:compras.ver');
        Route::get('/{compra}', [CuentaPorPagarController::class, 'show'])->name('show')->middleware('permission:compras.ver');
        Route::post('/{compra}/pago', [CuentaPorPagarController::class, 'storePago'])->name('store_pago')->middleware('permission:compras.crear');
    });

    // ============================================================
    // POS - CUENTAS POR COBRAR (ACCOUNTS RECEIVABLE)
    // ============================================================
    Route::prefix('cuentas-por-cobrar')->name('cuentas.')->group(function () {
        Route::get('/', [CuentaPorCobrarController::class, 'index'])->name('index')->middleware('permission:cobrar.ver');
        Route::get('/{cuenta}', [CuentaPorCobrarController::class, 'show'])->name('show')->middleware('permission:cobrar.ver');
        Route::post('/{cuenta}/pagar', [CuentaPorCobrarController::class, 'pagar'])->name('pagar')->middleware('permission:cobrar.pagar');
        Route::get('/reportes/historial-pagos', [CuentaPorCobrarController::class, 'historialPagos'])->name('historial-pagos')->middleware('permission:cobrar.ver');
    });

    // ============================================================
    // POS - GASTOS CATEGORÍAS
    // ============================================================
    Route::prefix('gastos/categorias')->name('gastos.categorias.')->group(function () {
        Route::get('/', [CategoriaGastoController::class, 'index'])->name('index')->middleware('permission:gastos_categorias.ver');
        Route::post('/', [CategoriaGastoController::class, 'store'])->name('store')->middleware('permission:gastos_categorias.crear');
        Route::put('/{categoria}', [CategoriaGastoController::class, 'update'])->name('update')->middleware('permission:gastos_categorias.editar');
        Route::delete('/{categoria}', [CategoriaGastoController::class, 'destroy'])->name('destroy')->middleware('permission:gastos_categorias.eliminar');
    });

    // ============================================================
    // POS - GASTOS
    // ============================================================
    Route::prefix('gastos')->name('gastos.')->group(function () {
        Route::get('/', [GastoController::class, 'index'])->name('index');
        Route::post('/', [GastoController::class, 'store'])->name('store');
        Route::get('/exportar/{formato}', [GastoController::class, 'exportar'])->name('exportar');
        Route::get('/{gasto}', [GastoController::class, 'show'])->name('show');
        Route::put('/{gasto}', [GastoController::class, 'update'])->name('update');
        Route::delete('/{gasto}', [GastoController::class, 'destroy'])->name('destroy');
    });

    // ============================================================
    // FINANZAS
    // ============================================================
    Route::prefix('finanzas')->name('finanzas.')->middleware('permission:finanzas.ver')->group(function () {
        Route::get('/', [\App\Http\Controllers\FinanzasController::class, 'index'])->name('index');
        Route::get('/reportes', [\App\Http\Controllers\FinanzasController::class, 'reportes'])->name('reportes')->middleware('permission:finanzas.reportes');
    });

    // ============================================================
    // FACTURACIÓN ELECTRÓNICA
    // ============================================================
    Route::prefix('facturacion')->name('facturacion.')->group(function () {
        Route::get('/', [\App\Http\Controllers\FacturaElectronicaController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\FacturaElectronicaController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\FacturaElectronicaController::class, 'store'])->name('store');
        Route::post('/store-manual', [\App\Http\Controllers\FacturaElectronicaController::class, 'storeManual'])->name('storeManual');
        Route::get('/{factura}', [\App\Http\Controllers\FacturaElectronicaController::class, 'show'])->name('show');
        Route::get('/{factura}/pdf', [\App\Http\Controllers\FacturaElectronicaController::class, 'pdf'])->name('pdf');
        Route::post('/{factura}/send-email', [\App\Http\Controllers\FacturaElectronicaController::class, 'sendEmail'])->name('sendEmail');
    });

});
