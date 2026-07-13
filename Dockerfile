FROM php:8.4-cli

# 1. Instal dependensi sistem dan ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd

# 2. Instal Node.js & NPM (Untuk Build Tailwind)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 3. Instal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Salin semua file proyek ke dalam container
WORKDIR /var/www/html
COPY . .

# 5. Jalankan composer install (Menggunakan PHP 8.4 bawaan container ini)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 6. Jalankan NPM Install & NPM Build untuk compile Tailwind CSS & Alpine.js
RUN npm install && npm run build

# 7. Atur permissions folder storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Sesuaikan port ke 8080 untuk Railway
EXPOSE 8080

# Jalankan optimasi dan server dengan format shell form agar multi-command berjalan lancar
CMD php artisan optimize && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=8080