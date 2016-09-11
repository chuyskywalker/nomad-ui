FROM php:7.0.10-apache
RUN docker-php-ext-install opcache
COPY html/ /var/www/html/
