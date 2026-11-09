# =====================================================================
# SISTEMA DE GESTIÓN Y GEORREFERENCIACIÓN DE ENCUESTAS - SANTIAGO DE CALI
# DOCKERFILE MULTI-ETAPA REPRODUCIBLE (PHP 8.2 + APACHE)
# Sub-Agente 4: DevOps_Agent
# =====================================================================

FROM php:8.2-apache

# Metadatos institucionales
LABEL maintainer="Equipo de Ingeniería de Software - Encuestas Cali"
LABEL version="1.0-entrega1"
LABEL description="Sistema de Gestión y Georreferenciación de Encuestas Domiciliarias Cali"

# Instalar dependencias del sistema operativo y extensiones requeridas de PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    netcat-openbsd \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath opcache \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar Apache VirtualHost hacia la carpeta public de Laravel
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Directorio de trabajo en el contenedor
WORKDIR /var/www/html

# Copiar archivos de dependencias de Composer primero (aprovechar caché de Docker)
COPY composer.json composer.lock ./

# Instalar dependencias PHP de producción sin ejecutar scripts aún
RUN composer install --no-dev --no-interaction --prefer-dist --no-autoloader --no-scripts

# Copiar el código fuente completo del proyecto
COPY . .

# Terminar instalación de Composer con optimización de autoloader
RUN composer dump-autoload --optimize --no-dev

# Configurar permisos para Apache (www-data) en storage y cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/docker/entrypoint.sh

# Variables de entorno predeterminadas
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Exponer el puerto HTTP
EXPOSE 80

# Script de entrada para migraciones y arranque de Apache
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
