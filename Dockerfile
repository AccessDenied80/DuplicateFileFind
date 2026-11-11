FROM php:8.5-cli-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
COPY src ./src
RUN composer install --no-dev --no-interaction --optimize-autoloader

COPY start.php ./

ENTRYPOINT ["php", "start.php"]
