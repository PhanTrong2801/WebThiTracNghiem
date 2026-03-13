# GIAI ĐOẠN 1: Build Frontend (React/Vite)
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# GIAI ĐOẠN 2: Chạy Backend (PHP & Nginx)
FROM php:8.2-fpm-alpine

# Cài đặt các thư viện hệ thống siêu nhẹ (Alpine)
RUN apk add --no-cache \
    nginx \
    curl \
    libpng-dev \
    libxml2-dev \
    oniguruma-dev \
    zip \
    unzip

# Cài đặt PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring gd bcmath

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Copy kết quả build từ Giai đoạn 1 sang (Loại bỏ node_modules nặng nề)
COPY --from=frontend-builder /app/public/build ./public/build

# Cài đặt dependencies Laravel (Không cài các gói dev)
RUN composer install --no-dev --optimize-autoloader

# Cấp quyền
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Cấu hình Nginx
COPY ./docker/nginx.conf /etc/nginx/http.d/default.conf

# Lệnh khởi động (Tạm thời bỏ migrate/seed để deploy nhanh hơn)
CMD nginx && php-fpm