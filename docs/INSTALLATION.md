# 🚀 Panduan Instalasi

Panduan instalasi lengkap untuk Sistem Monitoring Jaringan.

## 📋 Persyaratan Sistem

### Persyaratan Server
- **Sistem Operasi:** Linux (Ubuntu 20.04+/22.04+, CentOS 8+, Debian 11+) atau Windows Server
- **Web Server:** Apache 2.4+ atau Nginx 1.18+
- **PHP:** 8.2 atau lebih tinggi
- **Database:** MySQL 8.0+ atau MariaDB 10.6+
- **Memori:** Minimum 2GB RAM (4GB direkomendasikan)
- **Penyimpanan:** Minimum 10GB ruang disk bebas
- **Jaringan:** Alamat IP statis direkomendasikan

### Dependensi Perangkat Lunak
- **Composer:** 2.2+ untuk manajemen dependensi PHP
- **Node.js:** 16+ untuk kompilasi aset frontend
- **npm:** 8+ untuk manajemen paket Node.js
- **Python:** 3.6+ untuk script pemantauan
- **Git:** Untuk kontrol versi dan deployment

### Komponen Opsional
- **Redis:** Untuk pemrosesan antrian dan caching
- **Supervisor:** Untuk pemantauan proses
- **Sertifikat SSL:** Untuk HTTPS (direkomendasikan)

## 🛠️ Langkah-langkah Instalasi

### Langkah 1: Persiapkan Server

#### Perbarui Paket Sistem
```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade -y

# CentOS/RHEL
sudo yum update -y
# atau
sudo dnf update -y
```

#### Instal Paket yang Diperlukan
```bash
# Ubuntu/Debian
sudo apt install -y git curl wget unzip software-properties-common

# Instal PHP 8.2 dan ekstensi
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl php8.2-soap

# Instal komponen lainnya
sudo apt install -y apache2 mysql-server nodejs npm python3 python3-pip
```

#### Instal Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Langkah 2: Konfigurasi Database

#### Jalankan Layanan MySQL
```bash
sudo systemctl start mysql
sudo systemctl enable mysql
```

#### Amanikan Instalasi MySQL
```bash
sudo mysql_secure_installation
```

#### Buat Database dan Pengguna
```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE monitoring_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'monitor_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON monitoring_system.* TO 'monitor_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Langkah 3: Deploy Aplikasi

#### Clone Repositori
```bash
cd /var/www
sudo git clone https://github.com/your-repo/monitoring-konektivitas.git
sudo chown -R www-data:www-data monitoring-konektivitas
cd monitoring-konektivitas
```

#### Instal Dependensi PHP
```bash
composer install --no-dev --optimize-autoloader
```

#### Instal Dependensi Node
```bash
npm install
npm run build
```

### Langkah 4: Konfigurasi Aplikasi

#### Buat File Lingkungan
```bash
cp .env.example .env
```

#### Edit Konfigurasi Lingkungan
```bash
nano .env
```

Konfigurasikan pengaturan penting berikut:
```env
APP_NAME="Sistem Monitoring Jaringan"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=monitoring_system
DB_USERNAME=monitor_user
DB_PASSWORD=YOUR_STRONG_PASSWORD

LOG_CHANNEL=stack
LOG_LEVEL=debug

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Generate Kunci Aplikasi
```bash
php artisan key:generate
```

### Langkah 5: Migrasi Database dan Seeding

#### Jalankan Migrasi
```bash
php artisan migrate
```

#### Isi Database
```bash
php artisan db:seed
```

### Langkah 6: Konfigurasi Web Server

#### Konfigurasi Apache
Buat file virtual host:
```bash
sudo nano /etc/apache2/sites-available/monitoring.conf
```

Tambahkan konfigurasi berikut:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/monitoring-konektivitas/public

    <Directory /var/www/monitoring-konektivitas/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/monitoring_error.log
    CustomLog ${APACHE_LOG_DIR}/monitoring_access.log combined
</VirtualHost>
```

Aktifkan situs dan modul yang diperlukan:
```bash
sudo a2ensite monitoring.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Konfigurasi Nginx (Alternatif)
Buat file konfigurasi:
```bash
sudo nano /etc/nginx/sites-available/monitoring
```

Tambahkan konfigurasi berikut:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/monitoring-konektivitas/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/monitoring /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Langkah 7: Atur Izin File

```bash
sudo chown -R www-data:www-data /var/www/monitoring-konektivitas
sudo chmod -R 755 /var/www/monitoring-konektivitas
sudo chmod -R 775 /var/www/monitoring-konektivitas/storage
sudo chmod -R 775 /var/www/monitoring-konektivitas/bootstrap/cache
```

### Langkah 8: Konfigurasi SSL (Direkomendasikan)

#### Instal Certbot
```bash
# Ubuntu/Debian
sudo apt install certbot python3-certbot-apache -y
# atau untuk Nginx
sudo apt install certbot python3-certbot-nginx -y
```

