#!/bin/bash

# NetMonitor Database Access Fix Script
# This script fixes database authentication issues

echo "=================================="
echo "NetMonitor Database Access Fix"
echo "=================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running with sudo
if [ "$EUID" -eq 0 ]; then
    echo -e "${YELLOW}Warning: Running as root${NC}"
fi

echo "Step 1: Checking database service..."
if systemctl is-active --quiet mariadb || systemctl is-active --quiet mysql; then
    echo -e "${GREEN}✓ Database service is running${NC}"
else
    echo -e "${RED}✗ Database service is not running${NC}"
    echo "Starting database service..."
    sudo systemctl start mariadb || sudo systemctl start mysql
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Database service started${NC}"
    else
        echo -e "${RED}✗ Failed to start database service${NC}"
        exit 1
    fi
fi

echo ""
echo "Step 2: Setting up database user..."
echo "Please enter your MySQL/MariaDB root password (or press Enter if none):"

# Option 1: Connect with current root (if passwordless)
echo ""
echo "Attempting to connect to database..."

# Try to create/update netmonitor user
sudo mysql -u root <<EOF 2>/dev/null
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS netmonitor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create dedicated user for netmonitor
CREATE USER IF NOT EXISTS 'netmonitor'@'localhost' IDENTIFIED BY 'netmonitor_pass_2024';

-- Grant all privileges on netmonitor database
GRANT ALL PRIVILEGES ON netmonitor.* TO 'netmonitor'@'localhost';

-- Also allow root with a password (optional)
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
FLUSH PRIVILEGES;

-- Show created user
SELECT User, Host FROM mysql.user WHERE User IN ('root', 'netmonitor');
EOF

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database user configured successfully${NC}"
    echo ""
    echo "Database credentials:"
    echo "  User: netmonitor"
    echo "  Password: netmonitor_pass_2024"
    echo "  Database: netmonitor"
    echo ""
else
    echo -e "${RED}✗ Failed to configure database${NC}"
    echo ""
    echo "Please run this manually:"
    echo "  sudo mysql -u root"
    echo "  Then execute the following SQL:"
    echo ""
    echo "  CREATE DATABASE IF NOT EXISTS netmonitor;"
    echo "  CREATE USER IF NOT EXISTS 'netmonitor'@'localhost' IDENTIFIED BY 'netmonitor_pass_2024';"
    echo "  GRANT ALL PRIVILEGES ON netmonitor.* TO 'netmonitor'@'localhost';"
    echo "  FLUSH PRIVILEGES;"
    exit 1
fi

echo ""
echo "Step 3: Testing connection..."
mysql -u netmonitor -pnetmonitor_pass_2024 -e "SELECT 'Connection successful!' as Status;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database connection test successful${NC}"
else
    echo -e "${RED}✗ Database connection test failed${NC}"
    echo "Please verify credentials and try again"
    exit 1
fi

echo ""
echo "=================================="
echo -e "${GREEN}Database access fix completed!${NC}"
echo "=================================="
echo ""
echo "Next steps:"
echo "1. Update your .env file with:"
echo "   DB_USER=netmonitor"
echo "   DB_PASSWORD=netmonitor_pass_2024"
echo ""
echo "2. Run database migrations:"
echo "   ./venv/bin/alembic upgrade head"
echo ""
echo "3. Restart your application"
echo ""
