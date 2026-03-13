# Sử dụng PHP 8.2 (hoặc 8.4 nếu bạn đã nâng cấp)
FROM php:8.2-fpm

# Cài đặt các thư viện hệ thống cần thiết
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx

# Cài đặt PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www

# Copy toàn bộ code vào container
COPY . .

# Cài đặt Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Cài đặt Node.js và Build React (Inertia)
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# Cấp quyền cho thư mục storage
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copy file cấu hình Nginx (Sẽ tạo ở bước 2)
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

# Chạy lệnh khởi động
CMD php artisan migrate --force && \
    php artisan db:seed --force && \
    service nginx start && \
    php artisan config:cache && \
    php artisan route:cache && \
    php-fpm