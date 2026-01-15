
import streamlit as st
from streamlit_autorefresh import st_autorefresh
from streamlit_app.config import config

def auto_refresh(interval_seconds: int = None, key: str = "global_refresh"):
    """
    Setup automatic refresh for the page.
    
    Args:
        interval_seconds (int, optional): Refresh interval in seconds. 
                                          Defaults to config.DASHBOARD_REFRESH.
        key (str): Unique key for the autorefresh component.
    """
    if interval_seconds is None:
        interval_seconds = config.DASHBOARD_REFRESH
        
    # Convert to milliseconds
    interval_ms = interval_seconds * 1000
    
    # st_autorefresh returns the number of times it has refreshed.
    # We don't necessarily need the value, just the side effect of rerun.
    count = st_autorefresh(interval=interval_ms, limit=None, key=key)
    return count