#### Dapatkan Sertifikat SSL
```bash
# Untuk Apache
sudo certbot --apache -d your-domain.com -d www.your-domain.com

# Untuk Nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### Langkah 9: Konfigurasi Script Pemantauan

#### Instal Dependensi Python
```bash
cd /var/www/monitoring-konektivitas/scripts
pip3 install requests
```

#### Uji Eksekusi Script
```bash
python3 monitor.py
```

#### Jadikan Script Dapat Dieksekusi
```bash
chmod +x /var/www/monitoring-konektivitas/scripts/monitor.py
```

### Langkah 10: Siapkan Tugas Terjadwal

#### Konfigurasikan Cron Job
```bash
sudo crontab -e
```

Tambahkan baris berikut:
```bash
# Penjadwal Laravel
* * * * * cd /var/www/monitoring-konektivitas && php artisan schedule:run >> /dev/null 2>&1

# Pemantauan jaringan setiap 5 menit
*/5 * * * * cd /var/www/monitoring-konektivitas && python3 scripts/monitor.py >> /var/log/monitor.log 2>&1
```

#### Siapkan Rotasi Log
```bash
sudo nano /etc/logrotate.d/monitoring
```

Tambahkan konfigurasi berikut:
```
/var/log/monitor.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

### Langkah 11: Verifikasi Instalasi

#### Periksa Kesehatan Aplikasi
Kunjungi domain Anda di browser web:
```
http://your-domain.com
```

#### Uji Endpoint API
```bash
curl -X GET http://your-domain.com/api/devices
```

#### Periksa Script Pemantauan
```bash
cd /var/www/monitoring-konektivitas
python3 scripts/monitor.py
```

## 🔧 Konfigurasi Pascainstalasi

### Konfigurasi Akun Admin
1. Kunjungi aplikasi di browser Anda
2. Klik "Login" dan gunakan kredensial default:
   - Email: `admin@sttwastukancana.ac.id`
   - Password: `password`
3. Ubah password segera setelah login

### Konfigurasi Perangkat
1. Navigasi ke bagian "Devices"
2. Tambahkan perangkat jaringan Anda:
   - Router
   - Switch
   - Access Point
   - Server
3. Atur hirarki perangkat:
   - Hubungan induk-anak
   - Jenis dan lokasi perangkat

### Konfigurasi Notifikasi (Opsional)
1. Atur konfigurasi email di `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.your-provider.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@domain.com
   MAIL_PASSWORD=your-email-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=monitoring@your-domain.com
   MAIL_FROM_NAME="Network Monitoring"
   ```

### Konfigurasi Backup (Direkomendasikan)
Siapkan backup otomatis untuk database dan file aplikasi.

## 🛡️ Penguatan Keamanan

### Izin File
Pastikan izin file yang tepat:
```bash
# File aplikasi
sudo chown -R www-data:www-data /var/www/monitoring-konektivitas
sudo find /var/www/monitoring-konektivitas -type d -exec chmod 755 {} \;
sudo find /var/www/monitoring-konektivitas -type f -exec chmod 644 {} \;

# Direktori penyimpanan
sudo chmod -R 775 /var/www/monitoring-konektivitas/storage
sudo chmod -R 775 /var/www/monitoring-konektivitas/bootstrap/cache
```

### Sembunyikan Informasi Sensitif
1. Nonaktifkan mode debug di produksi:
   ```env
   APP_DEBUG=false
   ```

2. Konfigurasikan halaman error yang tepat:
   ```bash
   # Apache
   sudo a2dissite 000-default
   sudo systemctl reload apache2
   
   # Nginx
   sudo rm /etc/nginx/sites-enabled/default
   sudo systemctl reload nginx
   ```

### Konfigurasi Firewall
```bash
# UFW (Ubuntu)
sudo ufw allow ssh
sudo ufw allow 'Apache Full'  # atau 'Nginx Full'
sudo ufw --force enable

# Atau dengan port spesifik
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp     # HTTP
sudo ufw allow 443/tcp    # HTTPS
sudo ufw --force enable
```

## 📊 Optimasi Kinerja

### Aktifkan OPcache
Edit konfigurasi PHP:
```bash
sudo nano /etc/php/8.2/apache2/php.ini
# atau untuk Nginx dengan FPM
sudo nano /etc/php/8.2/fpm/php.ini
```

Tambahkan atau ubah pengaturan OPcache:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=12
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

Restart web server:
```bash
# Apache
sudo systemctl restart apache2

# Nginx dengan PHP-FPM
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Konfigurasi Optimasi Database
Edit konfigurasi MySQL:
```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Tambahkan pengaturan optimasi:
```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
query_cache_type = 1
query_cache_size = 128M
```

Restart MySQL:
```bash
sudo systemctl restart mysql
```

