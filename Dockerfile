FROM php:8.0-apache

# Install dependency sistem
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libzip-dev

# Install extension PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring zip

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