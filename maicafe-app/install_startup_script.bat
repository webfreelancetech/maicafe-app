@echo off
REM ========================================
REM Install Mai Cafe App Startup Script
REM This will add the auto_startup_fix.bat to Windows Startup
REM ========================================

echo ========================================
echo Mai Cafe App - Startup Script Installer
echo ========================================
echo.
echo This will add auto_startup_fix.bat to Windows Startup folder
echo The script will run automatically every time you start Windows.
echo.

set STARTUP_FOLDER=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup
set SCRIPT_NAME=maicafe_startup_fix.bat
set CURRENT_DIR=%~dp0

echo Current project directory: %CURRENT_DIR%
echo Startup folder: %STARTUP_FOLDER%
echo.

REM Check if startup folder exists
if not exist "%STARTUP_FOLDER%" (
    echo ERROR: Startup folder not found!
    echo Path: %STARTUP_FOLDER%
    pause
    exit /b 1
)

REM Check if auto_startup_fix.bat exists
if not exist "%CURRENT_DIR%auto_startup_fix.bat" (
    echo ERROR: auto_startup_fix.bat not found in current directory!
    echo Please run this script from the project root directory.
    pause
    exit /b 1
)

REM Create shortcut in startup folder
echo Creating shortcut in Startup folder...
powershell -Command "$WshShell = New-Object -ComObject WScript.Shell; $Shortcut = $WshShell.CreateShortcut('%STARTUP_FOLDER%\%SCRIPT_NAME%.lnk'); $Shortcut.TargetPath = '%CURRENT_DIR%auto_startup_fix.bat'; $Shortcut.WorkingDirectory = '%CURRENT_DIR%'; $Shortcut.Description = 'Mai Cafe App - Auto Startup Fix'; $Shortcut.Save()"

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo SUCCESS: Startup script installed!
    echo ========================================
    echo.
    echo The script will now run automatically on Windows startup.
    echo.
    echo To remove it later:
    echo 1. Press Win+R, type: shell:startup
    echo 2. Delete: %SCRIPT_NAME%.lnk
    echo.
) else (
    echo.
    echo ERROR: Failed to create shortcut!
    echo Please run this script as Administrator.
    echo.
)

pause


