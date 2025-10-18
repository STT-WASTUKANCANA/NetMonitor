# 📖 User Management Guide

## Overview

The User Management system provides administrators with comprehensive tools to manage system users, roles, and permissions. This guide explains how to use all available features effectively.

## Access Requirements

Only users with the **Admin** role can access user management features. Other users (Petugas) will not see the "Users" link in the navigation menu.

## Available Actions

### 1. View Users
- **Access:** Click "Users" in the main navigation
- **Features:**
  - See a list of all system users
  - View user details (name, email, role, status)
  - Search and filter users
  - Sort by different columns

### 2. Create Users
- **Access:** Click "Add User" button on the user list page
- **Required Fields:**
  - Name
  - Email
  - Password (minimum 8 characters)
  - Password Confirmation
  - Role (Admin or Petugas)

### 3. View User Details
- **Access:** Click the eye icon next to any user in the list
- **Features:**
  - See complete user profile information
  - View role and permissions
  - See account creation and update dates

### 4. Edit Users
- **Access:** Click the pencil icon next to any user in the list
- **Editable Fields:**
  - Name
  - Email
  - Password (optional - leave blank to keep current password)
  - Role
  - Profile Photo

### 5. Delete Users
- **Access:** Click the trash can icon next to any user in the list
- **Protection:**
  - Confirmation modal prevents accidental deletions
  - Cannot delete your own account
  - Cannot delete the last Admin user

## User Roles

### Admin
- Full access to all system features
- Can manage users, devices, alerts, and reports
- Can configure system settings

### Petugas
- Limited operational access
- Can view devices and alerts
- Can resolve alerts
- Cannot manage users or system settings

## Security Features

### Password Requirements
- Minimum 8 characters
- Case-sensitive
- Can include special characters

### Profile Photos
- Supported formats: JPG, PNG, GIF, SVG
- Maximum size: 2MB
- Automatically resized for optimal performance

### Account Protection
- Email verification status tracking
- Cannot delete your own account
- Cannot delete the last Admin user
- Role-based access control enforcement

## Best Practices

### For Administrators
1. **Assign Appropriate Roles:**
   - Use Admin role only for system administrators
   - Use Petugas role for operational staff

2. **Regular Audits:**
   - Review user accounts periodically
   - Remove inactive accounts
   - Update roles as needed

3. **Strong Passwords:**
   - Encourage users to use strong, unique passwords
   - Change default passwords immediately

4. **Profile Photos:**
   - Use professional photos for identification
   - Keep file sizes small for performance

### For All Users
1. **Keep Information Updated:**
   - Update profile information when it changes
   - Use current email addresses

2. **Security Awareness:**
   - Never share account credentials
   - Report suspicious activity
   - Log out when finished

## Troubleshooting

### Common Issues

**Cannot Access User Management:**
- Verify you have the Admin role
- Check that you're logged in
- Contact system administrator if needed

**User Creation Fails:**
- Check that email is unique
- Verify password meets requirements
- Ensure all required fields are filled

**User Deletion Fails:**
- Cannot delete your own account
- Cannot delete the last Admin user
- Check for system error messages

**Profile Photo Issues:**
- Verify file format is supported
- Check file size is under 2MB
- Ensure image is not corrupted

## API Integration

The system provides a comprehensive REST API for programmatic user management:

### Endpoints
- `GET /api/users` - List all users
- `POST /api/users` - Create new user
- `GET /api/users/{id}` - Get user details
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user
- `POST /api/users/{id}/photo` - Update profile photo
- `DELETE /api/users/{id}/photo` - Remove profile photo

### Authentication
All API requests require:
- Valid authentication session
- CSRF token in headers

## JavaScript Integration

The system includes a JavaScript module for frontend integration:

```javascript
// Initialize user management
const userManagement = new UserManagement();

// Get all users
userManagement.getUsers()
  .then(users => console.log(users))
  .catch(error => console.error(error));

// Create user
userManagement.createUser({
  name: 'John Doe',
  email: 'john@example.com',
  password: 'securepassword',
  password_confirmation: 'securepassword',
  role: 'Petugas'
})
.then(result => console.log(result))
.catch(error => console.error(error));
```

## Support

For issues not covered in this guide:
1. Check system logs for error messages
2. Verify user permissions and roles
3. Contact system administrator for assistance
4. Refer to technical documentation for API details

## Screenshots

### User List Page
![User List](/images/screenshots/user-list.png)
*The main user management page showing all system users with actions.*

### User Details Page
![User Details](/images/screenshots/user-details.png)
*User details page with comprehensive information about the selected user.*

### Edit User Page
![Edit User](/images/screenshots/edit-user.png)
*User edit form with validation and profile photo management.*

### Delete Confirmation
![Delete Confirmation](/images/screenshots/delete-confirmation.png)
*Delete confirmation modal to prevent accidental data loss.*