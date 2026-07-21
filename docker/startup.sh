#!/bin/sh
set -e

echo "🚀 Starting Alaa Systems Hub API..."
echo "================================================"

# Check Laravel version
echo "📦 Laravel Version:"
php artisan --version || echo "⚠️  Cannot determine Laravel version"

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

# Clear config cache first
echo "🧹 Clearing configuration cache..."
php artisan config:clear || true

# Test database connection with detailed error
echo "⏳ Testing database connection..."

# Simple connection test first
php -r "
try {
    \$host = getenv('DB_HOST');
    \$port = getenv('DB_PORT');
    \$db = getenv('DB_DATABASE');
    \$user = getenv('DB_USERNAME');
    \$pass = getenv('DB_PASSWORD');
    \$ssl_ca = getenv('MYSQL_ATTR_SSL_CA');
    
    \$dsn = \"mysql:host={\$host};port={\$port};dbname={\$db}\";
    \$options = [
        PDO::MYSQL_ATTR_SSL_CA => \$ssl_ca,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
    
    \$pdo = new PDO(\$dsn, \$user, \$pass, \$options);
    echo \"✅ Direct PDO connection successful!\" . PHP_EOL;
    
    \$version = \$pdo->query('SELECT VERSION()')->fetchColumn();
    echo \"📊 MySQL Version: \" . \$version . PHP_EOL;
    
    \$ssl = \$pdo->query(\"SHOW STATUS LIKE 'Ssl_cipher'\")->fetch(PDO::FETCH_ASSOC);
    echo \"🔒 SSL Cipher: \" . (\$ssl['Value'] ?? 'None') . PHP_EOL;
    
} catch (PDOException \$e) {
    echo \"❌ Direct connection failed!\" . PHP_EOL;
    echo \"Error: \" . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

# Now test through Laravel
echo "🔄 Testing Laravel DB connection..."
php artisan tinker --execute="
try {
    \$pdo = DB::connection()->getPdo();
    echo '✅ Laravel DB connection successful!' . PHP_EOL;
} catch (\Exception \$e) {
    echo '❌ Laravel DB connection failed!' . PHP_EOL;
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force --no-interaction

# Seed database
ROLES_COUNT=$(php artisan tinker --execute="echo App\Models\Role::count();" 2>/dev/null | grep -o '[0-9]*' | tail -n 1 || echo "0")
if [ "$ROLES_COUNT" = "0" ] || [ -z "$ROLES_COUNT" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force --class=RoleSeeder --no-interaction
    php artisan db:seed --force --class=AdminUserSeeder --no-interaction
    php artisan db:seed --force --class=PersonalProfileSeeder --no-interaction
fi

# Storage link
php artisan storage:link --force || true

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "================================================"
echo "✅ Application ready!"
echo "================================================"

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf