@php
    use App\Helpers\MenuHelper;

    // Determine active section server-side
    $isHomeActive = request()->is('/') || request()->is('finanzas*');
    $isCajaActive = request()->is('caja*') || request()->is('cuentas-por-cobrar*');
    $isInvActive = request()->is('inventario*');
    $isComprasActive = request()->is('compras*') || request()->is('cuentas-por-pagar*') || request()->is('gastos*');
    $isFacturacionActive = request()->is('facturacion*');
    $isClientesActive = request()->is('clientes*');
    $isConfigActive = request()->is('configuracion*');
    $isProfileActive = request()->is('profile*');
@endphp

<aside id="collapsible-sidebar" class="overlay [--body-scroll:true] border-base-content/20 overlay-open:translate-x-0 drawer drawer-start sm:overlay-layout-open:translate-x-0 hidden w-64 border-e [--auto-close:sm] [--is-layout-affect:true] [--opened:lg] fixed sm:fixed top-0 start-0 h-screen z-[998] sm:flex sm:shadow-none lg:[--overlay-backdrop:false]" role="dialog" tabindex="-1">
  <div class="drawer-body px-2 pt-4 flex flex-col h-full">
    
    {{-- Brand Logo / Header --}}
    <div class="flex items-center gap-2.5 px-3 pb-3 mb-2 border-b border-base-content/10">
      <img src="/images/logo/logotipohd.png" alt="WavePOS" class="h-8 w-8 shrink-0 object-contain" />
      <span class="font-bold text-lg text-base-content tracking-tight">WavePOS</span>
    </div>

    {{-- Main Menu with Submenus --}}
    <div class="flex-1 overflow-y-auto space-y-0.5 pr-1">
      <ul class="menu accordion p-0 w-full" data-accordion-always-open>
        
        {{-- 1. HOME / DASHBOARD (Submenu) --}}
        <li class="accordion-item {{ $isHomeActive ? 'active' : '' }}" id="menu-home">
          <button type="button" class="accordion-toggle inline-flex items-center justify-between w-full" aria-controls="menu-home-collapse" aria-expanded="{{ $isHomeActive ? 'true' : 'false' }}">
            <span class="inline-flex items-center gap-2">
              <span class="icon-[tabler--home] size-5"></span>
              Home
            </span>
            <span class="icon-[tabler--chevron-right] size-4 transition-transform duration-300 accordion-item-active:rotate-90"></span>
          </button>
          <div id="menu-home-collapse" class="accordion-content {{ $isHomeActive ? '' : 'hidden' }} w-full overflow-hidden transition-[height] duration-300">
            <ul class="pt-1 ps-6 space-y-0.5">
              <li>
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Ecommerce
                </a>
              </li>
              <li>
                <a href="{{ url('/finanzas') }}" class="{{ request()->is('finanzas*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Financiero
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- 2. ACCOUNT (Direct Link) --}}
        <li>
          <a href="{{ url('/profile') }}" class="{{ $isProfileActive ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
            <span class="icon-[tabler--user] size-5"></span>
            Account
          </a>
        </li>

        {{-- 3. CAJA & VENTAS (Submenu) --}}
        <li class="accordion-item {{ $isCajaActive ? 'active' : '' }}" id="menu-caja">
          <button type="button" class="accordion-toggle inline-flex items-center justify-between w-full" aria-controls="menu-caja-collapse" aria-expanded="{{ $isCajaActive ? 'true' : 'false' }}">
            <span class="inline-flex items-center gap-2">
              <span class="icon-[tabler--device-laptop] size-5"></span>
              Caja & Ventas
            </span>
            <span class="icon-[tabler--chevron-right] size-4 transition-transform duration-300 accordion-item-active:rotate-90"></span>
          </button>
          <div id="menu-caja-collapse" class="accordion-content {{ $isCajaActive ? '' : 'hidden' }} w-full overflow-hidden transition-[height] duration-300">
            <ul class="pt-1 ps-6 space-y-0.5">
              <li>
                <a href="{{ url('/caja/pos') }}" class="{{ request()->is('caja/pos*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Punto de Venta
                </a>
              </li>
              <li>
                <a href="{{ url('/caja') }}" class="{{ request()->is('caja') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Estado de Caja
                </a>
              </li>
              <li>
                <a href="{{ url('/caja/ventas/historial') }}" class="{{ request()->is('caja/ventas/historial*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Historial de Ventas
                </a>
              </li>
              <li>
                <a href="{{ url('/caja/devoluciones') }}" class="{{ request()->is('caja/devoluciones*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Devoluciones
                </a>
              </li>
              <li>
                <a href="{{ url('/caja/espera') }}" class="{{ request()->is('caja/espera*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Ventas en Espera
                </a>
              </li>
              <li>
                <a href="{{ url('/cuentas-por-cobrar') }}" class="{{ request()->is('cuentas-por-cobrar') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Cuentas por Cobrar
                </a>
              </li>
              <li>
                <a href="{{ url('/cuentas-por-cobrar/reportes/historial-pagos') }}" class="{{ request()->is('cuentas-por-cobrar/reportes*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Historial de Cobros
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- 4. PRODUCT / INVENTARIO (Submenu) --}}
        <li class="accordion-item {{ $isInvActive ? 'active' : '' }}" id="menu-product">
          <button type="button" class="accordion-toggle inline-flex items-center justify-between w-full" aria-controls="menu-product-collapse" aria-expanded="{{ $isInvActive ? 'true' : 'false' }}">
            <span class="inline-flex items-center gap-2">
              <span class="icon-[tabler--shopping-bag] size-5"></span>
              Product
            </span>
            <span class="icon-[tabler--chevron-right] size-4 transition-transform duration-300 accordion-item-active:rotate-90"></span>
          </button>
          <div id="menu-product-collapse" class="accordion-content {{ $isInvActive ? '' : 'hidden' }} w-full overflow-hidden transition-[height] duration-300">
            <ul class="pt-1 ps-6 space-y-0.5">
              <li>
                <a href="{{ url('/inventario/productos') }}" class="{{ request()->is('inventario/productos*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Productos
                </a>
              </li>
              <li>
                <a href="{{ url('/inventario/categorias') }}" class="{{ request()->is('inventario/categorias*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Categorías
                </a>
              </li>
              <li>
                <a href="{{ url('/inventario/proveedores') }}" class="{{ request()->is('inventario/proveedores*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Proveedores
                </a>
              </li>
              <li>
                <a href="{{ url('/inventario/stock') }}" class="{{ request()->is('inventario/stock*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Control de Stock
                </a>
              </li>
              <li>
                <a href="{{ url('/inventario/movimientos') }}" class="{{ request()->is('inventario/movimientos*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Movimientos
                </a>
              </li>
              <li>
                <a href="{{ url('/inventario/alertas') }}" class="{{ request()->is('inventario/alertas*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Alertas de Stock
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- 5. COMPRAS & GASTOS (Submenu) --}}
        <li class="accordion-item {{ $isComprasActive ? 'active' : '' }}" id="menu-compras">
          <button type="button" class="accordion-toggle inline-flex items-center justify-between w-full" aria-controls="menu-compras-collapse" aria-expanded="{{ $isComprasActive ? 'true' : 'false' }}">
            <span class="inline-flex items-center gap-2">
              <span class="icon-[tabler--truck] size-5"></span>
              Compras & Gastos
            </span>
            <span class="icon-[tabler--chevron-right] size-4 transition-transform duration-300 accordion-item-active:rotate-90"></span>
          </button>
          <div id="menu-compras-collapse" class="accordion-content {{ $isComprasActive ? '' : 'hidden' }} w-full overflow-hidden transition-[height] duration-300">
            <ul class="pt-1 ps-6 space-y-0.5">
              <li>
                <a href="{{ url('/compras') }}" class="{{ request()->is('compras') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Historial de Compras
                </a>
              </li>
              <li>
                <a href="{{ url('/compras/crear') }}" class="{{ request()->is('compras/crear*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Registrar Compra
                </a>
              </li>
              <li>
                <a href="{{ url('/cuentas-por-pagar') }}" class="{{ request()->is('cuentas-por-pagar*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Cuentas por Pagar
                </a>
              </li>
              <li>
                <a href="{{ url('/gastos') }}" class="{{ request()->is('gastos') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Gastos
                </a>
              </li>
              <li>
                <a href="{{ url('/gastos/categorias') }}" class="{{ request()->is('gastos/categorias*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Categorías de Gastos
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- 6. FACTURACIÓN DIAN (Direct Link) --}}
        <li>
          <a href="{{ url('/facturacion') }}" class="{{ $isFacturacionActive ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
            <span class="icon-[tabler--file-invoice] size-5"></span>
            Facturación DIAN
          </a>
        </li>

        {{-- 7. CLIENTES (Direct Link) --}}
        <li>
          <a href="{{ url('/clientes') }}" class="{{ $isClientesActive ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
            <span class="icon-[tabler--users] size-5"></span>
            Clientes
          </a>
        </li>

        {{-- 8. CONFIGURACIÓN (Submenu) --}}
        <li class="accordion-item {{ $isConfigActive ? 'active' : '' }}" id="menu-config">
          <button type="button" class="accordion-toggle inline-flex items-center justify-between w-full" aria-controls="menu-config-collapse" aria-expanded="{{ $isConfigActive ? 'true' : 'false' }}">
            <span class="inline-flex items-center gap-2">
              <span class="icon-[tabler--settings] size-5"></span>
              Configuración
            </span>
            <span class="icon-[tabler--chevron-right] size-4 transition-transform duration-300 accordion-item-active:rotate-90"></span>
          </button>
          <div id="menu-config-collapse" class="accordion-content {{ $isConfigActive ? '' : 'hidden' }} w-full overflow-hidden transition-[height] duration-300">
            <ul class="pt-1 ps-6 space-y-0.5">
              <li>
                <a href="{{ url('/configuracion/empresa') }}" class="{{ request()->is('configuracion/empresa*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Empresa
                </a>
              </li>
              <li>
                <a href="{{ url('/configuracion/sucursales') }}" class="{{ request()->is('configuracion/sucursales*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Sucursales
                </a>
              </li>
              <li>
                <a href="{{ url('/configuracion/usuarios') }}" class="{{ request()->is('configuracion/usuarios*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Usuarios
                </a>
              </li>
              <li>
                <a href="{{ url('/configuracion/roles') }}" class="{{ request()->is('configuracion/roles*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Roles y Permisos
                </a>
              </li>
              <li>
                <a href="{{ url('/configuracion/sistema') }}" class="{{ request()->is('configuracion/sistema*') ? 'menu-active bg-primary/10 text-primary font-medium' : '' }}">
                  Config. del Sistema
                </a>
              </li>
            </ul>
          </div>
        </li>

        {{-- Divider --}}
        <div class="my-1 border-t border-base-content/10"></div>

        {{-- 9. NOTIFICATIONS, EMAIL, CALENDAR --}}
        <li>
          <a href="#">
            <span class="icon-[tabler--message] size-5"></span>
            Notifications
          </a>
        </li>
        <li>
          <a href="#">
            <span class="icon-[tabler--mail] size-5"></span>
            Email
          </a>
        </li>
        <li>
          <a href="#">
            <span class="icon-[tabler--calendar] size-5"></span>
            Calendar
          </a>
        </li>

        {{-- 10. AUTH (Sign In / Sign Out) --}}
        @guest
        <li>
          <a href="{{ route('login') }}">
            <span class="icon-[tabler--login] size-5"></span>
            Sign In
          </a>
        </li>
        @else
        <li>
          <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 text-error hover:bg-error/10 text-left">
              <span class="icon-[tabler--logout-2] size-5"></span>
              Sign Out
            </button>
          </form>
        </li>
        @endguest

      </ul>
    </div>

  </div>
</aside>
