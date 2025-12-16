"""
Timezone utility for NetMonitor
Ensures consistent GMT+7 (Jakarta/WIB) timezone across the application.
"""
from datetime import datetime, timedelta
import pytz

# Application timezone
TZ = pytz.timezone('Asia/Jakarta')


def now_jakarta() -> datetime:
    """Get current time in Jakarta timezone (GMT+7)."""
    return datetime.now(TZ)


def to_jakarta(dt: datetime) -> datetime:
    """Convert datetime to Jakarta timezone."""
    if dt.tzinfo is None:
        # Naive datetime - assume UTC
        dt = pytz.UTC.localize(dt)
    return dt.astimezone(TZ)


def format_jakarta(dt: datetime, fmt: str = "%Y-%m-%d %H:%M:%S %Z") -> str:
    """Format datetime in Jakarta timezone."""
    jakarta_dt = to_jakarta(dt)
    return jakarta_dt.strftime(fmt)


def parse_to_jakarta(dt_string: str, fmt: str = "%Y-%m-%d %H:%M:%S") -> datetime:
    """Parse datetime string and convert to Jakarta timezone."""
    dt = datetime.strptime(dt_string, fmt)
    return TZ.localize(dt)


def jakarta_to_utc(dt: datetime) -> datetime:
    """
    Convert Jakarta timezone datetime to UTC.
    
    Args:
        dt: Datetime object in Jakarta timezone (naive or aware)
    
    Returns:
        Timezone-aware datetime in UTC
    """
    if dt is None:
        return None
    
    # If naive, assume Jakarta timezone
    if dt.tzinfo is None:
        dt = TZ.localize(dt)
    
    # Convert to UTC
    return dt.astimezone(pytz.UTC)


def utc_to_jakarta(dt: datetime) -> datetime:
    """
    Convert UTC datetime to Jakarta/WIB timezone (GMT+7).
    Alias for to_jakarta for consistency.
    
    Args:
        dt: Datetime object in UTC (naive or aware)
    
    Returns:
        Timezone-aware datetime in Jakarta timezone
    """
    return to_jakarta(dt)


def get_jakarta_now() -> datetime:
    """
    Get current time in Jakarta/WIB timezone.
    Alias for now_jakarta for consistency.
    
    Returns:
        Current timezone-aware datetime in Jakarta timezone
    """
    return now_jakarta()


def get_utc_now() -> datetime:
    """
    Get current time in UTC (for database operations).
    
    Returns:
        Current timezone-aware datetime in UTC
    """
    return datetime.now(pytz.UTC)


def get_period_start_time(period: str) -> datetime:
    """
    Calculate the start time for a given period from current Jakarta time.
    Returns UTC datetime for database queries.
    
    Args:
        period: Period string ('24h', '7d', '30d', '90d')
    
    Returns:
        Start datetime in UTC (for database queries)
    """
    from datetime import timedelta
    
    now_jakarta = get_jakarta_now()
    
    period_map = {
        "24h": timedelta(hours=24),
        "7d": timedelta(days=7),
        "30d": timedelta(days=30),
        "90d": timedelta(days=90)
    }
    
    time_delta = period_map.get(period, timedelta(days=7))
    start_time_jakarta = now_jakarta - time_delta
    
    # Convert to UTC for database queries
    return jakarta_to_utc(start_time_jakarta)


def get_period_display_name(period: str) -> str:
    """
    Get display-friendly name for period.
    
    Args:
        period: Period string ('24h', '7d', '30d', '90d')
    
    Returns:
        Display name
    """
    period_names = {
        "24h": "Last 24 Hours",
        "7d": "Last 7 Days", 
        "30d": "Last 30 Days",
        "90d": "Last 90 Days"
    }
    
    return period_names.get(period, period)
