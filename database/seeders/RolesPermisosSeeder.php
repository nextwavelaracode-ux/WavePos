<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir todos los permisos por módulo
        $permisos = [
            // Estado de caja
            'caja.ver', 'caja.abrir', 'caja.cerrar', 'caja.arqueo',
            // POS
            'pos.ver', 'pos.vender', 'pos.anular',
            // Ventas
            'ventas.ver', 'ventas.detalle',
            // Devoluciones
            'devoluciones.crear', 'devoluciones.ver',
            // Ventas en espera
            'ventas_espera.crear', 'ventas_espera.cargar',
            // Compras
            'compras.ver', 'compras.crear', 'compras.anular',
            // Proveedores
            'proveedores.ver', 'proveedores.crear', 'proveedores.editar', 'proveedores.eliminar',
            // Categorías
            'categorias.ver', 'categorias.crear', 'categorias.editar', 'categorias.eliminar',
            // Productos
            'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar',
            // Inventario
            'stock.ver', 'movimientos.ver', 'alertas.ver',
            // Clientes
            'clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar',
            // Empresa
            'empresa.ver', 'empresa.editar',
            // Sucursales
            'sucursales.ver', 'sucursales.crear', 'sucursales.editar',
            // Usuarios
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            // Roles y permisos
            'roles.ver', 'roles.crear', 'roles.editar',
            // Cuentas por cobrar
            'cobrar.ver', 'cobrar.pagar',
            // Gastos
            'gastos.ver', 'gastos.crear', 'gastos.editar', 'gastos.eliminar',
            // Gastos Categorías
            'gastos_categorias.ver', 'gastos_categorias.crear', 'gastos_categorias.editar', 'gastos_categorias.eliminar',
            // Finanzas
            'finanzas.ver', 'finanzas.reportes',
        ];

        // Crear permisos
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // Definir roles y sus permisos
        $roles = [
            'Administrador' => $permisos, // Acceso total

            'Gerente' => [
                'pos.ver', 'ventas.ver', 'ventas.detalle', 'devoluciones.ver', 'devoluciones.crear',
                'compras.ver', 'compras.crear', 'compras.anular',
                'caja.ver', 'caja.arqueo',
                'proveedores.ver', 'categorias.ver', 'productos.ver',
                'stock.ver', 'movimientos.ver', 'alertas.ver',
                'clientes.ver', 'empresa.ver', 'sucursales.ver',
                'usuarios.ver', 'roles.ver',
                'cobrar.ver', 'gastos.ver', 'gastos_categorias.ver'
            ],

            'Cajero' => [
                'caja.ver', 'caja.abrir', 'caja.cerrar', 'caja.arqueo',
                'pos.ver', 'pos.vender', 'pos.anular',
                'ventas.ver', 'ventas.detalle',
                'ventas_espera.crear', 'ventas_espera.cargar',
                'clientes.ver', 'clientes.crear'
            ],

            'Inventario' => [
                'productos.ver', 'productos.crear', 'productos.editar', 'productos.eliminar',
                'categorias.ver', 'categorias.crear', 'categorias.editar', 'categorias.eliminar',
                'stock.ver', 'movimientos.ver', 'alertas.ver',
                'compras.ver', 'compras.crear', 'compras.anular',
                'proveedores.ver', 'proveedores.crear', 'proveedores.editar'
            ],

            'Contabilidad' => [
                'cobrar.ver', 'cobrar.pagar',
                'caja.ver', 'caja.arqueo',
                'ventas.ver', 'ventas.detalle',
                'compras.ver', 'gastos.ver', 'gastos_categorias.ver'
            ],
        ];

        // Crear roles y asignar permisos
        foreach ($roles as $nombreRol => $permisosRol) {
            $rol = Role::firstOrCreate(['name' => $nombreRol, 'guard_name' => 'web']);
            $rol->syncPermissions($permisosRol);
        }
    }
}
