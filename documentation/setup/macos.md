# 🍎 Setup macOS - NetMonitor

Panduan instalasi **NetMonitor** di macOS.

---

## 📋 Prerequisites

- macOS 12+ (Monterey or later)
- Python 3.10+
- MySQL 8.0+ (via Homebrew)

---

## 🛠️ Installation Steps

### 1. Install Homebrew (if not installed)

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### 2. Install Dependencies

```bash
# Install Python
brew install python@3.10

# Install MySQL
brew install mysql
brew services start mysql
mysql_secure_installation
```

### 3. Setup Project

```bash
# Navigate to project
cd /path/to/NetMonitor

# Create virtual environment
python3 -m venv venv

# Activate
source venv/bin/activate

# Install dependencies
pip install -r requirements.txt
```

### 4. Configure Environment

```bash
cp .env.example .env
nano .env  # or use any editor
```

### 5. Start Services

**Terminal 1 - FastAPI:**
```bash
source venv/bin/activate
uvicorn app.main:app --reload --port 8001
```

**Terminal 2 - Streamlit:**
```bash
source venv/bin/activate
cd streamlit_app
streamlit run app.py
```

---

## 🔗 Access

- Frontend: http://localhost:8501
- API: http://localhost:8001/docs

---

**Versi**: 2.0
