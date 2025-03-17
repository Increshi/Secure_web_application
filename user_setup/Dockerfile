FROM php:8.1-cli

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your script
COPY create_users.php /app/create_users.php

WORKDIR /app

# Set the script as the entrypoint
CMD ["php", "create_users.php"]