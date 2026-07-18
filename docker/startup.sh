#!/bin/sh
# docker/startup.sh

echo "🚀 Starting Alaa Systems Hub..."

# Wait for database
echo "⏳ Waiting for database connection..."
until php artisan db:show 2>/dev/null | grep -q "mysql"; do
    echo "   Database not ready, waiting..."
    sleep 2
done

echo "✅ Database connected!"

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force --no-interaction

# Seed if needed (only first time)
if php artisan db:table roles --count 2>/dev/null | grep -q "0"; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force --class=RoleSeeder
    php artisan db:seed --force --class=AdminUserSeeder
else
    echo "✅ Database already seeded"
fi

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true

# Cache config
echo "💾 Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Setup complete!"

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf