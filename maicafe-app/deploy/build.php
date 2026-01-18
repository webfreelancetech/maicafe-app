<?php
/**
 * MaiCafe - PHP Build Script for Deployment
 * 
 * Run this script from command line:
 * php deploy/build.php
 * 
 * Or via web browser if needed (not recommended for security)
 */

// Increase limits for large projects
set_time_limit(300);
ini_set('memory_limit', '512M');

echo "============================================\n";
echo "  MaiCafe - Production Build Script\n";
echo "============================================\n\n";

// Get paths
$deployDir = __DIR__;
$projectDir = dirname($deployDir);
$buildDir = $projectDir . '/build';
$coreDir = $buildDir . '/core';
$timestamp = date('Ymd_His');
$zipName = "maicafe_deploy_{$timestamp}.zip";

echo "Project directory: $projectDir\n";
echo "Build directory: $buildDir\n\n";

// Helper function to recursively copy directory
function copyDirectory($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                copyDirectory($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
    }
    closedir($dir);
}

// Helper function to recursively delete directory
function deleteDirectory($dir) {
    if (!is_dir($dir)) return;
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    rmdir($dir);
}

// Clean previous build
if (is_dir($buildDir)) {
    echo "Cleaning previous build...\n";
    deleteDirectory($buildDir);
}

// Create build directory structure
echo "Creating build directory structure...\n";
mkdir($buildDir, 0755, true);
mkdir($coreDir, 0755, true);

// Directories to copy to core
$coreDirs = ['app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'storage', 'vendor'];

echo "Copying Laravel core files...\n";
foreach ($coreDirs as $dir) {
    $srcPath = $projectDir . '/' . $dir;
    if (is_dir($srcPath)) {
        echo "  - Copying $dir/\n";
        copyDirectory($srcPath, $coreDir . '/' . $dir);
    }
}

// Core files to copy
$coreFiles = ['artisan', 'composer.json', 'composer.lock'];
foreach ($coreFiles as $file) {
    $srcPath = $projectDir . '/' . $file;
    if (file_exists($srcPath)) {
        echo "  - Copying $file\n";
        copy($srcPath, $coreDir . '/' . $file);
    }
}

// Copy public folder contents to root
echo "Copying public files to root...\n";
$publicDir = $projectDir . '/public';
if (is_dir($publicDir)) {
    $publicFiles = scandir($publicDir);
    foreach ($publicFiles as $file) {
        if ($file != '.' && $file != '..' && $file != 'index.php') {
            $srcPath = $publicDir . '/' . $file;
            $dstPath = $buildDir . '/' . $file;
            if (is_dir($srcPath)) {
                copyDirectory($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
            echo "  - Copied $file\n";
        }
    }
}

// Copy deployment files
echo "Copying deployment files...\n";
copy($deployDir . '/.htaccess', $buildDir . '/.htaccess');
copy($deployDir . '/index.php', $buildDir . '/index.php');
copy($deployDir . '/.env.production', $buildDir . '/.env.example');
copy($deployDir . '/DEPLOYMENT_GUIDE.md', $buildDir . '/DEPLOYMENT_GUIDE.md');
echo "  - Copied .htaccess, index.php, .env.example, DEPLOYMENT_GUIDE.md\n";

// Create setup scripts
echo "Creating setup scripts...\n";

// Storage link script
$storageScript = <<<'PHP'
<?php
/**
 * Storage Link Setup Script
 * Run once after deployment, then DELETE this file!
 */
$target = __DIR__ . '/core/storage/app/public';
$link = __DIR__ . '/storage';

echo "<h2>Storage Link Setup</h2>";
if (is_link($link)) {
    echo "<p style='color:green;'>✓ Storage link already exists.</p>";
} elseif (file_exists($link)) {
    echo "<p style='color:orange;'>⚠ 'storage' exists but is not a symbolic link.</p>";
    echo "<p>Please remove it manually and run this script again.</p>";
} else {
    if (@symlink($target, $link)) {
        echo "<p style='color:green;'>✓ Storage symbolic link created successfully!</p>";
    } else {
        echo "<p style='color:red;'>✗ Failed to create symbolic link.</p>";
        echo "<p>Alternative: Manually copy contents from <code>core/storage/app/public</code> to <code>storage/</code></p>";
    }
}
echo "<p style='color:red;'><strong>⚠ DELETE this file after running!</strong></p>";
PHP;
file_put_contents($buildDir . '/setup_storage.php', $storageScript);

// Cache clear script
$cacheScript = <<<'PHP'
<?php
/**
 * Cache Clear Script - Run when needed, then DELETE!
 * Usage: https://yourdomain.com/clear_cache.php?key=YOUR_SECRET
 */
$secretKey = 'maicafe_cache_clear_2024';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die('Forbidden - Invalid key. Use: ?key=' . $secretKey);
}
require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo "<h2>Cache Clear</h2><pre>";
foreach (['config:clear', 'route:clear', 'view:clear', 'cache:clear'] as $cmd) {
    echo "Running $cmd... ";
    $kernel->call($cmd);
    echo "Done!\n";
}
echo "</pre><p style='color:green;'>✓ All caches cleared!</p>";
echo "<p style='color:red;'><strong>⚠ DELETE this file after use!</strong></p>";
PHP;
file_put_contents($buildDir . '/clear_cache.php', $cacheScript);

// Optimize script
$optimizeScript = <<<'PHP'
<?php
/**
 * Production Optimization Script - Run once, then DELETE!
 * Usage: https://yourdomain.com/optimize.php?key=YOUR_SECRET
 */
$secretKey = 'maicafe_optimize_2024';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die('Forbidden - Invalid key. Use: ?key=' . $secretKey);
}
require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo "<h2>Production Optimization</h2><pre>";
foreach (['config:cache', 'route:cache', 'view:cache'] as $cmd) {
    echo "Running $cmd... ";
    try {
        $kernel->call($cmd);
        echo "Done!\n";
    } catch (Exception $e) {
        echo "Warning: " . $e->getMessage() . "\n";
    }
}
echo "</pre><p style='color:green;'>✓ Optimization complete!</p>";
echo "<p style='color:red;'><strong>⚠ DELETE this file after use!</strong></p>";
PHP;
file_put_contents($buildDir . '/optimize.php', $optimizeScript);

// Migrate script
$migrateScript = <<<'PHP'
<?php
/**
 * Database Migration Script - Run once, then DELETE!
 * Usage: https://yourdomain.com/migrate.php?key=YOUR_SECRET
 */
$secretKey = 'maicafe_migrate_2024';
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die('Forbidden - Invalid key. Use: ?key=' . $secretKey);
}
require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo "<h2>Database Migration</h2><pre>";
echo "Running migrations...\n";
$kernel->call('migrate', ['--force' => true]);
echo "\nDone!</pre>";
echo "<p style='color:green;'>✓ Migrations completed!</p>";
echo "<p style='color:red;'><strong>⚠ DELETE this file after use!</strong></p>";
PHP;
file_put_contents($buildDir . '/migrate.php', $migrateScript);

