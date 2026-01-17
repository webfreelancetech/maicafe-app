# How to Prevent Files from Going Missing

## Common Causes

1. **Accidental Deletion in IDE**: You might be accidentally deleting files when:
   - Using "Find and Replace" with delete operations
   - Using "Clean" or "Optimize" features
   - Right-clicking and deleting files/folders
   - Using keyboard shortcuts that delete files

2. **IDE Auto-Cleanup**: Some IDEs have features that automatically remove "unused" files. Check your IDE settings:
   - **VS Code**: Check for extensions that clean up files
   - **PhpStorm**: Check "Code > Optimize Imports" settings
   - **Cursor**: Check for auto-cleanup features

3. **Working in Wrong Directory**: Make sure you're always in:
   ```
   C:\wamp64\www\maicafe-app
   ```

4. **File System Issues**: Sometimes Windows file system can have issues. Check:
   - Disk space
   - File permissions
   - Antivirus software (might be deleting files)

## Prevention Tips

1. **Create Regular Backups**: Run `backup_project.bat` regularly
2. **Use Version Control**: Consider installing Git to track changes
3. **Be Careful with IDE Features**: Avoid "Clean" or "Delete Unused" features
4. **Check Before Deleting**: Always verify what you're deleting
5. **Keep PROJECT_MANIFEST.md**: This file lists all critical files

## If Files Go Missing

1. **Check the backup folder**: Look in `backups/` directory
2. **Ask AI for restore**: Say "Full restore of the project"
3. **Check PROJECT_MANIFEST.md**: Verify which files are missing
4. **Check Recycle Bin**: Files might be in Windows Recycle Bin

## Quick Commands

- **Create Backup**: Double-click `backup_project.bat`
- **Check Routes**: `php artisan route:list`
- **Clear Cache**: `php artisan optimize:clear`
- **Check Files**: `php artisan migrate:status`

## Important Files to Never Delete

- All files in `app/Http/Controllers/`
- All files in `app/Models/`
- All files in `resources/views/`
- All files in `database/migrations/`
- `routes/web.php`
- `app/Providers/AppServiceProvider.php`


