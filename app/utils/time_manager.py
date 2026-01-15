from datetime import datetime, timezone
import pytz

class TimeManager:
    @staticmethod
    def get_current_time(tz='Asia/Jakarta'):
        """Dapatkan waktu sekarang dengan timezone"""
        try:
            return datetime.now(pytz.timezone(tz))
        except pytz.UnknownTimeZoneError:
            # Fallback to UTC if timezone is invalid
            return datetime.now(timezone.utc)
    
    @staticmethod
    def get_utc_time():
        """Dapatkan waktu UTC/GMT"""
        return datetime.now(timezone.utc)
    
    @staticmethod
    def format_timestamp(dt, format='%Y-%m-%d %H:%M:%S'):
        """Format datetime ke string"""
        if dt is None:
            return ""
        # If the datetime is naive, we assume it's in Jakarta timezone
        if dt.tzinfo is None:
            jakarta_tz = pytz.timezone('Asia/Jakarta')
            dt = jakarta_tz.localize(dt)
        else:
            # Convert to Jakarta timezone if it's in a different timezone
            dt = dt.astimezone(pytz.timezone('Asia/Jakarta'))
        return dt.strftime(format)

    @staticmethod
    def get_period_start_time(period):
        """Calculate start time for a period (e.g. 24h, 7d)"""
        from datetime import timedelta
        
        now = TimeManager.get_current_time()
        
        period_map = {
            "24h": timedelta(hours=24),
            "7d": timedelta(days=7),
            "30d": timedelta(days=30),
            "90d": timedelta(days=90)
        }
        
        time_delta = period_map.get(period, timedelta(days=7))
        start_time = now - time_delta
        
        # Convert to UTC for database queries if needed, or keep as aware
        return start_time.astimezone(timezone.utc)
    
    @staticmethod
    def to_jakarta(dt):
        """Convert to Jakarta Timezone"""
        tz = pytz.timezone('Asia/Jakarta')
        if dt.tzinfo is None:
             dt = pytz.utc.localize(dt)
        return dt.astimezone(tz)

    @staticmethod
    def to_utc(dt):
        """Convert to UTC Timezone"""
        if dt.tzinfo is None:
             # If naive datetime, assume it's in Jakarta timezone as the monitor sends Jakarta time
             tz = pytz.timezone('Asia/Jakarta')
             dt = tz.localize(dt)
        return dt.astimezone(timezone.utc)
