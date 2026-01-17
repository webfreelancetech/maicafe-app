# Mai Cafe App - Project File Manifest

This document lists all critical files in the project. If files go missing, use this as a reference to restore them.

## Database Migrations
- `database/migrations/2025_01_01_000001_create_categories_table.php`
- `database/migrations/2025_01_01_000002_create_products_table.php`
- `database/migrations/2025_01_01_000003_create_stores_table.php`
- `database/migrations/2025_01_01_000004_create_orders_table.php`
- `database/migrations/2025_01_01_000005_create_order_items_table.php`
- `database/migrations/2025_01_01_000006_create_coupons_table.php`
- `database/migrations/2025_01_01_000007_add_fields_to_users_table.php`
- `database/migrations/2025_01_01_000008_create_settings_table.php`
- `database/migrations/2025_01_02_000009_create_banners_table.php`

## Models
- `app/Models/Category.php`
- `app/Models/Product.php`
- `app/Models/Store.php`
- `app/Models/Order.php`
- `app/Models/OrderItem.php`
- `app/Models/Coupon.php`
- `app/Models/Setting.php`
- `app/Models/Banner.php`
- `app/Models/User.php` (updated with new fields)

## Controllers
- `app/Http/Controllers/EcommerceController.php`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/ProductController.php`
- `app/Http/Controllers/Admin/CategoryController.php`
- `app/Http/Controllers/Admin/OrderController.php`
- `app/Http/Controllers/Admin/StoreController.php`
- `app/Http/Controllers/Admin/CustomerController.php`
- `app/Http/Controllers/Admin/CouponController.php`
- `app/Http/Controllers/Admin/ReportController.php`
- `app/Http/Controllers/Admin/SettingController.php`
- `app/Http/Controllers/Admin/BannerController.php`

## Routes
- `routes/web.php` (contains all e-commerce and admin routes)

## Layouts
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/ecommerce.blade.php`

## Admin Views
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/products/index.blade.php`
- `resources/views/admin/products/create.blade.php`
- `resources/views/admin/products/edit.blade.php`
- `resources/views/admin/categories/index.blade.php`
- `resources/views/admin/categories/create.blade.php`
- `resources/views/admin/categories/edit.blade.php`
- `resources/views/admin/orders/index.blade.php`
- `resources/views/admin/orders/show.blade.php`
- `resources/views/admin/stores/index.blade.php`
- `resources/views/admin/customers/index.blade.php`
- `resources/views/admin/coupons/index.blade.php`
- `resources/views/admin/coupons/create.blade.php`
- `resources/views/admin/coupons/edit.blade.php`
- `resources/views/admin/reports/index.blade.php`
- `resources/views/admin/settings/index.blade.php`
- `resources/views/admin/banners/index.blade.php`
- `resources/views/admin/banners/create.blade.php`
- `resources/views/admin/banners/edit.blade.php`

## E-commerce Views
- `resources/views/ecommerce/index.blade.php`
- `resources/views/ecommerce/menu.blade.php`
- `resources/views/ecommerce/product.blade.php`
- `resources/views/ecommerce/stores.blade.php`
- `resources/views/ecommerce/cart.blade.php`
- `resources/views/auth/login.blade.php`

## Configuration
- `app/Providers/AppServiceProvider.php` (must have `Schema::defaultStringLength(191);` in boot method)

## Important Notes
1. Always keep a backup of these files
2. If files go missing, check if you're in the correct directory
3. Avoid using "Clean" or "Delete Unused Files" features in your IDE
4. The storage link must exist: `php artisan storage:link`

## Quick Restore Command
If files are missing, ask the AI assistant: "Full restore of the project"


