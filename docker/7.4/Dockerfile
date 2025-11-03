FROM php:7.4-apache

ARG WWWGROUP
ARG WWWUSER

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


# Разрешаем .htaccess
RUN a2enmod rewrite \
    && printf "<Directory /var/www/html>\n\
    AllowOverride All\n\
    Options Indexes FollowSymLinks\n\
    Require all granted\n\
</Directory>\n" > /etc/apache2/conf-available/allowoverride.conf \
    && a2enconf allowoverride

RUN groupadd --force -g $WWWGROUP dcms && \
    useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u $WWWUSER dcms

EXPOSE 80/tcp