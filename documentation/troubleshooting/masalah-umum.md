# 🔧 Masalah Umum - NetMonitor

Solusi untuk masalah yang sering ditemui saat menggunakan **NetMonitor**.

---

## ❌ Error: "Could not connect to API server"

### Gejala
Dashboard menampilkan error koneksi ke API.

### Penyebab
- FastAPI tidak berjalan
- Port 8001 terblokir
- URL API salah

### Solusi
```bash
# 1. Check if FastAPI is running
curl http://localhost:8001/health

# 2. Start FastAPI
cd /path/to/NetMonitor
source venv/bin/activate
uvicorn app.main:app --port 8001

# 3. Verify API_BASE_URL in .env
API_BASE_URL=http://localhost:8001
```

---

## ❌ Error: "Invalid credentials"

### Gejala
Login gagal dengan pesan "Invalid credentials"

### Penyebab
- Email tidak terdaftar
- Password salah
- Hash password tidak kompatibel

### Solusi
```python
# Verify user exists in database
# Connect to MySQL and check:
SELECT * FROM users WHERE email = 'admin@netmonitor.local';

# Reset password if needed (run in Python):
from passlib.context import CryptContext
pwd = CryptContext(schemes=["bcrypt"])
print(pwd.hash("new_password"))
# Update in database with the hash
```

---

## ❌ Error: "Database connection failed"

### Gejala
API return 500 error, log shows database error

### Penyebab
- MySQL tidak berjalan
- Credentials salah
- Database tidak exist

### Solusi
```bash
# 1. Check MySQL status
sudo systemctl status mysql

# 2. Test connection
mysql -h 127.0.0.1 -u root -p netmonitor

# 3. Verify .env settings
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=netmonitor
DB_USER=root
DB_PASSWORD=your_password
```

---

## ❌ Error: "Permission denied" (monitoring script)

### Gejala
`monitor.py` gagal dengan permission error saat ping

### Penyebab
ICMP ping membutuhkan root privileges

### Solusi
```bash
# Option 1: Run as root
sudo python scripts/monitor.py

# Option 2: Set capability (Linux)
sudo setcap cap_net_raw+ep /path/to/venv/bin/python

# Option 3: Use TCP ping instead (less accurate)
# Edit monitor.py to use socket connection instead of ICMP
```

---

## ❌ Charts tidak menampilkan data

### Gejala
Dashboard charts kosong atau "No data available"

### Penyebab
- Belum ada data di `device_logs`
- Filter periode terlalu sempit
- Device belum dicek monitoring script

### Solusi
1. Jalankan monitoring script minimal sekali
2. Check database:
   ```sql
   SELECT COUNT(*) FROM device_logs;
   ```
3. Ubah filter period dari "24h" ke "7d" atau "30d"

---

## ❌ Streamlit blank/white screen

### Gejala
Browser menampilkan halaman putih

### Penyebab
- JavaScript error
- Import error di Python

### Solusi
```bash
# 1. Check Streamlit logs
streamlit run app.py --logger.level=debug

# 2. Check browser console (F12)
# Look for JavaScript errors

# 3. Clear cache
streamlit cache clear
```

---

## ❌ Port already in use

### Gejala
Error "Address already in use" saat start server

### Penyebab
Port 8001 atau 8501 sudah dipakai

### Solusi
```bash
# Find process using port
sudo lsof -i :8001
sudo lsof -i :8501

# Kill the process
kill -9 <PID>

# Or use different port
uvicorn app.main:app --port 8002
streamlit run app.py --server.port 8502
```

---

## ❌ SQLAlchemy OperationalError

### Gejala
```
sqlalchemy.exc.OperationalError: (pymysql.err.OperationalError)
```

### Penyebab
- Connection pool exhausted
- Database timeout
- Too many connections

### Solusi
1. Increase pool size di `app/database.py`:
   ```python
   engine = create_engine(
       settings.database_url,
       pool_size=20,
       max_overflow=30
   )
   ```
2. Check MySQL max_connections:
   ```sql
   SHOW VARIABLES LIKE 'max_connections';
   SET GLOBAL max_connections = 200;
   ```

---

## ❌ Alert tidak muncul saat device down

### Gejala
Device status berubah ke DOWN tapi alert tidak dibuat

### Penyebab
- Bug di monitoring script
- Device sebelumnya sudah DOWN

### Solusi
Alert hanya dibuat saat status **berubah** dari UP ke DOWN. Check logic di `app/routers/devices.py`:
```python
# Alert created only when status changes to down
if old_status != 'down' and new_status == 'down':
    # Create alert
```

---

## 📞 Butuh Bantuan Lebih?

1. Check logs di `monitor.log` atau terminal output
2. Enable debug mode: `DEBUG=true` di `.env`
3. Check API response di Swagger UI: http://localhost:8001/docs

---

**Versi**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
