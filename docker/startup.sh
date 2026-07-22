#!/bin/sh
set -e

echo "🚀 Starting Alaa Systems Hub API..."
echo "================================================"

# Laravel version
echo "📦 Laravel Version:"
php artisan --version 2>/dev/null || echo "⚠️  Laravel not ready"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# SSL Certificate check
if [ -f "/var/www/ssl/aiven-ca.pem" ]; then
    echo "🔒 SSL Certificate: ✅ Found ($(wc -c < /var/www/ssl/aiven-ca.pem) bytes)"
else
    echo "❌ SSL Certificate not found!"
    exit 1
fi

# Show DB configuration (without password)
echo "📊 Database Configuration:"
echo "   Host: ${DB_HOST:-not set}"
echo "   Port: ${DB_PORT:-not set}"
echo "   Database: ${DB_DATABASE:-not set}"
echo "   Username: ${DB_USERNAME:-not set}"
echo "   SSL CA: ${MYSQL_ATTR_SSL_CA:-not set}"

# Clear all caches BEFORE connecting to DB
echo "🧹 Clearing ALL caches..."
rm -rf /var/www/bootstrap/cache/*.php
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Test database connection with detailed error
echo "⏳ Testing database connection..."

MAX_RETRIES=15
RETRY=0

while [ $RETRY -lt $MAX_RETRIES ]; do
    # Test using tinker for better error messages
    DB_TEST=$(php artisan tinker --execute="
        try {
            \$pdo = DB::connection()->getPdo();
            echo 'SUCCESS';
        } catch (\Exception \$e) {
            echo 'ERROR: ' . \$e->getMessage();
        }
    " 2>&1)
    
    if echo "$DB_TEST" | grep -q "SUCCESS"; then
        echo "✅ Database connected successfully!"
        
        # Show database info
        php artisan tinker --execute="
            try {
                \$db = DB::connection()->getDatabaseName();
                \$host = DB::connection()->getConfig('host');
                \$port = DB::connection()->getConfig('port');
                echo '📊 Connected to: ' . \$db . ' at ' . \$host . ':' . \$port . PHP_EOL;
                
                \$version = DB::select('SELECT VERSION() as v')[0]->v ?? 'Unknown';
                echo '📊 MySQL Version: ' . \$version . PHP_EOL;
                
                \$tables = DB::select('SHOW TABLES');
                echo '📊 Tables count: ' . count(\$tables) . PHP_EOL;
            } catch (\Exception \$e) {
                echo 'Info error: ' . \$e->getMessage() . PHP_EOL;
            }
        " 2>/dev/null || true
        
        break
    else
        RETRY=$((RETRY + 1))
        if [ $RETRY -lt $MAX_RETRIES ]; then
            echo "   ❌ Attempt $RETRY/$MAX_RETRIES failed"
            echo "   Error: $DB_TEST"
            echo "   Retrying in 5 seconds..."
            sleep 5
        else
            echo ""
            echo "================================================"
            echo "❌ DATABASE CONNECTION FAILED"
            echo "================================================"
            echo "Error details: $DB_TEST"
            echo ""
            echo "🔍 Environment check:"
            echo "   DB_HOST = ${DB_HOST:-NOT SET}"
            echo "   DB_PORT = ${DB_PORT:-NOT SET}"
            echo "   DB_DATABASE = ${DB_DATABASE:-NOT SET}"
            echo "   DB_USERNAME = ${DB_USERNAME:-NOT SET}"
            echo "   SSL CA exists = $([ -f "$MYSQL_ATTR_SSL_CA" ] && echo YES || echo NO)"
            echo ""
            echo "🔍 DNS Resolution:"
            nslookup $DB_HOST 2>&1 || echo "DNS lookup failed"
            echo ""
            echo "🔍 Port connectivity:"
            nc -zv $DB_HOST $DB_PORT 2>&1 || echo "Port not reachable"
            echo "================================================"
            exit 1
        fi
    fi
done

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force --no-interaction

# Seed database (only if needed)
echo "🌱 Checking seeders..."
php artisan db:seed --force --class=RoleSeeder --no-interaction 2>/dev/null || echo "   ✓ Roles already exist"
php artisan db:seed --force --class=AdminUserSeeder --no-interaction 2>/dev/null || echo "   ✓ Admin already exists"
php artisan db:seed --force --class=PersonalProfileSeeder --no-interaction 2>/dev/null || echo "   ✓ Profile already exists"

# Storage link
echo "🔗 Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

# Cache for production (AFTER DB connection works)
echo "💾 Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache 2>/dev/null || echo "⚠️  View cache skipped"

echo "================================================"
echo "✅ Application ready and listening on port 8080"
echo "   Environment: ${APP_ENV:-unknown}"
echo "   Debug: ${APP_DEBUG:-false}"
echo "================================================"

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf