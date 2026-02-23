<?php
/**
 * MaiCafe - Migration Runner
 * Upload to webroot. Visit: /migrate.php?key=maicafe_migrate_2024
 * DELETE after use!
 */

// Catch fatal errors too
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo '<div style="background:#ff4444;color:#fff;padding:20px;margin:10px;font-family:monospace;border-radius:6px;">';
        echo '<strong>Fatal PHP Error:</strong><br>';
        echo htmlspecialchars($err['message']) . '<br>';
        echo 'File: ' . htmlspecialchars($err['file']) . ' line ' . $err['line'];
        echo '</div>';
    }
});

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$key = 'maicafe_migrate_2024';
if (empty($_GET['key']) || $_GET['key'] !== $key) {
    die('<h2 style="color:red;font-family:monospace;">Access denied. Use ?key=maicafe_migrate_2024</h2>');
}

// Flush output immediately
ob_implicit_flush(true);

$css = '
<style>
body{font-family:monospace;background:#1a1a2e;color:#eee;margin:0;padding:20px}
h2{color:#00d4ff;border-bottom:1px solid #333;padding-bottom:8px}
.box{background:#16213e;border:1px solid #333;border-radius:6px;padding:16px;margin:12px 0}
.ok{color:#00ff88}.fail{color:#ff4444}.warn{color:#ffaa00}
pre{background:#0f0f1a;color:#ccc;padding:16px;border-radius:6px;overflow-x:auto;white-space:pre-wrap;word-break:break-all;font-size:13px}
pre.err{color:#ff6666}
</style>';

echo '<!DOCTYPE html><html><head><title>MaiCafe Migrate</title>' . $css . '</head><body>';
echo '<h1 style="color:#00d4ff;">MaiCafe - Database Migration</h1>';

$webroot  = __DIR__;
$corePath = $webroot . '/core';

// ──────────────────────────────────────────────
// STEP 1: Fix .env location
// ──────────────────────────────────────────────
echo '<h2>Step 1: .env Location</h2><div class="box">';
$envRoot = $webroot . '/.env';
$envCore = $corePath . '/.env';

if (file_exists($envCore)) {
    echo '<span class="ok">&#10003; core/.env found (correct)</span>';
} elseif (file_exists($envRoot)) {
    if (copy($envRoot, $envCore)) {
        echo '<span class="ok">&#10003; Copied .env → core/.env</span>';
    } else {
        echo '<span class="fail">&#10007; Cannot copy .env to core/ — do it manually in File Manager</span>';
    }
} else {
    echo '<span class="fail">&#10007; .env not found anywhere! Upload and configure it first.</span>';
}
echo '</div>';

// ──────────────────────────────────────────────
// STEP 2: Clear bootstrap cache
// ──────────────────────────────────────────────
echo '<h2>Step 2: Clear Bootstrap Cache</h2><div class="box">';
$cacheDir = $corePath . '/bootstrap/cache';
$count = 0;
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*.php') ?: [] as $f) {
        unlink($f) ? $count++ : null;
        echo '<span class="ok">&#10003; Deleted: ' . htmlspecialchars(basename($f)) . '</span><br>';
    }
    echo $count ? '' : '<span class="ok">&#10003; Cache already clean</span>';
} else {
    echo '<span class="warn">&#9888; Cache dir not found: ' . htmlspecialchars($cacheDir) . '</span>';
}
echo '</div>';

// ──────────────────────────────────────────────
// STEP 3: Try shell_exec (most reliable)
// ──────────────────────────────────────────────
echo '<h2>Step 3: Run Migrations</h2><div class="box">';

$phpBin   = PHP_BINARY ?: 'php';
$artisan  = $corePath . '/artisan';
$executed = false;

// Method A: shell_exec
if (!$executed && function_exists('shell_exec') && file_exists($artisan)) {
    echo '<span class="warn">Trying shell_exec...</span><br>';
    $cmd    = $phpBin . ' ' . escapeshellarg($artisan) . ' migrate --force 2>&1';
    $result = @shell_exec('cd ' . escapeshellarg($corePath) . ' && ' . $cmd);
    if ($result !== null) {
        echo '<span class="ok">&#10003; shell_exec worked:</span>';
        echo '<pre>' . htmlspecialchars($result) . '</pre>';
        $executed = true;
    } else {
        echo '<span class="warn">&#9888; shell_exec returned null (disabled or failed)</span><br>';
    }
}

// Method B: exec
if (!$executed && function_exists('exec') && file_exists($artisan)) {
    echo '<span class="warn">Trying exec...</span><br>';
    $output = [];
    $retval = null;
    @exec('cd ' . escapeshellarg($corePath) . ' && ' . $phpBin . ' artisan migrate --force 2>&1', $output, $retval);
    if (!empty($output)) {
        echo '<span class="ok">&#10003; exec worked:</span>';
        echo '<pre>' . htmlspecialchars(implode("\n", $output)) . '</pre>';
        $executed = true;
    } else {
        echo '<span class="warn">&#9888; exec not available or returned nothing</span><br>';
    }
}

// Method C: Boot Laravel via PHP
if (!$executed) {
    echo '<span class="warn">Trying PHP Laravel boot...</span><br>';

    $autoload = $corePath . '/vendor/autoload.php';
    $appFile  = $corePath . '/bootstrap/app.php';

    if (!file_exists($autoload)) {
        echo '<span class="fail">&#10007; vendor/autoload.php not found</span>';
    } elseif (!file_exists($appFile)) {
        echo '<span class="fail">&#10007; bootstrap/app.php not found</span>';
    } else {
        $_ENV['APP_BASE_PATH'] = $corePath;

        try {
            require $autoload;

            $app = require_once $appFile;
            $app->bind('path.public', fn() => $webroot);

            /** @var \Illuminate\Contracts\Console\Kernel $kernel */
            $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();

            $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $result   = \Illuminate\Support\Facades\Artisan::output();

            if ($exitCode === 0) {
                echo '<span class="ok">&#10003; Migrations completed!</span>';
            } else {
                echo '<span class="fail">&#10007; Migration exited with code ' . $exitCode . '</span>';
            }
            echo '<pre>' . htmlspecialchars($result) . '</pre>';
            $executed = true;

        } catch (\Throwable $e) {
            echo '<span class="fail">&#10007; ' . htmlspecialchars(get_class($e)) . ': '
                . htmlspecialchars($e->getMessage()) . '</span><br>';
            echo '<pre class="err">File: ' . htmlspecialchars($e->getFile()) . ' (line ' . $e->getLine() . ')'
                . "\n\nStack Trace:\n" . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }
    }
}

if (!$executed) {
    echo '<br><span class="fail">&#10007; All methods failed. See Method D below.</span>';
}
echo '</div>';

// ──────────────────────────────────────────────
// METHOD D: Direct PDO (manual SQL fallback)
// ──────────────────────────────────────────────
if (!$executed) {
    echo '<h2>Method D: Direct Database Setup (Fallback)</h2><div class="box">';
    echo '<span class="warn">Attempting to create tables directly via PDO...</span><br><br>';

    // Read .env
    $env = [];
    $ef  = file_exists($envCore) ? $envCore : $envRoot;
    if (file_exists($ef)) {
        foreach (file($ef, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
            [$k, $v] = explode('=', $line, 2);
            $env[trim($k)] = trim($v, " \t\r\n\"'");
        }
    }

    try {
        $pdo = new PDO(
            'mysql:host=' . ($env['DB_HOST'] ?? 'localhost') . ';dbname=' . ($env['DB_DATABASE'] ?? '') . ';charset=utf8mb4',
            $env['DB_USERNAME'] ?? '', $env['DB_PASSWORD'] ?? '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo '<span class="ok">&#10003; DB connected</span><br><br>';

        // Run each migration SQL manually
        $sqls = getMigrationSQL();
        foreach ($sqls as $label => $sql) {
            try {
                $pdo->exec($sql);
                echo '<span class="ok">&#10003; ' . htmlspecialchars($label) . '</span><br>';
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    echo '<span class="warn">&#9888; ' . htmlspecialchars($label) . ' (already exists, skipped)</span><br>';
                } else {
                    echo '<span class="fail">&#10007; ' . htmlspecialchars($label) . ': ' . htmlspecialchars($e->getMessage()) . '</span><br>';
                }
            }
        }
    } catch (PDOException $e) {
        echo '<span class="fail">&#10007; DB connection failed: ' . htmlspecialchars($e->getMessage()) . '</span>';
    }
    echo '</div>';
}

// ──────────────────────────────────────────────
// STEP 4: Verify tables
// ──────────────────────────────────────────────
echo '<h2>Step 4: Database Tables Check</h2><div class="box">';
$env = [];
$ef  = file_exists($envCore) ? $envCore : $envRoot;
if (file_exists($ef)) {
    foreach (file($ef, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v, " \t\r\n\"'");
    }
}
try {
    $pdo = new PDO(
        'mysql:host=' . ($env['DB_HOST'] ?? 'localhost') . ';dbname=' . ($env['DB_DATABASE'] ?? '') . ';charset=utf8mb4',
        $env['DB_USERNAME'] ?? '', $env['DB_PASSWORD'] ?? '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    if ($tables) {
        echo '<span class="ok">&#10003; ' . count($tables) . ' tables found:</span><br><br>';
        foreach ($tables as $t) {
            echo '&nbsp;&nbsp;&#9658; ' . htmlspecialchars($t) . '<br>';
        }
    } else {
        echo '<span class="fail">&#10007; No tables found — migration failed</span>';
    }
} catch (PDOException $e) {
    echo '<span class="fail">&#10007; ' . htmlspecialchars($e->getMessage()) . '</span>';
}
echo '</div>';

echo '<p style="color:#ff4444;margin-top:30px;font-family:monospace;"><strong>&#9888; DELETE this file after use!</strong></p>';
echo '</body></html>';


// ──────────────────────────────────────────────
// Direct SQL for all app tables (Method D fallback)
// ──────────────────────────────────────────────
function getMigrationSQL(): array
{
    return [
        'users table' => "CREATE TABLE IF NOT EXISTS `users` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `email_verified_at` timestamp NULL,
            `password` varchar(255) NOT NULL,
            `role` varchar(50) NOT NULL DEFAULT 'customer',
            `phone` varchar(20) NULL,
            `address` text NULL,
            `remember_token` varchar(100) NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `users_email_unique` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'password_resets table' => "CREATE TABLE IF NOT EXISTS `password_resets` (
            `email` varchar(255) NOT NULL,
            `token` varchar(255) NOT NULL,
            `created_at` timestamp NULL,
            KEY `password_resets_email_index` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'personal_access_tokens table' => "CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `tokenable_type` varchar(255) NOT NULL,
            `tokenable_id` bigint UNSIGNED NOT NULL,
            `name` varchar(255) NOT NULL,
            `token` varchar(64) NOT NULL,
            `abilities` text NULL,
            `last_used_at` timestamp NULL,
            `expires_at` timestamp NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
            KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'settings table' => "CREATE TABLE IF NOT EXISTS `settings` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `key` varchar(255) NOT NULL,
            `value` text NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `settings_key_unique` (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'stores table' => "CREATE TABLE IF NOT EXISTS `stores` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `address` text NULL,
            `phone` varchar(20) NULL,
            `email` varchar(255) NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'categories table' => "CREATE TABLE IF NOT EXISTS `categories` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `type` enum('cafe','restaurant') NOT NULL DEFAULT 'cafe',
            `slug` varchar(255) NOT NULL,
            `description` text NULL,
            `image` varchar(255) NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `sort_order` int NOT NULL DEFAULT 0,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `categories_slug_unique` (`slug`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'addon_groups table' => "CREATE TABLE IF NOT EXISTS `addon_groups` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `is_required` tinyint(1) NOT NULL DEFAULT 0,
            `min_select` int NOT NULL DEFAULT 0,
            `max_select` int NOT NULL DEFAULT 1,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'addons table' => "CREATE TABLE IF NOT EXISTS `addons` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `addon_group_id` bigint UNSIGNED NOT NULL,
            `name` varchar(255) NOT NULL,
            `price` decimal(10,2) NOT NULL DEFAULT 0.00,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            KEY `addons_addon_group_id_foreign` (`addon_group_id`),
            CONSTRAINT `addons_addon_group_id_foreign` FOREIGN KEY (`addon_group_id`) REFERENCES `addon_groups` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'products table' => "CREATE TABLE IF NOT EXISTS `products` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `category_id` bigint UNSIGNED NOT NULL,
            `name` varchar(255) NOT NULL,
            `slug` varchar(255) NOT NULL,
            `description` text NULL,
            `price` decimal(10,2) NOT NULL,
            `image` varchar(255) NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `is_featured` tinyint(1) NOT NULL DEFAULT 0,
            `sort_order` int NOT NULL DEFAULT 0,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `products_slug_unique` (`slug`),
            KEY `products_category_id_foreign` (`category_id`),
            CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'product_variants table' => "CREATE TABLE IF NOT EXISTS `product_variants` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `product_id` bigint UNSIGNED NOT NULL,
            `name` varchar(255) NOT NULL,
            `price` decimal(10,2) NOT NULL,
            `is_default` tinyint(1) NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            KEY `product_variants_product_id_foreign` (`product_id`),
            CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'product_addon_group table' => "CREATE TABLE IF NOT EXISTS `product_addon_group` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `product_id` bigint UNSIGNED NOT NULL,
            `addon_group_id` bigint UNSIGNED NOT NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            KEY `product_addon_group_product_id_foreign` (`product_id`),
            KEY `product_addon_group_addon_group_id_foreign` (`addon_group_id`),
            CONSTRAINT `product_addon_group_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
            CONSTRAINT `product_addon_group_addon_group_id_foreign` FOREIGN KEY (`addon_group_id`) REFERENCES `addon_groups` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'banners table' => "CREATE TABLE IF NOT EXISTS `banners` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NULL,
            `subtitle` varchar(255) NULL,
            `image` varchar(255) NOT NULL,
            `link` varchar(255) NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `sort_order` int NOT NULL DEFAULT 0,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'coupons table' => "CREATE TABLE IF NOT EXISTS `coupons` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `code` varchar(255) NOT NULL,
            `type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
            `value` decimal(10,2) NOT NULL,
            `min_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
            `max_discount` decimal(10,2) NULL,
            `usage_limit` int NULL,
            `used_count` int NOT NULL DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `expires_at` timestamp NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `coupons_code_unique` (`code`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'orders table' => "CREATE TABLE IF NOT EXISTS `orders` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint UNSIGNED NULL,
            `store_id` bigint UNSIGNED NULL,
            `order_number` varchar(255) NOT NULL,
            `status` enum('pending','payment_awaiting','confirmed','preparing','ready','completed','cancelled') NOT NULL DEFAULT 'pending',
            `payment_method` varchar(50) NOT NULL DEFAULT 'cash',
            `payment_status` varchar(50) NOT NULL DEFAULT 'pending',
            `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
            `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
            `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
            `total` decimal(10,2) NOT NULL DEFAULT 0.00,
            `coupon_code` varchar(255) NULL,
            `notes` text NULL,
            `token` varchar(255) NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `orders_order_number_unique` (`order_number`),
            KEY `orders_user_id_foreign` (`user_id`),
            KEY `orders_store_id_foreign` (`store_id`),
            CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
            CONSTRAINT `orders_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'order_items table' => "CREATE TABLE IF NOT EXISTS `order_items` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `order_id` bigint UNSIGNED NOT NULL,
            `product_id` bigint UNSIGNED NULL,
            `product_variant_id` bigint UNSIGNED NULL,
            `name` varchar(255) NOT NULL,
            `variant_name` varchar(255) NULL,
            `price` decimal(10,2) NOT NULL,
            `quantity` int NOT NULL DEFAULT 1,
            `addons` json NULL,
            `addon_price` decimal(10,2) NOT NULL DEFAULT 0.00,
            `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
            `notes` text NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            KEY `order_items_order_id_foreign` (`order_id`),
            KEY `order_items_product_id_foreign` (`product_id`),
            CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
            CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'wishlists table' => "CREATE TABLE IF NOT EXISTS `wishlists` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint UNSIGNED NOT NULL,
            `product_id` bigint UNSIGNED NOT NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
            KEY `wishlists_product_id_foreign` (`product_id`),
            CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'carts table' => "CREATE TABLE IF NOT EXISTS `carts` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint UNSIGNED NOT NULL,
            `store_id` bigint UNSIGNED NULL,
            `coupon_code` varchar(255) NULL,
            `coupon_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
            `notes` text NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `carts_user_id_unique` (`user_id`),
            CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'cart_items table' => "CREATE TABLE IF NOT EXISTS `cart_items` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `cart_id` bigint UNSIGNED NOT NULL,
            `product_id` bigint UNSIGNED NOT NULL,
            `product_variant_id` bigint UNSIGNED NULL,
            `quantity` int NOT NULL DEFAULT 1,
            `addons` json NULL,
            `notes` text NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            KEY `cart_items_cart_id_foreign` (`cart_id`),
            KEY `cart_items_product_id_foreign` (`product_id`),
            CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
            CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'otp_verifications table' => "CREATE TABLE IF NOT EXISTS `otp_verifications` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `otp` varchar(255) NOT NULL,
            `type` enum('registration','forgot_password') NOT NULL,
            `payload` json NULL,
            `expires_at` timestamp NOT NULL,
            `created_at` timestamp NULL,
            `updated_at` timestamp NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `otp_verifications_email_type_unique` (`email`,`type`),
            KEY `otp_verifications_email_index` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        'migrations table' => "CREATE TABLE IF NOT EXISTS `migrations` (
            `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `migration` varchar(255) NOT NULL,
            `batch` int NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ];
}
