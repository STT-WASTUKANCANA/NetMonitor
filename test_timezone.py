#!/usr/bin/env python3
"""
Test script to verify timezone handling in NetMonitor system
"""
import sys
import os
from datetime import datetime
from pathlib import Path

# Add project root to path
project_root = Path(__file__).parent
sys.path.insert(0, str(project_root))

def test_time_managers():
    """Test TimeManager utilities"""
    print("Testing TimeManager utilities...")
    
    from app.utils.time_manager import TimeManager
    from app.utils.timezone import format_jakarta, to_jakarta
    
    # Get current Jakarta time
    jakarta_time = TimeManager.get_current_time()
    print(f"Current Jakarta time: {jakarta_time}")
    
    # Test formatting
    formatted_time = TimeManager.format_timestamp(jakarta_time, '%Y-%m-%d %H:%M:%S WIB')
    print(f"Formatted time: {formatted_time}")
    
    # Check that it includes WIB
    assert 'WIB' in formatted_time, "Time should be formatted with WIB timezone"
    
    # Test the app timezone utility
    fmt_time = format_jakarta(jakarta_time)
    print(f"Using app timezone utility: {fmt_time}")
    assert 'WIB' in fmt_time, "Time should be formatted with WIB timezone"
    
    print("✅ TimeManager tests passed!\n")

def test_monitor_script_timezone():
    """Test monitor script timezone handling"""
    print("Testing monitor script timezone handling...")
    
    from scripts.monitor import now_jakarta
    from app.utils.time_manager import TimeManager
    
    # Get time from different sources
    monitor_time = now_jakarta()
    manager_time = TimeManager.get_current_time()
    
    print(f"Monitor script time: {monitor_time}")
    print(f"TimeManager time: {manager_time}")
    
    # Both should be in Jakarta timezone
    assert monitor_time.tzinfo.zone == 'Asia/Jakarta', "Monitor time should be in Jakarta timezone"
    assert str(monitor_time.tzinfo) == str(manager_time.tzinfo), "Both should use same timezone"
    
    print("✅ Monitor script timezone tests passed!\n")

def test_timezone_conversions():
    """Test timezone conversion utilities"""
    print("Testing timezone conversion utilities...")
    
    from app.utils.time_manager import TimeManager
    import pytz
    from datetime import timezone
    
    # Create a naive datetime
    naive_dt = datetime(2023, 12, 17, 10, 30, 0)
    
    # Convert to UTC (as the API would store)
    utc_time = TimeManager.to_utc(naive_dt)
    print(f"Naive datetime: {naive_dt}")
    print(f"Converted to UTC: {utc_time}")
    
    # Convert back to Jakarta
    jakarta_time = TimeManager.to_jakarta(utc_time)
    print(f"Converted back to Jakarta: {jakarta_time}")
    
    # In Jakarta, time should be 7 hours ahead of UTC
    time_diff = jakarta_time.hour - naive_dt.hour
    # Check if time difference is 7 hours (Jakarta is UTC+7)
    if time_diff < 0:
        time_diff += 24  # Handle day wrap
    assert time_diff == 7, f"Jakarta time should be 7 hours ahead, actual diff: {time_diff}"
    
    print("✅ Timezone conversion tests passed!\n")

def run_all_tests():
    """Run all timezone tests"""
    print("🔍 Running NetMonitor timezone tests...\n")
    
    test_time_managers()
    test_monitor_script_timezone()
    test_timezone_conversions()
    
    print("🎉 All timezone tests passed! System handles GMT+7 Jakarta timezone correctly.")

if __name__ == "__main__":
    run_all_tests()