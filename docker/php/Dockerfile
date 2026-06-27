FROM php:8.3-fpm-alpine

# Системные зависимости и PHP-расширения
# Устанавливаем как dev-, так и runtime-пакеты, затем удаляем только dev-
RUN apk add --no-cache \
        bash git curl zip unzip \
        libpng freetype libjpeg-turbo \
        libpng-dev freetype-dev libjpeg-turbo-dev \
        icu-libs libzip icu-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql intl zip bcmath gd pcntl \
    && apk del --no-cache libpng-dev freetype-dev libjpeg-turbo-dev icu-dev libzip-dev

# Composer
ENV COMPOSER_HOME=/tmp/composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Конфиг PHP (опционально можно добавить свои .ini)
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/zzz-custom.ini \
    && echo "upload_max_filesize=32M" >> /usr/local/etc/php/conf.d/zzz-custom.ini \
    && echo "post_max_size=32M" >> /usr/local/etc/php/conf.d/zzz-custom.ini \
    && echo "max_execution_time=120" >> /usr/local/etc/php/conf.d/zzz-custom.ini

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm", "-F"]
