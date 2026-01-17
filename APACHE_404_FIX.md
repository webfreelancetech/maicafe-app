# Fix 404 Error - Apache Configuration Guide

## Problem
Getting "404 Not Found" when accessing `http://localhost/maicafe-app/public/admin/dashboard`

## Root Cause
Apache's `mod_rewrite` module is not enabled or `AllowOverride` is not set correctly, preventing Laravel's routing from working.

## Solution Steps

### Step 1: Enable mod_rewrite Module

1. **Click on WAMP icon** in system tray
2. Go to **Apache → Apache modules**
3. Find **`rewrite_module`** in the list
4. **Check/Enable** it (should have a checkmark ✓)
5. If it was disabled, **restart Apache** (WAMP menu → Restart All Services)

### Step 2: Set AllowOverride All

1. Open Apache configuration file:
   - Location: `C:\wamp64\bin\apache\apache2.4.59\conf\httpd.conf`
   - Or: WAMP menu → Apache → httpd.conf

2. **Find the DocumentRoot section** (around line 245-250):
   ```apache
   <Directory "C:/wamp64/www">
       Options Indexes FollowSymLinks
       AllowOverride None  ← CHANGE THIS
       Require all granted
   </Directory>
   ```

3. **Change `AllowOverride None` to `AllowOverride All`**:
   ```apache
   <Directory "C:/wamp64/www">
       Options Indexes FollowSymLinks
       AllowOverride All  ← CHANGED
       Require all granted
   </Directory>
   ```

4. **Save the file**

5. **Restart Apache**:
   - WAMP menu → Apache → Service Administration → Restart Service
   - Or: WAMP menu → Restart All Services

### Step 3: Verify Configuration

1. **Test if mod_rewrite is working**:
   - Access: `http://localhost/maicafe-app/public/test-rewrite.php`
   - You should see "PHP is working!"

2. **Test Laravel routing**:
   - Access: `http://localhost/maicafe-app/public/`
   - Should show the home page (not 404)

3. **Test admin dashboard**:
   - Access: `http://localhost/maicafe-app/public/admin/dashboard`
   - Should show the admin dashboard or login page

## Alternative: Use Virtual Host (Recommended for Production)

For a cleaner URL like `http://maicafe-app.local`, set up a virtual host:

### 1. Edit httpd-vhosts.conf

Open: `C:\wamp64\bin\apache\apache2.4.59\conf\extra\httpd-vhosts.conf`

Add at the end:
```apache
<VirtualHost *:80>
    ServerName maicafe-app.local
    DocumentRoot "C:/wamp64/www/maicafe-app/maicafe-app/public"
    <Directory "C:/wamp64/www/maicafe-app/maicafe-app/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 2. Edit hosts file

1. Open as Administrator: `C:\Windows\System32\drivers\etc\hosts`
2. Add this line:
   ```
   127.0.0.1    maicafe-app.local
   ```
3. Save the file

### 3. Enable Virtual Hosts

In `httpd.conf`, find and uncomment (remove #):
```apache
# Include conf/extra/httpd-vhosts.conf
```
Should be:
```apache
Include conf/extra/httpd-vhosts.conf
```

### 4. Restart Apache

Now you can access:
- `http://maicafe-app.local/` (home)
- `http://maicafe-app.local/admin/dashboard` (admin)

## Quick Test Commands

After making changes, test with:

```bash
# Check if routes are registered
cd C:\wamp64\www\maicafe-app\maicafe-app
php artisan route:list

# Clear Laravel cache
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Common Issues

### Issue: Still getting 404 after enabling mod_rewrite

**Solution:**
1. Make sure you restarted Apache after changes
2. Check Apache error log: `C:\wamp64\logs\apache_error.log`
3. Verify `.htaccess` file exists in `public` folder
4. Check file permissions on `.htaccess` file

### Issue: "Internal Server Error" instead of 404

**Solution:**
1. Check Apache error log for specific error
2. Verify PHP syntax in `.htaccess` (shouldn't have any)
3. Check if `mod_rewrite` is actually loaded:
   - Create `phpinfo.php` in public folder with `<?php phpinfo(); ?>`
   - Access it and search for "mod_rewrite"

### Issue: Works with .php extension but not without

**Solution:**
- This confirms mod_rewrite is not working
- Follow Step 1 and Step 2 above

## Verification Checklist

- [ ] mod_rewrite module is enabled in WAMP
- [ ] `AllowOverride All` is set in httpd.conf
- [ ] Apache has been restarted
- [ ] `.htaccess` file exists in `public` folder
- [ ] Can access `http://localhost/maicafe-app/public/` without 404
- [ ] Can access `http://localhost/maicafe-app/public/admin/dashboard`

## Still Not Working?

1. Check Apache error logs: `C:\wamp64\logs\apache_error.log`
2. Check Laravel logs: `maicafe-app/storage/logs/laravel.log`
3. Verify the exact URL you're using matches the route in `routes/web.php`
4. Try accessing the root first: `http://localhost/maicafe-app/public/`

