FROM php:8.2-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip curl libxml2-dev \
    libonig-dev libpng-dev libjpeg-dev libwebp-dev \
    libevent-dev

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip sockets bcmath gd xml

# Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .


RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Install project dependencies via composer in container during build is optional
# but we'll leave scripts to composer install at runtime.

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
