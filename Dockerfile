FROM php:8.4-apache

# Install system dependencies and PHP extensions
RUN apt-get update \
  && apt-get upgrade -y \
  && apt-get install -y --no-install-recommends \
  curl \
  default-mysql-client \
  git \
  jq \
  libfreetype6-dev \
  libicu-dev \
  libjpeg-dev \
  libpng-dev \
  libzip-dev \
  unzip \
  xmlstarlet \
  && rm -rf /var/lib/apt/lists/* \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install mysqli pdo pdo_mysql intl gd zip

# Install Composer (multi-stage)
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

# Create necessary directories for Smarty and set permissions
RUN mkdir -p /var/www/smarty/templates_c \
  /var/www/smarty/cache \
  /var/www/smarty/configs \
  && chmod -R 777 /var/www/smarty


# Copy PHP import script
COPY ./tools/db_and_news_import.php /usr/local/bin/db_and_news_import.php
RUN chmod +x /usr/local/bin/db_and_news_import.php

# Expose port 80
EXPOSE 80


COPY ./tools/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
CMD ["/usr/local/bin/entrypoint.sh"]
