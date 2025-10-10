## 🛰️ **Sistem Monitoring Konektivitas Internet – Spesifikasi Teknis Final**

### 🎯 **Tujuan Utama**

Membangun platform berbasis web untuk **memantau konektivitas dan performa jaringan** kampus STT Wastukancana secara *real-time* menggunakan **Laravel 12** (sebagai backend & API) dan **Python** (sebagai agen monitoring).
Sistem ini akan mendeteksi **gangguan koneksi, latensi tinggi, dan perangkat mati**, serta menampilkan **laporan visual & PDF** untuk berbagai periode (harian, mingguan, bulanan, tahunan).

---

## ⚙️ **Arsitektur & Teknologi**

| Komponen          | Teknologi                                          |
| ----------------- | -------------------------------------------------- |
| Backend & API     | Laravel 12 (PHP 8.2)                               |
| Skrip Monitoring  | Python                                             |
| Database          | MySQL / MariaDB                                    |
| Frontend          | TailwindCSS 4.0 + JS Library (Chart.js, Alpine.js) |
| Autentikasi       | Laravel Breeze                                     |
| Laporan PDF       | DomPDF / Laravel Snappy                            |
| Jadwal Monitoring | Cron Job (Python script)                           |

---

## 🧠 **Konsep Sistem & Hierarki Jaringan**

Struktur jaringan yang dimonitor bersifat **hierarkis**:

```
Provider Jaringan
   ↓
Router Utama  (IP: publik atau gateway)
   ↓
Router Sub    (terhubung ke Router Utama)
   ↓
Device        (Access Point, Switch, dll)
```

Jika **Router Utama** mati, maka otomatis semua **Router Sub** dan **Device** di bawahnya ditandai *unreachable*.

---

## 🔄 **Alur Kerja Sistem**

1. **Pendaftaran Perangkat (Admin)**

   * Input: nama, IP address, tipe, lokasi, dan jenis hierarki (`Utama`, `Sub`, atau `Terhubung ke Sub`).
   * Simpan ke tabel `devices`.

2. **Pemantauan Otomatis (Python Script)**

   * Skrip `monitor.py` dijalankan secara periodik (misal tiap 5 menit via Cron).
   * Mengambil daftar perangkat dari API Laravel.
   * Melakukan *ping* dan *port checking* ke setiap perangkat sesuai hierarkinya.
   * Mengirim hasil ke endpoint API Laravel:
     `POST /api/device/status` → data: `{ device_id, status, latency }`.

3. **Penyimpanan Data**

   * Laravel menyimpan data ke tabel `logs` (riwayat waktu respons) dan memperbarui kolom `status` di tabel `devices`.

4. **Peringatan & Visualisasi**

   * Jika status berubah dari `UP` ke `DOWN`, Laravel mencatat entri baru di `alerts`.
   * Dashboard menampilkan status, grafik performa, dan notifikasi.

5. **Laporan**

   * Admin dapat mengunduh laporan PDF berdasarkan filter waktu (hari, minggu, bulan, tahun).

---

## 🧩 **Struktur Database (Konseptual)**

### 1. `devices`

| Kolom           | Tipe                                                        | Keterangan                |
| --------------- | ----------------------------------------------------------- | ------------------------- |
| id              | bigint                                                      | Primary Key               |
| name            | varchar                                                     | Nama perangkat            |
| ip_address      | varchar                                                     | Alamat IP                 |
| type            | enum('router', 'switch', 'access_point', 'server', 'other') | Jenis perangkat           |
| hierarchy_level | enum('utama', 'sub', 'device')                              | Posisi dalam hierarki     |
| parent_id       | bigint                                                      | Relasi ke perangkat induk |
| location        | varchar                                                     | Lokasi fisik perangkat    |
| status          | enum('up','down','unknown')                                 | Status terkini            |
| last_checked_at | timestamp                                                   | Waktu terakhir dicek      |

### 2. `logs`

