FROM php:7.4-apache

WORKDIR /var/www/html

# Устанавливаем системные зависимости и PHP-расширения
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        autoconf \
        build-essential \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        libonig-dev \
        libmcrypt-dev \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd pdo pdo_mysql mbstring zip \
    && pecl install mcrypt-1.0.5 \
    && docker-php-ext-enable mcrypt \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Выравниваем UID/GID www-data под пользователя хоста (обычно 1000:1000)
RUN groupmod -o -g 1000 www-data && \
    usermod -o -u 1000 -g 1000 www-data && \
    install -d -m 775 -o www-data -g www-data /var/www/html

# Разрешаем .htaccess
RUN printf "<Directory /var/www/html>\n\
    AllowOverride All\n\
    Options Indexes FollowSymLinks\n\
    Require all granted\n\
</Directory>\n" > /etc/apache2/conf-available/allowoverride.conf \
    && a2enconf allowoverride

EXPOSE 80/tcp