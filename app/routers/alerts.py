"""
Alerts router.
"""
from datetime import datetime
from typing import Optional
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.orm import Session

from app.database import get_db
from app.models import Alert, Device
from app.models.user import User
from app.schemas.alert import AlertUpdate, AlertBulkUpdate, AlertResponse
from app.middleware.auth import get_current_user


router = APIRouter(prefix="/api/alerts", tags=["Alerts"])


@router.get("", response_model=dict)
async def list_alerts(
    status: Optional[str] = Query(None, description="Filter by status"),
    severity: Optional[str] = Query(None, description="Filter by severity"),
    device_id: Optional[int] = Query(None, description="Filter by device ID"),
    per_page: int = Query(20, ge=1, le=100),
    page: int = Query(1, ge=1),
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Get all alerts with optional filtering and pagination.
    """
    query = db.query(Alert)
    
    # Apply filters
    if status:
        query = query.filter(Alert.status == status)
    if severity:
        query = query.filter(Alert.severity == severity)
    if device_id:
        query = query.filter(Alert.device_id == device_id)
    
    # Get total count
    total = query.count()
    
    # Apply pagination and ordering
    offset = (page - 1) * per_page
    alerts = query.order_by(Alert.created_at.desc()).offset(offset).limit(per_page).all()
    
    # Build response with device info
    alert_list = []
    for alert in alerts:
        alert_data = alert.to_dict(include_device=True, include_resolver=True)
        alert_list.append(alert_data)
    
    return {
        "success": True,
        "data": {
            "current_page": page,
            "per_page": per_page,
            "total": total,
            "data": alert_list
        }
    }


@router.get("/{alert_id}", response_model=dict)
async def get_alert(
    alert_id: int,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Get a single alert by ID.
    """
    alert = db.query(Alert).filter(Alert.id == alert_id).first()
    
    if not alert:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Alert not found"
        )
    
    return {
        "success": True,
        "data": alert.to_dict(include_device=True, include_resolver=True)
    }


@router.patch("/{alert_id}", response_model=dict)
async def update_alert(
    alert_id: int,
    alert_data: AlertUpdate,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Update alert status (acknowledge or resolve).
    """
    alert = db.query(Alert).filter(Alert.id == alert_id).first()
    
    if not alert:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Alert not found"
        )
    
    # Update status
    new_status = alert_data.status.value
    alert.status = new_status
    
    # If resolving, set resolver info
    if new_status == 'resolved':
        alert.resolved_at = datetime.utcnow()
        alert.resolved_by = current_user.id
    
    db.commit()
    db.refresh(alert)
    
    response_data = {
        "id": alert.id,
        "status": alert.status,
    }
    
    if alert.resolved_at:
        response_data["resolved_at"] = alert.resolved_at.isoformat()
        response_data["resolved_by"] = {
            "id": current_user.id,
            "first_name": current_user.first_name,
            "last_name": current_user.last_name
        }
    
    return {
        "success": True,
        "message": "Alert status updated successfully",
        "data": response_data
    }


@router.post("/bulk-update", response_model=dict)
async def bulk_update_alerts(
    bulk_data: AlertBulkUpdate,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """
    Bulk update multiple alerts.
    """
    alerts = db.query(Alert).filter(Alert.id.in_(bulk_data.alert_ids)).all()
    
    if not alerts:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="No alerts found"
        )
    
    new_status = bulk_data.status.value
    updated_ids = []
    
    for alert in alerts:
        alert.status = new_status
        if new_status == 'resolved':
            alert.resolved_at = datetime.utcnow()
            alert.resolved_by = current_user.id
        updated_ids.append(alert.id)
    
    db.commit()
    
    return {
        "success": True,
        "message": f"{len(updated_ids)} alerts updated successfully",
        "data": {
            "updated_count": len(updated_ids),
            "alert_ids": updated_ids
        }
    }
