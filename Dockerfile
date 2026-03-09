# Use official PHP image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Run composer install
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets (optional, only if using Vite/Laravel Mix)
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# **Fix Forbidden**: set Apache DocumentRoot to Laravel public
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/apache2.conf

# Expose default Apache port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]