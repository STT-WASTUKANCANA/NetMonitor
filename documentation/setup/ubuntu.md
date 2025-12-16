# Panduan Setup NetMonitor di Ubuntu Linux

Panduan lengkap instalasi dan konfigurasi NetMonitor di sistem operasi Ubuntu Linux untuk lingkungan lokal (development).

## 📋 Daftar Isi

- [Persyaratan Sistem](#persyaratan-sistem)
- [Persiapan Lingkungan](#persiapan-lingkungan)
- [Instalasi Database](#instalasi-database)
- [Setup Aplikasi](#setup-aplikasi)
- [Konfigurasi](#konfigurasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Setup Monitoring Script](#setup-monitoring-script)
- [Troubleshooting](#troubleshooting)

---

## 📦 Persyaratan Sistem

### Hardware Minimum
- **CPU**: 2 Core
- **RAM**: 4 GB
- **Storage**: 10 GB ruang kosong
- **Network**: Koneksi internet untuk download dependencies

### Software
- **OS**: Ubuntu 20.04 LTS atau lebih baru (22.04 LTS direkomendasikan)
- **Python**: 3.10 atau lebih baru
- **Database**: MySQL 8.0 atau MariaDB 10.6+
- **Git**: Untuk clone repository (opsional)

---

## 🔧 Persiapan Lingkungan

### 1. Update Sistem

```bash
sudo apt update
sudo apt upgrade -y
```

### 2. Install Python 3.10+

Ubuntu 22.04 sudah include Python 3.10. Untuk versi lebih lama:

```bash
# Cek versi Python saat ini
python3 --version

# Jika < 3.10, install dari deadsnakes PPA
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:deadsnakes/ppa -y
sudo apt update
sudo apt install python3.10 python3.10-venv python3.10-dev -y
```

### 3. Install Dependencies Sistem

```bash
sudo apt install -y \
    build-essential \
    libssl-dev \
    libffi-dev \
    python3-pip \
    python3-venv \
    git \
    curl \
    wget \
    iputils-ping \
    net-tools
```

### 4. Install MySQL/MariaDB

#### Opsi A: MySQL 8.0

```bash
sudo apt install mysql-server -y

# Jalankan secure installation
sudo mysql_secure_installation
```

#### Opsi B: MariaDB (Alternatif)

```bash
sudo apt install mariadb-server -y

# Jalankan secure installation
sudo mysql_secure_installation
```

**Catatan**: Ikuti prompt untuk:
- Set root password
- Remove anonymous users (Y)
- Disallow root login remotely (Y)
- Remove test database (Y)
- Reload privilege tables (Y)

---

## 💾 Instalasi Database

### 1. Login ke MySQL

```bash
sudo mysql -u root -p
```

### 2. Buat Database dan User

```sql
-- Buat database
CREATE DATABASE netmonitor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Buat user untuk aplikasi
CREATE USER 'netmonitor_user'@'localhost' IDENTIFIED BY 'password_anda_yang_kuat';

-- Berikan privileges
GRANT ALL PRIVILEGES ON netmonitor.* TO 'netmonitor_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Keluar
EXIT;
```

### 3. Verifikasi Koneksi

```bash
mysql -u netmonitor_user -p netmonitor
# Masukkan password, jika berhasil login berarti sudah OK
```

### 4. Import Schema Database

Jika sudah ada database dari Laravel sebelumnya, skip langkah ini. Jika belum:

```bash
# Jika ada file dump SQL
mysql -u netmonitor_user -p netmonitor < /path/to/database_dump.sql

# Atau jalankan migration Alembic
cd /path/to/NetMonitor
source venv/bin/activate
alembic upgrade head
```

---

## 🚀 Setup Aplikasi

### 1. Clone atau Pindah ke Direktori Project

```bash
# Jika belum ada, clone dari repository
git clone <repository-url> /opt/NetMonitor
cd /opt/NetMonitor

# Atau jika sudah ada
cd /media/boba/DATA/Project/Streamlit/NetMonitor
```

### 2. Buat Virtual Environment

```bash
# Buat virtual environment
python3.10 -m venv venv

# Aktifkan virtual environment
source venv/bin/activate

# Pastikan pip terbaru
pip install --upgrade pip
```

**Tips**: Untuk keluar dari venv, ketik `deactivate`

### 3. Install Dependencies Python

```bash
# Install semua dependencies
pip install -r requirements.txt

# Jika ada error, install satu per satu
# pip install fastapi uvicorn sqlalchemy pymysql...
```

**Troubleshooting**: Jika muncul error compiling packages:
```bash
# Install development headers
sudo apt install python3.10-dev libmysqlclient-dev -y
pip install -r requirements.txt
```

### 4. Setup Environment Variables

```bash
# Copy template .env
cp .env.example .env

# Edit file .env
nano .env
# atau
vim .env
```

---

## ⚙️ Konfigurasi

### File `.env` - Aplikasi Utama

Edit file `.env` di root project:

```env
# === APPLICATION ===
APP_NAME=NetMonitor
APP_ENV=development
DEBUG=true

# === FASTAPI ===
API_HOST=0.0.0.0
API_PORT=8001
SECRET_KEY=ganti-dengan-secret-key-yang-kuat-minimal-32-karakter

# === DATABASE ===
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=netmonitor
DB_USER=netmonitor_user
DB_PASSWORD=password_anda_yang_kuat

# === JWT AUTHENTICATION ===
JWT_SECRET_KEY=ganti-dengan-jwt-secret-yang-berbeda-dan-kuat
JWT_ALGORITHM=HS256
JWT_ACCESS_TOKEN_EXPIRE_MINUTES=30

# === STREAMLIT ===
STREAMLIT_PORT=8501
API_BASE_URL=http://localhost:8001

# === CORS ===
CORS_ORIGINS=["http://localhost:8501","http://127.0.0.1:8501"]
```

**Generate Secret Keys:**
```bash
# Generate random secret key
python3 -c "import secrets; print(secrets.token_urlsafe(32))"
```

### File `.env` - Monitoring Script

```bash
cd scripts
cp .env.example .env
nano .env
```

Edit:
```env
API_BASE_URL=http://localhost:8001
API_EMAIL=admin@wastukancana.ac.id
API_PASSWORD=password_admin_anda
MONITOR_INTERVAL=30
PING_TIMEOUT=10
PING_COUNT=3
```

### Verifikasi Konfigurasi

```bash
# Test koneksi database
python3 << EOF
from app.database import check_database_connection
print("Database OK!" if check_database_connection() else "Database Error!")
EOF
```

---

## 🏃 Menjalankan Aplikasi

### Method 1: Manual (Untuk Development)

#### Terminal 1 - FastAPI Backend

```bash
cd /path/to/NetMonitor
source venv/bin/activate
uvicorn app.main:app --reload --host 0.0.0.0 --port 8001
```

Output sukses:
```
INFO:     Uvicorn running on http://0.0.0.0:8001 (Press CTRL+C to quit)
INFO:     Started reloader process
INFO:     Started server process
INFO:     Waiting for application startup.
INFO:     Application startup complete.
```

#### Terminal 2 - Streamlit Frontend

```bash
cd /path/to/NetMonitor
source venv/bin/activate
cd streamlit_app
streamlit run app.py --server.port 8501
```

Output sukses:
```
You can now view your Streamlit app in your browser.

  Local URL: http://localhost:8501
  Network URL: http://192.168.x.x:8501
```

### Method 2: Menggunakan Script Otomatis

```bash
# Berikan permission execute
chmod +x start.sh

# Jalankan
./start.sh
```

### Akses Aplikasi

- **Frontend (Streamlit)**: http://localhost:8501
- **API Documentation**: http://localhost:8001/docs
- **API ReDoc**: http://localhost:8001/redoc
- **Health Check**: http://localhost:8001/health

### Login Default

- **Email**: admin@wastukancana.ac.id
- **Password**: (sesuai yang ada di database)

> **Catatan**: Jika belum ada user admin, buat dengan script `scripts/create_admin.py`

---

## 🔍 Setup Monitoring Script

Monitoring script dijalankan secara terpisah untuk ping devices.

### 1. Setup Script

```bash
cd scripts

# Pastikan .env sudah dikonfigurasi (lihat bagian Konfigurasi)
# Install dependencies (jika belum)
pip install -r requirements.txt
```

### 2. Test Manual Run

```bash
# Jalankan sekali (butuh sudo untuk ping)
sudo ../venv/bin/python monitor.py
```

Output:
```
🚀 Starting NetMonitor Real-time Monitoring Service...
📡 API URL: http://localhost:8001
⏱️ Check interval: 30 seconds (REAL-TIME)
✅ Login successful as admin@wastukancana.ac.id
🔍 Checking Device-A (192.168.1.1)...
  ✅ UP - Response: 5.23ms, Loss: 0.0%
```

### 3. Setup Systemd Service (Auto-start)

Buat file service:

```bash
sudo nano /etc/systemd/system/netmonitor-script.service
```

Isi:
```ini
[Unit]
Description=NetMonitor Monitoring Script
After=network.target netmonitor-api.service

[Service]
Type=simple
User=root
WorkingDirectory=/path/to/NetMonitor/scripts
Environment="PATH=/path/to/NetMonitor/venv/bin"
ExecStart=/path/to/NetMonitor/venv/bin/python monitor.py
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Aktifkan:
```bash
sudo systemctl daemon-reload
sudo systemctl enable netmonitor-script
sudo systemctl start netmonitor-script

# Cek status
sudo systemctl status netmonitor-script

# Lihat logs
sudo journalctl -u netmonitor-script -f
```

### 4. Setup Cron Job (Alternatif - Run Periodic)

```bash
# Edit crontab
sudo crontab -e

# Jalankan setiap 5 menit
*/5 * * * * cd /path/to/NetMonitor/scripts && /path/to/NetMonitor/venv/bin/python monitor.py >> /var/log/netmonitor.log 2>&1
```

---

## 🛠️ Troubleshooting

### Problem: Port sudah digunakan

```bash
# Cek port 8001
sudo lsof -i :8001
# Kill process
sudo kill -9 <PID>

# Cek port 8501
sudo lsof -i :8501
```

### Problem: Database connection error

```bash
# Cek status MySQL
sudo systemctl status mysql

# Restart MySQL
sudo systemctl restart mysql

# Cek user dan privileges
mysql -u root -p
SHOW GRANTS FOR 'netmonitor_user'@'localhost';
```

### Problem: Module not found

```bash
# Pastikan venv aktif
source venv/bin/activate

# Reinstall dependencies
pip install -r requirements.txt
```

### Problem: Permission denied saat ping

```bash
# Berikan capabilities ke Python (lebih aman dari sudo)
sudo setcap cap_net_raw+ep venv/bin/python3

# Atau jalankan dengan sudo
sudo venv/bin/python scripts/monitor.py
```

### Problem: Streamlit tidak bisa akses API

- Pastikan `API_BASE_URL` di `.env` benar
- Cek firewall: `sudo ufw status`
- Test API: `curl http://localhost:8001/health`

### Logs dan Debugging

```bash
# Lihat log monitoring
tail -f scripts/monitor.log

# Lihat systemd logs
sudo journalctl -u netmonitor-api -f
sudo journalctl -u netmonitor-script -f

# Enable debug mode di .env
DEBUG=true
```

---

## 📚 Referensi Lebih Lanjut

- [Environment Variables](environment-variables.md)
- [Database Setup](database.md)
- [Migration Guide](MIGRATION_GUIDE.md)
- [Troubleshooting Guide](../troubleshooting/)

---

## ✅ Checklist Setup Lengkap

- [ ] Ubuntu updated
- [ ] Python 3.10+ terinstall
- [ ] MySQL/MariaDB terinstall dan running
- [ ] Database `netmonitor` dibuat
- [ ] Virtual environment dibuat
- [ ] Dependencies terinstall
- [ ] File `.env` dikonfigurasi
- [ ] Database connection terverifikasi
- [ ] FastAPI backend berjalan di port 8001
- [ ] Streamlit frontend berjalan di port 8501
- [ ] Bisa login ke aplikasi
- [ ] Monitoring script berjalan (opsional)
- [ ] Systemd service dikonfigurasi (untuk production)

---

**Selamat!** NetMonitor sudah siap digunakan di Ubuntu Linux. 🎉

Untuk pertanyaan atau masalah, silakan cek dokumentasi troubleshooting atau contact administrator.
