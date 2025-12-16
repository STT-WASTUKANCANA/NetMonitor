"""
Timezone utilities for Streamlit Frontend.
"""
from datetime import datetime, timedelta
import pytz


def get_jakarta_timezone():
    """Get Jakarta timezone object."""
    return pytz.timezone('Asia/Jakarta')


def get_jakarta_now() -> datetime:
    """Get current time in Jakarta timezone."""
    return datetime.now(get_jakarta_timezone())


def format_jakarta_time(dt_str: str, fmt: str = "%Y-%m-%d %H:%M:%S") -> str:
    """
    Format ISO timestamp string (which might be in UTC or Jakarta) to Jakarta time string.
    
    Args:
        dt_str: ISO format datetime string
        fmt: Output format string
    
    Returns:
        Formatted datetime string in Jakarta timezone
    """
    if not dt_str:
        return "-"
        
    try:
        # Parse ISO string
        dt = datetime.fromisoformat(dt_str.replace('Z', '+00:00'))
        
        # If naive, assume UTC if ending in Z was stripped, or Jakarta if local
        if dt.tzinfo is None:
            # We assume backend returns UTC usually
            dt = pytz.UTC.localize(dt)
            
        # Convert to Jakarta
        jakarta_tz = get_jakarta_timezone()
        jakarta_dt = dt.astimezone(jakarta_tz)
        
        return jakarta_dt.strftime(fmt)
    except Exception as e:
        return dt_str


def get_period_display_name(period: str) -> str:
    """Get friendly name for period code."""
    mapping = {
        "24h": "Last 24 Hours",
        "7d": "Last 7 Days",
        "30d": "Last 30 Days",
        "90d": "Last 3 Months"
    }
    return mapping.get(period, period)
