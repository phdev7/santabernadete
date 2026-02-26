#!/usr/bin/env sh

set -eu

cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

if [ -z "${APP_KEY:-}" ] && [ "${APP_KEY_GENERATE:-false}" = "true" ]; then
    php artisan key:generate --force || true
fi

php artisan package:discover --ansi || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force || true

    if [ "${RUN_SEEDER:-false}" = "true" ]; then
        php artisan db:seed --force || true
    fi
fi

if [ "${APP_ENV:-production}" = "production" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

exec "$@"
