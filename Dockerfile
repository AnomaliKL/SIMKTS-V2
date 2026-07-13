# ==========================================
# STAGE 1: Build Assets (Tailwind CSS & Alpine.js)
# ==========================================
FROM node:20-alpine AS asset-builder
WORKDIR /app
COPY package*.json vite.config.js tailwind.config.js ./
COPY resources/ ./resources/
RUN npm ci && npm run build

# ==========================================
# STAGE 2: Application Runtime (PHP Bawaan Laravel)
# ==========================================
FROM php:8.4-cli-alpine

# Install sistem dependencies untuk Laravel
RUN apk add --no-cache \
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

# Install ekstensi PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . .

# Copy compiled assets dari Stage 1
COPY --from=asset-builder /app/public/build ./public/build

# Install PHP dependencies menggunakan PHP 8.4 di container
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Set permission folder storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Port default untuk Railway
EXPOSE 8080

# Jalankan server bawaan Laravel secara langsung tanpa Nginx & Supervisor
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]