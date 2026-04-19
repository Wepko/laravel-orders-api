FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    linux-headers \
    autoconf \
    dpkg-dev \
    dpkg \
    file \
    g++ \
    gcc \
    libc-dev \
    make \
    pkgconf \
    re2c \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql/ && \
    docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        sockets

# Install Redis extension (правильный способ для PHP 8.4)
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first (better layer caching)
COPY src/composer.json src/composer.lock* ./

# Install dependencies (--no-scripts to avoid running post-autoload-dump before artisan exists)
RUN composer install --no-interaction --no-scripts

# Copy application source (исключая vendor)
COPY src/ .

# Clear cache and optimize
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Switch to non-root user
USER www-data

EXPOSE 9000
CMD ["php-fpm"]