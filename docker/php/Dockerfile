# Версия задаётся в .env (PHP_VERSION). Движок требует 8.1+;
# стенд и разработка — на 8.5.
ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    curl \
    ffmpeg \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd pdo_mysql zip exif \
    # pdo встроен всегда, а mbstring и opcache — начиная с образа 8.5:
    # повторная установка встроенного расширения обрывает сборку
    && (php -m | grep -qi "^mbstring$" || docker-php-ext-install -j"$(nproc)" mbstring) \
    && (php -m | grep -qi "opcache"   || docker-php-ext-install -j"$(nproc)" opcache)

COPY php.ini /usr/local/etc/php/conf.d/dcms.ini

# PHP-FPM работает от uid хоста, иначе движок не запишет sys/ini, sys/tmp,
# files/: каталог проекта примонтирован с правами хозяина. Значения берутся
# из .env (UID/GID), по умолчанию — обычные 1000:1000.
ARG UID=1000
ARG GID=1000
RUN sed -i "s/^user = www-data/user = ${UID}/" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s/^group = www-data/group = ${GID}/" /usr/local/etc/php-fpm.d/www.conf

EXPOSE 9000
