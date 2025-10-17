# 💻 Dokumentasi Pengembang

Dokumentasi teknis untuk pengembang yang bekerja pada Sistem Monitoring Jaringan.

## 🏗️ Arsitektur Sistem

### Stack Teknologi
- **Backend:** Laravel 12 (PHP 8.2)
- **Frontend:** Template Blade dengan Tailwind CSS 4.0
- **Database:** MySQL/MariaDB
- **Monitoring:** Skrip Python 3.6+
- **Autentikasi:** Laravel Breeze + Spatie Laravel Permission
- **Pembuatan PDF:** DomPDF
- **Sistem Antrian:** Redis (disarankan) atau Database

### Arsitektur Tingkat Tinggi
```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Web Browser   │◄──►│  Laravel (PHP)   │◄──►│    Database     │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │                       ▲
                              │ API Calls             │
                              ▼                       │
                       ┌──────────────────┐           │
                       │ Python Monitoring│           │
                       │      Script      │◄──────────┘
                       └──────────────────┘
```

## 📁 Struktur Proyek

```
app/
├── Console/Commands/        # Perintah Artisan
├── Http/
│   ├── Controllers/         # Controller MVC
│   │   └── Api/            # Controller API
│   └── Resources/          # Resource API
├── Models/                  # Model Eloquent
├── Services/               # Layanan logika bisnis
└── Providers/              # Provider layanan

database/
├── migrations/             # Perubahan skema database
└── seeders/                # Generator data sampel

resources/
├── views/                  # Template Blade
├── css/                    # CSS terkompilasi
└── js/                     # File JavaScript

routes/
├── web.php                 # Rute web
├── api.php                 # Rute API
└── console.php             # Rute konsol

scripts/
└── monitor.py             # Skrip monitoring Python
```

## 🧩 Komponen Inti

### Model

#### Model Perangkat
```php
// app/Models/Device.php
class Device extends Model
{
    protected $fillable = [
        'name', 'ip_address', 'type', 'hierarchy_level',
        'parent_id', 'location', 'description',
        'status', 'last_checked_at', 'is_active'
    ];
    
    // Relasi
    public function parent() { /* BelongsTo */ }
    public function children() { /* HasMany */ }
    public function logs() { /* HasMany */ }
    public function alerts() { /* HasMany */ }
}
```

#### Model DeviceLog
```php
// app/Models/DeviceLog.php
class DeviceLog extends Model
{
    protected $fillable = [
        'device_id', 'response_time', 'status', 'checked_at'
    ];
    
    protected $casts = [
        'response_time' => 'float',
        'checked_at' => 'datetime'
    ];
    
    // Relasi
    public function device() { /* BelongsTo */ }
}
```

#### Model Peringatan
```php
// app/Models/Alert.php
class Alert extends Model
{
    protected $fillable = [
        'device_id', 'message', 'status', 'resolved_at'
    ];
    
    protected $casts = [
        'resolved_at' => 'datetime'
    ];
    
    // Relasi
    public function device() { /* BelongsTo */ }
}
```

### Layanan

#### Layanan DeviceMonitoringService
```php
// app/Services/DeviceMonitoringService.php
class DeviceMonitoringService
{
    public function checkDeviceStatus(Device $device) { /* Implementasi */ }
    public function checkAllDevices() { /* Implementasi */ }
    private function checkForAlert(Device $device, string $newStatus) { /* Implementasi */ }
}
```

### Controller

#### Controller Perangkat API
```php
// app/Http/Controllers/Api/DeviceController.php
class DeviceController extends Controller
{
    public function index() { /* Dapatkan semua perangkat */ }
    public function show($id) { /* Dapatkan perangkat spesifik */ }
    public function recordStatus(Request $request, $id) { /* Catat status perangkat */ }
}
```

## 🔄 Alir Data

### Proses Monitoring Perangkat
1. **Pemicu Penjadwal:** perintah `monitor:devices` dijalankan
2. **Inisialisasi Layanan:** DeviceMonitoringService diinstansiasi
3. **Pengambilan Perangkat:** Dapatkan semua perangkat aktif dari database
4. **Pemeriksaan Konektivitas:** Ping setiap perangkat
5. **Pencatatan Status:** Simpan hasil di DeviceLog
6. **Pembuatan Peringatan:** Buat peringatan untuk perubahan status
7. **Pembaruan Hirarki:** Terapkan status ke perangkat anak
8. **Notifikasi:** Kirim notifikasi sistem (fitur masa depan)

