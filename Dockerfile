FROM composer:2@sha256:af98f42dfff7c68ba8d53c2164fd9fde1087b7d449514baa38c418b1f6bc4bac AS composer

FROM php:8.4-cli-bookworm@sha256:836ac6c672d1372a47c8fd61b625015bd07760f493fb2eaab2087636498a2b4b

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev libzip-dev unzip \
    && docker-php-ext-install -j2 bcmath mbstring pcntl pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY docker/php/watchdog.ini /usr/local/etc/php/conf.d/watchdog.ini

WORKDIR /app

ENV COMPOSER_CACHE_DIR=/tmp/composer-cache

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000", "--env=testing"]
