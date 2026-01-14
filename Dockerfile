FROM php:8.2-fpm-alpine

# Install dependencies sistem & library SQLite
RUN apk add --no-cache \
    bash \
    curl \
    libpng-dev \
    libxml2-dev \
    sqlite-dev \
    zip \
    unzip \
    git

# Install ekstensi PHP yang dibutuhkan
RUN docker-php-ext-install pdo pdo_sqlite bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Beri izin akses ke folder penting (termasuk folder database untuk SQLite)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database

# Expose port 8000
EXPOSE 8000

# Jalankan server internal PHP langsung ke folder public untuk menghindari error "index.php not found"
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]