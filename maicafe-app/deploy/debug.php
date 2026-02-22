<?php
/**
 * Debug Script - Shows exact error
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Information</h1><pre>";

// Show current directory
echo "Current directory: " . __DIR__ . "\n\n";

// List files in root
echo "Files in root:\n";
foreach (scandir(__DIR__) as $file) {
    if ($file !== '.' && $file !== '..') {
        echo "  - $file" . (is_dir(__DIR__ . '/' . $file) ? '/' : '') . "\n";
    }
}

echo "\n";

// Check .env
echo "Checking .env file:\n";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "  ✓ .env exists at: $envPath\n";
    echo "  Content (first 500 chars):\n";
    echo "  " . str_replace("\n", "\n  ", substr(file_get_contents($envPath), 0, 500)) . "\n";
} else {
    echo "  ✗ .env NOT FOUND at: $envPath\n";
}

echo "\n";

// Check core folder
echo "Checking core folder:\n";
$corePath = __DIR__ . '/core';
if (is_dir($corePath)) {
    echo "  ✓ core/ exists\n";
    echo "  Contents:\n";
    foreach (scandir($corePath) as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "    - $file" . (is_dir($corePath . '/' . $file) ? '/' : '') . "\n";
        }
    }
} else {
    echo "  ✗ core/ NOT FOUND\n";
    
    // Maybe it's a different structure?
    echo "\n  Looking for vendor folder elsewhere:\n";
    if (is_dir(__DIR__ . '/vendor')) {
        echo "  ✓ vendor/ found in root\n";
    }
    if (file_exists(__DIR__ . '/bootstrap/app.php')) {
        echo "  ✓ bootstrap/app.php found in root\n";
    }
}

echo "\n";

// Try to load Laravel with error catching
echo "Attempting to load Laravel:\n";

// Determine the correct paths
$vendorPath = null;
$bootstrapPath = null;

if (file_exists(__DIR__ . '/core/vendor/autoload.php')) {
    $vendorPath = __DIR__ . '/core/vendor/autoload.php';
    $bootstrapPath = __DIR__ . '/core/bootstrap/app.php';
    echo "  Using core/ structure\n";
} elseif (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $vendorPath = __DIR__ . '/vendor/autoload.php';
    $bootstrapPath = __DIR__ . '/bootstrap/app.php';
    echo "  Using root structure\n";
}

if ($vendorPath && file_exists($vendorPath)) {
    echo "  Loading autoloader: $vendorPath\n";
    
    try {
        require $vendorPath;
        echo "  ✓ Autoloader loaded\n";
        
        if (file_exists($bootstrapPath)) {
            echo "  Loading bootstrap: $bootstrapPath\n";
            
            try {
                $app = require_once $bootstrapPath;
                echo "  ✓ App loaded\n";
                
                // Check if .env is loaded
                echo "\n  Environment values:\n";
                echo "    APP_KEY: " . (env('APP_KEY') ?: '(empty)') . "\n";
                echo "    APP_ENV: " . (env('APP_ENV') ?: '(empty)') . "\n";
                echo "    APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
                
                // Try to boot
                try {
                    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
                    echo "\n  ✓ Kernel created successfully!\n";
                } catch (Exception $e) {
                    echo "\n  ✗ Kernel error:\n";
                    echo "    " . $e->getMessage() . "\n";
                    echo "    File: " . $e->getFile() . ":" . $e->getLine() . "\n";
                }
                
            } catch (Exception $e) {
                echo "  ✗ Bootstrap error:\n";
                echo "    " . $e->getMessage() . "\n";
                echo "    File: " . $e->getFile() . ":" . $e->getLine() . "\n";
                echo "\n  Stack trace:\n";
                echo "    " . str_replace("\n", "\n    ", $e->getTraceAsString()) . "\n";
            }
        } else {
            echo "  ✗ Bootstrap file not found: $bootstrapPath\n";
        }
    } catch (Exception $e) {
        echo "  ✗ Autoloader error: " . $e->getMessage() . "\n";
    }
} else {
    echo "  ✗ Could not find vendor/autoload.php\n";
}

echo "\n";
echo "==========================================\n";
echo "If you see 'APP_KEY: (empty)', the .env\n";
echo "file is not being read correctly.\n";
echo "==========================================\n";

echo "</pre>";
echo "<p style='color:red;'><strong>DELETE this file after debugging!</strong></p>";
