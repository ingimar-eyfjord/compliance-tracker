FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        libpq-dev \
        libzip-dev \
        unzip \
        libonig-dev \
        libxml2-dev \
        libssl-dev \
        libicu-dev \
        libpng-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        bcmath \
        intl \
        pcntl \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.enable_cli=1'; \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.validate_timestamps=0'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy composer files and install dependencies (cached when possible)
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-interaction --no-ansi --no-scripts

# Copy application code (overridden by bind mount in dev)
COPY . .

# Ensure node_modules exists and is writable for the non-root user
RUN mkdir -p node_modules /var/www/.npm \
    && chown -R www-data:www-data node_modules /var/www/.npm

# Run framework discovery now that code is present
RUN php artisan package:discover --ansi

# Ensure storage bootstrap writable
RUN chown -R www-data:www-data storage bootstrap/cache

# Prepare runtime opcache override file
RUN touch /usr/local/etc/php/conf.d/zz-runtime-opcache.ini \
    && chown www-data:www-data /usr/local/etc/php/conf.d/zz-runtime-opcache.ini \
    && chmod 664 /usr/local/etc/php/conf.d/zz-runtime-opcache.ini

COPY docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