// Key generate script
$keyScript = <<<'PHP'
<?php
/**
 * Application Key Generator - Run once, then DELETE!
 * Usage: https://yourdomain.com/generate_key.php
 */
require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo "<h2>Application Key Generator</h2>";
$kernel->call('key:generate', ['--force' => true]);
echo "<p style='color:green;'>✓ Application key generated!</p>";
echo "<p>Check your .env file for the new APP_KEY value.</p>";
echo "<p style='color:red;'><strong>⚠ DELETE this file after use!</strong></p>";
PHP;
file_put_contents($buildDir . '/generate_key.php', $keyScript);

echo "  - Created setup_storage.php\n";
echo "  - Created clear_cache.php\n";
echo "  - Created optimize.php\n";
echo "  - Created migrate.php\n";
echo "  - Created generate_key.php\n";

// Create permissions info file
$permissionsInfo = <<<'TXT'
==============================================
  File Permissions for Shared Hosting
==============================================

Set these permissions after uploading:

DIRECTORIES (755):
- core/storage/          (recursive)
- core/bootstrap/cache/

WRITABLE (775):
- core/storage/app/
- core/storage/framework/
- core/storage/framework/cache/
- core/storage/framework/sessions/
- core/storage/framework/views/
- core/storage/logs/
- core/bootstrap/cache/

FILES (644):
- .env
- All PHP files

Via SSH:
chmod -R 755 core/storage core/bootstrap/cache
chmod 644 .env

==============================================
TXT;
file_put_contents($buildDir . '/PERMISSIONS.txt', $permissionsInfo);

// Remove unnecessary files from build
echo "\nCleaning up unnecessary files...\n";
$removeFiles = [
    $coreDir . '/storage/logs/laravel.log',
    $coreDir . '/storage/framework/sessions/*',
    $coreDir . '/storage/framework/cache/data/*',
];
foreach ($removeFiles as $pattern) {
    foreach (glob($pattern) as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
        }
    }
}

// Create .gitkeep files to preserve directory structure
$keepDirs = [
    $coreDir . '/storage/app/public',
    $coreDir . '/storage/framework/cache/data',
    $coreDir . '/storage/framework/sessions',
    $coreDir . '/storage/framework/views',
    $coreDir . '/storage/logs',
    $coreDir . '/bootstrap/cache',
];
foreach ($keepDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($dir . '/.gitkeep', '');
}

// Create ZIP file
echo "\nCreating deployment package...\n";

// Check if ZipArchive is available
if (class_exists('ZipArchive')) {
    $zip = new ZipArchive();
    $zipPath = $projectDir . '/' . $zipName;
    
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($buildDir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($buildDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        echo "  - Created: $zipPath\n";
    } else {
        echo "  - Warning: Could not create ZIP file\n";
    }
} else {
    echo "  - Warning: ZipArchive not available. Please manually zip the build folder.\n";
}

echo "\n============================================\n";
echo "  Build Complete!\n";
echo "============================================\n\n";
echo "Build folder: $buildDir\n";
if (file_exists($projectDir . '/' . $zipName)) {
    echo "ZIP package: $projectDir/$zipName\n";
}
echo "\nSetup URLs after deployment:\n";
echo "  1. https://yourdomain.com/generate_key.php\n";
echo "  2. https://yourdomain.com/setup_storage.php\n";
echo "  3. https://yourdomain.com/migrate.php?key=maicafe_migrate_2024\n";
echo "  4. https://yourdomain.com/optimize.php?key=maicafe_optimize_2024\n";
echo "\n⚠ Delete all setup scripts after running!\n\n";
echo "See DEPLOYMENT_GUIDE.md for detailed instructions.\n";
