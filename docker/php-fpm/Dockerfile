FROM php:8.0.0-fpm AS prod

#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update \
    && apt-get install -y libonig-dev libbz2-dev libpq-dev libzip-dev curl libxml2-dev zip unzip librabbitmq-dev git vim iputils-ping

RUN docker-php-ext-install \
    pdo_mysql mysqli opcache bcmath sockets zip bz2 intl

COPY ./docker/php-fpm/symfony.ini /usr/local/etc/php/conf.d
COPY ./docker/php-fpm/symfony.ini /usr/local/etc/php/cli/conf.d
COPY ./docker/php-fpm/symfony.pool.conf /usr/local/etc/php/php-fpm.d/
COPY ./. /var/www/html/
WORKDIR /var/www/html

FROM prod AS stage
COPY ./docker/php-fpm/xdebug.ini  /usr/local/etc/php/conf.d
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug intl

FROM prod AS dev
#install php-cs-fixer
RUN curl -L https://cs.symfony.com/download/php-cs-fixer-v2.phar -o php-cs-fixer
RUN chmod a+x php-cs-fixer
RUN mv php-cs-fixer /usr/local/bin/php-cs-fixer
#RUN pecl install mongodb
#RUN docker-php-ext-enable mongodb
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony