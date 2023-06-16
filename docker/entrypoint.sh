#!/usr/bin/env sh
set -e

php artisan optimize:clear
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache
php artisan horizon:install
php artisan horizon:publish
php artisan migrate

# Start cron daemon.
crond -b -l 8

# Run supervisor
supervisord -c /etc/supervisor/conf.d/supervisord.conf

# Start PHP FPM
php-fpm -D

# Run Nginx
nginx -g 'daemon off;'
