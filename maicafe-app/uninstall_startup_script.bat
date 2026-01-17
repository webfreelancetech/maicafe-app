@echo off
REM ========================================
REM Uninstall Mai Cafe App Startup Script
REM This will remove the startup script from Windows Startup
REM ========================================

echo ========================================
echo Mai Cafe App - Startup Script Uninstaller
echo ========================================
echo.

set STARTUP_FOLDER=%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup
set SCRIPT_NAME=maicafe_startup_fix.bat

echo Removing startup script...
echo.

if exist "%STARTUP_FOLDER%\%SCRIPT_NAME%.lnk" (
    del "%STARTUP_FOLDER%\%SCRIPT_NAME%.lnk"
    if %errorlevel% equ 0 (
        echo SUCCESS: Startup script removed!
        echo.
        echo The script will no longer run on Windows startup.
    ) else (
        echo ERROR: Failed to remove startup script.
        echo Please try running as Administrator.
    )
) else (
    echo INFO: Startup script not found. It may already be removed.
)

echo.
pause


