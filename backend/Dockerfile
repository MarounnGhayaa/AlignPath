FROM php:8.2-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
      --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-progress || true

COPY . .
