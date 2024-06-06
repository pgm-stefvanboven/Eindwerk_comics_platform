# Basis image
FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Optimize application
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]