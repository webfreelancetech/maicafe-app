@echo off
echo ========================================
echo Mai Cafe App - File Integrity Check
echo ========================================
echo.

echo Checking critical files...
echo.

set MISSING=0

echo [1/5] Checking Controllers...
if not exist "app\Http\Controllers\EcommerceController.php" (
    echo   [X] MISSING: EcommerceController.php
    set /a MISSING+=1
) else (
    echo   [OK] EcommerceController.php
)

if not exist "app\Http\Controllers\Admin\DashboardController.php" (
    echo   [X] MISSING: DashboardController.php
    set /a MISSING+=1
) else (
    echo   [OK] DashboardController.php
)

if not exist "app\Http\Controllers\Admin\ProductController.php" (
    echo   [X] MISSING: ProductController.php
    set /a MISSING+=1
) else (
    echo   [OK] ProductController.php
)

echo.
echo [2/5] Checking Models...
if not exist "app\Models\Product.php" (
    echo   [X] MISSING: Product.php
    set /a MISSING+=1
) else (
    echo   [OK] Product.php
)

if not exist "app\Models\Category.php" (
    echo   [X] MISSING: Category.php
    set /a MISSING+=1
) else (
    echo   [OK] Category.php
)

echo.
echo [3/5] Checking Routes...
if not exist "routes\web.php" (
    echo   [X] MISSING: web.php
    set /a MISSING+=1
) else (
    echo   [OK] web.php
)

echo.
echo [4/5] Checking Views...
if not exist "resources\views\layouts\admin.blade.php" (
    echo   [X] MISSING: admin.blade.php
    set /a MISSING+=1
) else (
    echo   [OK] admin.blade.php
)

if not exist "resources\views\admin\dashboard.blade.php" (
    echo   [X] MISSING: dashboard.blade.php
    set /a MISSING+=1
) else (
    echo   [OK] dashboard.blade.php
)

echo.
echo [5/5] Checking Migrations...
if not exist "database\migrations\2025_01_01_000001_create_categories_table.php" (
    echo   [X] MISSING: categories migration
    set /a MISSING+=1
) else (
    echo   [OK] categories migration
)

echo.
echo ========================================
if %MISSING% EQU 0 (
    echo All critical files are present!
    echo Status: OK
) else (
    echo WARNING: %MISSING% file(s) are missing!
    echo Status: NEEDS RESTORE
)
echo ========================================
echo.
echo Current Directory: %CD%
echo.
pause


