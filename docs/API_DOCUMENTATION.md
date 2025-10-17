# 📡 Dokumentasi API

## Ikhtisar

API Sistem Monitoring menyediakan endpoint untuk pemantauan perangkat, pelaporan status, dan pengambilan data. API mengikuti prinsip REST dan mengembalikan respons JSON.

## Autentikasi

Sebagian besar endpoint memerlukan autentikasi melalui sesi Laravel. Untuk integrasi eksternal, token API dapat digunakan.

### Header

```
Content-Type: application/json
Accept: application/json
```

## URL Dasar

```
http://localhost:8000/api
```

## Endpoint

### Perangkat

#### Mendapatkan Semua Perangkat Aktif
```http
GET /devices
```

**Respons:**
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

#### Mendapatkan Detail Perangkat
```http
GET /devices/{id}
```

**Respons:**
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

#### Mencatat Status Perangkat
```http
POST /devices/{id}/status
```

**Body Permintaan:**
```json
{
  "status": "up",
  "response_time": 15.5
}
```

**Parameter:**
| Nama | Tipe | Diperlukan | Deskripsi |
|------|------|------------|-----------|
| status | string | Ya | Status perangkat: "up" atau "down" |
| response_time | float | Tidak | Waktu respons dalam milidetik |

**Respons:**
```json
{
  "message": "Status perangkat berhasil dicatat",
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

## Pembatasan Laju

Endpoint API memiliki pembatasan laju untuk mencegah penyalahgunaan:

- 60 permintaan per menit per alamat IP
- Permintaan berlebihan akan menerima respons 429 Terlalu Banyak Permintaan

## Webhook (Fitur Masa Depan)

Dukungan webhook yang direncanakan untuk notifikasi real-time:

- Perubahan status perangkat
- Pemicu peringatan
- Event sistem

Endpoint webhook harus dikonfigurasi di panel admin.