# 🪟 Setup Windows - NetMonitor

Panduan instalasi **NetMonitor** di Windows.

---

## 📋 Prerequisites

- Windows 10/11
- Python 3.10+
- MySQL 8.0+

---

## 🛠️ Installation Steps

### 1. Install Python

1. Download dari https://python.org/downloads
2. Run installer, centang "Add Python to PATH"
3. Verify: `python --version`

### 2. Install MySQL

1. Download MySQL Installer: https://dev.mysql.com/downloads/installer/
2. Install MySQL Server & MySQL Workbench
3. Configure root password

### 3. Setup Project

```powershell
# Navigate to project
cd C:\path\to\NetMonitor

# Create virtual environment
python -m venv venv

# Activate
.\venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt
```

### 4. Configure Environment

```powershell
copy .env.example .env
notepad .env
```

Update database credentials.

### 5. Start Services

**Terminal 1 - FastAPI:**
```powershell
.\venv\Scripts\activate
uvicorn app.main:app --reload --port 8001
```

**Terminal 2 - Streamlit:**
```powershell
.\venv\Scripts\activate
cd streamlit_app
streamlit run app.py
```

---

## 🔗 Access

- Frontend: http://localhost:8501
- API: http://localhost:8001/docs

---

## ⚠️ Notes

- Untuk monitoring script, gunakan **Administrator PowerShell**
- Port 8001 dan 8501 harus dibuka di Windows Firewall

---

**Versi**: 2.0
