# Fix 404 Error for Admin Dashboard

## Problem
Getting "404 Not Found" when accessing:
```
http://localhost/maicafe-app/public/admin/dashboard
```

## Root Cause
Apache's `mod_rewrite` module is not enabled or `AllowOverride` is not set correctly. This prevents Laravel's routing from working.

## Solution (Follow These Steps)

### Step 1: Test if Public Folder is Accessible
First, verify PHP is working:
```
http://localhost/maicafe-app/public/test-direct.php
```
If this works, PHP is fine. The issue is routing.

### Step 2: Enable mod_rewrite Module

1. **Click WAMP icon** in system tray (bottom right)
2. Go to **Apache → Apache modules**
3. Find **`rewrite_module`** in the list
4. **Check it** (should have a ✓ next to it)
5. If it was unchecked, **restart Apache**:
   - WAMP menu → Restart All Services
   - Or: WAMP menu → Apache → Service Administration → Restart Service

### Step 3: Set AllowOverride All

1. **Open Apache configuration**:
   - WAMP menu → Apache → httpd.conf
   - Or manually: `C:\wamp64\bin\apache\apache2.4.59\conf\httpd.conf`

2. **Find this section** (around line 245-250):
   ```apache
   <Directory "C:/wamp64/www">
       Options Indexes FollowSymLinks
       AllowOverride None
       Require all granted
   </Directory>
   ```

3. **Change `AllowOverride None` to `AllowOverride All`**:
   ```apache
   <Directory "C:/wamp64/www">
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```

4. **Save the file** (Ctrl+S)

5. **Restart Apache**:
   - WAMP menu → Restart All Services

### Step 4: Test Again

After restarting, try:
```
http://localhost/maicafe-app/public/admin/dashboard
```

## Alternative: If mod_rewrite Still Doesn't Work

If you can't enable mod_rewrite, you can access routes by adding `index.php`:

```
http://localhost/maicafe-app/public/index.php/admin/dashboard
```

This should work even without mod_rewrite, but it's not ideal.

## Verification Checklist

- [ ] mod_rewrite module is enabled in WAMP
- [ ] AllowOverride is set to All in httpd.conf
- [ ] Apache has been restarted after changes
- [ ] Can access: http://localhost/maicafe-app/public/test-direct.php
- [ ] Can access: http://localhost/maicafe-app/public/admin/dashboard

## Still Not Working?

1. **Check Apache error log**:
   - `C:\wamp64\logs\apache_error.log`
   - Look for errors related to mod_rewrite or .htaccess

2. **Check Laravel logs**:
   - `maicafe-app/storage/logs/laravel.log`

3. **Verify .htaccess files exist**:
   - `C:\wamp64\www\maicafe-app\maicafe-app\public\.htaccess` should exist
   - It should contain the RewriteRule for index.php

4. **Test with index.php explicitly**:
   ```
   http://localhost/maicafe-app/public/index.php/admin/dashboard
   ```
   If this works, mod_rewrite is definitely not working.

## Quick Test Commands

Open PowerShell in the project directory and run:
```powershell
cd C:\wamp64\www\maicafe-app\maicafe-app
php artisan route:list | Select-String "dashboard"
```

This confirms the route exists (which it does ✅).

---

**The route exists, the controller exists, the view exists. The only issue is Apache configuration.**

