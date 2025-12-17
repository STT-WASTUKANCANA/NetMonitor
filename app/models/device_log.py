"""
DeviceLog model for NetMonitor.
"""
from datetime import datetime
from sqlalchemy import Column, BigInteger, Enum, DateTime, DECIMAL, ForeignKey
from sqlalchemy.orm import relationship

from app.database import Base


class DeviceLog(Base):
    """DeviceLog model representing monitoring history for devices."""
    
    __tablename__ = "device_logs"
    
    id = Column(BigInteger, primary_key=True, autoincrement=True)
    device_id = Column(BigInteger, ForeignKey('devices.id', ondelete='CASCADE'), nullable=False, index=True)
    status = Column(Enum('up', 'down', name='log_status'), nullable=False)
    response_time = Column(DECIMAL(8, 2), nullable=True, comment='Response time in milliseconds')
    packet_loss = Column(DECIMAL(5, 2), nullable=True, comment='Packet loss percentage')
    checked_at = Column(DateTime, nullable=False, index=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    
    # Relationships
    device = relationship("Device", back_populates="logs")
    
    @property
    def is_up(self) -> bool:
        """Check if log status is up."""
        return self.status == 'up'
    
    @property
    def response_time_ms(self) -> float:
        """Get response time as float in milliseconds."""
        return float(self.response_time) if self.response_time else 0.0
    
    @property
    def packet_loss_percent(self) -> float:
        """Get packet loss as float percentage."""
        return float(self.packet_loss) if self.packet_loss else 0.0
    
    @property
    def checked_at_jakarta(self) -> datetime:
        """Get checked_at in Jakarta timezone."""
        from app.utils.timezone import utc_to_jakarta
        return utc_to_jakarta(self.checked_at)

    def to_dict(self) -> dict:
        """Convert model to dictionary."""
        from app.utils.timezone import format_jakarta
        
        return {
            "id": self.id,
            "device_id": self.device_id,
            "status": self.status,
            "response_time": float(self.response_time) if self.response_time else None,
            "packet_loss": float(self.packet_loss) if self.packet_loss else None,
            "checked_at": self.checked_at.isoformat() if self.checked_at else None,
            "checked_at_formatted": format_jakarta(self.checked_at) if self.checked_at else None,
            "created_at": self.created_at.isoformat() if self.created_at else None
        }
    
    def __repr__(self):
        return f"<DeviceLog device_id={self.device_id} status={self.status} at={self.checked_at}>"
