# syntax=docker/dockerfile:1.7

FROM php:8.4-apache
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
      libzip-dev libonig-dev unzip git curl \
 && docker-php-ext-install pdo_mysql mysqli mbstring zip \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV APACHE_DOCUMENT_ROOT=/var/www/html/src/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
 && printf '<Directory ${APACHE_DOCUMENT_ROOT}>\n    Options Indexes FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-available/app-docroot.conf \
 && a2enconf app-docroot

EXPOSE 80
