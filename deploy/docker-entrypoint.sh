#!/bin/sh
# Run the Laravel scheduler
chown -R www-data:www-data /var/www/html && chmod -R 775 /var/www/html/storage && chmod -R 775 /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/vendor && chmod -R 775 /var/www/html/vendor

# Ensure log directories exist and have correct permissions
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage/logs
chmod -R 775 /var/www/html/storage/logs

exec "$@"