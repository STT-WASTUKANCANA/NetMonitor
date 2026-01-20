# 📚 NetMonitor Documentation

Dokumentasi lengkap untuk sistem **NetMonitor** - Platform monitoring jaringan berbasis **Streamlit + FastAPI**.

## 🆕 Versi 2.0 - Streamlit Edition

NetMonitor telah dimitigrasikan dari Laravel (PHP) ke full-stack Python:
- **Backend**: FastAPI dengan SQLAlchemy ORM
- **Frontend**: Streamlit dengan Plotly Charts
- **Database**: MySQL/MariaDB (kompatibel dengan schema existing)

---

## 📖 Daftar Dokumentasi

### 🚀 Quick Start
- [README Utama](../README.md) - Quick start guide

### 📋 Overview
- [App Summary](app_summary.md) - Ringkasan fitur aplikasi
- [Arsitektur Sistem](arsitektur-sistem.md) - Arsitektur teknis
- [Database Schema](database-schema.md) - Struktur database
- [CHANGELOG](CHANGELOG.md) - Riwayat perubahan

### 🛠️ Setup & Installation
- [Linux Setup](setup/linux.md) - Instalasi di Linux
- [macOS Setup](setup/macos.md) - Instalasi di macOS
- [Windows Setup](setup/windows.md) - Instalasi di Windows
- [Environment Variables](setup/environment-variables.md) - Konfigurasi

### 📖 Panduan Developer
- [API Documentation](panduan/api-documentation.md) - REST API reference
- [Struktur Proyek](panduan/struktur-proyek.md) - Project structure
- [Konvensi Kode](panduan/konvensi-kode.md) - Coding standards

### 👤 Panduan Pengguna
- [Dashboard](pengguna/dashboard.md) - Menggunakan dashboard

### ⚙️ Administrasi
- [Monitoring Script](administrasi/monitoring-script.md) - Konfigurasi monitoring

### 🔧 Troubleshooting
- [FAQ](troubleshooting/faq.md) - Pertanyaan umum
- [Masalah Umum](troubleshooting/masalah-umum.md) - Solusi error

---

## 🏗️ Tech Stack

| Layer | Technology |
|-------|------------|
| Frontend | Streamlit 1.29+ |
| Backend | FastAPI 0.104+ |
| Database | MySQL 8.0 / MariaDB 10.6 |
| ORM | SQLAlchemy 2.0 |
| Charts | Plotly 5.18+ |
| Auth | JWT (python-jose) |
| Monitoring | Python (ping3) |

---

## 🔗 Quick Links

- **Frontend**: http://localhost:8501
- **API Docs**: http://localhost:8001/docs
- **ReDoc**: http://localhost:8001/redoc

---

**Versi Dokumentasi**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
