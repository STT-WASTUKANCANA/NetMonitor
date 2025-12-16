"""
NetMonitor Authentication Module

This module provides centralized authentication functionality including:
- Password hashing and verification using bcrypt
- Login attempt tracking and brute force protection
- Session validation with activity tracking
- Demo user database for testing

Production Note: This module provides a demo user database for testing.
In production, authentication should use the backend API.
"""
import bcrypt
import streamlit as st
from datetime import datetime, timedelta
from typing import Dict, Optional, Tuple


# Demo user database (for testing purposes)
# In production, users are authenticated via the backend API
DEMO_USERS = {
    "admin@netmonitor.local": {
        "password_hash": None,  # Will be set on first use
        "first_name": "Admin",
        "last_name": "User",
        "role": "admin",
        "email": "admin@netmonitor.local"
    },
    "user@netmonitor.local": {
        "password_hash": None,
        "first_name": "Regular",
        "last_name": "User",
        "role": "user",
        "email": "user@netmonitor.local"
    }
}

# Default password for demo users (only used if backend API is unavailable)
DEFAULT_DEMO_PASSWORD = "password123"


def hash_password(password: str) -> str:
    """
    Hash a password using bcrypt.
    
    Args:
        password: Plain text password to hash
        
    Returns:
        Hashed password as string
    """
    salt = bcrypt.gensalt()
    hashed = bcrypt.hashpw(password.encode('utf-8'), salt)
    return hashed.decode('utf-8')


def verify_password(password: str, password_hash: str) -> bool:
    """
    Verify a password against a hash.
    
    Args:
        password: Plain text password to verify
        password_hash: Hashed password to compare against
        
    Returns:
        True if password matches, False otherwise
    """
    try:
        return bcrypt.checkpw(password.encode('utf-8'), password_hash.encode('utf-8'))
    except Exception:
        return False


def initialize_demo_passwords():
    """Initialize demo user passwords if not already set."""
    for email in DEMO_USERS:
        if DEMO_USERS[email]["password_hash"] is None:
            DEMO_USERS[email]["password_hash"] = hash_password(DEFAULT_DEMO_PASSWORD)


def get_demo_user(email: str) -> Optional[Dict]:
    """
    Get demo user by email.
    
    Args:
        email: User email address
        
    Returns:
        User dict if found, None otherwise
    """
    initialize_demo_passwords()
    return DEMO_USERS.get(email)


def verify_demo_credentials(email: str, password: str) -> Tuple[bool, Optional[Dict]]:
    """
    Verify demo user credentials.
    
    Args:
        email: User email address
        password: Plain text password
        
    Returns:
        Tuple of (success: bool, user_data: Optional[Dict])
    """
    user = get_demo_user(email)
    
    if not user:
        return False, None
    
    if verify_password(password, user["password_hash"]):
        # Return user data without password hash
        user_data = {k: v for k, v in user.items() if k != "password_hash"}
        return True, user_data
    
    return False, None


def check_login_attempts(email: str, max_attempts: int = 5, lockout_minutes: int = 15) -> Tuple[bool, Optional[str]]:
    """
    Check if user has exceeded login attempts and should be locked out.
    
    Args:
        email: User email address
        max_attempts: Maximum allowed failed login attempts
        lockout_minutes: Duration of lockout in minutes
        
    Returns:
        Tuple of (allowed: bool, message: Optional[str])
    """
    # Initialize login attempts tracking in session state
    if "login_attempts" not in st.session_state:
        st.session_state.login_attempts = {}
    
    if email not in st.session_state.login_attempts:
        return True, None
    
    attempt_data = st.session_state.login_attempts[email]
    attempts = attempt_data.get("count", 0)
    last_attempt = attempt_data.get("last_attempt")
    
    # Check if lockout period has expired
    if last_attempt:
        lockout_until = last_attempt + timedelta(minutes=lockout_minutes)
        if datetime.now() < lockout_until:
            if attempts >= max_attempts:
                remaining = (lockout_until - datetime.now()).seconds // 60
                return False, f"Too many failed attempts. Please try again in {remaining} minute(s)."
        else:
            # Lockout expired, reset attempts
            st.session_state.login_attempts[email] = {"count": 0, "last_attempt": None}
            return True, None
    
    if attempts >= max_attempts:
        return False, "Too many failed attempts. Please try again later."
    
    return True, None


def record_login_attempt(email: str, success: bool):
    """
    Record a login attempt (success or failure).
    
    Args:
        email: User email address
        success: Whether the login was successful
    """
    if "login_attempts" not in st.session_state:
        st.session_state.login_attempts = {}
    
    if success:
        # Clear attempts on successful login
        if email in st.session_state.login_attempts:
            del st.session_state.login_attempts[email]
    else:
        # Increment failed attempts
        if email not in st.session_state.login_attempts:
            st.session_state.login_attempts[email] = {"count": 0, "last_attempt": None}
        
        st.session_state.login_attempts[email]["count"] += 1
        st.session_state.login_attempts[email]["last_attempt"] = datetime.now()


def get_session_time_remaining(last_activity: datetime, timeout_minutes: int = 15) -> int:
    """
    Calculate remaining session time in seconds.
    
    Args:
        last_activity: Timestamp of last user activity
        timeout_minutes: Session timeout in minutes
        
    Returns:
        Remaining seconds (0 if expired)
    """
    if not last_activity:
        return 0
    
    timeout_delta = timedelta(minutes=timeout_minutes)
    expiry_time = last_activity + timeout_delta
    remaining = (expiry_time - datetime.now()).total_seconds()
    
    return max(0, int(remaining))


def format_time_remaining(seconds: int) -> str:
    """
    Format remaining time in human-readable format.
    
    Args:
        seconds: Remaining seconds
        
    Returns:
        Formatted string (e.g., "14m 30s")
    """
    if seconds <= 0:
        return "Expired"
    
    minutes = seconds // 60
    secs = seconds % 60
    
    if minutes > 0:
        return f"{minutes}m {secs}s"
    else:
        return f"{secs}s"
