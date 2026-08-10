FROM php:8.4-cli-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/
ENV COMPOSER_CACHE_DIR=/tmp/composer_cache

COPY phpstan/composer.json phpstan/composer.lock /opt/phpstan/
WORKDIR /opt/phpstan
RUN --mount=type=cache,target=/tmp/composer_cache composer install
RUN echo memory_limit=-1 > /usr/local/etc/php/conf.d/disable_memory_limit.ini

COPY php-cs-fixer/composer.json php-cs-fixer/composer.lock /opt/php-cs-fixer/
WORKDIR /opt/php-cs-fixer
RUN --mount=type=cache,target=/tmp/composer_cache composer install

COPY php_codesniffer/composer.json php_codesniffer/composer.lock /opt/php_codesniffer/
WORKDIR /opt/php_codesniffer
RUN --mount=type=cache,target=/tmp/composer_cache composer install

COPY deptrac/composer.json deptrac/composer.lock /opt/deptrac/
WORKDIR /opt/deptrac
RUN --mount=type=cache,target=/tmp/composer_cache composer install

COPY rector/composer.json rector/composer.lock /opt/rector/
WORKDIR /opt/rector
RUN --mount=type=cache,target=/tmp/composer_cache composer install

WORKDIR /app

COPY docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]
