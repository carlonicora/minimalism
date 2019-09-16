FROM php:7.3.9-fpm-alpine

ARG BUILD_ENVIRONMENT

RUN apk update \
    && apk add --no-cache curl g++ make autoconf bash

RUN if [ "${BUILD_ENVIRONMENT}" = "development" ]; then \
    docker-php-source extract \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-source delete; \
fi

RUN docker-php-ext-install mysqli bcmath \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN rm -rf /tmp/*