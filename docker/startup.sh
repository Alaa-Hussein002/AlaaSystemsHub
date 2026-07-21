#!/bin/sh
set -e

echo "🚀 Starting Alaa Systems Hub API..."
echo "================================================"

# Laravel version
echo "📦 Laravel Version:"
php artisan --version 2>/dev/null || echo "⚠️  Laravel not ready"

# Generate APP_KEY
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# SSL Certificate
if [ -f "/var/www/ssl/aiven-ca.pem" ]; then
    echo "🔒 SSL Certificate: ✅ Found ($(wc -c < /var/www/ssl/aiven-ca.pem) bytes)"
else
    echo "❌ SSL Certificate not found!"
    exit 1
fi

# Connection info
if [ -n "$DATABASE_URL" ]; then
    echo "📊 Using DATABASE_URL connection"
    echo "   Format: mysql://user:***@host:port/database"
else
    echo "⚠️  DATABASE_URL not set, using individual variables"
fi

# Clear cache
echo "🧹 Clearing caches..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Test database connection
echo "⏳ Testing database connection..."

MAX_RETRIES=10
RETRY=0

while [ $RETRY -lt $MAX_RETRIES ]; do
    if php artisan db:show 2>&1 | grep -qi "mysql"; then
        echo "✅ Database connected successfully!"
        
        # Show MySQL version
        php artisan tinker --execute="
            \$version = DB::select('SELECT VERSION() as v')[0]->v ?? 'Unknown';
            echo '📊 MySQL Version: ' . \$version . PHP_EOL;
        " 2>/dev/null || true
        
        break
    else
        RETRY=$((RETRY + 1))
        if [ $RETRY -lt $MAX_RETRIES ]; then
            echo "   Attempt $RETRY/$MAX_RETRIES failed, retrying in 3s..."
            sleep 3
        else
            echo "❌ Could not connect to database after $MAX_RETRIES attempts"
            echo "🔍 Debugging info:"
            php artisan tinker --execute="
                try {
                    DB::connection()->getPdo();
                } catch (\Exception \$e) {
                    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
                }
            " 2>&1
            exit 1
        fi
    fi
done

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force --no-interaction

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force --class=RoleSeeder --no-interaction 2>/dev/null || echo "   Roles already exist"
php artisan db:seed --force --class=AdminUserSeeder --no-interaction 2>/dev/null || echo "   Admin already exists"
php artisan db:seed --force --class=PersonalProfileSeeder --no-interaction 2>/dev/null || echo "   Profile already exists"

# Storage link
echo "🔗 Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# Cache for production
echo "💾 Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "================================================"
echo "✅ Application ready and listening on port 8080"
echo "================================================"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf