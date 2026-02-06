FROM php:7.4-apache

# Install system deps
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libzip-dev libpng-dev libonig-dev libxml2-dev \
        default-mysql-client git unzip && \
    rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install mysqli pdo_mysql mbstring zip gd xml intl

# Apache modules
RUN a2enmod rewrite headers

# PHP ini tweaks
RUN { \
    echo "upload_max_filesize=32M"; \
    echo "post_max_size=32M"; \
    echo "memory_limit=256M"; \
  } > /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

# Copy app
COPY . /var/www/html

# Default dev login; override via env/.env
ENV DEV_LOGIN=1

# Expose port
EXPOSE 80
