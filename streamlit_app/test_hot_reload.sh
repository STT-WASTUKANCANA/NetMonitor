#!/bin/bash

# Hot Reload Test Script
# This script helps verify that hot reload is working correctly

echo "🧪 Testing Hot Reload Functionality"
echo "===================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Check configuration
echo "📋 Step 1: Checking configuration..."
if grep -q "runOnSave = true" .streamlit/config.toml; then
    echo -e "${GREEN}✅ runOnSave is enabled${NC}"
else
    echo "❌ runOnSave is NOT enabled"
    exit 1
fi

if grep -q 'fileWatcherType = "watchdog"' .streamlit/config.toml; then
    echo -e "${GREEN}✅ fileWatcherType is set to watchdog${NC}"
else
    echo "⚠️  fileWatcherType is not set to watchdog"
fi

echo ""

# Step 2: Check if watchdog is installed
echo "📦 Step 2: Checking watchdog installation..."
if ../venv/bin/python -c "import watchdog" 2>/dev/null; then
    echo -e "${GREEN}✅ watchdog library is installed${NC}"
else
    echo "❌ watchdog is NOT installed"
    echo "   Run: pip install watchdog"
    exit 1
fi

echo ""

# Step 3: Instructions for manual testing
echo "🎯 Step 3: Manual Testing Instructions"
echo "======================================"
echo ""
echo -e "${YELLOW}1. Make sure Streamlit is running:${NC}"
echo "   ./dev.sh"
echo ""
echo -e "${YELLOW}2. Open your browser to:${NC}"
echo "   http://localhost:8501"
echo ""
echo -e "${YELLOW}3. In browser Settings (☰ menu):${NC}"
echo "   ✓ Check 'Always rerun'"
echo ""
echo -e "${YELLOW}4. Make a test change:${NC}"
echo "   - Edit app.py (add a comment)"
echo "   - Save the file (Ctrl+S)"
echo "   - Watch browser - should auto-reload in ~2 seconds!"
echo ""
echo -e "${YELLOW}5. Success indicators:${NC}"
echo "   ✅ Browser refreshes automatically"
echo "   ✅ No need to press F5"
echo "   ✅ No need to restart terminal"
echo ""
echo "If it works, you'll see:"
echo "  - Streamlit console: 'Source file changed...'"
echo "  - Browser: Brief 'Running...' animation then refresh"
echo ""
echo "🎉 Your hot reload should be working!"
