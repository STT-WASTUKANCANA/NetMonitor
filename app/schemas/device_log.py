"""
DeviceLog Pydantic schemas.
"""
from datetime import datetime
from typing import Optional, List
from pydantic import BaseModel, Field
from enum import Enum


class LogStatus(str, Enum):
    up = "up"
    down = "down"


class DeviceLogBase(BaseModel):
    """Base device log schema."""
    device_id: int
    status: LogStatus
    response_time: Optional[float] = Field(None, ge=0)
    packet_loss: Optional[float] = Field(None, ge=0, le=100)
    checked_at: datetime


class DeviceLogCreate(DeviceLogBase):
    """Schema for creating a device log."""
    pass


class DeviceLogResponse(BaseModel):
    """Schema for device log response."""
    id: int
    device_id: int
    status: str
    response_time: Optional[float] = None
    packet_loss: Optional[float] = None
    checked_at: datetime
    created_at: Optional[datetime] = None
    
    class Config:
        from_attributes = True


class DeviceLogStatistics(BaseModel):
    """Statistics for device logs."""
    total_checks: int
    up_count: int
    down_count: int
    uptime_percentage: float
    avg_response_time: Optional[float] = None
    min_response_time: Optional[float] = None
    max_response_time: Optional[float] = None
    avg_packet_loss: Optional[float] = None


class DeviceLogListResponse(BaseModel):
    """Schema for paginated device log list response."""
    device: dict
    logs: dict
    statistics: DeviceLogStatistics
