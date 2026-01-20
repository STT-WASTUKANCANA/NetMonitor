# 📁 Struktur Proyek - NetMonitor

Dokumentasi struktur direktori dan file untuk **NetMonitor** dengan Streamlit + FastAPI.

---

## 📂 Struktur Utama

```
NetMonitor/
├── app/                        # FastAPI Backend
├── streamlit_app/              # Streamlit Frontend
├── scripts/                    # Python Monitoring Scripts
├── documentation/              # Dokumentasi
├── tests/                      # Unit & Integration Tests
├── requirements.txt            # Python Dependencies
├── .env.example                # Environment Template
├── .gitignore                  # Git Ignore Rules
└── README.md                   # Quick Start Guide
```

---

## 📂 FastAPI Backend (`app/`)

```
app/
├── __init__.py                 # Package init
├── main.py                     # FastAPI entry point
├── config.py                   # Application settings
├── database.py                 # SQLAlchemy connection
│
├── models/                     # SQLAlchemy ORM Models
│   ├── __init__.py             # Model exports
│   ├── user.py                 # User model
│   ├── device.py               # Device model
│   ├── device_log.py           # DeviceLog model
│   └── alert.py                # Alert model
│
├── schemas/                    # Pydantic Schemas
│   ├── __init__.py             # Schema exports
│   ├── user.py                 # User schemas (Login, Token, etc)
│   ├── device.py               # Device schemas (CRUD, status)
│   ├── device_log.py           # DeviceLog schemas
│   └── alert.py                # Alert schemas (bulk update)
│
├── routers/                    # API Endpoints
│   ├── __init__.py             # Router exports
│   ├── auth.py                 # /api/auth/* endpoints
│   ├── devices.py              # /api/devices/* endpoints
│   ├── alerts.py               # /api/alerts/* endpoints
│   └── dashboard.py            # /api/dashboard/* endpoints
│
├── middleware/                 # Custom Middleware
│   ├── __init__.py
│   └── auth.py                 # JWT authentication
│
└── services/                   # Business Logic (future)
    └── __init__.py
```

### File Descriptions

| File | Purpose |
|------|---------|
| `main.py` | FastAPI app initialization, routers, middleware |
| `config.py` | Environment variables loading dengan Pydantic Settings |
| `database.py` | SQLAlchemy engine, session factory, DB utilities |
| `models/*.py` | ORM models dengan relationships dan helper methods |
| `schemas/*.py` | Request/response validation dengan Pydantic |
| `routers/*.py` | API endpoint handlers |
| `middleware/auth.py` | JWT token creation dan verification |

---

## 📂 Streamlit Frontend (`streamlit_app/`)

```
streamlit_app/
├── __init__.py                 # Package init
├── app.py                      # Main entry point
├── config.py                   # Streamlit configuration
│
├── pages/                      # Multi-page App Pages
│   ├── 1_🏠_Dashboard.py       # Dashboard page
│   ├── 2_📡_Devices.py         # Device management
│   ├── 3_🔔_Alerts.py          # Alert management
│   └── 4_📊_Monitoring.py      # Real-time monitoring
│
├── components/                 # Reusable UI Components
│   ├── __init__.py             # Charts, cards, badges
│   └── monitoring.py           # Monitoring-specific components
│
├── utils/                      # Utilities
│   ├── __init__.py
│   ├── api_client.py           # HTTP client for FastAPI
│   └── session.py              # Auth session management
│
└── assets/                     # Static assets (images, etc)
```

### File Descriptions

| File | Purpose |
|------|---------|
| `app.py` | Main entry, sidebar, welcome page |
| `config.py` | API URLs, refresh intervals, colors |
| `pages/*.py` | Individual page implementations |
| `components/__init__.py` | Plotly charts, metric cards, status badges |
| `utils/api_client.py` | Wrapper untuk HTTP requests ke FastAPI |
| `utils/session.py` | Authentication state management |

---

## 📂 Monitoring Scripts (`scripts/`)

```
scripts/
├── monitor.py                  # Main monitoring script
├── requirements.txt            # Script dependencies
├── .env.scripts.example        # Environment template
└── monitor.log                 # Log file (generated)
```

### File Descriptions

| File | Purpose |
|------|---------|
| `monitor.py` | Ping devices, check ports, update status via API |
| `requirements.txt` | ping3, requests, python-dotenv |
| `.env.scripts` | API credentials, intervals, timeout config |

---

## 📂 Documentation (`documentation/`)

```
documentation/
├── README.md                   # Documentation index
├── CHANGELOG.md                # Version history
├── app_summary.md              # Feature overview
├── arsitektur-sistem.md        # Architecture docs
├── database-schema.md          # Database structure
│
├── panduan/                    # Developer Guides
│   ├── api-documentation.md    # API reference
│   ├── struktur-proyek.md      # This file
│   └── konvensi-kode.md        # Coding standards
│
├── pengguna/                   # User Guides
│   └── dashboard.md            # Dashboard usage
│
├── setup/                      # Installation Guides
│   ├── linux.md
│   ├── macos.md
│   ├── windows.md
│   └── environment-variables.md
│
├── administrasi/               # Admin Guides
│   └── monitoring-script.md    # Monitoring setup
│
└── troubleshooting/            # Problem Solving
    ├── faq.md
    └── masalah-umum.md
```

---

## 📂 Tests (`tests/`)

```
tests/
├── __init__.py
├── conftest.py                 # Pytest fixtures
├── test_api/                   # API tests
│   ├── test_auth.py
│   ├── test_devices.py
│   ├── test_alerts.py
│   └── test_dashboard.py
└── test_models/                # Model tests
    ├── test_user.py
    └── test_device.py
```

---

## 📄 Root Files

| File | Purpose |
|------|---------|
| `requirements.txt` | Main Python dependencies |
| `.env.example` | Environment variable template |
| `.env` | Actual environment variables (git ignored) |
| `README.md` | Quick start guide |
| `.gitignore` | Git ignore patterns |

---

## 🔧 Running the Application

### Start FastAPI Backend
```bash
cd /path/to/NetMonitor
source venv/bin/activate
uvicorn app.main:app --reload --port 8001
```

### Start Streamlit Frontend
```bash
cd /path/to/NetMonitor/streamlit_app
streamlit run app.py --server.port 8501
```

### Run Monitoring Script
```bash
cd /path/to/NetMonitor/scripts
python monitor.py
```

---

**Versi**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
