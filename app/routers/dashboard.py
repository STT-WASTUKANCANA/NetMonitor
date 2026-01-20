"""
Dashboard router with GMT+7 Jakarta timezone support.
"""
from datetime import datetime, timedelta
from typing import Optional
from fastapi import APIRouter, Depends, Query
from sqlalchemy.orm import Session
from sqlalchemy import func, case

from app.database import get_db
from app.models import Device, DeviceLog, Alert
from app.models.user import User
from app.middleware.auth import get_current_user
from app.utils.time_manager import TimeManager



router = APIRouter(prefix="/api/dashboard", tags=["Dashboard"])


@router.get("/summary", response_model=dict)
async def get_dashboard_summary(
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Get dashboard summary statistics.
    """
    # Device counts
    total_devices = db.query(func.count(Device.id)).scalar() or 0
    devices_up = db.query(func.count(Device.id)).filter(Device.status == 'up').scalar() or 0
    devices_down = db.query(func.count(Device.id)).filter(Device.status == 'down').scalar() or 0
    devices_unknown = db.query(func.count(Device.id)).filter(Device.status == 'unknown').scalar() or 0
    
    # Alert counts
    active_alerts = db.query(func.count(Alert.id)).filter(Alert.status == 'active').scalar() or 0
    critical_alerts = db.query(func.count(Alert.id)).filter(
        Alert.status == 'active',
        Alert.severity == 'critical'
    ).scalar() or 0
    high_alerts = db.query(func.count(Alert.id)).filter(
        Alert.status == 'active',
        Alert.severity == 'high'
    ).scalar() or 0
    resolved_alerts = db.query(func.count(Alert.id)).filter(Alert.status == 'resolved').scalar() or 0
    
    # Average response time (last 7 days)
    seven_days_ago = datetime.utcnow() - timedelta(days=7)
    avg_response_time = db.query(func.avg(DeviceLog.response_time)).filter(
        DeviceLog.checked_at >= seven_days_ago,
        DeviceLog.response_time.isnot(None)
    ).scalar()
    
    # Uptime percentage (last 7 days)
    total_checks = db.query(func.count(DeviceLog.id)).filter(
        DeviceLog.checked_at >= seven_days_ago
    ).scalar() or 0
    up_checks = db.query(func.count(DeviceLog.id)).filter(
        DeviceLog.checked_at >= seven_days_ago,
        DeviceLog.status == 'up'
    ).scalar() or 0
    uptime_percentage = (up_checks / total_checks * 100) if total_checks > 0 else 0
    
    # Get current Jakarta time for response
    jakarta_now = TimeManager.get_current_time()
    
    return {
        "success": True,
        "data": {
            "total_devices": total_devices,
            "devices_up": devices_up,
            "devices_down": devices_down,
            "devices_unknown": devices_unknown,
            "active_alerts": active_alerts,
            "critical_alerts": critical_alerts,
            "high_alerts": high_alerts,
            "resolved_alerts": resolved_alerts,
            "avg_response_time_7days": round(float(avg_response_time), 2) if avg_response_time else None,
            "uptime_percentage": round(uptime_percentage, 2),
            "last_updated": jakarta_now.isoformat(),
            "last_updated_formatted": TimeManager.format_timestamp(jakarta_now, "%Y-%m-%d %H:%M:%S %Z")
        }
    }


@router.get("/metrics", response_model=dict)
async def get_network_metrics(
    period: str = Query("7d", description="Time period: 24h, 7d, 30d, 90d"),
    device_id: Optional[int] = Query(None, description="Specific device ID"),
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Get network health metrics with chart data in GMT+7 timezone.
    Returns accurate timestamps and aggregated data based on period.
    """
    # Get period boundaries (in UTC for database queries)
    start_date = TimeManager.get_period_start_time(period)
    jakarta_now = TimeManager.get_current_time()
    
    # Build base query
    query = db.query(DeviceLog).filter(DeviceLog.checked_at >= start_date)
    if device_id:
        query = query.filter(DeviceLog.device_id == device_id)
    
    # Get statistics
    total_checks = query.count()
    up_count = query.filter(DeviceLog.status == 'up').count()
    down_count = query.filter(DeviceLog.status == 'down').count()
    
    # Response time stats
    response_stats = db.query(
        func.avg(DeviceLog.response_time).label('avg'),
        func.min(DeviceLog.response_time).label('min'),
        func.max(DeviceLog.response_time).label('max')
    ).filter(
        DeviceLog.checked_at >= start_date,
        DeviceLog.response_time.isnot(None)
    )
    if device_id:
        response_stats = response_stats.filter(DeviceLog.device_id == device_id)
    stats = response_stats.first()
    
    # Packet loss average
    packet_loss_avg = db.query(func.avg(DeviceLog.packet_loss)).filter(
        DeviceLog.checked_at >= start_date,
        DeviceLog.packet_loss.isnot(None)
    )
    if device_id:
        packet_loss_avg = packet_loss_avg.filter(DeviceLog.device_id == device_id)
    avg_packet_loss = packet_loss_avg.scalar()
    
    # Calculate uptime percentage
    uptime_percentage = (up_count / total_checks * 100) if total_checks > 0 else 0
    
    # Generate chart data with GMT+7 labels
    chart_labels = []
    chart_labels_full = []  # Full timestamps for tooltips
    response_time_data = []
    up_count_data = []
    down_count_data = []
    
    # Calculate time bucket format and size based on period
    if period == "24h":
        # Hourly buckets
        time_format = "%Y-%m-%d %H:00:00"
        delta = timedelta(hours=1)
        num_buckets = 24
        label_format = "%H:%M"
        full_label_format = "%Y-%m-%d %H:%M %Z"
    elif period == "7d":
        # Daily buckets
        time_format = "%Y-%m-%d 00:00:00"
        delta = timedelta(days=1)
        num_buckets = 7
        label_format = "%a %m-%d"
        full_label_format = "%Y-%m-%d %Z"
    elif period == "30d":
        # Daily buckets
        time_format = "%Y-%m-%d 00:00:00"
        delta = timedelta(days=1)
        num_buckets = 30
        label_format = "%m-%d"
        full_label_format = "%Y-%m-%d %Z"
    elif period == "90d":
        # Weekly buckets (approximate via Python loop for simpler handling)
        # For simplicity in SQL, we'll stick to daily averaging then aggregate in Python or 
        # use a weekly format if MySQL supports it easily (%x-%v). 
        # Let's use simple daily for 90d to ensure smoothness, or week.
        # WEEK() mode 1: Monday is first day
        time_format = "%Y-%u" # Week number
        delta = timedelta(weeks=1)
        num_buckets = 13
        label_format = "W%W"
        full_label_format = "Week %W %Y"

    # --- SQL Aggregation ---
    
    # 1. Inner Query: Count stats per unique scan cycle (checked_at)
    # We group by specific timestamp to get accurate snapshot counts
    subquery = db.query(
        DeviceLog.checked_at,
        func.sum(case((DeviceLog.status == 'up', 1), else_=0)).label('ups'),
        func.sum(case((DeviceLog.status == 'down', 1), else_=0)).label('downs'),
        func.avg(DeviceLog.response_time).label('avg_resp')
    ).filter(
        DeviceLog.checked_at >= start_date
    )
    
    if device_id:
        subquery = subquery.filter(DeviceLog.device_id == device_id)
        
    subquery = subquery.group_by(DeviceLog.checked_at).subquery()

    # 2. Main Query: Average the snapshot counts over the time bucket
    # We need to construct the time bucket in UTC or convert to Jakarta first?
    # Doing conversion in Python is safer for DB portability, but for aggregation we need DB time.
    # We will assume the DB handles UTC. We group by the formatted date string.
    
    # Note: DATE_FORMAT is MySQL specific.
    # We will fetch the raw subquery results and aggregate in Python for maximum reliability 
    # across timezones and to ensure zero-filling for missing buckets.
    
    # Fetch all snapshot data
    snapshots = db.query(
        subquery.c.checked_at,
        subquery.c.ups,
        subquery.c.downs,
        subquery.c.avg_resp
    ).all()
    
    # Process in Python to strictly align with Jakarta Time buckets
    # This avoids complex SQL timezone math and ensures synchronization
    
    # Initialize buckets
    bucket_data = {}
    current_bucket_time = jakarta_now.replace(minute=0, second=0, microsecond=0)
    
    if period == "24h":
         pass # Start from current hour
    elif period in ["7d", "30d", "90d"]:
        current_bucket_time = current_bucket_time.replace(hour=0)
    
    # Create slots
    for i in range(num_buckets):
        if period == "24h":
            t = current_bucket_time - timedelta(hours=i)
            key = t.strftime("%Y-%m-%d %H")
        elif period in ["7d", "30d"]:
            t = current_bucket_time - timedelta(days=i)
            key = t.strftime("%Y-%m-%d")
        elif period == "90d":
            t = current_bucket_time - timedelta(weeks=i)
            key = t.strftime("%Y-%U")
            
        bucket_data[key] = {
            "time": t,
            "ups": [],
            "downs": [],
            "resps": []
        }
        
    # Fill buckets
    # from app.utils.timezone import utc_to_jakarta # Removed
    
    for snap in snapshots:
        chk_time = TimeManager.to_jakarta(snap.checked_at)
        
        if period == "24h":
            key = chk_time.strftime("%Y-%m-%d %H")
        elif period in ["7d", "30d"]:
            key = chk_time.strftime("%Y-%m-%d")
        elif period == "90d":
            key = chk_time.strftime("%Y-%U")
            
        if key in bucket_data:
            bucket_data[key]["ups"].append(snap.ups or 0)
            bucket_data[key]["downs"].append(snap.downs or 0)
            if snap.avg_resp:
                bucket_data[key]["resps"].append(snap.avg_resp)

    # Calculate averages and format output
    # Sort correctly (oldest to newest)
    sorted_keys = sorted(bucket_data.keys())
    
    for key in sorted_keys:
        data = bucket_data[key]
        t_obj = data["time"]
        
        # Labels
        chart_labels.append(t_obj.strftime(label_format))
        chart_labels_full.append(t_obj.strftime(full_label_format))
        
        # Averages
        if data["ups"]:
            avg_up = sum(data["ups"]) / len(data["ups"])
            avg_down = sum(data["downs"]) / len(data["downs"])
            up_count_data.append(round(avg_up, 1))
            down_count_data.append(round(avg_down, 1))
        else:
            up_count_data.append(0)
            down_count_data.append(0)
            
        if data["resps"]:
            avg_resp = sum(data["resps"]) / len(data["resps"])
            response_time_data.append(round(avg_resp, 2))
        else:
            response_time_data.append(None)
    
    return {
        "success": True,
        "data": {
            "period": period,
            "device_id": device_id,
            "period_start": TimeManager.format_timestamp(TimeManager.to_jakarta(start_date), "%Y-%m-%d %H:%M:%S %Z"),
            "period_end": TimeManager.format_timestamp(jakarta_now, "%Y-%m-%d %H:%M:%S %Z"),
            "metrics": {
                "uptime_percentage": round(uptime_percentage, 2),
                "total_checks": total_checks,
                "up_count": up_count,
                "down_count": down_count,
                "avg_response_time": round(float(stats.avg), 2) if stats.avg else None,
                "min_response_time": round(float(stats.min), 2) if stats.min else None,
                "max_response_time": round(float(stats.max), 2) if stats.max else None,
                "avg_packet_loss": round(float(avg_packet_loss), 2) if avg_packet_loss else None
            },
            "chart_data": {
                "labels": chart_labels,
                "labels_full": chart_labels_full,
                "response_time": response_time_data,
                "up_count": up_count_data,
                "down_count": down_count_data
            }
        }
    }


@router.get("/recent-alerts", response_model=dict)
async def get_recent_alerts(
    limit: int = Query(10, ge=1, le=50),
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Get recent active alerts for dashboard.
    """
    alerts = db.query(Alert).filter(
        Alert.status == 'active'
    ).order_by(Alert.created_at.desc()).limit(limit).all()
    
    alert_list = [alert.to_dict(include_device=True) for alert in alerts]
    
    return {
        "success": True,
        "data": alert_list
    }


@router.get("/device-hierarchy", response_model=dict)
async def get_device_hierarchy(
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Get device hierarchy tree for dashboard visualization.
    """
    # Get root devices (level utama, no parent)
    root_devices = db.query(Device).filter(
        Device.hierarchy_level == 'utama',
        Device.parent_id.is_(None)
    ).all()
    
    def build_tree(device):
        return {
            "id": device.id,
            "name": device.name,
            "ip_address": device.ip_address,
            "type": device.type,
            "status": device.status,
            "status_icon": device.status_icon,
            "type_icon": device.type_icon,
            "location": device.location,
            "children": [build_tree(child) for child in device.children]
        }
    
    hierarchy = [build_tree(device) for device in root_devices]
    
    return {
        "success": True,
        "data": hierarchy
    }
