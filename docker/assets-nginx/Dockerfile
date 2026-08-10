FROM nginx:1-alpine AS base

COPY docker/assets-nginx/default.conf /etc/nginx/conf.d/default.conf


FROM php:8.4-fpm-alpine AS tools

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/

ENV COMPOSER_CACHE_DIR=/tmp/composer_cache


FROM tools AS build

COPY . .

RUN --mount=type=cache,target=/tmp/composer_cache composer install --no-dev --ignore-platform-req=ext-pdo_mysql


FROM base AS dev


FROM base AS prod

COPY --from=build /var/www/html/public/assets /var/www/html
