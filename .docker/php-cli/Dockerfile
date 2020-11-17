FROM php:7.4-cli-alpine

RUN apk update \
    && apk add --no-cache curl g++ make autoconf bash supervisor

RUN docker-php-ext-install mysqli bcmath \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ARG XDEBUG_PORT
ARG XDEBUG_HOST

COPY ./shell/ /tmp/scripts/
RUN chmod -R 777 /tmp/scripts/

COPY ./php-cli/configurations/* /usr/local/etc/php/conf.d/

RUN docker-php-source extract \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && /tmp/scripts/distributeConfig.sh /usr/local/etc/php/conf.d/zz-xdebug.ini "{XDEBUG_PORT}" "${XDEBUG_PORT}" \
    && /tmp/scripts/distributeConfig.sh /usr/local/etc/php/conf.d/zz-xdebug.ini "{XDEBUG_HOST}" "${XDEBUG_HOST}" \
    && docker-php-source delete;

RUN rm -rf /tmp/*