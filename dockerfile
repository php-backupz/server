FROM php:7.1-fpm-alpine

RUN apk add --no-cache \
    autoconf \
    binutils \
    curl-dev \
    freetype-dev \
    g++ \
    gcc \
    gmp-dev \
    libcurl \
    libjpeg-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libxml2-dev \
    make \
    postfix

RUN docker-php-ext-install -j "$(getconf _NPROCESSORS_ONLN)" \
    curl \
    exif \
    fileinfo \
    json \
    pdo_mysql \
    xml \
    zip

RUN mkdir /var/www/html/var && chmod 777 /var/www/html/var