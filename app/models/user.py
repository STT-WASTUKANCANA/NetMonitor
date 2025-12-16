"""
User model for NetMonitor.
"""
from datetime import datetime
from sqlalchemy import Column, BigInteger, String, Enum, DateTime
from sqlalchemy.orm import relationship
import bcrypt

from app.database import Base


class User(Base):
    """User model representing system users (admin and petugas)."""
    
    __tablename__ = "users"
    
    id = Column(BigInteger, primary_key=True, autoincrement=True)
    first_name = Column(String(255), nullable=False)
    last_name = Column(String(255), nullable=False)
    email = Column(String(255), unique=True, nullable=False, index=True)
    email_verified_at = Column(DateTime, nullable=True)
    password = Column(String(255), nullable=False)
    role = Column(Enum('admin', 'petugas', name='user_role'), default='petugas', nullable=False)
    profile_photo = Column(String(255), nullable=True)
    remember_token = Column(String(100), nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    devices_created = relationship("Device", back_populates="creator", foreign_keys="Device.created_by")
    alerts_resolved = relationship("Alert", back_populates="resolver", foreign_keys="Alert.resolved_by")
    
    @property
    def full_name(self) -> str:
        """Get user's full name."""
        return f"{self.first_name} {self.last_name}"
    
    @property
    def is_admin(self) -> bool:
        """Check if user is admin."""
        return self.role == 'admin'
    
    def verify_password(self, plain_password: str) -> bool:
        """Verify a plain password against the stored hash."""
        return bcrypt.checkpw(plain_password.encode('utf-8'), self.password.encode('utf-8'))
    
    @staticmethod
    def hash_password(plain_password: str) -> str:
        """Hash a plain password."""
        return bcrypt.hashpw(plain_password.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')
    
    def to_dict(self) -> dict:
        """Convert model to dictionary."""
        return {
            "id": self.id,
            "first_name": self.first_name,
            "last_name": self.last_name,
            "email": self.email,
            "role": self.role,
            "profile_photo": self.profile_photo,
            "created_at": self.created_at.isoformat() if self.created_at else None
        }
    
    def __repr__(self):
        return f"<User {self.email}>"
