#!/bin/bash

# Wait for database to be ready
echo "Waiting for database to be ready..."
until nc -z db 3306; do
  echo "Database is not ready yet..."
  sleep 2
done
echo "Database is ready!"

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env || echo "APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=herba_scan_dashboard
DB_USERNAME=root
DB_PASSWORD=root" > .env
fi

# Generate application key
php artisan key:generate --force --no-interaction

# Create necessary directories and set permissions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/bootstrap/cache

# Set permissions
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Run migrations
php artisan migrate --force --no-interaction || echo "Migration failed, but continuing..."

# Create storage link
php artisan storage:link --force || echo "Storage link creation failed, but continuing..."

# Cache config (only if not in development)
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "Laravel application is ready!"

# Start Apache
exec apache2-foreground