FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    ca-certificates

# Install gosu for user switching
RUN dpkgArch="$(dpkg --print-architecture | awk -F- '{ print $NF }')" \
    && curl -fsSL "https://github.com/tianon/gosu/releases/download/1.17/gosu-$dpkgArch" -o /usr/local/bin/gosu \
    && chmod +x /usr/local/bin/gosu

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Fix git dubious ownership
RUN git config --system --add safe.directory /var/www

# Set working directory
WORKDIR /var/www

# Copy composer files first
COPY composer.json composer.lock ./

# Install dependencies as root
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-autoloader

# Copy application files with correct ownership
COPY --chown=www-data:www-data . /var/www

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set proper permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Complete composer installation as root first
RUN cd /var/www && composer dump-autoload --optimize

# Expose port 9000 and start php-fpm server
EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
