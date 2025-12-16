"""
Device Pydantic schemas.
"""
from datetime import datetime
from typing import Optional, List
from pydantic import BaseModel, Field, IPvAnyAddress
from enum import Enum


class DeviceType(str, Enum):
    router = "router"
    switch = "switch"
    access_point = "access_point"
    server = "server"
    firewall = "firewall"
    other = "other"


class HierarchyLevel(str, Enum):
    utama = "utama"
    sub = "sub"
    device = "device"


class DeviceStatus(str, Enum):
    up = "up"
    down = "down"
    unknown = "unknown"


class DeviceBase(BaseModel):
    """Base device schema."""
    name: str = Field(..., min_length=1, max_length=255)
    ip_address: str = Field(..., max_length=45)
    type: DeviceType
    hierarchy_level: HierarchyLevel = HierarchyLevel.device
    parent_id: Optional[int] = None
    location: Optional[str] = Field(None, max_length=255)
    description: Optional[str] = None
    port: Optional[int] = Field(None, ge=1, le=65535)


class DeviceCreate(DeviceBase):
    """Schema for creating a device."""
    pass


class DeviceUpdate(BaseModel):
    """Schema for updating a device."""
    name: Optional[str] = Field(None, min_length=1, max_length=255)
    ip_address: Optional[str] = Field(None, max_length=45)
    type: Optional[DeviceType] = None
    hierarchy_level: Optional[HierarchyLevel] = None
    parent_id: Optional[int] = None
    location: Optional[str] = Field(None, max_length=255)
    description: Optional[str] = None
    port: Optional[int] = Field(None, ge=1, le=65535)


class DeviceStatusUpdate(BaseModel):
    """Schema for updating device status from monitoring script."""
    device_id: int
    status: DeviceStatus
    response_time: Optional[float] = Field(None, ge=0)
    packet_loss: Optional[float] = Field(None, ge=0, le=100)
    checked_at: datetime


class DeviceChildResponse(BaseModel):
    """Simplified device response for children."""
    id: int
    name: str
    ip_address: str
    status: str
    
    class Config:
        from_attributes = True


class DeviceResponse(BaseModel):
    """Schema for device response."""
    id: int
    name: str
    ip_address: str
    type: str
    hierarchy_level: str
    parent_id: Optional[int] = None
    location: Optional[str] = None
    description: Optional[str] = None
    port: Optional[int] = None
    status: str
    last_checked_at: Optional[datetime] = None
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None
    parent: Optional[DeviceChildResponse] = None
    children: Optional[List[DeviceChildResponse]] = None
    
    class Config:
        from_attributes = True


class DeviceListResponse(BaseModel):
    """Schema for paginated device list response."""
    current_page: int
    per_page: int
    total: int
    data: List[DeviceResponse]