### Komunikasi API
1. **Skrip Python:** Membuat HTTP POST ke `/api/devices/{id}/status`
2. **Rute API:** Diteruskan ke DeviceController@recordStatus
3. **Validasi:** Data permintaan divalidasi
4. **Pembaruan Database:** Catatan Device dan DeviceLog diperbarui
5. **Pemrosesan Peringatan:** Periksa perubahan status dan buat peringatan
6. **Respons:** Respons JSON dengan sukses/kegagalan

## 🔐 Otentikasi & Otorisasi

### Peran
- **Admin:** Akses sistem penuh
- **Petugas:** Akses operasional terbatas

### Izin
Izin dikelola melalui Spatie Laravel Permission:
- `view devices`
- `create devices`
- `edit devices`
- `delete devices`
- `view alerts`
- `resolve alerts`
- `view reports`
- `generate reports`
- `view settings` (Admin saja)
- `edit settings` (Admin saja)

### Middleware
```php
// Contoh proteksi rute
Route::middleware(['auth', 'role:Admin'])->group(function () {
    // Rute hanya untuk Admin
});

Route::middleware(['auth', 'permission:view devices'])->group(function () {
    // Rute manajemen perangkat
});
```

## 🎨 Pengembangan Frontend

### Template Blade
Template menggunakan sintaks Blade Laravel dengan kelas Tailwind CSS:
```blade
{{-- resources/views/devices/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow rounded-lg">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
            {{ __('Devices') }}
        </h2>
        <!-- Daftar perangkat -->
    </div>
</div>
@endsection
```

### Dukungan Mode Gelap
Mode gelap diimplementasikan dengan varian gelap Tailwind:
```html
<!-- Tombol toggle -->
<button id="dark-mode-toggle">
    <svg class="dark:hidden">...</svg>
    <svg class="hidden dark:block">...</svg>
</button>

<!-- Elemen dengan mode gelap diaktifkan -->
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    Content
</div>
```

### Peningkatan JavaScript
JavaScript Vanilla dan Alpine.js untuk interaktivitas:
```javascript
// Pembaruan dashboard real-time
setInterval(() => {
    fetch('/dashboard/realtime')
        .then(response => response.json())
        .then(data => updateDashboard(data));
}, 30000);
```

## 🐍 Skrip Monitoring Python

### Arsitektur Skrip
```python
class NetworkMonitor:
    def __init__(self, api_base_url, api_token=None):
        self.api_base_url = api_base_url
        self.headers = {'Content-Type': 'application/json'}
    
    def get_devices(self):  # Ambil perangkat dari API
    def ping_device(self, ip_address):  # Pemeriksaan konektivitas ping
    def check_port(self, ip_address, port):  # Pemeriksaan ketersediaan port
    def check_device(self, device):  # Pemeriksaan perangkat gabungan
    def report_status(self, device_id, status, response_time, message):  # Laporkan ke API
    def run_monitoring_cycle(self):  # Eksekusi monitoring lengkap
```

### Dependensi Eksternal
```python
import requests      # Klien HTTP
import subprocess    # Eksekusi perintah sistem
import time          # Fungsi waktu
import json          # Pemrosesan JSON
```

## 📊 Skema Database

### Tabel Perangkat
```sql
CREATE TABLE devices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    type ENUM('router', 'switch', 'access_point', 'server', 'other') DEFAULT 'other',
    hierarchy_level ENUM('utama', 'sub', 'device') DEFAULT 'device',
    parent_id BIGINT UNSIGNED NULL,
    location VARCHAR(255) NULL,
    description TEXT NULL,
    status ENUM('up', 'down', 'unknown') DEFAULT 'unknown',
    last_checked_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES devices(id) ON DELETE SET NULL
);
```

### Tabel Log Perangkat
```sql
CREATE TABLE device_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id BIGINT UNSIGNED NOT NULL,
    response_time FLOAT NULL,
    status ENUM('up', 'down') NOT NULL,
    checked_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
);
```

### Tabel Peringatan
```sql
CREATE TABLE alerts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id BIGINT UNSIGNED NOT NULL,
    message TEXT NULL,
    status ENUM('active', 'resolved') DEFAULT 'active',
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
);
```

## 🧪 Pengujian

### Uji PHPUnit
```php
// tests/Feature/DeviceApiTest.php
class DeviceApiTest extends TestCase
{
    public function test_can_get_devices()
    {
        $response = $this->get('/api/devices');
        $response->assertStatus(200);
    }
    
    public function test_can_record_device_status()
    {
        $device = Device::factory()->create();
        $response = $this->post("/api/devices/{$device->id}/status", [
            'status' => 'up',
            'response_time' => 15.5
        ]);
        $response->assertStatus(201);
    }
}
```

