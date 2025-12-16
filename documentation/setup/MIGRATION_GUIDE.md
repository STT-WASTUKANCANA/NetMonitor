# Database Migration Guide - NetMonitor

Panduan lengkap untuk migrasi database NetMonitor ke MySQL menggunakan terminal.

## Prerequisites

✅ MySQL server running di port **3307**
✅ Database user: **root**
✅ Alembic sudah terinstall
✅ Python virtual environment sudah aktif

## Step 1: Aktifkan Virtual Environment

```bash
# Pastikan berada di direktori project
cd /media/boba/DATA/Project/Streamlit/NetMonitor

# Aktifkan virtual environment
source venv/bin/activate
```

## Step 2: Connect ke MySQL dan Buat Database

```bash
# Connect ke MySQL
mysql -h 127.0.0.1 -P 3307 -u root -p

# Di MySQL prompt, jalankan:
CREATE DATABASE IF NOT EXISTS NetMonitor;
SHOW DATABASES;
USE NetMonitor;
EXIT;
```

## Step 3: Generate Migration Script

```bash
# Generate migration dari models
alembic revision --autogenerate -m "Initial migration"
```

File migration akan dibuat di `alembic/versions/` dengan nama seperti `xxxxx_initial_migration.py`

## Step 4: Review Migration Script

```bash
# List migrations
alembic history

# View migration file (ganti dengan nama file yang sebenarnya)
cat alembic/versions/*_initial_migration.py
```

## Step 5: Jalankan Migration

```bash
# Apply migrations ke database
alembic upgrade head
```

Expected output:
```
INFO  [alembic.runtime.migration] Context impl MySQLImpl.
INFO  [alembic.runtime.migration] Will assume non-transactional DDL.
INFO  [alembic.runtime.migration] Running upgrade  -> xxxxx, Initial migration
```

## Step 6: Verifikasi Tables

```bash
# Connect ke MySQL dan check tables
mysql -h 127.0.0.1 -P 3307 -u root -p NetMonitor

# Di MySQL prompt:
SHOW TABLES;
DESCRIBE users;
DESCRIBE devices;
DESCRIBE device_logs;
DESCRIBE alerts;
EXIT;
```

## Step 7: Create Admin User

```bash
# Jalankan script untuk create admin user
python scripts/create_admin.py
```

Expected output:
```
Creating admin user...
✅ Admin user created successfully!
   Email: admin@netmonitor.com
   Password: admin123
   ⚠️  Please change the password after first login!
```

## Step 8: Verifikasi Admin User

```bash
# Check admin user di database
mysql -h 127.0.0.1 -P 3307 -u root -p NetMonitor

# Di MySQL prompt:
SELECT id, first_name, last_name, email, role FROM users;
EXIT;
```

## Step 9: Test FastAPI Application

```bash
# Restart FastAPI (jika sudah running)
# Ctrl+C untuk stop, kemudian:
uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload
```

## Step 10: Test Login

Buka browser dan akses:
- API Docs: http://localhost:8001/docs
- Streamlit: http://localhost:8501

Login credentials:
- Email: `admin@netmonitor.com`
- Password: `admin123`

## Troubleshooting

### Error: Can't connect to MySQL server
```bash
# Check MySQL status
sudo systemctl status mysql
# atau
sudo service mysql status

# Start MySQL if not running
sudo systemctl start mysql
```

### Error: Access denied for user 'root'
Update password di `.env` file:
```bash
DB_PASSWORD=your_mysql_password
```

### Error: Database doesn't exist
```bash
mysql -h 127.0.0.1 -P 3307 -u root -p -e "CREATE DATABASE NetMonitor;"
```

### Migration sudah jalan, ingin rollback
```bash
# Rollback 1 migration
alembic downgrade -1

# Atau rollback semua
alembic downgrade base
```

## Useful Commands

```bash
# Check current migration version
alembic current

# Show migration history
alembic history

# Show migration script SQL (tanpa execute)
alembic upgrade head --sql

# Create new migration after model changes
alembic revision --autogenerate -m "Description of changes"
```

## Database Connection String

Database URL yang digunakan (dari `.env`):
```
mysql+pymysql://root:@127.0.0.1:3307/NetMonitor
```

Format:
```
mysql+pymysql://<user>:<password>@<host>:<port>/<database>
```
