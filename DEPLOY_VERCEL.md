# 🚀 Guía Profesional de Despliegue de WavePos en Vercel

Esta guía detalla la arquitectura y el procedimiento paso a paso para desplegar **WavePos** (Laravel 12 + Livewire + Vite + Tailwind CSS) en **Vercel** de forma profesional, garantizando alta disponibilidad, cero caídas y compatibilidad total con el entorno Serverless.

---

## 🏗️ 1. Arquitectura del Despliegue en Vercel

Vercel opera sobre funciones serverless efímeras (AWS Lambda) que imponen dos condiciones técnicas fundamentales:
1. **Sistema de archivos de solo lectura**: Salvo la carpeta `/tmp`, no se puede escribir en el disco del servidor.
2. **Naturaleza sin estado (Stateless)**: No hay servidores persistentes en segundo plano; las instancias se inician y suspenden por demanda.

### 🛡️ Soluciones implementadas en el proyecto:
* **`api/index.php`**: Actúa como puente hacia Laravel. Inicializa automáticamente en `/tmp` los directorios requeridos (`views`, `cache`, `sessions`, `logs`) y reubica el storage de Laravel mediante `$app->useStoragePath('/tmp/storage')`.
* **`vercel.json`**:
  * Utiliza el runtime comunitario optimizado `vercel-php@0.7.4` (PHP 8.3).
  * Asigna **1024 MB de memoria** y **30 segundos de tiempo de ejecución**.
  * Enruta directamente los assets estáticos compilados por Vite (`/build/(.*)`) con encabezados de caché inmutable para descarga ultra-rápida desde el CDN perimetral (Edge).
  * Envía todos los registros a `stderr` (`LOG_CHANNEL=stderr`) para visualizarlos en vivo en la pestaña **Functions -> Logs** del panel de Vercel.
* **`/vercel-artisan`**: Endpoint seguro protegido por token (`ARTISAN_KEY`) para ejecutar migraciones (`migrate`), sembradores (`db:seed`) o limpiar cachés sin necesidad de acceso SSH.

---

## 📋 2. Archivos Configurados en el Proyecto

