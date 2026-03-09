#!/bin/sh

# Only run caching if APP_KEY exists (env injected at runtime)
if [ ! -z "$APP_KEY" ]; then
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan view:clear
fi

# Start Apache in foreground
apache2-foreground