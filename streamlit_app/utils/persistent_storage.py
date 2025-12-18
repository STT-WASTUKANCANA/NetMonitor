"""
Persistent storage utilities using browser localStorage.
Allows session persistence across page refreshes.
"""
import streamlit as st
import streamlit.components.v1 as components
import json
from typing import Optional, Dict, Any


def _inject_storage_script():
    """Inject JavaScript for localStorage communication."""
    return """
    <script>
    // Store data in localStorage
    function storeData(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (e) {
            console.error('Error storing data:', e);
            return false;
        }
    }
    
    // Retrieve data from localStorage
    function retrieveData(key) {
        try {
            const value = localStorage.getItem(key);
            return value ? JSON.parse(value) : null;
        } catch (e) {
            console.error('Error retrieving data:', e);
            return null;
        }
    }
    
    // Clear specific key from localStorage
    function clearData(key) {
        try {
            localStorage.removeItem(key);
            return true;
        } catch (e) {
            console.error('Error clearing data:', e);
            return false;
        }
    }
    
    // Clear all NetMonitor data
    function clearAllData() {
        try {
            const keys = Object.keys(localStorage);
            keys.forEach(key => {
                if (key.startsWith('netmonitor_')) {
                    localStorage.removeItem(key);
                }
            });
            return true;
        } catch (e) {
            console.error('Error clearing all data:', e);
            return false;
        }
    }
    
    // Expose functions to window for Streamlit communication
    window.storageHelper = {
        store: storeData,
        retrieve: retrieveData,
        clear: clearData,
        clearAll: clearAllData
    };
    
    // Send stored data to Streamlit on page load
    window.addEventListener('load', function() {
        const token = retrieveData('netmonitor_token');
        const user = retrieveData('netmonitor_user');
        const tokenExpiry = retrieveData('netmonitor_token_expiry');
        const lastActivity = retrieveData('netmonitor_last_activity');
        
        if (token || user) {
            // Store in a global variable that Streamlit can access
            window.netmonitorStoredData = {
                token: token,
                user: user,
                token_expiry: tokenExpiry,
                last_activity: lastActivity
            };
        }
    });
    </script>
    """


def save_to_storage(key: str, value: Any) -> None:
    """
    Save data to browser localStorage.
    
    Args:
        key: Storage key (will be prefixed with 'netmonitor_')
        value: Value to store (will be JSON serialized)
    """
    storage_key = f"netmonitor_{key}"
    value_json = json.dumps(value)
    
    # Inject storage script and save
    html = f"""
    {_inject_storage_script()}
    <script>
    if (window.storageHelper) {{
        window.storageHelper.store('{storage_key}', {value_json});
    }}
    </script>
    """
    components.html(html, height=0)


def get_from_storage(key: str) -> Optional[Any]:
    """
    Get data from browser localStorage via query params.
    
    Args:
        key: Storage key (will be prefixed with 'netmonitor_')
        
    Returns:
        Stored value or None
    """
    storage_key = f"netmonitor_{key}"
    
    # Try to get from session state cache first
    cache_key = f"_storage_cache_{storage_key}"
    if cache_key in st.session_state:
        return st.session_state[cache_key]
    
    return None


def clear_from_storage(key: str) -> None:
    """
    Clear specific key from browser localStorage.
    
    Args:
        key: Storage key (will be prefixed with 'netmonitor_')
    """
    storage_key = f"netmonitor_{key}"
    
    html = f"""
    {_inject_storage_script()}
    <script>
    if (window.storageHelper) {{
        window.storageHelper.clear('{storage_key}');
    }}
    </script>
    """
    components.html(html, height=0)
    
    # Clear from session state cache
    cache_key = f"_storage_cache_{storage_key}"
    if cache_key in st.session_state:
        del st.session_state[cache_key]


