FROM php:8.3-fpm

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get update \
 && apt-get install -y libpq-dev git unzip curl libcurl4-openssl-dev \
    libonig-dev libxml2-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_pgsql curl mbstring xml bcmath ctype zip gd fileinfo

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN { \
        echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)"; \
        echo "xdebug.mode=debug"; \
        echo "xdebug.start_with_request=yes"; \
        echo "xdebug.discover_client_host=true"; \
        echo "xdebug.client_host=host.docker.internal"; \
        echo "xdebug.client_port=9003"; \
        echo "xdebug.log=/tmp/xdebug.log"; \
        echo "xdebug.max_nesting_level=512"; \
    } > /usr/local/etc/php/conf.d/xdebug.ini

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

COPY . .
RUN composer install --no-interaction --optimize-autoloader --no-scripts \
 && chown -R www-data:www-data storage bootstrap/cache

COPY .docker/php/entrypoint.sh /usr/local/bin/docker-custom-entrypoint
RUN chmod +x /usr/local/bin/docker-custom-entrypoint
ENTRYPOINT ["docker-custom-entrypoint"]
CMD ["php-fpm"]
