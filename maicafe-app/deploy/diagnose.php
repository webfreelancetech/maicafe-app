<?php
/**
 * MaiCafe - Deployment Diagnostic Script
 * Upload this to your hosting root and visit it in browser
 * DELETE after troubleshooting!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<html><head><title>MaiCafe Diagnostics</title>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; }
.success { color: green; }
.error { color: red; }
.warning { color: orange; }
h2 { border-bottom: 2px solid #333; padding-bottom: 10px; }
.check { margin: 10px 0; padding: 10px; background: #f5f5f5; border-radius: 5px; }
pre { background: #1e1e1e; color: #fff; padding: 15px; border-radius: 5px; overflow-x: auto; }
</style></head><body>";

echo "<h1>MaiCafe - Deployment Diagnostics</h1>";

// 1. PHP Version
echo "<h2>1. PHP Environment</h2>";
echo "<div class='check'>";
$phpVersion = phpversion();
$requiredVersion = '8.0.0';
if (version_compare($phpVersion, $requiredVersion, '>=')) {
    echo "<span class='success'>✓ PHP Version: $phpVersion (OK)</span>";
} else {
    echo "<span class='error'>✗ PHP Version: $phpVersion (Required: >= $requiredVersion)</span>";
}
echo "</div>";

// Required extensions
echo "<div class='check'>";
echo "<strong>Required Extensions:</strong><br>";
$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<span class='success'>✓ $ext</span> &nbsp; ";
    } else {
        echo "<span class='error'>✗ $ext (MISSING)</span> &nbsp; ";
    }
}
echo "</div>";

// 2. Directory Structure
echo "<h2>2. Directory Structure</h2>";
$paths = [
    'core' => __DIR__ . '/core',
    'core/vendor' => __DIR__ . '/core/vendor',
    'core/vendor/autoload.php' => __DIR__ . '/core/vendor/autoload.php',
    'core/bootstrap' => __DIR__ . '/core/bootstrap',
    'core/storage' => __DIR__ . '/core/storage',
    'core/storage/framework' => __DIR__ . '/core/storage/framework',
    'core/storage/logs' => __DIR__ . '/core/storage/logs',
    '.env' => __DIR__ . '/.env',
    '.htaccess' => __DIR__ . '/.htaccess',
    'index.php' => __DIR__ . '/index.php',
];

foreach ($paths as $name => $path) {
    echo "<div class='check'>";
    if (file_exists($path)) {
        $type = is_dir($path) ? 'directory' : 'file';
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "<span class='success'>✓ $name ($type, permissions: $perms)</span>";
    } else {
        echo "<span class='error'>✗ $name - NOT FOUND</span>";
    }
    echo "</div>";
}

// 3. Writable directories
echo "<h2>3. Writable Directories</h2>";
$writablePaths = [
    'core/storage' => __DIR__ . '/core/storage',
    'core/storage/app' => __DIR__ . '/core/storage/app',
    'core/storage/framework' => __DIR__ . '/core/storage/framework',
    'core/storage/framework/cache' => __DIR__ . '/core/storage/framework/cache',
    'core/storage/framework/sessions' => __DIR__ . '/core/storage/framework/sessions',
    'core/storage/framework/views' => __DIR__ . '/core/storage/framework/views',
    'core/storage/logs' => __DIR__ . '/core/storage/logs',
    'core/bootstrap/cache' => __DIR__ . '/core/bootstrap/cache',
];

foreach ($writablePaths as $name => $path) {
    echo "<div class='check'>";
    if (file_exists($path)) {
        if (is_writable($path)) {
            echo "<span class='success'>✓ $name is writable</span>";
        } else {
            echo "<span class='error'>✗ $name is NOT writable - chmod 775 needed</span>";
        }
    } else {
        echo "<span class='warning'>⚠ $name does not exist - needs to be created</span>";
    }
    echo "</div>";
}

// 4. Environment File
echo "<h2>4. Environment Configuration</h2>";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "<div class='check'><span class='success'>✓ .env file exists</span></div>";
    
    $envContent = file_get_contents($envFile);
    
    // Check APP_KEY
    if (preg_match('/APP_KEY=(.*)/', $envContent, $matches)) {
        $appKey = trim($matches[1]);
        if (empty($appKey) || $appKey === '' || strpos($appKey, 'base64:') !== 0) {
            echo "<div class='check'><span class='error'>✗ APP_KEY is empty or invalid - run generate_key.php</span></div>";
        } else {
            echo "<div class='check'><span class='success'>✓ APP_KEY is set</span></div>";
        }
    } else {
        echo "<div class='check'><span class='error'>✗ APP_KEY not found in .env</span></div>";
    }
    
    // Check APP_DEBUG
    if (preg_match('/APP_DEBUG=(.*)/', $envContent, $matches)) {
        $debug = trim($matches[1]);
        echo "<div class='check'><span class='warning'>APP_DEBUG=$debug (set to true temporarily to see errors)</span></div>";
    }
    
    // Check DB settings
    if (preg_match('/DB_DATABASE=(.*)/', $envContent, $matches)) {
        $db = trim($matches[1]);
        if (empty($db) || $db === 'your_database') {
            echo "<div class='check'><span class='error'>✗ DB_DATABASE not configured</span></div>";
        } else {
            echo "<div class='check'><span class='success'>✓ DB_DATABASE is set: $db</span></div>";
        }
    }
} else {
    echo "<div class='check'><span class='error'>✗ .env file NOT FOUND - copy .env.example to .env</span></div>";
}

// 5. Try loading Laravel
echo "<h2>5. Laravel Bootstrap Test</h2>";
$autoloadPath = __DIR__ . '/core/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    echo "<div class='check'><span class='success'>✓ Autoloader found</span></div>";
    
    try {
        require $autoloadPath;
        echo "<div class='check'><span class='success'>✓ Autoloader loaded</span></div>";
        
        $appPath = __DIR__ . '/core/bootstrap/app.php';
        if (file_exists($appPath)) {
            try {
                $app = require_once $appPath;
                echo "<div class='check'><span class='success'>✓ Laravel app bootstrapped</span></div>";
                
                // Try to get the kernel
                try {
                    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
                    echo "<div class='check'><span class='success'>✓ HTTP Kernel loaded</span></div>";
                } catch (Exception $e) {
                    echo "<div class='check'><span class='error'>✗ HTTP Kernel error: " . $e->getMessage() . "</span></div>";
                }
            } catch (Exception $e) {
                echo "<div class='check'><span class='error'>✗ Bootstrap error: " . $e->getMessage() . "</span></div>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
        } else {
            echo "<div class='check'><span class='error'>✗ bootstrap/app.php not found</span></div>";
        }
    } catch (Exception $e) {
        echo "<div class='check'><span class='error'>✗ Autoloader error: " . $e->getMessage() . "</span></div>";
    }
} else {
    echo "<div class='check'><span class='error'>✗ vendor/autoload.php NOT FOUND - composer install needed</span></div>";
}

// 6. Database Connection Test
echo "<h2>6. Database Connection Test</h2>";
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    preg_match('/DB_HOST=(.*)/', $envContent, $hostMatch);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $dbMatch);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $userMatch);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
    
    $host = trim($hostMatch[1] ?? 'localhost');
    $database = trim($dbMatch[1] ?? '');
    $username = trim($userMatch[1] ?? '');
    $password = trim($passMatch[1] ?? '');
    
    if ($database && $username) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<div class='check'><span class='success'>✓ Database connection successful</span></div>";
            
            // Check if tables exist
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            if (count($tables) > 0) {
                echo "<div class='check'><span class='success'>✓ Found " . count($tables) . " tables</span></div>";
            } else {
                echo "<div class='check'><span class='warning'>⚠ No tables found - run migrations</span></div>";
            }
        } catch (PDOException $e) {
            echo "<div class='check'><span class='error'>✗ Database connection failed: " . $e->getMessage() . "</span></div>";
        }
    } else {
        echo "<div class='check'><span class='warning'>⚠ Database credentials not configured in .env</span></div>";
    }
} else {
    echo "<div class='check'><span class='warning'>⚠ Cannot test - .env file missing</span></div>";
}

// 7. Check Laravel Log
echo "<h2>7. Laravel Error Log</h2>";
$logFile = __DIR__ . '/core/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logSize = filesize($logFile);
    echo "<div class='check'><span class='success'>✓ Log file exists (Size: " . round($logSize/1024, 2) . " KB)</span></div>";
    
    // Show last 50 lines
    $lines = file($logFile);
    $lastLines = array_slice($lines, -50);
    if (!empty($lastLines)) {
        echo "<div class='check'><strong>Last log entries:</strong></div>";
        echo "<pre>" . htmlspecialchars(implode('', $lastLines)) . "</pre>";
    }
} else {
    echo "<div class='check'><span class='warning'>⚠ Log file does not exist yet</span></div>";
}

// 8. Quick Fixes
echo "<h2>8. Quick Fixes</h2>";
echo "<div class='check'>";
echo "<strong>If you see permission errors:</strong><br>";
echo "<code>chmod -R 755 core/storage core/bootstrap/cache</code><br><br>";
echo "<strong>If APP_KEY is missing:</strong><br>";
echo "Visit: <a href='generate_key.php'>generate_key.php</a><br><br>";
echo "<strong>If database tables missing:</strong><br>";
echo "Visit: <a href='migrate.php?key=maicafe_migrate_2024'>migrate.php?key=maicafe_migrate_2024</a><br><br>";
echo "<strong>To see detailed errors, edit .env:</strong><br>";
echo "<code>APP_DEBUG=true</code>";
echo "</div>";

echo "<p style='color:red; margin-top: 30px;'><strong>⚠️ DELETE this file after troubleshooting!</strong></p>";
echo "</body></html>";
