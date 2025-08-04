
# Use the official PHP 7 image with Apache
FROM php:7.4-apache

# Install PHP extensions (mysqli for MySQL, pdo, etc.) and required extensions for Alexandria
RUN apt-get update \
    && apt-get install -y default-mysql-client libfreetype6-dev libicu-dev libjpeg-dev libpng-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql intl gd zip \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite (if needed)
RUN a2enmod rewrite


# Copy project files to the Apache document root
COPY ./www /var/www/html
COPY ./includes /var/www/includes

# Copy smarty folder and rename to smarty-4.1.1 as required by the app (must come after www to avoid overwrite)
COPY ./smarty /var/www/smarty
COPY ./smarty-4.1.1 /var/www/smarty-4.1.1


# Ensure smarty/templates_c is writable
RUN mkdir -p /var/www/smarty/templates_c \
    && chmod -R 777 /var/www/smarty/templates_c

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
