
# Use the official PHP 8.0 image with Apache
FROM php:8.0-apache

# Install system dependencies
RUN apt-get update \
    && apt-get install -y \
        default-mysql-client \
        git \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libpng-dev \
        libzip-dev \
        unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql intl gd zip

# Install Composer
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www

# Copy composer files first for better Docker layer caching
COPY composer.json composer.lock* ./

# Install PHP dependencies (including dev dependencies for development)
RUN composer install --optimize-autoloader

# Copy includes directory and Smarty assets (templates/configs)
COPY ./includes /var/www/includes
COPY ./smarty/templates /var/www/smarty/templates
# If you add configs in repo later, uncomment the next line
# COPY ./smarty/configs /var/www/smarty/configs

# Create necessary directories for Smarty and set permissions
RUN mkdir -p /var/www/smarty/templates_c \
             /var/www/smarty/cache \
             /var/www/smarty/configs \
    && chmod -R 777 /var/www/smarty

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
