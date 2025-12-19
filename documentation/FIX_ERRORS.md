# 🔧 Panduan Memperbaiki Error NetMonitor

## Error yang Ditemukan:
1. ❌ **Database service is currently unavailable**
2. ⏳ **Account locked due to multiple failed login attempts**

---

## 🗄️ Solusi 1: Memperbaiki Database Connection

### Penyebab:
MariaDB menolak akses untuk user 'root' dengan password kosong.

### Langkah Perbaikan:

#### Opsi A: Buat User Database Baru (Recommended)

```bash
# 1. Masuk ke MySQL sebagai root
sudo mysql -u root

# 2. Jalankan SQL berikut:
CREATE DATABASE IF NOT EXISTS netmonitor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'netmonitor'@'localhost' IDENTIFIED BY 'netmonitor123';
GRANT ALL PRIVILEGES ON netmonitor.* TO 'netmonitor'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# 3. Update file .env
nano .env
# Ubah:
DB_USER=netmonitor
DB_PASSWORD=netmonitor123
```

#### Opsi B: Reset Password Root MySQL

```bash
# 1. Stop MySQL
sudo systemctl stop mariadb

# 2. Start MySQL tanpa password check
sudo mysqld_safe --skip-grant-tables &

# 3. Dalam terminal baru:
mysql -u root

# 4. Reset password:
USE mysql;
UPDATE user SET password=PASSWORD('') WHERE User='root';
FLUSH PRIVILEGES;
EXIT;

# 5. Restart MySQL normal
sudo killall mysqld
sudo systemctl start mariadb
```

#### Opsi C: Gunakan Unix Socket Authentication

```bash
# Edit .env dan ubah connection string:
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
```

---

## 🔐 Solusi 2: Reset Login Attempts

### Cara 1: Hapus Cache Streamlit

```bash
# Jalankan script reset yang sudah dibuat:
python3 reset_login_attempts.py

# Atau manual:
rm -rf ~/.streamlit/cache
rm -rf .streamlit/cache
find . -type d -name "__pycache__" -exec rm -rf {} + 2>/dev/null
```

### Cara 2: Reset Lewat Browser

1. Buka Developer Tools (F12)
2. Buka tab **Console**
3. Jalankan:
```javascript
localStorage.clear();
sessionStorage.clear();
location.reload();
```

### Cara 3: Clear Browser Data
1. Tekan `Ctrl + Shift + Delete`
2. Pilih "Cached images and files"
3. Pilih "Cookies and other site data"
4. Clear data untuk localhost

---

## 🚀 Langkah-Langkah Lengkap (Recommended)

### 1. Fix Database (Pilih salah satu):

**Cara Cepat - Buat User Baru:**
```bash
sudo mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS netmonitor;
CREATE USER IF NOT EXISTS 'netmonitor'@'localhost' IDENTIFIED BY 'netmonitor123';
GRANT ALL PRIVILEGES ON netmonitor.* TO 'netmonitor'@'localhost';
FLUSH PRIVILEGES;
EOF
```

### 2. Update .env File:
```bash
# Edit .env
nano .env
```

Ubah bagian database:
```env
DB_USER=netmonitor
DB_PASSWORD=netmonitor123
```

### 3. Reset Login Attempts:
```bash
# Gunakan script yang sudah dibuat
python3 reset_login_attempts.py

# Atau manual
rm -rf ~/.streamlit/cache .streamlit/cache
```

### 4. Test Database Connection:
```bash
# Test koneksi database
mysql -u netmonitor -pnetmonitor123 -e "SELECT 'Connection OK' as Status;"

# Test dari Python
python3 -c "from app.database import check_database_connection; print('DB:', 'OK' if check_database_connection() else 'FAIL')"
```

### 5. Jalankan Migrations:
```bash
# Jalankan migrasi database
./venv/bin/alembic upgrade head
```

### 6. Restart Aplikasi:
```bash
# Stop aplikasi yang sedang berjalan
pkill -f "streamlit run"
pkill -f "uvicorn"

# Start ulang
./start.sh
```

---

## ✅ Verifikasi

### Check 1: Database Connection
```bash
mysql -u netmonitor -pnetmonitor123 -D netmonitor -e "SHOW TABLES;"
```

### Check 2: API Backend
```bash
curl http://localhost:8001/api/auth/login -X POST \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@netmonitor.com","password":"admin123"}'
```

### Check 3: Streamlit
```bash
# Buka browser ke:
http://localhost:8501
```

---

## 🆘 Troubleshooting Tambahan

### Error: "Access denied for user"
```bash
# Cek user yang ada
sudo mysql -u root -e "SELECT User, Host FROM mysql.user;"

# Hapus dan buat ulang user
sudo mysql -u root -e "DROP USER IF EXISTS 'netmonitor'@'localhost'; CREATE USER 'netmonitor'@'localhost' IDENTIFIED BY 'netmonitor123'; GRANT ALL PRIVILEGES ON netmonitor.* TO 'netmonitor'@'localhost'; FLUSH PRIVILEGES;"
```

### Error: "Can't connect to MySQL server"
```bash
# Check service status
sudo systemctl status mariadb

# Start jika tidak berjalan
sudo systemctl start mariadb

# Enable auto-start
sudo systemctl enable mariadb
```

### Login Attempts Masih Locked
```bash
# Clear semua cache
rm -rf ~/.streamlit
rm -rf ~/.cache/streamlit
find . -type d -name ".streamlit" -exec rm -rf {} + 2>/dev/null

# Clear browser localStorage (F12 -> Console):
localStorage.removeItem('netmonitor_token');
localStorage.removeItem('netmonitor_user');
localStorage.clear();
```

---

## 📞 Kontak Bantuan

Jika masih ada masalah:
1. Check log file: `tail -f logs/netmonitor.log`
2. Check MariaDB log: `sudo tail -f /var/log/mysql/error.log`
3. Run dengan debug mode: `DEBUG=true ./start.sh`

---

**Setelah semua langkah di atas, error seharusnya sudah teratasi! ✨**
