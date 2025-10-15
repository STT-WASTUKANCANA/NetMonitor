# 🚀 Panduan Setup dan Penggunaan Sistem Monitoring Konektivitas

## 📋 Daftar Isi
1. [Persiapan Awal](#persiapan-awal)
2. [Instalasi Sistem](#instalasi-sistem)
3. [Konfigurasi Database](#konfigurasi-database)
4. [Setup Pengguna](#setup-pengguna)
5. [Menjalankan Aplikasi Web](#menjalankan-aplikasi-web)
6. [Setup Script Monitoring](#setup-script-monitoring)
7. [Penggunaan Sistem](#penggunaan-sistem)
8. [Fitur-Fitur Utama](#fitur-fitur-utama)
9. [Troubleshooting](#troubleshooting)

## 🛠️ Persiapan Awal

### Prasyarat Sistem
Sebelum memulai instalasi, pastikan komputer Anda memiliki komponen berikut:

- **Sistem Operasi**: Linux (Ubuntu 20.04+/22.04+), macOS, atau Windows
- **PHP**: Versi 8.2 atau lebih tinggi
- **Database**: MySQL 8.0+ atau MariaDB 10.6+
- **Web Server**: Apache 2.4+ atau Nginx
- **Composer**: Untuk mengelola dependensi PHP
- **Node.js**: Versi 16+ untuk frontend
- **npm**: Untuk mengelola paket Node.js
- **Python**: Versi 3.6+ untuk script monitoring

### Instalasi Prasyarat (Ubuntu/Debian)
```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install PHP dan ekstensi yang dibutuhkan
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl php8.2-soap

# Install database (MySQL)
sudo apt install -y mysql-server

# Install web server (Apache)
sudo apt install -y apache2

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js dan npm
sudo apt install -y nodejs npm

# Install Python dan pip
sudo apt install -y python3 python3-pip
```

## 📦 Instalasi Sistem

### 1. Clone Repository
```bash
# Masuk ke direktori web server (biasanya /var/www/html)
cd /var/www/html

# Clone repository project
git clone https://github.com/bondanbanuaji/monitoring-konektivitas.git

# Masuk ke direktori project
cd monitoring-konektivitas
```

### 2. Instal Dependensi PHP
```bash
# Instal semua dependensi PHP yang dibutuhkan
composer install
```

### 3. Instal Dependensi Node.js
```bash
# Instal dependensi Node.js
npm install

# Compile asset frontend
npm run build
```

### 4. Konfigurasi Environment
```bash
# Copy file konfigurasi contoh
cp .env.example .env

# Generate application key
php artisan key:generate
```

## 🗄️ Konfigurasi Database

### 1. Buat Database
```bash
# Masuk ke MySQL
mysql -u root -p

# Di dalam MySQL, buat database dan user
CREATE DATABASE monitoring_konektivitas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'monitor_user'@'localhost' IDENTIFIED BY 'password_kuat_anda';
GRANT ALL PRIVILEGES ON monitoring_konektivitas.* TO 'monitor_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Konfigurasi File .env
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=monitoring_konektivitas
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Jalankan Migrasi Database
```bash
# Jalankan migrasi untuk membuat tabel-tabel
php artisan migrate

# (Opsional) Jalankan seeder untuk data contoh
php artisan db:seed
```

## 👤 Setup Pengguna

### Akun Default
Setelah instalasi selesai, sistem akan memiliki akun default:

**Admin:**
- Email: `admin@sttwastukancana.ac.id`
- Password: `password`

**Petugas:**
- Email: `petugas@sttwastukancana.ac.id`
- Password: `password`

### Mengganti Password
Disarankan untuk segera mengganti password setelah login pertama kali.

## 🌐 Menjalankan Aplikasi Web

### Metode 1: Menggunakan Laravel Development Server
```bash
# Jalankan server development
php artisan serve

# Aplikasi akan tersedia di: http://localhost:8000
```

### Metode 2: Menggunakan Apache/Nginx
1. Konfigurasi virtual host untuk mengarah ke direktori `public/`
2. Restart web server
3. Akses melalui domain yang telah dikonfigurasi

## 🐍 Setup Script Monitoring

### 1. Instal Dependensi Python
```bash
# Masuk ke direktori script
cd /var/www/html/monitoring-konektivitas/scripts

# Instal dependensi Python yang dibutuhkan
pip3 install requests
```

### 2. Konfigurasi Script
Edit file `config.env`:
```env
# Konfigurasi API
API_BASE_URL="http://localhost:8000"
API_TOKEN=""

# Pengaturan monitoring
MONITOR_INTERVAL=300
PING_TIMEOUT=2
HTTP_TIMEOUT=5

# Logging
LOG_FILE="/var/log/network-monitor.log"
LOG_LEVEL="INFO"
```

### 3. Uji Script Monitoring
```bash
# Jalankan script untuk pengujian
python3 monitor.py --debug
```

### 4. Setup Cron Job untuk Monitoring Otomatis
```bash
# Edit crontab
crontab -e

# Tambahkan baris berikut untuk menjalankan monitoring setiap 5 menit
*/5 * * * * cd /var/www/html/monitoring-konektivitas/scripts && python3 monitor.py >> /var/log/network-monitor.log 2>&1
```

## 🖥️ Penggunaan Sistem

### 1. Login ke Sistem
1. Buka browser dan akses alamat aplikasi (misal: `http://localhost:8000`)
2. Klik tombol "Login" di pojok kanan atas
3. Masukkan kredensial:
   - Untuk Admin: `admin@sttwastukancana.ac.id` / `password`
   - Untuk Petugas: `petugas@sttwastukancana.ac.id` / `password`

### 2. Navigasi Menu
Setelah login, Anda akan melihat menu navigasi di bagian atas:

- **Dashboard**: Tampilan utama dengan statistik real-time
- **Devices**: Manajemen perangkat jaringan
- **Alerts**: Daftar peringatan dan notifikasi
- **Reports**: Laporan dan grafik performa
- **Profile**: Pengaturan akun pribadi

### 3. Menggunakan Dashboard
Dashboard menampilkan:
- **Statistik Utama**: Total perangkat, perangkat aktif, tidak aktif, dan peringatan
- **Grafik Performa**: Tren waktu respons perangkat
- **Peringatan Terbaru**: Daftar 5 peringatan terakhir
- **Status Perangkat Utama**: Tampilan hirarkis perangkat

### 4. Manajemen Perangkat
#### Menambah Perangkat Baru
1. Klik menu "Devices"
2. Klik tombol "Tambah Perangkat"
3. Isi formulir dengan data perangkat:
   - Nama perangkat
   - Alamat IP
   - Tipe perangkat (Router, Switch, Access Point, Server, dll)
   - Tingkat hirarki (Utama, Sub-Utama, Perangkat Terhubung)
   - Lokasi fisik (opsional)
   - Deskripsi (opsional)
4. Klik "Simpan"

#### Mengedit Perangkat
1. Di halaman Devices, klik ikon pensil pada perangkat yang ingin diedit
2. Ubah data sesuai kebutuhan
3. Klik "Update"

#### Menghapus Perangkat
1. Di halaman Devices, klik ikon tong sampah pada perangkat yang ingin dihapus
2. Konfirmasi penghapusan

### 5. Mengelola Peringatan
#### Melihat Peringatan
1. Klik menu "Alerts"
2. Lihat daftar peringatan dengan status (Aktif/Terselesaikan)

#### Menandai Peringatan sebagai Terselesaikan
1. Di halaman Alerts, klik tombol "Tandai Selesai" pada peringatan yang ingin ditutup
2. Peringatan akan berubah status menjadi "Terselesaikan"

### 6. Menggunakan Laporan
#### Melihat Laporan
1. Klik menu "Reports"
2. Pilih rentang waktu dan perangkat yang ingin dilaporkan
3. Klik "Generate Report" untuk melihat grafik

#### Mengekspor Laporan PDF
1. Setelah melihat laporan, klik tombol "Export to PDF"
2. File PDF akan diunduh secara otomatis

### 7. Mengatur Profil
1. Klik nama pengguna di pojok kanan atas
2. Pilih "Profile" dari dropdown
3. Ubah informasi pribadi atau password
4. Klik "Save Changes" atau "Update Password"

## 🎯 Fitur-Fitur Utama

### 1. **Monitoring Real-Time**
- Pemeriksaan status perangkat setiap 5 menit
- Deteksi otomatis perangkat yang down/up
- Pemberitahuan langsung melalui dashboard

### 2. **Struktur Hirarkis Perangkat**
- **Utama**: Perangkat inti jaringan (Router Utama)
- **Sub-Utama**: Perangkat distribusi (Switch distribusi)
- **Perangkat Terhubung**: Perangkat akhir (Access Point, Server, dll)

### 3. **Sistem Peringatan**
- Notifikasi otomatis saat status perangkat berubah
- Penandaan peringatan sebagai terselesaikan
- Riwayat peringatan lengkap

### 4. **Visualisasi Data**
- Grafik tren waktu respons
- Statistik performa harian/mingguan/bulanan
- Dashboard interaktif dengan auto-refresh

### 5. **Laporan Komprehensif**
- Laporan PDF yang dapat dicetak
- Filter berdasarkan rentang waktu dan perangkat
- Statistik performa detail

### 6. **Role-Based Access Control**
- **Admin**: Akses penuh ke semua fitur
- **Petugas**: Akses terbatas untuk operasional

## 🆘 Troubleshooting

### Masalah Umum dan Solusi

#### 1. **Tidak Bisa Login**
- **Masalah**: Kredensial tidak dikenali
- **Solusi**: 
  - Pastikan database telah dimigrasi dan ditanam (seeded)
  - Periksa kembali email dan password
  - Reset password melalui database jika perlu

#### 2. **Dashboard Tidak Menampilkan Data**
- **Masalah**: Statistik kosong atau tidak diperbarui
- **Solusi**:
  - Pastikan script monitoring berjalan
  - Periksa log monitoring: `tail -f /var/log/network-monitor.log`
  - Jalankan script monitoring secara manual untuk testing

#### 3. **Script Monitoring Gagal**
- **Masalah**: Error saat menjalankan `monitor.py`
- **Solusi**:
  - Pastikan dependensi Python terinstal: `pip3 install requests`
  - Periksa konfigurasi di `config.env`
  - Jalankan dengan mode debug: `python3 monitor.py --debug`

#### 4. **Peringatan Tidak Muncul**
- **Masalah**: Tidak ada notifikasi meskipun perangkat down
- **Solusi**:
  - Periksa apakah script monitoring berjalan dengan benar
  - Lihat log perangkat untuk memastikan status berubah
  - Periksa konfigurasi cron job

#### 5. **Grafik Tidak Muncul**
- **Masalah**: Chart di dashboard/report tidak menampilkan data
- **Solusi**:
  - Pastikan ada data di database
  - Periksa koneksi internet untuk Chart.js
  - Clear cache browser

#### 6. **Permission Denied**
- **Masalah**: Error akses ke fitur tertentu
- **Solusi**:
  - Periksa role pengguna
  - Pastikan pengguna memiliki permission yang sesuai
  - Cek konfigurasi permission di database

### Perintah Berguna untuk Debugging

```bash
# Clear semua cache Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Lihat log aplikasi
tail -f storage/logs/laravel.log

# Lihat log monitoring
tail -f /var/log/network-monitor.log

# Test koneksi database
php artisan tinker
>>> DB::connection()->getPdo();

# Jalankan migrasi ulang (HATI-HATI: ini akan menghapus data)
php artisan migrate:fresh --seed
```

## 🔧 Maintenance

### Backup Database
```bash
# Backup database
mysqldump -u monitor_user -p monitoring_konektivitas > backup_$(date +%Y%m%d).sql

# Restore database
mysql -u monitor_user -p monitoring_konektivitas < backup_file.sql
```

### Update Sistem
```bash
# Pull perubahan terbaru
git pull origin main

# Update dependensi
composer update
npm update

# Compile ulang asset
npm run build

# Jalankan migrasi jika ada perubahan skema
php artisan migrate
```

### Monitoring Script
```bash
# Cek status cron job
crontab -l

# Restart cron service
sudo systemctl restart cron

# Test script monitoring
cd scripts && python3 monitor.py --debug
```

## 📞 Dukungan

Jika mengalami masalah yang tidak dapat diselesaikan dengan panduan ini:

1. Periksa log aplikasi dan monitoring
2. Pastikan semua prasyarat terinstal dengan benar
3. Cek koneksi database dan API
4. Hubungi tim pengembang untuk bantuan lebih lanjut

---

**🎉 Selamat! Sistem Monitoring Konektivitas Internet Anda siap digunakan untuk memantau jaringan STT Wastukancana secara efektif dan efisien.**