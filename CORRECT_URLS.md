# Correct URLs to Access Your Application

## Important: Your Laravel App is in a Nested Folder

Your application structure is:
```
C:\wamp64\www\maicafe-app\          ← Root (this folder)
  └── maicafe-app\                  ← Laravel application
      └── public\                    ← Web root
          └── index.php
```

## ✅ Correct URLs

### Home Page
```
http://localhost/maicafe-app/maicafe-app/public/
```

### Admin Dashboard
```
http://localhost/maicafe-app/maicafe-app/public/admin/dashboard
```

### Other Routes
- Menu: `http://localhost/maicafe-app/maicafe-app/public/menu`
- Stores: `http://localhost/maicafe-app/maicafe-app/public/stores`
- Cart: `http://localhost/maicafe-app/maicafe-app/public/cart`
- Login: `http://localhost/maicafe-app/maicafe-app/public/login`
- Register: `http://localhost/maicafe-app/maicafe-app/public/register`

## ⚠️ Why the Double "maicafe-app"?

Your project has a nested folder structure. The Laravel application is inside:
```
C:\wamp64\www\maicafe-app\maicafe-app\
```

So when accessing via `http://localhost/`, you need:
- `/maicafe-app/` (first level - the project folder)
- `/maicafe-app/` (second level - the Laravel app folder)
- `/public/` (the web-accessible folder)

## 🔧 Alternative: Simplify the Structure (Optional)

If you want cleaner URLs like `http://localhost/maicafe-app/public/`, you could:

1. Move all files from `maicafe-app/maicafe-app/` to `maicafe-app/`
2. Then access: `http://localhost/maicafe-app/public/`

But this requires moving many files. The current structure works fine with the URLs above.

## 🧪 Test First

Before trying the admin dashboard, test if PHP is working:
```
http://localhost/maicafe-app/test-simple.php
```

If this works, then try the Laravel URLs above.

## 🚨 Still Getting 404?

1. **Enable mod_rewrite**: WAMP menu → Apache → Apache modules → Check 'rewrite_module'
2. **Set AllowOverride All**: In `httpd.conf`, change `AllowOverride None` to `AllowOverride All` in the `<Directory "C:/wamp64/www">` section
3. **Restart Apache**: WAMP menu → Restart All Services
4. **Try with index.php explicitly**: 
   ```
   http://localhost/maicafe-app/maicafe-app/public/index.php/admin/dashboard
   ```

