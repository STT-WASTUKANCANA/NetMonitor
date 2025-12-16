# 📡 API Documentation - NetMonitor

Dokumentasi REST API untuk **NetMonitor** dengan FastAPI backend.

---

## 🔗 Base URL

| Environment | URL |
|-------------|-----|
| Development | `http://localhost:8001` |
| Production | `https://api.netmonitor.local` |

## 📖 Interactive Docs

- **Swagger UI**: http://localhost:8001/docs
- **ReDoc**: http://localhost:8001/redoc

---

## 🔐 Authentication

NetMonitor menggunakan **JWT Bearer Token** untuk autentikasi.

### Header Format
```
Authorization: Bearer <token>
```

### Get Token
```bash
curl -X POST "http://localhost:8001/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@netmonitor.local", "password": "password"}'
```

### Response
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "first_name": "Admin",
      "last_name": "System",
      "email": "admin@netmonitor.local",
      "role": "admin"
    }
  }
}
```

---

## 📋 API Endpoints

### Authentication

#### POST /api/auth/login
Login dan dapatkan JWT token.

**Request Body**
```json
{
  "email": "admin@netmonitor.local",
  "password": "password123"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "token_type": "Bearer",
    "user": { ... }
  }
}
```

---

#### POST /api/auth/logout
Logout user. (Client-side token invalidation)

**Headers**: `Authorization: Bearer <token>`

**Response (200)**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

#### GET /api/auth/user
Get current authenticated user.

**Headers**: `Authorization: Bearer <token>`

**Response (200)**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "first_name": "Admin",
    "last_name": "System",
    "email": "admin@netmonitor.local",
    "role": "admin"
  }
}
```

---

### Devices

#### GET /api/devices
List semua devices dengan optional filtering dan pagination.

**Headers**: `Authorization: Bearer <token>`

**Query Parameters**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter: up, down, unknown |
| type | string | Filter: router, switch, access_point, server, firewall, other |
| hierarchy_level | string | Filter: utama, sub, device |
| per_page | integer | Items per page (default: 15, max: 100) |
| page | integer | Page number (default: 1) |

**Response (200)**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "per_page": 15,
    "total": 50,
    "data": [
      {
        "id": 1,
        "name": "Router Utama",
        "ip_address": "192.168.1.1",
        "type": "router",
        "hierarchy_level": "utama",
        "status": "up",
        "parent": null,
        "children": [...]
      }
    ]
  }
}
```

---

#### GET /api/devices/{id}
Get single device dengan details.

**Response (200)**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Router Utama",
    "ip_address": "192.168.1.1",
    "type": "router",
    "hierarchy_level": "utama",
    "status": "up",
    "location": "Ruang Server",
    "description": "Main gateway router",
    "last_checked_at": "2025-12-11T10:30:00",
    "recent_logs": [...],
    "active_alerts": [...]
  }
}
```

---

#### POST /api/devices
Create new device. **Admin only.**

**Headers**: `Authorization: Bearer <token>`

**Request Body**
```json
{
  "name": "Switch Lantai 1",
  "ip_address": "192.168.1.10",
  "type": "switch",
  "hierarchy_level": "sub",
  "parent_id": 1,
  "location": "Lantai 1 Gedung A",
  "description": "Switch utama lantai 1",
  "port": 22
}
```

**Response (201)**
```json
{
  "success": true,
  "message": "Device created successfully",
  "data": { ... }
}
```

---

#### PUT /api/devices/{id}
Update device. **Admin only.**

**Request Body** (partial update allowed)
```json
{
  "name": "Switch Lt.1 - Updated",
  "location": "Lantai 1 Gedung B"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Device updated successfully",
  "data": { ... }
}
```

---

#### DELETE /api/devices/{id}
Delete device dan cascade ke children. **Admin only.**

**Response (200)**
```json
{
  "success": true,
  "message": "Device deleted successfully"
}
```

---

#### POST /api/devices/status
Update device status dari monitoring script.

**Request Body**
```json
{
  "device_id": 1,
  "status": "up",
  "response_time": 15.5,
  "packet_loss": 0.0,
  "checked_at": "2025-12-11T10:30:00"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Device status updated successfully",
  "data": {
    "device_id": 1,
    "old_status": "unknown",
    "new_status": "up",
    "alert_created": false,
    "alert": null
  }
}
```

---

#### GET /api/devices/{id}/logs
Get device logs dengan statistics.

**Query Parameters**
| Parameter | Type | Description |
|-----------|------|-------------|
| from | string | Filter from date (ISO format) |
| to | string | Filter to date (ISO format) |
| status | string | Filter: up, down |
| per_page | integer | Items per page (default: 50) |
| page | integer | Page number |

