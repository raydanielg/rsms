# EMAS Marker Panel Documentation

## Overview
The EMAS Marker Panel is a specialized interface designed for marks entry personnel within the Exam Management & Administration System (EMAS). This panel provides a streamlined, focused environment for examiners to enter and manage exam marks efficiently.

## Features Implemented

### 1. **Role-Based Authentication**
- Added `marker` role to EMAS users
- Separate authentication flow with role-based redirection
- Upon login, markers are automatically redirected to their dedicated panel
- Security middleware ensures only authorized markers can access marking functions

### 2. **Marker-Specific Layout** (`resources/js/Layouts/MarkerLayout.vue`)
- **Blue-themed design** to distinguish from coordinator/supervisor interface
- Simplified navigation focused on marks entry tasks
- Responsive design for mobile and desktop devices
- Toast notification system for user feedback
- Quick stats sidebar showing:
  - Marks entered today
  - Total marks entered
  - Real-time progress indicators

### 3. **Marker Dashboard** (`resources/js/Pages/Emas/Marker/Dashboard.vue`)
- Statistics cards displaying:
  - Total marks entered
  - Today's entries
  - Assigned exams count
  - Pending exams
- List of assigned exams with:
  - Exam details (name, code, subject)
  - Exam date and time
  - Current status (ongoing/completed)
  - Quick "Enter Marks" action button
- Recent activity feed showing latest mark entries

### 4. **Marks Entry Interface** (`resources/js/Pages/Emas/Marker/EnterMarks.vue`)
- **Intuitive table-based marks entry**
- Features:
  - Real-time progress tracking (marked vs unmarked)
  - Search and filter functionality
  - Auto-calculation of grades based on score
  - Auto-calculation of remarks (Excellent, Very Good, Good, Satisfactory, Fail)
  - Optional comments field for each candidate
  - Bulk save functionality
  - Visual indicators for marked/unmarked candidates
  - Score validation (0 to total marks)
- Grade calculation logic:
  - A: ≥ 75%
  - B: 65-74%
  - C: 50-64%
  - D: 40-49%
  - E: 30-39%
  - F: < 30%

### 5. **Marking History** (`resources/js/Pages/Emas/Marker/History.vue`)
- Comprehensive view of all marks entered by the marker
- Paginated list with:
  - Date and time of entry
  - Exam details
  - Candidate information
  - Score, grade, and remarks
- Search functionality across candidates and exams
- Audit trail for accountability

### 6. **Database & Models**
- **Migration**: `2025_11_03_000001_add_marker_role_to_emas_users.php`
  - Adds 'marker' as a valid role option for EMAS users
- **EmasUser Model** updated with:
  - `isMarker()` - Check if user has marker role
  - `getRoleDisplayName()` - Get human-readable role name
- **EmasMarkerDashboardController** - Handles all marker panel operations

### 7. **Security & Middleware**
- `EmasMarkerMiddleware` - Ensures only users with 'marker' role can access marker routes
- Returns 403 Forbidden for unauthorized access attempts
- Automatic redirect to login if not authenticated
- All marker routes protected by `emas.marker` middleware

### 8. **Routes Structure**
All marker routes are under `/emas/marker/` prefix:
- `GET /emas/marker/dashboard` - Marker dashboard
- `GET /emas/marker/exams/{exam}/marks` - Marks entry form
- `POST /emas/marker/exams/{exam}/marks` - Save marks
- `GET /emas/marker/history` - Marking history

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Emas/
│   │       ├── EmasAuthController.php (updated with role redirection)
│   │       └── EmasMarkerDashboardController.php
│   └── Middleware/
│       └── EmasMarkerMiddleware.php
├── Models/
│   └── EmasUser.php (updated with marker methods)

database/
├── migrations/
│   └── 2025_11_03_000001_add_marker_role_to_emas_users.php
└── seeders/
    └── EmasMarkerSeeder.php

resources/
└── js/
    ├── Layouts/
    │   └── MarkerLayout.vue
    └── Pages/
        └── Emas/
            └── Marker/
                ├── Dashboard.vue
                ├── EnterMarks.vue
                └── History.vue

routes/
└── web.php (updated with marker routes)

bootstrap/
└── app.php (middleware registration)
```

## Usage

### Setup & Installation

1. **Run migrations**:
   ```bash
   php artisan migrate
   ```

2. **Seed marker test accounts**:
   ```bash
   php artisan db:seed --class=EmasMarkerSeeder
   ```

3. **Start development servers**:
   ```bash
   php artisan serve
   npm run dev
   ```

### Accessing Marker Panel

1. Navigate to EMAS login page: `http://localhost:8000/emas/login`
2. Login with marker credentials:
   - **Username**: `marker`
   - **Password**: `marker`
