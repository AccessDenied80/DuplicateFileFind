FROM php:8.5-cli-alpine AS base

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

FROM base AS test

RUN composer install --no-interaction --no-autoloader --no-scripts
COPY src ./src
COPY tests ./tests
COPY phpunit.xml.dist ./
RUN composer dump-autoload

ENTRYPOINT ["vendor/bin/phpunit"]

FROM base AS app

COPY src ./src
RUN composer install --no-dev --no-interaction --optimize-autoloader

COPY start.php ./

ENTRYPOINT ["php", "start.php"]
