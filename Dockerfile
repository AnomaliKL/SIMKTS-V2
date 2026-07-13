# ==========================================
# STAGE 1: Build Assets (Tailwind CSS & Alpine.js)
# ==========================================
FROM node:20-alpine AS asset-builder
WORKDIR /app
COPY package*.json vite.config.js tailwind.config.js ./
COPY resources/ ./resources/
RUN npm ci && npm run build

# ==========================================
# STAGE 2: Application Runtime (PHP-FPM & Nginx)
# ==========================================
FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libxml2-dev

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Copy Composer dari image resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . .

# Copy compiled assets dari Stage 1 (Tailwind & Alpine)
COPY --from=asset-builder /app/public/build ./public/build

# Install PHP dependencies (Pakai PHP 8.4 bawaan container)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Set permission folder storage & cache agar bisa ditulis oleh server
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy file konfigurasi Nginx dan Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Port 8080 untuk Railway
EXPOSE 8080

# Jalankan Nginx dan PHP-FPM secara bersamaan lewat Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]