#!/bin/sh
composer install --no-interaction --optimize-autoloader
php artisan migrate:fresh
php artisan db:seed
chown -R www-data:www-data storage bootstrap/cache
apachectl -D FOREGROUND
exec "$@"

