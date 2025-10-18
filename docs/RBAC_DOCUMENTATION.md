# Role-Based Access Control (RBAC) Documentation

## Overview

This system implements role-based access control (RBAC) to restrict system access based on user roles. There are two primary roles in the system:

1. **Admin** - Full system access with all permissions
2. **Petugas** - Limited operational access

## Roles and Permissions

### Admin Role
The Admin role has all permissions in the system, including:
- View, create, edit, and delete users
- Manage system settings
- Full access to all devices and alerts
- Generate and view all reports
- Access to all dashboard features

### Petugas Role
The Petugas role has limited permissions, including:
- View devices and their status
- View and resolve alerts
- View reports
- Manage their own profile

## Implementation Details

### Backend Implementation
The RBAC system is implemented using the Spatie Laravel Permission package:
- Permissions are defined in `database/seeders/RolePermissionSeeder.php`
- Roles are created and assigned permissions during seeding
- Authorization is enforced in controllers using Laravel's built-in authorization features

### Frontend Implementation
Access control is implemented in the frontend through:
- Conditional navigation links based on user permissions
- Role-based visibility of dashboard components
- API request authorization on the backend

## Checking User Permissions

### In Controllers
```php
// Check specific permission
$this->authorize('view users');

// Alternative method
if (Auth::user()->can('view users')) {
    // User can view users
}
```

### In Blade Templates
```blade
@can('view users')
    <!-- Content only visible to users with 'view users' permission -->
@endcan

@cannot('view users')
    <!-- Content only visible to users without 'view users' permission -->
@endcannot
```

### In JavaScript
```javascript
// Check if user has a specific permission (requires custom implementation)
if (user.permissions.includes('view users')) {
    // Show user management features
}
```

## Adding New Permissions

To add new permissions to the system:

1. Add the permission to the `$permissions` array in `RolePermissionSeeder.php`
2. Assign the permission to the appropriate roles in the seeder
3. Run the seeder: `php artisan db:seed --class=RolePermissionSeeder`
4. Use the permission in controllers and views as needed

## Best Practices

1. Always check permissions before allowing access to sensitive features
2. Use the most specific permission when possible (e.g., `edit users` instead of `manage users`)
3. Regularly review and audit user permissions
4. Follow the principle of least privilege - grant only the permissions users need
5. Test both positive and negative permission cases

## Common Permission Patterns

### CRUD Operations
- `view {resource}`
- `create {resource}`
- `edit {resource}`
- `delete {resource}`

### Specialized Permissions
- `resolve alerts`
- `generate reports`
- `view settings`

## Troubleshooting

### Permission Not Working
1. Verify the permission exists in the database
2. Check that the user has been assigned the correct role
3. Ensure the role has the required permission
4. Verify the authorization check is implemented correctly

### Adding New Roles
1. Add the role to `RolePermissionSeeder.php`
2. Define which permissions the role should have
3. Run the seeder to create the role and assign permissions