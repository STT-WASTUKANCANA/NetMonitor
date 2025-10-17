# 🔧 Panduan Penyelesaian Masalah

Masalah-masalah umum dan solusi untuk Sistem Monitoring Jaringan.

## 🚀 Masalah Instalasi

### Kegagalan Dependensi Composer
**Masalah:** `composer install` gagal karena konflik dependensi.

**Solusi:**
```bash
# Bersihkan cache composer
composer clear-cache

# Instal dengan mengabaikan persyaratan platform
composer install --ignore-platform-reqs

# Atau perbarui dependensi
composer update
```

### Kesalahan Build Node.js
**Masalah:** `npm run build` gagal dengan kesalahan webpack.

**Solusi:**
```bash
# Bersihkan node_modules dan instal ulang
rm -rf node_modules
npm install

# Bangun ulang dengan output verbose
npm run build --verbose

# Periksa kompatibilitas versi Node.js
node --version
npm --version
```

### Koneksi Database Gagal
**Masalah:** Laravel tidak dapat terhubung ke database selama instalasi.

**Solusi:**
1. Verifikasi kredensial database di file `.env`
2. Pastikan server database berjalan
3. Periksa pengaturan firewall
4. Uji koneksi secara manual:
   ```bash
   mysql -h localhost -u username -p
   ```

### Kesalahan Migrasi
**Masalah:** Migrasi database gagal selama instalasi.

**Solusi:**
```bash
# Periksa status migrasi saat ini
php artisan migrate:status

# Reset dan jalankan ulang migrasi
php artisan migrate:fresh

# Jalankan dengan output verbose
php artisan migrate --verbose
```

## 🔌 Masalah Konektivitas

### Perangkat Muncul Offline Saat Mereka Online
**Masalah:** Perangkat yang dipantau muncul down meskipun dapat diakses.

**Langkah Diagnosis:**
1. **Uji Ping Manual:**
   ```bash
   ping DEVICE_IP_ADDRESS
   ```

2. **Periksa Konfigurasi Perangkat:**
   - Verifikasi alamat IP di sistem cocok dengan IP perangkat sebenarnya
   - Pastikan perangkat mengizinkan balasan ICMP echo
   - Periksa apakah perangkat berada di belakang firewall

3. **Tinjau Log Pemantauan:**
   ```bash
   # Periksa log Laravel
   tail -f storage/logs/laravel.log
   
   # Periksa log script pemantauan
   tail -f logs/monitor.log
   ```

4. **Uji dari Server:**
   ```bash
   # SSH ke server yang menjalankan Laravel
   ping DEVICE_IP_ADDRESS
   ```

**Solusi:**
- Perbaiki alamat IP perangkat di sistem
- Konfigurasikan perangkat untuk mengizinkan permintaan ping
- Sesuaikan aturan firewall
- Modifikasi nilai timeout script pemantauan

### Script Python Tidak Bisa Berkomunikasi dengan API
**Masalah:** Script pemantauan gagal mengirimkan data ke API Laravel.

**Langkah Diagnosis:**
1. **Periksa Aksesibilitas Endpoint API:**
   ```bash
   curl -X GET http://your-domain.com/api/devices
   ```

2. **Verifikasi Variabel Lingkungan:**
   ```bash
   echo $API_BASE_URL
   echo $API_TOKEN
   ```

3. **Uji Panggilan API Langsung:**
   ```bash
   curl -X POST http://your-domain.com/api/devices/1/status \
        -H "Content-Type: application/json" \
        -d '{"status":"up","response_time":15.5}'
   ```

**Solusi:**
- Verifikasi variabel lingkungan `API_BASE_URL`
- Periksa otentikasi API (jika menggunakan token)
- Pastikan server web mengizinkan permintaan POST
- Verifikasi konfigurasi CORS

## 🖥️ Masalah Antarmuka Web

### Dashboard Tidak Memuat
**Masalah:** Halaman dashboard memuat kosong atau dengan kesalahan.

**Langkah Diagnosis:**
1. **Periksa Konsol Browser:**
   - Buka Developer Tools (F12)
   - Cari kesalahan JavaScript
   - Periksa permintaan jaringan untuk kegagalan

2. **Tinjau Log Laravel:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Bersihkan Cache Aplikasi:**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan config:clear
   ```

**Solusi:**
- Bersihkan cache browser dan refresh keras (Ctrl+F5)
- Periksa izin file pada direktori penyimpanan
- Pastikan semua aset berhasil dikompilasi (`npm run build`)
- Verifikasi konektivitas database

### Kegagalan Otentikasi
**Masalah:** Tidak dapat login atau sesi kadaluarsa secara tak terduga.

**Langkah Diagnosis:**
1. **Periksa Konfigurasi Sesi:**
   - Verifikasi `SESSION_DRIVER` di `.env`
   - Periksa izin penyimpanan sesi

2. **Tinjau Log Kesalahan:**
   ```bash
   # log Laravel
   tail -f storage/logs/laravel.log
   
   # log server web
   tail -f /var/log/apache2/error.log  # atau log error nginx
   ```

3. **Uji Koneksi Database:**
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

**Solusi:**
- Pastikan direktori `storage` dapat ditulis
- Verifikasi kredensial database
- Periksa konfigurasi masa hidup sesi
- Bersihkan cookie browser untuk domain

### Slow Page Load Times
**Problem:** Pages take too long to load.

**Diagnosis Steps:**
1. **Enable Debug Mode:**
   ```env
   # In .env file
   APP_DEBUG=true
   ```

2. **Check Database Queries:**
   - Install Laravel Debugbar
   - Review slow queries in logs

3. **Monitor System Resources:**
   ```bash
   # Check system resources
   top
   free -h
   df -h
   
   # Check database performance
   mysqladmin processlist
   ```

**Solutions:**
- Add database indexes to frequently queried columns
- Enable query caching
- Optimize images and assets
- Use CDN for static assets
- Implement pagination for large datasets

## 📊 Reporting Issues

### PDF Reports Not Generating
**Problem:** PDF report generation fails or produces blank documents.

**Diagnosis Steps:**
1. **Check DomPDF Installation:**
   ```bash
   composer show barryvdh/laravel-dompdf
   ```

2. **Review Error Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Test PDF Generation:**
   ```php
   // In tinker
   $pdf = \ Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test</h1>');
   $pdf->save('/tmp/test.pdf');
   ```

**Solusi:**
- Pastikan ekstensi PHP `gd` dan `imagick` terinstal
- Periksa izin tulis pada direktori penyimpanan
- Tingkatkan batas memori PHP
- Verifikasi sintaks template HTML

### Data Hilang dalam Laporan
**Masalah:** Laporan menampilkan data yang tidak lengkap atau hilang.

**Langkah Diagnosis:**
1. **Periksa Filter Tanggal:**
   - Verifikasi rentang tanggal yang dipilih
   - Pastikan pengaturan zona waktu cocok

2. **Tinjau Ketersediaan Data:**
   ```sql
   SELECT COUNT(*) FROM device_logs 
   WHERE checked_at BETWEEN 'start_date' AND 'end_date';
   ```

3. **Periksa Pemilihan Perangkat:**
   - Verifikasi perangkat yang dipilih memiliki data
   - Periksa status aktif perangkat

**Solusi:**
- Sesuaikan pemilihan rentang tanggal
- Pastikan perangkat ditandai sebagai aktif
- Verifikasi script pemantauan berjalan secara teratur
- Periksa celah data dalam log

## 🛡️ Masalah Keamanan

### Upaya Akses Tidak Sah
**Masalah:** Upaya login mencurigakan atau akses tidak sah.

**Langkah Diagnosis:**
1. **Tinjau Log Akses:**
   ```bash
   # Log akses server web
   tail -f /var/log/apache2/access.log
   
   # Log Laravel
   grep "unauthorized\|forbidden" storage/logs/laravel.log
   ```

2. **Periksa Upaya Login Gagal:**
   ```sql
   SELECT * FROM failed_jobs WHERE name LIKE '%login%';
   ```

3. **Monitor Sesi Pengguna:**
   ```sql
   SELECT user_id, ip_address, user_agent, last_activity 
   FROM sessions ORDER BY last_activity DESC LIMIT 10;
   ```

**Solusi:**
- Terapkan pembatasan kecepatan
- Aktifkan otentikasi dua faktor
- Siapkan daftar putih IP
- Konfigurasikan aturan firewall
- Tinjau izin pengguna secara teratur

### Peringatan Pemindaian Kerentanan
**Masalah:** Pemindai keamanan mendeteksi potensi kerentanan.

**Langkah Diagnosis:**
1. **Jalankan Pemindaian Keamanan:**
   ```bash
   # Instal pemeriksa keamanan Enlightn
   composer require enlightn/security-checker
   
   # Jalankan pemeriksaan keamanan
   php artisan security:check
   ```

2. **Periksa Versi Paket:**
   ```bash
   composer outdated
   ```

3. **Tinjau Konfigurasi:**
   - Periksa informasi debug yang terpapar
   - Verifikasi header aman
   - Tinjau pengaturan CORS

**Solusi:**
- Perbarui paket rentan
- Terapkan patch keamanan
- Konfigurasikan header aman
- Terapkan kebijakan keamanan konten
- Audit keamanan berkala

## ⏰ Masalah Tugas Terjadwal

### Pemantauan Tidak Berjalan Otomatis
**Masalah:** Tugas pemantauan terjadwal tidak dieksekusi.

**Langkah Diagnosis:**
1. **Periksa Konfigurasi Cron:**
   ```bash
   crontab -l
   ```

2. **Uji Perintah Artisan Secara Manual:**
   ```bash
   php artisan monitor:devices
   ```

3. **Tinjau Log Cron:**
   ```bash
   # Periksa log cron sistem
   tail -f /var/log/cron
   
   # Periksa log tugas terjadwal Laravel
   tail -f storage/logs/laravel.log | grep schedule
   ```

4. **Verifikasi Layanan Cron:**
   ```bash
   systemctl status cron  # atau crond di beberapa sistem
   ```

**Solusi:**
- Pastikan daemon cron berjalan
- Verifikasi sintaks tugas cron
- Periksa izin file pada perintah artisan
- Atur PATH yang tepat di crontab
- Tambahkan logging ke tugas cron untuk debugging

### Pembatasan Waktu Eksekusi Tugas
**Masalah:** Tugas pemantauan melebihi batas waktu eksekusi.

**Langkah Diagnosis:**
1. **Periksa Konfigurasi PHP:**
   ```bash
   php -i | grep max_execution_time
   ```

2. **Tinjau Kinerja Tugas:**
   ```bash
   # Ukur waktu eksekusi perintah
   time php artisan monitor:devices
   ```

3. **Periksa Batas Proses:**
   ```bash
   ulimit -a
   ```

**Solusi:**
- Tingkatkan `max_execution_time` di php.ini
- Optimalkan kueri database
- Terapkan pemrosesan batch untuk set perangkat besar
- Gunakan worker antrian untuk pemrosesan asinkron
- Tambahkan indikator kemajuan ke tugas berjalan lama

## 🐍 Masalah Script Python

### Script Gagal Dimulai
**Masalah:** Script pemantauan Python mogok saat startup.

**Langkah Diagnosis:**
1. **Periksa Versi Python:**
   ```bash
   python3 --version
   ```

2. **Verifikasi Dependensi:**
   ```bash
   pip list | grep requests
   ```

3. **Jalankan dengan Output Verbose:**
   ```bash
   python3 scripts/monitor.py --debug
   ```

4. **Check Syntax:**
   ```bash
   python3 -m py_compile scripts/monitor.py
   ```

**Solutions:**
- Install required Python packages
- Verify Python version compatibility
- Check file permissions
- Ensure script is executable

### Network Connectivity Problems
**Problem:** Python script cannot reach network devices.

**Diagnosis Steps:**
1. **Test Network Access:**
   ```bash
   # From server running script
   ping DEVICE_IP_ADDRESS
   telnet DEVICE_IP_ADDRESS PORT
   ```

2. **Check Firewall Rules:**
   ```bash
   iptables -L
   ```

3. **Verify Network Configuration:**
   ```bash
   ip route
   ```

**Solutions:**
- Adjust firewall rules
- Configure routing
- Check VLAN configurations
- Verify network segmentation policies

## 📱 Mobile/Browser Issues

### Interface Not Responsive on Mobile
**Problem:** Mobile interface appears broken or unusable.

**Diagnosis Steps:**
1. **Check Viewport Meta Tag:**
   ```html
   <meta name="viewport" content="width=device-width, initial-scale=1">
   ```

2. **Review CSS Media Queries:**
   ```css
   @media (max-width: 768px) {
     /* Mobile styles */
   }
   ```

3. **Test on Multiple Devices:**
   - Various screen sizes
   - Different browsers
   - iOS and Android platforms

**Solusi:**
- Terapkan prinsip desain responsif
- Tambahkan CSS khusus mobile
- Uji dengan alat pengembang browser
- Optimalkan target sentuh untuk mobile

### Masalah Kompatibilitas Fitur
**Masalah:** Beberapa fitur tidak berfungsi di browser tertentu.

**Langkah Diagnosis:**
1. **Periksa Dukungan Browser:**
   - Gunakan CanIUse.com untuk memverifikasi dukungan fitur
   - Uji di beberapa browser

2. **Tinjau Kompatibilitas JavaScript:**
   ```javascript
   // Periksa fitur JavaScript modern
   if (typeof Promise !== 'undefined') {
     // Fitur didukung
   }
   ```

3. **Gunakan Polyfills:**
   ```html
   <script src="https://polyfill.io/v3/polyfill.min.js"></script>
   ```

**Solusi:**
- Tambahkan polyfill kompatibilitas browser
- Terapkan degradasi elegan
- Sediakan implementasi fallback
- Gunakan teknik peningkatan progresif

## 🔧 Masalah Pemeliharaan

### Kehabisan Ruang Disk
**Masalah:** Sistem kehabisan ruang disk.

**Langkah Diagnosis:**
1. **Periksa Penggunaan Disk:**
   ```bash
   df -h
   ```

2. **Temukan File Besar:**
   ```bash
   du -ah /var/www | sort -rh | head -20
   ```

3. **Tinjau Ukuran Log:**
   ```bash
   ls -lh storage/logs/
   ```

**Solusi:**
- Terapkan rotasi log
- Bersihkan backup lama
- Arsipkan data historis
- Tingkatkan ruang disk
- Konfigurasikan tingkat log secara tepat

### Degradasi Kinerja Database
**Masalah:** Kueri database menjadi semakin lambat seiring waktu.

**Langkah Diagnosis:**
1. **Periksa Kinerja Kueri:**
   ```sql
   SHOW PROCESSLIST;
   EXPLAIN SELECT * FROM devices WHERE status = 'down';
   ```

2. **Tinjau Penggunaan Indeks:**
   ```sql
   SHOW INDEX FROM devices;
   ANALYZE TABLE devices;
   ```

3. **Monitor Kueri Lambat:**
   ```bash
   # Aktifkan log kueri lambat di MySQL
   # Periksa /var/log/mysql/slow.log
   ```

**Solusi:**
- Tambahkan indeks database yang hilang
- Optimalkan kueri kompleks
- Terapkan caching kueri
- Partisi tabel besar
- Pemeliharaan database berkala

## 🆘 Prosedur Darurat

### Sistem Tidak Merespons Sama Sekali
**Masalah:** Seluruh sistem tidak dapat diakses.

**Tindakan Segera:**
1. **Periksa Status Server:**
   ```bash
   ping SERVER_IP
   ssh SERVER_USER@SERVER_IP
   ```

2. **Restart Layanan:**
   ```bash
   sudo systemctl restart apache2  # atau nginx
   sudo systemctl restart mysql
   sudo systemctl restart redis
   ```

3. **Periksa Sumber Daya Sistem:**
   ```bash
   top
   free -h
   df -h
   ```

4. **Tinjau Perubahan Terkini:**
   - Periksa log deployment
   - Tinjau perubahan konfigurasi
   - Identifikasi pembaruan terkini

**Langkah Pemulihan:**
- Kembalikan ke status kerja sebelumnya
- Pulihkan dari backup terkini
- Kembalikan perubahan bermasalah
- Terapkan perbaikan di lingkungan pengembangan terlebih dahulu

### Kehilangan Data Kritis
**Masalah:** Data pemantauan penting telah hilang.

**Tindakan Pemulihan:**
1. **Periksa Backup:**
   ```bash
   # Daftar backup yang tersedia
   ls -la /backup/
   
   # Periksa dump database
   ls -la /backup/mysql/
   ```

2. **Pulihkan dari Backup:**
   ```bash
   # Pulihkan database
   mysql -u username -p database_name < backup_file.sql
   ```

3. **Buat Ulang Data yang Hilang:**
   - Tambahkan ulang perangkat
   - Pulihkan akun pengguna
   - Konfigurasikan ulang pengaturan

**Pencegahan:**
- Terapkan backup otomatis berkala
- Uji prosedur pemulihan backup
- Simpan backup di beberapa lokasi
- Dokumentasikan prosedur pemulihan

## 📞 Mendapatkan Bantuan

### Sumber Komunitas
- **Dokumentasi Laravel:** https://laravel.com/docs
- **Dokumentasi Tailwind CSS:** https://tailwindcss.com/docs
- **Dokumentasi Resmi Python:** https://docs.python.org
- **Stack Overflow:** Cari pesan kesalahan spesifik

### Dukungan Profesional
- **Laravel Forge:** Manajemen server
- **Laravel Envoyer:** Alat deployment
- **Konsultan Mitra:** Untuk dukungan enterprise

### Pelaporan Masalah
Saat melaporkan masalah, sertakan:
1. **Deskripsi Terperinci:** Apa yang terjadi dan kapan
2. **Langkah-langkah untuk Mereproduksi:** Langkah tepat yang menyebabkan masalah
3. **Pesan Kesalahan:** Teks kesalahan lengkap
4. **Informasi Lingkungan:** 
   - Versi OS
   - Versi PHP
   - Versi database
   - Informasi browser
5. **Screenshot:** Jika relevan
6. **Kutipan Log:** Bagian relevan dari file log

## 📚 Sumber Daya Tambahan

### Perintah Berguna
```bash
# Pemeliharaan sistem
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

# Pemeliharaan database
php artisan migrate:status
php artisan migrate:fresh --seed

# Pemantauan
php artisan monitor:devices
php artisan schedule:run

# Log
tail -f storage/logs/laravel.log
```

### File Konfigurasi untuk Diperiksa
- `.env` - Konfigurasi lingkungan
- `config/database.php` - Pengaturan database
- `config/logging.php` - Konfigurasi logging
- `config/mail.php` - Pengaturan email
- `php.ini` - Konfigurasi PHP
- Konfigurasi server web (Apache/Nginx)

### Checklist Pemantauan
Tugas pemeliharaan berkala:
- [ ] Periksa ruang disk mingguan
- [ ] Tinjau log harian
- [ ] Perbarui dependensi bulanan
- [ ] Uji backup triwulanan
- [ ] Tinjau keamanan bulanan
- [ ] Penyetelan kinerja sesuai kebutuhan
- [ ] Optimalisasi database triwulanan