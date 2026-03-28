# 🌊 WavePOS - Sistema de Punto de Venta y Facturación Electrónica

**WavePOS** es un sistema integral de Punto de Venta (POS) moderno, escalable y listo para producción, construido sobre **Laravel 12**, **Tailwind CSS v4** y **Alpine.js** bajo una arquitectura modular estricta. Está diseñado específicamente para gestionar de forma completa el ciclo comercial: ventas en tiempo real, control de inventarios, finanzas, cuentas por cobrar/pagar y cuenta con integración nativa para la **Facturación Electrónica (DIAN)**.

## ✨ Características Principales

- 🚀 **Núcleo en Laravel 12** - Construido con la última versión de Laravel, garantizando seguridad máxima, agilidad en las peticiones y un código limpio.
- 🎨 **Diseño Moderno (Tailwind CSS)** - Interfaz de usuario intuitiva, fluida y totalmente responsiva (se adapta a móviles, tabletas y PC).
- ⚡ **Interactividad con Alpine.js** - Comportamientos reactivos y modales en tiempo real sin la pesadez visual de otros frameworks JS complejos.
- 🧾 **Facturación Electrónica (API Factus)** - Módulo especializado para la emisión, validación y envío por correo de facturas electrónicas ante la DIAN en Colombia.
- 🛒 **Módulo POS Dinámico** - Interfaz de cobro ágil habilitada para manejar ventas en espera, métodos de pago múltiples (efectivo, tarjetas, crédito) y cortes de caja.
- 📊 **Gestión Integral** - Control robusto sobre Clientes, Proveedores, Compras, Devoluciones, Ajustes de Stock y Catálogo de Productos.
- 🌙 **Modo Oscuro Integrado** - Tema oscuro nativo de alta calidad para mayor confort visual e impacto estético.
- 🔒 **Control de Acceso (Roles y Permisos)** - Sistema de permisos granulares Spatie para restringir de forma segura la operativa de cajeros, administradores o contadores.

## 📋 Requisitos del Sistema

Para desplegar y desarrollar WavePOS localmente o en un servidor, asegúrate de tener:

- **PHP 8.2+**
- **Composer** (Gestor de dependencias de PHP)
- **Node.js 18+** y **npm** (para la compilación de Vite HMR)
- **Base de Datos** - MySQL 8+ o MariaDB
- **Extensiones PHP:** `ext-zip`, `ext-curl`, `ext-pdo` habilitadas.

Verifica tus herramientas principales en consola con:

```bash
php -v
composer -V
node -v
npm -v

# 🚀 Instalación y Puesta en Marcha (Desarrollo local)
git clone https://github.com/nextwavelaracode-ux/WavePos.git
cd WavePos

# Instalar dependencias
composer install
npm install

# Crear archivo .env
# windows
copy .env.example .env
# linux/mac
cp .env.example .env

# Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wavepos_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

# Genera la llave de encriptación única de la aplicación
php artisan key:generate

# Construye la estructura de base de datos e inyecta los Datos Secuencia (Roles, Permisos, Empresa base)
php artisan migrate:fresh --seed

# Crea el puente de acceso para los archivos públicos (logos, facturas PDF)
php artisan storage:link

# Iniciar servidor de desarrollo
php artisan serve
npm run dev


# 🚀 Despliegue en Producción (Comandos Finales)
# Compilar los archivos finales minificados de CSS y JavaScript
npm run build

# Limpiar y optimizar todas las cachés
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimizar el sistema de autocarga de Clases PHP
composer install --optimize-autoloader --no-dev

# 📂 Estructura del Proyecto
WavePos/
├── app/
│   ├── Helpers/            # Gestor dinámico del Menú Lateral e Iconos
│   ├── Http/Controllers/   # Lógica central (Caja, Inventario, Compras, Facturación...)
│   └── Models/             # Modelos ORM, Mutadores y relaciones lógicas de BD
├── config/                 # Configuraciones especiales (incluye setup de API Factus)
├── database/               # Esquemas y Plantillas de llenado de BD
├── public/                 # Recursos de marca y assets minificados
├── resources/
│   └── views/pages/        # Vistas Blade altamente estructuradas por Área de Negocio
└── routes/
    └── web.php             # Enrutamiento protegido e inyección de middlewares de Roles


```
