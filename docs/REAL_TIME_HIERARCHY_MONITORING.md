# Sistem Pemantauan Hirarki Perangkat Real-time

## Ikhtisar
Dokumen ini menjelaskan implementasi sistem pemantauan real-time sejati yang melacak tingkat hirarki perangkat (utama, sub, dan perangkat biasa) dengan pembaruan otomatis per-detik. Sistem ini menyediakan pembaruan status segera, deteksi status perangkat akurat (UP, DOWN, UNKNOWN), dan pengukuran latensi dinamis dengan opsi penyegaran otomatis dan manual.

## Features Implemented

### 1. API Endpoints
- **GET `/api/devices/hierarchy`** - Retrieves the complete device hierarchy
- **GET `/api/devices/hierarchy/realtime`** - Retrieves real-time hierarchy data with current status and response times
- **POST `/api/devices/bulk-ping`** - Manually trigger immediate refresh of all devices
- **GET `/api/metrics/realtime`** - Get real-time network metrics for dashboard charts

### 2. Real-time Updates
- True per-second updates for all device status and latency data
- WebSocket broadcasting for instant UI updates (via `PerSecondDeviceStatusUpdated` event)
- High-frequency monitoring jobs for rapid data processing
- Automatic detection of UP/DOWN/UNKNOWN device states

### 3. Status Detection
- **UP**: Device responds normally to ping
- **DOWN**: Device fails to respond to ping
- **UNKNOWN**: IP address is missing, invalid, or malformed
- Accurate status indicators with appropriate color coding

### 4. Hierarchy Visualization
- Tree-structured display of device relationships (main → sub → device)
- Real-time status indicators for each hierarchy level
- Response time tracking with visual indicators
- Dynamic latency values in milliseconds updated per second

### 5. Dashboard Components
- Real-time status bars for each hierarchy level (main, sub, device)
- Auto-updating response time charts with per-second refresh
- Device hierarchy tree visualization with live updates
- Summary statistics with live counters
- Manual refresh button ("Scan" icon) for immediate recheck

### 6. Manual Refresh Functionality
- Dedicated "Scan" button on dashboard triggers immediate device recheck
- Visual feedback during manual refresh process
- Bulk refresh capability for all devices simultaneously
- Real-time updates of results with WebSocket integration

## Implementasi Teknis

### Komponen Backend
1. **Pekerjaan Pemantauan Per-Detik** - Pekerjaan `MonitorDevicesPerSecond` memproses semua perangkat setiap detik
2. **Layanan Ping yang Ditingkatkan** - `PingService` yang diperbarui kini menangani status UNKNOWN untuk IP tidak valid
3. **Event Baru** - Event `PerSecondDeviceStatusUpdated` untuk pembaruan UI per-detik
4. **Pembaruan Database** - Tabel `device_logs` kini mendukung status 'unknown' (enum: up/down/unknown)
5. **Model Perangkat** - Diperbarui untuk menangani ketiga status dengan validasi yang tepat
6. **Kontroler API** - `DeviceController` yang ditingkatkan dengan endpoint ping massal dan penyegaran manual
7. **Perintah Konsol** - Perintah `PerSecondMonitor` untuk pemantauan berkelanjutan (opsional)

### Komponen Frontend
1. **Integrasi WebSocket** - Pembaruan real-time melalui Pusher untuk data per-detik
2. **Indikator UI yang Ditingkatkan** - Representasi visual yang tepat dari status UP/DOWN/UNKNOWN
3. **Tombol Penyegaran Manual** - Tombol "Scan" memicu penyegaran massal perangkat segera
4. **Indikator Pemuatan** - Umpan balik visual selama operasi penyegaran manual
5. **Sistem Notifikasi** - Pesan sukses/kesalahan untuk tindakan penyegaran manual
6. **Pembaruan Teroptimalkan** - Pembaruan DOM efisien untuk menangani perubahan per-detik

### Sistem Event
- `PerSecondDeviceStatusUpdated` - Menyebarkan perubahan status perangkat segera
- `RealTimeHierarchyUpdated` - Diperbarui untuk menangani perubahan hirarki per-detik
- `DeviceStatusUpdated` - Event yang ada tetap digunakan untuk pemantauan reguler

## Alur Data

### Pembaruan Otomatis (Per-Detik)
1. Pekerjaan `MonitorDevicesPerSecond` berjalan untuk memeriksa semua perangkat
2. `PingService` memverifikasi status perangkat (UP/DOWN/UNKNOWN)
3. Event `PerSecondDeviceStatusUpdated` menyiarkan perubahan secara instan
4. Frontend menerima pembaruan real-time dan memperbarui UI segera
5. Database mencatat semua perubahan untuk pelacakan historis

### Penyegaran Manual
1. Pengguna mengklik tombol "Scan" di dashboard
2. Frontend memanggil endpoint `/api/devices/bulk-ping`
3. Backend melakukan ping ke semua perangkat segera
4. Event `PerSecondDeviceStatusUpdated` menyiarkan hasil
5. UI Dashboard diperbarui secara real-time dengan umpan balik visual

## Manfaat
- **Pembaruan Real-time Sejati**: Pemantauan perangkat per-detik dengan pembaruan UI instan
- **Deteksi Status Akurat**: Sistem tiga status (UP/DOWN/UNKNOWN) dengan validasi IP yang tepat
- **Visibilitas Langsung**: Masalah jaringan terdeteksi dan ditampilkan dalam hitungan detik
- **Kontrol Manual**: Tombol "Scan" menyediakan kemampuan penyegaran sesuai permintaan
- **Pelacakan Historis**: Semua perubahan status disimpan untuk analisis dan pelaporan
- **Arsitektur Dapat Diskalakan**: Sistem berbasis antrian menangani pemantauan frekuensi tinggi

## Penggunaan
Sistem menyediakan dua mode operasi:

### Mode Otomatis
- Pemantauan perangkat berjalan terus-menerus pada interval per-detik
- Data status dan latensi diperbarui secara otomatis dalam real-time
- Dashboard mencerminkan perubahan segera tanpa intervensi manual

### Mode Manual
- Klik tombol "Scan" (ikon segar) di dashboard
- Memicu pemeriksaan ulang segera semua perangkat
- Umpan balik visual menunjukkan kemajuan dan status penyelesaian penyegaran
- Hasil diperbarui secara real-time saat perangkat diperiksa

## Konfigurasi
Sistem dapat dikonfigurasi untuk lingkungan berbeda:
- Produksi: Worker berbasis antrian menangani pemantauan frekuensi tinggi
- Pengembangan: Penjadwalan yang lebih sederhana dapat digunakan berdasarkan persyaratan kinerja

## Pertimbangan Kinerja
- Pemantauan frekuensi tinggi dapat mempengaruhi sumber daya sistem dengan jumlah perangkat besar
- Worker antrian harus dikonfigurasi secara tepat untuk beban perangkat yang diharapkan
- Kinerja database harus dipantau dengan operasi tulis yang sering