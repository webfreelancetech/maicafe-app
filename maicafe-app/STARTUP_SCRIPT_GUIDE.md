# Automated Startup Script - Installation Guide

## What It Does

The automated startup script (`auto_startup_fix.bat`) will:
1. ✅ Recreate storage symlinks
2. ✅ Clear Laravel caches
3. ✅ Check for missing critical files
4. ✅ Verify routes are working
5. ✅ Log everything to a log file
6. ✅ Show warnings if files are missing

## Installation

### Option 1: Automatic Installation (Recommended)

1. **Run the installer**:
   ```
   Double-click: install_startup_script.bat
   ```

2. **Done!** The script will now run automatically on every Windows startup.

### Option 2: Manual Installation

1. **Press `Win + R`**, type: `shell:startup`
   - This opens your Windows Startup folder

2. **Create a shortcut**:
   - Right-click in the Startup folder
   - New → Shortcut
   - Browse to: `C:\wamp64\www\maicafe-app\auto_startup_fix.bat`
   - Click Next → Finish

3. **Done!** The script will run on startup.

## How It Works

### On Windows Startup:
1. Script runs automatically (silently in background)
2. Checks all critical files
3. Fixes common issues (storage links, caches)
4. Creates a log file in: `%TEMP%\maicafe_startup_YYYYMMDD.log`

### If Files Are Missing:
- Creates a warning file: `%TEMP%\maicafe_status_warning.txt`
- Logs all missing files to the log file
- You'll see a notification when you log in (if running interactively)

### If Everything Is OK:
- Creates a success file: `%TEMP%\maicafe_status_ok.txt`
- Logs "All systems operational"

## Checking Status

### After Startup:
1. **Check the log file**:
   - Location: `C:\Users\YOUR_USERNAME\AppData\Local\Temp\maicafe_startup_YYYYMMDD.log`
   - Or press `Win + R`, type: `%TEMP%`
   - Look for files starting with `maicafe_startup_`

2. **Check status files**:
   - `%TEMP%\maicafe_status_ok.txt` - Everything is OK
   - `%TEMP%\maicafe_status_warning.txt` - Files are missing

3. **Run manual check**:
   ```
   check_files.bat
   ```

## Uninstallation

### Option 1: Automatic Uninstallation

1. **Run the uninstaller**:
   ```
   Double-click: uninstall_startup_script.bat
   ```

### Option 2: Manual Uninstallation

1. **Press `Win + R`**, type: `shell:startup`
2. **Delete**: `maicafe_startup_fix.bat.lnk`

## Log File Location

Log files are stored in:
```
C:\Users\YOUR_USERNAME\AppData\Local\Temp\maicafe_startup_YYYYMMDD.log
```

To quickly access:
- Press `Win + R`
- Type: `%TEMP%`
- Look for files starting with `maicafe_startup_`

## What Gets Checked

The script checks for these critical files:
- ✅ `app/Http/Controllers/EcommerceController.php`
- ✅ `app/Http/Controllers/Admin/DashboardController.php`
- ✅ `app/Http/Controllers/Admin/ProductController.php`
- ✅ `app/Models/Product.php`
- ✅ `app/Models/Category.php`
- ✅ `routes/web.php`
- ✅ `resources/views/layouts/admin.blade.php`
- ✅ `resources/views/admin/dashboard.blade.php`
- ✅ Laravel routes registration

## Troubleshooting

### Script Not Running on Startup

1. **Check if shortcut exists**:
   - Press `Win + R`, type: `shell:startup`
   - Look for `maicafe_startup_fix.bat.lnk`

2. **Check file permissions**:
   - Right-click `auto_startup_fix.bat` → Properties
   - Make sure it's not blocked

3. **Run manually to test**:
   ```
   auto_startup_fix.bat
   ```

### Script Shows Errors

1. **Check PHP path**:
   - Make sure PHP is in your system PATH
   - Or WAMP is running

2. **Check project path**:
   - Script expects: `C:\wamp64\www\maicafe-app`
   - If different, edit `auto_startup_fix.bat` and change the path

3. **Check log file**:
   - Look in `%TEMP%\maicafe_startup_*.log` for detailed errors

## Advanced: Customize the Script

You can edit `auto_startup_fix.bat` to:
- Add more file checks
- Change log location
- Add email notifications
- Run additional commands

## Benefits

✅ **Automatic**: Runs every time you start Windows
✅ **Silent**: Doesn't interrupt your workflow
✅ **Logged**: All checks are logged for review
✅ **Smart**: Only shows warnings when needed
✅ **Fast**: Completes in seconds

## Next Steps

1. **Install the startup script**: Run `install_startup_script.bat`
2. **Restart your computer** to test
3. **Check the log file** after restart
4. **If files are missing**: Ask AI: "Full restore of the project"

---

**Note**: The script runs silently in the background. You won't see a window unless files are missing (then it shows a brief notification).


