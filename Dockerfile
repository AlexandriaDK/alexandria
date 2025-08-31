FROM php:8.4-fpm

# Install system dependencies and PHP extensions
RUN apt-get update \
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

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better Docker layer caching
COPY composer.json composer.lock* ./

# Install PHP dependencies (including dev dependencies for development)
RUN composer install --optimize-autoloader --no-dev || composer install --optimize-autoloader --disable-tls --no-dev

# Copy includes directory and Smarty assets (templates/configs)
COPY ./includes /var/www/html/includes
COPY ./smarty/templates /var/www/html/smarty/templates

# Create necessary directories for Smarty and set permissions
RUN mkdir -p /var/www/html/smarty/templates_c \
  /var/www/html/smarty/cache \
  /var/www/html/smarty/configs \
  && chmod -R 777 /var/www/html/smarty


# Copy PHP import script
COPY ./tools/db_and_news_import.php /usr/local/bin/db_and_news_import.php
RUN chmod +x /usr/local/bin/db_and_news_import.php

# Expose port 9000 for PHP-FPM
EXPOSE 9000

COPY ./tools/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
CMD ["/usr/local/bin/entrypoint.sh"]
