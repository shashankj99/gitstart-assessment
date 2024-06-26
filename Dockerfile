FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \ 
    libzip-dev zip unzip libpng-dev \
    default-mysql-client

RUN docker-php-ext-install pdo pdo_mysql zip gd

WORKDIR /var/www

COPY . /var/www/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-scripts --no-autoloader

EXPOSE 80
EXPOSE 9000
