#!/bin/sh
set -e

echo "🚀 Starting Alaa Systems Hub API..."
echo "================================================"

# ✅ إنشاء .env من متغيرات البيئة
echo "📝 Creating .env from environment variables..."
cat > /var/www/.env << EOF
APP_NAME="${APP_NAME}"
APP_ENV="${APP_ENV}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG}"
APP_URL="${APP_URL}"

DB_CONNECTION="${DB_CONNECTION}"
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

MYSQL_ATTR_SSL_CA="${MYSQL_ATTR_SSL_CA}"
MYSQL_ATTR_SSL_VERIFY_SERVER_CERT="${MYSQL_ATTR_SSL_VERIFY_SERVER_CERT}"

SESSION_DRIVER="${SESSION_DRIVER}"
CACHE_STORE="${CACHE_STORE}"
QUEUE_CONNECTION="${QUEUE_CONNECTION}"

MAIL_MAILER="${MAIL_MAILER}"
MAIL_HOST="${MAIL_HOST}"
MAIL_PORT="${MAIL_PORT}"
MAIL_USERNAME="${MAIL_USERNAME}"
MAIL_PASSWORD="${MAIL_PASSWORD}"
MAIL_ENCRYPTION="${MAIL_ENCRYPTION}"
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS}"
MAIL_FROM_NAME="${MAIL_FROM_NAME}"
EOF

chmod 644 /var/www/.env
echo "✅ .env file created"

# Laravel version
echo "📦 Laravel Version:"
php artisan --version 2>/dev/null || echo "⚠️  Laravel not ready"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
    # تحديث المتغير
    export APP_KEY=$(grep APP_KEY /var/www/.env | cut -d '=' -f2)
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

# التحقق من وجود المتغيرات المطلوبة
if [ -z "$DB_HOST" ] || [ -z "$DB_PORT" ] || [ -z "$DB_DATABASE" ]; then
    echo "❌ ERROR: Missing required database environment variables!"
    echo "   Please set DB_HOST, DB_PORT, DB_DATABASE in Render dashboard"
    exit 1
fi

# Clear all caches BEFORE connecting to DB
echo "🧹 Clearing ALL caches..."
rm -rf /var/www/bootstrap/cache/*.php
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Test database connection with detailed error
echo "⏳ Testing database connection..."

MAX_RETRIES=10
RETRY=0

while [ $RETRY -lt $MAX_RETRIES ]; do
    # اختبار بسيط بدون tinker
    DB_TEST=$(php -r "
        require '/var/www/vendor/autoload.php';
        \$app = require_once '/var/www/bootstrap/app.php';
        \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        try {
            \$pdo = DB::connection()->getPdo();
            \$version = \$pdo->query('SELECT VERSION()')->fetchColumn();
            echo 'SUCCESS|' . \$version;
        } catch (Exception \$e) {
            echo 'ERROR|' . \$e->getMessage();
        }
    " 2>&1)
    
    if echo "$DB_TEST" | grep -q "SUCCESS"; then
        VERSION=$(echo "$DB_TEST" | cut -d'|' -f2)
        echo "✅ Database connected successfully!"
        echo "📊 MySQL Version: $VERSION"
        
        # عرض معلومات إضافية
        php -r "
            require '/var/www/vendor/autoload.php';
            \$app = require_once '/var/www/bootstrap/app.php';
            \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
            
            try {
                \$db = DB::connection()->getDatabaseName();
                \$host = DB::connection()->getConfig('host');
                \$port = DB::connection()->getConfig('port');
                echo '📊 Connected to: ' . \$db . ' at ' . \$host . ':' . \$port . PHP_EOL;
                
                // عدد الجداول
                \$tables = DB::select('SHOW TABLES');
                echo '📊 Tables count: ' . count(\$tables) . PHP_EOL;
            } catch (Exception \$e) {
                echo 'Info error: ' . \$e->getMessage() . PHP_EOL;
            }
        " 2>/dev/null || true
        
        break
    else
        RETRY=$((RETRY + 1))
        ERROR_MSG=$(echo "$DB_TEST" | cut -d'|' -f2)
        
        if [ $RETRY -lt $MAX_RETRIES ]; then
            echo "   ❌ Attempt $RETRY/$MAX_RETRIES failed"
            echo "   Error: $ERROR_MSG"
            echo "   Retrying in 3 seconds..."
            sleep 3
        else
            echo ""
            echo "================================================"
            echo "❌ DATABASE CONNECTION FAILED"
            echo "================================================"
            echo "Error details: $ERROR_MSG"
            echo ""
            echo "🔍 Config from .env file:"
            grep "^DB_" /var/www/.env || echo "No DB_ variables in .env"
            echo ""
            echo "🔍 Testing basic PDO connection..."
            php -r "
                try {
                    \$host = getenv('DB_HOST');
                    \$port = getenv('DB_PORT');
                    \$db = getenv('DB_DATABASE');
                    \$user = getenv('DB_USERNAME');
                    \$pass = getenv('DB_PASSWORD');
                    \$ssl = getenv('MYSQL_ATTR_SSL_CA');
                    
                    \$dsn = \"mysql:host=\$host;port=\$port;dbname=\$db;charset=utf8mb4\";
                    \$options = [
                        PDO::MYSQL_ATTR_SSL_CA => \$ssl,
                        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                    ];
                    
                    \$pdo = new PDO(\$dsn, \$user, \$pass, \$options);
                    echo 'Direct PDO connection: SUCCESS' . PHP_EOL;
                    
                    \$stmt = \$pdo->query('SELECT VERSION()');
                    echo 'MySQL Version: ' . \$stmt->fetchColumn() . PHP_EOL;
                } catch (Exception \$e) {
                    echo 'Direct PDO Error: ' . \$e->getMessage() . PHP_EOL;
                }
            "
            echo ""
            exit 1
        fi
    fi
done

# Run migrations
echo "🗄️  Running migrations..."
# Run migrations
echo "🗄️  Checking migrations..."

# محاولة تشغيل migrations
MIGRATE_OUTPUT=$(php artisan migrate --force --no-interaction 2>&1)
MIGRATE_EXIT=$?

if [ $MIGRATE_EXIT -eq 0 ]; then
    echo "✅ Migrations completed successfully"
elif echo "$MIGRATE_OUTPUT" | grep -q "already exists"; then
    echo "⚠️  Tables already exist - skipping migration errors"
    echo "📊 Current migration status:"
    php artisan migrate:status 2>/dev/null || true
    echo "✅ Continuing deployment..."
else
    echo "❌ Migration failed with unexpected error:"
    echo "$MIGRATE_OUTPUT"
    exit 1
fi

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