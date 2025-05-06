# 1) Base image: official PHP 8.2 with Apache
FROM php:8.2-apache AS base

# Set working directory
WORKDIR /var/www/html

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Ensure environment vars for Apache doc root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 2) Dependencies — install system libs & PHP extensions
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libzip-dev zip git unzip libonig-dev \
 && docker-php-ext-install pdo_mysql mbstring zip \
 && rm -rf /var/lib/apt/lists/*

# 3) Composer — copy only the files needed to install deps
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# 4) Application code
COPY . .

# 5) Permissions & final tweaks
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
