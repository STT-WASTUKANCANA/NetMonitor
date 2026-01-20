#!/bin/bash
###############################################################################
# Quick Start - Streamlit Only with Auto Monitor
# For development: Starts Streamlit and Monitor (assumes FastAPI already running)
###############################################################################

set -e

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo -e "${BLUE}🚀 Quick Start - Streamlit + Monitor${NC}\n"

# Activate venv
source "$PROJECT_ROOT/venv/bin/activate"

# Create logs directory
mkdir -p "$PROJECT_ROOT/logs"

# Cleanup function
cleanup() {
    echo -e "\n${YELLOW}🛑 Stopping services...${NC}"
    kill 0
    exit
}

trap cleanup SIGINT SIGTERM

# Start Streamlit
echo -e "${GREEN}🌐 Starting Streamlit...${NC}"
cd "$PROJECT_ROOT/streamlit_app"
streamlit run app.py --server.port 8501 > ../logs/streamlit.log 2>&1 &
STREAMLIT_PID=$!

# Wait for Streamlit
sleep 3

# Start Monitor
echo -e "${GREEN}📡 Starting Monitor...${NC}"
cd "$PROJECT_ROOT/scripts"
python monitor.py > ../logs/monitor.log 2>&1 &
MONITOR_PID=$!

echo -e "\n${GREEN}✅ Services Started!${NC}"
echo -e "   Streamlit: ${GREEN}http://localhost:8501${NC} (PID: $STREAMLIT_PID)"
echo -e "   Monitor:   Running (PID: $MONITOR_PID)"
echo -e "\n${YELLOW}Press Ctrl+C to stop${NC}\n"

# Show monitor logs
tail -f logs/monitor.log