| Kolom         | Tipe              | Keterangan              |
| ------------- | ----------------- | ----------------------- |
| id            | bigint            | Primary Key             |
| device_id     | bigint (FK)       | ID perangkat            |
| response_time | float             | Waktu respons (ms)      |
| status        | enum('up','down') | Status hasil monitoring |
| checked_at    | timestamp         | Waktu pengecekan        |

### 3. `alerts`

| Kolom       | Tipe                      | Keterangan         |
| ----------- | ------------------------- | ------------------ |
| id          | bigint                    | Primary Key        |
| device_id   | bigint (FK)               | ID perangkat       |
| message     | text                      | Pesan peringatan   |
| status      | enum('active','resolved') | Status peringatan  |
| created_at  | timestamp                 | Waktu munculnya    |
| resolved_at | timestamp                 | Waktu diselesaikan |

### 4. `users`

| Kolom                   | Tipe                    | Keterangan       |
| ----------------------- | ----------------------- | ---------------- |
| id                      | bigint                  | Primary Key      |
| first_name              | varchar                 | Nama depan       |
| last_name               | varchar                 | Nama belakang    |
| email                   | varchar                 | Login email      |
| password                | varchar                 | Hash password    |
| role                    | enum('admin','petugas') | Hak akses        |
| profile_photo           | varchar                 | Path foto profil |
| created_at / updated_at | timestamp               | Otomatis Laravel |

---

## 📊 **Komponen Frontend**

### 1. **Dashboard (Admin & Petugas)**

* Kartu ringkasan:

  * Total perangkat
  * Perangkat UP / DOWN
  * Peringatan aktif
  * Rata-rata waktu respons (7 hari terakhir)
* Grafik tren respons
* Notifikasi terbaru

### 2. **Kelola Perangkat**

* Hierarki interaktif: klik *Router Utama* → tampil *Router Sub* → tampil *Device*.
* CRUD (admin) / View-only (petugas)

### 3. **Alert Notification**

* Daftar semua peringatan.
* Aksi: “Tandai selesai” untuk menutup alert.

### 4. **Pengaturan & Profil**

* Form edit akun, password, foto profil.
* Admin dapat ubah pengaturan global (misal interval monitoring).

---

## 🔗 **Integrasi Laravel – Python**

### Laravel API

| Endpoint             | Method | Fungsi                                |
| -------------------- | ------ | ------------------------------------- |
| `/api/devices`       | GET    | Mengambil semua perangkat             |
| `/api/device/status` | POST   | Menerima hasil pengecekan dari Python |
| `/api/logs`          | GET    | Mengambil data log                    |
| `/api/alerts`        | GET    | Mengambil daftar alert                |

### Python Script (monitor.py)

1. Ambil daftar perangkat dari `/api/devices`.
2. Untuk tiap perangkat:

   * Ping atau cek port.
   * Simpan waktu respons dan status.
3. Kirim hasil ke `/api/device/status` sebagai JSON.

---

## 👥 **Role dan Akses**

| Fitur                | Admin | Petugas   |
| -------------------- | ----- | --------- |
| Dashboard            | ✔️    | ✔️        |
| Kelola Perangkat     | CRUD  | View-only |
| Lihat Alert          | ✔️    | ✔️        |
| Tandai Alert Selesai | ✔️    | ✔️        |
| Kelola Akun          | ✔️    | ❌         |
| Laporan PDF          | ✔️    | View-only |
| Pengaturan Aplikasi  | ✔️    | ❌         |
| Profil Akun          | ✔️    | ✔️        |

---

## 🧾 **Laporan PDF**

* Filter waktu (hari/minggu/bulan/tahun)
* Menampilkan:

  * Total perangkat dipantau
  * Rata-rata latensi
  * Jumlah down-time
  * Grafik performa
  * Daftar peringatan penting

---

## 🧩 **Langkah Implementasi Selanjutnya**

1. Buat **struktur database** di Laravel (`migration` + `model`).
2. Buat **API endpoint** untuk integrasi Python.
3. Bangun **skrip Python** (`monitor.py`) untuk ping + port check.
4. Kembangkan **frontend dashboard** pakai Tailwind & Chart.js.
5. Tambahkan **fitur laporan PDF**.
6. Testing integrasi Laravel ↔ Python.
