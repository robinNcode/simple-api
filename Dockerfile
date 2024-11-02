# Use the official PHP image with the latest PHP version
FROM php:latest

# Set the working directory in the container
WORKDIR /var/www/html

# Install system dependencies and PHP extensions as needed
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath opcache intl mysqli

# Copy the current project to the working directory in the container
COPY . /var/www/html

# Set correct file permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies using Composer
RUN composer install --no-interaction --optimize-autoloader

# Clean up APT when done
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Expose port 8081 for external access
EXPOSE 8081

# Start the PHP built-in server
CMD ["php", "-S", "0.0.0.0:8081", "-t", "/var/www/html/public"]
