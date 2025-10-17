# 👤 Panduan Pengguna

Selamat datang di Panduan Pengguna Sistem Monitoring Jaringan. Panduan ini akan membantu Anda menavigasi dan menggunakan semua fitur sistem secara efektif.

## 🎯 Memulai

### Login
1. Arahkan ke URL aplikasi (misalnya, http://localhost:8000)
2. Klik "Login" di pojok kanan atas
3. Masukkan kredensial Anda:
   - **Admin:** admin@sttwastukancana.ac.id / password
   - **Petugas:** petugas@sttwastukancana.ac.id / password
4. Klik "Sign in"

### Ikhtisar Dashboard
Setelah login, Anda akan diarahkan ke dashboard yang menampilkan:
- **Total Perangkat:** Jumlah perangkat yang dipantau
- **Perangkat Aktif:** Perangkat yang sedang online
- **Perangkat Tidak Aktif:** Perangkat yang sedang offline
- **Peringatan Aktif:** Peringatan yang belum diselesaikan
- **Hirarki Perangkat:** Representasi visual struktur jaringan dengan pembaruan real-time
- **Peringatan Terbaru:** Notifikasi sistem terbaru
- **Grafik Kinerja Jaringan:** Tren waktu respons real-time dengan pembaruan per-detik
- **Tombol Refresh Manual:** Tombol ikon "Scan" untuk refresh manual instan semua perangkat
- **Indikator Status:** Indikator berwarna yang menunjukkan status UP (hijau), DOWN (merah), atau UNKNOWN (abu-abu)

## 🖥️ Navigasi

Menu navigasi utama mencakup:
- **Dashboard:** Ikhtisar dan statistik sistem
- **Devices:** Bagian manajemen perangkat
- **Alerts:** Manajemen notifikasi
- **Reports:** Laporan dan analitik kinerja
- **Settings:** (Admin saja) Konfigurasi sistem
- **Profile:** Pengaturan akun pengguna

## 📱 Manajemen Perangkat

### Melihat Perangkat
Klik "Devices" di menu navigasi untuk melihat semua perangkat jaringan yang diorganisir dalam struktur hirarkis:
- **Level Utama:** Infrastruktur jaringan utama (router, switch utama)
- **Level Sub:** Perangkat distribusi (switch distribusi)
- **Level Perangkat:** Perangkat akhir (access point, server, printer)

Setiap kartu perangkat menampilkan:
- Nama dan alamat IP perangkat
- Status saat ini (Up/Down)
- Lokasi
- Perangkat anak (jika ada)

### Menambahkan Perangkat Baru
1. Klik "Devices" di navigasi
2. Klik tombol "Add Device"
3. Isi detail perangkat:
   - **Name:** Nama perangkat deskriptif
   - **IP Address:** Alamat IPv4 yang valid
   - **Type:** Router, Switch, Access Point, Server, atau Lainnya
   - **Hierarchy Level:** Utama, Sub, atau Perangkat
   - **Parent Device:** Pilih induk jika berlaku
   - **Location:** Lokasi fisik perangkat
   - **Description:** Catatan tambahan
4. Aktifkan saklar "Active" untuk mengaktifkan pemantauan
5. Klik "Save Device"

### Mengedit Perangkat
1. Navigasi ke bagian Devices
2. Temukan perangkat yang ingin diedit
3. Klik ikon pensil di sebelah perangkat
4. Lakukan perubahan yang diperlukan
5. Klik "Update Device"

### Menghapus Perangkat
1. Navigasi ke bagian Devices
2. Temukan perangkat yang ingin dihapus
3. Klik ikon tempat sampah di sebelah perangkat
4. Konfirmasi penghapusan di dialog popup

## ⚠️ Manajemen Peringatan

### Melihat Peringatan
Klik "Alerts" di menu navigasi untuk melihat semua notifikasi sistem:
- **Peringatan Aktif:** Masalah yang belum diselesaikan
- **Peringatan yang Diselesaikan:** Notifikasi yang sebelumnya ditangani

Setiap peringatan menampilkan:
- Nama perangkat yang terpengaruh
- Pesan peringatan
- Timestamp
- Status saat ini

### Menyelesaikan Peringatan
1. Navigasi ke bagian Alerts
2. Temukan peringatan yang ingin diselesaikan
3. Klik tombol "Resolve"
4. Status peringatan akan berubah menjadi "Resolved"

## 📊 Laporan dan Analitik

### Laporan Kinerja
1. Klik "Reports" di menu navigasi
2. Pilih rentang tanggal dan perangkat untuk disertakan
3. Pilih jenis laporan:
   - **Laporan Ringkasan:** Ikhtisar tingkat tinggi
   - **Laporan Terperinci:** Analisis mendalam
   - **Laporan Kustom:** Metrik spesifik
4. Klik "Generate Report"

### Ekspor PDF
1. Setelah membuat laporan, klik "Export to PDF"
2. Laporan akan diunduh secara otomatis
3. Buka dengan pembaca PDF apa pun

### Opsi Pemfilteran
Laporan dapat difilter berdasarkan:
- **Rentang Tanggal:** Harian, Mingguan, Bulanan, Tahunan
- **Jenis Perangkat:** Router, Switch, Access Point, dll.
- **Status:** Up, Down, atau Keduanya
- **Lokasi:** Bangunan atau lantai spesifik

## 🌙 Mode Gelap

Sistem mendukung tema terang dan gelap:
1. Klik ikon bulan/matahari di pojok kanan atas
2. Alihkan antara mode terang dan gelap
3. Preferensi Anda disimpan secara otomatis

## ⚡ Pemantauan Real-time dan Refresh Manual

Sistem menyediakan pemantauan real-time sejati dengan pembaruan per-detik:

### Dashboard Real-time
- Status perangkat diperbarui secara otomatis setiap detik
- Grafik kinerja jaringan diperbarui terus-menerus
- Visualisasi hirarki menunjukkan perubahan status langsung
- Indikator status menggunakan pewarnaan:
  - **Hijau (UP)**: Perangkat merespons normal
  - **Merah (DOWN)**: Perangkat tidak merespons
  - **Abu-abu (UNKNOWN)**: Alamat IP tidak valid atau hilang

### Refresh Manual
1. Klik tombol ikon "Scan" berbentuk lingkaran di bagian atas dashboard
2. Tombol akan menampilkan animasi berputar saat menyegarkan
3. Semua perangkat akan diperiksa segera
4. Hasil diperbarui secara real-time saat pemeriksaan selesai
5. Notifikasi sukses akan muncul saat selesai

Refresh manual ini berguna ketika Anda membutuhkan informasi status segera tanpa menunggu pemeriksaan otomatis berikutnya.

## 👤 Pengaturan Profil

### Memperbarui Informasi Profil
1. Klik nama pengguna Anda di pojok kanan atas
2. Pilih "Profile" dari dropdown
3. Perbarui:
   - Nama
   - Alamat email
   - Foto profil (opsional)
4. Klik "Save Changes"

### Mengganti Password
1. Dari halaman Profile, klik tab "Change Password"
2. Masukkan password saat ini
3. Masukkan password baru dua kali untuk konfirmasi
4. Klik "Update Password"

## 🔧 Fungsi Admin

Administrator memiliki kemampuan tambahan:

### Manajemen Pengguna
1. Klik "Settings" di menu navigasi
2. Pilih tab "Users"
3. Lihat, tambah, edit, atau hapus akun pengguna
4. Tetapkan peran (Admin atau Petugas)

### Konfigurasi Sistem
1. Klik "Settings" di menu navigasi
2. Konfigurasikan:
   - Interval pemantauan
   - Ambang peringatan
   - Notifikasi email
   - Branding sistem

### Manajemen Peran
1. Klik "Settings" di menu navigasi
2. Pilih tab "Roles"
3. Buat, edit, atau hapus peran
4. Tetapkan izin ke peran

## 🐍 Script Pemantauan Python

Sistem mencakup script pemantauan Python untuk pemeriksaan perangkat otomatis:

### Penyiapan
1. Instal Python 3.6 atau lebih tinggi
2. Instal paket yang diperlukan:
   ```bash
   pip install requests
   ```

### Konfigurasi
Atur variabel lingkungan:
```bash
export API_BASE_URL="http://your-domain.com"
export API_TOKEN="your-api-token"
```

### Menjalankan Script
```bash
cd scripts
python3 monitor.py
```

### Penjadwalan
Tambahkan ke crontab untuk eksekusi otomatis setiap 5 menit:
```bash
*/5 * * * * cd /path/to/project/scripts && python3 monitor.py >> /var/log/monitor.log 2>&1
```

## 🆘 Penyelesaian Masalah

### Masalah Umum

**Tidak Bisa Login:**
- Verifikasi username dan password
- Periksa apakah akun aktif
- Reset password jika diperlukan

**Perangkat Muncul Offline:**
- Verifikasi alamat IP benar
- Periksa status perangkat fisik
- Pastikan perangkat mengizinkan permintaan ping
- Uji konektivitas secara manual

**Peringatan Tidak Hapus:**
- Verifikasi perangkat benar-benar online kembali
- Selesaikan peringatan persisten secara manual
- Periksa log script pemantauan

**Laporan Tidak Dibuat:**
- Verifikasi pemilihan rentang tanggal
- Periksa pemilihan perangkat
- Pastikan data cukup tersedia

### Hubungi Dukungan
Jika Anda terus mengalami masalah:
1. Dokumentasikan masalah dengan screenshot
2. Catat waktu dan langkah-langkah yang diambil
3. Hubungi administrator sistem

## 📱 Penggunaan Mobile

Sistem sepenuhnya responsif dan bekerja di perangkat mobile:
- Semua navigasi beradaptasi dengan layar lebih kecil
- Kontrol yang ramah sentuhan
- Tampilan yang dioptimalkan untuk tampilan vertikal
- Fungsi yang sama dengan versi desktop

## 🔒 Praktik Terbaik Keamanan

### Keamanan Password
- Gunakan password kuat dengan karakter campuran
- Ganti password secara berkala
- Jangan pernah berbagi kredensial
- Aktifkan otentikasi dua faktor jika tersedia

### Manajemen Sesi
- Selalu logout saat selesai
- Hindari menggunakan komputer umum
- Bersihkan cache browser secara berkala
- Laporkan aktivitas mencurigakan

## 🔄 Pemeliharaan Rutin

Untuk administrator sistem:

### Pemeriksaan Harian
- Tinjau peringatan aktif
- Pantau kinerja sistem
- Periksa ruang disk dan backup

### Tugas Mingguan
- Perbarui inventaris perangkat
- Tinjau akses pengguna
- Bersihkan peringatan yang diselesaikan

### Tinjauan Bulanan
- Analisis laporan kinerja
- Audit izin pengguna
- Tinjau log sistem
- Perbarui dokumentasi