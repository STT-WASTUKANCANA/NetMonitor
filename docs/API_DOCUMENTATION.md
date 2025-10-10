# 📡 API Documentation

## Overview

The Monitoring System API provides endpoints for device monitoring, status reporting, and data retrieval. The API follows REST principles and returns JSON responses.

## Authentication

Most endpoints require authentication via Laravel session. For external integrations, API tokens can be used.

### Headers

```
Content-Type: application/json
Accept: application/json
```

## Base URL

```
http://localhost:8000/api
```

## Endpoints

### Devices

#### Get All Active Devices
```http
GET /devices
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Router Utama STT",
    "ip_address": "192.168.1.1",
    "type": "router",
    "hierarchy_level": "utama",
    "parent_id": null,
    "location": "Server Room Lt. 1",
    "description": "Router utama kampus STT Wastukancana",
    "status": "up",
    "last_checked_at": "2023-06-15 10:30:45",
    "is_active": true,
    "created_at": "2023-06-01 09:00:00",
    "updated_at": "2023-06-15 10:30:45"
  }
]
```

#### Get Device Details
```http
GET /devices/{id}
```

**Response:**
```json
{
  "id": 1,
  "name": "Router Utama STT",
  "ip_address": "192.168.1.1",
  "type": "router",
  "hierarchy_level": "utama",
  "parent_id": null,
  "location": "Server Room Lt. 1",
  "description": "Router utama kampus STT Wastukancana",
  "status": "up",
  "last_checked_at": "2023-06-15 10:30:45",
  "is_active": true,
  "created_at": "2023-06-01 09:00:00",
  "updated_at": "2023-06-15 10:30:45"
}
```

#### Record Device Status
```http
POST /devices/{id}/status
```

**Request Body:**
```json
{
  "status": "up",
  "response_time": 15.5
}
```

**Parameters:**
| Name | Type | Required | Description |
|------|------|----------|-------------|
| status | string | Yes | Device status: "up" or "down" |
| response_time | float | No | Response time in milliseconds |

**Response:**
```json
{
  "message": "Device status recorded successfully",
  "log_id": 123
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
    "status": [
      "The status field is required."
    ]
  }
}
```

### Not Found Error
```json
{
  "message": "No query results for model [App\\Models\\Device] 999"
}
```

## Python Script Integration

The Python monitoring script communicates with the API to report device statuses.

### Example Usage
```python
import requests

# Get devices to monitor
response = requests.get('http://localhost:8000/api/devices')
devices = response.json()

# Report status for each device
for device in devices:
    # Perform ping or other checks
    status = 'up' if ping_device(device['ip_address']) else 'down'
    
    # Report to API
    data = {
        'status': status,
        'response_time': response_time  # Optional
    }
    
    requests.post(
        f"http://localhost:8000/api/devices/{device['id']}/status",
        json=data
    )
```

## Rate Limiting

API endpoints are rate-limited to prevent abuse:

- 60 requests per minute per IP address
- Excessive requests will receive a 429 Too Many Requests response

## Webhooks (Future Feature)

Planned webhook support for real-time notifications:

- Device status changes
- Alert triggers
- System events

Webhook endpoints will need to be configured in the admin panel.