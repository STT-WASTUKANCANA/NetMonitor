"""
Monitoring Page - NetMonitor
Real-time system health and performance monitoring.
"""
import streamlit as st
import sys
from pathlib import Path
import time
from datetime import datetime

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).parent.parent.parent))

from streamlit_app.utils.ui import setup_page_config
from streamlit_app.config import config
from streamlit_app.utils import init_session_state, is_authenticated, require_auth, get_current_user, logout
from streamlit_app.utils.api_client import api_client
from streamlit_app.components import create_uptime_gauge, create_response_time_chart
from streamlit_app.components.monitoring import monitoring_dashboard, api_metrics_panel


def setup_page():
    """Setup page configuration."""
    setup_page_config(
        title=f"Monitoring - {config.PAGE_TITLE}",
        icon="📈",
        layout=config.LAYOUT
    )


def render_sidebar():
    """Render sidebar."""
    with st.sidebar:
        st.markdown(f"# {config.PAGE_ICON} {config.PAGE_TITLE}")
        st.markdown("---")
        
        user = get_current_user()
        if user:
            st.markdown(f"""
            <div style="
                background: #F1F5F9;
                border-radius: 8px;
                padding: 1rem;
                margin-bottom: 1rem;
                border: 1px solid #E2E8F0;
            ">
                <p style="color: #1A1A1A; font-weight: bold; margin: 0;">
                    {user.get('first_name', '')} {user.get('last_name', '')}
                </p>
                <p style="color: #64748B; font-size: 0.875rem; margin: 0;">
                    {user.get('role', '').capitalize()}
                </p>
            </div>
            """, unsafe_allow_html=True)
        
        st.markdown("---")
        
        # Refresh controls
        st.markdown("### ⚙️ Settings")
        auto_refresh = st.checkbox("Auto Refresh", value=True)
        refresh_interval = st.slider("Refresh Interval (sec)", 5, 30, 10)
        
        st.markdown("---")
        
        if st.button("🚪 Logout", use_container_width=True):
            logout()
            st.rerun()
        
        return auto_refresh, refresh_interval


def main():
    """Monitoring main function."""
    init_session_state()
    require_auth()
    setup_page()
    
    # Set token
    if st.session_state.token:
        api_client.set_token(st.session_state.token)
    
    auto_refresh, refresh_interval = render_sidebar()
    
    # Page title
    st.title("📈 Real-time System Monitoring")
    st.markdown("Monitor API, database, and application health")
    
    # System Health Overview
    monitoring_dashboard(api_client)
    
    st.markdown("---")
    
    # Fetch data
    summary = api_client.get_dashboard_summary()
    metrics_24h = api_client.get_dashboard_metrics(period="24h")
    metrics_7d = api_client.get_dashboard_metrics(period="7d")
    
    if summary.get("success"):
        api_metrics_panel(summary)
    
    st.markdown("---")
    
    # Two column layout for charts
    col1, col2 = st.columns(2)
    
    with col1:
        st.markdown("### 📉 Response Time (24 Hours)")
        
        if metrics_24h.get("success"):
            chart_data = metrics_24h.get("data", {}).get("chart_data", {})
            labels = chart_data.get("labels", [])
            response_time = chart_data.get("response_time", [])
            
            if labels and response_time:
                fig = create_response_time_chart(labels, response_time)
                st.plotly_chart(fig, use_container_width=True)
            else:
                st.info("No data available for the last 24 hours")
    
    with col2:
        st.markdown("### 📈 Uptime (7 Days)")
        
        if metrics_7d.get("success"):
            uptime = metrics_7d.get("data", {}).get("metrics", {}).get("uptime_percentage", 0)
            fig = create_uptime_gauge(uptime)
            st.plotly_chart(fig, use_container_width=True)
    
    st.markdown("---")
    
    # Detailed Metrics Table
    st.markdown("### 📋 Detailed Metrics")
    
    if metrics_7d.get("success"):
        metrics = metrics_7d.get("data", {}).get("metrics", {})
        
        col1, col2, col3, col4 = st.columns(4)
        
        with col1:
            st.metric("Total Checks (7d)", metrics.get("total_checks", 0))
        
        with col2:
            st.metric("UP Count", metrics.get("up_count", 0))
        
        with col3:
            st.metric("DOWN Count", metrics.get("down_count", 0))
        
        with col4:
            packet_loss = metrics.get("avg_packet_loss")
            st.metric("Avg Packet Loss", f"{packet_loss:.2f}%" if packet_loss else "N/A")
    
    # Response time breakdown
    st.markdown("---")
    st.markdown("### ⏱️ Response Time Analysis")
    
    if metrics_7d.get("success"):
        metrics = metrics_7d.get("data", {}).get("metrics", {})
        
        col1, col2, col3 = st.columns(3)
        
        with col1:
            min_rt = metrics.get("min_response_time")
            st.metric("Min Response Time", f"{min_rt:.2f}ms" if min_rt else "N/A")
        
        with col2:
            avg_rt = metrics.get("avg_response_time")
            st.metric("Avg Response Time", f"{avg_rt:.2f}ms" if avg_rt else "N/A")
        
        with col3:
            max_rt = metrics.get("max_response_time")
            st.metric("Max Response Time", f"{max_rt:.2f}ms" if max_rt else "N/A")
    
    # Last updated timestamp
    st.markdown("---")
    st.markdown(f"*Last updated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}*")
    
    # Auto-refresh
    if auto_refresh:
        time.sleep(refresh_interval)
        st.rerun()


if __name__ == "__main__":
    main()
