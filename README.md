# Sistem Monitoring Konektivitas Internet

Sistem monitoring jaringan berbasis web untuk memantau kesehatan dan performa perangkat jaringan secara real-time.

## 🚀 Fitur Utama

- **Pemantauan Perangkat Real-Time:** Memeriksa status konektivitas perangkat melalui ping dan pengecekan port secara berkala
- **Peringatan Otomatis:** Mengirimkan notifikasi di dalam aplikasi ketika sebuah perangkat terdeteksi down atau memiliki latensi tinggi
- **Visualisasi & Pelaporan Data:** Menampilkan grafik interaktif dan menghasilkan laporan performa dalam format PDF
- **Manajemen Perangkat:** CRUD untuk data perangkat jaringan dengan struktur hirarkis
- **Pencatatan Riwayat:** Menyimpan semua catatan status dan histori waktu respons setiap perangkat
- **Struktur Hirarkis:** Mengelola perangkat dalam tingkatan: Utama > Sub-Utama > Perangkat Terhubung

## 🛠️ Teknologi yang Digunakan

- **Backend:** Laravel 12 (PHP 8.2)
- **Frontend:** Tailwind CSS 4.0, Alpine.js
- **Database:** MySQL / MariaDB
- **Monitoring Script:** Python
- **Autentikasi:** Laravel Breeze + Spatie Laravel Permission

## 📁 Struktur Direktori

```
├── app/                    # Core Laravel application
│   ├── Console/           # Artisan commands
│   ├── Http/              # Controllers and middleware
│   ├── Models/            # Eloquent models
│   ├── Providers/          # Service providers
│   └── Services/           # Custom services
├── database/              # Migrations and seeders
├── public/                 # Public assets
├── resources/              # Views and assets
├── routes/                # Route definitions
├── scripts/                # Python monitoring scripts
└── tests/                  # Automated tests
```

## 🚀 Instalasi

### Persyaratan Sistem

- PHP 8.2 atau lebih tinggi
- Composer
- MySQL / MariaDB
- Node.js dan npm
- Python 3.6+ (untuk monitoring script)

### Langkah Instalasi

1. **Clone repository:**
   ```bash
   git clone https://github.com/your-repo/monitoring-konektivitas.git
   cd monitoring-konektivitas
   ```

2. **Instal dependensi PHP:**
   ```bash
   composer install
   ```

3. **Instal dependensi frontend:**
   ```bash
   npm install
   npm run build
   ```

4. **Konfigurasi database:**
   ```bash
   cp .env.example .env
   # Edit file .env sesuai konfigurasi database Anda
   php artisan key:generate
   ```

5. **Migrasi dan seeding database:**
   ```bash
   php artisan migrate --seed
   ```

6. **Jalankan server development:**
   ```bash
   php artisan serve
   ```

## 🔧 Konfigurasi

### Akun Default

Setelah instalasi selesai, sistem akan memiliki akun default:

- **Admin:**
  - Email: `admin@sttwastukancana.ac.id`
  - Password: `password`

- **Petugas:**
  - Email: `petugas@sttwastukancana.ac.id`
  - Password: `password`

### Menjalankan Monitoring

Ada dua cara untuk menjalankan monitoring:

#### 1. Menggunakan Command Line (Direkomendasikan)

```bash
php artisan monitor:devices
```

#### 2. Menggunakan Python Script

```bash
cd scripts
python3 monitor.py
```

### Konfigurasi Cron Job

Untuk monitoring otomatis setiap 5 menit, tambahkan ke crontab:

```bash
*/5 * * * * cd /path/to/your/project && php artisan monitor:devices >> /dev/null 2>&1
```

## 📊 API Endpoints

### Devices
- `GET /api/devices` - Mendapatkan semua perangkat aktif
- `GET /api/devices/{id}` - Mendapatkan detail perangkat tertentu
- `POST /api/devices/{id}/status` - Mencatat status perangkat dari script monitoring

### Authentication
Menggunakan Laravel Sanctum untuk API authentication.

## 🔒 Role dan Hak Akses

### Admin
- Akses penuh ke semua fitur
- Dapat mengelola perangkat, pengguna, dan pengaturan sistem

### Petugas
- Akses terbatas untuk operasional
- Dapat melihat status perangkat dan menandai peringatan

## 🎨 Desain UI/UX

Sistem mendukung mode gelap dan terang dengan:

- **Tailwind CSS:** Untuk styling yang responsif
- **Desain Modern:** Antarmuka yang bersih dan intuitif
- **Dark Mode Toggle:** Tombol untuk beralih antara mode terang/gelap
- **Responsive Design:** Berfungsi baik di desktop maupun mobile

## 📈 Laporan dan Visualisasi

- **Dashboard Real-time:** Statistik langsung tentang status perangkat
- **Grafik Interaktif:** Menggunakan Chart.js untuk visualisasi data
- **Laporan PDF:** Dapat menghasilkan laporan dalam format PDF dengan filter tanggal

## 🛡️ Keamanan

- **Role-Based Access Control:** Menggunakan Spatie Laravel Permission
- **Validasi Input:** Validasi data masuk untuk mencegah injection
- **CSRF Protection:** Perlindungan terhadap serangan CSRF
- **Rate Limiting:** Pembatasan permintaan untuk mencegah abuse

## 🧪 Testing

Untuk menjalankan test:

```bash
php artisan test
```

## 📄 Lisensi

MIT License - lihat file [LICENSE](LICENSE) untuk detail lebih lanjut.

## 🤝 Kontribusi

1. Fork repository
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buka Pull Request

## 📞 Dukungan

Untuk dukungan, buka issue di repository ini atau hubungi tim pengembang.