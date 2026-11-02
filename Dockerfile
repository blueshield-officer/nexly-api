FROM dunglas/frankenphp:php8.4-bookworm

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    default-mysql-client \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install \
        gd \
        zip \
        pdo \
        pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN touch .env

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV APP_RUNTIME="Runtime\\FrankenPhpSymfony\\Runtime"

RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

EXPOSE 80

COPY Caddyfile /etc/frankenphp/Caddyfile

CMD ["sh", "-c", "\
    mkdir -p \
        /app/storage/public/images/school \
        /app/storage/public/images/district \
        /app/storage/public/images/other \
        /app/storage/public/images/sis \
    && cp -rn /app/resources/storage-seed/. /app/storage/public/ \
    && php bin/console app:create-storage-link \
    && exec frankenphp run --config /etc/frankenphp/Caddyfile \
"]
