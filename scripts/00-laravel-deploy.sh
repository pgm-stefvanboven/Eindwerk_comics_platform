#!/usr/bin/env bash
echo "Running composer"
ddev composer global require hirak/prestissimo
ddev composer install --no-dev --working-dir=/var/www/html

echo "Caching config..."
ddev artisan config:cache

echo "Caching routes..."
ddev artisan route:cache

echo "Running migrations..."
ddev artisan migrate --force