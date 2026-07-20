FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    nginx \
    curl \
    git \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    oniguruma-dev \
    mysql-client \
    bash \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
       pdo_mysql \
       mysqli \
       gd \
       zip \
       bcmath \
       opcache \
       pcntl \
       mbstring \
       exif

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY src/composer.json src/composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

COPY src/package.json src/package-lock.json* ./
RUN npm install --ignore-scripts

COPY src/ .

COPY docker/php/php.ini /usr/local/etc/php/conf.d/coffee-crm.ini

RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