3. You'll be automatically redirected to `/emas/marker/dashboard`

### Creating Additional Marker Users

#### Method 1: Manual Database Update
```sql
UPDATE emas_users SET role = 'marker' WHERE username = 'desired_username';
```

#### Method 2: Programmatically
```php
$user = EmasUser::find($id);
$user->role = 'marker';
$user->save();
```

#### Method 3: During Registration
When creating a new EMAS user through the registration form, set the role field to 'marker'.

### Entering Marks Workflow

1. **Login** to marker panel
2. **View assigned exams** on dashboard
3. **Click "Enter Marks"** button for an exam
4. **Enter scores** for each candidate in the table
   - Grades and remarks are calculated automatically
   - Add optional comments if needed
5. **Save marks** using the "Save Marks" button
6. **View history** to see all previously entered marks

## Testing Accounts

The seeder creates the following test accounts:

| Username | Password | Role | Region | District |
|----------|----------|------|--------|----------|
| marker | marker | marker | Dar es Salaam | Dar es Salaam |
| marker_arusha | marker | marker | Arusha | Arusha Urban |
| marker_mwanza | marker | marker | Mwanza | Mwanza City |

## Role-Based Redirection Logic

When users log into EMAS, they are automatically redirected based on their role:

- **Marker** → `/emas/marker/dashboard` (Marker Panel)
- **Coordinator** → `/emas/dashboard` (Full EMAS Dashboard)
- **Supervisor** → `/emas/dashboard` (Full EMAS Dashboard)
- **Examiner** → `/emas/dashboard` (Full EMAS Dashboard)

This ensures markers have a focused, simplified interface while coordinators and supervisors retain access to all administrative functions.

## API Endpoints

### Authentication
- `POST /emas/login` - Login with automatic role-based redirection
- `POST /emas/logout` - Logout from marker panel

### Marker Operations
- `GET /emas/marker/dashboard` - Fetch dashboard data
- `GET /emas/marker/exams/{exam}/marks` - Fetch exam and candidates for marking
- `POST /emas/marker/exams/{exam}/marks` - Submit marks for candidates
- `GET /emas/marker/history` - Fetch marking history (paginated)

## Security Features

### Access Control
- Only users with `role = 'marker'` can access marker panel routes
- Middleware verification on every request
- Session-based authentication with CSRF protection

### Data Validation
- Score must be between 0 and exam total marks
- Candidate ID must exist in database
- Grade format validation (A-F)
- Remarks must be one of predefined values

### Audit Trail
- All mark entries are logged with:
  - Marker ID (uploaded_by)
  - Timestamp (created_at, updated_at)
  - Complete candidate and exam details

## Troubleshooting

### Issue: 403 Forbidden when accessing marker routes
**Solution**: Ensure the logged-in user has `role = 'marker'` in the `emas_users` table.

### Issue: Not redirecting to marker dashboard after login
**Solution**: 
1. Clear browser cache and session
2. Verify user role in database
3. Check `EmasAuthController::getRedirectRoute()` logic

### Issue: Grades not calculating automatically
**Solution**: 
1. Ensure score is entered as a number
2. Check that total marks are set correctly for the exam
3. Verify `calculateGrade()` method in EnterMarks.vue

### Issue: Cannot save marks
**Solution**:
1. Check that at least one candidate has a score entered
2. Verify CSRF token is valid
3. Check browser console for JavaScript errors
4. Ensure exam ID is valid

## Performance Considerations

- Marks are saved in bulk to reduce database queries
- Pagination on history page (20 records per page)
- Search and filter operations performed client-side for speed
- Auto-calculation of grades happens in real-time without server requests

## Future Enhancements

Planned features for future versions:

1. **Bulk Import**
   - Excel/CSV import for marks
   - Template download functionality

2. **Offline Support**
   - Progressive Web App (PWA) capabilities
   - Local storage for temporary mark entries
   - Sync when connection restored

3. **Advanced Validation**
   - Configurable grade boundaries per exam
   - Warning for outlier scores
   - Duplicate entry detection

4. **Analytics**
   - Marker performance metrics
   - Average marking time per candidate
   - Error rate tracking

5. **Mobile App**
   - Native iOS/Android applications
   - Optimized mobile marking interface

## Support & Maintenance

### Regular Maintenance Tasks
- Review marking history for anomalies
- Monitor marker activity logs
- Backup database regularly
- Update grade calculation formulas as needed

### Getting Help
For issues or questions about the marker panel, contact:
- Development team
- System administrators
- EMAS support desk

---

**Version**: 1.0  
**Last Updated**: November 3, 2025  
**Status**: ✅ Fully Functional  
**Author**: RSMS Development Team
