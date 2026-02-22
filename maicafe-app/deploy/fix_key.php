<?php
/**
 * Fix APP_KEY - Direct Fix Script
 * Upload to hosting root and visit in browser
 * DELETE after use!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>APP_KEY Fix Script</h1>";

$envFile = __DIR__ . '/.env';
$newKey = 'base64:' . base64_encode(random_bytes(32));

// Step 1: Check if .env exists
if (!file_exists($envFile)) {
    // Try to copy from .env.example
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', $envFile);
        echo "<p style='color:green;'>✓ Created .env from .env.example</p>";
    } else {
        die("<p style='color:red;'>✗ ERROR: No .env or .env.example file found!</p>");
    }
}

// Step 2: Read current .env
$envContent = file_get_contents($envFile);
echo "<p>Current .env file found.</p>";

// Step 3: Update or add APP_KEY
if (preg_match('/^APP_KEY=.*$/m', $envContent)) {
    // Replace existing APP_KEY
    $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $newKey, $envContent);
    echo "<p style='color:green;'>✓ Updated existing APP_KEY</p>";
} else {
    // Add APP_KEY after APP_NAME or at the beginning
    if (preg_match('/^APP_NAME=.*$/m', $envContent)) {
        $envContent = preg_replace('/^(APP_NAME=.*)$/m', "$1\nAPP_KEY=" . $newKey, $envContent);
    } else {
        $envContent = "APP_KEY=" . $newKey . "\n" . $envContent;
    }
    echo "<p style='color:green;'>✓ Added APP_KEY</p>";
}

// Step 4: Write .env file
if (file_put_contents($envFile, $envContent)) {
    echo "<p style='color:green;'>✓ Saved .env file</p>";
} else {
    die("<p style='color:red;'>✗ ERROR: Could not write to .env file. Check permissions!</p>");
}

// Step 5: Clear config cache
$cacheFile = __DIR__ . '/core/bootstrap/cache/config.php';
if (file_exists($cacheFile)) {
    unlink($cacheFile);
    echo "<p style='color:green;'>✓ Cleared config cache</p>";
}

// Clear other caches
$cacheDirs = [
    __DIR__ . '/core/bootstrap/cache/',
    __DIR__ . '/core/storage/framework/cache/data/',
    __DIR__ . '/core/storage/framework/views/',
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '*');
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore' && basename($file) !== '.gitkeep') {
                unlink($file);
            }
        }
    }
}
echo "<p style='color:green;'>✓ Cleared all cache files</p>";

echo "<hr>";
echo "<h2>New APP_KEY:</h2>";
echo "<pre style='background:#1e1e1e;color:#0f0;padding:15px;'>" . $newKey . "</pre>";

echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li><a href='/'>Click here to test the site</a></li>";
echo "<li>If it works, <strong style='color:red;'>DELETE this file (fix_key.php)</strong></li>";
echo "</ol>";

echo "<p style='color:red;margin-top:30px;'><strong>⚠️ DELETE this file immediately after use!</strong></p>";
