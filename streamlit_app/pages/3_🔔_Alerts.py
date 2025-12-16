"""
Alerts Page - NetMonitor
View and manage network alerts.
"""
import streamlit as st
import sys
from pathlib import Path
import pandas as pd
from datetime import datetime

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).parent.parent.parent))

from streamlit_app.utils.ui import setup_page_config
from streamlit_app.config import config
from streamlit_app.utils import init_session_state, is_authenticated, require_auth, get_current_user, logout
from streamlit_app.utils.api_client import api_client


def setup_page():
    """Setup page configuration."""
    setup_page_config(
        title=f"Alerts - {config.PAGE_TITLE}",
        icon="🔔",
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
        
        if st.button("🚪 Logout", use_container_width=True):
            logout()
            st.rerun()


def check_new_alerts():
    """Check for new alerts and show toast notifications."""
    if 'last_alert_check' not in st.session_state:
        st.session_state.last_alert_check = None
        st.session_state.shown_alert_ids = set()
    
    # Get recent alerts
    alerts_response = api_client.get_recent_alerts(limit=10)
    
    if alerts_response.get("success"):
        alerts = alerts_response.get("data", [])
        
        for alert in alerts:
            alert_id = alert.get("id")
            
            # Only show new alerts not shown before
            if alert_id not in st.session_state.shown_alert_ids:
                severity = alert.get("severity", "medium")
                message = alert.get("message", "Device alert")
                device_name = alert.get("device", {}).get("name", "Unknown")
                
                # Show toast notification based on severity
                if severity == "critical":
                    st.error(f"🚨 CRITICAL ALERT: {device_name} - {message}", icon="🔴")
                elif severity == "high":
                    st.warning(f"⚠️ HIGH ALERT: {device_name} - {message}", icon="🟠")
                else:
                    st.info(f"ℹ️ Alert: {device_name} - {message}", icon="🔵")
                
                # Mark as shown
                st.session_state.shown_alert_ids.add(alert_id)


def format_time_ago(iso_string: str) -> str:
    """Format ISO timestamp as relative time."""
    try:
        dt = datetime.fromisoformat(iso_string.replace('Z', '+00:00'))
        now = datetime.now(dt.tzinfo) if dt.tzinfo else datetime.now()
        diff = now - dt
        
        if diff.days > 0:
            return f"{diff.days}d ago"
        elif diff.seconds >= 3600:
            return f"{diff.seconds // 3600}h ago"
        elif diff.seconds >= 60:
            return f"{diff.seconds // 60}m ago"
        else:
            return "Just now"
    except:
        return iso_string


def main():
    """Alerts main function."""
    init_session_state()
    require_auth()
    setup_page()
    
    # Set token
    if st.session_state.token:
        api_client.set_token(st.session_state.token)
    
    render_sidebar()
    
    # Page title
    st.title("🔔 Alerts Management")
    st.markdown("View and manage network alerts")
    
    # Check for new alerts (Toast notifications)
    check_new_alerts()
    
    # Filters
    col1, col2, col3, col4 = st.columns(4)
    
    with col1:
        status_filter = st.selectbox(
            "Status",
            options=[None, "active", "acknowledged", "resolved"],
            format_func=lambda x: "All Status" if x is None else x.title(),
            index=1  # Default to active
        )
    
    with col2:
        severity_filter = st.selectbox(
            "Severity",
            options=[None, "critical", "high", "medium", "low"],
            format_func=lambda x: "All Severity" if x is None else x.title()
        )
    
    with col3:
        per_page = st.selectbox("Per Page", options=[10, 25, 50], index=1)
    
    with col4:
        if st.button("🔄 Refresh", use_container_width=True):
            st.rerun()
    
    st.markdown("---")
    
    # Fetch alerts
    params = {"per_page": per_page, "page": 1}
    if status_filter:
        params["status"] = status_filter
    if severity_filter:
        params["severity"] = severity_filter
    
    response = api_client.get_alerts(**params)
    
    if response.get("success"):
        data = response.get("data", {})
        alerts = data.get("data", [])
        total = data.get("total", 0)
        
        # Summary stats (Fetch from global summary for accuracy)
        summary_response = api_client.get_dashboard_summary()
        summary_data = summary_response.get("data", {}) if summary_response.get("success") else {}
        
        col1, col2, col3, col4 = st.columns(4)
        
        with col1:
            st.metric("Total Alerts", total)
        
        with col2:
            st.metric("Critical", summary_data.get("critical_alerts", 0))
        
        with col3:
            st.metric("Active", summary_data.get("active_alerts", 0))
        
        with col4:
            st.metric("Resolved", summary_data.get("resolved_alerts", 0))
        
        st.markdown("---")
        
        if alerts:
            # Bulk actions
            if status_filter == "active":
                st.markdown("### 🔧 Bulk Actions")
                col1, col2 = st.columns(2)
                
                with col1:
                    if st.button("✅ Acknowledge All", use_container_width=True):
                        alert_ids = [a.get("id") for a in alerts]
                        result = api_client.bulk_update_alerts(alert_ids, "acknowledged")
                        if result.get("success"):
                            st.success(f"Acknowledged {len(alert_ids)} alerts")
                            st.rerun()
                        else:
                            st.error(result.get("message", "Failed"))
                
                with col2:
                    if st.button("🎯 Resolve All", use_container_width=True):
                        alert_ids = [a.get("id") for a in alerts]
                        result = api_client.bulk_update_alerts(alert_ids, "resolved")
                        if result.get("success"):
                            st.success(f"Resolved {len(alert_ids)} alerts")
                            st.rerun()
                        else:
                            st.error(result.get("message", "Failed"))
                
                st.markdown("---")
            
            # Alert cards
            st.markdown("### 📋 Alert List")
            
            for alert in alerts:
                device = alert.get("device", {})
                
                severity_colors = {
                    "critical": "#EF4444",
                    "high": "#F97316",
                    "medium": "#F59E0B",
                    "low": "#10B981"
                }
                
                severity_icons = {
                    "critical": "🔴",
                    "high": "🟠",
                    "medium": "🟡",
                    "low": "🟢"
                }
                
                status_icons = {
                    "active": "🔔",
                    "acknowledged": "👁️",
                    "resolved": "✅"
                }
                
                border_color = severity_colors.get(alert.get("severity"), "#334155")
                time_ago = format_time_ago(alert.get("created_at", ""))
                
                with st.container():
                    st.markdown(f"""
                    <div style="
                        background: #F1F5F9;
                        border-radius: 12px;
                        padding: 1rem;
                        margin: 0.5rem 0;
                        border-left: 4px solid {border_color};
                        border-top: 1px solid #E2E8F0;
                        border-right: 1px solid #E2E8F0;
                        border-bottom: 1px solid #E2E8F0;
                        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                    ">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-size: 1.25rem;">
                                    {severity_icons.get(alert.get('severity'), '🔔')}
                                    {status_icons.get(alert.get('status'), '🔔')}
                                </span>
                                <strong style="color: #1A1A1A; margin-left: 0.5rem;">
                                    {device.get('name', 'Unknown Device')}
                                </strong>
                                <span style="color: #64748B; margin-left: 0.5rem;">
                                    ({device.get('ip_address', 'N/A')})
                                </span>
                            </div>
                            <div>
                                <span style="
                                    background: {border_color}30;
                                    color: {border_color};
                                    padding: 0.25rem 0.75rem;
                                    border-radius: 9999px;
                                    font-size: 0.75rem;
                                    font-weight: 500;
                                    margin-right: 0.5rem;
                                ">{alert.get('severity', '').upper()}</span>
                                <span style="color: #64748B; font-size: 0.875rem;">{time_ago}</span>
                            </div>
                        </div>
                        <p style="color: #475569; margin: 0.5rem 0 0 2rem;">{alert.get('message', '')}</p>
                    </div>
                    """, unsafe_allow_html=True)
                    
                    # Action buttons for non-resolved alerts
                    if alert.get("status") != "resolved":
                        col1, col2, col3 = st.columns([1, 1, 4])
                        
                        with col1:
                            if alert.get("status") == "active":
                                if st.button("👁️ Acknowledge", key=f"ack_{alert.get('id')}"):
                                    result = api_client.update_alert(alert.get("id"), {"status": "acknowledged"})
                                    if result.get("success"):
                                        st.success("Acknowledged!")
                                        st.rerun()
                        
                        with col2:
                            if st.button("✅ Resolve", key=f"resolve_{alert.get('id')}"):
                                result = api_client.update_alert(alert.get("id"), {"status": "resolved"})
                                if result.get("success"):
                                    st.success("Resolved!")
                                    st.rerun()
                    
                    st.markdown("")  # Spacer
        else:
            st.success("🎉 No alerts matching your filters!")
    else:
        st.error(response.get("message", "Could not fetch alerts"))


if __name__ == "__main__":
    main()
