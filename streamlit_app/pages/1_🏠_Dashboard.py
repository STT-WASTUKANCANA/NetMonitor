"""
Dashboard Page - NetMonitor
Displays real-time network statistics, metrics, and alerts.
"""
import streamlit as st
import sys
from pathlib import Path
import time
from datetime import datetime
import pytz

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).parent.parent.parent))

from streamlit_app.utils.ui import setup_page_config, sidebar_user_card
from streamlit_app.config import config
from streamlit_app.utils import init_session_state, is_authenticated, require_auth, get_current_user, logout
from streamlit_app.utils.timezone import get_period_display_name
from app.utils.time_manager import TimeManager
from streamlit_app.utils.api_client import api_client
from streamlit_app.components import (
    metric_card, status_badge, create_response_time_chart,
    create_device_status_chart, create_uptime_gauge
)


def setup_page():
    """Setup page configuration."""
    setup_page_config(
        title=f"Dashboard - {config.PAGE_TITLE}",
        icon=config.PAGE_ICON,
        layout=config.LAYOUT
    )


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
    """Dashboard main function."""
    init_session_state()
    require_auth()
    setup_page()
    
    # Set token
    if st.session_state.token:
        api_client.set_token(st.session_state.token)
    
    render_sidebar()
    
    # Period Selector and Title
    col_title, col_period = st.columns([3, 1])
    
    with col_title:
        st.title("📊 Dashboard")
        st.markdown("Real-time network monitoring overview")
    
    with col_period:
        # Period selector
        selected_period = st.selectbox(
            "Time Period",
            options=["24h", "7d", "30d", "90d"],
            format_func=get_period_display_name,
            index=["24h", "7d", "30d", "90d"].index(st.session_state.get('dashboard_period', '7d')),
            key="dashboard_period_selector"
        )
        # Update session state if changed
        if selected_period != st.session_state.get('dashboard_period', '7d'):
            st.session_state.dashboard_period = selected_period
            st.rerun()
            
    # Use selected period from session state
    period = st.session_state.get('dashboard_period', '7d')
    
    # Auto-refresh placeholder
    refresh_placeholder = st.empty()
    
    # Fetch dashboard data
    summary = api_client.get_dashboard_summary()
    metrics = api_client.get_dashboard_metrics(period=period)
    
    if not summary.get("success"):
        st.error("Could not fetch dashboard data. Please check API connection.")
        return
    
    data = summary.get("data", {})
    
    # Statistics Cards
    st.markdown("### 📈 Network Statistics")
    
    col1, col2, col3, col4 = st.columns(4)
    
    with col1:
        metric_card(
            title="Total Devices",
            value=str(data.get("total_devices") or 0),
            subtitle="Monitored devices",
            icon="📡"
        )
    
    with col2:
        metric_card(
            title="Devices UP",
            value=str(data.get("devices_up") or 0),
            subtitle=f"{float(data.get('uptime_percentage') or 0):.1f}% uptime",
            icon="✅",
            color="#064E3B"
        )
    
    with col3:
        devices_down = int(data.get("devices_down") or 0)
        metric_card(
            title="Devices DOWN",
            value=str(devices_down),
            subtitle="Requires attention" if devices_down > 0 else "All systems operational",
            icon="❌",
            color="#7F1D1D" if devices_down > 0 else "#1E293B"
        )
    
    with col4:
        active_alerts = int(data.get("active_alerts") or 0)
        critical = int(data.get("critical_alerts") or 0)
        metric_card(
            title="Active Alerts",
            value=str(active_alerts),
            subtitle=f"{critical} critical" if critical > 0 else "No critical alerts",
            icon="🔔",
            color="#7F1D1D" if critical > 0 else "#1E293B"
        )
    
    st.markdown("---")
    
    # Charts Row
    st.markdown("---")
    
    if metrics.get("success"):
        metrics_data = metrics.get("data", {})
        chart_data = metrics_data.get("chart_data", {})
        period_start = metrics_data.get("period_start", "")
        
        st.markdown(f"### 📈 Network Performance ({get_period_display_name(period)})")
        st.caption(f"Monitoring from {period_start} to Now (GMT+7)")
        
        col_chart1, col_chart2 = st.columns(2)
        
        with col_chart1:
            st.markdown("#### Response Time Trend")
            labels = chart_data.get("labels", [])
            full_labels = chart_data.get("labels_full", [])
            response_time = chart_data.get("response_time", [])
            
            if labels and response_time:
                fig = create_response_time_chart(labels, response_time, full_labels)
                st.plotly_chart(fig, use_container_width=True)
            else:
                st.info("No response time data available for this period")
        
        with col_chart2:
            st.markdown("#### Device Status Over Time")
            labels = chart_data.get("labels", [])
            full_labels = chart_data.get("labels_full", [])
            up_data = chart_data.get("up_count", [])
            down_data = chart_data.get("down_count", [])
            
            if labels:
                fig = create_device_status_chart(labels, up_data, down_data, full_labels)
                st.plotly_chart(fig, use_container_width=True)
            else:
                st.info("No status data available for this period")
    else:
        st.warning("Could not load metrics data")
    
    st.markdown("---")
    
    # Last updated timestamp with real-time indicator
    st.markdown("---")
    col_time1, col_time2, col_time3 = st.columns([2, 1, 1])

    with col_time1:
        # Use standardized timezone utility
        jakarta_now = TimeManager.get_current_time()
        current_time = TimeManager.format_timestamp(jakarta_now, '%Y-%m-%d %H:%M:%S WIB')
        st.markdown(f"🕒 **Current Time (WIB):** {current_time}")

    with col_time2:
        st.markdown(f"📡 **Monitoring:** <span style='color: #10B981;'>●</span> Active (30s interval)", unsafe_allow_html=True)

    with col_time3:
        st.markdown(f"🔄 **Auto-refresh:** <span style='color: #3B82F6;'>●</span> {config.DASHBOARD_REFRESH}s", unsafe_allow_html=True)
    
    # Auto-refresh
    time.sleep(config.DASHBOARD_REFRESH)
    st.rerun()


if __name__ == "__main__":
    main()