## 🔄 Jadwal Pemeliharaan

### Tugas Harian
```bash
# Periksa sumber daya sistem
df -h
free -h
top -b -n 1 | head -20

# Periksa log untuk error
tail -n 100 /var/log/apache2/error.log | grep -i error
tail -n 100 /var/log/mysql/error.log | grep -i error
tail -n 100 /var/www/monitoring-konektivitas/storage/logs/laravel.log | grep -i error
```

### Tugas Mingguan
```bash
# Perbarui paket sistem
sudo apt update && sudo apt upgrade -y

# Periksa penggunaan disk
du -sh /var/www/monitoring-konektivitas/storage/logs/

# Optimalkan database
mysql -u monitor_user -p monitoring_system -e "OPTIMIZE TABLE devices, device_logs, alerts;"
```

### Tugas Bulanan
```bash
# Perbarui aplikasi
cd /var/www/monitoring-konektivitas
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm install
npm run build
php artisan optimize:clear
php artisan optimize

# Restart layanan
sudo systemctl restart apache2  # atau nginx dan php-fpm
```

## 🆘 Penyelesaian Masalah Umum

### Aplikasi Tidak Dimuat
1. Periksa status web server:
   ```bash
   sudo systemctl status apache2  # atau nginx
   ```

2. Periksa PHP-FPM (jika menggunakan Nginx):
   ```bash
   sudo systemctl status php8.2-fpm
   ```

3. Periksa izin file:
   ```bash
   ls -la /var/www/monitoring-konektivitas/public/
   ```

### Koneksi Database Gagal
1. Periksa layanan database:
   ```bash
   sudo systemctl status mysql
   ```

2. Uji koneksi database:
   ```bash
   mysql -u monitor_user -p monitoring_system
   ```

3. Verifikasi konfigurasi database di `.env`

### Masalah Script Pemantauan
1. Periksa eksekusi script:
   ```bash
   cd /var/www/monitoring-konektivitas/scripts
   python3 monitor.py --debug
   ```

2. Periksa log script:
   ```bash
   tail -f /var/log/monitor.log
   ```

3. Verifikasi konektivitas API:
   ```bash
   curl -X GET http://localhost/api/devices
   ```

## 📞 Dukungan dan Pembaruan

### Mendapatkan Bantuan
Untuk masalah yang tidak dicakup dalam panduan ini:
1. Periksa [Panduan Penyelesaian Masalah](TROUBLESHOOTING.md)
2. Tinjau log aplikasi:
   - `/var/log/apache2/error.log` atau `/var/log/nginx/error.log`
   - `/var/log/mysql/error.log`
   - `/var/www/monitoring-konektivitas/storage/logs/laravel.log`
3. Konsultasikan forum komunitas dan dokumentasi

### Pembaruan Berkala
Perbarui sistem secara berkala:
```bash
# Perbarui kode aplikasi
cd /var/www/monitoring-konektivitas
git pull origin main

# Perbarui dependensi
composer update
npm update

# Jalankan migrasi jika diperlukan
php artisan migrate --force

# Kompilasi ulang aset
npm run build

# Bersihkan cache
php artisan optimize:clear
php artisan optimize
```

## 📋 Ringkasan Checklist

Sebelum online, pastikan semua item ini telah selesai:

### ✅ Pre-Instalasi
- [ ] Server memenuhi persyaratan sistem
- [ ] Paket perangkat lunak yang diperlukan terinstal
- [ ] Server database dikonfigurasi dan berjalan
- [ ] Sertifikat SSL diperoleh (jika menggunakan HTTPS)

### ✅ Instalasi
- [ ] File aplikasi dideploy
- [ ] Dependensi PHP terinstal
- [ ] Dependensi Node.js terinstal dan aset dikompilasi
- [ ] Konfigurasi lingkungan disiapkan
- [ ] Database dimigrasi dan diisi
- [ ] Web server dikonfigurasi dan berjalan
- [ ] SSL dikonfigurasi (jika berlaku)

### ✅ Pascainstalasi
- [ ] Izin file diatur dengan benar
- [ ] Script pemantauan diuji dan berfungsi
- [ ] Tugas terjadwal dikonfigurasi
- [ ] Akun admin dikonfigurasi
- [ ] Perangkat awal ditambahkan ke sistem
- [ ] Penguatan keamanan diterapkan
- [ ] Optimasi kinerja diterapkan
- [ ] Strategi backup dikonfigurasi
- [ ] Pemantauan diverifikasi berfungsi

### ✅ Going Live
- [ ] Pengujian final selesai
- [ ] Dokumentasi ditinjau dan dipahami
- [ ] Kontak dukungan ditetapkan
- [ ] Jadwal pemeliharaan direncanakan
- [ ] Peringatan pemantauan dikonfigurasi

Selamat! Sistem Monitoring Jaringan Anda sekarang telah terinstal dan siap digunakan.