<?php
/**
 * Fix .env Path Issue
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Fix .env Configuration</h1><pre>";

$rootDir = __DIR__;
$coreDir = __DIR__ . '/core';

echo "Root directory: $rootDir\n";
echo "Core directory: $coreDir\n\n";

// Check where .env exists
$envInRoot = file_exists($rootDir . '/.env');
$envInCore = file_exists($coreDir . '/.env');

echo ".env in root: " . ($envInRoot ? "YES" : "NO") . "\n";
echo ".env in core: " . ($envInCore ? "YES" : "NO") . "\n\n";

// Laravel looks for .env in the base path, which is 'core' folder
// So we need to either:
// 1. Copy .env to core folder
// 2. Or create a symlink

if ($envInRoot && !$envInCore) {
    echo "Copying .env from root to core folder...\n";
    
    if (copy($rootDir . '/.env', $coreDir . '/.env')) {
        echo "✓ Successfully copied .env to core/\n";
    } else {
        echo "✗ Failed to copy. Trying to create new .env in core/...\n";
        
        // Read from root and write to core
        $envContent = file_get_contents($rootDir . '/.env');
        if (file_put_contents($coreDir . '/.env', $envContent)) {
            echo "✓ Created .env in core/\n";
        } else {
            echo "✗ Failed to create .env in core/. Check permissions!\n";
        }
    }
}

// Also make sure APP_KEY is set in core/.env
$coreEnvPath = $coreDir . '/.env';
if (file_exists($coreEnvPath)) {
    $envContent = file_get_contents($coreEnvPath);
    
    // Check if APP_KEY is empty
    if (preg_match('/^APP_KEY=\s*$/m', $envContent) || !preg_match('/^APP_KEY=/m', $envContent)) {
        echo "\nAPP_KEY is empty, generating new key...\n";
        
        $newKey = 'base64:' . base64_encode(random_bytes(32));
        
        if (preg_match('/^APP_KEY=/m', $envContent)) {
            $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $newKey, $envContent);
        } else {
            $envContent = "APP_KEY=" . $newKey . "\n" . $envContent;
        }
        
        file_put_contents($coreEnvPath, $envContent);
        echo "✓ APP_KEY set: $newKey\n";
    } else {
        preg_match('/^APP_KEY=(.*)$/m', $envContent, $matches);
        echo "\nAPP_KEY already set: " . trim($matches[1]) . "\n";
    }
}

// Clear all caches
echo "\nClearing caches...\n";

$cacheFiles = [
    $coreDir . '/bootstrap/cache/config.php',
    $coreDir . '/bootstrap/cache/routes-v7.php',
    $coreDir . '/bootstrap/cache/services.php',
    $coreDir . '/bootstrap/cache/packages.php',
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "  Deleted: " . basename($file) . "\n";
    }
}

// Clear framework cache
$cacheDirs = [
    $coreDir . '/storage/framework/cache/data',
    $coreDir . '/storage/framework/views',
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "  Cleared: " . str_replace($coreDir, 'core', $dir) . "\n";
    }
}

echo "\n";
echo "==========================================\n";
echo "Fix complete! Now test the site.\n";
echo "==========================================\n";

echo "</pre>";

echo "<h2>Test Laravel Now:</h2>";

// Quick test
if (file_exists($coreDir . '/vendor/autoload.php')) {
    require $coreDir . '/vendor/autoload.php';
    $app = require $coreDir . '/bootstrap/app.php';
    
    echo "<pre>";
    echo "APP_KEY from env(): " . (env('APP_KEY') ?: '(still empty)') . "\n";
    echo "</pre>";
    
    if (env('APP_KEY')) {
        echo "<p style='color:green; font-size:18px;'>✓ APP_KEY is now working!</p>";
        echo "<p><a href='/' style='font-size:18px;'>→ Click here to visit the site</a></p>";
    } else {
        echo "<p style='color:red;'>✗ Still not working. Check file permissions on core/ folder.</p>";
    }
}

echo "<p style='color:red; margin-top:20px;'><strong>DELETE this file after use!</strong></p>";
