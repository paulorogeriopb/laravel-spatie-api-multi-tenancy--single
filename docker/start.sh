#!/bin/bash
set -e

if [ "$APP_ENV" = "production" ]; then
    echo "Building assets for production..."
    npm run build
    echo "Starting php-fpm..."
    exec php-fpm
else
    echo "Starting vite in dev mode and php-fpm..."
    npm run dev &
    exec php-fpm
fi
