FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    sqlite \
    sqlite-dev \
    sqlite-libs \
    postgresql-dev \
    curl \
    git \
    unzip \
    libzip-dev \
    oniguruma-dev

RUN docker-php-ext-install pdo pdo_sqlite pdo_pgsql mbstring zip bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --ignore-platform-req=php --no-dev --optimize-autoloader --no-interaction

RUN chmod +x entrypoint.sh

EXPOSE 8000

CMD ["/app/entrypoint.sh"]
