FROM php:8.3-fpm

# Install system dependencies including PostgreSQL dev libraries
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libzip-dev libpng-dev libxml2-dev libpq-dev \
    nodejs npm \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . /var/www

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install JS dependencies and build Vite
RUN npm install && npm run build

# Laravel permissions
RUN mkdir -p /var/www/storage \
    && mkdir -p /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Start Laravel in runtime
CMD php artisan migrate --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=8000

EXPOSE 8000
