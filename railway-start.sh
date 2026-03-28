#!/bin/bash

# =============================================================
# Script de inicio para Railway — WavePos (Laravel 12)
# Se ejecuta CADA VEZ que el contenedor arranca
# =============================================================

set -e

echo "🚀 Iniciando WavePos en Railway..."

# --- 1. Optimizaciones de Laravel ---
echo "⚡ Cacheando configuración, rutas y vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# --- 2. Ejecutar migraciones (seguro en producción) ---
echo "🗄️  Ejecutando migraciones..."
php artisan migrate --force --no-interaction

# --- 3. Crear storage link si no existe ---
echo "🔗 Creando storage link..."
php artisan storage:link --quiet 2>/dev/null || true

# --- 4. Ajustar permisos de storage ---
echo "🔐 Ajustando permisos..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# --- 5. Iniciar servidor PHP integrado ---
echo "🌐 Iniciando servidor en 0.0.0.0:${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
