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
    openssl

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Copy SSL certificate for Aiven
RUN mkdir -p /var/www/ssl
COPY ssl/aiven-ca.pem /var/www/ssl/

# Install dependencies with increased timeout
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-scripts

# Run post-install scripts separately
RUN composer run-script post-autoload-dump --no-interaction || true

# Create necessary directories
RUN mkdir -p /var/www/storage/app/public/media/{articles,certificates,cv,education,experiences,icons,products,profile,projects,seo,social-icons,tool-icons} \
    && mkdir -p /var/www/storage/framework/{cache,sessions,views} \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Copy configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/startup.sh /usr/local/bin/startup.sh

RUN chmod +x /usr/local/bin/startup.sh

EXPOSE 8080

CMD ["/usr/local/bin/startup.sh"]