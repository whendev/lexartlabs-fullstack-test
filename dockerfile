FROM php:7.4.15-apache
RUN a2enmod rewrite
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install pdo pdo_mysql
EXPOSE 80