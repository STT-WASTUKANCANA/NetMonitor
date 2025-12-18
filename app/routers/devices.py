"""
Devices router.
"""
from datetime import datetime
from typing import Optional
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.orm import Session
from sqlalchemy import func
from sqlalchemy.exc import OperationalError

from app.database import get_db
from app.models import Device, DeviceLog, Alert
# Import manager from main (circular import is tricky, but main imports routers, not vice versa usually.
# However, here manager is in main. Simple solution: put manager in separate file or import inside function)

from app.models.user import User
from app.schemas.device import (
    DeviceCreate, DeviceUpdate, DeviceResponse, DeviceStatusUpdate,
    DeviceListResponse, DeviceChildResponse
)
from app.middleware.auth import get_current_user, get_current_admin_user


router = APIRouter(prefix="/api/devices", tags=["Devices"])


@router.get("", response_model=dict)
async def list_devices(
    status: Optional[str] = Query(None, description="Filter by status (up, down, unknown)"),
    type: Optional[str] = Query(None, description="Filter by type"),
    hierarchy_level: Optional[str] = Query(None, description="Filter by hierarchy level"),
    per_page: int = Query(15, ge=1, le=100),
    page: int = Query(1, ge=1),
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Get all devices with optional filtering and pagination.
    """
    try:
        query = db.query(Device)
        
        # Apply filters
        if status:
            query = query.filter(Device.status == status)
        if type:
            query = query.filter(Device.type == type)
        if hierarchy_level:
            query = query.filter(Device.hierarchy_level == hierarchy_level)
        
        # Get total count
        total = query.count()
        
        # Apply pagination
        offset = (page - 1) * per_page
        devices = query.offset(offset).limit(per_page).all()
        
        # Build response
        device_list = []
        for device in devices:
            device_data = device.to_dict()
            device_data["parent"] = DeviceChildResponse(
                id=device.parent.id,
                name=device.parent.name,
                ip_address=device.parent.ip_address,
                status=device.parent.status
            ).model_dump() if device.parent else None
            device_data["children"] = [
                DeviceChildResponse(
                    id=child.id,
                    name=child.name,
                    ip_address=child.ip_address,
                    status=child.status
                ).model_dump() for child in device.children
            ]
            device_list.append(device_data)
        
        return {
            "success": True,
            "data": {
                "current_page": page,
                "per_page": per_page,
                "total": total,
                "data": device_list
            }
        }
    
    except OperationalError:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Database service is currently unavailable"
        )
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="An unexpected error occurred"
        )


@router.get("/{device_id}", response_model=dict)
async def get_device(
    device_id: int,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Get a single device by ID.
    """
    device = db.query(Device).filter(Device.id == device_id).first()
    
    if not device:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Device not found"
        )
    
    device_data = device.to_dict(include_relations=True)
    
    # Add recent logs
    recent_logs = db.query(DeviceLog).filter(
        DeviceLog.device_id == device_id
    ).order_by(DeviceLog.checked_at.desc()).limit(10).all()
    device_data["recent_logs"] = [log.to_dict() for log in recent_logs]
    
    # Add active alerts
    active_alerts = db.query(Alert).filter(
        Alert.device_id == device_id,
        Alert.status == 'active'
    ).order_by(Alert.created_at.desc()).all()
    device_data["active_alerts"] = [alert.to_dict() for alert in active_alerts]
    
    return {
        "success": True,
        "data": device_data
    }


@router.post("", response_model=dict, status_code=status.HTTP_201_CREATED)
async def create_device(
    device_data: DeviceCreate,
    current_user: User = Depends(get_current_admin_user),
    db: Session = Depends(get_db)
):
    """
    Create a new device. Admin only.
    """
    # Check if IP already exists
    existing = db.query(Device).filter(Device.ip_address == device_data.ip_address).first()
    if existing:
        raise HTTPException(
            status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
            detail="IP address already exists"
        )
    
    # Check parent exists if specified
    if device_data.parent_id:
        parent = db.query(Device).filter(Device.id == device_data.parent_id).first()
        if not parent:
            raise HTTPException(
                status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
                detail="Parent device not found"
            )
    
    # Create device
    device = Device(
        name=device_data.name,
        ip_address=device_data.ip_address,
        type=device_data.type.value,
        hierarchy_level=device_data.hierarchy_level.value,
        parent_id=device_data.parent_id,
        location=device_data.location,
        description=device_data.description,
        port=device_data.port,
        status='unknown',
        created_by=current_user.id
    )
    
    db.add(device)
    db.commit()
    db.refresh(device)
    
    return {
        "success": True,
        "message": "Device created successfully",
        "data": device.to_dict()
    }


