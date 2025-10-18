# 📡 User Management API Documentation

## Overview

The User Management API provides endpoints for managing system users with role-based access control. All endpoints require appropriate permissions and are protected by authentication.

## Authentication

All endpoints require authentication via Laravel session. For external integrations, API tokens can be used.

### Headers

```
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: <csrf_token>
```

## Base URL

```
http://localhost:8000/api/users
```

## Endpoints

### Users

#### Get All Users
```http
GET /api/users
```

**Query Parameters:**
| Name | Type | Required | Description |
|------|------|----------|-------------|
| search | string | No | Search term for name or email |
| role | string | No | Filter by role name |
| per_page | integer | No | Number of items per page (default: 15) |
| page | integer | No | Page number |

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Admin User",
      "email": "admin@sttwastukancana.ac.id",
      "profile_photo_path": "profile-photos/photo.jpg",
      "email_verified_at": "2023-06-01T09:00:00.000000Z",
      "created_at": "2023-06-01T09:00:00.000000Z",
      "updated_at": "2023-06-15T10:30:45.000000Z",
      "roles": [
        {
          "id": 1,
          "name": "Admin",
          "guard_name": "web",
          "created_at": "2023-06-01T09:00:00.000000Z",
          "updated_at": "2023-06-01T09:00:00.000000Z"
        }
      ]
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 2
  }
}
```

#### Create User
```http
POST /api/users
```

**Form Data:**
| Name | Type | Required | Description |
|------|------|----------|-------------|
| name | string | Yes | User's full name |
| email | string | Yes | User's email address |
| password | string | Yes | User's password (min 8 characters) |
| password_confirmation | string | Yes | Password confirmation |
| role | string | Yes | User's role (Admin or Petugas) |
| profile_photo | file | No | Profile photo (max 2MB) |

**Response:**
```json
{
  "message": "User created successfully.",
  "user": {
    "id": 3,
    "name": "New User",
    "email": "newuser@sttwastukancana.ac.id",
    "profile_photo_path": null,
    "email_verified_at": null,
    "created_at": "2023-06-15T10:30:45.000000Z",
    "updated_at": "2023-06-15T10:30:45.000000Z",
    "roles": [
      {
        "id": 2,
        "name": "Petugas",
        "guard_name": "web",
        "created_at": "2023-06-01T09:00:00.000000Z",
        "updated_at": "2023-06-01T09:00:00.000000Z"
      }
    ]
  }
}
```

#### Get User Details
```http
GET /api/users/{id}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@sttwastukancana.ac.id",
    "profile_photo_path": "profile-photos/photo.jpg",
    "email_verified_at": "2023-06-01T09:00:00.000000Z",
    "created_at": "2023-06-01T09:00:00.000000Z",
    "updated_at": "2023-06-15T10:30:45.000000Z",
    "roles": [
      {
        "id": 1,
        "name": "Admin",
        "guard_name": "web",
        "created_at": "2023-06-01T09:00:00.000000Z",
        "updated_at": "2023-06-01T09:00:00.000000Z"
      }
    ]
  }
}
```

#### Update User
```http
PUT /api/users/{id}
```

**Form Data:**
| Name | Type | Required | Description |
|------|------|----------|-------------|
| name | string | No | User's full name |
| email | string | No | User's email address |
| password | string | No | User's password (min 8 characters) |
| password_confirmation | string | No | Password confirmation |
| role | string | No | User's role (Admin or Petugas) |
| profile_photo | file | No | Profile photo (max 2MB) |

**Response:**
```json
{
  "message": "User updated successfully.",
  "user": {
    "id": 1,
    "name": "Updated Admin User",
    "email": "admin@sttwastukancana.ac.id",
    "profile_photo_path": "profile-photos/photo.jpg",
    "email_verified_at": "2023-06-01T09:00:00.000000Z",
    "created_at": "2023-06-01T09:00:00.000000Z",
    "updated_at": "2023-06-15T11:30:45.000000Z",
    "roles": [
      {
        "id": 1,
        "name": "Admin",
        "guard_name": "web",
        "created_at": "2023-06-01T09:00:00.000000Z",
        "updated_at": "2023-06-01T09:00:00.000000Z"
      }
    ]
  }
}
```

#### Delete User
```http
DELETE /api/users/{id}
```

**Response:**
```json
{
  "message": "User deleted successfully."
}
```

#### Update User Profile Photo
```http
POST /api/users/{id}/photo
```

**Form Data:**
| Name | Type | Required | Description |
|------|------|----------|-------------|
| profile_photo | file | Yes | Profile photo (max 2MB) |
| _method | string | Yes | Set to "PUT" for method override |

**Response:**
```json
{
  "message": "Profile photo updated successfully",
  "profile_photo_url": "http://localhost:8000/storage/profile-photos/photo.jpg",
  "profile_photo_path": "profile-photos/photo.jpg"
}
```

#### Remove User Profile Photo
```http
DELETE /api/users/{id}/photo
```

**Response:**
```json
{
  "message": "Profile photo removed successfully",
  "profile_photo_url": "http://localhost:8000/images/default-profile.png"
}
```

## Response Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Unprocessable Entity |
| 500 | Internal Server Error |

## Error Responses

### Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

### Authorization Error
```json
{
  "message": "This action is unauthorized."
}
```

### Not Found Error
```json
{
  "message": "No query results for model [App\\Models\\User] 999"
}
```

## JavaScript Integration

The system includes a JavaScript module for enhanced user management:

```javascript
// Initialize user management
const userManagement = new UserManagement();