### Uji Skrip Python
```python
# tests/test_monitor.py
import unittest
from unittest.mock import patch, MagicMock
from scripts.monitor import NetworkMonitor

class TestNetworkMonitor(unittest.TestCase):
    def setUp(self):
        self.monitor = NetworkMonitor("http://localhost:8000")
    
    @patch('requests.get')
    def test_get_devices_success(self, mock_get):
        mock_response = MagicMock()
        mock_response.status_code = 200
        mock_response.json.return_value = [{'id': 1, 'ip_address': '192.168.1.1'}]
        mock_get.return_value = mock_response
        
        devices = self.monitor.get_devices()
        self.assertEqual(len(devices), 1)
```

## 🚀 Deployment

### Persyaratan Server
- PHP 8.2+
- MySQL 5.7+ atau MariaDB 10.2+
- Apache 2.4+ atau Nginx
- Composer
- Node.js dan npm
- Python 3.6+ (untuk skrip monitoring)

### Konfigurasi Lingkungan
Buat file `.env`:
```env
APP_NAME="Monitoring Jaringan"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=monitoring
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### Langkah Deployment
1. Clone repositori
2. Instal dependensi PHP: `composer install --no-dev`
3. Instal dependensi Node: `npm install`
4. Bangun aset: `npm run build`
5. Generate kunci aplikasi: `php artisan key:generate`
6. Jalankan migrasi: `php artisan migrate`
7. Isi database: `php artisan db:seed`
8. Siapkan cron job untuk monitoring
9. Konfigurasikan server web (Apache/Nginx)

### Setup Cron Job
```bash
# Tambahkan ke crontab untuk monitoring setiap 5 menit
*/5 * * * * cd /path/to/project && php artisan monitor:devices >> /dev/null 2>&1
```

## 🔧 Pemeliharaan

### Optimasi Database
```bash
# Optimalkan tabel database
php artisan optimize

# Bersihkan cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Pemeliharaan database
php artisan migrate:status
php artisan migrate:fresh --seed
```

### Manajemen Log
```bash
# Putar log
logrotate /etc/logrotate.d/laravel-monitoring

# Monitor log
tail -f storage/logs/laravel.log
```

### Monitoring Kinerja
Metrik kunci untuk dipantau:
- Kinerja query database
- Waktu respons API
- Penggunaan memori
- Pemanfaatan ruang disk
- Penggunaan CPU selama siklus monitoring

## 🆕 Peningkatan Masa Depan

### Fitur yang Direncanakan
1. **Notifikasi Real-time:** Pembaruan langsung berbasis WebSocket
2. **Multi-tenant:** Dukungan untuk banyak organisasi
3. **Analitik Lanjutan:** Deteksi anomali berbasis machine learning
4. **Aplikasi Mobile:** Aplikasi mobile asli
5. **Integrasi SNMP:** Monitoring perangkat berbasis protokol
6. **Pelacakan SLA:** Pemantauan kepatuhan kesepakatan tingkat layanan
7. **Pusat Integrasi:** Konektor untuk sistem pihak ketiga

### Peningkatan API
1. **Pembuatan Versi:** Manajemen versi API
2. **Pembatasan Laju:** Kontrol throttling yang ditingkatkan
3. **Pagination:** Pagination terstandarisasi di seluruh endpoint
4. **Filtering:** Kemampuan filter query lanjutan
5. **Webhook:** Sistem notifikasi berbasis event

### Peningkatan Keamanan
1. **OAuth2:** Otentikasi pihak ketiga
2. **Logging Audit:** Pelacakan aktivitas sistem komprehensif
3. **Enkripsi Data:** Enkripsi pada data sensitif di database
4. **Kepatuhan:** Fitur kepatuhan GDPR/HIPAA

## 🤝 Berkontribusi

### Alur Kerja Pengembangan
1. Fork repositori
2. Buat branch fitur
3. Lakukan perubahan
4. Tulis/perbarui uji
5. Kirim pull request

### Standar Kode
Ikuti standar penulisan PSR-12:
```bash
# Pemformatan kode
./vendor/bin/phpcs --standard=PSR12 app/

# Perbaikan kode
./vendor/bin/phpcbf --standard=PSR12 app/
```

### Pedoman Pull Request
1. Sertakan deskripsi komprehensif
2. Referensikan isu terkait
3. Sertakan uji untuk fungsionalitas baru
4. Perbarui dokumentasi seperlunya
5. Ikuti semantic versioning

## 📞 Dukungan

Untuk pertanyaan atau isu pengembangan:
1. Periksa dokumentasi yang ada
2. Cari di pelacak isu
3. Buat isu baru dengan deskripsi terperinci
4. Sertakan langkah-langkah untuk mereproduksi
5. Sediakan informasi sistem/lingkungan