FROM php:8.3-alpine AS builder
WORKDIR /usr/src/app

RUN apk add --no-cache git

COPY phpunit.xml ./

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json ./
COPY composer.lock ./
RUN composer install

COPY src ./
COPY tests ./
COPY banco.sqlite ./

CMD ["./vendor/bin/phpunit"]
