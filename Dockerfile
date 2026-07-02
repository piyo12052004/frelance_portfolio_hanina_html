FROM php:8.0-apache

# Install dependency sistem
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Konfigurasi GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install extension PHP
RUN docker-php-ext-install \
    gd \
    pdo \
    pdo_mysql \
    mbstring \
    zip

# Enable apache rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project
COPY . .

# Install dependency PHP
RUN composer install --no-dev --optimize-autoloader

# Permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80