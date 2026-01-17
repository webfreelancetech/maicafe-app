# Why Files Disappear After System Shutdown - WAMP Server

## The Problem
Files in your Laravel project (`C:\wamp64\www\maicafe-app`) are disappearing after you shut down your computer.

## Possible Causes & Solutions

### 1. **Windows Disk Cleanup or Antivirus** ⚠️ MOST LIKELY
**Problem**: Windows Disk Cleanup or antivirus software might be deleting files it thinks are "temporary" or "unused".

**Solution**:
- **Add exception in Windows Defender/Antivirus**:
  1. Open Windows Security
  2. Go to Virus & threat protection
  3. Click "Manage settings"
  4. Under "Exclusions", click "Add or remove exclusions"
  5. Add folder: `C:\wamp64\www\maicafe-app`
  
- **Disable Disk Cleanup for this folder**:
  1. Right-click `C:\wamp64\www\maicafe-app`
  2. Properties → General → Uncheck "Read-only" if checked
  3. Properties → Security → Make sure your user has "Full control"

### 2. **WAMP Cleanup Script** 🔍
**Problem**: WAMP might have a cleanup script that runs on shutdown.

**Solution**:
- Check `C:\wamp64\scripts\` for any cleanup scripts
- Check Windows Task Scheduler for WAMP-related tasks
- Disable any automatic cleanup tasks

### 3. **Laravel Cache Issues** 💾
**Problem**: Laravel's compiled views/cache might be cleared, making it seem like files are missing.

**Solution**:
- Run `php artisan optimize:clear` after startup
- Check if `bootstrap/cache/` folder is being deleted
- Check if `storage/framework/views/` is being cleared

### 4. **File Permissions** 🔐
**Problem**: Files might be getting deleted due to permission issues.

**Solution**:
```batch
# Run as Administrator
icacls "C:\wamp64\www\maicafe-app" /grant Users:F /T
```

### 5. **Symlink Issues** 🔗
**Problem**: The storage symlink might be broken after restart.

**Solution**:
- After each restart, run: `php artisan storage:link`
- Or create a startup script (see below)

## Immediate Actions

### Step 1: Check What's Actually Missing
Run `check_files.bat` to see which files are missing.

### Step 2: Create Startup Script
Create a file `startup_fix.bat` in your project root:
```batch
@echo off
cd /d C:\wamp64\www\maicafe-app
php artisan storage:link
php artisan optimize:clear
php artisan config:clear
echo Project initialized!
pause
```

### Step 3: Add to Windows Startup
1. Press `Win + R`, type `shell:startup`
2. Create a shortcut to `startup_fix.bat`
3. This will run automatically on startup

## Prevention Checklist

- [ ] Add project folder to antivirus exclusions
- [ ] Check Windows Task Scheduler for cleanup tasks
- [ ] Verify file permissions on project folder
- [ ] Create regular backups using `backup_project.bat`
- [ ] Run `check_files.bat` after each restart
- [ ] Keep `PROJECT_MANIFEST.md` as reference

## Diagnostic Commands

After restarting, run these to check:

```batch
# Check if files exist
dir app\Http\Controllers\Admin

# Check Laravel routes
php artisan route:list

# Check migrations
php artisan migrate:status

# Check storage link
dir public\storage
```

## If Files Are Still Missing

1. **Check Windows Event Viewer**:
   - Press `Win + X` → Event Viewer
   - Look for file deletion events

2. **Check Recycle Bin**:
   - Files might be there instead of deleted

3. **Check if project is in wrong location**:
   - Verify you're in `C:\wamp64\www\maicafe-app`
   - Not in `C:\Users\...\AppData\Local\Temp\`

4. **Restore from backup**:
   - Use `backup_project.bat` backups
   - Or ask AI: "Full restore of the project"

## Most Common Cause

**90% of the time, it's antivirus or Windows Defender deleting files it thinks are suspicious or temporary.**

**Solution**: Add the entire `C:\wamp64\www\` folder to antivirus exclusions.


