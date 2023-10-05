FROM php:8.2.3-apache

RUN apt-get update

# Install tools required for build stage
RUN apt-get install -fyqq \
	bash curl wget rsync ca-certificates openssl ssh git tzdata openntpd \
	libxrender1 fontconfig libc6 \
	mariadb-client gnupg binutils-gold autoconf \
	g++ gcc gnupg gettext libgcc1 make python3 libicu-dev

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
 && chmod 755 /usr/bin/composer

# Install additional PHP libraries
RUN docker-php-ext-install bcmath pdo_mysql pdo mysqli intl
RUN docker-php-ext-enable bcmath pdo_mysql pdo mysqli intl

# Install libraries for compiling GD, then build it
RUN apt-get install -fyqq libfreetype6 libfreetype6-dev libpng16-16 libpng-dev libjpeg62-turbo libjpeg62-turbo-dev \
 && docker-php-ext-install gd \
 && apt-get remove -fyqq libfreetype6-dev libpng-dev libjpeg62-turbo-dev

# Add ZIP archives support
RUN apt-get install -fyqq zip libzip-dev \
 && docker-php-ext-install zip \
 && apt-get remove -fyqq libzip-dev

# Install xdebug
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

RUN a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!/var/www/html/www!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/www!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable XDebug
ADD xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /app