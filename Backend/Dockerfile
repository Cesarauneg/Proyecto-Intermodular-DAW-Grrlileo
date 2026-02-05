FROM php:8.2-fpm

#Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip

#Extensiones para MySQL y Redis
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

#Instalamos la extensión de Redis para PHP
RUN pecl install redis && docker-php-ext-enable redis

#Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

#Permisos para el almacenamiento de Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]