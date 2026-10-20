# Build the Vite assets with the lockfile used by the project.
FROM node:22-bookworm-slim AS frontend

WORKDIR /var/www/html

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# Render supplies PORT at runtime. The PHP CLI server is sufficient for the
# free-plan demonstration service and keeps the container intentionally small.
FROM php:8.2-cli-bookworm

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        git \
        libicu-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
COPY --from=frontend /var/www/html/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 10000

CMD ["sh", "-c", "php artisan storage:link --force && exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
