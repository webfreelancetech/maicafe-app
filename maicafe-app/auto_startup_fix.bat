@echo off
REM ========================================
REM Mai Cafe App - Auto Startup Fix
REM This script runs automatically on Windows startup
REM ========================================

REM Change to project directory
cd /d C:\wamp64\www\maicafe-app 2>nul
if errorlevel 1 (
    echo ERROR: Cannot access project directory: C:\wamp64\www\maicafe-app
    echo Please check if the path is correct.
    exit /b 1
)

REM Create log file
set LOGFILE=%TEMP%\maicafe_startup_%date:~-4,4%%date:~-10,2%%date:~-7,2%.log
echo ======================================== >> %LOGFILE%
echo Mai Cafe App - Startup Check >> %LOGFILE%
echo Date: %date% %time% >> %LOGFILE%
echo ======================================== >> %LOGFILE%
echo. >> %LOGFILE%

REM Step 1: Recreate storage link
echo [%time%] [1/5] Recreating storage link... >> %LOGFILE%
php artisan storage:link >nul 2>&1
if %errorlevel% equ 0 (
    echo   [OK] Storage link created/verified >> %LOGFILE%
) else (
    echo   [WARNING] Storage link creation failed >> %LOGFILE%
)

REM Step 2: Clear Laravel caches
echo [%time%] [2/5] Clearing Laravel caches... >> %LOGFILE%
php artisan optimize:clear >nul 2>&1
if %errorlevel% equ 0 (
    echo   [OK] Caches cleared >> %LOGFILE%
) else (
    echo   [WARNING] Cache clearing failed >> %LOGFILE%
)

REM Step 3: Check critical files
echo [%time%] [3/5] Checking critical files... >> %LOGFILE%
set MISSING=0
set MISSING_FILES=

if not exist "app\Http\Controllers\EcommerceController.php" (
    set /a MISSING+=1
    set MISSING_FILES=%MISSING_FILES% EcommerceController.php
    echo   [X] MISSING: EcommerceController.php >> %LOGFILE%
) else (
    echo   [OK] EcommerceController.php >> %LOGFILE%
)

if not exist "app\Http\Controllers\Admin\DashboardController.php" (
    set /a MISSING+=1
    set MISSING_FILES=%MISSING_FILES% DashboardController.php
    echo   [X] MISSING: DashboardController.php >> %LOGFILE%
) else (
    echo   [OK] DashboardController.php >> %LOGFILE%
)

if not exist "app\Http\Controllers\Admin\ProductController.php" (
    set /a MISSING+=1
    set MISSING_FILES=%MISSING_FILES% ProductController.php
    echo   [X] MISSING: ProductController.php >> %LOGFILE%
) else (
    echo   [OK] ProductController.php >> %LOGFILE%
)

if not exist "app\Models\Product.php" (
    set /a MISSING+=1
    set MISSING_FILES=%MISSING_FILES% Product.php
    echo   [X] MISSING: Product.php >> %LOGFILE%
) else (
    echo   [OK] Product.php >> %LOGFILE%
)

if not exist "app\Models\Category.php" (
    set /a MISSING+=1
    set MISSING_FILES=%MISSING_FILES% Category.php
    echo   [X] MISSING: Category.php >> %LOGFILE%
) else (
    echo   [OK] Category.php >> %LOGFILE%
)

if not exist "routes\web.php" (
    set /a MISSING+=1
    set MISSING_FILES=%MISSING_FILES% web.php
    echo   [X] MISSING: web.php >> %LOGFILE%
) else (
    echo   [OK] web.php >> %LOGFILE%
)

if not exist "resources\views\layouts\admin.blade.php" (
    set /a MISSING+=1
    set MISSING_FILES=%MISSING_FILES% admin.blade.php
    echo   [X] MISSING: admin.blade.php >> %LOGFILE%
) else (
    echo   [OK] admin.blade.php >> %LOGFILE%
)

if not exist "resources\views\admin\dashboard.blade.php" (
    set /a MISSING+=1
    set MISSING_FILES=%MISSING_FILES% dashboard.blade.php
    echo   [X] MISSING: dashboard.blade.php >> %LOGFILE%
) else (
    echo   [OK] dashboard.blade.php >> %LOGFILE%
)

REM Step 4: Verify routes
echo [%time%] [4/5] Verifying routes... >> %LOGFILE%
php artisan route:list --path=admin >nul 2>&1
if %errorlevel% equ 0 (
    echo   [OK] Routes are registered >> %LOGFILE%
) else (
    echo   [ERROR] Routes not working >> %LOGFILE%
    set /a MISSING+=1
)

REM Step 5: Summary
echo [%time%] [5/5] Summary... >> %LOGFILE%
echo. >> %LOGFILE%
if %MISSING% EQU 0 (
    echo ======================================== >> %LOGFILE%
    echo STATUS: ALL OK - All files present! >> %LOGFILE%
    echo ======================================== >> %LOGFILE%
    echo. >> %LOGFILE%
    REM Create success notification file
    echo All systems operational > "%TEMP%\maicafe_status_ok.txt"
) else (
    echo ======================================== >> %LOGFILE%
    echo STATUS: WARNING - %MISSING% file(s) missing! >> %LOGFILE%
    echo Missing files:%MISSING_FILES% >> %LOGFILE%
    echo ======================================== >> %LOGFILE%
    echo. >> %LOGFILE%
    echo ACTION REQUIRED: >> %LOGFILE%
    echo 1. Run check_files.bat for detailed report >> %LOGFILE%
    echo 2. Ask AI assistant: "Full restore of the project" >> %LOGFILE%
    echo 3. Check log file: %LOGFILE% >> %LOGFILE%
    echo. >> %LOGFILE%
    REM Create warning notification file
    echo WARNING: %MISSING% files missing! Check log: %LOGFILE% > "%TEMP%\maicafe_status_warning.txt"
)

REM Show notification if files are missing (only if running interactively)
if not "%SESSIONNAME%"=="Console" (
    REM Running in background, exit silently
    exit /b 0
)

REM If running interactively, show brief summary
if %MISSING% GTR 0 (
    echo.
    echo ========================================
    echo WARNING: %MISSING% file(s) are missing!
    echo ========================================
    echo Missing files:%MISSING_FILES%
    echo.
    echo Log file: %LOGFILE%
    echo.
    echo Run check_files.bat for detailed report
    echo Or ask AI: "Full restore of the project"
    echo.
    timeout /t 10
) else (
    echo.
    echo ========================================
    echo All systems operational!
    echo ========================================
    echo.
    timeout /t 3
)

exit /b 0


