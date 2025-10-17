# Penyelesaian Masalah: Status Perangkat "Unknown" di NetMonitor

## Ikhtisar
Dokumen ini memberikan panduan tentang cara menyelesaikan masalah status perangkat yang tampil sebagai "unknown" di sistem NetMonitor.

## Memahami Jenis Status

Sistem NetMonitor menggunakan tiga jenis status untuk perangkat jaringan:
- **up**: Perangkat merespons permintaan pemantauan
- **down**: Perangkat tidak merespons permintaan pemantauan
- **unknown**: Status perangkat tidak diketahui karena berbagai alasan

## Penyebab Umum Status "Unknown"

1. **Alamat IP Tidak Valid**: Alamat IP yang disimpan di database salah atau tidak dapat dijangkau
2. **Masalah Konektivitas Jaringan**: Server pemantau tidak dapat menghubungi perangkat
3. **Pemblokiran Firewall**: Perangkat memblokir permintaan ping ICMP
4. **Perangkat Offline**: Perangkat dalam keadaan mati atau terputus dari jaringan
5. **Masalah Konfigurasi**: Perangkat tidak dikonfigurasi dengan benar untuk pemantauan

## Solusi

### 1. Identifikasi Perangkat dengan Status "Unknown"

Pertama, mari kita identifikasi perangkat mana yang memiliki status unknown:

```bash
# Menggunakan Laravel Tinker
php artisan tinker
>>> App\Models\Device::where('status', 'unknown')->get()

# Atau periksa melalui query database
php artisan db:query "SELECT * FROM devices WHERE status = 'unknown'"
```

### 2. Verifikasi Alamat IP

Langkah paling penting adalah memastikan semua perangkat memiliki alamat IP yang benar:

#### Metode A: Verifikasi Manual
1. Identifikasi alamat IP sebenarnya dari setiap perangkat menggunakan alat jaringan
2. Perbarui database dengan alamat IP yang benar
3. Gunakan antarmuka NetMonitor untuk memperbarui informasi perangkat

#### Metode B: Deteksi IP Otomatis
1. Gunakan skrip deteksi IP yang baru dibuat:
   ```bash
   # Jadikan skrip dapat dieksekusi
   chmod +x scripts/detect_ip.py
   
   # Pindai jaringan Anda (sesuaikan subnet sesuai kebutuhan)
   python3 scripts/detect_ip.py --network 192.168.1.0/24
   ```

2. Bandingkan IP yang terdeteksi dengan yang ada di database NetMonitor Anda

### 3. Perbarui Informasi Perangkat

Setelah Anda memiliki alamat IP yang benar, perbarui di sistem:

```bash
# Secara manual perbarui perangkat melalui command line
php artisan tinker
>>> $device = App\Models\Device::find($id);
>>> $device->ip_address = '192.168.1.100';
>>> $device->save();
```

### 4. Uji Konektivitas Perangkat

Setelah memperbarui alamat IP, uji konektivitas:

#### Menggunakan endpoint bulk ping baru:
```bash
# Ini akan melakukan ping ke semua perangkat dan memperbarui statusnya
curl -X POST http://your-netmonitor-domain/api/devices/bulk-ping \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d "{}"
```

#### Menggunakan endpoint ping individual:
```bash
# Uji perangkat tertentu
curl -X POST http://your-netmonitor-domain/api/device/scan \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token" \
  -d '{"device_id": 1}'
```

### 5. Jalankan Perintah Deteksi IP Kustom

Gunakan perintah Laravel baru untuk mengaudit semua perangkat:

```bash
# Jalankan perintah deteksi IP
php artisan network:detect-ips

# Tentukan rentang jaringan spesifik
php artisan network:detect-ips --network 10.0.0.0/24
```

### 6. Verifikasi Pembaruan Status

Setelah melakukan langkah-langkah di atas, periksa apakah masalah status "unknown" telah teratasi:

```bash
# Periksa jumlah perangkat dengan berbagai status
php artisan tinker
>>> App\Models\Device::groupBy('status')->selectRaw('status, count(*) as count')->get()
```

## Strategi Pencegahan

### 1. Gunakan Alamat IP Statis
Untuk perangkat kritis (tingkat utama dan sub), tetapkan alamat IP statis untuk memastikan konsistensi.

### 2. Terapkan Reservasi DHCP
Untuk perangkat yang membutuhkan IP dinamis, gunakan reservasi DHCP untuk memastikan mereka selalu mendapatkan alamat IP yang sama.

### 3. Audit IP Berkala
Secara berkala jalankan perintah deteksi IP untuk mengidentifikasi potensi konflik IP atau perubahan:

```bash
# Jadwalkan audit IP berkala menggunakan cron
0 2 * * * cd /path/to/netmonitor && php artisan network:detect-ips
```

### 4. Dokumentasi Jaringan
Jaga peta jaringan dengan penugasan IP:

```
Router Utama: 192.168.1.1
Router Sub 1: 192.168.1.2
Router Sub 2: 192.168.1.3
Switch 1: 192.168.1.10
Server 1: 192.168.1.20
```

## Alat Tambahan

### Skrip Deteksi IP
Skrip `scripts/detect_ip.py` dapat digunakan untuk memindai jaringan Anda dan menemukan perangkat aktif:

```bash
# Pindai jaringan dan simpan hasil ke file
python3 scripts/detect_ip.py --network 192.168.1.0/24 --output detected_devices.json

# Sesuaikan timeout jika diperlukan
python3 scripts/detect_ip.py --network 192.168.1.0/24 --timeout 3
```

### Pembaruan Status Massal
Endpoint baru `/api/devices/bulk-ping` memungkinkan Anda untuk memperbarui semua status perangkat sekaligus, yang dapat membantu cepat menyelesaikan masalah status unknown.

## Tips Penyelesaian Masalah

Jika perangkat masih tampil sebagai "unknown":

1. **Periksa Konektivitas Jaringan**: Pastikan server pemantau dapat menghubungi perangkat:
   ```bash
   ping <device_ip_address>
   ```

2. **Periksa Pengaturan Firewall**: Beberapa perangkat secara default memblokir permintaan ping ICMP

3. **Verifikasi Status Perangkat**: Pastikan perangkat benar-benar menyala dan terhubung

4. **Periksa Segmentasi Jaringan**: Pastikan server pemantau berada di segmen jaringan yang dapat menghubungi semua perangkat yang dipantau

5. **Tinjau Log**: Periksa log Laravel untuk informasi kesalahan terperinci:
   ```bash
   tail -f storage/logs/laravel.log
   ```

Dengan mengikuti langkah-langkah ini dan menerapkan alat yang disediakan, Anda harus dapat menyelesaikan masalah status "unknown" dan menjaga pemantauan perangkat yang akurat di sistem NetMonitor.