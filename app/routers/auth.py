"""
Authentication router.
"""
from datetime import timedelta
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from sqlalchemy.exc import OperationalError

from app.database import get_db
from app.models.user import User
from app.schemas.user import UserLogin, Token, UserResponse
from app.middleware.auth import create_access_token, get_current_user
from app.config import settings


router = APIRouter(prefix="/api/auth", tags=["Authentication"])


@router.post("/login", response_model=dict)
async def login(user_data: UserLogin, db: Session = Depends(get_db)):
    """
    Authenticate user and return JWT token.
    """
    try:
        # Find user by email
        user = db.query(User).filter(User.email == user_data.email).first()
        
        if not user:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid credentials"
            )
        
        # Verify password
        if not user.verify_password(user_data.password):
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid credentials"
            )
        
        # Create access token
        access_token_expires = timedelta(minutes=settings.jwt_access_token_expire_minutes)
        access_token = create_access_token(
            data={"sub": str(user.id), "email": user.email},
            expires_delta=access_token_expires
        )
        
        return {
            "success": True,
            "message": "Login successful",
            "data": {
                "token": access_token,
                "token_type": "Bearer",
                "user": user.to_dict()
            }
        }
    
    except OperationalError as e:
        # Database connection error - return graceful error
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Database service is currently unavailable. Please try again later."
        )
    except HTTPException:
        # Re-raise HTTP exceptions (auth errors)
        raise
    except Exception as e:
        # Catch any other unexpected errors
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="An unexpected error occurred during authentication"
        )


@router.post("/logout", response_model=dict)
async def logout(current_user: User = Depends(get_current_user)):
    """
    Logout user (invalidate token on client side).
    """
    # In JWT, logout is typically handled on client side by deleting the token
    # For server-side invalidation, you would need a token blacklist (not implemented here)
    return {
        "success": True,
        "message": "Logged out successfully"
    }


@router.get("/user", response_model=dict)
async def get_user(current_user: User = Depends(get_current_user)):
    """
    Get current authenticated user.
    """
    return {
        "success": True,
        "data": current_user.to_dict()
    }
