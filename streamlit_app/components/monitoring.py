"""
Real-time monitoring dashboard component.
"""
import streamlit as st
from datetime import datetime
from typing import Dict
import time


def monitoring_dashboard(api_client):
    """
    Display real-time monitoring dashboard.
    Shows API health, database status, and application metrics.
    """
    st.subheader("🔍 Real-time System Monitoring")
    
    # Get health status
    health = api_client.health_check()
    
    col1, col2, col3 = st.columns(3)
    
    with col1:
        api_status = "🟢 Healthy" if health.get("success") else "🔴 Down"
        st.markdown(f"""
        <div style="
            background-color: #FFFFFF;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #E2E8F0;
            text-align: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        ">
            <h4 style="color: #64748B; margin: 0;">API Status</h4>
            <p style="font-size: 1.5rem; margin: 0.5rem 0;">{api_status}</p>
            <small style="color: #475569;">FastAPI Backend</small>
        </div>
        """, unsafe_allow_html=True)
    
    with col2:
        components = health.get("components", {}) if health.get("success") else {}
        db_status = "🟢 Healthy" if components.get("database") == "healthy" else "🔴 Unhealthy"
        st.markdown(f"""
        <div style="
            background-color: #FFFFFF;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #E2E8F0;
            text-align: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        ">
            <h4 style="color: #64748B; margin: 0;">Database</h4>
            <p style="font-size: 1.5rem; margin: 0.5rem 0;">{db_status}</p>
            <small style="color: #475569;">MySQL Connection</small>
        </div>
        """, unsafe_allow_html=True)
    
    with col3:
        overall = health.get("status", "unknown")
        overall_display = "🟢 Healthy" if overall == "healthy" else "🟡 Degraded" if overall == "degraded" else "🔴 Down"
        st.markdown(f"""
        <div style="
            background-color: #FFFFFF;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #E2E8F0;
            text-align: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        ">
            <h4 style="color: #64748B; margin: 0;">Overall Health</h4>
            <p style="font-size: 1.5rem; margin: 0.5rem 0;">{overall_display}</p>
            <small style="color: #475569;">System Status</small>
        </div>
        """, unsafe_allow_html=True)
    
    # Last updated
    from app.utils.time_manager import TimeManager

    jakarta_time = TimeManager.get_current_time()
    current_time = TimeManager.format_timestamp(jakarta_time, '%H:%M:%S WIB')

    st.markdown(f"""
    <p style="text-align: right; color: #64748B; font-size: 0.75rem; margin-top: 1rem;">
        Last updated: {current_time}
    </p>
    """, unsafe_allow_html=True)


def api_metrics_panel(summary_data: Dict):
    """Display API performance metrics."""
    st.markdown("### 📊 API Performance Metrics")
    
    metrics = summary_data.get("data", {})
    
    col1, col2, col3, col4 = st.columns(4)
    
    with col1:
        avg_response = metrics.get("avg_response_time_7days")
        display_val = f"{avg_response:.1f}ms" if avg_response else "N/A"
        st.metric("Avg Response Time (7d)", display_val)
    
    with col2:
        uptime = metrics.get("uptime_percentage", 0)
        st.metric("Uptime", f"{uptime:.1f}%")
    
    with col3:
        total_devices = metrics.get("total_devices", 0)
        st.metric("Monitored Devices", total_devices)
    
    with col4:
        active_alerts = metrics.get("active_alerts", 0)
        st.metric("Active Alerts", active_alerts)
