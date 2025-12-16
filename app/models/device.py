"""
Device model for NetMonitor.
"""
from datetime import datetime
from sqlalchemy import Column, BigInteger, String, Text, Integer, Enum, DateTime, ForeignKey
from sqlalchemy.orm import relationship

from app.database import Base


class Device(Base):
    """Device model representing network devices being monitored."""
    
    __tablename__ = "devices"
    
    id = Column(BigInteger, primary_key=True, autoincrement=True)
    name = Column(String(255), nullable=False)
    ip_address = Column(String(45), unique=True, nullable=False, index=True)
    type = Column(
        Enum('router', 'switch', 'access_point', 'server', 'firewall', 'other', name='device_type'),
        nullable=False
    )
    hierarchy_level = Column(
        Enum('utama', 'sub', 'device', name='hierarchy_level'),
        default='device',
        nullable=False,
        index=True
    )
    parent_id = Column(BigInteger, ForeignKey('devices.id', ondelete='CASCADE'), nullable=True, index=True)
    location = Column(String(255), nullable=True)
    description = Column(Text, nullable=True)
    port = Column(Integer, nullable=True)
    status = Column(
        Enum('up', 'down', 'unknown', name='device_status'),
        default='unknown',
        nullable=False,
        index=True
    )
    last_checked_at = Column(DateTime, nullable=True)
    created_by = Column(BigInteger, ForeignKey('users.id', ondelete='SET NULL'), nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    parent = relationship("Device", remote_side=[id], back_populates="children")
    children = relationship("Device", back_populates="parent", cascade="all, delete-orphan")
    creator = relationship("User", back_populates="devices_created", foreign_keys=[created_by])
    logs = relationship("DeviceLog", back_populates="device", cascade="all, delete-orphan")
    alerts = relationship("Alert", back_populates="device", cascade="all, delete-orphan")
    
    @property
    def is_online(self) -> bool:
        """Check if device is online."""
        return self.status == 'up'
    
    @property
    def is_offline(self) -> bool:
        """Check if device is offline."""
        return self.status == 'down'
    
    @property
    def type_icon(self) -> str:
        """Get icon for device type."""
        icons = {
            'router': '🌐',
            'switch': '🔌',
            'access_point': '📶',
            'server': '🖥️',
            'firewall': '🛡️',
            'other': '📡'
        }
        return icons.get(self.type, '📡')
    
    @property
    def status_icon(self) -> str:
        """Get icon for status."""
        icons = {
            'up': '✅',
            'down': '❌',
            'unknown': '❓'
        }
        return icons.get(self.status, '❓')
    
    def to_dict(self, include_relations: bool = False) -> dict:
        """Convert model to dictionary."""
        data = {
            "id": self.id,
            "name": self.name,
            "ip_address": self.ip_address,
            "type": self.type,
            "hierarchy_level": self.hierarchy_level,
            "parent_id": self.parent_id,
            "location": self.location,
            "description": self.description,
            "port": self.port,
            "status": self.status,
            "last_checked_at": self.last_checked_at.isoformat() if self.last_checked_at else None,
            "created_at": self.created_at.isoformat() if self.created_at else None,
            "updated_at": self.updated_at.isoformat() if self.updated_at else None
        }
        
        if include_relations:
            data["parent"] = self.parent.to_dict() if self.parent else None
            data["children"] = [child.to_dict() for child in self.children]
            data["creator"] = self.creator.to_dict() if self.creator else None
        
        return data
    
    def __repr__(self):
        return f"<Device {self.name} ({self.ip_address})>"
