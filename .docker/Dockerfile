FROM composer:2.5.4 AS composer

FROM php:8.2.3-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update \
        && apt-get install -y curl libpq-dev git rsync libzip-dev libpng-dev \
        && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) zip gd pdo_mysql

EXPOSE 9000

WORKDIR /var/www/html
