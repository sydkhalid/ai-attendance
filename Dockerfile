FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libonig-dev libzip-dev libpng-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy code
COPY . /var/www

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Storage + Permissions
RUN mkdir -p /var/www/storage \
    && mkdir -p /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Command to run Laravel
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000

EXPOSE 8000
