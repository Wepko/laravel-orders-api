#!/bin/bash
set -e

echo "Starting Laravel application..."

# Generate APP_KEY if not set
if [ -z "${APP_KEY}" ] || [ "${APP_KEY}" = "base64:" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Install composer dependencies (in case vendor was cleared)
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Create storage directories if needed
mkdir -p storage/logs storage/framework/{cache,sessions,views} storage/app/public bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Run migrations if not in testing
if [ "${APP_ENV}" != "testing" ]; then
    php artisan migrate --force --no-interaction || true
fi

echo "Laravel application ready!"