def clear_all_storage() -> None:
    """Clear all NetMonitor data from browser localStorage."""
    html = f"""
    {_inject_storage_script()}
    <script>
    if (window.storageHelper) {{
        window.storageHelper.clearAll();
    }}
    </script>
    """
    components.html(html, height=0)
    
    # Clear all cached storage from session state
    keys_to_delete = [k for k in st.session_state.keys() if k.startswith('_storage_cache_')]
    for key in keys_to_delete:
        del st.session_state[key]


def init_storage_loader() -> Dict[str, Any]:
    """
    Initialize storage loader and retrieve stored data on page load.
    Should be called once at the start of the app.
    
    Returns:
        Dictionary with stored session data
    """
    # Check if we already loaded storage this session
    if "_storage_loaded" in st.session_state:
        return st.session_state.get("_stored_session_data", {})
    
    # Load stored data via JavaScript
    html = f"""
    {_inject_storage_script()}
    <script>
    // Retrieve and pass data to parent via query params
    const token = localStorage.getItem('netmonitor_token');
    const user = localStorage.getItem('netmonitor_user');
    const tokenExpiry = localStorage.getItem('netmonitor_token_expiry');
    const lastActivity = localStorage.getItem('netmonitor_last_activity');
    
    // Store in session storage for this page load
    if (token) {{
        sessionStorage.setItem('_temp_token', token);
    }}
    if (user) {{
        sessionStorage.setItem('_temp_user', user);
    }}
    if (tokenExpiry) {{
        sessionStorage.setItem('_temp_token_expiry', tokenExpiry);
    }}
    if (lastActivity) {{
        sessionStorage.setItem('_temp_last_activity', lastActivity);
    }}
    </script>
    """
    components.html(html, height=0)
    
    # Mark as loaded
    st.session_state._storage_loaded = True
    st.session_state._stored_session_data = {}
    
    return {}


def save_session_to_storage(token: str, user: Dict, token_expiry: str, last_activity: str) -> None:
    """
    Save complete session data to browser localStorage.
    
    Args:
        token: JWT access token
        user: User data dictionary
        token_expiry: Token expiry timestamp (ISO format)
        last_activity: Last activity timestamp (ISO format)
    """
    # Save each piece of data
    html = f"""
    {_inject_storage_script()}
    <script>
    if (window.storageHelper) {{
        window.storageHelper.store('netmonitor_token', {json.dumps(token)});
        window.storageHelper.store('netmonitor_user', {json.dumps(user)});
        window.storageHelper.store('netmonitor_token_expiry', {json.dumps(token_expiry)});
        window.storageHelper.store('netmonitor_last_activity', {json.dumps(last_activity)});
    }}
    </script>
    """
    components.html(html, height=0)


def load_session_from_storage() -> Optional[Dict[str, Any]]:
    """
    Load session data from browser localStorage.
    Returns dict with token, user, token_expiry, last_activity or None.
    """
    # Read from JavaScript and check if data exists
    html = f"""
    {_inject_storage_script()}
    <div id="storage-data" style="display:none;"></div>
    <script>
    const token = localStorage.getItem('netmonitor_token');
    const user = localStorage.getItem('netmonitor_user');
    const tokenExpiry = localStorage.getItem('netmonitor_token_expiry');
    const lastActivity = localStorage.getItem('netmonitor_last_activity');
    
    if (token && user) {{
        const dataDiv = document.getElementById('storage-data');
        dataDiv.setAttribute('data-token', token);
        dataDiv.setAttribute('data-user', user);
        dataDiv.setAttribute('data-token-expiry', tokenExpiry || '');
        dataDiv.setAttribute('data-last-activity', lastActivity || '');
        
        // Also send to parent via postMessage
        window.parent.postMessage({{
            type: 'netmonitor_session',
            token: JSON.parse(token),
            user: JSON.parse(user),
            tokenExpiry: tokenExpiry ? JSON.parse(tokenExpiry) : null,
            lastActivity: lastActivity ? JSON.parse(lastActivity) : null
        }}, '*');
    }}
    </script>
    """
    
    result = components.html(html, height=0)
    return result
