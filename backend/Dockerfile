# # Use PHP 8.1 Apache as the base image
# FROM php:8.1-apache

# # Install required PHP extensions and dependencies
# RUN apt-get update && apt-get install -y \
#     git \
#     unzip \
#     zip \
#     && docker-php-ext-install pdo pdo_mysql

# # Copy Composer from the Composer image
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Set working directory
# WORKDIR /var/www/html

# # Copy composer.json before installing dependencies
# COPY composer.json ./

# # Install dependencies using Composer
# RUN composer install --no-dev --no-interaction --prefer-dist

# # Copy all files into the container
# COPY . .

# # Set permissions
# RUN chown -R www-data:www-data /var/www/html

# # Expose port 80
# EXPOSE 443

# # Start Apache
# CMD ["apache2-foreground"]

# Use PHP 8.1 Apache as the base image
FROM php:8.1-apache

# Install required PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    && docker-php-ext-install pdo pdo_mysql

# Enable SSL and necessary Apache modules
RUN a2enmod ssl

# Create SSL directory
RUN mkdir -p /etc/apache2/ssl

# Copy SSL certificates from the local system into the container
COPY ./ssl_certificate/certificate.crt /etc/apache2/ssl/backend.crt
COPY ./ssl_certificate/private.key /etc/apache2/ssl/backend.key

# Copy custom Apache SSL config
COPY ./apache/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

# Enable the SSL site configuration
RUN a2ensite default-ssl

# Expose HTTPS port
EXPOSE 443

# Start Apache
CMD ["apache2-foreground"]
