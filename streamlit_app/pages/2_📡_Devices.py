"""
Devices Page - NetMonitor
Manage network devices - list, create, edit, delete.
"""
import streamlit as st
import sys
from pathlib import Path
import pandas as pd

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).parent.parent.parent))

from streamlit_app.utils.ui import setup_page_config, sidebar_user_card
from streamlit_app.config import config
from streamlit_app.utils import init_session_state, is_authenticated, require_auth, get_current_user, is_admin, logout
from streamlit_app.utils.api_client import api_client
from streamlit_app.components import status_badge
from streamlit_app.utils.autorefresh import auto_refresh


def setup_page():
    """Setup page configuration."""
    setup_page_config(
        title=f"Devices - {config.PAGE_TITLE}",
        icon="📡",
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


def render_device_form(device=None, parent_options=None):
    """Render device create/edit form."""
    is_edit = device is not None
    
    with st.form("device_form"):
        st.markdown(f"### {'Edit' if is_edit else 'Add New'} Device")
        
        col1, col2 = st.columns(2)
        
        with col1:
            name = st.text_input(
                "Device Name *",
                value=device.get("name", "") if device else "",
                placeholder="Router Utama Gedung A"
            )
            
            ip_address = st.text_input(
                "IP Address *",
                value=device.get("ip_address", "") if device else "",
                placeholder="192.168.1.1"
            )
            
            device_type = st.selectbox(
                "Device Type *",
                options=["router", "switch", "access_point", "server", "firewall", "other"],
                index=["router", "switch", "access_point", "server", "firewall", "other"].index(
                    device.get("type", "router")
                ) if device else 0
            )
        
        with col2:
            hierarchy = st.selectbox(
                "Hierarchy Level *",
                options=["utama", "sub", "device"],
                index=["utama", "sub", "device"].index(
                    device.get("hierarchy_level", "device")
                ) if device else 2
            )
            
            parent_id = st.selectbox(
                "Parent Device",
                options=[None] + (parent_options or []),
                format_func=lambda x: "None (Root Device)" if x is None else f"{x['name']} ({x['ip_address']})",
                index=0
            )
            
            location = st.text_input(
                "Location",
                value=device.get("location", "") if device else "",
                placeholder="Ruang Server Lt.1"
            )
        
        description = st.text_area(
            "Description",
            value=device.get("description", "") if device else "",
            placeholder="Optional description..."
        )
        
        port = st.number_input(
            "Port (optional)",
            min_value=0,
            max_value=65535,
            value=device.get("port", 0) if device else 0,
            help="Port for monitoring (0 = not set)"
        )
        
        submitted = st.form_submit_button(
            "Update Device" if is_edit else "Create Device",
            use_container_width=True
        )
        
        if submitted:
            if not name or not ip_address:
                st.error("Name and IP Address are required")
                return None
            
            form_data = {
                "name": name,
                "ip_address": ip_address,
                "type": device_type,
                "hierarchy_level": hierarchy,
                "parent_id": parent_id.get("id") if parent_id else None,
                "location": location or None,
                "description": description or None,
                "port": port if port > 0 else None
            }
            
            return form_data
    
    return None


def main():
    """Devices main function."""
    init_session_state()
    require_auth()
    setup_page()
    
    # Set token
    if st.session_state.token:
        api_client.set_token(st.session_state.token)
    
    render_sidebar()
    
    # Page title
    st.title("📡 Devices Management")
    
    # Auto-refresh devices list every 10 seconds
    auto_refresh(interval_seconds=10, key="devices_refresh")
    
    # Auto Discovery Section (for admins only)
    if is_admin():
        st.markdown("### 🔍 Auto Discovery Network Devices")
        
        col1, col2 = st.columns([3, 1])
        with col1:
            st.info("Auto Discovery Network Devices")
        with col2:
            if st.button("🚀 Scan Network", use_container_width=True, type="primary"):
                # Start discovery
                with st.spinner("🔍 Starting network scan..."):
                    result = api_client.post("/api/devices/discover", {})
                    if result.get("success"):
                        st.session_state.discovery_running = True
                        st.session_state.discovery_started = True
                        st.rerun()
                    else:
                        msg = result.get("message", "Failed to start discovery")
                        # If already running, sync state
                        if "already running" in msg.lower():
                            st.warning("Discovery is already running in background. Attaching...")
                            st.session_state.discovery_running = True
                            st.session_state.discovery_started = True
                            import time
                            time.sleep(1)
                            st.rerun()
                        else:
                            st.error(msg)
        
        # Show scanning animation and auto-process
        if st.session_state.get("discovery_running") or st.session_state.get("discovery_started"):
            with st.container():
                st.markdown("---")
                
                # Check status
                status_response = api_client.get("/api/devices/discover/status")
                if status_response.get("success"):
                    status_data = status_response.get("data", {})
                    
                    if status_data.get("running"):
                        # Show scanning animation
                        col1, col2, col3 = st.columns([1, 2, 1])
                        with col2:
                            st.markdown("### 🔄 Scanning Network...")
                            progress = status_data.get("progress", 0)
                            st.progress(progress / 100)
                            st.markdown(f"**Progress: {progress}%**")
                            st.markdown("*Detecting routers, gateways, and switches...*")
                        
                        # Auto-refresh every 2 seconds
                        import time
                        time.sleep(2)
                        st.rerun()
                    
                    else:
                        # Scan complete - auto-save
                        if st.session_state.get("discovery_started"):
                            results_response = api_client.get("/api/devices/discover/results")
                            if results_response.get("success"):
                                results_data = results_response.get("data", {})
                                discovered_devices = results_data.get("devices", [])
                                
                                # Filter only utama and sub
                                filtered_devices = [d for d in discovered_devices if d.get("hierarchy_level") in ["utama", "sub"]]
                                
                                if filtered_devices:
                                    st.success(f"✅ Found {len(discovered_devices)} devices! Saving {len(filtered_devices)} critical devices (Utama/Sub)...")
                                    
                                    # Show table
                                    df_data = []
                                    for device in filtered_devices:
                                        df_data.append({
                                            "Name": device.get("name"),
                                            "IP": device.get("ip_address"),
                                            "Type": device.get("type", "").replace("_", " ").title(),
                                            "Level": device.get("hierarchy_level", "").title(),
                                            "MAC": device.get("mac_address", "N/A")
                                        })
                                    
                                    if df_data:
                                        df = pd.DataFrame(df_data)
                                        st.dataframe(df, use_container_width=True, hide_index=True)
                                    
                                    # Auto-save
                                    with st.spinner("💾 Saving devices to database..."):
                                        save_response = api_client.post("/api/devices/discover/save", discovered_devices)
                                        if save_response.get("success"):
                                            saved_data = save_response.get("data", {})
                                            st.success(f"🎉 Saved {saved_data.get('saved', 0)} device(s)! (Skipped {saved_data.get('skipped', 0)} existing, Filtered {saved_data.get('filtered', 0)} regular devices)")
                                            st.balloons()
                                            
                                            # Clear state
                                            st.session_state.discovery_running = False
                                            st.session_state.discovery_started = False
                                            
                                            # Clear results
                                            api_client.post("/api/devices/discover/clear", {})
                                            
                                            # Wait then refresh
                                            import time
                                            time.sleep(2)
                                            st.rerun()
                                        else:
                                            st.error(save_response.get("message", "Failed to save devices"))
                                            st.session_state.discovery_started = False
                                else:
                                    st.warning("⚠️ No Utama or Sub devices found. Only router/gateway and switch devices are saved.")
                                    st.session_state.discovery_running = False
                                    st.session_state.discovery_started = False
                                    
                        if status_data.get("error"):
                            st.error(f"❌ Discovery error: {status_data.get('error')}")
                            st.session_state.discovery_running = False
                            st.session_state.discovery_started = False
                
                st.markdown("---")
    
    # Tabs
    tab1, tab2 = st.tabs(["📋 Device List", "➕ Add Device"])
    
    # Tab 1: Device List
    with tab1:
        # Filters
        col1, col2, col3, col4 = st.columns(4)
        
        with col1:
            status_filter = st.selectbox(
                "Status",
                options=[None, "up", "down", "unknown"],
                format_func=lambda x: "All Status" if x is None else x.upper()
            )
        
        with col2:
            type_filter = st.selectbox(
                "Type",
                options=[None, "router", "switch", "access_point", "server", "firewall", "other"],
                format_func=lambda x: "All Types" if x is None else x.replace("_", " ").title()
            )
        
        with col3:
            hierarchy_filter = st.selectbox(
                "Hierarchy",
                options=[None, "utama", "sub", "device"],
                format_func=lambda x: "All Levels" if x is None else x.title()
            )
        
        with col4:
            per_page = st.selectbox("Per Page", options=[10, 25, 50, 100], index=1)
        
        # Fetch devices
        params = {"per_page": per_page, "page": 1}
        if status_filter:
            params["status"] = status_filter
        if type_filter:
            params["type"] = type_filter
        if hierarchy_filter:
            params["hierarchy_level"] = hierarchy_filter
        
        response = api_client.get_devices(**params)
        
        if response.get("success"):
            data = response.get("data", {})
            devices = data.get("data", [])
            total = data.get("total", 0)
            
            st.markdown(f"*Showing {len(devices)} of {total} devices*")
            
            if devices:
                # Create DataFrame
                df_data = []
                for device in devices:
                    status_icon = "✅" if device.get("status") == "up" else "❌" if device.get("status") == "down" else "❓"
                    type_icons = {
                        "router": "🌐",
                        "switch": "🔌",
                        "access_point": "📶",
                        "server": "🖥️",
                        "firewall": "🛡️",
                        "other": "📡"
                    }
                    
                    df_data.append({
                        "Status": status_icon,
                        "Name": device.get("name"),
                        "IP Address": device.get("ip_address"),
                        "Type": f"{type_icons.get(device.get('type'), '📡')} {device.get('type', '').replace('_', ' ').title()}",
                        "Hierarchy": device.get("hierarchy_level", "").title(),
                        "Location": device.get("location", "-"),
                        "Last Check": device.get("last_checked_at", "Never")[:19] if device.get("last_checked_at") else "Never"
                    })
                
                df = pd.DataFrame(df_data)
                st.dataframe(df, use_container_width=True, hide_index=True)
                
                # Device actions (for admins)
                if is_admin():
                    st.markdown("---")
                    st.markdown("### 🔧 Device Actions")
                    
                    device_options = {f"{d.get('name')} ({d.get('ip_address')})": d for d in devices}
                    selected_device = st.selectbox(
                        "Select Device",
                        options=list(device_options.keys())
                    )
                    
                    if selected_device:
                        device = device_options[selected_device]
                        
                        col1, col2, col3 = st.columns(3)
                        
                        with col1:
                            if st.button("📝 View Details", use_container_width=True):
                                st.session_state.view_device_id = device.get("id")
                        
                        with col2:
                            if st.button("✏️ Edit", use_container_width=True):
                                st.session_state.edit_device_id = device.get("id")
                        
                        with col3:
                            if st.button("🗑️ Delete", type="primary", use_container_width=True):
                                if st.session_state.get("confirm_delete") == device.get("id"):
                                    result = api_client.delete_device(device.get("id"))
                                    if result.get("success"):
                                        st.success("Device deleted!")
                                        st.session_state.confirm_delete = None
                                        st.rerun()
                                    else:
                                        st.error(result.get("message", "Delete failed"))
                                else:
                                    st.session_state.confirm_delete = device.get("id")
                                    st.warning("Click Delete again to confirm")
            else:
                st.info("No devices found matching your filters")
        else:
            st.error(response.get("message", "Could not fetch devices"))
    
    # Tab 2: Add Device
    with tab2:
        if not is_admin():
            st.warning("Only administrators can add devices")
        else:
            # Get parent device options
            all_devices = api_client.get_devices(per_page=100)
            parent_options = []
            if all_devices.get("success"):
                parent_options = [
                    {"id": d["id"], "name": d["name"], "ip_address": d["ip_address"]}
                    for d in all_devices.get("data", {}).get("data", [])
                ]
            
            form_data = render_device_form(parent_options=parent_options)
            
            if form_data:
                result = api_client.create_device(form_data)
                
                if result.get("success"):
                    st.success("Device created successfully!")
                    st.balloons()
                else:
                    st.error(result.get("message", "Failed to create device"))


if __name__ == "__main__":
    main()
