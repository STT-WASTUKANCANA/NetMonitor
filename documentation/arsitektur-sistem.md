# 🏗️ Arsitektur Sistem NetMonitor

Dokumentasi arsitektur sistem NetMonitor berbasis **Streamlit + FastAPI**.

---

## 📊 Overview Arsitektur

NetMonitor dibangun dengan arsitektur **microservices** sederhana:

```
┌─────────────────────────────────────────────────────────────┐
│                    USER INTERFACE                           │
│               (Browser - Streamlit App)                     │
│                   Port: 8501                                │
└────────────────────┬────────────────────────────────────────┘
                     │ HTTP Requests
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                  FASTAPI BACKEND                            │
│                    Port: 8001                               │
│  ┌────────────┐  ┌────────────┐  ┌──────────────────┐      │
│  │  Routers   │  │  Services  │  │    Middleware    │      │
│  │  - auth    │  │  - device  │  │    - JWT Auth    │      │
│  │  - devices │  │  - alert   │  │    - CORS        │      │
│  │  - alerts  │  │  - monitor │  │                  │      │
│  │  - dashboard│ │            │  │                  │      │
│  └─────┬──────┘  └─────┬──────┘  └──────────────────┘      │
│        │               │                                    │
│        └───────────────┼────────────────────────────────────│
│                        ▼                                    │
│  ┌─────────────────────────────────────────────────┐       │
│  │           SQLAlchemy ORM Models                  │       │
│  │  • User • Device • DeviceLog • Alert            │       │
│  └─────────────┬───────────────────────────────────┘       │
└────────────────┼───────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────┐
│                   DATABASE (MySQL)                          │
│  • users  • devices  • device_logs  • alerts               │
└────────────┬────────────────────────────────────────────────┘
             │
             ▲
             │
┌────────────┴────────────────────────────────────────────────┐
│              PYTHON MONITORING SCRIPT                       │
│  • Ping Devices  • Port Check  • Status Update via API     │
│  • Runs via Cron (every 5 minutes)                         │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧩 Komponen Utama

### 1. Streamlit Frontend

#### Technologies
- **Streamlit 1.29+** - UI framework
- **Plotly 5.18+** - Interactive charts
- **Pandas** - Data manipulation

#### Pages
```
streamlit_app/
├── app.py                     # Main entry point
├── config.py                  # Configuration
├── pages/
│   ├── 1_🏠_Dashboard.py     # Dashboard page
│   ├── 2_📡_Devices.py       # Device management
│   ├── 3_🔔_Alerts.py        # Alert management
│   └── 4_📊_Monitoring.py    # Real-time monitoring
├── components/                # Reusable UI components
└── utils/                     # API client, session
```

---

### 2. FastAPI Backend

#### Technologies
- **FastAPI 0.104+** - Web framework
- **SQLAlchemy 2.0** - ORM
- **Pydantic 2.5+** - Validation
- **python-jose** - JWT authentication
- **PyMySQL** - MySQL driver

#### Structure
```
app/
├── main.py                    # FastAPI entry point
├── config.py                  # Settings
├── database.py                # DB connection
├── models/                    # SQLAlchemy models
│   ├── user.py
│   ├── device.py
│   ├── device_log.py
│   └── alert.py
├── schemas/                   # Pydantic schemas
├── routers/                   # API endpoints
│   ├── auth.py
│   ├── devices.py
│   ├── alerts.py
│   └── dashboard.py
└── middleware/                # JWT auth
```

---

### 3. Database Layer

#### Configuration
- **MySQL 8.0+** atau **MariaDB 10.6+**
- Charset: `utf8mb4`
- Collation: `utf8mb4_unicode_ci`

#### Schema
```
┌──────────┐         ┌─────────────┐         ┌────────────┐
│  users   │         │   devices   │         │device_logs │
├──────────┤         ├─────────────┤         ├────────────┤
│ id       │         │ id          │◄────────│ device_id  │
│ email    │         │ name        │         │ status     │
│ role     │         │ ip_address  │         │ response_time
└──────────┘         │ type        │         │ checked_at │
                     │ hierarchy   │         └────────────┘
                     │ parent_id   │
                     │ status      │         ┌────────────┐
                     └─────────────┘◄────────│   alerts   │
                                             ├────────────┤
                                             │ device_id  │
                                             │ message    │
                                             │ severity   │
                                             │ status     │
                                             └────────────┘
