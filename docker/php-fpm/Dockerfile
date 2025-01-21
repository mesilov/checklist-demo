FROM php:8.3-fpm-alpine

RUN apk add unzip libpq-dev git icu-dev \
    && docker-php-ext-install bcmath pdo pdo_pgsql intl \
    && docker-php-ext-enable bcmath pdo pdo_pgsql intl

