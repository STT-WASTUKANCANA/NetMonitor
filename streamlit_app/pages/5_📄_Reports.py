"""
Reports Page - NetMonitor
Generates and displays comprehensive network reports.
"""
import streamlit as st
import sys
from pathlib import Path
import time
from datetime import datetime
import pandas as pd

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).parent.parent.parent))

from streamlit_app.utils.ui import setup_page_config, sidebar_user_card
from streamlit_app.config import config
from streamlit_app.utils import init_session_state, require_auth, get_current_user, logout
from streamlit_app.utils.timezone import get_period_display_name
from streamlit_app.utils.api_client import api_client
from streamlit_app.utils.report_generator import generate_pdf_report, generate_excel_report
from streamlit_app.components import metric_card, create_response_time_chart, create_device_status_chart

def setup_page():
    """Setup page configuration."""
    setup_page_config(
        title=f"Reports - {config.PAGE_TITLE}",
        icon="📄",
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
        
        st.subheader("Report Settings")
        
        # Period selector
        current_period = st.session_state.get('report_period', '7d')
        # Handle state mapping if old values exist
        if current_period not in ["24h", "7d", "30d", "90d", "Custom"]:
             current_period = "7d"
        
        # Map nice labels
        options_map = {
            "24h": "Last 24 Hours",
            "7d": "Last 7 Days",
            "30d": "Last 30 Days",
            "90d": "Last 3 Months",
            "Custom": "Custom Period"
        }
        
        selected_period = st.radio(
            "Select Period",
            options=["24h", "7d", "30d", "90d", "Custom"],
            format_func=lambda x: options_map.get(x, x),
            index=["24h", "7d", "30d", "90d", "Custom"].index(current_period),
            key="report_period_selector"
        )
        
        # Date picker for Custom
        if selected_period == "Custom":
            today = datetime.now()
            default_val = (today.replace(day=1) if today.day > 1 else today, today)
            if 'custom_date_range' in st.session_state:
                default_val = st.session_state.custom_date_range
            
            dates = st.date_input(
                "Select Date Range",
                value=default_val,
                max_value=today,
                key="custom_date_range"
            )
        
        # Info Preview
        st.info("Report will include uptime, alerts, and performance metrics for the selected range.")
        
        if selected_period != st.session_state.get('report_period', '7d'):
            st.session_state.report_period = selected_period
            st.rerun()
            
        st.markdown("---")
        
        if st.button("🚪 Logout", use_container_width=True):
            logout()
            st.rerun()

def main():
    """Reports main function."""
    init_session_state()
    require_auth()
    setup_page()
    
    # Set token
    if st.session_state.token:
        api_client.set_token(st.session_state.token)
    
    render_sidebar()
    
    period = st.session_state.get('report_period', '7d')
    start_date = None
    end_date = None
    period_display = period
    
    # Calculate display string
    if period == "Custom":
        dates = st.session_state.get('custom_date_range')
        if dates and len(dates) == 2:
            start_date = dates[0].strftime("%Y-%m-%d")
            end_date = dates[1].strftime("%Y-%m-%d")
            period_display = f"{start_date} to {end_date}"
        else:
            st.warning("Please select both start and end dates in the sidebar.")
            return
    else:
        period_display = get_period_display_name(period)

    col_title = st.container()
    with col_title:
        st.title("📄 Network Reports")
        st.markdown(f"Generated for period: **{period_display}**")
        
    # Generate Section
    st.markdown("### Generate Report")
    
    # If we have data cached in session state for this exact period, we could use it, 
    # but requirement implies "Generate" button action.
    # To follow requirements: Show Generate Buttons -> Progress -> Download.
    
    # But current structure fetches data first then shows preview. behavior is slightly different.
    # Requirement: "Generate Buttons... Disable if processing... Progress Indicator... Download Section"
    # So we should NOT auto-fetch on load. We wait for button click.
    
    col_gen1, col_gen2 = st.columns(2)
    with col_gen1:
        gen_pdf = st.button("📄 Generate PDF Report", use_container_width=True, type="primary")
    with col_gen2:
        gen_excel = st.button("📊 Generate Excel Report", use_container_width=True)
    
    if gen_pdf or gen_excel:
        status_text = st.empty()
        progress_bar = st.progress(0)
        
        try:
            # Step 1: Fetching
            status_text.text("Fetching monitoring data...")
            progress_bar.progress(20)
            time.sleep(0.5) # UX delay
            
            data = api_client.get_report_data(period=period, start_date=start_date, end_date=end_date)
            
            if not data or "generated_at" not in data:
                 status_text.error("Failed to fetch data.")
                 return
                 
            # Step 2: Processing
            status_text.text("Processing charts and visualizations...")
            progress_bar.progress(50)
            
            # Step 3: Building Report
            status_text.text(f"Building {'PDF' if gen_pdf else 'Excel'} report...")
            progress_bar.progress(80)
            
            # Generate valid filename
            if period == "Custom" and start_date and end_date:
                file_period = f"Custom_{start_date.replace('-', '')}_to_{end_date.replace('-', '')}"
            else:
                ts = datetime.now().strftime('%Y%m%d')
                file_period = f"{period.upper()}_{ts}"
                
            report_bytes = None
            file_name = ""
            mime_type = ""
            
            if gen_pdf:
                report_bytes = generate_pdf_report(data)
                file_name = f"NetMonitor_{file_period}.pdf"
                mime_type = "application/pdf"
            else:
                report_bytes = generate_excel_report(data)
                file_name = f"NetMonitor_{file_period}.xlsx"
                mime_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            
            progress_bar.progress(100)
            status_text.success("Report generated successfully!")
            time.sleep(1)
            status_text.empty()
            progress_bar.empty()
            
            # Show download button prominently
            st.warning("⬇️ **Download your report below:**")
            st.download_button(
                label=f"📥 Download {file_name}",
                data=report_bytes,
                file_name=file_name,
                mime=mime_type,
                use_container_width=True,
                type="primary"
            )
            
            # Show preview summaries
            st.markdown("---")
            st.subheader("📊 Report Preview")
            
            summary = data.get('summary', {})
            alert_stats = data.get('alert_stats', {})
            
            c1, c2, c3, c4 = st.columns(4)
            with c1:
                metric_card("Uptime", f"{summary.get('uptime_percentage')}%", "Avail", "✅")
            with c2:
                metric_card("Avg Latency", f"{summary.get('avg_response_time')} ms", "Resp", "⚡")
            with c3:
                metric_card("Total Alerts", str(alert_stats.get('total_alerts')), f"{alert_stats.get('critical_count')} crit", "🔔")
            with c4:
                metric_card("MTTR", f"{alert_stats.get('avg_resolution_time_minutes')} min", "Fix Time", "⏱️")
                
        except Exception as e:
            st.error(f"Error generating report: {str(e)}")
            
    # Initial state (no report generated yet)
    else:
        st.info("👈 Select a period from the sidebar and click Generate to create a report.")
if __name__ == "__main__":
    main()