```

---

### 4. Python Monitoring Script

#### Responsibilities
- Ping semua devices menggunakan ICMP
- Check port availability
- Measure response time (latency)
- Send data ke FastAPI via REST API
- Handle hierarchical checking

#### Flow
```
1. Load Configuration (.env.scripts)
   ├── API endpoint
   ├── Credentials
   └── Timeout settings

2. Login to API (JWT)
   └── POST /api/auth/login

3. Fetch Devices
   └── GET /api/devices

4. For Each Device (Hierarchical)
   ├── Check if parent is UP
   ├── Ping device
   ├── Check ports
   └── Calculate latency

5. Send Results
   └── POST /api/devices/status

6. Wait for Interval (default: 5 min)
```

---

## 🔄 Data Flow

### User Request Flow
```
1. User Action (Browser)
   │
   ▼
2. Streamlit App
   │
   ▼
3. API Client (requests)
   │
   ▼
4. FastAPI Router
   │
   ▼
5. Business Logic / Service
   │
   ▼
6. SQLAlchemy Query
   │
   ▼
7. MySQL Database
   │
   ▼
8. Response (JSON)
   │
   ▼
9. Streamlit Render
```

### Monitoring Data Flow
```
1. Cron Triggers monitor.py
   │
   ▼
2. Fetch Devices (API)
   │
   ▼
3. Ping/Check Each Device
   │
   ▼
4. POST Status to API
   │
   ▼
5. FastAPI Process
   │
   ├─→ Update devices table
   ├─→ Insert device_logs
   └─→ Create alerts (if status changed)
   │
   ▼
6. Dashboard Auto-refresh Shows Changes
```

---

## 🔐 Security Architecture

### Authentication Flow
```
User Login (Streamlit)
   │
   ▼
POST /api/auth/login (email, password)
   │
   ▼
Verify Password (bcrypt)
   │
   ▼
Generate JWT Token
   │
   ▼
Return Token to Client
   │
   ▼
Store in Session State
   │
   ▼
Include in All API Requests (Bearer Token)
```

### Security Features
1. **JWT Authentication** - Stateless tokens
2. **Password Hashing** - bcrypt
3. **CORS Middleware** - Cross-origin control
4. **Input Validation** - Pydantic schemas
5. **SQL Injection Prevention** - SQLAlchemy ORM

---

## 📈 Performance Features

### Caching
- Streamlit session state untuk user data
- SQLAlchemy connection pooling

### Auto-refresh
- Dashboard: setiap 5 detik
- Monitoring: configurable (5-30 detik)

### Database Optimization
- Indexed columns: status, device_id, checked_at
- Connection pool: 10 connections, max 20

---

## 📦 Deployment Architecture

### Development
```
Local Machine
   ├─→ FastAPI (uvicorn, port 8001)
   ├─→ Streamlit (port 8501)
   ├─→ MySQL Local
   └─→ monitor.py (manual)
```

### Production
```
┌────────────────────────────────────┐
│        Nginx Reverse Proxy         │
│   (HTTPS, Load Balancing)          │
└──────────────┬─────────────────────┘
               │
     ┌─────────┴─────────┐
     ▼                   ▼
┌──────────────┐  ┌──────────────┐
│  Streamlit   │  │   FastAPI    │
│  (Gunicorn)  │  │  (Uvicorn)   │
│  Port 8501   │  │  Port 8001   │
└──────────────┘  └──────────────┘
               │
               ▼
       ┌──────────────┐
       │    MySQL     │
       │   Database   │
       └──────────────┘
               ▲
               │
┌──────────────────────────────────┐
│    Monitoring Script (Cron)      │
└──────────────────────────────────┘
```

---

## 🛠️ Technology Stack Summary

| Layer | Technology | Purpose |
|-------|------------|---------|
| **Frontend** | Streamlit | UI Framework |
| | Plotly | Charts |
| | Pandas | Data handling |
| **Backend** | FastAPI | REST API |
| | Pydantic | Validation |
| | SQLAlchemy | ORM |
| **Database** | MySQL/MariaDB | Data storage |
| **Auth** | python-jose | JWT tokens |
| | passlib | Password hashing |
| **Monitoring** | ping3 | ICMP ping |
| | requests | HTTP client |

---

**Versi**: 2.0 (Streamlit Edition)  
**Terakhir Diperbarui**: 11 Desember 2025