| Archivo | Función |
| :--- | :--- |
| [`api/index.php`](file:///c:/Users/Jhared92/WavePos/api/index.php) | Entry point serverless para Vercel. Gestiona `/tmp` y normaliza variables de servidor. |
| [`vercel.json`](file:///c:/Users/Jhared92/WavePos/vercel.json) | Configuración de runtime PHP 8.3, enrutamiento, headers de caché CDN y variables. |
| [`.vercelignore`](file:///c:/Users/Jhared92/WavePos/.vercelignore) | Evita la subida de dependencias locales para garantizar compilación limpia en la nube. |
| [`.gitignore`](file:///c:/Users/Jhared92/WavePos/.gitignore) | Protege credenciales, claves y archivos temporales. |
| [`.env.production.example`](file:///c:/Users/Jhared92/WavePos/.env.production.example) | Modelo con todas las variables requeridas en Vercel. |
| [`package.json`](file:///c:/Users/Jhared92/WavePos/package.json) | Incluye script `vercel-build` para Vite. |
| [`routes/web.php`](file:///c:/Users/Jhared92/WavePos/routes/web.php) | Endpoint de mantenimiento seguro `/vercel-artisan`. |

---

## ⚙️ 3. Variables de Entorno Obligatorias en Vercel

Antes de iniciar el despliegue, agrega estas variables en:
**Vercel Dashboard ➔ Tu Proyecto ➔ Settings ➔ Environment Variables**

```env
APP_NAME=WavePos
APP_ENV=production
APP_KEY=base64:2Zo099X8sUSrZ0pCePa72gAje02rPqI1pFpgpedahWg=
APP_DEBUG=false
APP_URL=https://tu-proyecto.vercel.app

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=db.cbzrrsijtgurpszwtone.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=3plPJkjUYcMuAUQN
DB_SSLMODE=require

SUPABASE_URL=https://cbzrrsijtgurpszwtone.supabase.co
SUPABASE_PUBLISHABLE_KEY=sb_publishable_pDQuNQQ9A7DKYrJ8D_v6NQ_cLgSwu2e
SUPABASE_SECRET_KEY=your_supabase_secret_key_here
SUPABASE_JWKS_URL=https://cbzrrsijtgurpszwtone.supabase.co/auth/v1/.well-known/jwks.json

SESSION_DRIVER=cookie
CACHE_STORE=array
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php
APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php
APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php
APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php
APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php
VIEW_COMPILED_PATH=/tmp/storage/framework/views

ARTISAN_KEY=wv_sec_8d4f91b72e063a5c9d18e24fa7b30c1e
```

> [!IMPORTANT]
> **Base de Datos Externa Requerida**: Vercel no aloja MySQL internamente. Debes usar una base de datos en la nube accesible desde internet, como **Railway (MySQL)**, **Aiven**, **PlanetScale**, **Supabase** o **AWS RDS**.

---

## 🚀 4. Paso a Paso para Desplegar

### Paso 1: Subir cambios a tu repositorio Git
Realiza un commit y sube los archivos creados a tu repositorio (GitHub, GitLab o Bitbucket):
```bash
git add .
git commit -m "feat: configuracion profesional de despliegue en Vercel"
git push origin main
```

### Paso 2: Importar el proyecto en Vercel
1. Ingresa a [vercel.com](https://vercel.com/) e inicia sesión.
2. Haz clic en **"Add New..." ➔ "Project"**.
3. Selecciona tu repositorio de Git.
4. En **Framework Preset**, déjalo en **Other** (el archivo `vercel.json` se encarga de todo).
5. Despliega la sección **Environment Variables** y copia las variables listadas en la sección anterior.
6. Haz clic en **Deploy**.

Vercel compilará automáticamente los assets con Vite (`npm run build`), descargará las dependencias con Composer y publicará tu aplicación con URL `https://...vercel.app`.

---

## 🗄️ 5. Ejecutar Migraciones en Producción

Una vez desplegada la aplicación, tienes **2 métodos seguros** para crear las tablas en tu base de datos:

### Método A: Desde la URL con el Endpoint Seguro (Recomendado)
Abre tu navegador e ingresa a:
```text
https://tu-proyecto.vercel.app/vercel-artisan?key=wv_sec_8d4f91b72e063a5c9d18e24fa7b30c1e&cmd=migrate
```

Recibirás una respuesta JSON confirmando el resultado de las migraciones:
```json
{
  "status": "success",
  "command": "migrate",
  "output": "Migrating: 0001_01_01_000000_create_users_table...\nMigrated:  0001_01_01_000000_create_users_table"
}
```

Para sembrar roles, permisos y usuarios iniciales:
```text
https://tu-proyecto.vercel.app/vercel-artisan?key=wv_sec_8d4f91b72e063a5c9d18e24fa7b30c1e&cmd=db:seed
```

### Método B: Desde tu Terminal Local
También puedes ejecutar la migración conectándote a la base de datos de producción desde tu equipo local:
```bash
php artisan migrate --force
```

---

## 🔍 6. Monitoreo y Solución de Problemas

1. **Ver Errores en Tiempo Real**:
   En el panel de Vercel, entra a **Project ➔ Logs** o **Functions**. Gracias a `LOG_CHANNEL=stderr`, cualquier excepción de Laravel aparecerá inmediatamente con su stack trace completo.
2. **Archivos e Imágenes**:
   Para subida de imágenes de productos y facturas en entornos serverless, configura un disco de almacenamiento en la nube (AWS S3, Cloudflare R2 o Supabase Storage) seteando `FILESYSTEM_DISK=s3`.
3. **Caché y Vistas**:
   Si realizas cambios en el código y quieres refrescar la caché:
   ```text
   https://tu-proyecto.vercel.app/vercel-artisan?key=TU_ARTISAN_KEY&cmd=optimize:clear
   ```
