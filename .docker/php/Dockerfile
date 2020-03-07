FROM php:7.4.3-fpm-alpine

RUN apk update \
    && apk add --no-cache curl g++ make autoconf bash

RUN docker-php-ext-install mysqli bcmath \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer global require hirak/prestissimo --no-plugins --no-scripts