# 🗄️ Database Schema - NetMonitor

Dokumentasi struktur database untuk **NetMonitor** versi Streamlit + FastAPI.

---

## 📊 Entity Relationship Diagram (ERD)

```
┌──────────────────────┐
│       users          │
├──────────────────────┤
│ PK  id              │
│     first_name      │
│     last_name       │
│     email ◄─────────┼──────────────────────────────┐
│     password        │                              │
│     role            │                              │
│     profile_photo   │                              │
│     created_at      │                              │
│     updated_at      │                              │
└──────────────────────┘                              │
         │                                           │
         │ created_by                                │ resolved_by
         ▼                                           │
┌──────────────────────┐                              │
│      devices         │                              │
├──────────────────────┤                              │
│ PK  id              │◄─────────────┐               │
│     name            │              │               │
│     ip_address      │              │               │
│     type            │              │ parent_id     │
│     hierarchy_level │              │               │
│ FK  parent_id  ─────┼──────────────┘               │
│ FK  created_by      │                              │
│     location        │                              │
│     description     │                              │
│     port            │                              │
│     status          │                              │
│     last_checked_at │                              │
│     created_at      │                              │
│     updated_at      │                              │
└──────────────────────┘                              │
         │                                           │
         │ device_id                                 │
         ▼                                           │
┌──────────────────────┐     ┌──────────────────────┐│
│    device_logs       │     │       alerts         ││
├──────────────────────┤     ├──────────────────────┤│
│ PK  id              │     │ PK  id              ││
│ FK  device_id       │     │ FK  device_id       ││
│     status          │     │     message         ││
│     response_time   │     │     severity        ││
│     packet_loss     │     │     status          ││
│     checked_at      │     │     resolved_at     ││
│     created_at      │     │ FK  resolved_by  ───┼┘
└──────────────────────┘     │     created_at      │
                             │     updated_at      │
                             └──────────────────────┘
```

---

## 📋 Table: `users`

Menyimpan data pengguna sistem.

### Columns

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `first_name` | VARCHAR(255) | NO | - | Nama depan |
| `last_name` | VARCHAR(255) | NO | - | Nama belakang |
| `email` | VARCHAR(255) | NO | - | Email (unique) |
| `email_verified_at` | TIMESTAMP | YES | NULL | Waktu verifikasi email |
| `password` | VARCHAR(255) | NO | - | Password hash (bcrypt) |
| `role` | ENUM | NO | 'petugas' | Role: 'admin' atau 'petugas' |
| `profile_photo` | VARCHAR(255) | YES | NULL | Path foto profil |
| `remember_token` | VARCHAR(100) | YES | NULL | Token remember me |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu diupdate |

### Indexes

| Name | Columns | Type |
|------|---------|------|
| PRIMARY | id | PRIMARY KEY |
| users_email_unique | email | UNIQUE |
| idx_users_role | role | INDEX |

### SQLAlchemy Model

```python
from app.models.user import User

user = User(
    first_name="Admin",
    last_name="NetMonitor",
    email="admin@netmonitor.local",
    password=User.hash_password("password"),
    role="admin"
)
```

---

## 📋 Table: `devices`

Menyimpan data perangkat jaringan.

### Columns

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `name` | VARCHAR(255) | NO | - | Nama perangkat |
| `ip_address` | VARCHAR(45) | NO | - | Alamat IP (unique) |
| `type` | ENUM | NO | - | Jenis perangkat |
| `hierarchy_level` | ENUM | NO | 'device' | Level hierarki |
| `parent_id` | BIGINT UNSIGNED | YES | NULL | FK ke devices (self-referencing) |
| `location` | VARCHAR(255) | YES | NULL | Lokasi fisik |
| `description` | TEXT | YES | NULL | Deskripsi |
| `port` | INT | YES | NULL | Port untuk monitoring |
| `status` | ENUM | NO | 'unknown' | Status: up/down/unknown |
| `last_checked_at` | TIMESTAMP | YES | NULL | Waktu terakhir dicek |
| `created_by` | BIGINT UNSIGNED | YES | NULL | FK ke users |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu diupdate |

### ENUM Values

#### `type`
- `router` - Router
- `switch` - Switch
- `access_point` - Access Point
- `server` - Server
- `firewall` - Firewall
- `other` - Lainnya

