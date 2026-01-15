"""
User Pydantic schemas.
"""
from datetime import datetime
from typing import Optional
from pydantic import BaseModel, Field
from enum import Enum


class UserRole(str, Enum):
    admin = "admin"
    petugas = "petugas"


class UserBase(BaseModel):
    """Base user schema."""
    first_name: str = Field(..., min_length=1, max_length=255)
    last_name: str = Field(..., min_length=1, max_length=255)
    email: str = Field(..., max_length=255)  # Changed from EmailStr to allow .local domains


class UserCreate(UserBase):
    """Schema for creating a user."""
    password: str = Field(..., min_length=8)
    role: UserRole = UserRole.petugas


class UserUpdate(BaseModel):
    """Schema for updating a user."""
    first_name: Optional[str] = Field(None, min_length=1, max_length=255)
    last_name: Optional[str] = Field(None, min_length=1, max_length=255)
    email: Optional[str] = Field(None, max_length=255)
    password: Optional[str] = Field(None, min_length=8)
    role: Optional[UserRole] = None
    profile_photo: Optional[str] = None


class UserResponse(BaseModel):
    """Schema for user response."""
    id: int
    first_name: str
    last_name: str
    email: str
    role: str
    profile_photo: Optional[str] = None
    created_at: Optional[datetime] = None
    
    class Config:
        from_attributes = True


class UserLogin(BaseModel):
    """Schema for user login."""
    email: str = Field(..., max_length=255)
    password: str


class Token(BaseModel):
    """Schema for JWT token response."""
    token: str
    token_type: str = "Bearer"
    user: UserResponse


class TokenData(BaseModel):
    """Schema for decoded token data."""
    user_id: Optional[int] = None
    email: Optional[str] = None
