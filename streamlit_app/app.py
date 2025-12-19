"""
NetMonitor - Streamlit Main Application
"""
import streamlit as st
import sys
from pathlib import Path

# Add parent directory to path for imports
sys.path.insert(0, str(Path(__file__).parent.parent))

from streamlit_app.config import config
from streamlit_app.utils import init_session_state, is_authenticated, require_auth, logout, get_current_user
from streamlit_app.utils.api_client import api_client
from streamlit_app.utils.websocket import inject_websocket_listener
from streamlit_app.utils.autorefresh import auto_refresh


from streamlit_app.utils.ui import setup_page_config, sidebar_user_card


def setup_page():
    """Setup page configuration."""
    setup_page_config()


def render_sidebar():
    """Render responsive sidebar."""
    with st.sidebar:
        st.markdown(f"# {config.PAGE_ICON} {config.PAGE_TITLE}")
        st.markdown("---")
        
        user = get_current_user()
        if user:
            sidebar_user_card(user)
        
        st.markdown("---")
        
        if st.button("🚪 Logout", use_container_width=True):
            logout()
            st.rerun()


def main():
    """Main application entry point."""
    init_session_state()
    
    if not is_authenticated():
        # Show login page
        from streamlit_app.utils.session import show_login_page
        show_login_page()
        return
    
    # Set token in API client
    if st.session_state.token:
        api_client.set_token(st.session_state.token)
    
    # Setup page
    setup_page()
    
    # Render sidebar
    render_sidebar()
    
    # Main content - Welcome page
    st.title(f"{config.PAGE_ICON} Welcome to {config.PAGE_TITLE}")
    
    # Inject WebSocket for real-time alerts
    inject_websocket_listener()
    
    # Auto-refresh dashboard data every 10 seconds
    auto_refresh(interval_seconds=10, key="dashboard_refresh")
    
    user = get_current_user()
    st.markdown(f"### Hello, {user.get('first_name', 'User')}! 👋")
    
    # Show session status info
    if st.session_state.get('_session_persistent'):
        st.info("🔐 Your session is active and will remain logged in even after refresh. Session expires in 24 hours or when you click Logout.")
        # Only show once per session
        del st.session_state._session_persistent
    
    st.markdown("""
    **NetMonitor** is your network monitoring solution. Use the sidebar to navigate to:
    
    - **📊 Dashboard** - View real-time network statistics and alerts
    - **📡 Devices** - Manage your network devices
    - **🔔 Alerts** - View and manage active alerts
    - **📈 Monitoring** - Real-time system health monitoring
    """)
    
    # Quick stats
    st.markdown("---")
    st.markdown("### 📈 Quick Overview")
    
    summary = api_client.get_dashboard_summary()
    
    if summary.get("success"):
        data = summary.get("data", {})
        
        col1, col2, col3, col4 = st.columns(4)
        
        with col1:
            st.metric("Total Devices", data.get("total_devices", 0))
        
        with col2:
            devices_up = data.get("devices_up", 0)
            total = data.get("total_devices", 1)
            st.metric("Devices Up", f"{devices_up}", f"{(devices_up/total*100):.0f}%" if total > 0 else "0%")
        
        with col3:
            st.metric("Devices Down", data.get("devices_down", 0))
        
        with col4:
            st.metric("Active Alerts", data.get("active_alerts", 0))
    else:
        st.warning("Could not fetch dashboard data. Make sure the API server is running.")
        st.info(f"API URL: {config.API_BASE_URL}")


if __name__ == "__main__":
    main()
