# MaiCafe - Shared Hosting Deployment Guide

## Overview

This guide explains how to deploy MaiCafe on a Linux shared hosting environment (cPanel, DirectAdmin, etc.) with SEO-friendly URLs and without `/public` in the URL.

## Directory Structure After Deployment

```
public_html/                    (or www, htdocs)
├── index.php                   # Modified entry point
├── .htaccess                   # URL rewriting & security
├── .env                        # Environment configuration
├── favicon.ico
├── robots.txt
├── storage/                    # Symlink to core/storage/app/public
├── core/                       # Laravel application
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   └── vendor/
├── setup_storage.php           # Delete after setup
├── clear_cache.php             # Delete after setup
└── optimize.php                # Delete after setup
```

## Deployment Steps

### Step 1: Prepare the Build

Run the deployment script on your local machine:

```bash
cd maicafe-app/deploy
chmod +x deploy.sh
./deploy.sh
```

This creates a `maicafe_deploy_XXXXXX.zip` file ready for upload.

### Step 2: Upload to Hosting

1. Login to your hosting control panel (cPanel/DirectAdmin)
2. Navigate to File Manager
3. Go to `public_html` (or your document root)
4. Upload the zip file
5. Extract the zip file
6. Delete the zip file after extraction

### Step 3: Configure Environment

1. Rename `.env.example` to `.env`
2. Edit `.env` with your settings:

```env
APP_NAME="MaiCafe"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_cpanel_database
DB_USERNAME=your_cpanel_dbuser
DB_PASSWORD=your_db_password

# Update these for your domain
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
SESSION_DOMAIN=.yourdomain.com
```

3. Generate application key (via SSH or setup script):
```bash
php artisan key:generate
```

Or add a pre-generated key to `.env`:
```env
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Step 4: Create Database

1. In cPanel, go to MySQL Databases
2. Create a new database
3. Create a database user
4. Add user to database with ALL PRIVILEGES
5. Update `.env` with database credentials

### Step 5: Set File Permissions

Via File Manager or FTP, set these permissions:

| Path | Permission |
|------|------------|
| `core/storage/` | 755 (recursive) |
| `core/bootstrap/cache/` | 755 |
| `.env` | 644 |

If you have SSH access:
```bash
chmod -R 755 core/storage
chmod -R 755 core/bootstrap/cache
chmod 644 .env
```

### Step 6: Setup Storage Link

Visit in your browser:
```
https://yourdomain.com/setup_storage.php
```

This creates the symbolic link for uploaded files.

**⚠️ Delete `setup_storage.php` after running!**

### Step 7: Run Database Migrations

**Option A: Via SSH**
```bash
cd public_html
php core/artisan migrate --force
```

**Option B: Via Web Script**

Create a temporary `migrate.php` file:
```php
<?php
require __DIR__.'/core/vendor/autoload.php';
$app = require_once __DIR__.'/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('migrate', ['--force' => true]);
echo "<pre>Migration complete!</pre>";
// DELETE THIS FILE AFTER RUNNING!
```

### Step 8: Optimize for Production

Visit:
```
https://yourdomain.com/optimize.php?key=change_this_to_your_secret_key_123
```

**⚠️ Delete `optimize.php` and `clear_cache.php` after running!**

### Step 9: Seed Initial Data (Optional)

If you need sample data, via SSH:
```bash
php core/artisan db:seed --force
```

## URL Structure

After deployment, your URLs will be SEO-friendly:

| URL | Description |
|-----|-------------|
| `https://yourdomain.com/` | Homepage |
| `https://yourdomain.com/menu` | Menu page |
| `https://yourdomain.com/product/bacon-burger` | Product page |
| `https://yourdomain.com/admin` | Admin panel |
| `https://yourdomain.com/kitchen` | Kitchen display |
| `https://yourdomain.com/order-status` | Order status display |
| `https://yourdomain.com/api/products` | API endpoint |

## API Endpoints

All API endpoints work without `/public`:

```
https://yourdomain.com/api/auth/login
https://yourdomain.com/api/products
https://yourdomain.com/api/categories
https://yourdomain.com/api/cart
https://yourdomain.com/api/orders
```

## Troubleshooting

### 500 Internal Server Error

1. Check `.htaccess` is uploaded correctly
2. Verify `mod_rewrite` is enabled
3. Check file permissions
4. Check `core/storage/logs/laravel.log` for errors

### 404 Not Found

1. Verify `.htaccess` rules are working
2. Check if `mod_rewrite` is enabled
3. Clear route cache: visit `clear_cache.php`

### Storage/Upload Issues

1. Verify storage symlink exists
2. Check `core/storage/app/public` permissions
3. Ensure `storage/` folder exists in root

### Database Connection Error

1. Verify database credentials in `.env`
2. Check database host (usually `localhost`)
3. Ensure database user has proper privileges

### Session Issues

1. Verify `SESSION_DOMAIN` in `.env`
2. Check `core/storage/framework/sessions` is writable
3. Clear session files if needed

## Security Checklist

After deployment, ensure:

- [ ] `.env` file has 644 permissions
- [ ] `APP_DEBUG=false` in `.env`
- [ ] Delete `setup_storage.php`
- [ ] Delete `clear_cache.php`
- [ ] Delete `optimize.php`
- [ ] Delete `migrate.php` (if created)
- [ ] SSL certificate installed (HTTPS)
- [ ] Default admin password changed

## Updating the Application

1. Backup your database and `.env` file
2. Upload new `core/` folder contents
3. Run migrations if needed
4. Clear and optimize cache

```bash
# Via SSH
php core/artisan migrate --force
php core/artisan config:cache
php core/artisan route:cache
php core/artisan view:cache
```

## Support

For issues, check:
1. Laravel logs: `core/storage/logs/laravel.log`
2. PHP error logs (in cPanel: Errors section)
3. Apache error logs

---

**Note:** This deployment method is designed for shared hosting where you cannot modify server configuration. For VPS/dedicated servers, the standard Laravel deployment with proper virtual host configuration is recommended.
