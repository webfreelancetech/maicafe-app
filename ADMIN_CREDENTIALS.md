# Admin Login Credentials

## Admin Account

**Email:** `admin@maicafe.com`  
**Password:** `admin123`

## How to Login

1. Go to the login page:
   ```
   http://localhost/maicafe-app/maicafe-app/public/login
   ```

2. Enter the credentials above

3. After login, you'll be redirected to:
   ```
   http://localhost/maicafe-app/maicafe-app/public/admin/dashboard
   ```

## Security Note

⚠️ **Important:** This is a default admin account with a simple password. For production use, you should:

1. Change the password to something more secure
2. Consider using a stronger password policy
3. Enable two-factor authentication if available

## To Change Admin Password

You can change the password through:
- Admin panel → Settings (if available)
- Or use Laravel Tinker:
  ```bash
  cd C:\wamp64\www\maicafe-app\maicafe-app
  php artisan tinker
  ```
  Then:
  ```php
  $admin = App\Models\User::where('email', 'admin@maicafe.com')->first();
  $admin->password = Hash::make('your_new_password');
  $admin->save();
  ```

---

**Admin user has been created and is ready to use!**