@router.put("/{device_id}", response_model=dict)
async def update_device(
    device_id: int,
    device_data: DeviceUpdate,
    current_user: User = Depends(get_current_admin_user),
    db: Session = Depends(get_db)
):
    """
    Update a device. Admin only.
    """
    device = db.query(Device).filter(Device.id == device_id).first()
    
    if not device:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Device not found"
        )
    
    # Check IP uniqueness if changed
    if device_data.ip_address and device_data.ip_address != device.ip_address:
        existing = db.query(Device).filter(
            Device.ip_address == device_data.ip_address,
            Device.id != device_id
        ).first()
        if existing:
            raise HTTPException(
                status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
                detail="IP address already exists"
            )
    
    # Update fields
    update_data = device_data.model_dump(exclude_unset=True)
    for key, value in update_data.items():
        if value is not None:
            if hasattr(value, 'value'):  # Handle enums
                setattr(device, key, value.value)
            else:
                setattr(device, key, value)
    
    db.commit()
    db.refresh(device)
    
    return {
        "success": True,
        "message": "Device updated successfully",
        "data": device.to_dict()
    }


@router.delete("/{device_id}", response_model=dict)
async def delete_device(
    device_id: int,
    current_user: User = Depends(get_current_admin_user),
    db: Session = Depends(get_db)
):
    """
    Delete a device and its children (cascade). Admin only.
    """
    device = db.query(Device).filter(Device.id == device_id).first()
    
    if not device:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Device not found"
        )
    
    db.delete(device)
    db.commit()
    
    return {
        "success": True,
        "message": "Device deleted successfully"
    }


