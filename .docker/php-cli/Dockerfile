FROM carlonicora/php-cli-base:7.4.5.1

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