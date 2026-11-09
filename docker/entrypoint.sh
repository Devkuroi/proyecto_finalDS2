#!/bin/sh
set -e

echo "=== Iniciando Entorno de Aplicación - Encuestas Cali (Entrega 1) ==="

# Esperar a que MySQL esté disponible si la base de datos es MySQL
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Esperando conexión con MySQL ($DB_HOST:$DB_PORT)..."
    while ! nc -z "$DB_HOST" "$DB_PORT"; do
      sleep 1
    done
    echo "MySQL está listo y aceptando conexiones."
fi

# Generar clave de aplicación si no existe
if [ -z "$APP_KEY" ]; then
    echo "Generando clave institucional APP_KEY..."
    php artisan key:generate --force
fi

# Ejecutar migraciones y precarga de catálogos territoriales y sedes
echo "Ejecutando migraciones y precarga de datos..."
php artisan migrate --force --seed

# Limpiar y optimizar cachés
echo "Optimizando rutas y configuración..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Iniciar Apache en primer plano
echo "Servidor web disponible en puerto 80 (mapeado a http://localhost:8000)"
exec apache2-foreground
