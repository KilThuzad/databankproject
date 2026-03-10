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

# Copy application files (this will be done by Git, but we copy everything from context)
COPY . .

# Install PHP dependencies (production only)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Cache Laravel config and routes for better performance
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Set permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy custom Nginx configuration (create this file next)
COPY nginx.conf /etc/nginx/nginx.conf.template

# Copy startup script
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Expose the dynamic port (Render will set $PORT)
EXPOSE $PORT

# Start services
CMD ["/start.sh"]