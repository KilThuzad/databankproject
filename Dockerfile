# Dockerfile
FROM php:8.2-fpm as build

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files (including .env.build – see note below)
COPY . .

# Rename the dummy environment file to .env for the build process
# (Make sure you have created .env.build in your project root)
COPY .env.build .env

# Install PHP dependencies (production only)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Cache Laravel config, routes, and views
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Remove the dummy .env for security (so it's not in the final image)
RUN rm .env

# Set permissions for storage and bootstrap cache
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy custom Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf.template

# Copy startup script
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Expose the dynamic port (Render will set $PORT)
EXPOSE $PORT

# Start services
CMD ["/start.sh"]