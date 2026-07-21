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
if [ -n "$DATABASE_URL" ]; then
    echo "   Using DATABASE_URL: ${DATABASE_URL%%:*}://***"
else
    echo "   Host: $DB_HOST"
    echo "   Port: $DB_PORT"
    echo "   Database: $DB_DATABASE"
    echo "   Username: $DB_USERNAME"
fi

# Test DNS resolution first
echo "🔍 Testing DNS resolution..."
if [ -n "$DB_HOST" ]; then
    if nslookup "$DB_HOST" >/dev/null 2>&1; then
        echo "✅ DNS resolution successful for $DB_HOST"
        DB_IP=$(nslookup "$DB_HOST" | grep 'Address:' | tail -n1 | awk '{print $2}')
        echo "   Resolved to: $DB_IP"
    else
        echo "❌ DNS resolution failed for $DB_HOST"
        echo "🔧 Trying alternative DNS servers..."
        
        # Try with Google DNS
        nslookup "$DB_HOST" 8.8.8.8 || echo "   Google DNS also failed"
        
        # Try with Cloudflare DNS
        nslookup "$DB_HOST" 1.1.1.1 || echo "   Cloudflare DNS also failed"
        
        echo "⚠️  DNS issue detected - check Aiven service status"
    fi
fi

# Test network connectivity
echo "🌐 Testing network connectivity..."
if [ -n "$DB_HOST" ]; then
    if nc -zv -w5 "$DB_HOST" "$DB_PORT" 2>&1; then
        echo "✅ Can reach $DB_HOST:$DB_PORT"
    else
        echo "❌ Cannot reach $DB_HOST:$DB_PORT"
        echo "   Possible causes:"
        echo "   1. Firewall blocking connection"
        echo "   2. Service not publicly accessible"
        echo "   3. Wrong hostname/port"
    fi
fi

# Clear config cache
echo "🧹 Clearing configuration cache..."
php artisan config:clear || true

# Test database connection
echo "⏳ Testing database connection..."

php -r "
try {
    if (getenv('DATABASE_URL')) {
        echo '📡 Using DATABASE_URL connection...' . PHP_EOL;
        \$dsn = getenv('DATABASE_URL');
        \$pdo = new PDO(\$dsn);
    } else {
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
    }
    
    echo '✅ Database connection successful!' . PHP_EOL;
    
    \$version = \$pdo->query('SELECT VERSION()')->fetchColumn();
    echo '📊 MySQL Version: ' . \$version . PHP_EOL;
    
    \$ssl = \$pdo->query(\"SHOW STATUS LIKE 'Ssl_cipher'\")->fetch(PDO::FETCH_ASSOC);
    echo '🔒 SSL Cipher: ' . (\$ssl['Value'] ?? 'None') . PHP_EOL;
    
} catch (PDOException \$e) {
    echo '❌ Database connection failed!' . PHP_EOL;
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
    echo 'Code: ' . \$e->getCode() . PHP_EOL;
    exit(1);
}
"

# If we reached here, connection is OK
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