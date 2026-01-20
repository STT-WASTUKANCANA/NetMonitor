"""
Alert model for NetMonitor.
"""
from datetime import datetime
from sqlalchemy import Column, BigInteger, Text, Enum, DateTime, ForeignKey
from sqlalchemy.orm import relationship

from app.database import Base


class Alert(Base):
    """Alert model representing notifications and warnings for devices."""
    
    __tablename__ = "alerts"
    
    id = Column(BigInteger, primary_key=True, autoincrement=True)
    device_id = Column(BigInteger, ForeignKey('devices.id', ondelete='CASCADE'), nullable=False, index=True)
    message = Column(Text, nullable=False)
    severity = Column(
        Enum('low', 'medium', 'high', 'critical', name='alert_severity'),
        default='medium',
        nullable=False,
        index=True
    )
    status = Column(
        Enum('active', 'acknowledged', 'resolved', name='alert_status'),
        default='active',
        nullable=False,
        index=True
    )
    resolved_at = Column(DateTime, nullable=True)
    resolved_by = Column(BigInteger, ForeignKey('users.id', ondelete='SET NULL'), nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    device = relationship("Device", back_populates="alerts")
    resolver = relationship("User", back_populates="alerts_resolved", foreign_keys=[resolved_by])
    
    @property
    def is_active(self) -> bool:
        """Check if alert is active."""
        return self.status == 'active'
    
    @property
    def is_resolved(self) -> bool:
        """Check if alert is resolved."""
        return self.status == 'resolved'
    
    @property
    def is_critical(self) -> bool:
        """Check if alert is critical severity."""
        return self.severity == 'critical'
    
    @property
    def severity_icon(self) -> str:
        """Get icon for severity level."""
        icons = {
            'low': '🟢',
            'medium': '🟡',
            'high': '🟠',
            'critical': '🔴'
        }
        return icons.get(self.severity, '🟡')
    
    @property
    def status_icon(self) -> str:
        """Get icon for status."""
        icons = {
            'active': '🔔',
            'acknowledged': '👁️',
            'resolved': '✅'
        }
        return icons.get(self.status, '🔔')
    
    def resolve(self, user_id: int) -> None:
        """Mark alert as resolved."""
        self.status = 'resolved'
        self.resolved_at = datetime.utcnow()
        self.resolved_by = user_id
    
    def acknowledge(self) -> None:
        """Mark alert as acknowledged."""
        self.status = 'acknowledged'
    
    def to_dict(self, include_device: bool = False, include_resolver: bool = False) -> dict:
        """Convert model to dictionary."""
        data = {
            "id": self.id,
            "device_id": self.device_id,
            "message": self.message,
            "severity": self.severity,
            "status": self.status,
            "resolved_at": self.resolved_at.isoformat() if self.resolved_at else None,
            "resolved_by": self.resolved_by,
            "created_at": self.created_at.isoformat() if self.created_at else None,
            "updated_at": self.updated_at.isoformat() if self.updated_at else None
        }
        
        if include_device and self.device:
            data["device"] = {
                "id": self.device.id,
                "name": self.device.name,
                "ip_address": self.device.ip_address,
                "location": self.device.location,
                "type": self.device.type,
                "current_status": self.device.status
            }
        
        if include_resolver and self.resolver:
            data["resolved_by_user"] = {
                "id": self.resolver.id,
                "first_name": self.resolver.first_name,
                "last_name": self.resolver.last_name
            }
        
        return data
    
    def __repr__(self):
        return f"<Alert id={self.id} device_id={self.device_id} severity={self.severity}>"
