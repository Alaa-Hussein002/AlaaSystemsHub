#!/bin/sh

echo "🚀 Starting Alaa Systems Hub API..."

# Generate APP_KEY if not exists
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:WILL_BE_GENERATED_ON_RENDER" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force --no-interaction
fi

# Wait for database with timeout
echo "⏳ Waiting for database connection..."
RETRIES=30
until php artisan db:show 2>/dev/null | grep -q "mysql" || [ $RETRIES -eq 0 ]; do
    echo "   Database not ready, waiting... ($RETRIES attempts left)"
    RETRIES=$((RETRIES-1))
    sleep 3
done

if [ $RETRIES -eq 0 ]; then
    echo "❌ Could not connect to database after 90 seconds"
    echo "📋 Database configuration:"
    echo "   Host: $DB_HOST"
    echo "   Port: $DB_PORT"
    echo "   Database: $DB_DATABASE"
    echo "   Username: $DB_USERNAME"
    exit 1
fi

echo "✅ Database connected!"

# Test SSL connection
echo "🔒 Testing SSL connection..."
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'SSL Connection OK'; } catch (\Exception \$e) { echo 'Connection Error: ' . \$e->getMessage(); }"

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force --no-interaction || {
    echo "❌ Migration failed!"
    exit 1
}

# Seed if needed (only first time)
echo "🌱 Checking database seed status..."
ROLES_COUNT=$(php artisan tinker --execute="echo \App\Models\Role::count();" 2>/dev/null | tail -n 1)

if [ "$ROLES_COUNT" = "0" ] || [ -z "$ROLES_COUNT" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force --class=RoleSeeder --no-interaction
    php artisan db:seed --force --class=AdminUserSeeder --no-interaction
    php artisan db:seed --force --class=PersonalProfileSeeder --no-interaction
else
    echo "✅ Database already seeded (Roles: $ROLES_COUNT)"
fi

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link --force || true

# Clear all caches first
echo "🧹 Clearing caches..."
php artisan cache:clear --no-interaction || true
php artisan config:clear --no-interaction || true
php artisan route:clear --no-interaction || true
php artisan view:clear --no-interaction || true

# Cache config for production
echo "💾 Caching configuration..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

# Optimize for production
echo "⚡ Optimizing application..."
php artisan optimize --no-interaction

echo "✅ Setup complete!"
echo "🌐 Application is ready to serve requests"

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf