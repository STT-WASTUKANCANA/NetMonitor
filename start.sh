#!/bin/bash
###############################################################################
# NetMonitor Startup Script
# Starts all services: FastAPI Backend, Streamlit Frontend, and Monitor
###############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get project root directory
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║           NetMonitor - All Services Startup                    ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"

# Check if virtual environment exists
if [ ! -d "$PROJECT_ROOT/venv" ]; then
    echo -e "${RED}❌ Virtual environment not found!${NC}"
    echo -e "${YELLOW}Please create it first: python -m venv venv${NC}"
    exit 1
fi

# Activate virtual environment
source "$PROJECT_ROOT/venv/bin/activate"

# Function to cleanup on exit
cleanup() {
    echo -e "\n${YELLOW}🛑 Shutting down all services...${NC}"
    kill 0  # Kill all background processes in this process group
    exit
}

trap cleanup SIGINT SIGTERM

# Create logs directory
mkdir -p "$PROJECT_ROOT/logs"

echo -e "\n${GREEN}📦 Starting services...${NC}\n"

# 1. Start FastAPI Backend
echo -e "${BLUE}🚀 Starting FastAPI Backend on port 8001...${NC}"
cd "$PROJECT_ROOT"
uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload > logs/fastapi.log 2>&1 &
FASTAPI_PID=$!
echo -e "${GREEN}   ✓ FastAPI started (PID: $FASTAPI_PID)${NC}"

# Wait for FastAPI to be ready
echo -e "${YELLOW}   ⏳ Waiting for FastAPI to be ready...${NC}"
for i in {1..30}; do
    if curl -s http://localhost:8001/health > /dev/null 2>&1; then
        echo -e "${GREEN}   ✓ FastAPI is ready!${NC}"
        break
    fi
    sleep 1
    if [ $i -eq 30 ]; then
        echo -e "${RED}   ✗ FastAPI failed to start${NC}"
        cat logs/fastapi.log
        cleanup
    fi
done

# 2. Start Streamlit Frontend
echo -e "\n${BLUE}🌐 Starting Streamlit Frontend on port 8501...${NC}"
cd "$PROJECT_ROOT/streamlit_app"
streamlit run app.py --server.port 8501 > ../logs/streamlit.log 2>&1 &
STREAMLIT_PID=$!
echo -e "${GREEN}   ✓ Streamlit started (PID: $STREAMLIT_PID)${NC}"

# Wait a bit for Streamlit to initialize
sleep 3

# 3. Start Monitoring Script
echo -e "\n${BLUE}📡 Starting Network Monitor...${NC}"
cd "$PROJECT_ROOT/scripts"
python monitor.py > ../logs/monitor.log 2>&1 &
MONITOR_PID=$!
echo -e "${GREEN}   ✓ Monitor started (PID: $MONITOR_PID)${NC}"

echo -e "\n${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                  All Services Running!                         ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"

echo -e "\n${BLUE}📊 Service URLs:${NC}"
echo -e "   🌐 Streamlit App:  ${GREEN}http://localhost:8501${NC}"
echo -e "   🔌 FastAPI Docs:   ${GREEN}http://localhost:8001/docs${NC}"
echo -e "   💚 Health Check:   ${GREEN}http://localhost:8001/health${NC}"

echo -e "\n${BLUE}📝 Log Files:${NC}"
echo -e "   FastAPI:    logs/fastapi.log"
echo -e "   Streamlit:  logs/streamlit.log"
echo -e "   Monitor:    logs/monitor.log"

echo -e "\n${BLUE}🔧 Process IDs:${NC}"
echo -e "   FastAPI:    $FASTAPI_PID"
echo -e "   Streamlit:  $STREAMLIT_PID"
echo -e "   Monitor:    $MONITOR_PID"

echo -e "\n${YELLOW}Press Ctrl+C to stop all services${NC}\n"

# Show live monitor logs
echo -e "${BLUE}📡 Monitor Output (live):${NC}"
echo -e "${BLUE}─────────────────────────────────────────────────────────────────${NC}"
tail -f logs/monitor.log
