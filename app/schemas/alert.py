"""
Alert Pydantic schemas.
"""
from datetime import datetime
from typing import Optional, List
from pydantic import BaseModel, Field
from enum import Enum


class AlertSeverity(str, Enum):
    low = "low"
    medium = "medium"
    high = "high"
    critical = "critical"


class AlertStatus(str, Enum):
    active = "active"
    acknowledged = "acknowledged"
    resolved = "resolved"


class AlertBase(BaseModel):
    """Base alert schema."""
    device_id: int
    message: str = Field(..., min_length=1)
    severity: AlertSeverity = AlertSeverity.medium


class AlertCreate(AlertBase):
    """Schema for creating an alert."""
    pass


class AlertUpdate(BaseModel):
    """Schema for updating an alert."""
    status: AlertStatus


class AlertBulkUpdate(BaseModel):
    """Schema for bulk updating alerts."""
    alert_ids: List[int] = Field(..., min_length=1)
    status: AlertStatus


class AlertDeviceInfo(BaseModel):
    """Device info for alert response."""
    id: int
    name: str
    ip_address: str
    location: Optional[str] = None
    type: str
    current_status: str


class AlertResolverInfo(BaseModel):
    """Resolver info for alert response."""
    id: int
    first_name: str
    last_name: str


class AlertResponse(BaseModel):
    """Schema for alert response."""
    id: int
    device_id: int
    message: str
    severity: str
    status: str
    resolved_at: Optional[datetime] = None
    resolved_by: Optional[int] = None
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None
    device: Optional[AlertDeviceInfo] = None
    resolved_by_user: Optional[AlertResolverInfo] = None
    
    class Config:
        from_attributes = True


class AlertListResponse(BaseModel):
    """Schema for paginated alert list response."""
    current_page: int
    per_page: int
    total: int
    data: List[AlertResponse]
