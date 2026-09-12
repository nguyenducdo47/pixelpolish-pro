#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
    storage/app/tmp \
    storage/app/private/livewire-tmp \
    storage/app/public/avatars \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -d vendor ]; then
    COMPOSER_ALLOW_SUPERUSER=1 composer install --prefer-dist --no-interaction --no-ansi
fi

if [ ! -d node_modules ]; then
    npm install --ignore-scripts
fi

if [ ! -d public/build ] || [ -z "$(ls -A public/build 2>/dev/null)" ]; then
    npm run build
fi

if ! grep -qE '^APP_KEY=base64:' .env; then
    php artisan key:generate --force --no-interaction
fi

php artisan storage:link --force --no-interaction || true

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

if [ "${SKIP_DB_WAIT:-0}" != "1" ]; then
    echo "Waiting for MySQL..."
    i=0
    until php -r "new PDO(
        'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD') ?: ''
    );" 2>/dev/null; do
        i=$((i + 1))
        if [ "$i" -ge 60 ]; then
            echo "MySQL did not become ready in time."
            exit 1
        fi
        sleep 2
    done

    php artisan migrate --force --no-interaction

    if [ ! -f storage/app/.docker-seeded ]; then
        php artisan db:seed --force --no-interaction
        touch storage/app/.docker-seeded
    fi
fi

exec docker-php-entrypoint "$@"
