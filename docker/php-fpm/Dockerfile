FROM php:8.1-fpm-alpine

RUN docker-php-ext-install pdo_mysql

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del -f .build-deps

RUN echo cgi.fix_pathinfo=0 > /usr/local/etc/php/conf.d/disable_cgi_fix_pathinfo.ini

# for PHPStan
RUN echo memory_limit=-1 > /usr/local/etc/php/conf.d/disable_memory_limit.ini

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
