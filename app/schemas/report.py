from datetime import datetime
from typing import List, Optional, Dict
from pydantic import BaseModel
from app.schemas.device import DeviceResponse
from app.schemas.alert import AlertResponse
from app.schemas.device_log import DeviceLogResponse

class DeviceStats(BaseModel):
    total_devices: int
    online_count: int
    offline_count: int
    unknown_count: int
    uptime_percentage: float
    avg_response_time: float

class AlertStats(BaseModel):
    total_alerts: int
    active_count: int
    resolved_count: int
    critical_count: int
    high_count: int
    medium_count: int
    low_count: int
    avg_resolution_time_minutes: float

class DailyStat(BaseModel):
    date: str
    uptime: float
    avg_latency: float
    alert_count: int

class DeviceIssue(BaseModel):
    device_id: int
    device_name: str
    ip_address: str
    downtime_minutes: float
    alert_count: int

class ReportDeviceStat(BaseModel):
    id: int
    name: str
    ip_address: str
    type: str
    location: Optional[str] = None
    uptime_percentage: float
    downtime_hours: float
    alert_count: int
    status: str

class ReportData(BaseModel):
    generated_at: str
    period: str
    period_start: str
    period_end: str
    summary: DeviceStats
    alert_stats: AlertStats
    daily_stats: List[DailyStat]
    top_issues: List[DeviceIssue]
    recent_alerts: List[AlertResponse]
    all_devices_stats: List[ReportDeviceStat]
    all_alerts: List[AlertResponse]
    raw_data: List[DeviceLogResponse]  # Note: This can be large