#### `hierarchy_level`
- `utama` - Perangkat utama (gateway)
- `sub` - Sub-perangkat (terhubung ke utama)
- `device` - End device (leaf)

#### `status`
- `up` - Online/aktif
- `down` - Offline/tidak aktif
- `unknown` - Status belum diketahui

### Indexes & Constraints

| Name | Type | Reference |
|------|------|-----------|
| PRIMARY | PRIMARY KEY | id |
| devices_ip_address_unique | UNIQUE | ip_address |
| idx_devices_status | INDEX | status |
| idx_devices_parent_id | INDEX | parent_id |
| idx_devices_hierarchy | INDEX | hierarchy_level |
| idx_devices_type | INDEX | type |
| fk_devices_parent | FOREIGN KEY | parent_id → devices(id) CASCADE |
| fk_devices_created_by | FOREIGN KEY | created_by → users(id) SET NULL |

---

## 📋 Table: `device_logs`

Menyimpan history monitoring perangkat.

### Columns

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `device_id` | BIGINT UNSIGNED | NO | - | FK ke devices |
| `status` | ENUM | NO | - | Status: 'up' atau 'down' |
| `response_time` | DECIMAL(8,2) | YES | NULL | Response time (ms) |
| `packet_loss` | DECIMAL(5,2) | YES | NULL | Packet loss (%) |
| `checked_at` | TIMESTAMP | NO | CURRENT_TIMESTAMP | Waktu pengecekan |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |

### Indexes & Constraints

| Name | Type | Reference |
|------|------|-----------|
| PRIMARY | PRIMARY KEY | id |
| idx_logs_device_checked | INDEX | device_id, checked_at |
| idx_logs_checked_at | INDEX | checked_at |
| fk_logs_device | FOREIGN KEY | device_id → devices(id) CASCADE |

---

## 📋 Table: `alerts`

Menyimpan notifikasi dan peringatan.

### Columns

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary Key |
| `device_id` | BIGINT UNSIGNED | NO | - | FK ke devices |
| `message` | TEXT | NO | - | Pesan alert |
| `severity` | ENUM | NO | 'medium' | Tingkat keparahan |
| `status` | ENUM | NO | 'active' | Status alert |
| `resolved_at` | TIMESTAMP | YES | NULL | Waktu diselesaikan |
| `resolved_by` | BIGINT UNSIGNED | YES | NULL | FK ke users |
| `created_at` | TIMESTAMP | YES | NULL | Waktu dibuat |
| `updated_at` | TIMESTAMP | YES | NULL | Waktu diupdate |

### ENUM Values

#### `severity`
- `low` 🟢 - Rendah
- `medium` 🟡 - Sedang
- `high` 🟠 - Tinggi
- `critical` 🔴 - Kritis

#### `status`
- `active` - Aktif (belum ditangani)
- `acknowledged` - Sudah dilihat
- `resolved` - Sudah diselesaikan

### Indexes & Constraints

| Name | Type | Reference |
|------|------|-----------|
| PRIMARY | PRIMARY KEY | id |
| idx_alerts_device_status | INDEX | device_id, status |
| idx_alerts_severity | INDEX | severity |
| idx_alerts_status | INDEX | status |
| fk_alerts_device | FOREIGN KEY | device_id → devices(id) CASCADE |
| fk_alerts_resolved_by | FOREIGN KEY | resolved_by → users(id) SET NULL |

---

## 🔄 Relationships Summary

| From | To | Type | On Delete |
|------|----|------|-----------|
| devices.parent_id | devices.id | Self-referencing | CASCADE |
| devices.created_by | users.id | Many-to-One | SET NULL |
| device_logs.device_id | devices.id | Many-to-One | CASCADE |
| alerts.device_id | devices.id | Many-to-One | CASCADE |
| alerts.resolved_by | users.id | Many-to-One | SET NULL |

---

## 📊 Sample Queries (SQLAlchemy)

### Get All Active Devices UP
```python
from app.models import Device
from app.database import get_db

db = next(get_db())
devices_up = db.query(Device).filter(Device.status == 'up').all()
```

### Get Device with Logs
```python
device = db.query(Device).filter(Device.id == 1).first()
logs = device.logs  # Lazy loaded relationship
```

### Get Active Alerts by Severity
```python
from app.models import Alert

critical_alerts = db.query(Alert).filter(
    Alert.status == 'active',
    Alert.severity == 'critical'
).all()
```

---

**Versi**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
