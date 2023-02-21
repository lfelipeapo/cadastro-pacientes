#!/bin/bash

cd /var/www/html

php artisan serve &
php artisan queue:work &

exec "$@"
