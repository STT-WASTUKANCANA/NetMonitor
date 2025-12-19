#!/bin/bash
###############################################################################
# NetMonitor Local Setup Script
# Complete setup untuk backend, frontend, dan monitoring scripts
###############################################################################

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║         NetMonitor - Local Setup Script                       ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"

# Step 1: Check Python
echo -e "\n${BLUE}[1/8]${NC} Checking Python installation..."
if ! command -v python3 &> /dev/null; then
    echo -e "${RED}❌ Python3 not found!${NC}"
    exit 1
fi
PYTHON_VERSION=$(python3 --version)
echo -e "${GREEN}✓ Found: $PYTHON_VERSION${NC}"

# Step 2: Virtual Environment
echo -e "\n${BLUE}[2/8]${NC} Setting up virtual environment..."
if [ -d "$PROJECT_ROOT/venv" ]; then
    echo -e "${YELLOW}⚠ Virtual environment already exists${NC}"
else
    echo -e "${YELLOW}Creating virtual environment...${NC}"
    python3 -m venv "$PROJECT_ROOT/venv"
    echo -e "${GREEN}✓ Virtual environment created${NC}"
fi

# Activate venv
source "$PROJECT_ROOT/venv/bin/activate"
echo -e "${GREEN}✓ Virtual environment activated${NC}"

# Step 3: Install Dependencies
echo -e "\n${BLUE}[3/8]${NC} Installing dependencies..."
echo -e "${YELLOW}Installing main requirements...${NC}"
pip install -q --upgrade pip
pip install -q -r "$PROJECT_ROOT/requirements.txt"
echo -e "${GREEN}✓ All dependencies installed${NC}"

# Step 4: Make Scripts Executable
echo -e "\n${BLUE}[4/8]${NC} Making scripts executable..."
chmod +x "$PROJECT_ROOT/start.sh"
chmod +x "$PROJECT_ROOT/quickstart.sh"
chmod +x "$PROJECT_ROOT/scripts/detect_ip.py"
chmod +x "$PROJECT_ROOT/streamlit_app/dev.sh"
echo -e "${GREEN}✓ Scripts are now executable${NC}"

# Step 5: Database Setup
echo -e "\n${BLUE}[5/8]${NC} Setting up database..."
echo -e "${YELLOW}Checking MySQL/MariaDB connection...${NC}"

# Check if MySQL is running
if systemctl is-active --quiet mysql || systemctl is-active --quiet mariadb; then
    echo -e "${GREEN}✓ MySQL/MariaDB is running${NC}"
else
    echo -e "${RED}❌ MySQL/MariaDB is not running!${NC}"
    echo -e "${YELLOW}Please start MySQL/MariaDB:${NC}"
    echo -e "  sudo systemctl start mysql"
    echo -e "  sudo systemctl start mariadb"
    exit 1
fi

# Database configuration
DB_NAME="netmonitor"
DB_USER="root"

echo -e "\n${YELLOW}Database Configuration:${NC}"
echo -e "  Database: ${GREEN}$DB_NAME${NC}"
echo -e "  User: ${GREEN}$DB_USER${NC}"
echo -e "  Host: ${GREEN}127.0.0.1:3306${NC}"

echo -e "\n${YELLOW}Creating database (requires MySQL root access)...${NC}"
echo -e "${YELLOW}You may need to enter your MySQL root password.${NC}"

# Try to create database (will prompt for password if needed)
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || \
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || \
{
    echo -e "${RED}❌ Failed to create database${NC}"
    echo -e "${YELLOW}Please create the database manually:${NC}"
    echo -e "  sudo mysql -e \"CREATE DATABASE IF NOT EXISTS $DB_NAME;\""
    echo -e "\nOr set a password in .env file and update DB_PASSWORD"
    exit 1
}

echo -e "${GREEN}✓ Database '$DB_NAME' is ready${NC}"

# Step 6: Initialize Database Tables
echo -e "\n${BLUE}[6/8]${NC} Initializing database tables..."
cd "$PROJECT_ROOT"

# Create alembic versions directory if it doesn't exist
mkdir -p "$PROJECT_ROOT/alembic/versions"

# Check if we need to use alembic or direct table creation
if [ -z "$(ls -A $PROJECT_ROOT/alembic/versions)" ]; then
    echo -e "${YELLOW}No migration files found. Using direct table creation...${NC}"
    python "$PROJECT_ROOT/scripts/init_db.py"
else
    echo -e "${YELLOW}Running alembic migrations...${NC}"
    alembic upgrade head
fi

echo -e "${GREEN}✓ Database initialized${NC}"

# Step 7: Create logs directory
echo -e "\n${BLUE}[7/8]${NC} Setting up directories..."
mkdir -p "$PROJECT_ROOT/logs"
echo -e "${GREEN}✓ Log directory created${NC}"

# Step 8: Verify Setup
echo -e "\n${BLUE}[8/8]${NC} Verifying setup..."

# Check if key modules can be imported
python3 -c "from app.main import app; from app.database import engine" 2>/dev/null && \
echo -e "${GREEN}✓ Backend modules OK${NC}" || \
echo -e "${RED}✗ Backend modules have issues${NC}"

python3 -c "import streamlit" 2>/dev/null && \
echo -e "${GREEN}✓ Streamlit OK${NC}" || \
echo -e "${RED}✗ Streamlit has issues${NC}"

cd "$PROJECT_ROOT/scripts"
python3 -c "from monitor import *" 2>/dev/null && \
echo -e "${GREEN}✓ Monitor script OK${NC}" || \
echo -e "${RED}✗ Monitor script has issues${NC}"

# Final Summary
echo -e "\n${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║              Setup Complete! 🎉                                 ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"

echo -e "\n${BLUE}📋 Default Credentials:${NC}"
echo -e "   Email:    ${GREEN}admin@wastukancana.ac.id${NC}"
echo -e "   Password: ${GREEN}password123${NC}"

echo -e "\n${BLUE}🚀 To start all services:${NC}"
echo -e "   ${YELLOW}./start.sh${NC}"

echo -e "\n${BLUE}🌐 Service URLs (after starting):${NC}"
echo -e "   Streamlit:  ${GREEN}http://localhost:8501${NC}"
echo -e "   FastAPI:    ${GREEN}http://localhost:8001/docs${NC}"
echo -e "   Health:     ${GREEN}http://localhost:8001/health${NC}"

echo -e "\n${BLUE}📝 Configuration Files:${NC}"
echo -e "   Main:       ${YELLOW}.env${NC}"
echo -e "   Scripts:    ${YELLOW}scripts/.env${NC}"

echo -e "\n${YELLOW}Next steps:${NC}"
echo -e "  1. Review and update ${YELLOW}.env${NC} file if needed"
echo -e "  2. Review and update ${YELLOW}scripts/.env${NC} file if needed"
echo -e "  3. Run ${GREEN}./start.sh${NC} to start all services"
echo -e ""
