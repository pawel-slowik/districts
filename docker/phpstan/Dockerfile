FROM php:8.3-cli-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/

COPY composer.json composer.lock /phpstan/

WORKDIR /phpstan

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install

ENV PATH="/phpstan/vendor/bin:$PATH"

RUN echo memory_limit=-1 > /usr/local/etc/php/conf.d/disable_memory_limit.ini

WORKDIR /app

ENTRYPOINT ["phpstan"]
