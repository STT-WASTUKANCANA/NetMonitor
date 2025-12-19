#!/bin/bash
###############################################################################
# Fix MySQL/MariaDB Root Access
# Script untuk memperbaiki akses root MySQL/MariaDB
###############################################################################

echo "==================================================================="
echo "  Fix MySQL/MariaDB Root Access untuk NetMonitor"
echo "==================================================================="
echo ""
echo "Script ini akan memperbaiki akses root MySQL/MariaDB."
echo "Anda akan diminta password sudo."
echo ""

# Create database and grant permissions
echo "Membuat database dan memberikan akses..."
sudo mysql <<EOF
-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS netmonitor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant all privileges ke root untuk database ini (tanpa password)
GRANT ALL PRIVILEGES ON netmonitor.* TO 'root'@'localhost';
GRANT ALL PRIVILEGES ON netmonitor.* TO 'root'@'127.0.0.1';

-- Flush privileges
FLUSH PRIVILEGES;

-- Tampilkan konfirmasi
SELECT 'Database netmonitor berhasil dibuat!' AS Status;
SHOW DATABASES LIKE 'netmonitor';
EOF

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Database setup berhasil!"
    echo ""
    echo "Sekarang jalankan:"
    echo "  python scripts/init_db.py"
    echo ""
else
    echo ""
    echo "❌ Gagal setup database."
    echo ""
    echo "Silakan jalankan manual:"
    echo "  sudo mysql"
    echo "  CREATE DATABASE IF NOT EXISTS netmonitor;"
    echo "  EXIT;"
    echo ""
fi
