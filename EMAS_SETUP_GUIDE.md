# EMAS Marker Panel - Quick Setup Guide

## Overview
This guide will help you set up and test the EMAS Marker Panel for marks entry personnel.

## Prerequisites
- PHP 8.1 or higher
- Composer installed
- Node.js and npm installed
- MySQL/MariaDB database
- Laravel application running

## Installation Steps

### Step 1: Run Database Migrations

Run the new migration to add marker role support:

```bash
php artisan migrate
```

This will execute the migration: `2025_11_03_000001_add_marker_role_to_emas_users.php`

### Step 2: Seed Test Marker Accounts

Create test marker accounts for testing:

```bash
php artisan db:seed --class=EmasMarkerSeeder
```

This creates three test accounts:
- **Username**: `marker`, **Password**: `marker` (Dar es Salaam)
- **Username**: `marker_arusha`, **Password**: `marker` (Arusha)
- **Username**: `marker_mwanza`, **Password**: `marker` (Mwanza)

### Step 3: Start Development Servers

Start both the Laravel and Vite servers:

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

### Step 4: Test the Marker Panel

1. Open your browser and navigate to: `http://localhost:8000/emas/login`
2. Login with marker credentials:
   - Username: `marker`
   - Password: `marker`
3. You should be automatically redirected to: `http://localhost:8000/emas/marker/dashboard`

## File Structure Created

```
New Files Created:
├── app/Http/Controllers/Emas/EmasMarkerDashboardController.php
├── app/Http/Middleware/EmasMarkerMiddleware.php
├── database/migrations/2025_11_03_000001_add_marker_role_to_emas_users.php
├── database/seeders/EmasMarkerSeeder.php
├── resources/js/Layouts/MarkerLayout.vue
├── resources/js/Pages/Emas/Marker/Dashboard.vue
├── resources/js/Pages/Emas/Marker/EnterMarks.vue
├── resources/js/Pages/Emas/Marker/History.vue
├── EMAS_MARKER_PANEL_README.md (Full documentation)
└── EMAS_SETUP_GUIDE.md (This file)

Modified Files:
├── app/Models/EmasUser.php (Added isMarker() and getRoleDisplayName())
├── app/Http/Controllers/Emas/EmasAuthController.php (Added role-based redirection)
├── routes/web.php (Added marker panel routes)
└── bootstrap/app.php (Registered EmasMarkerMiddleware)
```

## Testing Workflow

### 1. Test Login and Redirection
- Login as marker
- Verify automatic redirect to marker dashboard
- Check that only marker-specific menu items are visible

### 2. Test Dashboard
- Verify statistics cards display correctly
- Check assigned exams list
- Ensure navigation works

### 3. Test Marks Entry
- Click "Enter Marks" on an exam
- Enter scores for candidates
- Verify automatic grade calculation
- Save marks and check for success message

### 4. Test History
- Navigate to marking history
- Verify marks appear in the list
- Test search functionality

## Creating Additional Marker Users

### Via Database
```sql
INSERT INTO emas_users (name, username, email, phone, role, district, region, password, created_at, updated_at)
VALUES ('New Marker', 'newmarker', 'newmarker@emas.com', '0700000004', 'marker', 'Dodoma', 'Dodoma', '$2y$12$hashedpassword', NOW(), NOW());
```

### Via Tinker
```bash
php artisan tinker
```

```php
use App\Models\EmasUser;
use Illuminate\Support\Facades\Hash;

EmasUser::create([
    'name' => 'New Marker',
    'username' => 'newmarker',
    'email' => 'newmarker@emas.com',
    'phone' => '0700000004',
    'role' => 'marker',
    'district' => 'Dodoma',
    'region' => 'Dodoma',
    'password' => Hash::make('password123'),
]);
```

### Via Registration (Then Update)
1. Register a new EMAS user normally
2. Update their role to 'marker':
```sql
UPDATE emas_users SET role = 'marker' WHERE username = 'username';
```

## Routes Available

### Authentication Routes (Public)
- `GET /emas/login` - Login page
- `POST /emas/login` - Login action
- `GET /emas/register` - Registration page
- `POST /emas/register` - Registration action
- `POST /emas/logout` - Logout action

### Marker Panel Routes (Protected by emas.marker middleware)
- `GET /emas/marker/dashboard` - Marker dashboard
- `GET /emas/marker/exams/{exam}/marks` - Marks entry form
- `POST /emas/marker/exams/{exam}/marks` - Save marks
- `GET /emas/marker/history` - Marking history

## Role-Based Access

| Role | Access to EMAS Panel | Access to Marker Panel |
|------|---------------------|------------------------|
| Coordinator | ✅ Yes | ❌ No |
| Supervisor | ✅ Yes | ❌ No |
| Examiner | ✅ Yes | ❌ No |
| Marker | ❌ No | ✅ Yes |

**Note**: Markers have a completely separate, simplified interface focused only on marks entry tasks.

## Troubleshooting

### Issue: Cannot login as marker
**Check**:
1. Verify user exists: `SELECT * FROM emas_users WHERE username = 'marker';`
2. Verify role is 'marker': Check the `role` column
3. Clear browser cache and cookies
4. Check Laravel logs: `storage/logs/laravel.log`

### Issue: 403 Forbidden on marker routes
**Solution**:
- Ensure the logged-in user has `role = 'marker'`
- Check middleware is registered in `bootstrap/app.php`
- Verify session is active

### Issue: Dashboard not loading
**Check**:
1. Vite dev server is running: `npm run dev`
2. Check browser console for errors
3. Verify Vue component paths are correct
4. Clear Laravel cache: `php artisan cache:clear`

### Issue: Grades not calculating
**Solution**:
1. Ensure score is entered as a number
2. Check that `total_marks` is set for the exam
3. Open browser console to check for JavaScript errors

## Next Steps

### For Development
1. Create sample exams and candidates for testing
2. Test marks entry with various scenarios
3. Verify grade calculation logic
4. Test on different screen sizes (mobile responsiveness)

### For Production
1. Change default passwords for all seeded accounts
2. Set up proper backup procedures
3. Configure environment variables
4. Enable error logging and monitoring
5. Set up SSL/HTTPS
6. Configure rate limiting for API endpoints

## Support

For detailed documentation, see: `EMAS_MARKER_PANEL_README.md`

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Check browser console for JavaScript errors
- Review the full documentation
- Contact the development team

---

**Quick Test Commands**:
```bash
# Run all migrations
php artisan migrate

# Seed marker accounts
php artisan db:seed --class=EmasMarkerSeeder

# Start servers
php artisan serve & npm run dev

# Clear cache if issues occur
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Test URL**: `http://localhost:8000/emas/login`

**Test Credentials**:
- Username: `marker`
- Password: `marker`

✅ **Setup Complete!** You can now test the EMAS Marker Panel.
