# ❓ FAQ - NetMonitor

Pertanyaan yang sering ditanyakan tentang **NetMonitor**.

---

## 🔐 Authentication

### Q: Bagaimana cara login?
**A:** Akses http://localhost:8501, masukkan email dan password user yang terdaftar di database.

### Q: Token expired, apa yang harus dilakukan?
**A:** Token JWT berlaku 30 menit secara default. Logout dan login kembali untuk mendapatkan token baru.

### Q: Lupa password?
**A:** Untuk saat ini, reset password harus dilakukan langsung di database. Update kolom `password` di tabel `users` dengan hash bcrypt baru.

---

## 🖥️ Frontend (Streamlit)

### Q: Dashboard tidak muncul / error?
**A:** 
1. Pastikan FastAPI backend berjalan di port 8001
2. Check `API_BASE_URL` di `.env` atau `streamlit_app/config.py`
3. Lihat console browser untuk error

### Q: Auto-refresh terlalu cepat/lambat?
**A:** Edit interval di halaman Monitoring (sidebar settings) atau ubah di `streamlit_app/config.py`:
```python
DASHBOARD_REFRESH = 5  # seconds
```

### Q: Charts tidak muncul?
**A:** Pastikan ada data di database. Charts membutuhkan data dari `device_logs`.

---

## 🔧 Backend (FastAPI)

### Q: API tidak bisa diakses?
**A:**
1. Check apakah uvicorn berjalan: `ps aux | grep uvicorn`
2. Test health: `curl http://localhost:8001/health`
3. Check firewall settings

### Q: Database connection error?
**A:** Verify credentials di `.env`:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=netmonitor
DB_USER=root
DB_PASSWORD=your_password
```

### Q: CORS error di browser?
**A:** Sudah di-handle di `app/main.py`. Jika masih error, tambahkan origin spesifik:
```python
allow_origins=["http://localhost:8501"]
```

---

## 📡 Monitoring Script

### Q: Permission denied saat ping?
**A:** ICMP ping membutuhkan root:
```bash
sudo python scripts/monitor.py
```

### Q: Script tidak update status?
**A:** 
1. Check credentials di `scripts/.env.scripts`
2. Verify API URL accessible

### Q: Bagaimana setup cron job?
**A:**
```bash
sudo crontab -e
# Add: */5 * * * * /path/to/venv/bin/python /path/to/scripts/monitor.py
```

---

## 📊 Data & Database

### Q: Bagaimana backup database?
**A:**
```bash
mysqldump -u root -p netmonitor > backup_$(date +%Y%m%d).sql
```

### Q: Cara clear old logs?
**A:** Via SQL:
```sql
DELETE FROM device_logs WHERE checked_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Q: Migrasi dari Laravel?
**A:** Database schema sama, tidak perlu migrasi. Hanya password harus menggunakan bcrypt hash yang kompatibel.

---

## 🚀 Deployment

### Q: Bagaimana deploy ke production?
**A:**
1. Set `DEBUG=false` dan `APP_ENV=production`
2. Generate strong secret keys
3. Setup systemd services
4. Configure nginx reverse proxy
5. Enable HTTPS

### Q: Recommended server specs?
**A:** Minimum:
- 2 CPU cores
- 4GB RAM
- 20GB storage

---

**Versi**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
