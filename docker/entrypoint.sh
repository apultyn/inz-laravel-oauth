#!/bin/sh

php artisan migrate

php-fpm &

exec nginx -g 'daemon off;'