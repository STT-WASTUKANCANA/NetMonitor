# Sistem Monitoring Jaringan - Manajemen Alamat IP

## Mendapatkan Alamat IP untuk Perangkat Jaringan

Dokumen ini menjelaskan cara yang tepat untuk mendapatkan alamat IP untuk perangkat jaringan (utama, sub, dan perangkat biasa) untuk memastikan mereka tidak disalahidentifikasi sebagai "unknown" oleh sistem.

## Masalah: Status Perangkat "Unknown"

Sistem NetMonitor mungkin menampilkan status perangkat sebagai "unknown" karena berbagai alasan:

1. **Alamat IP tidak valid atau hilang** di database
2. **Perangkat offline** atau tidak merespons terhadap permintaan pemantauan
3. **Masalah konektivitas jaringan** antara sistem pemantauan dan perangkat
4. **Konfigurasi IP salah** pada perangkat

## Solusi: Akuisisi Alamat IP yang Tepat

### 1. Penugasan IP Manual

Metode paling dapat diandalkan adalah menetapkan alamat IP secara manual berdasarkan arsitektur jaringan Anda:

```bash
# Contoh struktur jaringan:
# - Perangkat Utama (Gateway/Router): 192.168.1.1
# - Perangkat Sub (Access Point, Switch): 192.168.1.2, 192.168.1.3, dll.
# - Perangkat Biasa (Server, Workstation): 192.168.1.10, 192.168.1.11, dll.
```

### 2. Pemindaian Jaringan untuk Menemukan IP Aktif

Gunakan script deteksi IP yang disertakan untuk mengidentifikasi perangkat aktif di jaringan Anda:

```bash
# Pertama, buat script dapat dieksekusi
chmod +x scripts/detect_ip.py

# Pindai rentang jaringan Anda (sesuaikan subnet sesuai kebutuhan)
python3 scripts/detect_ip.py --network 192.168.1.0/24

# Simpan hasil ke file
python3 scripts/detect_ip.py --network 192.168.1.0/24 --output detected_devices.json
```

### 3. Menggunakan Perintah Laravel untuk Audit Perangkat

Sistem menyertakan perintah untuk mengaudit perangkat yang ada dan mengidentifikasi yang memiliki IP unknown:

```bash
# Jalankan perintah deteksi IP
php artisan network:detect-ips

# Tentukan rentang jaringan berbeda
php artisan network:detect-ips --network 10.0.0.0/24

# Sesuaikan timeout ping
php artisan network:detect-ips --network 192.168.1.0/24 --timeout 3
```

### 4. Praktik Terbaik untuk Manajemen IP

1. **Gunakan IP Statis untuk Perangkat Kritis**: Tetapkan alamat IP statis ke perangkat utama dan sub untuk memastikan pemantauan yang konsisten.

2. **Dokumentasikan Arsitektur Jaringan**: Jaga peta jaringan Anda dengan penugasan IP:
   ```
   Router Utama: 192.168.1.1
   Router Sub 1: 192.168.1.2
   Router Sub 2: 192.168.1.3
   Switch 1: 192.168.1.10
   Server 1: 192.168.1.20
   ```

3. **Reservasi DHCP**: Untuk perangkat yang membutuhkan IP dinamis, gunakan reservasi DHCP untuk memastikan mereka selalu mendapatkan alamat IP yang sama.

4. **Audit IP Berkala**: Secara berkala jalankan perintah deteksi IP untuk mengidentifikasi dan memperbaiki masalah alamat IP.

### 5. Tips Penyelesaian Masalah

Jika perangkat masih muncul sebagai "unknown":

1. **Periksa Konektivitas IP**: Verifikasi bahwa sistem pemantauan dapat mengakses perangkat melalui ping:
   ```bash
   ping <device_ip_address>
   ```

2. **Verifikasi Status Perangkat**: Pastikan perangkat menyala dan terhubung ke jaringan.

3. **Periksa Pengaturan Firewall**: Beberapa perangkat mungkin memiliki firewall yang memblokir permintaan ping ICMP.

4. **Validasi Konfigurasi**: Konfirmasikan bahwa alamat IP di sistem NetMonitor cocok dengan IP sebenarnya perangkat.

5. **Periksa Segmentasi Jaringan**: Pastikan sistem pemantauan berada di segmen jaringan yang dapat mengakses semua perangkat yang dipantau.

### 6. Menambahkan Perangkat ke Sistem

Saat menambahkan perangkat baru ke NetMonitor:

1. Tentukan alamat IP perangkat menggunakan pemindaian jaringan atau konfigurasi manual
2. Verifikasi IP dapat dijangkau dari sistem pemantauan
3. Tambahkan perangkat ke NetMonitor dengan alamat IP yang benar
4. Atur tingkat hirarki yang sesuai (utama, sub, perangkat)
5. Verifikasi perangkat muncul sebagai "up" di dashboard pemantauan

Dengan mengikuti praktik ini, Anda dapat memastikan bahwa semua perangkat jaringan teridentifikasi dan dipantau dengan benar, mencegah masalah status "unknown".