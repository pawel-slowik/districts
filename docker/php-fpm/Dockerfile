FROM php:8.4-fpm-alpine AS base

RUN echo cgi.fix_pathinfo=0 > /usr/local/etc/php/conf.d/disable_cgi_fix_pathinfo.ini

RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html


FROM base AS tools

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/

ENV COMPOSER_CACHE_DIR=/tmp/composer_cache


FROM tools AS build

COPY . .

RUN --mount=type=cache,target=/tmp/composer_cache composer install --no-dev --optimize-autoloader


FROM tools AS dev

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del -f .build-deps


FROM base AS prod

COPY --from=build /var/www/html/bin bin
COPY --from=build /var/www/html/public public
COPY --from=build /var/www/html/src src
COPY --from=build /var/www/html/vendor vendor
