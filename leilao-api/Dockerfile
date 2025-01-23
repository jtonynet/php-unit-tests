FROM php:8.3-alpine AS builder
WORKDIR /usr/src/app

RUN apk add --no-cache git

COPY phpunit.xml ./

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer require --dev phpunit/phpunit ^10

COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY src ./
COPY tests ./

