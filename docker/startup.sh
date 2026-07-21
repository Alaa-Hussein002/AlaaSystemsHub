#!/bin/sh
set -e

echo "🚀 Starting Alaa Systems Hub API..."
echo "================================================"

# Generate APP_KEY if not exists
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force --no-interaction
fi

# Verify SSL certificate
if [ -f "/var/www/ssl/aiven-ca.pem" ]; then
    echo "🔒 SSL Certificate found ($(wc -c < /var/www/ssl/aiven-ca.pem) bytes)"
else
    echo "⚠️  SSL Certificate not found!"
    exit 1
fi

# Display connection info
echo "📊 Database Configuration:"
echo "   Host: $DB_HOST"
echo "   Port: $DB_PORT"
echo "   Database: $DB_DATABASE"
echo "   Username: $DB_USERNAME"
echo "   SSL CA: $MYSQL_ATTR_SSL_CA"

# Test database connection with detailed error
echo "⏳ Testing database connection..."
if ! php artisan tinker --execute="
try {
    \$pdo = DB::connection()->getPdo();
    echo '✅ Connected to MySQL successfully!' . PHP_EOL;
    \$version = DB::select('SELECT VERSION() as v')[0]->v;
    echo '📊 MySQL Version: ' . \$version . PHP_EOL;
    \$ssl = DB::select(\"SHOW STATUS LIKE 'Ssl_cipher'\")[0]->Value ?? 'None';
    echo '🔒 SSL Cipher: ' . \$ssl . PHP_EOL;
} catch (\Exception \$e) {
    echo '❌ Connection failed!' . PHP_EOL;
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
" 2>&1; then
    echo "❌ Database connection test failed!"
    exit 1
fi

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force --no-interaction

# Seed database
ROLES_COUNT=$(php artisan tinker --execute="echo App\Models\Role::count();" 2>/dev/null | tail -n 1 || echo "0")
if [ "$ROLES_COUNT" = "0" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force --class=RoleSeeder
    php artisan db:seed --force --class=AdminUserSeeder
    php artisan db:seed --force --class=PersonalProfileSeeder
fi

# Storage link
php artisan storage:link --force || true

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "================================================"
echo "✅ Application ready!"
echo "================================================"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf