#!/bin/sh
# start.sh

# Substitute the $PORT variable in the Nginx template
envsubst '$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Start PHP-FPM in the background
php-fpm -D

# Start Nginx in the foreground (keeps container alive)
nginx -g 'daemon off;'