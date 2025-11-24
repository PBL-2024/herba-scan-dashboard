FROM php:8.3-fpm-alpine AS build

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    bash \
    nodejs \
    npm \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    oniguruma-dev \
    gettext-dev \
    libavif-dev \
    libwebp-dev \
    libxpm-dev \
    curl

# Install PHP extensions via installer (lebih cepat daripada compile manual)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    gd \
    zip \
    pdo_mysql \
    pdo_pgsql \
    bcmath \
    exif \
    gettext \
    opcache \
    intl

# Install Composer
COPY --from=composer:2.7.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Debug: List files to verify copy
RUN ls -la /var/www/html

# Copy only dependency files first (agar cache efektif)
COPY composer.json ./
COPY composer.lock ./ 

# Install PHP dependencies
RUN composer install --no-dev --prefer-dist --no-progress --no-scripts

# Copy source code
COPY . .

# Debug: List files to verify copy
RUN ls -la /var/www/html

# Build frontend assets
# RUN npm run prod

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

# =========================
# Stage 2: Production
# =========================
FROM php:8.3-fpm-alpine

# Install runtime dependencies
RUN apk add --no-cache \
    nginx \
    bash \
    curl \
    icu \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    postgresql-libs \
    oniguruma \
    gettext \
    libavif \
    libwebp \
    libxpm

# Install PHP extensions in production stage
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    gd \
    zip \
    pdo_mysql \
    pdo_pgsql \
    bcmath \
    exif \
    gettext \
    opcache \
    intl

# Copy application code
COPY --from=build /var/www/html /var/www/html

# Copy configs
COPY deploy/nginx.conf /etc/nginx/http.d/default.conf
COPY deploy/php.ini "$PHP_INI_DIR/conf.d/app.ini"
COPY deploy/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

VOLUME ["/var/www/html/storage/app"]

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["sh", "-c", "nginx && php-fpm"]