# Frontend Website URLs

## 🌐 Main Website URLs

### Home Page
```
http://localhost/maicafe-app/maicafe-app/public/
```

### Menu Page (View Products)
```
http://localhost/maicafe-app/maicafe-app/public/menu
```

### Individual Product Page
```
http://localhost/maicafe-app/maicafe-app/public/product/{product-slug}
```
**Examples:**
- `http://localhost/maicafe-app/maicafe-app/public/product/bacon-burger`
- `http://localhost/maicafe-app/maicafe-app/public/product/bbq-chicken-breast`

### Stores Page
```
http://localhost/maicafe-app/maicafe-app/public/stores
```

### Shopping Cart
```
http://localhost/maicafe-app/maicafe-app/public/cart
```

## 🔐 Authentication Pages

### Login Page
```
http://localhost/maicafe-app/maicafe-app/public/login
```

### Registration Page
```
http://localhost/maicafe-app/maicafe-app/public/register
```

## 📱 Quick Access Links

**Copy and paste these into your browser:**

- **Home:** `http://localhost/maicafe-app/maicafe-app/public/`
- **Menu:** `http://localhost/maicafe-app/maicafe-app/public/menu`
- **Cart:** `http://localhost/maicafe-app/maicafe-app/public/cart`
- **Stores:** `http://localhost/maicafe-app/maicafe-app/public/stores`
- **Login:** `http://localhost/maicafe-app/maicafe-app/public/login`

## 🛠️ Admin Panel URLs

### Admin Dashboard
```
http://localhost/maicafe-app/maicafe-app/public/admin/dashboard
```

### Admin Products
```
http://localhost/maicafe-app/maicafe-app/public/admin/products
```

### Admin Categories
```
http://localhost/maicafe-app/maicafe-app/public/admin/categories
```

### Admin Orders
```
http://localhost/maicafe-app/maicafe-app/public/admin/orders
```

## 📝 Notes

- All URLs use the nested folder structure: `/maicafe-app/maicafe-app/public/`
- Make sure Apache `mod_rewrite` is enabled for clean URLs
- If URLs don't work, try adding `index.php` explicitly:
  - `http://localhost/maicafe-app/maicafe-app/public/index.php/menu`

## 🔧 Troubleshooting

If you get 404 errors:
1. Enable mod_rewrite: WAMP menu → Apache → Apache modules → Check `rewrite_module`
2. Set AllowOverride All in `httpd.conf`
3. Restart Apache

---

**Your main website URL:**
```
http://localhost/maicafe-app/maicafe-app/public/
```

