FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    mysql-client \
    nginx \
    supervisor \
    ca-certificates \
    openssl\
    bind-tools \
    netcat-openbsd

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Copy SSL certificate early
RUN mkdir -p /var/www/ssl
COPY ssl/aiven-ca.pem /var/www/ssl/

# Install dependencies WITHOUT running scripts
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-scripts

# Now copy the rest of the application
COPY . .

# Run composer scripts after all files are in place
RUN composer run-script post-autoload-dump --no-interaction || true

# Create necessary directories
RUN mkdir -p /var/www/storage/app/public/media/{articles,certificates,cv,education,experiences,icons,products,profile,projects,seo,social-icons,tool-icons} \
    && mkdir -p /var/www/storage/framework/{cache/data,sessions,views} \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# ✅ نسخ إعدادات PHP
COPY docker/php.ini /usr/local/etc/php/conf.d/uploads.ini

# Copy configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/startup.sh /usr/local/bin/startup.sh

RUN chmod +x /usr/local/bin/startup.sh



EXPOSE 8080

CMD ["/usr/local/bin/startup.sh"]