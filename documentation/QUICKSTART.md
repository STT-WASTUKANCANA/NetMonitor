# Quick Start Guide - NetMonitor

## 🚀 Starting All Services

### Option 1: All Services at Once (Recommended)
```bash
./start.sh
```

This starts:
- ✅ FastAPI Backend (port 8001)
- ✅ Streamlit Frontend (port 8501)  
- ✅ Network Monitor (realtime monitoring)

### Option 2: Quick Start (FastAPI already running)
```bash
./quickstart.sh
```

This starts:
- ✅ Streamlit Frontend (port 8501)
- ✅ Network Monitor (realtime monitoring)

Assumes FastAPI is already running separately.

### Option 3: Manual (Individual Services)

#### 1. Start FastAPI Backend
```bash
source venv/bin/activate
uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload
```

#### 2. Start Streamlit Frontend
```bash
source venv/bin/activate
cd streamlit_app
streamlit run app.py --server.port 8501
```

#### 3. Start Network Monitor
```bash
source venv/bin/activate
cd scripts
python monitor.py
```

## 🔗 Service URLs

| Service | URL | Description |
|---------|-----|-------------|
| **Streamlit App** | http://localhost:8501 | Main web interface |
| **FastAPI Docs** | http://localhost:8001/docs | API documentation |
| **Health Check** | http://localhost:8001/health | System health status |

## 🔐 Login Credentials

```
Email: admin@wastukancana.ac.id
Password: password123
```

## 📝 Logs

When using `start.sh` or `quickstart.sh`, logs are saved to:
- `logs/fastapi.log` - Backend API logs
- `logs/streamlit.log` - Frontend logs
- `logs/monitor.log` - Monitoring script logs

## 🛑 Stopping Services

Press `Ctrl+C` in the terminal where services are running.

All services will stop gracefully.

## ⚙️ Configuration

### Monitoring Settings
Edit `scripts/.env`:
```bash
API_BASE_URL=http://localhost:8001
API_EMAIL=admin@wastukancana.ac.id
API_PASSWORD=password123
MONITOR_INTERVAL=300  # Check every 5 minutes
PING_TIMEOUT=10
PING_COUNT=3
```

### Database Settings
Edit `.env` in project root:
```bash
DB_HOST=127.0.0.1
DB_PORT=3307
DB_NAME=NetMonitor
DB_USER=root
DB_PASSWORD=
```

## 🔧 Troubleshooting

### Port Already in Use
```bash
# Kill process on port 8001
sudo lsof -ti:8001 | xargs kill -9

# Kill process on port 8501  
sudo lsof -ti:8501 | xargs kill -9
```

### Monitor Can't Login
Check credentials in `scripts/.env` match database user.

### Database Connection Failed
```bash
# Check MySQL is running
sudo systemctl status mysql

# Start MySQL
sudo systemctl start mysql
```

## 📦 First Time Setup

1. **Install dependencies**:
   ```bash
   pip install -r requirements.txt
   ```

2. **Setup database** (if not done):
   ```bash
   # Create database
   mysql -h 127.0.0.1 -P 3307 -u root -p -e "CREATE DATABASE NetMonitor;"
   
   # Run migrations
   alembic upgrade head
   
   # Create admin user
   python scripts/create_admin_wastukancana.py
   ```

3. **Start services**:
   ```bash
   ./start.sh
   ```

## ✨ Features

- 📊 Real-time network device monitoring
- 🚨 Automatic alert generation
- 📈 Performance metrics and graphs
- 👥 Multi-user support with roles
- 🔔 Hierarchical device management
- 📱 Responsive web interface
