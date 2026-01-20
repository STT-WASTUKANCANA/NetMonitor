# 📝 Changelog - NetMonitor

Semua perubahan penting pada NetMonitor didokumentasikan di sini.

---

## [2.0.0] - 2025-12-11 - Streamlit Edition 🚀

### 🔄 Migration
- **BREAKING**: Migrasi penuh dari Laravel ke Streamlit + FastAPI
- Backend diganti dari Laravel (PHP) ke **FastAPI (Python)**
- Frontend diganti dari Blade + Livewire ke **Streamlit**
- Database tetap kompatibel (MySQL/MariaDB tanpa perubahan schema)

### ✨ Added (Backend)
- FastAPI REST API dengan 13 endpoints lengkap
- SQLAlchemy ORM models untuk semua tables
- JWT authentication dengan python-jose
- Pydantic schemas untuk validasi
- CORS middleware untuk cross-origin requests
- Health check endpoints

### ✨ Added (Frontend)
- Streamlit multi-page application
- Dashboard dengan Plotly charts interaktif
- Device management dengan CRUD operations
- Alert management dengan bulk actions
- Real-time monitoring dashboard
- Auto-refresh mechanism
- Dark theme UI

### ✨ Added (Monitoring)
- Updated `monitor.py` dengan JWT auth
- Hierarchical device checking
- Improved logging

### ✨ Added (Documentation)
- README.md baru untuk Python stack
- Semua dokumentasi diupdate untuk Streamlit+FastAPI

### 🗑️ Removed
- Laravel PHP backend
- Blade templates
- Livewire components
- Alpine.js frontend
- TailwindCSS (diganti Streamlit CSS)

---

## [1.0.0] - 2025-11-XX - Laravel Edition

### Initial Release
- Laravel 12 backend
- Blade + Livewire + Alpine.js frontend
- TailwindCSS 4.0 styling
- MySQL database
- Python monitoring script
- PDF report generation

---

## Migration Guide

### Dari Laravel ke Streamlit

1. **Backup database** (optional, schema sama)
2. **Install Python 3.10+**
3. **Install dependencies**: `pip install -r requirements.txt`
4. **Configure .env** dengan database credentials
5. **Start FastAPI**: `uvicorn app.main:app --port 8001`
6. **Start Streamlit**: `streamlit run streamlit_app/app.py`

### Breaking Changes
- API endpoints berubah prefix (dari `/api/v1` ke `/api`)
- Auth menggunakan JWT (bukan Sanctum tokens)
- Password hash tetap bcrypt (kompatibel)

---

**Maintained by**: STT Wastukancana  
**Format**: [Keep a Changelog](https://keepachangelog.com)
