# 🎯 NetMonitor - App Summary

## Ringkasan Aplikasi

**NetMonitor** adalah platform monitoring konektivitas jaringan kampus STT Wastukancana yang dibangun dengan full-stack Python.

---

## 🏗️ Arsitektur & Teknologi

| Komponen | Teknologi |
|----------|-----------|
| **Backend API** | FastAPI (Python 3.10+) |
| **Frontend** | Streamlit |
| **Database** | MySQL / MariaDB |
| **ORM** | SQLAlchemy 2.0 |
| **Charts** | Plotly |
| **Authentication** | JWT (python-jose) |
| **Monitoring Script** | Python (ping3, requests) |

---

## 🧠 Konsep Sistem & Hierarki Jaringan

Struktur jaringan yang dimonitor bersifat **hierarkis**:

```
Provider Jaringan
   ↓
Router Utama  (IP: gateway)
   ↓
Router Sub    (terhubung ke Router Utama)
   ↓
Device        (Access Point, Switch, dll)
```

Jika **Router Utama** down → semua device di bawahnya otomatis ditandai *unreachable*.

---

## 🔄 Alur Kerja Sistem

### 1. Pendaftaran Perangkat (Admin)
- Input via Streamlit: nama, IP, tipe, lokasi, hierarki
- Data disimpan ke MySQL via FastAPI

### 2. Pemantauan Otomatis
- Script `monitor.py` dijalankan via Cron
- Fetch devices dari `GET /api/devices`
- Ping & port check setiap perangkat
- Update status via `POST /api/devices/status`

### 3. Penyimpanan Data
- FastAPI menyimpan ke `device_logs` (history)
- Update `devices.status` dan `last_checked_at`

### 4. Peringatan & Visualisasi
- Alert otomatis jika status berubah ke DOWN
- Dashboard menampilkan status real-time
- Grafik performa dengan Plotly

---

## 📊 Komponen Frontend (Streamlit)

### 1. Dashboard
- Statistics cards (Total, UP, DOWN, Alerts)
- Response time chart
- Device status over time
- Recent alerts table
- Device hierarchy tree

### 2. Devices Management
- Device list dengan filter (status, type, hierarchy)
- CRUD operations (Admin only)
- Hierarchical tree view

### 3. Alerts
- Alert list dengan severity filter
- Acknowledge & Resolve actions
- Bulk operations

### 4. Real-time Monitoring
- API health status
- Database connection status
- Performance metrics
- Auto-refresh (configurable)

---

## 🔗 API Endpoints

| Endpoint | Method | Fungsi |
|----------|--------|--------|
| `/api/auth/login` | POST | Login, dapatkan JWT |
| `/api/auth/user` | GET | Get current user |
| `/api/devices` | GET | List semua devices |
| `/api/devices` | POST | Create device |
| `/api/devices/{id}` | GET/PUT/DELETE | CRUD single device |
| `/api/devices/status` | POST | Update status (monitoring) |
| `/api/devices/{id}/logs` | GET | History logs |
| `/api/alerts` | GET | List alerts |
| `/api/alerts/{id}` | PATCH | Update alert status |
| `/api/dashboard/summary` | GET | Dashboard stats |
| `/api/dashboard/metrics` | GET | Performance metrics |

---

## 👥 Role dan Akses

| Fitur | Admin | Petugas |
|-------|-------|---------|
| Dashboard | ✅ | ✅ |
| Kelola Perangkat | CRUD | View-only |
| Lihat Alert | ✅ | ✅ |
| Resolve Alert | ✅ | ✅ |
| Monitoring Dashboard | ✅ | ✅ |

---

## 📦 Struktur Database

### Tables
1. **users** - Data pengguna (admin, petugas)
2. **devices** - Perangkat jaringan
3. **device_logs** - History monitoring
4. **alerts** - Notifikasi & peringatan

Detail: [Database Schema](database-schema.md)

---

**Versi**: 2.0 (Streamlit Edition)  
**Terakhir Diperbarui**: 11 Desember 2025