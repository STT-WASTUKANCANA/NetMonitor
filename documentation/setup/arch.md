# Panduan Setup Lokal untuk Arch Linux

Panduan ini akan membantu Anda menjalankan sistem NetMonitor secara lokal di mesin Arch Linux.

## Prasyarat Sistem

Sebelum memulai, pastikan sistem Anda memiliki paket-paket berikut. Jalankan perintah di bawah ini untuk menginstalnya:

```bash
# Update sistem
sudo pacman -Syu

# Install Python, pip, dan dependencies build lainnya
sudo pacman -S python python-pip base-devel git

# Catatan: Kita akan menggunakan LAMPP (XAMPP) untuk database.
# Pastikan LAMPP sudah terinstall di /opt/lampp.

# Install Nmap (diperlukan untuk fitur scanning jaringan)
sudo pacman -S nmap
```

## Setup Database (LAMPP)

Kita menggunakan LAMPP (XAMPP) untuk database server. Jalankan perintah berikut untuk menyalakan LAMPP:

```bash
sudo /opt/lampp/lampp start
```

Pastikan output menunjukkan bahwa MySQL (dan layanan lain) berhasil dijalankan ("...ok").

*Secara default, user database adalah `root` dengan password kosong.*

## Instalasi Project

1. **Clone Repository**
   (Asumsi Anda sudah memiliki source code project ini)
   ```bash
   cd /path/to/NetMonitor
   ```

2. **Buat Virtual Environment**
   Disarankan menggunakan virtual environment agar paket Python tidak konflik dengan sistem.
   ```bash
   python -m venv venv
   source venv/bin/activate
   ```

3. **Install Dependencies Python**
   ```bash
   pip install --upgrade pip
   pip install -r requirements.txt
   ```
   *Catatan: Jika terjadi error saat install `mysqlclient` atau `cryptography`, pastikan `base-devel` sudah terinstall.*

## Konfigurasi Environment

Salin file contoh konfigurasi menjadi `.env`:

```bash
cp .env.example .env
```

Edit file `.env` sesuai kebutuhan Anda. Pastikan konfigurasi database sesuai dengan yang Anda setup tadi.

```ini
# Contoh konfigurasi DB di .env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=netmonitor
DB_USER=root
DB_PASSWORD=
# Isi DB_PASSWORD sesuai password root MariaDB Anda, kosongkan jika tidak ada.
```

## Inisialisasi Database

Jalankan script migrasi untuk membuat tabel-tabel yang diperlukan:

1. **Buat Database**
   ```bash
   # Masuk ke shell MySQL via LAMPP binary
   /opt/lampp/bin/mysql -u root
   ```
   ```sql
   -- Di dalam shell MariaDB:
   CREATE DATABASE netmonitor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   EXIT;
   ```

2. **Jalankan Migrasi Alembic**
   Pastikan venv aktif.
   ```bash
   alembic upgrade head
   ```

## Menjalankan Aplikasi

Anda dapat menjalankan layanan secara terpisah atau menggunakan script helper.

### Cara Cepat (Recommended)

Gunakan script `start.sh` yang sudah disediakan:

```bash
chmod +x start.sh
./start.sh
```

Script ini akan menjalankan Backend (FastAPI), Frontend (Streamlit), dan Monitoring Script secara bersamaan.

### Cara Manual

Jika ingin menjalankan satu per satu terminal:

1. **Backend (FastAPI)**
   ```bash
   uvicorn app.main:app --reload --port 8001
   ```

2. **Frontend (Streamlit)**
   ```bash
   streamlit run streamlit_app/app.py
   ```
   Akses via browser di `http://localhost:8501`

3. **Monitoring Script** (Background process)
   sudo privileges mungkin diperlukan untuk `ping` atau `nmap`.
   ```bash
   sudo ./venv/bin/python scripts/monitor.py
   ```

## Troubleshooting Umum di Arch

- **Error `externally-managed-environment` saat pip install:**
  Pastikan Anda sudah mengaktifkan virtual environment (`source venv/bin/activate`). Arch Linux memblokir pip install di level sistem secara default.

- **MySQL Connection Error:**
  Pastikan service LAMPP berjalan: `sudo /opt/lampp/lampp status`.

- **Library nmap tidak ditemukan:**
  Pastikan paket `nmap` terinstall via pacman, bukan pip (pip hanya wrapper python-nya).