@router.post("/status", response_model=dict)
async def update_device_status(
    status_data: DeviceStatusUpdate,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Update device status from monitoring script.
    Creates a log entry and alert if status changes.
    """
    try:
        device = db.query(Device).filter(Device.id == status_data.device_id).first()
        
        if not device:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Device not found"
            )
        
        old_status = device.status
        new_status = status_data.status.value
    
        # Update device status
        device.status = new_status
        
        # Ensure timestamp is stored in UTC to prevent double-shifting
        # Monitor sends Jakarta time, but DB implies UTC storage for metric queries
        from app.utils.time_manager import TimeManager
        checked_at_utc = TimeManager.to_utc(status_data.checked_at)
        
        device.last_checked_at = checked_at_utc
        
        # Create log entry
        log = DeviceLog(
            device_id=device.id,
            status=new_status,
            response_time=status_data.response_time,
            packet_loss=status_data.packet_loss,
            checked_at=checked_at_utc
        )
        db.add(log)
        
        # Create alert if status is down
        # Logic:
        # 1. If status CHANGED to down -> Alert
        # 2. If status IS down (persisting) AND no active alert exists (meaning it was resolved) -> Re-Alert
        
        alert_created = False
        alert_data = None
        
        if new_status == 'down':
            # Check for existing active/acknowledged alert
            existing_alert = db.query(Alert).filter(
                Alert.device_id == device.id,
                Alert.status.in_(['active', 'acknowledged'])
            ).first()
            
            should_alert = False
            
            if old_status != 'down':
                # New failure
                should_alert = True
            elif not existing_alert:
                # Persisting failure but no active alert (was resolved?)
                should_alert = True
                
            if should_alert and not existing_alert:
                # Determine severity based on hierarchy
                if device.hierarchy_level == 'utama':
                    severity = 'critical'
                elif device.hierarchy_level == 'sub':
                    severity = 'high'
                else:
                    severity = 'medium'
                
                alert_msg = f"{device.name} tidak merespon (down)"
                if old_status == 'down':
                    alert_msg += " - Issue Persisting (Re-Triggered)"
                
                alert = Alert(
                    device_id=device.id,
                    message=alert_msg,
                    severity=severity,
                    status='active'
                )
                db.add(alert)
                alert_created = True
                db.flush()
                alert_data = {
                    "id": alert.id,
                    "message": alert.message,
                    "severity": alert.severity
                }
        
        db.commit()

        # Broadcast update via WebSocket
        try:
            from app.main import manager
            pass # manager imported
            
            # Prepare broadcast message
            import asyncio
            from datetime import datetime
            
            # Need to reconstruct alert_data if not present but strictly needed? 
            # Actually frontend handles optional alert.
            
            message = {
                "type": "device_status_update",
                "data": {
                    "device_id": device.id,
                    "device_name": device.name,
                    "status": new_status,
                    "old_status": old_status,
                    "alert": alert_data,
                    "timestamp": datetime.utcnow().isoformat()
                }
            }
            
            await manager.broadcast(message)
            
        except Exception as e:
            print(f"WebSocket broadcast error: {e}")
        
        return {
            "success": True,
            "message": "Device status updated successfully",
            "data": {
                "device_id": device.id,
                "old_status": old_status,
                "new_status": new_status,
                "alert_created": alert_created,
                "alert": alert_data
            }
        }
    
    except OperationalError:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Database service is currently unavailable"
        )
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="An unexpected error occurred"
        )


@router.get("/{device_id}/logs", response_model=dict)
async def get_device_logs(
    device_id: int,
    from_date: Optional[str] = Query(None, alias="from"),
    to_date: Optional[str] = Query(None, alias="to"),
    status: Optional[str] = None,
    per_page: int = Query(50, ge=1, le=200),
    page: int = Query(1, ge=1),
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Get logs for a specific device with filtering and statistics.
    """
    device = db.query(Device).filter(Device.id == device_id).first()
    
    if not device:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Device not found"
        )
    
    query = db.query(DeviceLog).filter(DeviceLog.device_id == device_id)
    
    # Apply filters
    if from_date:
        query = query.filter(DeviceLog.checked_at >= from_date)
    if to_date:
        query = query.filter(DeviceLog.checked_at <= to_date)
    if status:
        query = query.filter(DeviceLog.status == status)
    
    # Get total and statistics
    total = query.count()
    
    # Calculate statistics
    stats_query = db.query(
        func.count(DeviceLog.id).label('total_checks'),
        func.sum(func.if_(DeviceLog.status == 'up', 1, 0)).label('up_count'),
        func.sum(func.if_(DeviceLog.status == 'down', 1, 0)).label('down_count'),
        func.avg(DeviceLog.response_time).label('avg_response_time'),
        func.min(DeviceLog.response_time).label('min_response_time'),
        func.max(DeviceLog.response_time).label('max_response_time'),
        func.avg(DeviceLog.packet_loss).label('avg_packet_loss')
    ).filter(DeviceLog.device_id == device_id)
    
    if from_date:
        stats_query = stats_query.filter(DeviceLog.checked_at >= from_date)
    if to_date:
        stats_query = stats_query.filter(DeviceLog.checked_at <= to_date)
    
    stats = stats_query.first()
    
    # Get paginated logs
    offset = (page - 1) * per_page
    logs = query.order_by(DeviceLog.checked_at.desc()).offset(offset).limit(per_page).all()
    
    # Calculate uptime percentage
    total_checks = stats.total_checks or 0
    up_count = stats.up_count or 0
    uptime_percentage = (up_count / total_checks * 100) if total_checks > 0 else 0
    
    return {
        "success": True,
        "data": {
            "device": {
                "id": device.id,
                "name": device.name,
                "ip_address": device.ip_address
            },
            "logs": {
                "current_page": page,
                "per_page": per_page,
                "total": total,
                "data": [log.to_dict() for log in logs]
            },
            "statistics": {
                "total_checks": total_checks,
                "up_count": up_count,
                "down_count": stats.down_count or 0,
                "uptime_percentage": round(uptime_percentage, 2),
                "avg_response_time": round(float(stats.avg_response_time), 2) if stats.avg_response_time else None,
                "min_response_time": round(float(stats.min_response_time), 2) if stats.min_response_time else None,
                "max_response_time": round(float(stats.max_response_time), 2) if stats.max_response_time else None,
                "avg_packet_loss": round(float(stats.avg_packet_loss), 2) if stats.avg_packet_loss else None
            }
        }
    }
