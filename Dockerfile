FROM php:8.4-fpm-alpine AS assets

WORKDIR /var/www/html

RUN apk add --no-cache \
    nodejs \
    npm

COPY package*.json vite.config.js ./
COPY src/ ./src/
COPY public/ ./public/
RUN npm ci && npm run build

FROM php:8.4-fpm-alpine AS php-runtime

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    curl \
    icu-dev \
    oniguruma-dev \
    libzip-dev

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    intl \
    mbstring \
    zip

COPY app/ ./app/
COPY database/schema.sql ./database/schema.sql
COPY database/migrations/ ./database/migrations/
COPY scripts/ ./scripts/
COPY --from=assets /var/www/html/public ./public/

EXPOSE 9000

FROM nginx:stable-alpine AS nginx-runtime

WORKDIR /var/www/html

COPY --from=assets /var/www/html/public ./public/
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
