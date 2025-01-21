FROM php:8.3-cli-alpine

RUN apk add unzip libpq-dev git icu-dev \
    && docker-php-ext-install bcmath pdo pdo_pgsql intl \
    && docker-php-ext-enable bcmath pdo pdo_pgsql intl

COPY ./crontab /home/www-data/crontab

RUN crontab -u www-data /home/www-data/crontab
ENTRYPOINT ["crond", "-f"]
CMD [""]
