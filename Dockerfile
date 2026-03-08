# Dockerfile for development (all-in-one)
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    git \
    sqlite3 \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Enable Apache modules
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project
COPY . /app

# Install PHP dependencies
RUN composer install

# Install Node dependencies
RUN npm install

# Copy Apache config
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Create storage directory and set permissions
RUN mkdir -p /app/storage/logs \
    && chmod -R 775 /app/storage \
    && chown -R www-data:www-data /app

# Expose port
EXPOSE 80

# Default command
CMD ["apache2ctl", "-D", "FOREGROUND"]
