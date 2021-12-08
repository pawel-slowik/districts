FROM php:7.4-fpm-alpine

RUN docker-php-ext-install pdo_mysql

RUN echo cgi.fix_pathinfo=0 > /usr/local/etc/php/conf.d/disable_cgi_fix_pathinfo.ini

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
