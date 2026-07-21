<?php
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Database Connection...\n\n";

echo "Configuration:\n";
echo "  Host: " . config('database.connections.mysql.host') . "\n";
echo "  Port: " . config('database.connections.mysql.port') . "\n";
echo "  Database: " . config('database.connections.mysql.database') . "\n";
echo "  Username: " . config('database.connections.mysql.username') . "\n";
echo "  SSL CA: " . config('database.connections.mysql.options')[PDO::MYSQL_ATTR_SSL_CA] . "\n\n";

try {
    $pdo = DB::connection()->getPdo();
    echo "✅ Connection successful!\n\n";
    
    $version = DB::select('SELECT VERSION() as version')[0]->version;
    echo "📊 MySQL Version: $version\n";
    
    $ssl = DB::select("SHOW STATUS LIKE 'Ssl_cipher'")[0]->Value ?? 'Not using SSL';
    echo "🔒 SSL Status: " . ($ssl ? "Enabled ($ssl)" : "Disabled") . "\n";
    
} catch (\Exception $e) {
    echo "❌ Connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}