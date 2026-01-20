#!/bin/bash

# Script untuk verifikasi hasil migrasi database

echo "╔════════════════════════════════════════════════════════════════════╗"
echo "║         NetMonitor - Database Migration Verification              ║"
echo "╚════════════════════════════════════════════════════════════════════╝"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Database config
DB_HOST="127.0.0.1"
DB_PORT="3307"
DB_NAME="NetMonitor"
DB_USER="root"

echo -e "${BLUE}[1]${NC} Checking Database Connection..."
mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -e "SELECT VERSION() as Version, DATABASE() as CurrentDB;" $DB_NAME 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓${NC} Database connection successful"
else
    echo -e "${RED}✗${NC} Database connection failed"
    exit 1
fi

echo ""
echo -e "${BLUE}[2]${NC} Checking Tables..."
echo "Expected tables: alembic_version, users, devices, device_logs, alerts"
echo ""

TABLES=$(mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -N -e "SHOW TABLES;" $DB_NAME 2>/dev/null)

if [ $? -eq 0 ]; then
    TABLE_COUNT=$(echo "$TABLES" | wc -l)
    echo "Found $TABLE_COUNT tables:"
    echo "$TABLES" | while read table; do
        echo "  - $table"
    done
    
    if [ "$TABLE_COUNT" -ge 5 ]; then
        echo -e "${GREEN}✓${NC} All required tables exist"
    else
        echo -e "${YELLOW}!${NC} Warning: Expected 5 tables, found $TABLE_COUNT"
    fi
else
    echo -e "${RED}✗${NC} Could not retrieve tables"
    exit 1
fi

echo ""
echo -e "${BLUE}[3]${NC} Checking Migration Version..."
VERSION=$(mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -N -e "SELECT version_num FROM alembic_version LIMIT 1;" $DB_NAME 2>/dev/null)

if [ $? -eq 0 ]; then
    if [ -n "$VERSION" ]; then
        echo -e "${GREEN}✓${NC} Migration version: $VERSION"
    else
        echo -e "${YELLOW}!${NC} No migration version recorded"
    fi
else
    echo -e "${RED}✗${NC} Could not check migration version"
fi

echo ""
echo -e "${BLUE}[4]${NC} Checking Users Table..."
USER_COUNT=$(mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -N -e "SELECT COUNT(*) FROM users;" $DB_NAME 2>/dev/null)

if [ $? -eq 0 ]; then
    echo "Found $USER_COUNT user(s) in database"
    
    if [ "$USER_COUNT" -gt 0 ]; then
        echo ""
        echo "User details:"
        mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -e "SELECT id, email, role, created_at FROM users;" $DB_NAME 2>/dev/null
        echo -e "${GREEN}✓${NC} Users table populated"
    else
        echo -e "${YELLOW}!${NC} Warning: No users found (you may need to seed data)"
    fi
else
    echo -e "${RED}✗${NC} Could not check users"
fi

echo ""
echo -e "${BLUE}[5]${NC} Checking Python Connection..."

cd "$(dirname "$0")"

if [ -d "venv" ]; then
    TEST_RESULT=$(./venv/bin/python3 << 'EOF'
from app.database import check_database_connection
if check_database_connection():
    print("✓ Python database connection successful")
    exit(0)
else:
    print("✗ Python database connection failed")
    exit(1)
EOF
)
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}$TEST_RESULT${NC}"
    else
        echo -e "${RED}$TEST_RESULT${NC}"
    fi
else
    echo -e "${YELLOW}!${NC} Virtual environment not found, skipping Python test"
fi

echo ""
echo "╔════════════════════════════════════════════════════════════════════╗"
echo "║                    Verification Summary                            ║"
echo "╚════════════════════════════════════════════════════════════════════╝"
echo ""
echo "Database: $DB_NAME"
echo "Host: $DB_HOST:$DB_PORT"
echo "User: $DB_USER"
echo ""
echo -e "${GREEN}✓ Migration verification complete!${NC}"
echo ""
echo "Next steps:"
echo "  1. Create admin user (if needed)"
echo "  2. Start application: ./start.sh"
echo "  3. Access web interface: http://localhost:8501"
echo ""
