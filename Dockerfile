FROM php:7.0.10-apache

RUN docker-php-ext-install opcache \
 && apt-get update && apt-get install -y libsodium-dev \
 && pecl install libsodium \
 && docker-php-ext-enable libsodium \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY html/ /var/www/html/
