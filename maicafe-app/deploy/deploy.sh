#!/bin/bash

# MaiCafe Deployment Build Script
# This script creates a production-ready build for shared hosting

set -e

echo "============================================"
echo "  MaiCafe - Production Build Script"
echo "============================================"
echo ""

# Configuration
BUILD_DIR="build"
CORE_DIR="core"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
ZIP_NAME="maicafe_deploy_${TIMESTAMP}.zip"

# Get the script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"

echo "Project directory: $PROJECT_DIR"
echo "Build directory: $PROJECT_DIR/$BUILD_DIR"
echo ""

# Clean previous build
if [ -d "$PROJECT_DIR/$BUILD_DIR" ]; then
    echo "Cleaning previous build..."
    rm -rf "$PROJECT_DIR/$BUILD_DIR"
fi

# Create build directory structure
echo "Creating build directory structure..."
mkdir -p "$PROJECT_DIR/$BUILD_DIR"
mkdir -p "$PROJECT_DIR/$BUILD_DIR/$CORE_DIR"

# Copy core Laravel files to core directory
echo "Copying Laravel core files..."
cd "$PROJECT_DIR"

# Copy directories
for dir in app bootstrap config database resources routes storage vendor; do
    if [ -d "$dir" ]; then
        cp -r "$dir" "$BUILD_DIR/$CORE_DIR/"
        echo "  - Copied $dir/"
    fi
done

# Copy core files
for file in artisan composer.json composer.lock; do
    if [ -f "$file" ]; then
        cp "$file" "$BUILD_DIR/$CORE_DIR/"
        echo "  - Copied $file"
    fi
done

# Copy public folder contents to root
echo "Copying public files to root..."
if [ -d "public" ]; then
    cp -r public/* "$BUILD_DIR/"
    echo "  - Copied public/* to root"
fi

# Copy deployment files
echo "Copying deployment files..."
cp "$SCRIPT_DIR/.htaccess" "$BUILD_DIR/"
cp "$SCRIPT_DIR/index.php" "$BUILD_DIR/"
cp "$SCRIPT_DIR/.env.production" "$BUILD_DIR/.env.example"
echo "  - Copied .htaccess, index.php, .env.example"

# Create storage symbolic link script
echo "Creating storage link setup script..."
cat > "$BUILD_DIR/setup_storage.php" << 'EOFPHP'
<?php
/**
 * Storage Link Setup Script
 * Run this once after deployment: https://yourdomain.com/setup_storage.php
 * Then DELETE this file for security!
 */

$target = __DIR__ . '/core/storage/app/public';
$link = __DIR__ . '/storage';

if (is_link($link)) {
    echo "Storage link already exists.<br>";
} elseif (file_exists($link)) {
    echo "Error: 'storage' already exists and is not a symbolic link.<br>";
    echo "Please remove it manually and run this script again.<br>";
} else {
    if (symlink($target, $link)) {
        echo "Storage symbolic link created successfully!<br>";
        echo "Target: $target<br>";
        echo "Link: $link<br>";
    } else {
        echo "Failed to create symbolic link.<br>";
        echo "Please create it manually or contact your hosting provider.<br>";
        echo "<br>Alternative: Copy contents from core/storage/app/public to storage/<br>";
    }
}

echo "<br><strong style='color:red;'>IMPORTANT: Delete this file (setup_storage.php) after running!</strong>";
EOFPHP

# Create cache clear script
echo "Creating cache clear script..."
cat > "$BUILD_DIR/clear_cache.php" << 'EOFPHP'
<?php
/**
 * Cache Clear Script
 * Run this after deployment or when you need to clear cache
 * https://yourdomain.com/clear_cache.php?key=YOUR_SECRET_KEY
 * Then DELETE this file for security!
 */

// Set your secret key here
$secretKey = 'change_this_to_your_secret_key_123';

if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die('Forbidden - Invalid key');
}

require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
echo "Clearing caches...\n\n";

// Clear various caches
$commands = [
    'config:clear' => 'Configuration cache',
    'route:clear' => 'Route cache',
    'view:clear' => 'View cache',
    'cache:clear' => 'Application cache',
];

foreach ($commands as $command => $description) {
    echo "Clearing $description...\n";
    $kernel->call($command);
    echo "  Done!\n";
}

echo "\n\nAll caches cleared successfully!";
echo "\n\n<strong style='color:red;'>IMPORTANT: Delete this file after use!</strong>";
echo "</pre>";
EOFPHP

# Create optimize script for production
echo "Creating optimization script..."
cat > "$BUILD_DIR/optimize.php" << 'EOFPHP'
<?php
/**
 * Production Optimization Script
 * Run this after deployment: https://yourdomain.com/optimize.php?key=YOUR_SECRET_KEY
 * Then DELETE this file for security!
 */

$secretKey = 'change_this_to_your_secret_key_123';

if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die('Forbidden - Invalid key');
}

require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
echo "Optimizing for production...\n\n";

$commands = [
    'config:cache' => 'Caching configuration',
    'route:cache' => 'Caching routes',
    'view:cache' => 'Caching views',
];

foreach ($commands as $command => $description) {
    echo "$description...\n";
    try {
        $kernel->call($command);
        echo "  Done!\n";
    } catch (Exception $e) {
        echo "  Warning: " . $e->getMessage() . "\n";
    }
}

echo "\n\nOptimization complete!";
echo "\n\n<strong style='color:red;'>IMPORTANT: Delete this file after use!</strong>";
echo "</pre>";
EOFPHP

# Set permissions script
echo "Creating permissions setup info..."
cat > "$BUILD_DIR/PERMISSIONS.txt" << 'EOF'
==============================================
  File Permissions for Shared Hosting
==============================================

After uploading, set these permissions via cPanel File Manager
or FTP client:

DIRECTORIES (755):
- core/storage/          (and all subdirectories)
- core/bootstrap/cache/

FILES (644):
- All PHP files
- .htaccess
- .env

WRITABLE DIRECTORIES (775 or 777 if needed):
- core/storage/app/
- core/storage/framework/
- core/storage/framework/cache/
- core/storage/framework/sessions/
- core/storage/framework/views/
- core/storage/logs/
- core/bootstrap/cache/

Via SSH (if available):
chmod -R 755 core/storage
chmod -R 755 core/bootstrap/cache
chmod 644 .env

==============================================
EOF

# Create the deployment guide
cp "$SCRIPT_DIR/DEPLOYMENT_GUIDE.md" "$BUILD_DIR/" 2>/dev/null || true

# Create zip file
echo ""
echo "Creating deployment package..."
cd "$PROJECT_DIR/$BUILD_DIR"
zip -r "../$ZIP_NAME" . -x "*.git*"

echo ""
echo "============================================"
echo "  Build Complete!"
echo "============================================"
echo ""
echo "Deployment package: $PROJECT_DIR/$ZIP_NAME"
echo ""
echo "Next steps:"
echo "1. Upload and extract to your hosting"
echo "2. Configure .env with your settings"
echo "3. Run setup_storage.php"
echo "4. Run database migrations"
echo "5. Run optimize.php"
echo "6. Delete setup scripts for security"
echo ""
echo "See DEPLOYMENT_GUIDE.md for detailed instructions"
echo ""
