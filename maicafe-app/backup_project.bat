@echo off
echo Creating backup of Mai Cafe App project...
echo.

set BACKUP_DIR=backups
set TIMESTAMP=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%
set BACKUP_NAME=maicafe_backup_%TIMESTAMP%

if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

echo Backing up to: %BACKUP_DIR%\%BACKUP_NAME%
echo.

xcopy /E /I /Y app "%BACKUP_DIR%\%BACKUP_NAME%\app" >nul
xcopy /E /I /Y database "%BACKUP_DIR%\%BACKUP_NAME%\database" >nul
xcopy /E /I /Y resources "%BACKUP_DIR%\%BACKUP_NAME%\resources" >nul
xcopy /I /Y routes\web.php "%BACKUP_DIR%\%BACKUP_NAME%\routes\web.php" >nul
xcopy /I /Y app\Providers\AppServiceProvider.php "%BACKUP_DIR%\%BACKUP_NAME%\app\Providers\AppServiceProvider.php" >nul

echo Backup completed successfully!
echo Location: %BACKUP_DIR%\%BACKUP_NAME%
echo.
pause


