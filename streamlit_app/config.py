"""
Streamlit Application Configuration
"""
import os
from dotenv import load_dotenv

load_dotenv()


class StreamlitConfig:
    """Streamlit application configuration."""
    
    # API Configuration
    API_BASE_URL = os.getenv("API_BASE_URL", "http://localhost:8001")
    
    # Session Configuration
    SESSION_EXPIRY_HOURS = 24
    SESSION_TIMEOUT_MINUTES = 120  # Auto-logout after 2 hours of inactivity (was 15 minutes)
    SESSION_WARNING_MINUTES = 10   # Show warning when this much time remains
    ENABLE_SESSION_TIMEOUT = False  # Set to False to disable inactivity timeout
    
    # Authentication Configuration
    MAX_LOGIN_ATTEMPTS = 5        # Maximum failed login attempts before lockout
    LOCKOUT_DURATION_MINUTES = 15 # Duration of account lockout after max attempts
    SHOW_SESSION_TIMER = True     # Display remaining session time in UI
    
    # UI Configuration
    PAGE_TITLE = "NetMonitor"
    PAGE_ICON = "🌐"
    LAYOUT = "wide"
    
    # Refresh intervals (Real-time: 30 seconds)
    DASHBOARD_REFRESH = 30
    ALERTS_REFRESH = 30
    
    # Timezone
    TIMEZONE = "Asia/Jakarta"  # GMT+7 WIB
    
    # Theme colors
    COLORS = {
        "primary": "#4F46E5",
        "success": "#10B981",
        "warning": "#F59E0B",
        "danger": "#EF4444",
        "info": "#3B82F6"
    }


config = StreamlitConfig()