// Get all users with filters
userManagement.getUsers({ search: 'admin', role: 'Admin' })
  .then(response => {
    console.log('Users:', response.data);
    console.log('Pagination:', response.pagination);
  })
  .catch(error => console.error(error));

// Create a new user
userManagement.createUser({
  name: 'John Doe',
  email: 'john@sttwastukancana.ac.id',
  password: 'password123',
  password_confirmation: 'password123',
  role: 'Petugas'
})
  .then(response => console.log('User created:', response.user))
  .catch(error => console.error(error));

// Update an existing user
userManagement.updateUser(1, {
  name: 'Updated Name',
  email: 'updated@sttwastukancana.ac.id',
  role: 'Admin'
})
  .then(response => console.log('User updated:', response.user))
  .catch(error => console.error(error));

// Delete a user
userManagement.deleteUser(1)
  .then(response => console.log('User deleted:', response.message))
  .catch(error => console.error(error));

// Update profile photo
const photoFile = document.getElementById('profile_photo').files[0];
userManagement.updateProfilePhoto(1, photoFile)
  .then(response => console.log('Photo updated:', response.message))
  .catch(error => console.error(error));

// Remove profile photo
userManagement.removeProfilePhoto(1)
  .then(response => console.log('Photo removed:', response.message))
  .catch(error => console.error(error));
```

## Security Considerations

1. **Role-Based Access Control**: All endpoints enforce appropriate permissions
2. **CSRF Protection**: All requests require valid CSRF tokens
3. **Rate Limiting**: API endpoints are rate-limited to prevent abuse
4. **Input Validation**: All input is validated and sanitized
5. **Password Security**: Passwords are securely hashed using bcrypt
6. **File Upload Restrictions**: Profile photos have strict size and format limits

## Best Practices

1. **Always validate user input on both client and server side**
2. **Use HTTPS in production environments**
3. **Implement proper error handling**
4. **Follow RESTful principles**
5. **Use appropriate HTTP status codes**
6. **Implement proper pagination for large datasets**
7. **Handle file uploads securely**
8. **Validate file types and sizes**

## Testing

To test the API endpoints:

```bash
# Get all users
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"

# Create a new user
curl -X POST http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json" \
  -F "name=John Doe" \
  -F "email=john@sttwastukancana.ac.id" \
  -F "password=password123" \
  -F "password_confirmation=password123" \
  -F "role=Petugas"

# Update a user
curl -X PUT http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json" \
  -F "name=Updated Name" \
  -F "email=updated@sttwastukancana.ac.id" \
  -F "role=Admin"

# Delete a user
curl -X DELETE http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"
```