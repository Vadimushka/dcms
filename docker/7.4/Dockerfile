FROM php:7.4-fpm

WORKDIR /var/www/html

# Устанавливаем системные зависимости и PHP-расширения
RUN apt-get update && apt-get install -y --no-install-recommends \
    autoconf \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libmcrypt-dev \
    curl \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd pdo pdo_mysql mbstring zip

RUN apt-get update && apt-get install -y libmcrypt-dev \
    && pecl install mcrypt-1.0.5 \
    && docker-php-ext-enable mcrypt

# PHP-настройки (были в .htaccess для mod_php)
RUN { \
    echo "upload_max_filesize = 100M"; \
    echo "post_max_size = 100M"; \
    echo "display_errors = Off"; \
    echo "default_charset = UTF-8"; \
    echo "session.use_trans_sid = 1"; \
    echo "arg_separator.output = &amp;"; \
} > /usr/local/etc/php/conf.d/dcms.ini

EXPOSE 9000