**Response (200)**
```json
{
  "success": true,
  "data": {
    "device": {
      "id": 1,
      "name": "Router Utama",
      "ip_address": "192.168.1.1"
    },
    "logs": {
      "current_page": 1,
      "per_page": 50,
      "total": 100,
      "data": [...]
    },
    "statistics": {
      "total_checks": 100,
      "up_count": 95,
      "down_count": 5,
      "uptime_percentage": 95.0,
      "avg_response_time": 15.5,
      "min_response_time": 5.0,
      "max_response_time": 50.0
    }
  }
}
```

---

### Alerts

#### GET /api/alerts
List alerts dengan filtering dan pagination.

**Query Parameters**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter: active, acknowledged, resolved |
| severity | string | Filter: low, medium, high, critical |
| device_id | integer | Filter by device |
| per_page | integer | Items per page (default: 20) |
| page | integer | Page number |

**Response (200)**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "per_page": 20,
    "total": 10,
    "data": [
      {
        "id": 1,
        "device_id": 5,
        "message": "Switch Lt.2 tidak merespon (down)",
        "severity": "high",
        "status": "active",
        "created_at": "2025-12-11T10:30:00",
        "device": {
          "id": 5,
          "name": "Switch Lt.2",
          "ip_address": "192.168.1.15"
        }
      }
    ]
  }
}
```

---

#### PATCH /api/alerts/{id}
Update alert status.

**Request Body**
```json
{
  "status": "resolved"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "Alert status updated successfully",
  "data": {
    "id": 1,
    "status": "resolved",
    "resolved_at": "2025-12-11T11:00:00",
    "resolved_by": {
      "id": 1,
      "first_name": "Admin",
      "last_name": "System"
    }
  }
}
```

---

#### POST /api/alerts/bulk-update
Bulk update multiple alerts.

**Request Body**
```json
{
  "alert_ids": [1, 2, 3],
  "status": "acknowledged"
}
```

**Response (200)**
```json
{
  "success": true,
  "message": "3 alerts updated successfully",
  "data": {
    "updated_count": 3,
    "alert_ids": [1, 2, 3]
  }
}
```

---

### Dashboard

#### GET /api/dashboard/summary
Get dashboard summary statistics.

**Response (200)**
```json
{
  "success": true,
  "data": {
    "total_devices": 50,
    "devices_up": 45,
    "devices_down": 3,
    "devices_unknown": 2,
    "active_alerts": 5,
    "critical_alerts": 1,
    "high_alerts": 2,
    "avg_response_time_7days": 18.5,
    "uptime_percentage": 96.5,
    "last_updated": "2025-12-11T10:30:00"
  }
}
```

---

#### GET /api/dashboard/metrics
Get network metrics dengan chart data.

**Query Parameters**
| Parameter | Type | Description |
|-----------|------|-------------|
| period | string | Time period: 24h, 7d, 30d, 90d |
| device_id | integer | Specific device (optional) |

**Response (200)**
```json
{
  "success": true,
  "data": {
    "period": "7d",
    "device_id": null,
    "metrics": {
      "uptime_percentage": 96.5,
      "total_checks": 1000,
      "up_count": 965,
      "down_count": 35,
      "avg_response_time": 18.5,
      "min_response_time": 5.0,
      "max_response_time": 150.0
    },
    "chart_data": {
      "labels": ["2025-12-05", "2025-12-06", ...],
      "response_time": [15.5, 18.2, ...],
      "up_count": [140, 138, ...],
      "down_count": [4, 6, ...]
    }
  }
}
```

---

#### GET /api/dashboard/recent-alerts
Get recent active alerts.

**Query Parameters**
| Parameter | Type | Default |
|-----------|------|---------|
| limit | integer | 10 |

---

#### GET /api/dashboard/device-hierarchy
Get device hierarchy tree.

**Response (200)**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Router Utama",
      "ip_address": "192.168.1.1",
      "type": "router",
      "status": "up",
      "children": [
        {
          "id": 2,
          "name": "Switch Lt.1",
          "status": "up",
          "children": [...]
        }
      ]
    }
  ]
}
```

---

### Health Check

#### GET /health
System health check.

**Response (200)**
```json
{
  "success": true,
  "status": "healthy",
  "components": {
    "api": "healthy",
    "database": "healthy"
  }
}
```

---

## ❌ Error Responses

### 401 Unauthorized
```json
{
  "detail": "Could not validate credentials"
}
```

### 403 Forbidden
```json
{
  "detail": "Admin access required"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Device not found"
}
```

### 422 Validation Error
```json
{
  "detail": [
    {
      "loc": ["body", "ip_address"],
      "msg": "IP address already exists",
      "type": "value_error"
    }
  ]
}
```

---

## 🔧 Example: Python Client

```python
import requests

BASE_URL = "http://localhost:8001"

# Login
response = requests.post(f"{BASE_URL}/api/auth/login", json={
    "email": "admin@netmonitor.local",
    "password": "password"
})
token = response.json()["data"]["token"]

# Get devices
headers = {"Authorization": f"Bearer {token}"}
devices = requests.get(f"{BASE_URL}/api/devices", headers=headers)
print(devices.json())
```

---

**Versi API**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
