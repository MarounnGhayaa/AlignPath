FROM php:8.2-cli

WORKDIR /var/www/html

# Minimal extensions for Laravel + MySQL
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
      --install-dir=/usr/local/bin --filename=composer

# Cache deps at build (fallback if no lock)
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-progress || true

# App code
COPY . .

# Dev php.ini overrides: more memory & upload size, log/show errors
RUN printf "log_errors=On\nerror_reporting=E_ALL\ndisplay_errors=On\n" > /usr/local/etc/php/conf.d/10-dev.ini \
 && printf "memory_limit=512M\npost_max_size=64M\nupload_max_filesize=64M\nmax_execution_time=120\n" > /usr/local/etc/php/conf.d/90-limits.ini
