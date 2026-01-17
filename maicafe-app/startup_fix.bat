@echo off
echo ========================================
echo Mai Cafe App - Startup Fix
echo ========================================
echo.

cd /d C:\wamp64\www\maicafe-app

echo [1/4] Recreating storage link...
php artisan storage:link >nul 2>&1
if %errorlevel% equ 0 (
    echo   [OK] Storage link created
) else (
    echo   [OK] Storage link already exists
)

echo.
echo [2/4] Clearing Laravel caches...
php artisan optimize:clear >nul 2>&1
echo   [OK] Caches cleared

echo.
echo [3/4] Checking critical files...
set MISSING=0

if not exist "app\Http\Controllers\EcommerceController.php" set /a MISSING+=1
if not exist "app\Http\Controllers\Admin\DashboardController.php" set /a MISSING+=1
if not exist "routes\web.php" set /a MISSING+=1
if not exist "resources\views\layouts\admin.blade.php" set /a MISSING+=1

if %MISSING% GTR 0 (
    echo   [WARNING] %MISSING% critical file(s) missing!
    echo   [ACTION] Run check_files.bat for details
    echo   [ACTION] Or ask AI: "Full restore of the project"
) else (
    echo   [OK] All critical files present
)

echo.
echo [4/4] Verifying routes...
php artisan route:list --path=admin >nul 2>&1
if %errorlevel% equ 0 (
    echo   [OK] Routes are registered
) else (
    echo   [ERROR] Routes not working - files may be missing
)

echo.
echo ========================================
echo Startup check complete!
echo ========================================
echo.
echo If files are missing, run: check_files.bat
echo Or restore project with AI assistance
echo.
timeout /t 5 >nul


