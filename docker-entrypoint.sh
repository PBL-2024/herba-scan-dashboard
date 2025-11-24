#!/bin/sh

# Fix permissions for storage and bootstrap/cache
chmod -R 777 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# Execute the main command directly
exec "$@"
