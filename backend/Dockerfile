# Use PHP 8.1 Apache as the base image
FROM php:8.1-apache

# Install required PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    && docker-php-ext-install pdo pdo_mysql

# Copy Composer from the Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer.json before installing dependencies
COPY composer.json ./

# Install dependencies using Composer
RUN composer install --no-dev --no-interaction --prefer-dist

# Copy all files into the container
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

