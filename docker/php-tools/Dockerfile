FROM php:8.3-cli-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/

COPY phpstan/composer.json phpstan/composer.lock /opt/phpstan/
WORKDIR /opt/phpstan
RUN composer install
ENV PATH="/opt/phpstan/vendor/bin:$PATH"
RUN echo memory_limit=-1 > /usr/local/etc/php/conf.d/disable_memory_limit.ini

COPY php-cs-fixer/composer.json php-cs-fixer/composer.lock /opt/php-cs-fixer/
WORKDIR /opt/php-cs-fixer
RUN composer install
ENV PATH="/opt/php-cs-fixer/vendor/bin:$PATH"

COPY php_codesniffer/composer.json php_codesniffer/composer.lock /opt/php_codesniffer/
WORKDIR /opt/php_codesniffer
RUN composer install
ENV PATH="/opt/php_codesniffer/vendor/bin:$PATH"

COPY deptrac/composer.json deptrac/composer.lock /opt/deptrac/
WORKDIR /opt/deptrac
RUN composer install
ENV PATH="/opt/deptrac/vendor/bin:$PATH"

WORKDIR /app

COPY docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]
