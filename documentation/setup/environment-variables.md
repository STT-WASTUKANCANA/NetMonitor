# 🔧 Environment Variables - NetMonitor

Dokumentasi semua environment variables untuk **NetMonitor**.

---

## 📁 File Locations

| File | Purpose |
|------|---------|
| `.env` | Konfigurasi utama (FastAPI + Streamlit) |
| `scripts/.env.scripts` | Konfigurasi monitoring script |

---

## 📋 Main Configuration (`.env`)

### Application Settings

```env
# Application
APP_NAME=NetMonitor
APP_ENV=development          # development | production
DEBUG=true                   # true | false
```

### FastAPI Backend

```env
# FastAPI Server
API_HOST=0.0.0.0             # Bind address
API_PORT=8001                # Server port
SECRET_KEY=your-secret-key   # Change in production!
```

### Database Configuration

```env
# Database (MySQL/MariaDB)
DB_HOST=127.0.0.1            # Database host
DB_PORT=3306                 # Database port
DB_NAME=netmonitor           # Database name
DB_USER=root                 # Database user
DB_PASSWORD=                 # Database password
```

### JWT Authentication

```env
# JWT Settings
JWT_SECRET_KEY=your-jwt-secret   # Change in production!
JWT_ALGORITHM=HS256              # Algorithm (HS256, RS256)
JWT_ACCESS_TOKEN_EXPIRE_MINUTES=30  # Token expiry
```

### Streamlit Configuration

```env
# Streamlit
STREAMLIT_PORT=8501          # Frontend port
API_BASE_URL=http://localhost:8001  # API URL for Streamlit
```

---

## 📋 Monitoring Script Configuration (`scripts/.env.scripts`)

```env
# API Configuration
API_BASE_URL=http://localhost:8001   # FastAPI URL
API_EMAIL=admin@netmonitor.local     # Login email
API_PASSWORD=password                # Login password

# Monitoring Settings
MONITOR_INTERVAL=300         # Check interval (seconds) - default 5 min
PING_TIMEOUT=10              # Ping timeout (seconds)
PING_COUNT=3                 # Number of ping attempts
```

---

## 🔒 Security Notes

### Development vs Production

| Variable | Development | Production |
|----------|-------------|------------|
| `DEBUG` | true | false |
| `APP_ENV` | development | production |
| `SECRET_KEY` | any | strong random string |
| `JWT_SECRET_KEY` | any | strong random string |

### Generate Strong Keys

```bash
# Python
python -c "import secrets; print(secrets.token_hex(32))"

# OpenSSL
openssl rand -hex 32
```

### Example Production `.env`

```env
APP_NAME=NetMonitor
APP_ENV=production
DEBUG=false

API_HOST=0.0.0.0
API_PORT=8001
SECRET_KEY=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0

DB_HOST=db.server.local
DB_PORT=3306
DB_NAME=netmonitor_prod
DB_USER=netmonitor_user
DB_PASSWORD=strong_password_here

JWT_SECRET_KEY=x1y2z3a4b5c6d7e8f9g0h1i2j3k4l5m6n7o8p9q0
JWT_ALGORITHM=HS256
JWT_ACCESS_TOKEN_EXPIRE_MINUTES=60

STREAMLIT_PORT=8501
API_BASE_URL=http://localhost:8001
```

---

## 📊 Variable Reference

### Application

| Variable | Type | Default | Description |
|----------|------|---------|-------------|
| `APP_NAME` | string | NetMonitor | Application name |
| `APP_ENV` | string | development | Environment |
| `DEBUG` | boolean | true | Debug mode |

### FastAPI

| Variable | Type | Default | Description |
|----------|------|---------|-------------|
| `API_HOST` | string | 0.0.0.0 | Bind address |
| `API_PORT` | integer | 8001 | Server port |
| `SECRET_KEY` | string | - | App secret |

### Database

| Variable | Type | Default | Description |
|----------|------|---------|-------------|
| `DB_HOST` | string | 127.0.0.1 | MySQL host |
| `DB_PORT` | integer | 3306 | MySQL port |
| `DB_NAME` | string | netmonitor | Database name |
| `DB_USER` | string | root | Database user |
| `DB_PASSWORD` | string | - | Database password |

### JWT

| Variable | Type | Default | Description |
|----------|------|---------|-------------|
| `JWT_SECRET_KEY` | string | - | JWT signing key |
| `JWT_ALGORITHM` | string | HS256 | JWT algorithm |
| `JWT_ACCESS_TOKEN_EXPIRE_MINUTES` | integer | 30 | Token TTL |

### Monitoring

| Variable | Type | Default | Description |
|----------|------|---------|-------------|
| `MONITOR_INTERVAL` | integer | 300 | Check interval (sec) |
| `PING_TIMEOUT` | integer | 10 | Ping timeout (sec) |
| `PING_COUNT` | integer | 3 | Ping attempts |

---

**Versi**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
