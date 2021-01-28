FROM composer as build

COPY composer.json /app/composer.json

RUN composer install

FROM php:8.0-alpine as test

RUN apk add autoconf g++ make
RUN pecl install mongodb-1.9.0
RUN docker-php-ext-enable mongodb

RUN apk add postgresql-dev
RUN docker-php-ext-install pdo pdo_pgsql
RUN docker-php-ext-enable pdo pdo_pgsql

COPY .php_cs.dist /app/.php_cs.dist
COPY .phpcs.xml.dist /app/.phpcs.xml.dist
COPY phpunit.xml.dist /app/phpunit.xml.dist
COPY --from=build /app/vendor /app/vendor
COPY src /app/src
COPY tests /app/tests

WORKDIR /app

ENTRYPOINT ["tail", "-f", "/dev/null"]
