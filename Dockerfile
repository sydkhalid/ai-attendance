# syntax=docker/dockerfile:1

FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libzip-dev libpng-dev libxml2-dev libpq-dev \
    ca-certificates gnupg

# Install Node.js (required for Vite 7)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install JS dependencies and build Vite
RUN npm install && npm run build

# FIX: Move Vite manifest to where Laravel expects it
RUN cp public/build/.vite/manifest.json public/build/manifest.json

# Permissions
RUN chmod -R 777 storage bootstrap/cache public/build

CMD php artisan migrate --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=8000

EXPOSE 8000
