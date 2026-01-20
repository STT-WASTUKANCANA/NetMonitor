"""
Pydantic schemas for NetMonitor.
"""
from app.schemas.user import (
    UserBase, UserCreate, UserUpdate, UserResponse, UserLogin, Token, TokenData
)
from app.schemas.device import (
    DeviceBase, DeviceCreate, DeviceUpdate, DeviceResponse, DeviceStatusUpdate, DeviceListResponse
)
from app.schemas.device_log import (
    DeviceLogBase, DeviceLogCreate, DeviceLogResponse, DeviceLogListResponse
)
from app.schemas.alert import (
    AlertBase, AlertCreate, AlertUpdate, AlertResponse, AlertListResponse, AlertBulkUpdate
)

__all__ = [
    "UserBase", "UserCreate", "UserUpdate", "UserResponse", "UserLogin", "Token", "TokenData",
    "DeviceBase", "DeviceCreate", "DeviceUpdate", "DeviceResponse", "DeviceStatusUpdate", "DeviceListResponse",
    "DeviceLogBase", "DeviceLogCreate", "DeviceLogResponse", "DeviceLogListResponse",
    "AlertBase", "AlertCreate", "AlertUpdate", "AlertResponse", "AlertListResponse", "AlertBulkUpdate"
]
