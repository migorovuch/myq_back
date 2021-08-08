FROM php:8-fpm AS php

#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update \
    && apt-get install -y libbz2-dev libpq-dev libzip-dev libxml2-dev zip unzip librabbitmq-dev git vim
RUN docker-php-ext-install \
    pdo_mysql mysqli opcache bcmath sockets zip bz2

COPY ./docker/php-fpm/symfony.ini /usr/local/etc/php/conf.d
COPY ./docker/php-fpm/symfony.ini /usr/local/etc/php/cli/conf.d
COPY ./docker/php-fpm/symfony.pool.conf /usr/local/etc/php/php-fpm.d/
COPY ./. /var/www/project/

WORKDIR /var/www/project/

RUN chown -R www-data:www-data var

#install dependencies
RUN composer install

RUN php bin/console lexik:jwt:generate-keypair
