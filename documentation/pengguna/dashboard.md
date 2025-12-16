# 📊 Panduan Dashboard - NetMonitor

Panduan penggunaan dashboard **NetMonitor** dengan Streamlit.

---

## 🔑 Login

1. Buka browser dan akses: **http://localhost:8501**
2. Masukkan email dan password
3. Klik **Login**

```
Email: admin@netmonitor.local
Password: [your password]
```

---

## 🏠 Halaman Dashboard

### Overview

Dashboard menampilkan ringkasan status jaringan secara real-time.

### Komponen

#### 1. Statistics Cards
| Card | Deskripsi |
|------|-----------|
| **Total Devices** | Jumlah total perangkat |
| **Devices UP** | Perangkat yang online |
| **Devices DOWN** | Perangkat yang offline |
| **Active Alerts** | Jumlah alert aktif |

#### 2. Response Time Chart
- Grafik garis waktu respons 7 hari terakhir
- Satuan: milliseconds (ms)
- Hover untuk detail

#### 3. Device Status Chart
- Area chart status UP vs DOWN
- Menampilkan tren performa

#### 4. Recent Alerts
- Daftar 5 alert terbaru
- Warna berdasarkan severity:
  - 🔴 Critical
  - 🟠 High
  - 🟡 Medium
  - 🟢 Low

#### 5. Device Hierarchy
- Tree view struktur perangkat
- Menampilkan parent-child relationship

### Auto-Refresh
- Dashboard refresh otomatis setiap **5 detik**
- Data selalu up-to-date

---

## 📡 Halaman Devices

### Device List
- Tabel semua perangkat
- Kolom: Status, Name, IP, Type, Hierarchy, Location

### Filter
| Filter | Options |
|--------|---------|
| Status | All, UP, DOWN, Unknown |
| Type | Router, Switch, Access Point, Server, Firewall, Other |
| Hierarchy | Utama, Sub, Device |

### Actions (Admin Only)
- ✏️ **Edit** - Ubah data perangkat
- 🗑️ **Delete** - Hapus perangkat (cascade ke children)

### Add Device (Admin Only)
1. Klik tab **Add Device**
2. Isi form:
   - Name: Nama perangkat
   - IP Address: Alamat IP
   - Type: Jenis perangkat
   - Hierarchy: Level hierarki
   - Parent: Perangkat parent (opsional)
   - Location: Lokasi (opsional)
3. Klik **Create Device**

---

## 🔔 Halaman Alerts

### Alert List
- Daftar semua alert dengan severity badges
- Filter by status atau severity

### Alert Actions

| Action | Deskripsi |
|--------|-----------|
| 👁️ **Acknowledge** | Tandai alert sudah dilihat |
| ✅ **Resolve** | Tandai alert selesai |

### Bulk Actions
- **Acknowledge All**: Acknowledge semua alert active
- **Resolve All**: Resolve semua alert active

---

## 📈 Halaman Monitoring

### System Health
- **API Status**: Status FastAPI backend
- **Database**: Status koneksi MySQL
- **Overall Health**: Status sistem keseluruhan

### Performance Metrics
- Response time 24h chart
- Uptime gauge (target: >95%)
- Detailed statistics

### Settings
- Auto Refresh: On/Off
- Refresh Interval: 5-30 detik

---

## 🔄 Auto-Refresh

| Halaman | Interval |
|---------|----------|
| Dashboard | 5 detik |
| Alerts | 10 detik |
| Monitoring | Configurable (5-30s) |

---

## 👤 Role dan Akses

### Admin
- ✅ Full access semua fitur
- ✅ CRUD devices
- ✅ Resolve alerts

### Petugas
- ✅ View dashboard
- ✅ View devices (read-only)
- ✅ View & resolve alerts
- ❌ Tidak bisa create/edit/delete devices

---

## 💡 Tips Penggunaan

1. **Perhatikan Critical Alerts** - Prioritaskan perangkat dengan alert critical
2. **Check Device Down** - Jika router utama down, semua child device akan unreachable
3. **Monitor Uptime** - Target uptime >95%, jika kurang, investigasi penyebab
4. **Response Time** - Normal <50ms, perhatikan jika >100ms

---

**Versi**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
