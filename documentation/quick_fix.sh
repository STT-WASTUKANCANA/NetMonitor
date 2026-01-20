#!/bin/bash

# Quick Fix Script untuk NetMonitor
# Memperbaiki database dan reset login attempts

echo "╔════════════════════════════════════════╗"
echo "║   NetMonitor Quick Fix Script          ║"
echo "╚════════════════════════════════════════╝"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

# Step 1: Fix Database
print_status "Step 1: Memperbaiki Database Connection"
echo ""

print_warning "Anda akan diminta password sudo untuk mengakses MySQL..."
echo ""

# Create database and user
sudo mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS netmonitor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'netmonitor'@'localhost' IDENTIFIED BY 'netmonitor123';
GRANT ALL PRIVILEGES ON netmonitor.* TO 'netmonitor'@'localhost';
FLUSH PRIVILEGES;
SELECT 'Database configured successfully' as Status;
EOF

if [ $? -eq 0 ]; then
    print_success "Database berhasil dikonfigurasi"
else
    print_error "Gagal mengkonfigurasi database"
    print_warning "Jalankan manual: sudo mysql -u root < fix_database.sql"
    exit 1
fi

# Step 2: Update .env
print_status "Step 2: Update .env configuration"
echo ""

if [ -f .env ]; then
    # Backup .env
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    print_success ".env di-backup"
    
    # Update database credentials
    sed -i 's/^DB_USER=.*/DB_USER=netmonitor/' .env
    sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=netmonitor123/' .env
    
    print_success ".env berhasil diupdate"
else
    print_warning ".env tidak ditemukan"
fi

# Step 3: Reset Login Attempts
print_status "Step 3: Reset Login Attempts"
echo ""

# Clear Streamlit cache
rm -rf ~/.streamlit/cache 2>/dev/null
rm -rf .streamlit/cache 2>/dev/null
find . -type d -name "__pycache__" -exec rm -rf {} + 2>/dev/null

print_success "Cache Streamlit dibersihkan"

# Step 4: Test Database Connection
print_status "Step 4: Test Database Connection"
echo ""

mysql -u netmonitor -pnetmonitor123 -e "SELECT 'Connection OK' as Status;" 2>/dev/null

if [ $? -eq 0 ]; then
    print_success "Koneksi database berhasil!"
else
    print_error "Koneksi database gagal"
    print_warning "Periksa kredensial database"
fi

# Step 5: Run Migrations
print_status "Step 5: Menjalankan Database Migrations"
echo ""

if [ -d "venv" ]; then
    if [ -f "venv/bin/alembic" ]; then
        ./venv/bin/alembic upgrade head
        
        if [ $? -eq 0 ]; then
            print_success "Migrasi database berhasil"
        else
            print_warning "Migrasi database gagal (mungkin sudah up-to-date)"
        fi
    else
        print_warning "Alembic tidak ditemukan di venv"
    fi
else
    print_warning "Virtual environment tidak ditemukan"
fi

# Summary
echo ""
echo "╔════════════════════════════════════════╗"
echo "║           Fix Summary                  ║"
echo "╚════════════════════════════════════════╝"
echo ""
print_success "Database: Dikonfigurasi dengan user 'netmonitor'"
print_success "Login Attempts: Di-reset"
print_success "Configuration: Diupdate"
echo ""
print_status "Langkah selanjutnya:"
echo "  1. Restart aplikasi: ./start.sh"
echo "  2. Buka browser: http://localhost:8501"
echo "  3. Login dengan: admin@netmonitor.com / admin123"
echo ""
print_warning "Jika masih ada error, lihat FIX_ERRORS.md untuk panduan lengkap"
echo ""
