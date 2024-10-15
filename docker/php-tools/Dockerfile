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

WORKDIR /app

ENTRYPOINT ["sh"]

CMD ["-c", "result=0; trap result=1 ERR; phpstan -V; phpstan analyse; php-cs-fixer fix -v --dry-run --diff; phpcs --version; phpcs; exit $result"]
