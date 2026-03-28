#!/bin/bash

# =============================================================
# Script de build para Vercel - WavePos (Laravel 12)
# =============================================================

set -e

echo "🚀 Iniciando build de WavePos para Vercel..."

# --- 1. Variables de entorno ----
export APP_ENV=production
export APP_DEBUG=false
export LOG_CHANNEL=stderr

# --- 2. Instalar dependencias PHP (sin dev) ---
echo "📦 Instalando dependencias de Composer..."
composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# --- 3. Instalar dependencias Node ---
echo "📦 Instalando dependencias de Node..."
npm ci

# --- 4. Compilar assets con Vite ---
echo "🎨 Compilando assets con Vite..."
npm run build

# --- 5. Optimizaciones de Laravel ---
echo "⚡ Optimizando Laravel para producción..."

# Generar APP_KEY si no existe
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Cachear configuración, rutas y vistas
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Storage link (para archivos públicos)
php artisan storage:link --quiet || true

echo "✅ Build completado exitosamente!"
