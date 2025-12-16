# ⚙️ Monitoring Script - NetMonitor

Panduan konfigurasi dan penggunaan **monitoring script** Python.

---

## 📋 Overview

Script `monitor.py` bertugas:
- Ping semua devices secara periodik
- Check port connectivity
- Update status ke FastAPI via REST API
- Generate alerts jika device down

---

## 🛠️ Setup

### 1. Navigate to Scripts Directory
```bash
cd /path/to/NetMonitor/scripts
```

### 2. Setup Environment
```bash
cp .env.scripts.example .env.scripts
```

### 3. Edit Configuration
```bash
nano .env.scripts
```

```env
# API Configuration
API_BASE_URL=http://localhost:8001
API_EMAIL=admin@netmonitor.local
API_PASSWORD=your_password

# Monitoring Settings
MONITOR_INTERVAL=300        # 5 minutes
PING_TIMEOUT=10             # 10 seconds
PING_COUNT=3                # 3 ping attempts
```

### 4. Install Dependencies
```bash
pip install -r requirements.txt
```

Dependencies:
- `ping3` - ICMP ping
- `requests` - HTTP client
- `python-dotenv` - Env loading

---

## 🚀 Running

### Manual Run
```bash
# Requires root for ICMP ping
sudo python monitor.py
```

### Background Run
```bash
sudo nohup python monitor.py > monitor.log 2>&1 &
```

### Cron Job (Recommended)
```bash
# Edit crontab
sudo crontab -e

# Add line (every 5 minutes)
*/5 * * * * cd /path/to/NetMonitor/scripts && /path/to/venv/bin/python monitor.py >> /var/log/netmonitor.log 2>&1
```

---

## 📊 Flow Diagram

```
┌─────────────────────────────────────┐
│        Start Monitoring             │
└────────────────┬────────────────────┘
                 ▼
┌─────────────────────────────────────┐
│    Load Configuration (.env)        │
└────────────────┬────────────────────┘
                 ▼
┌─────────────────────────────────────┐
│    Login to API (JWT Token)         │
│    POST /api/auth/login             │
└────────────────┬────────────────────┘
                 ▼
┌─────────────────────────────────────┐
│    Fetch All Devices                │
│    GET /api/devices                 │
└────────────────┬────────────────────┘
                 ▼
┌─────────────────────────────────────┐
│    Sort by Hierarchy                │
│    (utama → sub → device)           │
└────────────────┬────────────────────┘
                 ▼
┌─────────────────────────────────────┐
│    For Each Device:                 │
│    1. Check if parent is UP         │
│    2. Ping device                   │
│    3. Check port (if configured)    │
│    4. Send status to API            │
└────────────────┬────────────────────┘
                 ▼
┌─────────────────────────────────────┐
│    Sleep (MONITOR_INTERVAL)         │
│    Default: 5 minutes               │
└────────────────┬────────────────────┘
                 ▼
              Loop ↺
```

---

## 🔍 Hierarchical Checking

Script mengecek perangkat berdasarkan hierarki:

1. Check **Router Utama** dulu
2. Jika utama DOWN → skip semua children (mark as down)
3. Jika utama UP → check sub-devices
4. Continue ke leaf devices

Ini mencegah false alerts ketika router utama down.

---

## 📝 Log Output

### Success Log
```
2025-12-11 10:30:00 - INFO - 🔄 Starting monitoring cycle
2025-12-11 10:30:01 - INFO - 📡 Fetched 50 devices
2025-12-11 10:30:02 - INFO - 🔍 Checking Router Utama (192.168.1.1)...
2025-12-11 10:30:03 - INFO -   ✅ UP - Response: 15.5ms, Loss: 0.0%
2025-12-11 10:30:04 - INFO - 🔍 Checking Switch Lt.1 (192.168.1.10)...
2025-12-11 10:30:05 - INFO -   ✅ UP - Response: 8.2ms, Loss: 0.0%
2025-12-11 10:30:30 - INFO - ✅ Monitoring cycle completed - checked 50 devices
```

### Error Log
```
2025-12-11 10:30:02 - INFO - 🔍 Checking AP Lt.3 (192.168.1.50)...
2025-12-11 10:30:12 - INFO -   ❌ DOWN - Response: N/A, Loss: 100%
2025-12-11 10:30:12 - WARNING - ⚠️ Alert created for device 15
```

---

## 🔧 Troubleshooting

### Permission Denied (ping)
```bash
# ICMP requires root
sudo python monitor.py

# Or set capability
sudo setcap cap_net_raw+ep /path/to/python
```

### API Connection Failed
```
❌ Login request failed: Connection refused
```
Check jika FastAPI berjalan:
```bash
curl http://localhost:8001/health
```

### Authentication Failed
```
❌ Login failed: Invalid credentials
```
Verify credentials di `.env.scripts`

---

## 📊 Metrics Collected

| Metric | Type | Description |
|--------|------|-------------|
| `status` | enum | up, down |
| `response_time` | float | Latency dalam ms |
| `packet_loss` | float | Packet loss % |
| `checked_at` | datetime | Timestamp check |

---

## 🔄 Systemd Service (Production)

### Create Service File
```bash
sudo nano /etc/systemd/system/netmonitor-script.service
```

```ini
[Unit]
Description=NetMonitor Monitoring Script
After=network.target

[Service]
Type=simple
User=root
WorkingDirectory=/path/to/NetMonitor/scripts
ExecStart=/path/to/venv/bin/python monitor.py
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

### Enable Service
```bash
sudo systemctl daemon-reload
sudo systemctl enable netmonitor-script
sudo systemctl start netmonitor-script
sudo systemctl status netmonitor-script
```

---

**Versi**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
