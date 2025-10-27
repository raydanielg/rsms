# Admin Panel Documentation

## Overview
A complete admin panel has been successfully implemented for the RSMS (Result Management System). The admin panel provides a separate interface for system administrators with role-based access control.

## Features Implemented

### 1. **Admin Layout** (`resources/js/Layouts/AdminLayout.vue`)
- Purple-themed design to distinguish from regular user interface
- Sidebar navigation with admin-specific menu items
- Responsive design for mobile and desktop
- Toast notifications system
- Quick access to:
  - Dashboard
  - User Management (Coming Soon)
  - Schools Management (Coming Soon)
  - Reports (Coming Soon)
  - System Settings (Coming Soon)
  - Activity Logs (Coming Soon)

### 2. **Admin Dashboard** (`resources/js/Pages/Admin/Dashboard.vue`)
- Statistics cards showing:
  - Total Users
  - Total Schools
  - Total Students
  - Total Teachers
- "Coming Soon" section highlighting future features
- Quick action buttons for common tasks

### 3. **Role-Based Authentication**
- Added `role` field to users table (default: 'user')
- User model methods:
  - `isAdmin()` - Check if user is admin
  - `isUser()` - Check if user is regular user
- Admin middleware to protect admin routes
- Automatic redirect based on role after login:
  - Admins → `/admin/dashboard`
  - Regular users → `/dashboard`

### 4. **Database Changes**
- **Migration**: `2025_10_27_000002_add_role_to_users_table.php`
  - Adds `role` column with default value 'user'
  - Indexed for performance
- **Seeder**: `AdminSeeder.php`
  - Creates default admin account
  - Credentials:
    - **Username**: `admin`
    - **Password**: `admin`
    - **Email**: `admin@rsms.com`

### 5. **Admin Routes**
All admin routes are protected by `auth` and `admin` middleware:
- `GET /admin/dashboard` - Admin dashboard

### 6. **Security**
- `AdminMiddleware` ensures only users with role='admin' can access admin routes
- Returns 403 Forbidden for unauthorized access attempts
- Redirects to login if not authenticated

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       └── AdminDashboardController.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   └── User.php (updated with role methods)

database/
├── migrations/
│   └── 2025_10_27_000002_add_role_to_users_table.php
└── seeders/
    ├── AdminSeeder.php
    └── DatabaseSeeder.php (updated)

resources/
└── js/
    ├── Layouts/
    │   └── AdminLayout.vue
    └── Pages/
        └── Admin/
            └── Dashboard.vue

routes/
└── web.php (updated with admin routes)

bootstrap/
└── app.php (middleware registration)
```

## Usage

### Accessing Admin Panel
1. Navigate to the login page
2. Enter admin credentials:
   - Username: `admin`
   - Password: `admin`
3. You'll be automatically redirected to `/admin/dashboard`

### Creating Additional Admin Users
You can create additional admin users by:
1. Registering a regular user
2. Manually updating their role in the database:
   ```sql
   UPDATE users SET role = 'admin' WHERE username = 'desired_username';
   ```

Or programmatically:
```php
$user = User::find($id);
$user->role = 'admin';
$user->save();
```

### Checking User Role in Code
```php
// In controllers
if (auth()->user()->isAdmin()) {
    // Admin-specific logic
}

// In Blade/Vue
@if(auth()->user()->isAdmin())
    <!-- Admin content -->
@endif
```

## Future Enhancements (Coming Soon)

The admin panel is designed to be extensible. Planned features include:

1. **User Management**
   - View all users
   - Edit user details
   - Change user roles
   - Suspend/activate accounts

2. **School Management**
   - View all registered schools
   - Approve/reject school registrations
   - Edit school information

3. **Advanced Reports**
   - System-wide analytics
   - User activity reports
   - Performance metrics

4. **System Settings**
   - Configure system parameters
   - Manage application settings
   - Email templates

5. **Activity Logs**
   - Track user actions
   - Monitor system events
   - Audit trail

## Security Recommendations

⚠️ **IMPORTANT**: Change the default admin password immediately after first login!

1. After first login, go to Profile settings
2. Change password from `admin` to a strong password
3. Consider implementing:
   - Two-factor authentication
   - Password complexity requirements
   - Session timeout for admin users
   - IP whitelisting for admin access

## Testing

To test the admin panel:

1. **Run migrations** (if not already done):
   ```bash
   php artisan migrate
   ```

2. **Seed admin user**:
   ```bash
   php artisan db:seed --class=AdminSeeder
   ```

3. **Start development server**:
   ```bash
   php artisan serve
   npm run dev
   ```

4. **Login with admin credentials**:
   - Visit: `http://localhost:8000/login`
   - Username: `admin`
   - Password: `admin`

5. **Verify redirect**:
   - Should automatically redirect to `/admin/dashboard`

## Troubleshooting

### Issue: 403 Forbidden when accessing admin routes
**Solution**: Ensure the logged-in user has `role = 'admin'` in the database.

### Issue: Not redirecting to admin dashboard after login
**Solution**: Clear browser cache and session, then login again.

### Issue: Admin seeder fails
**Solution**: Check that all required fields are present in the seeder (name, username, email, phone, region, school_name, school_number).

## Support

For issues or questions about the admin panel, please contact the development team.

---

**Version**: 1.0  
**Last Updated**: October 26, 2025  
**Status**: ✅ Fully Functional
