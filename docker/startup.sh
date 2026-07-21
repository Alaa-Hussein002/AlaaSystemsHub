#!/bin/sh
set -e

echo "🚀 Starting Alaa Systems Hub API..."
echo "================================================"

# Generate APP_KEY if not exists
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force --no-interaction
    echo "✅ APP_KEY generated"
fi

# Verify SSL certificate
if [ -f "/var/www/ssl/aiven-ca.pem" ]; then
    echo "🔒 SSL Certificate found"
    echo "   Size: $(wc -c < /var/www/ssl/aiven-ca.pem) bytes"
else
    echo "⚠️  SSL Certificate not found at /var/www/ssl/aiven-ca.pem"
fi

# Wait for database with detailed logging
echo "⏳ Waiting for database connection..."
echo "   Host: $DB_HOST"
echo "   Port: $DB_PORT"
echo "   Database: $DB_DATABASE"
echo "   Username: $DB_USERNAME"

RETRIES=30
until php artisan db:show 2>&1 | grep -q "mysql" || [ $RETRIES -eq 0 ]; do
    echo "   Attempt $((31-RETRIES))/30..."
    RETRIES=$((RETRIES-1))
    sleep 3
done

if [ $RETRIES -eq 0 ]; then
    echo "❌ Could not connect to database after 90 seconds"
    echo "📋 Attempting direct connection test..."
    php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Connected!'; } catch (\Exception \$e) { echo 'Error: ' . \$e->getMessage(); }"
    exit 1
fi

echo "✅ Database connected!"

# Test SSL connection
echo "🔐 Testing SSL connection..."
php artisan tinker --execute="
    try {
        \$pdo = DB::connection()->getPdo();
        \$ssl = DB::select(\"SHOW STATUS LIKE 'Ssl_cipher'\")[0]->Value ?? 'None';
        echo \"SSL Cipher: \" . \$ssl . PHP_EOL;
    } catch (\Exception \$e) {
        echo \"SSL Test Error: \" . \$e->getMessage() . PHP_EOL;
    }
" 2>&1 | tail -n 1

# Run migrations
echo "🗄️  Running migrations..."
if php artisan migrate --force --no-interaction; then
    echo "✅ Migrations completed successfully"
else
    echo "❌ Migration failed!"
    exit 1
fi

# Seed if needed
echo "🌱 Checking database seed status..."
ROLES_COUNT=$(php artisan tinker --execute="echo \App\Models\Role::count();" 2>/dev/null | tail -n 1 || echo "0")

if [ "$ROLES_COUNT" = "0" ] || [ -z "$ROLES_COUNT" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force --class=RoleSeeder --no-interaction
    php artisan db:seed --force --class=AdminUserSeeder --no-interaction
    php artisan db:seed --force --class=PersonalProfileSeeder --no-interaction
    echo "✅ Database seeded"
else
    echo "✅ Database already seeded (Roles: $ROLES_COUNT)"
fi

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link --force 2>&1 || echo "   Storage link already exists"

# Clear caches
echo "🧹 Clearing caches..."
php artisan cache:clear --no-interaction 2>&1 || true
php artisan config:clear --no-interaction 2>&1 || true
php artisan route:clear --no-interaction 2>&1 || true
php artisan view:clear --no-interaction 2>&1 || true

# Cache for production
echo "💾 Caching configuration..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

# Optimize
echo "⚡ Optimizing application..."
php artisan optimize --no-interaction

echo "================================================"
echo "✅ Setup complete!"
echo "🌐 Application ready at: $APP_URL"
echo "📊 Environment: $APP_ENV"
echo "================================================"

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf