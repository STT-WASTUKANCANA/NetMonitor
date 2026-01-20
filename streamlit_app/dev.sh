#!/bin/bash

# Streamlit Hot Reload Development Script
# This script starts Streamlit with optimal hot reload configuration

echo "🚀 Starting Streamlit in Development Mode with Hot Reload..."
echo "📝 Any changes to Python files will auto-reload in the browser"
echo "⚡ No need to manually refresh or restart!"
echo ""

# Check if venv exists
if [ -d "../venv" ]; then
    echo "✅ Using virtual environment: ../venv"
    source ../venv/bin/activate
elif [ -d "venv" ]; then
    echo "✅ Using virtual environment: ./venv"
    source venv/bin/activate
else
    echo "⚠️  No virtual environment found, using system Python"
fi

# Display configuration
echo ""
echo "Configuration:"
echo "  - runOnSave: enabled ✅"
echo "  - fileWatcherType: auto ✅"
echo "  - Port: 8501"
echo ""
echo "💡 Tips:"
echo "  1. Edit any .py file and save - browser will auto-reload"
echo "  2. Press 'R' in browser to manually rerun"
echo "  3. Press 'C' to clear cache and rerun"
echo "  4. Ctrl+C to stop the server"
echo ""

# Start Streamlit with hot reload
streamlit run app.py \
    --server.runOnSave=true \
    --server.fileWatcherType=auto \
    --server.port=8501
